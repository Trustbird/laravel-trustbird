<?php

declare(strict_types=1);

use Illuminate\Support\Facades\Event;
use Trustbird\Facades\Trustbird;
use Trustbird\Incidents\Enums\IncidentSeverity;
use Trustbird\Incidents\Enums\IncidentStatus;
use Trustbird\Incidents\Models\Incident;
use Trustbird\Incidents\Models\IncidentNote;

beforeEach(fn () => Event::fake());

it('can create an incident via the facade', function () {
    $incident = Trustbird::incidents()->create(
        title: 'Phishing report',
        severity: IncidentSeverity::Medium,
    );

    expect($incident)->toBeInstanceOf(Incident::class)
        ->and($incident->title)->toBe('Phishing report')
        ->and($incident->severity)->toBe(IncidentSeverity::Medium)
        ->and($incident->status)->toBe(IncidentStatus::Open);

    Event::assertDispatched('eloquent.created: '.Incident::class);
});

it('can update an incident via the facade', function () {
    $incident = Incident::factory()->create(['title' => 'Old']);

    $updated = Trustbird::incidents()->update($incident,
        title: 'New',
        status: IncidentStatus::Investigating,
    );

    expect($updated->title)->toBe('New')
        ->and($updated->status)->toBe(IncidentStatus::Investigating);

    Event::assertDispatched('eloquent.updated: '.Incident::class);
});

it('can add a note via the facade', function () {
    $incident = Incident::factory()->create();

    $note = Trustbird::incidents()->addNote(
        incident: $incident,
        body: 'Note for testing.',
    );

    expect($note)->toBeInstanceOf(IncidentNote::class)
        ->and($note->incident_id)->toBe($incident->id)
        ->and($note->body)->toBe('Note for testing.');

    Event::assertDispatched('eloquent.created: '.IncidentNote::class);
});

