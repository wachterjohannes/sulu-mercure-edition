<?php

namespace App\Model\Story\Query;

use Sulu\Component\Rest\ListBuilder\ListRepresentation;

class ListStoriesQuery
{
    /**
     * @var array
     */
    private $query;

    /**
     * @var ListRepresentation|null
     */
    private $result;

    public function __construct(array $query)
    {
        $this->query = $query;
    }

    public function getQuery(): array
    {
        return $this->query;
    }

    public function getResult(): ListRepresentation
    {
        if (!$this->result) {
            throw new \Exception('Missing result exception');
        }

        return $this->result;
    }

    public function setResult(ListRepresentation $result): self
    {
        $this->result = $result;

        return $this;
    }

}
