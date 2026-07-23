<?php

declare(strict_types=1);

use Illuminate\Support\Facades\Event;
use Trustbird\Ai\Contracts\GeneratesAiSuggestions;
use Trustbird\Ai\Enums\AiProviderDriver;
use Trustbird\Ai\Enums\AiSuggestionKind;
use Trustbird\Ai\Enums\AiSuggestionLogEvent;
use Trustbird\Ai\Enums\AiSuggestionStatus;
use Trustbird\Ai\Events\AiSuggestionApproved;
use Trustbird\Ai\Events\AiSuggestionRejected;
use Trustbird\Ai\Models\AiPrompt;
use Trustbird\Ai\Models\AiProvider;
use Trustbird\Ai\Models\AiSuggestion;
use Trustbird\Ai\Models\AiSuggestionLog;
use Trustbird\Controls\Models\Control;
use Trustbird\Facades\Trustbird;
use Trustbird\People\Models\Person;

beforeEach(fn () => Event::fake());

test('it can register an abstracted AI provider', function (): void {
    $provider = Trustbird::ai()->registerProvider(
        name: 'Workspace OpenAI',
        driver: AiProviderDriver::OpenAi,
        settings: ['model' => 'gpt-4.1'],
    );

    expect($provider)->toBeInstanceOf(AiProvider::class)
        ->and($provider->driver)->toBe(AiProviderDriver::OpenAi)
        ->and($provider->isActive())->toBeTrue();

    Event::assertDispatched('eloquent.created: '.AiProvider::class);
});

test('it can create a prompt template', function (): void {
    $prompt = Trustbird::ai()->createPrompt(
        key: 'control-from-interview',
        name: 'Control suggestion prompt',
        body: 'Propose a control for: {{answer}}',
        purpose: 'measure',
    );

    expect($prompt)->toBeInstanceOf(AiPrompt::class)
        ->and($prompt->key)->toBe('control-from-interview');
});

test('it records AI output as a pending suggestion not an automatic decision', function (): void {
    $provider = Trustbird::ai()->registerProvider(name: 'Custom', driver: AiProviderDriver::Custom);
    $prompt = Trustbird::ai()->createPrompt(
        key: 'risk-hint',
        name: 'Risk hint',
        body: 'Suggest a risk',
        workspaceId: $provider->workspace_id,
    );
    $control = Control::factory()->create(['workspace_id' => $provider->workspace_id]);
    $person = Person::factory()->create(['workspace_id' => $provider->workspace_id]);

    $suggestion = Trustbird::ai()->suggest(
        output: ['text' => 'Introduce MFA for admin accounts'],
        kind: AiSuggestionKind::Measure,
        provider: $provider,
        prompt: $prompt,
        subject: $control,
        title: 'Add MFA control',
        input: ['source' => 'interview'],
        modelName: 'demo-model',
        createdById: $person->id,
    );

    expect($suggestion)->toBeInstanceOf(AiSuggestion::class)
        ->and($suggestion->status)->toBe(AiSuggestionStatus::Pending)
        ->and($suggestion->isPending())->toBeTrue()
        ->and($suggestion->isApproved())->toBeFalse()
        ->and($suggestion->subject_id)->toBe($control->id);

    expect($suggestion->logs)->toHaveCount(1)
        ->and($suggestion->logs->first()->event)->toBe(AiSuggestionLogEvent::Created);
});

test('it can approve a pending suggestion with audit metadata', function (): void {
    $suggestion = AiSuggestion::factory()->create(['status' => AiSuggestionStatus::Pending]);
    $reviewer = Person::factory()->create(['workspace_id' => $suggestion->workspace_id]);

    $approved = Trustbird::ai()->approve(
        suggestion: $suggestion,
        reviewedById: $reviewer->id,
        reviewNotes: 'Looks correct for our context.',
    );

    expect($approved->status)->toBe(AiSuggestionStatus::Approved)
        ->and($approved->isApproved())->toBeTrue()
        ->and($approved->reviewed_by_id)->toBe($reviewer->id)
        ->and($approved->logs()->where('event', AiSuggestionLogEvent::Approved)->count())->toBe(1);

    Event::assertDispatched(AiSuggestionApproved::class);
});

test('it can reject a pending suggestion with audit metadata', function (): void {
    $suggestion = AiSuggestion::factory()->create(['status' => AiSuggestionStatus::Pending]);
    $reviewer = Person::factory()->create(['workspace_id' => $suggestion->workspace_id]);

    $rejected = Trustbird::ai()->reject(
        suggestion: $suggestion,
        reviewedById: $reviewer->id,
        reviewNotes: 'Not applicable.',
    );

    expect($rejected->status)->toBe(AiSuggestionStatus::Rejected)
        ->and($rejected->isRejected())->toBeTrue();

    Event::assertDispatched(AiSuggestionRejected::class);
});

