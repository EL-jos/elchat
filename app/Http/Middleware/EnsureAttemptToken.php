<?php

namespace App\Http\Middleware;

use App\Models\TestAttemp;
use Closure;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;

class EnsureAttemptToken
{
    /**
     * Handle an incoming request.
     *
     * @param  \Closure(\Illuminate\Http\Request): (\Symfony\Component\HttpFoundation\Response)  $next
     */
    public function handle(Request $request, Closure $next)
    {
        $user = $request->user();

        if (!$user) {
            return response()->json(['message' => 'Non authentifié'], 401);
        }

        // attempt_id peut venir :
        // - de la route
        // - ou du body (fallback)
        $attemptId = $request->route('attempt')
            ?? $request->input('attempt');

        if (!$attemptId) {
            return response()->json(['message' => 'Attempt manquant'], 400);
        }

        $attempt = TestAttemp::where('id', $attemptId)
            ->where('user_id', $user->id)
            ->first();

        if (!$attempt) {
            return response()->json(['message' => 'Attempt introuvable'], 404);
        }

        $token = $request->header('X-Attempt-Token')
            ?? $request->input('attempt_token');

        if (!$token || $attempt->token !== $token) {
            return response()->json(['message' => 'Token invalide'], 403);
        }

        if ($attempt->status !== 'in_progress') {
            return response()->json(['message' => 'Test non actif'], 403);
        }

        // On injecte l’attempt validé dans la request
        $request->attributes->set('attempt', $attempt);

        return $next($request);
    }
}
