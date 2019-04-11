<?php

namespace App\Model\Story\MessageHandler;

use App\Model\Story\Message\RemoveStoryMessage;
use App\Model\Story\StoryRepositoryInterface;
use Symfony\Component\Mercure\Publisher;
use Symfony\Component\Mercure\Update;
use Symfony\Component\Messenger\Handler\MessageHandlerInterface;

class RemoveStoryMessageHandler implements MessageHandlerInterface
{
    /**
     * @var StoryRepositoryInterface
     */
    private $repository;

    /**
     * @var Publisher
     */
    private $publisher;

    public function __construct(StoryRepositoryInterface $repository, Publisher $publisher)
    {
        $this->repository = $repository;
        $this->publisher = $publisher;
    }

    public function __invoke(RemoveStoryMessage $message): void
    {
        $story = $this->repository->findById($message->getId());
        $this->repository->remove($story);

        $update = new Update(
            'http://sulu-mercure.localhost/stories/' . $message->getId(),
            json_encode(null)
        );

        // The Publisher service is an invokable object
        $this->publisher->__invoke($update);
    }
}
