<?php

namespace SocialPiranha\InPlayerSupport\Services\DataObjects;

// use Carbon\Carbon;
use SocialPiranha\InPlayerSupport\Contracts\DataObjectContract;

class Asset implements DataObjectContract
{
    /**
     * @param int $id
     * @param string $title
     * @param Carbon $created
     */
    public function __construct(
        public readonly int $id,
        public readonly string $title,
        // public readonly Carbon $created,
    ) {}

    /**
     * @return array
     */
    public function toArray(): array
    {
        return [
            'id' => $this->id,
            'title' => $this->title,
            // 'created' => $this->created->toDateString(),
        ];
    }
}