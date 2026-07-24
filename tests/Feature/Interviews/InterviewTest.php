<?php

declare(strict_types=1);

use Illuminate\Support\Facades\Event;
use Trustbird\Facades\Trustbird;
use Trustbird\Interviews\Enums\InterviewQuestionType;
use Trustbird\Interviews\Enums\InterviewStatus;
use Trustbird\Interviews\Enums\InterviewSuggestionDomain;
use Trustbird\Interviews\Events\InterviewArchived;
use Trustbird\Interviews\Events\InterviewCompleted;
use Trustbird\Interviews\Models\Interview;
use Trustbird\Interviews\Models\InterviewAnswer;
use Trustbird\Interviews\Models\InterviewQuestion;
use Trustbird\People\Models\Person;
use Trustbird\Workspaces\Models\Workspace;

beforeEach(fn () => Event::fake());

test('it can create an interview session', function (): void {
    $owner = Person::factory()->create();

    $interview = Trustbird::interviews()->create(
        title: 'Company context questionnaire',
        description: 'Gather plain-language context for suggestions.',
        ownerId: $owner->id,
    );

    expect($interview)->toBeInstanceOf(Interview::class)
        ->and($interview->title)->toBe('Company context questionnaire')
        ->and($interview->status)->toBe(InterviewStatus::Draft)
        ->and($interview->progressPercent())->toBe(0);

    Event::assertDispatched('eloquent.created: '.Interview::class);
});

test('it can add questions with suggestion hooks', function (): void {
    $interview = Interview::factory()->create();

    $question = Trustbird::interviews()->addQuestion(
        interview: $interview,
        prompt: 'Do you process personal data of customers?',
        type: InterviewQuestionType::Boolean,
        suggestionDomain: InterviewSuggestionDomain::Policy,
        suggestionKey: 'privacy-policy',
        position: 1,
    );

    expect($question)->toBeInstanceOf(InterviewQuestion::class)
        ->and($question->suggestion_domain)->toBe(InterviewSuggestionDomain::Policy)
        ->and($question->suggestion_key)->toBe('privacy-policy');

    expect($interview->fresh()->question_count)->toBe(1);
});

test('it can answer a question and updates progress', function (): void {
    $interview = Interview::factory()->create();
    $person = Person::factory()->create(['workspace_id' => $interview->workspace_id]);

    $question = Trustbird::interviews()->addQuestion(
        interview: $interview,
        prompt: 'Which cloud providers do you use?',
        type: InterviewQuestionType::Text,
        suggestionDomain: InterviewSuggestionDomain::Risk,
        suggestionKey: 'cloud-dependency',
    );

    $answer = Trustbird::interviews()->answer(
        interview: $interview,
        question: $question,
        value: 'AWS and Cloudflare',
        answeredById: $person->id,
    );

    expect($answer)->toBeInstanceOf(InterviewAnswer::class)
        ->and($answer->value)->toBe(['value' => 'AWS and Cloudflare'])
        ->and($answer->answered_by_id)->toBe($person->id);

    $interview = $interview->fresh();

    expect($interview->status)->toBe(InterviewStatus::InProgress)
        ->and($interview->isInProgress())->toBeTrue()
        ->and($interview->answered_count)->toBe(1)
        ->and($interview->question_count)->toBe(1)
        ->and($interview->progressPercent())->toBe(100);
});

test('it can answer with a pre-normalized value payload', function (): void {
    $interview = Interview::factory()->create();
    $question = Trustbird::interviews()->addQuestion(
        interview: $interview,
        prompt: 'Select your primary hosting region',
        type: InterviewQuestionType::SingleChoice,
        options: ['eu', 'us'],
    );

    $answer = Trustbird::interviews()->answer(
        interview: $interview,
        question: $question,
        value: ['value' => 'eu'],
    );

    expect($answer->value)->toBe(['value' => 'eu']);
});

test('it cannot answer a question from another interview', function (): void {
    $interview = Interview::factory()->create();
    $otherQuestion = InterviewQuestion::factory()->create();

    Trustbird::interviews()->answer(
        interview: $interview,
        question: $otherQuestion,
        value: 'nope',
    );
})->throws(InvalidArgumentException::class, 'The question does not belong to this interview.');

test('it can complete an interview and dispatches event', function (): void {
    $interview = Interview::factory()->inProgress()->create();

    $completed = Trustbird::interviews()->complete(interview: $interview);

    expect($completed->status)->toBe(InterviewStatus::Completed)
        ->and($completed->isCompleted())->toBeTrue()
        ->and($completed->completed_at)->not->toBeNull();

    Event::assertDispatched(InterviewCompleted::class);
});

test('it cannot complete an already completed interview', function (): void {
    $interview = Interview::factory()->completed()->create();

    Trustbird::interviews()->complete(interview: $interview);
})->throws(InvalidArgumentException::class, 'This interview is already completed.');

test('it cannot complete an archived interview', function (): void {
    $interview = Interview::factory()->create(['status' => InterviewStatus::Archived]);

    Trustbird::interviews()->complete(interview: $interview);
})->throws(InvalidArgumentException::class, 'Archived interviews cannot be completed.');

test('it cannot complete an interview with unanswered required questions', function (): void {
    $interview = Interview::factory()->create();

    Trustbird::interviews()->addQuestion(
        interview: $interview,
        prompt: 'Required question?',
        isRequired: true,
    );

    Trustbird::interviews()->complete(interview: $interview);
})->throws(InvalidArgumentException::class, 'All required questions must be answered before completing the interview.');

