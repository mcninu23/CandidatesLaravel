<?php

declare(strict_types=1);

namespace App\Domain\Candidate\ValidationRules;

final class ValidationResult
{
    public function __construct(
        public readonly string $ruleName,
        public readonly bool $passed,
        public readonly ?string $message = null,
    ) {
    }

    public static function pass(string $ruleName): self
    {
        return new self($ruleName, true, null);
    }

    public static function fail(string $ruleName, string $message): self
    {
        return new self($ruleName, false, $message);
    }
}
