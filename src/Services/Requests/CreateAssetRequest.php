<?php

namespace SocialPiranha\InPlayerSupport\Services\Requests;

final class CreateAssetRequest
{
    public function __construct(
        public readonly string $item_type,
        public readonly int $access_control_type_id,
        public readonly string|null $title = null,
        public readonly array|null $metadata = null,
        public readonly string|null $external_asset_id = null,
        public readonly int|null $template_id = null,
        public readonly string|null $event_type = null,
    ) {}

    public function toData(): array
    {
        return array_merge(
            [
                'item_type' => $this->item_type,
                'access_control_type_id' => $this->access_control_type_id,
            ],
            ($this->title ? ['title' => $this->title] : []),
            ($this->metadata ? ['metadata' => $this->metadata] : []),
            ($this->external_asset_id ? ['external_asset_id' => $this->external_asset_id] : []),
            ($this->template_id ? ['template_id' => $this->template_id] : []),
            ($this->event_type ? ['event_type' => $this->event_type] : []),
        );
    }
}