<?php

declare(strict_types = 1);

namespace Adshares\AdSelect\Infrastructure\ElasticSearch\Mapper;

use Adshares\AdSelect\Domain\Model\Event;

class EventMapper
{
    public static function map(Event $event, string $index): array
    {
        $mapped['index'] = [
            'index' => [
                '_index' => $index,
                '_type' => '_doc',
                '_id' => $event->getCaseId(),
            ],
        ];

        $data = $event->toArray();
        $data['keywords_flat'] = $event->flatKeywords();

        if ($data['payment_id'] === null) {
            unset($data['payment_id']);
        }

        $mapped['data'] = $data;

        return $mapped;
    }
}
