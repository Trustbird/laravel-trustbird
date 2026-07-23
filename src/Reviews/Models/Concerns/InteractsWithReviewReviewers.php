<?php

declare(strict_types=1);

namespace Trustbird\Reviews\Models\Concerns;

use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Trustbird\Database\Factories\Review\ReviewReviewerFactory;
use Trustbird\People\Contracts\HasPeople;
use Trustbird\Reviews\Enums\ReviewerRole;
use Trustbird\Reviews\Models\Review;
use Trustbird\Workspaces\Concerns\BelongsToWorkspace;

trait InteractsWithReviewReviewers
{
    use BelongsToWorkspace;

    public function initializeInteractsWithReviewReviewers(): void
    {
        $this->mergeFillable([
            'review_id',
            'person_id',
            'role',
            'metadata',
        ]);

        $this->mergeCasts([
            'role' => ReviewerRole::class,
            'metadata' => 'array',
        ]);
    }

    public function review(): BelongsTo
    {
        return $this->belongsTo(Review::class);
    }

    public function person(): BelongsTo
    {
        return $this->belongsTo(app(HasPeople::class)::class, 'person_id');
    }

    protected static function newFactory(): ReviewReviewerFactory
    {
        return ReviewReviewerFactory::new();
    }
}
