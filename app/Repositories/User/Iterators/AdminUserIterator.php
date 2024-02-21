<?php

namespace App\Repositories\User\Iterators;

class AdminUserIterator extends UserIterator
{
    protected string|null $updatedAt;
    protected bool $isBanned;

    public function __construct(object $data)
    {
        parent::__construct($data);

        $this->updatedAt    = $data->updated_at;
        $this->isBanned     = $data->is_banned;
    }

    /**
     * @return string|null
     */
    public function getUpdatedAt(): ?string
    {
        return $this->updatedAt;
    }

    /**
     * @return bool
     */
    public function getIsBanned(): bool
    {
        return $this->isBanned;
    }
}
