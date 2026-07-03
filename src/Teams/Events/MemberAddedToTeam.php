<?php

declare(strict_types=1);

namespace Trustbird\Teams\Events;

use Illuminate\Foundation\Events\Dispatchable;
use Illuminate\Queue\SerializesModels;
use Trustbird\Teams\Contracts\HasTeams;

class MemberAddedToTeam
{
    use Dispatchable;
    use SerializesModels;

    /**
     * @param  array<int, string>  $personIds
     */
    public function __construct(
        public HasTeams $team,
        public array $personIds,
    ) {}
}
