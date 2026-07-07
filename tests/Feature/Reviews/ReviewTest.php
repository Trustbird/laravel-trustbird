<?php

declare(strict_types=1);

use Illuminate\Support\Facades\Event;
use Trustbird\Controls\Models\Control;
use Trustbird\Facades\Trustbird;
use Trustbird\People\Models\Person;
use Trustbird\Reviews\Enums\ReviewStatus;
use Trustbird\Reviews\Enums\ReviewerRole;
use Trustbird\Reviews\Events\ReviewCompleted;
use Trustbird\Reviews\Events\ReviewReopened;
use Trustbird\Reviews\Events\ReviewScheduled;
use Trustbird\Reviews\Models\Review;
use Trustbird\Reviews\Models\ReviewReviewer;

beforeEach(fn () => Event::fake());

test('it can schedule a review for a subject and dispatches event', function (): void {
    $control = Control::factory()->create();
    $reviewer = Person::factory()->create(['workspace_id' => $control->workspace_id]);
    $dueAt = now()->addMonth();

    $review = Trustbird::reviews()->schedule(
        subject: $control,
        dueAt: $dueAt,
        reviewerId: $reviewer->id,
    );

    expect($review)->toBeInstanceOf(Review::class)
        ->and($review->status)->toBe(ReviewStatus::Scheduled)
        ->and($review->reviewable_id)->toBe($control->id)
        ->and($review->due_at?->toDateTimeString())->toBe($dueAt->toDateTimeString());

    Event::assertDispatched(ReviewScheduled::class);
});

test('it can complete a review and preserves history', function (): void {
    $review = Review::factory()->create([
        'status' => ReviewStatus::Scheduled,
        'completed_at' => null,
    ]);
    $reviewer = Person::factory()->create(['workspace_id' => $review->workspace_id]);

    $completed = Trustbird::reviews()->complete(
        review: $review,
        reviewerId: $reviewer->id,
        notes: 'All checks passed.',
    );

    expect($completed->status)->toBe(ReviewStatus::Completed)
        ->and($completed->completed_at)->not->toBeNull()
        ->and($completed->notes)->toBe('All checks passed.');

    Event::assertDispatched(ReviewCompleted::class);
});

test('it cannot complete an already completed review', function (): void {
    $review = Review::factory()->completed()->create();

    Trustbird::reviews()->complete(review: $review);
})->throws(InvalidArgumentException::class, 'This review is already completed.');

test('it can reopen a completed review and dispatches event', function (): void {
    $review = Review::factory()->completed()->create();

    $reopened = Trustbird::reviews()->reopen(review: $review);

    expect($reopened->status)->toBe(ReviewStatus::Reopened)
        ->and($reopened->completed_at)->not->toBeNull();

    Event::assertDispatched(ReviewReopened::class);
});

test('it cannot reopen a review that is not completed', function (): void {
    $review = Review::factory()->create(['status' => ReviewStatus::Scheduled]);

    Trustbird::reviews()->reopen(review: $review);
})->throws(InvalidArgumentException::class, 'Only completed reviews can be reopened.');

test('it can assign reviewers to a review', function (): void {
    $review = Review::factory()->create();
    $person = Person::factory()->create(['workspace_id' => $review->workspace_id]);

    $assignment = Trustbird::reviews()->assignReviewer(
        review: $review,
        personId: $person->id,
        role: ReviewerRole::Contributor,
    );

    expect($assignment)->toBeInstanceOf(ReviewReviewer::class)
        ->and($assignment->person_id)->toBe($person->id)
        ->and($assignment->role)->toBe(ReviewerRole::Contributor);

    Event::assertDispatched('eloquent.created: '.ReviewReviewer::class);
});

test('it reports due scheduled reviews', function (): void {
    $review = Review::factory()->overdue()->create();

    expect($review->isDue())->toBeTrue();
    expect($review->isCompleted())->toBeFalse();
});

test('it does not report due when review is completed or due date is missing', function (): void {
    $completed = Review::factory()->completed()->create();
    $scheduledWithoutDue = Review::factory()->create([
        'status' => ReviewStatus::Scheduled,
        'due_at' => null,
    ]);

    expect($completed->isDue())->toBeFalse()
        ->and($completed->isCompleted())->toBeTrue();
    expect($scheduledWithoutDue->isDue())->toBeFalse();
});

test('it covers review and reviewer model methods and factories', function (): void {
    $review = Review::factory()->create();
    $person = Person::factory()->create(['workspace_id' => $review->workspace_id]);

    $assignment = ReviewReviewer::factory()->create([
        'workspace_id' => $review->workspace_id,
        'review_id' => $review->id,
        'person_id' => $person->id,
        'metadata' => ['notified' => true],
    ]);

    expect($review->reviewable)->toBeInstanceOf(Control::class);
    expect($review->reviewer)->toBeInstanceOf(Person::class);
    expect($review->reviewers)->toHaveCount(1);
    expect($assignment->review->id)->toBe($review->id);
    expect($assignment->person->id)->toBe($person->id);

    expect(Review::newFactory())->toBeInstanceOf(\Trustbird\Database\Factories\Review\ReviewFactory::class);
    expect(ReviewReviewer::newFactory())->toBeInstanceOf(\Trustbird\Database\Factories\Review\ReviewReviewerFactory::class);
});
