<?php

namespace App\Application;

use DateTimeImmutable;

final class Label
{
    private $label;
    private $dateTimeImmutable;

    public function __construct(
        string $label,
        DateTimeImmutable $dateTimeImmutable
    ) {
        $this->label = $label;
        $this->dateTimeImmutable = $dateTimeImmutable;
    }

    public function getLabel(): string
    {
        return $this->label;
    }

    public function getDateTimeImmutable(): DateTimeImmutable
    {
        return $this->dateTimeImmutable;
    }
}
