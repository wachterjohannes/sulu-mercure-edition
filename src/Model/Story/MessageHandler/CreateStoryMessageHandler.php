<?php

namespace App\Model\Story\MessageHandler;

use App\Model\Story\Message\CreateStoryMessage;
use App\Model\Story\StoryRepositoryInterface;
use Symfony\Component\Mercure\Publisher;
use Symfony\Component\Mercure\Update;
use Symfony\Component\Messenger\Handler\MessageHandlerInterface;

class CreateStoryMessageHandler implements MessageHandlerInterface
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

    public function __invoke(CreateStoryMessage $message): void
    {
        $story = $this->repository->create($message->getId());
        $story->setTitle($message->getTitle());

        $message->setResult($story);

        $update = new Update(
            'http://sulu-mercure.localhost/stories/' . $message->getId(),
            json_encode(['id' => $story->getId(), 'title' => $story->getTitle()])
        );

        // The Publisher service is an invokable object
        $this->publisher->__invoke($update);
    }
}
