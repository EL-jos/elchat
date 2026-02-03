<?php

namespace App\Jobs;

use App\Models\KnowledgeQualityScore;
use App\Models\Site;
use App\Services\KnowledgeQualityService;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use Illuminate\Foundation\Queue\Queueable;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Queue\SerializesModels;
use Illuminate\Support\Facades\Log;

class ComputeKnowledgeQualityJob implements ShouldQueue
{
    use Dispatchable, InteractsWithQueue, Queueable, SerializesModels;

    public ?string $siteId;

    /**
     * Create a new job instance.
     * @param int|null $siteId pour recalculer un site spécifique ou tous
     */
    public function __construct(?string $siteId = null)
    {
        $this->siteId = $siteId;
    }

    /**
     * Execute the job.
     */
    public function handle(KnowledgeQualityService $service)
    {
        $sites = $this->siteId
            ? Site::where('id', $this->siteId)->get()
            : Site::all();

        foreach ($sites as $site) {
            $score = $service->calculateForSite($site);
            Log::info("KQI recalculé pour le site {$site->id}", [
                'global_score' => $score->global_score
            ]);
        }
    }
}