test('it cannot approve a non-pending suggestion', function (): void {
    $suggestion = AiSuggestion::factory()->approved()->create();

    Trustbird::ai()->approve(suggestion: $suggestion);
})->throws(InvalidArgumentException::class, 'Only pending AI suggestions can be approved.');

test('it cannot reject a non-pending suggestion', function (): void {
    $suggestion = AiSuggestion::factory()->rejected()->create();

    Trustbird::ai()->reject(suggestion: $suggestion);
})->throws(InvalidArgumentException::class, 'Only pending AI suggestions can be rejected.');

test('it can withdraw a pending suggestion', function (): void {
    $suggestion = AiSuggestion::factory()->create(['status' => AiSuggestionStatus::Pending]);

    $withdrawn = Trustbird::ai()->withdraw(
        suggestion: $suggestion,
        reviewNotes: 'Superseded by a better prompt.',
    );

    expect($withdrawn->status)->toBe(AiSuggestionStatus::Withdrawn);
});

test('it cannot withdraw a non-pending suggestion', function (): void {
    $suggestion = AiSuggestion::factory()->approved()->create();

    Trustbird::ai()->withdraw(suggestion: $suggestion);
})->throws(InvalidArgumentException::class, 'Only pending AI suggestions can be withdrawn.');

test('it can update providers and prompts', function (): void {
    $provider = Trustbird::ai()->registerProvider(name: 'Old');
    $prompt = Trustbird::ai()->createPrompt(
        key: 'old-key',
        name: 'Old',
        body: 'Old body',
        workspaceId: $provider->workspace_id,
    );

    Trustbird::ai()->updateProvider(
        provider: $provider,
        name: 'New provider',
        isActive: false,
    );

    Trustbird::ai()->updatePrompt(
        prompt: $prompt,
        name: 'New prompt',
        body: 'Updated body',
    );

    expect($provider->fresh()->name)->toBe('New provider')
        ->and($provider->fresh()->isActive())->toBeFalse();
    expect($prompt->fresh()->body)->toBe('Updated body');
});

test('it covers AI model helpers, factories and provider contract', function (): void {
    $provider = AiProvider::factory()->create();
    $prompt = AiPrompt::factory()->create(['workspace_id' => $provider->workspace_id]);
    $person = Person::factory()->create(['workspace_id' => $provider->workspace_id]);
    $control = Control::factory()->create(['workspace_id' => $provider->workspace_id]);

    $suggestion = AiSuggestion::factory()->create([
        'workspace_id' => $provider->workspace_id,
        'provider_id' => $provider->id,
        'prompt_id' => $prompt->id,
        'subject_type' => Control::class,
        'subject_id' => $control->id,
        'created_by_id' => $person->id,
        'reviewed_by_id' => $person->id,
    ]);

    $log = AiSuggestionLog::factory()->create([
        'workspace_id' => $provider->workspace_id,
        'suggestion_id' => $suggestion->id,
        'actor_id' => $person->id,
    ]);

    expect($provider->suggestions)->toHaveCount(1);
    expect($prompt->suggestions)->toHaveCount(1);
    expect($suggestion->provider->id)->toBe($provider->id);
    expect($suggestion->prompt->id)->toBe($prompt->id);
    expect($suggestion->subject)->toBeInstanceOf(Control::class);
    expect($suggestion->createdBy->id)->toBe($person->id);
    expect($suggestion->reviewedBy->id)->toBe($person->id);
    expect($suggestion->logs)->toHaveCount(1);
    expect($log->suggestion->id)->toBe($suggestion->id);
    expect($log->actor->id)->toBe($person->id);

    $driver = new class implements GeneratesAiSuggestions
    {
        public function generate(string $promptBody, array $context = []): array
        {
            return [
                'title' => 'Demo',
                'output' => ['text' => $promptBody],
                'model_name' => 'stub',
            ];
        }
    };

    expect($driver->generate('hello', ['a' => 1])['model_name'])->toBe('stub');

    expect(AiProvider::newFactory())->toBeInstanceOf(\Trustbird\Database\Factories\Ai\AiProviderFactory::class);
    expect(AiPrompt::newFactory())->toBeInstanceOf(\Trustbird\Database\Factories\Ai\AiPromptFactory::class);
    expect(AiSuggestion::newFactory())->toBeInstanceOf(\Trustbird\Database\Factories\Ai\AiSuggestionFactory::class);
    expect(AiSuggestionLog::newFactory())->toBeInstanceOf(\Trustbird\Database\Factories\Ai\AiSuggestionLogFactory::class);
});
