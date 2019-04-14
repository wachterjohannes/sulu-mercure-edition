<?php

namespace App\Model\Story\MessageHandler;

use App\Common\Update\UpdateCollector;
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
     * @var UpdateCollector
     */
    private $updateCollector;

    public function __construct(StoryRepositoryInterface $repository, UpdateCollector $updateCollector)
    {
        $this->repository = $repository;
        $this->updateCollector = $updateCollector;
    }

    public function __invoke(RemoveStoryMessage $message): void
    {
        $story = $this->repository->findById($message->getId());
        $this->repository->remove($story);

        $update = new Update(
            [
                'http://sulu-mercure.localhost/stories/' . $message->getId(),
                'http://sulu-mercure.localhost/',
            ],
            json_encode(null)
        );

        $this->updateCollector->push($update);
    }
}