test('it cannot set completed status via update', function (): void {
    $interview = Interview::factory()->create();

    Trustbird::interviews()->update(
        interview: $interview,
        status: InterviewStatus::Completed,
    );
})->throws(InvalidArgumentException::class, 'Use complete() or archive() to finish an interview. Status cannot be set to completed or archived via update().');

test('it cannot create an interview as completed or archived', function (): void {
    Trustbird::interviews()->create(
        title: 'Already done',
        status: InterviewStatus::Completed,
    );
})->throws(InvalidArgumentException::class, 'Use complete() or archive() to finish an interview. Status cannot be set to completed or archived via create().');

test('it can archive an interview and dispatches event', function (): void {
    $interview = Interview::factory()->completed()->create();

    $archived = Trustbird::interviews()->archive(interview: $interview);

    expect($archived->status)->toBe(InterviewStatus::Archived)
        ->and($archived->isArchived())->toBeTrue();

    Event::assertDispatched(InterviewArchived::class);
});

test('it cannot archive an already archived interview', function (): void {
    $interview = Interview::factory()->create(['status' => InterviewStatus::Archived]);

    Trustbird::interviews()->archive(interview: $interview);
})->throws(InvalidArgumentException::class, 'This interview is already archived.');

test('it cannot create an interview as archived', function (): void {
    Trustbird::interviews()->create(
        title: 'Already archived',
        status: InterviewStatus::Archived,
    );
})->throws(InvalidArgumentException::class, 'Use complete() or archive() to finish an interview. Status cannot be set to completed or archived via create().');

test('it cannot answer a question from another workspace', function (): void {
    $interview = Interview::factory()->create();
    $question = InterviewQuestion::factory()->create([
        'interview_id' => $interview->id,
        'workspace_id' => Workspace::factory()->create()->id,
    ]);

    Trustbird::interviews()->answer(
        interview: $interview,
        question: $question,
        value: 'nope',
    );
})->throws(InvalidArgumentException::class, 'Related object must belong to the same workspace.');

test('it allows answering when the question has no workspace id', function (): void {
    $interview = Interview::factory()->create();
    $question = Trustbird::interviews()->addQuestion(
        interview: $interview,
        prompt: 'Optional workspace?',
    );
    $question->forceFill(['workspace_id' => null])->saveQuietly();

    $answer = Trustbird::interviews()->answer(
        interview: $interview,
        question: $question->fresh(),
        value: 'ok',
    );

    expect($answer->value)->toBe(['value' => 'ok']);
});

test('it cannot modify a completed interview', function (): void {
    $interview = Interview::factory()->completed()->create();

    Trustbird::interviews()->addQuestion(
        interview: $interview,
        prompt: 'Too late?',
    );
})->throws(InvalidArgumentException::class, 'Completed or archived interviews cannot be modified.');

test('it can update interview and question metadata', function (): void {
    $interview = Interview::factory()->create(['title' => 'Old']);
    $question = Trustbird::interviews()->addQuestion(
        interview: $interview,
        prompt: 'Original prompt?',
    );

    Trustbird::interviews()->update(
        interview: $interview,
        title: 'Updated questionnaire',
    );

    Trustbird::interviews()->updateQuestion(
        question: $question,
        prompt: 'Updated prompt?',
        helpText: 'Answer in plain language.',
    );

    expect($interview->fresh()->title)->toBe('Updated questionnaire');
    expect($question->fresh()->prompt)->toBe('Updated prompt?')
        ->and($question->fresh()->help_text)->toBe('Answer in plain language.');
});

test('it covers interview model helpers and factories', function (): void {
    $owner = Person::factory()->create();
    $interview = Interview::factory()->create([
        'workspace_id' => $owner->workspace_id,
        'owner_id' => $owner->id,
        'question_count' => 4,
        'answered_count' => 1,
        'metadata' => ['source' => 'onboarding'],
    ]);

    $question = InterviewQuestion::factory()->create([
        'workspace_id' => $interview->workspace_id,
        'interview_id' => $interview->id,
        'suggestion_domain' => InterviewSuggestionDomain::Measure,
        'suggestion_key' => 'access-control',
    ]);

    $answer = InterviewAnswer::factory()->create([
        'workspace_id' => $interview->workspace_id,
        'interview_id' => $interview->id,
        'question_id' => $question->id,
        'answered_by_id' => $owner->id,
    ]);

    expect($interview->owner)->toBeInstanceOf(Person::class);
    expect($interview->questions)->toHaveCount(1);
    expect($interview->answers)->toHaveCount(1);
    expect($interview->progressPercent())->toBe(25);
    expect($question->interview->id)->toBe($interview->id);
    expect($question->answer->id)->toBe($answer->id);
    expect($answer->question->id)->toBe($question->id);
    expect($answer->answeredBy->id)->toBe($owner->id);
    expect($answer->interview->id)->toBe($interview->id);

    $empty = Interview::factory()->create(['question_count' => 0, 'answered_count' => 0]);
    expect($empty->progressPercent())->toBe(0);

    expect(Interview::newFactory())->toBeInstanceOf(\Trustbird\Database\Factories\Interview\InterviewFactory::class);
    expect(InterviewQuestion::newFactory())->toBeInstanceOf(\Trustbird\Database\Factories\Interview\InterviewQuestionFactory::class);
    expect(InterviewAnswer::newFactory())->toBeInstanceOf(\Trustbird\Database\Factories\Interview\InterviewAnswerFactory::class);
});
