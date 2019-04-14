<?php

namespace App\Model\Story\Message;

use Ramsey\Uuid\Uuid;

class CreateStoryMessage
{
    /**
     * @var string
     */
    private $id;

    /**
     * @var string
     */
    private $title;

    public function __construct(string $title)
    {
        $this->id = Uuid::uuid4()->toString();
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
