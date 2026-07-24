## Interviews

Interviews gather company context through understandable questions. Sessions store questions and answers without depending on a specific UI.

Answers can later drive suggestions for policies, risks, measures and evidence via `suggestion_domain` and `suggestion_key` on each question.

### Concepts

- **Interview session** (`InterviewStatus`): draft, in progress, completed, archived
- **Question** (`InterviewQuestionType`): text, boolean, single choice, multi choice, scale
- **Answer**: JSON `value` payload plus optional notes and answered-by person
- **Progress**: `question_count`, `answered_count`, and `progressPercent()`
- **Suggestion hooks**: optional `suggestion_domain` (`policy`, `risk`, `measure`, `evidence`) and `suggestion_key`

### Create a session

```php
use Trustbird\Facades\Trustbird;

$interview = Trustbird::interviews()->create(
    title: 'Company context questionnaire',
    ownerId: $person->id,
);
```

### Add a question

```php
use Trustbird\Interviews\Enums\InterviewQuestionType;
use Trustbird\Interviews\Enums\InterviewSuggestionDomain;

$question = Trustbird::interviews()->addQuestion(
    interview: $interview,
    prompt: 'Do you process personal data of customers?',
    type: InterviewQuestionType::Boolean,
    suggestionDomain: InterviewSuggestionDomain::Policy,
    suggestionKey: 'privacy-policy',
);
```

### Record an answer

```php
Trustbird::interviews()->answer(
    interview: $interview,
    question: $question,
    value: true,
    answeredById: $person->id,
);
```

### Complete the session

`complete()` requires every question with `is_required: true` to have an answer. Do not set `Completed` or `Archived` via `create()` / `update()` — use `complete()` and `archive()`.

```php
Trustbird::interviews()->complete(interview: $interview);

Trustbird::interviews()->archive(interview: $interview);
```
