<?php

declare(strict_types=1);

namespace SebastianHofer\BePermissions\Collection;

use SebastianHofer\BePermissions\Configuration\BeGroupConfiguration;
use SebastianHofer\BePermissions\Model\BeGroup;
use SebastianHofer\BePermissions\Value\Identifier;

final class BeGroupOverviewItem
{
    private ?BeGroup $beGroupRecord = null;
    private ?BeGroupConfiguration $beGroupConfiguration = null;
    private Identifier $identifier;

    public function __construct(Identifier $identifier)
    {
        $this->identifier = $identifier;
    }

    public function addBeGroupRecord(BeGroup $beGroup): void
    {
        if ((string)$this->identifier != (string)$beGroup->identifier()) {
            throw new \InvalidArgumentException('Identifier of given be_groups record does not match!');
        }

        $this->beGroupRecord = $beGroup;
    }

    public function addBeGroupConfiguration(BeGroupConfiguration $beGroupConfiguration): void
    {
        if ((string)$this->identifier != (string)$beGroupConfiguration->identifier()) {
            throw new \InvalidArgumentException('Identifier of given be_groups configuration does not match!');
        }

        $this->beGroupConfiguration = $beGroupConfiguration;
    }

    public function beGroupRecord(): ?BeGroup
    {
        return $this->beGroupRecord;
    }

    public function beGroupConfiguration(): ?BeGroupConfiguration
    {
        return $this->beGroupConfiguration;
    }

    public function beGroupRecordExists(): bool
    {
        return $this->beGroupRecord() instanceof BeGroup;
    }

    public function getBeGroupRecordExists(): bool
    {
        return $this->beGroupRecordExists();
    }

    public function beGroupConfigurationExists(): bool
    {
        return $this->beGroupConfiguration() instanceof BeGroupConfiguration;
    }

    public function getBeGroupConfigurationExists(): bool
    {
        return $this->beGroupConfigurationExists();
    }

    public function getIdentifier(): Identifier
    {
        return $this->identifier;
    }

    public function getBeGroupRecord(): ?BeGroup
    {
        return $this->beGroupRecord();
    }

    public function getBeGroupConfiguration(): ?BeGroupConfiguration
    {
        return $this->beGroupConfiguration();
    }

    public function getInSynch(): bool
    {
        return $this->beGroupConfiguration instanceof BeGroupConfiguration
            && $this->beGroupRecord instanceof BeGroup
            && $this->beGroupConfiguration->beGroupFieldCollection()
                ->isEqual($this->beGroupRecord->beGroupFieldCollection());
    }
}
