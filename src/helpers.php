<?php

declare(strict_types=1);

use Trustbird\TrustbirdManager;

if (! function_exists('trustbird')) {
    function trustbird(): TrustbirdManager
    {
        return app('trustbird');
    }
}
