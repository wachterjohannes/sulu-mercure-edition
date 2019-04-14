<?php

namespace App\Model\Story\MessageHandler;

use App\Common\Update\UpdateCollector;
use App\Model\Story\Message\CreateStoryMessage;
use App\Model\Story\Story;
use App\Model\Story\StoryRepositoryInterface;
use Symfony\Component\Mercure\Update;
use Symfony\Component\Messenger\Handler\MessageHandlerInterface;

class CreateStoryMessageHandler implements MessageHandlerInterface
{
    /**
     * @var StoryRepositoryInterface
     */
    private $repository;

    /**
     * @var UpdateCollector
     */
    private $updateCollector;

    public function __construct(StoryRepositoryInterface $repository, UpdateCollector $updateCollector)
    {
        $this->repository = $repository;
        $this->updateCollector = $updateCollector;
    }

    public function __invoke(CreateStoryMessage $message): Story
    {
        $story = $this->repository->create($message->getId());
        $story->setTitle($message->getTitle());

        $update = new Update(
            [
                'http://sulu-mercure.localhost/stories/' . $message->getId(),
                'http://sulu-mercure.localhost/',
            ],
            json_encode(['id' => $story->getId(), 'title' => $story->getTitle()])
        );

        $this->updateCollector->push($update);

        return $story;
    }
}
