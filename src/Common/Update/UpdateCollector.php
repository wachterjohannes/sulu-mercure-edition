<?php

namespace App\Common\Update;

use Symfony\Component\Mercure\Update;

class UpdateCollector
{
    /**
     * @var Update[]
     */
    private $updates = [];

    public function push(Update $update): void
    {
        $this->updates[] = $update;
    }

    /**
     * @return Update[]
     */
    public function release(): \Generator
    {
        try {
            foreach ($this->updates as $update) {
                yield $update;
            }
        } finally {
            $this->updates = [];
        }
    }
}
