<?php

namespace App\Http\Controllers\api\v1;

use App\Http\Controllers\Controller;
use App\Models\Site;
use App\Models\WidgetSetting;
use Illuminate\Http\Request;
use Illuminate\Support\Str;

class WidgetSettingController extends Controller
{
    /**
     * Get widget settings for a site
     * (auto-create if not exists)
     */
    public function index(Request $request, Site $site)
    {
        $user = $request->user();

        // 🔐 Sécurité : propriétaire du site
        abort_if($site->account_id !== $user->ownedAccount->id, 403);

        // 🔹 Auto-création si absent
        /**
         * @var WidgetSetting $widgetSetting
         */
        $widgetSetting = $site->settings()->first();

        if (!$widgetSetting) {
            $widgetSetting = WidgetSetting::create([
                'id' => Str::uuid(),
                'site_id' => $site->id,
            ]);
        }

        return response()->json([
            'data' => $widgetSetting->refresh(),
        ]);
    }

    /**
     * Store (create) widget settings (rarement appelé si index auto-create)
     */
    public function store(Request $request)
    {
        $validated = $request->validate([
            'site_id' => 'required|uuid|exists:sites,id',
        ]);

        $site = Site::findOrFail($validated['site_id']);
        $user = $request->user();

        abort_if($site->account_id !== $user->ownedAccount->id, 403);

        // ❌ Un seul widget setting par site
        if ($site->widgetSetting) {
            return response()->json([
                'message' => 'Widget settings already exist for this site',
            ], 409);
        }

        $widgetSetting = WidgetSetting::create([
            'id' => Str::uuid(),
            'site_id' => $site->id,
        ]);

        return response()->json([
            'message' => 'Widget settings created',
            'data' => $widgetSetting,
        ], 201);
    }

    /**
     * Show widget settings
     */
    public function show(Request $request, WidgetSetting $widgetSetting)
    {
        $user = $request->user();

        abort_if(
            $widgetSetting->site->account_id !== $user->ownedAccount->id,
            403
        );

        return response()->json([
            'data' => $widgetSetting,
        ]);
    }

    /**
     * Update widget settings
     */
    public function update(Request $request, WidgetSetting $widgetSetting)
    {
        $user = $request->user();

        abort_if(
            $widgetSetting->site->account_id !== $user->ownedAccount->id,
            403
        );

        $validated = $request->validate([
            // Button
            'button_text' => 'nullable|string|max:50',
            'button_background' => 'nullable|string|max:20',
            'button_color' => 'nullable|string|max:20',
            'button_position' => 'nullable|in:bottom-right,bottom-left,top-right,top-left',
            'button_offset_x' => 'nullable|string|max:10',
            'button_offset_y' => 'nullable|string|max:10',

            // Theme
            'theme_primary' => 'nullable|string|max:20',
            'theme_secondary' => 'nullable|string|max:20',
            'theme_background' => 'nullable|string|max:20',
            'theme_color' => 'nullable|string|max:20',

            // Messages
            'message_user_background' => 'nullable|string|max:20',
            'message_user_color' => 'nullable|string|max:20',
            'message_bot_background' => 'nullable|string|max:20',
            'message_bot_color' => 'nullable|string|max:20',

            // Widget / AI
            'widget_enabled' => 'boolean',
            'ai_enabled' => 'boolean',
            'bot_name' => 'nullable|string|max:50',
            'bot_language' => 'nullable|string|max:5',
            'welcome_message' => 'nullable|string|max:255',
            'input_placeholder' => 'nullable|string|max:100',

            'ai_temperature' => 'nullable|numeric|min:0|max:1',
            'ai_max_tokens' => 'nullable|integer|min:50|max:4000',
            'min_similarity_score' => 'nullable|numeric|min:0|max:1',
            'fallback_message' => 'nullable|string|max:255',
        ]);

        $widgetSetting->update($validated);

        return response()->json([
            'message' => 'Widget settings updated',
            'data' => $widgetSetting->fresh(),
        ]);
    }

    /**
     * Delete widget settings (rare)
     */
    public function destroy(Request $request, WidgetSetting $widgetSetting)
    {
        $user = $request->user();

        abort_if(
            $widgetSetting->site->account_id !== $user->ownedAccount->id,
            403
        );

        $widgetSetting->delete();

        return response()->json([
            'message' => 'Widget settings deleted',
        ]);
    }
}
