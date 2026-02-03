<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class KnowledgeQualityScore extends BaseModel
{
    protected $table = 'knowledge_quality_scores';

    public function site() {
        return $this->belongsTo(Site::class);
    }
}
