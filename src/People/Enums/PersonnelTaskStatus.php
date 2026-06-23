<?php

namespace Trustbird\People\Enums;

enum PersonnelTaskStatus: string
{
    case NoTasks = 'no_tasks';
    case Complete = 'complete';
    case Overdue = 'overdue';
    case DueSoon = 'due_soon';
}