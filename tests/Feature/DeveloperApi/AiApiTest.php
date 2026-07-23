<?php

declare(strict_types=1);

use Illuminate\Support\Facades\Event;
use Trustbird\Ai\Enums\AiSuggestionKind;
use Trustbird\Ai\Models\AiSuggestion;
use Trustbird\Facades\Trustbird;

beforeEach(fn () => Event::fake());

it('can record an AI suggestion via the facade', function () {
    $suggestion = Trustbird::ai()->suggest(
        output: ['text' => 'Document your backup restore test.'],
        kind: AiSuggestionKind::Evidence,
        title: 'Evidence suggestion',
    );

    expect($suggestion)->toBeInstanceOf(AiSuggestion::class)
        ->and($suggestion->kind)->toBe(AiSuggestionKind::Evidence);

    Event::assertDispatched('eloquent.created: '.AiSuggestion::class);
});
