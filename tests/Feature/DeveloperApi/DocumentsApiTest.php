<?php

declare(strict_types=1);

use Illuminate\Support\Facades\Event;
use Trustbird\Documents\Models\Document;
use Trustbird\Facades\Trustbird;

beforeEach(fn () => Event::fake());

it('can create a document via the facade', function () {
    $document = Trustbird::documents()->create(
        title: 'Incident response playbook',
        content: 'Step 1: identify the incident.',
    );

    expect($document)->toBeInstanceOf(Document::class)
        ->and($document->title)->toBe('Incident response playbook')
        ->and($document->versions)->toHaveCount(1);

    Event::assertDispatched('eloquent.created: '.Document::class);
});
