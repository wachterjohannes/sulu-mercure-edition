<?php

namespace App\Model\Story\MessageHandler;

use App\Model\Story\Message\CreateStoryMessage;
use App\Model\Story\StoryRepositoryInterface;
use Symfony\Component\Messenger\Handler\MessageHandlerInterface;

class CreateStoryMessageHandler implements MessageHandlerInterface
{
    /**
     * @var StoryRepositoryInterface
     */
    private $repository;

    public function __construct(StoryRepositoryInterface $repository)
    {
        $this->repository = $repository;
    }

    public function __invoke(CreateStoryMessage $message): void
    {
        $story = $this->repository->create($message->getId());
        $story->setTitle($message->getTitle());
    }
}
