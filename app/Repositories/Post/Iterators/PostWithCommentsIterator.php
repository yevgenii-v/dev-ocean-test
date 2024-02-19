<?php

namespace App\Repositories\Post\Iterators;

use Illuminate\Support\Collection;

class PostWithCommentsIterator extends PostIterator
{
    protected Collection $comments;

    public function __construct(object $data)
    {
        parent::__construct($data);

        $this->comments     = $data->comments;
    }

    /**
     * @return Collection
     */
    public function getComments(): Collection
    {
        return $this->comments;
    }
}
