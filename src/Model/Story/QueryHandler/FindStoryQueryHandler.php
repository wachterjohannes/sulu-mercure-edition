<?php

namespace App\Model\Story\QueryHandler;

use App\Model\Story\Query\FindStoryQuery;
use App\Model\Story\StoryRepositoryInterface;
use Symfony\Component\Messenger\Handler\MessageHandlerInterface;

class FindStoryQueryHandler implements MessageHandlerInterface
{
    /**
     * @var StoryRepositoryInterface
     */
    private $repository;

    public function __construct(StoryRepositoryInterface $repository)
    {
        $this->repository = $repository;
    }

    public function __invoke(FindStoryQuery $query): void
    {
        $story = $this->repository->findById($query->getId());

        $query->setResult($story);
    }
}
