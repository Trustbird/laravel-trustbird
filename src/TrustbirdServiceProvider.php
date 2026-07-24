<?php

declare(strict_types=1);

namespace Trustbird;

use Illuminate\Support\ServiceProvider;
use Trustbird\Ai\Contracts\HasAiPrompts;
use Trustbird\Ai\Contracts\HasAiProviders;
use Trustbird\Ai\Contracts\HasAiSuggestionLogs;
use Trustbird\Ai\Contracts\HasAiSuggestions;
use Trustbird\Ai\Models\AiPrompt;
use Trustbird\Ai\Models\AiProvider;
use Trustbird\Ai\Models\AiSuggestion;
use Trustbird\Ai\Models\AiSuggestionLog;
use Trustbird\Assets\Contracts\HasAssets;
use Trustbird\Assets\Models\Asset;
use Trustbird\Controls\Contracts\HasControlRelations;
use Trustbird\Controls\Contracts\HasControls;
use Trustbird\Controls\Models\Control;
use Trustbird\Controls\Models\ControlRelation;
use Trustbird\Documents\Contracts\HasDocuments;
use Trustbird\Documents\Models\Document;
use Trustbird\Evidence\Contracts\HasEvidence;
use Trustbird\Evidence\Contracts\HasEvidenceRelations;
use Trustbird\Evidence\Models\Evidence;
use Trustbird\Evidence\Models\EvidenceRelation;
use Trustbird\Frameworks\Contracts\HasFrameworkMappings;
use Trustbird\Frameworks\Contracts\HasFrameworkRequirements;
use Trustbird\Frameworks\Contracts\HasFrameworkVersions;
use Trustbird\Frameworks\Contracts\HasFrameworks;
use Trustbird\Frameworks\Models\Framework;
use Trustbird\Frameworks\Models\FrameworkMapping;
use Trustbird\Frameworks\Models\FrameworkRequirement;
use Trustbird\Frameworks\Models\FrameworkVersion;
use Trustbird\Incidents\Contracts\HasIncidentNotes;
use Trustbird\Incidents\Contracts\HasIncidents;
use Trustbird\Incidents\Models\Incident;
use Trustbird\Incidents\Models\IncidentNote;
use Trustbird\Interviews\Contracts\HasInterviewAnswers;
use Trustbird\Interviews\Contracts\HasInterviewQuestions;
use Trustbird\Interviews\Contracts\HasInterviews;
use Trustbird\Interviews\Models\Interview;
use Trustbird\Interviews\Models\InterviewAnswer;
use Trustbird\Interviews\Models\InterviewQuestion;
use Trustbird\People\Contracts\HasPeople;
use Trustbird\People\Models\Person;
use Trustbird\Reviews\Contracts\HasReviewReviewers;
use Trustbird\Reviews\Contracts\HasReviews;
use Trustbird\Reviews\Models\Review;
use Trustbird\Reviews\Models\ReviewReviewer;
use Trustbird\Risks\Contracts\HasRisks;
use Trustbird\Risks\Models\Risk;
use Trustbird\Policies\Contracts\HasPolicies;
use Trustbird\Policies\Models\Policy;
use Trustbird\Suppliers\Contracts\HasSupplierRelations;
use Trustbird\Suppliers\Contracts\HasSuppliers;
use Trustbird\Suppliers\Models\Supplier;
use Trustbird\Suppliers\Models\SupplierRelation;
use Trustbird\Tasks\Contracts\HasTaskLinks;
use Trustbird\Tasks\Contracts\HasTasks;
use Trustbird\Tasks\Models\Task;
use Trustbird\Tasks\Models\TaskRelation;
use Trustbird\Teams\Contracts\HasTeams;
use Trustbird\Teams\Models\Team;
use Trustbird\Workspaces\Contracts\HasWorkspaces;
use Trustbird\Workspaces\Models\Workspace;

final class TrustbirdServiceProvider extends ServiceProvider
{
    public function register(): void
    {
        $this->mergeConfigFrom(__DIR__.'/../config/trustbird.php', 'trustbird');

        $this->app->singleton('trustbird', function ($app) {
            return new TrustbirdManager;
        });

        $this->registerModels();
    }

