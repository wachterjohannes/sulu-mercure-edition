<?php

namespace App\Common\Update;

use Symfony\Component\Messenger\Stamp\StampInterface;

class UpdateStamp implements StampInterface
{
    /**
     * @var string
     */
    private $updateId;

    /**
     * @var \string[]
     */
    private $targets = [];

    public function __construct(string $updateId, array $targets)
    {
        $this->updateId = $updateId;
        $this->targets = $targets;
    }

    public function getUpdateId(): string
    {
        return $this->updateId;
    }

    public function getTargets(): array
    {
        return $this->targets;
    }
}
