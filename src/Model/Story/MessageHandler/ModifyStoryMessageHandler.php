<?php

namespace App\Model\Story\MessageHandler;

use App\Model\Story\Message\ModifyStoryMessage;
use App\Model\Story\StoryRepositoryInterface;
use Symfony\Component\Messenger\Handler\MessageHandlerInterface;

class ModifyStoryMessageHandler implements MessageHandlerInterface
{
    /**
     * @var StoryRepositoryInterface
     */
    private $repository;

    public function __construct(StoryRepositoryInterface $repository)
    {
        $this->repository = $repository;
    }

    public function __invoke(ModifyStoryMessage $message): void
    {
        $story = $this->repository->findById($message->getId());
        $story->setTitle($message->getTitle());
    }
}
