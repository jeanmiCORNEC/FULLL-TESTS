<?php

declare(strict_types=1);

namespace Fulll\Domain;


class Vehicle
{
    private string $plateNumber;

    public function __construct(string $plateNumber)
    {
        $this->plateNumber = $plateNumber;
    }

    public function getPlateNumber(): string
    {
        return $this->plateNumber;
    }
}