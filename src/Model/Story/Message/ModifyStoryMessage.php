<?php

namespace App\Model\Story\Message;

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
}
