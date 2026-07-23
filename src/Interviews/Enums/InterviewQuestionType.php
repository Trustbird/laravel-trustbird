<?php

declare(strict_types=1);

namespace Trustbird\Interviews\Enums;

enum InterviewQuestionType: string
{
    case Text = 'text';

    case Boolean = 'boolean';

    case SingleChoice = 'single_choice';

    case MultiChoice = 'multi_choice';

    case Scale = 'scale';
}
