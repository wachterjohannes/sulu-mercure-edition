<?php

namespace App\Model\Story\MessageHandler;

use App\Model\Story\Message\RemoveStoryMessage;
use App\Model\Story\StoryRepositoryInterface;
use Symfony\Component\Messenger\Handler\MessageHandlerInterface;

class RemoveStoryMessageHandler implements MessageHandlerInterface
{
    /**
     * @var StoryRepositoryInterface
     */
    private $repository;

    public function __construct(StoryRepositoryInterface $repository)
    {
        $this->repository = $repository;
    }

    public function __invoke(RemoveStoryMessage $message): void
    {
        $story = $this->repository->findById($message->getId());
        $this->repository->remove($story);
    }
}