    protected function registerModels(): void
    {
        $models = [
            'person' => [
                'contract' => HasPeople::class,
                'default' => Person::class,
            ],
            'workspace' => [
                'contract' => HasWorkspaces::class,
                'default' => Workspace::class,
            ],
            'asset' => [
                'contract' => HasAssets::class,
                'default' => Asset::class,
            ],
            'ai_provider' => [
                'contract' => HasAiProviders::class,
                'default' => AiProvider::class,
            ],
            'ai_prompt' => [
                'contract' => HasAiPrompts::class,
                'default' => AiPrompt::class,
            ],
            'ai_suggestion' => [
                'contract' => HasAiSuggestions::class,
                'default' => AiSuggestion::class,
            ],
            'ai_suggestion_log' => [
                'contract' => HasAiSuggestionLogs::class,
                'default' => AiSuggestionLog::class,
            ],
            'control' => [
                'contract' => HasControls::class,
                'default' => Control::class,
            ],
            'control_relation' => [
                'contract' => HasControlRelations::class,
                'default' => ControlRelation::class,
            ],
            'document' => [
                'contract' => HasDocuments::class,
                'default' => Document::class,
            ],
            'evidence' => [
                'contract' => HasEvidence::class,
                'default' => Evidence::class,
            ],
            'evidence_relation' => [
                'contract' => HasEvidenceRelations::class,
                'default' => EvidenceRelation::class,
            ],
            'framework' => [
                'contract' => HasFrameworks::class,
                'default' => Framework::class,
            ],
            'framework_version' => [
                'contract' => HasFrameworkVersions::class,
                'default' => FrameworkVersion::class,
            ],
            'framework_requirement' => [
                'contract' => HasFrameworkRequirements::class,
                'default' => FrameworkRequirement::class,
            ],
            'framework_mapping' => [
                'contract' => HasFrameworkMappings::class,
                'default' => FrameworkMapping::class,
            ],
            'team' => [
                'contract' => HasTeams::class,
                'default' => Team::class,
            ],
            'risk' => [
                'contract' => HasRisks::class,
                'default' => Risk::class,
            ],
            'review' => [
                'contract' => HasReviews::class,
                'default' => Review::class,
            ],
            'review_reviewer' => [
                'contract' => HasReviewReviewers::class,
                'default' => ReviewReviewer::class,
            ],
            'policy' => [
                'contract' => HasPolicies::class,
                'default' => Policy::class,
            ],
            'incident' => [
                'contract' => HasIncidents::class,
                'default' => Incident::class,
            ],
            'incident_note' => [
                'contract' => HasIncidentNotes::class,
                'default' => IncidentNote::class,
            ],
            'interview' => [
                'contract' => HasInterviews::class,
                'default' => Interview::class,
            ],
            'interview_question' => [
                'contract' => HasInterviewQuestions::class,
                'default' => InterviewQuestion::class,
            ],
            'interview_answer' => [
                'contract' => HasInterviewAnswers::class,
                'default' => InterviewAnswer::class,
            ],
            'supplier' => [
                'contract' => HasSuppliers::class,
                'default' => Supplier::class,
            ],
            'supplier_relation' => [
                'contract' => HasSupplierRelations::class,
                'default' => SupplierRelation::class,
            ],
            'task' => [
                'contract' => HasTasks::class,
                'default' => Task::class,
            ],
            'task_relation' => [
                'contract' => HasTaskLinks::class,
                'default' => TaskRelation::class,
            ],
        ];

        foreach ($models as $key => $config) {
            $concrete = $this->app['config']["trustbird.models.{$key}"] ?? $config['default'];

            $this->app->bind($config['contract'], $concrete);

            if ($concrete !== $config['default']) {
                $this->app->bind($config['default'], $concrete);
            }
        }
    }

    public function boot(): void
    {
        if ($this->app->runningInConsole()) {
            $this->commands([
                Workspaces\Commands\InstallCommand::class,
            ]);

            $this->publishes([
                __DIR__.'/../config/trustbird.php' => config_path('trustbird.php'),
            ], 'trustbird-config');
        }
    }
}
