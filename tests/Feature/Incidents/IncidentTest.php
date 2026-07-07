<?php

declare(strict_types=1);

use Illuminate\Support\Facades\Event;
use Trustbird\Facades\Trustbird;
use Trustbird\Incidents\Enums\IncidentSeverity;
use Trustbird\Incidents\Enums\IncidentStatus;
use Trustbird\Incidents\Models\Incident;
use Trustbird\Incidents\Models\IncidentNote;
use Trustbird\People\Models\Person;

beforeEach(fn () => Event::fake());

test('it can register an incident and dispatches eloquent event', function (): void {
    $owner = Person::factory()->create();
    $responder = Person::factory()->create(['workspace_id' => $owner->workspace_id]);

    $incident = Trustbird::incidents()->create(
        title: 'Suspicious login detected',
        description: 'Multiple failed attempts followed by a successful login.',
        severity: IncidentSeverity::High,
        status: IncidentStatus::Open,
        ownerId: $owner->id,
        responderId: $responder->id,
    );

    expect($incident)->toBeInstanceOf(Incident::class)
        ->and($incident->title)->toBe('Suspicious login detected')
        ->and($incident->severity)->toBe(IncidentSeverity::High)
        ->and($incident->status)->toBe(IncidentStatus::Open)
        ->and($incident->owner_id)->toBe($owner->id)
        ->and($incident->responder_id)->toBe($responder->id);

    expect($incident->owner)->toBeInstanceOf(Person::class);
    expect($incident->responder)->toBeInstanceOf(Person::class);

    Event::assertDispatched('eloquent.created: '.Incident::class);
});

test('it can update an incident and dispatches eloquent event', function (): void {
    $incident = Incident::factory()->create([
        'severity' => IncidentSeverity::Medium,
        'status' => IncidentStatus::Open,
    ]);

    $updated = Trustbird::incidents()->update($incident,
        severity: IncidentSeverity::Critical,
        status: IncidentStatus::Investigating,
        containedAt: now(),
    );

    expect($updated->severity)->toBe(IncidentSeverity::Critical)
        ->and($updated->status)->toBe(IncidentStatus::Investigating)
        ->and($updated->contained_at)->not->toBeNull();

    Event::assertDispatched('eloquent.updated: '.Incident::class);
});

test('it can add a timeline note to an incident and dispatches eloquent event', function (): void {
    $incident = Incident::factory()->create();
    $author = Person::factory()->create(['workspace_id' => $incident->workspace_id]);

    $note = Trustbird::incidents()->addNote(
        incident: $incident,
        body: 'Initial triage completed.',
        authorId: $author->id,
        occurredAt: now()->subMinute(),
    );

    expect($note)->toBeInstanceOf(IncidentNote::class)
        ->and($note->incident_id)->toBe($incident->id)
        ->and($note->author_id)->toBe($author->id)
        ->and($note->body)->toBe('Initial triage completed.');

    expect($note->incident)->toBeInstanceOf(Incident::class);
    expect($note->author)->toBeInstanceOf(Person::class);

    Event::assertDispatched('eloquent.created: '.IncidentNote::class);
});

test('it can determine incident lifecycle state', function (): void {
    $open = Incident::factory()->create(['status' => IncidentStatus::Open]);
    $resolved = Incident::factory()->resolved()->create();
    $archived = Incident::factory()->create(['status' => IncidentStatus::Archived]);

    expect($open->isActive())->toBeTrue()
        ->and($open->isResolved())->toBeFalse()
        ->and($open->isArchived())->toBeFalse();

    expect($resolved->isResolved())->toBeTrue()
        ->and($resolved->isActive())->toBeFalse();

    expect($archived->isArchived())->toBeTrue()
        ->and($archived->isActive())->toBeFalse();
});

test('it covers incident and note model methods', function (): void {
    $owner = Person::factory()->create();
    $responder = Person::factory()->create(['workspace_id' => $owner->workspace_id]);

    $incident = Incident::factory()->create([
        'workspace_id' => $owner->workspace_id,
        'owner_id' => $owner->id,
        'responder_id' => $responder->id,
        'metadata' => ['source' => 'email'],
        'detected_at' => now()->subHour(),
        'contained_at' => now()->subMinutes(30),
        'resolved_at' => null,
        'severity' => IncidentSeverity::Medium,
        'status' => IncidentStatus::Investigating,
    ]);

    $note = IncidentNote::factory()->create([
        'workspace_id' => $incident->workspace_id,
        'incident_id' => $incident->id,
        'author_id' => $owner->id,
        'occurred_at' => now(),
        'metadata' => ['channel' => 'slack'],
    ]);

    expect($incident->owner)->toBeInstanceOf(Person::class)
        ->and($incident->responder)->toBeInstanceOf(Person::class);

    expect($incident->metadata)->toBeArray()
        ->and($incident->metadata['source'])->toBe('email');

    expect($incident->detected_at)->toBeInstanceOf(\Carbon\CarbonInterface::class)
        ->and($incident->contained_at)->toBeInstanceOf(\Carbon\CarbonInterface::class);

    expect($incident->notes)->toHaveCount(1)
        ->and($incident->notes->first()->id)->toBe($note->id);

    expect($note->incident)->toBeInstanceOf(Incident::class)
        ->and($note->author)->toBeInstanceOf(Person::class)
        ->and($note->occurred_at)->toBeInstanceOf(\Carbon\CarbonInterface::class);

    expect($note->metadata)->toBeArray()
        ->and($note->metadata['channel'])->toBe('slack');

    expect(Incident::newFactory())->toBeInstanceOf(\Trustbird\Database\Factories\Incident\IncidentFactory::class);
    expect(IncidentNote::newFactory())->toBeInstanceOf(\Trustbird\Database\Factories\Incident\IncidentNoteFactory::class);
});

