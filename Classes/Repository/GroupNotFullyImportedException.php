<?php

declare(strict_types=1);

namespace SebastianHofer\BePermissions\Repository;

use SebastianHofer\BePermissions\Collection\BeGroupCollection;
use SebastianHofer\BePermissions\Model\BeGroup;
use Throwable;

final class GroupNotFullyImportedException extends \Exception
{
    private ?BeGroup $beGroup;
    private ?BeGroupCollection $beGroupCollection;

    public function __construct(string $message = '', int $code = 0, Throwable $previous = null, ?BeGroup $beGroup = null, ?BeGroupCollection $beGroupCollection = null)
    {
        parent::__construct($message, $code, $previous);
        $this->beGroup = $beGroup;
        $this->beGroupCollection = $beGroupCollection;
    }

    public function beGroup(): ?BeGroup
    {
        return $this->beGroup;
    }

    public function beGroupCollection(): ?BeGroupCollection
    {
        return $this->beGroupCollection;
    }
}
