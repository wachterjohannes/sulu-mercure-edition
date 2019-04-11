<?php

namespace App\Model\Story\Query;

use App\Model\Story\Story;

class FindStoryQuery
{
    /**
     * @var string
     */
    private $id;

    /**
     * @var Story|null
     */
    private $result;

    public function __construct(string $id)
    {
        $this->id = $id;
    }

    public function getId(): string
    {
        return $this->id;
    }

    public function getResult(): Story
    {
        if (!$this->result) {
            throw new \Exception('Missing result exception');
        }

        return $this->result;
    }

    public function setResult(Story $result): self
    {
        $this->result = $result;

        return $this;
    }
}
