<?php

declare(strict_types=1);

namespace Trustbird\Ai\Contracts;

/**
 * Application-level AI provider driver.
 *
 * Trustbird stores suggestions; concrete providers generate candidate output.
 * Implementations live outside this package and must never auto-apply changes.
 *
 * @return array{title?: string|null, output: array<string, mixed>, model_name?: string|null, provider_reference?: string|null}
 */
interface GeneratesAiSuggestions
{
    /**
     * @param  array<string, mixed>  $context
     * @return array{title?: string|null, output: array<string, mixed>, model_name?: string|null, provider_reference?: string|null}
     */
    public function generate(string $promptBody, array $context = []): array;
}
