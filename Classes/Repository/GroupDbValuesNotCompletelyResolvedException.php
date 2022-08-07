<?php

declare(strict_types=1);

namespace SebastianHofer\BePermissions\Repository;

final class GroupDbValuesNotCompletelyResolvedException extends \Exception
{
    /**
     * @var array<string, string>
     */
    private array $groupDbValues;

    /**
     * @param string $message
     * @param array<string, string> $groupDbValues
     */
    public function __construct(string $message, array $groupDbValues)
    {
        parent::__construct($message);
        $this->groupDbValues = $groupDbValues;
    }

    /**
     * @return array<string, string>
     */
    public function groupDbValues(): array
    {
        return $this->groupDbValues;
    }
}
