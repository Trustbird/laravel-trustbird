<?php

declare(strict_types=1);

use Illuminate\Support\Facades\Event;
use Trustbird\Controls\Models\Control;
use Trustbird\Facades\Trustbird;
use Trustbird\Reviews\Models\Review;

beforeEach(fn () => Event::fake());

it('can schedule a review via the facade', function () {
    $control = Control::factory()->create();

    $review = Trustbird::reviews()->schedule(
        subject: $control,
        dueAt: now()->addWeek(),
    );

    expect($review)->toBeInstanceOf(Review::class)
        ->and($review->reviewable_id)->toBe($control->id);

    Event::assertDispatched('eloquent.created: '.Review::class);
});
