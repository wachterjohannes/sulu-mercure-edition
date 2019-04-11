<?php

namespace App\Model\Story\Message;

use App\Model\Story\Story;

class ModifyStoryMessage
{
    /**
     * @var string
     */
    private $id;

    /**
     * @var string
     */
    private $title;

    /**
     * @var Story|null
     */
    private $result;

    public function __construct(string $id, string $title)
    {
        $this->id = $id;
        $this->title = $title;
    }

    public function getId(): string
    {
        return $this->id;
    }

    public function getTitle(): string
    {
        return $this->title;
    }

    public function getResult(): ?Story
    {
        return $this->result;
    }

    public function setResult(Story $result): self
    {
        $this->result = $result;

        return $this;
    }
}
