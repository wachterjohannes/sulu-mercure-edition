<?php

namespace App\Repository;

use App\Model\Story\Story;
use App\Model\Story\StoryRepositoryInterface;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\Common\Persistence\ManagerRegistry;

class StoryRepository extends ServiceEntityRepository implements StoryRepositoryInterface
{
    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, Story::class);
    }

    public function create(string $id): Story
    {
        $story = new Story($id);
        $this->getEntityManager()->persist($story);

        return $story;
    }

    public function findById(string $id): ?Story
    {
        return $this->findOneBy(['id' => $id]);
    }

    public function remove(Story $story): void
    {
        $this->getEntityManager()->remove($story);
    }
}
