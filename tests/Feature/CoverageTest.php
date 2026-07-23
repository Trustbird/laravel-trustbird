<?php

use Trustbird\Ai\Enums\AiProviderDriver;
use Trustbird\Ai\Enums\AiSuggestionKind;
use Trustbird\Ai\Enums\AiSuggestionLogEvent;
use Trustbird\Ai\Enums\AiSuggestionStatus;
use Trustbird\Ai\Events\AiSuggestionApproved;
use Trustbird\Ai\Events\AiSuggestionRejected;
use Trustbird\Ai\Models\AiSuggestion;
use Trustbird\Assets\Enums\AssetKind;
use Trustbird\Controls\Enums\ControlStatus;
use Trustbird\Controls\Events\ControlApproved;
use Trustbird\Controls\Models\Control;
use Trustbird\Documents\Enums\DocumentVersionStatus;
use Trustbird\Documents\Events\DocumentReviewed;
use Trustbird\Documents\Events\DocumentVersionDrafted;
use Trustbird\Documents\Events\DocumentVersionPublished;
use Trustbird\Documents\Models\Document;
use Trustbird\Documents\Models\DocumentVersion;
use Trustbird\Evidence\Enums\EvidenceStatus;
use Trustbird\Evidence\Enums\EvidenceType;
use Trustbird\Evidence\Events\EvidenceReviewed;
use Trustbird\Evidence\Models\Evidence;
use Trustbird\Frameworks\Enums\FrameworkMappingCoverage;
use Trustbird\Frameworks\Enums\FrameworkVersionStatus;
use Trustbird\Frameworks\Events\FrameworkVersionDrafted;
use Trustbird\Frameworks\Events\FrameworkVersionPublished;
use Trustbird\Frameworks\Models\Framework;
use Trustbird\Interviews\Enums\InterviewQuestionType;
use Trustbird\Interviews\Enums\InterviewStatus;
use Trustbird\Interviews\Enums\InterviewSuggestionDomain;
use Trustbird\Interviews\Events\InterviewCompleted;
use Trustbird\Interviews\Models\Interview;
use Trustbird\People\Actions\MarkPersonnelTaskComplete;
use Trustbird\People\Actions\RecordPersonnelReminder;
use Trustbird\Reviews\Enums\ReviewerRole;
use Trustbird\Reviews\Enums\ReviewStatus;
use Trustbird\Reviews\Events\ReviewCompleted;
use Trustbird\Reviews\Events\ReviewReopened;
use Trustbird\Reviews\Events\ReviewScheduled;
use Trustbird\Reviews\Models\Review;
use Trustbird\Risks\Events\RiskReviewed;
use Trustbird\Risks\Enums\RiskStatus;
use Trustbird\Risks\Enums\RiskTreatment;
use Trustbird\Risks\Enums\RiskLevel;
use Trustbird\Risks\Models\Risk;
use Trustbird\Policies\Events\PolicyReviewed;
use Trustbird\Policies\Events\PolicyVersionDrafted;
use Trustbird\Policies\Events\PolicyVersionUpdated;
use Trustbird\Policies\Events\PolicyVersionPublished;
use Trustbird\Policies\Enums\PolicyVersionStatus;
use Trustbird\Policies\Models\Policy;
use Trustbird\Incidents\Enums\IncidentSeverity;
use Trustbird\Incidents\Enums\IncidentStatus;
use Trustbird\Suppliers\Enums\SupplierCriticality;
use Trustbird\Suppliers\Enums\SupplierStatus;
use Trustbird\Tasks\Enums\TaskPriority;
use Trustbird\Tasks\Enums\TaskStatus;
use Trustbird\People\Enums\EmploymentStatus;
use Trustbird\People\Enums\EmploymentType;
use Trustbird\People\Enums\PersonnelTaskStatus;
use Trustbird\People\Events\PersonnelReminderRecorded;
use Trustbird\People\Events\PersonnelTaskMarkedComplete;
use Trustbird\People\Events\PersonTerminated;
use Trustbird\People\Models\Person;
use Trustbird\TrustbirdServiceProvider;

