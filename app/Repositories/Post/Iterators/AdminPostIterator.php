<?php

namespace App\Repositories\Post\Iterators;

use App\Repositories\User\Iterators\UserIterator;
use Carbon\Carbon;

class AdminPostIterator extends PostIterator
{
    protected string|null $deletedAt;

    public function __construct(object $data)
    {
        parent::__construct($data);

        $this->deletedAt    = $data->deleted_at;
    }

    /**
     * @return string|null
     */
    public function getDeletedAt(): string|null
    {
        return $this->deletedAt;
    }
}
