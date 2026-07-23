<?php

declare(strict_types=1);

use Illuminate\Support\Facades\Event;
use Trustbird\Documents\Enums\DocumentVersionStatus;
use Trustbird\Documents\Events\DocumentReviewed;
use Trustbird\Documents\Events\DocumentVersionDrafted;
use Trustbird\Documents\Events\DocumentVersionPublished;
use Trustbird\Documents\Models\Document;
use Trustbird\Documents\Models\DocumentVersion;
use Trustbird\Facades\Trustbird;
use Trustbird\People\Models\Person;

beforeEach(fn () => Event::fake());

test('it can create a document with an initial draft version', function (): void {
    $owner = Person::factory()->create();
    $reviewer = Person::factory()->create(['workspace_id' => $owner->workspace_id]);

    $document = Trustbird::documents()->create(
        title: 'Business continuity plan',
        content: 'Initial BCP content.',
        ownerId: $owner->id,
        reviewerId: $reviewer->id,
        changeSummary: 'Initial draft',
    );

    expect($document)->toBeInstanceOf(Document::class)
        ->title->toBe('Business continuity plan')
        ->owner_id->toBe($owner->id)
        ->reviewer_id->toBe($reviewer->id);

    expect($document->versions)->toHaveCount(1);
    expect($document->versions->first())
        ->version_number->toBe(1)
        ->status->toBe(DocumentVersionStatus::Draft);
});

test('it can publish a document version and dispatches event', function (): void {
    $document = Document::factory()->withDraftVersion()->create();
    $version = $document->versions->first();
    $publisher = Person::factory()->create(['workspace_id' => $document->workspace_id]);

    $published = Trustbird::documents()->publishVersion(
        document: $document,
        version: $version,
        publishedById: $publisher->id,
    );

    expect($published->status)->toBe(DocumentVersionStatus::Published)
        ->and($document->fresh()->current_version_id)->toBe($version->id);

    Event::assertDispatched(DocumentVersionPublished::class);
});

test('it cannot publish a version that does not belong to the document', function (): void {
    $document = Document::factory()->withDraftVersion()->create();
    $otherVersion = DocumentVersion::factory()->create();

    Trustbird::documents()->publishVersion(
        document: $document,
        version: $otherVersion,
    );
})->throws(InvalidArgumentException::class, 'The document version does not belong to this document.');

test('it can draft a new document version and dispatches event', function (): void {
    $document = Document::factory()->withPublishedVersion()->create();

    $version = Trustbird::documents()->draftVersion(
        document: $document,
        content: 'Updated BCP content.',
        changeSummary: 'Annual refresh',
    );

    expect($version->version_number)->toBe(2)
        ->and($version->status)->toBe(DocumentVersionStatus::Draft);

    Event::assertDispatched(DocumentVersionDrafted::class);
});

test('it can review a document and dispatches event', function (): void {
    $document = Document::factory()->create(['next_review_at' => now()->subDay()]);
    $reviewer = Person::factory()->create(['workspace_id' => $document->workspace_id]);

    expect($document->needsReview())->toBeTrue();

    $reviewed = Trustbird::documents()->review(
        document: $document,
        reviewerId: $reviewer->id,
        nextReviewAt: now()->addYear(),
    );

    expect($reviewed->reviewer_id)->toBe($reviewer->id)
        ->and($reviewed->reviewed_at)->not->toBeNull();

    Event::assertDispatched(DocumentReviewed::class);
});

test('it needs review when next review date is not set', function (): void {
    $document = Document::factory()->create(['next_review_at' => null]);

    expect($document->needsReview())->toBeTrue();
});

test('it can update a document', function (): void {
    $document = Document::factory()->create(['title' => 'Old title']);

    $updated = Trustbird::documents()->update(
        document: $document,
        title: 'Updated title',
    );

    expect($updated->title)->toBe('Updated title');
});

test('it cannot publish a non-draft document version', function (): void {
    $document = Document::factory()->withPublishedVersion()->create();
    $version = $document->currentVersion;

    Trustbird::documents()->publishVersion(
        document: $document,
        version: $version,
    );
})->throws(InvalidArgumentException::class, 'Only draft document versions can be published.');

test('it supersedes the previous published document version when publishing a new one', function (): void {
    $document = Document::factory()->withPublishedVersion()->create();
    $firstVersion = $document->currentVersion;

    $draft = Trustbird::documents()->draftVersion(
        document: $document,
        content: 'Second version content.',
    );

    Trustbird::documents()->publishVersion(
        document: $document,
        version: $draft,
    );

    expect($firstVersion->fresh()->status)->toBe(DocumentVersionStatus::Superseded)
        ->and($document->fresh()->current_version_id)->toBe($draft->id);
});

test('it covers document and version model methods and factories', function (): void {
    $owner = Person::factory()->create();
    $reviewer = Person::factory()->create(['workspace_id' => $owner->workspace_id]);
    $publisher = Person::factory()->create(['workspace_id' => $owner->workspace_id]);

    $document = Document::factory()->withPublishedVersion()->create([
        'owner_id' => $owner->id,
        'reviewer_id' => $reviewer->id,
        'metadata' => ['category' => 'operations'],
        'next_review_at' => now()->addYear(),
    ]);

    $publishedVersion = $document->currentVersion;
    $publishedVersion->update(['published_by_id' => $publisher->id]);

    $draftVersion = DocumentVersion::factory()->create([
        'workspace_id' => $document->workspace_id,
        'document_id' => $document->id,
        'version_number' => 2,
        'status' => DocumentVersionStatus::Draft,
    ]);

    $supersededVersion = DocumentVersion::factory()->create([
        'workspace_id' => $document->workspace_id,
        'document_id' => $document->id,
        'version_number' => 3,
        'status' => DocumentVersionStatus::Superseded,
    ]);

    expect($document->owner->id)->toBe($owner->id)
        ->and($document->reviewer->id)->toBe($reviewer->id)
        ->and($document->currentVersion->id)->toBe($publishedVersion->id)
        ->and($document->versions)->toHaveCount(3);

    expect($publishedVersion->isPublished())->toBeTrue()
        ->and($publishedVersion->isDraft())->toBeFalse()
        ->and($publishedVersion->isSuperseded())->toBeFalse()
        ->and($publishedVersion->canBePublished())->toBeFalse()
        ->and($publishedVersion->publishedBy->id)->toBe($publisher->id);

    expect($draftVersion->isDraft())->toBeTrue()
        ->and($draftVersion->canBePublished())->toBeTrue()
        ->and($draftVersion->document->id)->toBe($document->id);

    expect($supersededVersion->isSuperseded())->toBeTrue();

    expect(Document::newFactory())->toBeInstanceOf(\Trustbird\Database\Factories\Document\DocumentFactory::class);
    expect(DocumentVersion::newFactory())->toBeInstanceOf(\Trustbird\Database\Factories\Document\DocumentVersionFactory::class);
});