it('instantiates events', function (): void {
    $person = Person::factory()->create();

    expect(new PersonTerminated($person))->toBeInstanceOf(PersonTerminated::class);

    $risk = Risk::factory()->create();
    expect(new RiskReviewed($risk))->toBeInstanceOf(RiskReviewed::class);

    $policy = Policy::factory()->withDraftVersion()->create();
    $version = $policy->versions->first();

    expect(new PolicyReviewed($policy))->toBeInstanceOf(PolicyReviewed::class);
    expect(new PolicyVersionDrafted($policy, $version))->toBeInstanceOf(PolicyVersionDrafted::class);
    expect(new PolicyVersionUpdated($version))->toBeInstanceOf(PolicyVersionUpdated::class);
    expect(new PolicyVersionPublished($policy, $version))->toBeInstanceOf(PolicyVersionPublished::class);

    $control = Control::factory()->make();
    expect(new ControlApproved($control))->toBeInstanceOf(ControlApproved::class);

    $document = Document::factory()->withDraftVersion()->create();
    $documentVersion = $document->versions->first();
    expect(new DocumentVersionDrafted($document, $documentVersion))->toBeInstanceOf(DocumentVersionDrafted::class);
    expect(new DocumentVersionPublished($document, $documentVersion))->toBeInstanceOf(DocumentVersionPublished::class);
    expect(new DocumentReviewed($document))->toBeInstanceOf(DocumentReviewed::class);

    $evidence = Evidence::factory()->make();
    expect(new EvidenceReviewed($evidence))->toBeInstanceOf(EvidenceReviewed::class);

    $framework = Framework::factory()->withDraftVersion()->create();
    $frameworkVersion = $framework->versions->first();
    expect(new FrameworkVersionDrafted($framework, $frameworkVersion))->toBeInstanceOf(FrameworkVersionDrafted::class);
    expect(new FrameworkVersionPublished($framework, $frameworkVersion))->toBeInstanceOf(FrameworkVersionPublished::class);

    $interview = Interview::factory()->make();
    expect(new InterviewCompleted($interview))->toBeInstanceOf(InterviewCompleted::class);

    $aiSuggestion = AiSuggestion::factory()->make();
    expect(new AiSuggestionApproved($aiSuggestion))->toBeInstanceOf(AiSuggestionApproved::class);
    expect(new AiSuggestionRejected($aiSuggestion))->toBeInstanceOf(AiSuggestionRejected::class);

    $review = Review::factory()->make();
    expect(new ReviewScheduled($review))->toBeInstanceOf(ReviewScheduled::class);
    expect(new ReviewCompleted($review))->toBeInstanceOf(ReviewCompleted::class);
    expect(new ReviewReopened($review))->toBeInstanceOf(ReviewReopened::class);
});

it('can instantiate service provider', function (): void {
    $provider = new TrustbirdServiceProvider(app());
    expect($provider)->toBeInstanceOf(TrustbirdServiceProvider::class);

    $provider->register();
    $provider->boot();

    // Test class definitions for empty classes
    expect(new MarkPersonnelTaskComplete)->toBeInstanceOf(MarkPersonnelTaskComplete::class);
    expect(new RecordPersonnelReminder)->toBeInstanceOf(RecordPersonnelReminder::class);

    // Test new event constructors
    $person = Person::factory()->make();
    expect(new PersonnelTaskMarkedComplete($person, ['test' => 1]))->toBeInstanceOf(PersonnelTaskMarkedComplete::class);
    expect(new PersonnelReminderRecorded($person, ['test' => 1]))->toBeInstanceOf(PersonnelReminderRecorded::class);

    expect(true)->toBeTrue();
});

