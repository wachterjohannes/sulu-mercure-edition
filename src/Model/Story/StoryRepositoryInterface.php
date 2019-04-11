<?php

namespace App\Model\Story;

interface StoryRepositoryInterface
{
    public function create(string $id): Story;

    public function findById(string $id): ?Story;

    public function remove(Story $story): void;
}
