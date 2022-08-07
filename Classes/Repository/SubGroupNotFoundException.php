<?php

declare(strict_types=1);

namespace SebastianHofer\BePermissions\Repository;

final class SubGroupNotFoundException extends \Exception
{
    private string $subGroupValue;

    public function __construct(string $message, string $subGroupValue)
    {
        parent::__construct($message);
        $this->subGroupValue = $subGroupValue;
    }

    public function subGroupValue(): string
    {
        return $this->subGroupValue;
    }
}