it('covers all enums', function (): void {
    expect(EmploymentType::cases())->toBeArray()
        ->and(EmploymentType::Employee->value)->toBe('employee');

    expect(EmploymentStatus::cases())->toBeArray()
        ->and(EmploymentStatus::Active->value)->toBe('active');

    expect(PersonnelTaskStatus::cases())->toBeArray()
        ->and(PersonnelTaskStatus::Complete->value)->toBe('complete');

    expect(AssetKind::cases())->toBeArray()
        ->and(AssetKind::Device->value)->toBe('device');

    expect(RiskStatus::cases())->toBeArray()
        ->and(RiskStatus::Open->value)->toBe('open');

    expect(RiskTreatment::cases())->toBeArray()
        ->and(RiskTreatment::Reduce->value)->toBe('reduce');

    expect(RiskLevel::cases())->toBeArray()
        ->and(RiskLevel::High->value)->toBe('high');

    expect(PolicyVersionStatus::cases())->toBeArray()
        ->and(PolicyVersionStatus::Draft->value)->toBe('draft');

    expect(IncidentSeverity::cases())->toBeArray()
        ->and(IncidentSeverity::Critical->value)->toBe('critical');

    expect(IncidentStatus::cases())->toBeArray()
        ->and(IncidentStatus::Open->value)->toBe('open');

    expect(SupplierStatus::cases())->toBeArray()
        ->and(SupplierStatus::Active->value)->toBe('active');

    expect(SupplierCriticality::cases())->toBeArray()
        ->and(SupplierCriticality::Critical->value)->toBe('critical');
    expect(TaskStatus::cases())->toBeArray()
        ->and(TaskStatus::Open->value)->toBe('open');

    expect(TaskPriority::cases())->toBeArray()
        ->and(TaskPriority::Urgent->value)->toBe('urgent');

    expect(ControlStatus::cases())->toBeArray()
        ->and(ControlStatus::Active->value)->toBe('active');

    expect(DocumentVersionStatus::cases())->toBeArray()
        ->and(DocumentVersionStatus::Draft->value)->toBe('draft');

    expect(EvidenceType::cases())->toBeArray()
        ->and(EvidenceType::Link->value)->toBe('link');

    expect(EvidenceStatus::cases())->toBeArray()
        ->and(EvidenceStatus::Active->value)->toBe('active');

    expect(ReviewStatus::cases())->toBeArray()
        ->and(ReviewStatus::Scheduled->value)->toBe('scheduled');

    expect(ReviewerRole::cases())->toBeArray()
        ->and(ReviewerRole::Primary->value)->toBe('primary');

    expect(FrameworkVersionStatus::cases())->toBeArray()
        ->and(FrameworkVersionStatus::Draft->value)->toBe('draft');

    expect(FrameworkMappingCoverage::cases())->toBeArray()
        ->and(FrameworkMappingCoverage::Full->value)->toBe('full');

    expect(InterviewStatus::cases())->toBeArray()
        ->and(InterviewStatus::InProgress->value)->toBe('in_progress');

    expect(InterviewQuestionType::cases())->toBeArray()
        ->and(InterviewQuestionType::Boolean->value)->toBe('boolean');

    expect(InterviewSuggestionDomain::cases())->toBeArray()
        ->and(InterviewSuggestionDomain::Policy->value)->toBe('policy');

    expect(AiProviderDriver::cases())->toBeArray()
        ->and(AiProviderDriver::OpenAi->value)->toBe('openai');

    expect(AiSuggestionStatus::cases())->toBeArray()
        ->and(AiSuggestionStatus::Pending->value)->toBe('pending');

    expect(AiSuggestionKind::cases())->toBeArray()
        ->and(AiSuggestionKind::Measure->value)->toBe('measure');

    expect(AiSuggestionLogEvent::cases())->toBeArray()
        ->and(AiSuggestionLogEvent::Approved->value)->toBe('approved');
});
