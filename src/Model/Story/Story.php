<?php

namespace App\Model\Story;

class Story
{
    const RESOURCE_KEY = 'stories';
    const LIST_KEY = 'stories';
    const FORM_KEY = 'story_details';

    /**
     * @var int
     */
    private $no;

    /**
     * @var string
     */
    private $id;

    /**
     * @var string
     */
    private $title;

    public function __construct(string $id, string $title = '')
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

    public function setTitle(string $title): self
    {
        $this->title = $title;

        return $this;
    }
}
