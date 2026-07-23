<?php

declare(strict_types=1);

namespace Trustbird\Controls\Actions;

use Trustbird\Controls\Contracts\HasControls;
use Trustbird\Controls\Enums\ControlStatus;
use Trustbird\Controls\Events\ControlApproved;

final readonly class ApproveControl
{
    /**
     * @param array{
     *     reviewed_at?: \DateTimeInterface|null,
     *     next_review_at?: \DateTimeInterface|null,
     * } $attributes
     */
    public function handle(HasControls $control, array $attributes = []): HasControls
    {
        $control->update([
            'status' => ControlStatus::Active,
            'reviewed_at' => $attributes['reviewed_at'] ?? now(),
            'next_review_at' => $attributes['next_review_at'] ?? null,
        ]);

        ControlApproved::dispatch($control);

        return $control;
    }
}
