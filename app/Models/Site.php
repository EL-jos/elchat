<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

class Site extends BaseModel
{
    public function account(): BelongsTo
    {
        return $this->belongsTo(Account::class);
    }
    public function crawlJobs(): HasMany
    {
        return $this->hasMany(CrawlJob::class);
    }
    public function pages(): HasMany
    {
        return $this->hasMany(Page::class);
    }
    public function conversations(): HasMany
    {
        return $this->hasMany(Conversation::class);
    }
    public function unansweredQuestions(): HasMany
    {
        return $this->hasMany(UnansweredQuestion::class);
    }
}
