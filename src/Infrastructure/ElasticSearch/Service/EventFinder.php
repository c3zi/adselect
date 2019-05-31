<?php

declare(strict_types = 1);

namespace Adshares\AdSelect\Infrastructure\ElasticSearch\Service;

use Adshares\AdSelect\Application\Dto\FoundEvent;
use Adshares\AdSelect\Application\Exception\EventNotFound;
use Adshares\AdSelect\Application\Service\EventFinder as EventFinderInterface;
use Adshares\AdSelect\Infrastructure\ElasticSearch\Client;
use Adshares\AdSelect\Infrastructure\ElasticSearch\Mapping\EventIndex;
use Psr\Log\LoggerInterface;
use function json_encode;
use function sprintf;

class EventFinder implements EventFinderInterface
{
    /** @var Client */
    private $client;
    /** @var array */
    private $params;
    /** @var LoggerInterface */
    private $logger;

    public function __construct(Client $client, LoggerInterface $logger)
    {
        $this->client = $client;

        $this->params = [
            'index' => EventIndex::INDEX,
            'body' => [
                '_source' => true,
                'size' => 1,
                'query' => [],
                'sort' => [],
            ],
        ];

        $this->logger = $logger;
    }

    public function findLastUnpaidEvent(): FoundEvent
    {
        $query = [
            'bool' => [
                'must_not' => [
                    'exists' => [
                        'field' => 'payment_id',
                    ],
                ],
            ],
        ];

        $sort = [
            [
                'id' => [
                    'order' => 'desc'
                ],
            ],
        ];

        $params = $this->params;
        $params['body']['query'] = $query;
        $params['body']['sort'] = $sort;

        $this->logger->debug(sprintf('[EVENT FINDER] (paid) sending a query: %s', json_encode($params)));

        $response = $this->client->search($params);
        $data = $response['hits']['hits'][0]['_source'] ?? null;

        if (!$data) {
            throw new EventNotFound('No unpaid events.');
        }

        return new FoundEvent(
            $data['id'],
            $data['case_id'],
            $data['publisher_id'],
            $data['paid_amount'],
            $data['date']
        );
    }

    public function findLastPaidEvent(): FoundEvent
    {
        $query = [
            'bool' => [
                'must' => [
                    'exists' => [
                        'field' => 'payment_id',
                    ],
                ],
            ],
        ];

        $sort = [
            [
                'payment_id' => [
                    'order' => 'desc'
                ],
            ],
        ];

        $params = $this->params;
        $params['body']['query'] = $query;
        $params['body']['sort'] = $sort;

        $this->logger->debug(sprintf('[EVENT FINDER] (paid) sending a query: %s', json_encode($params)));

        $response = $this->client->search($params);
        $data = $response['hits']['hits'][0]['_source'] ?? null;

        if (!$data) {
            throw new EventNotFound('No paid events.');
        }

        return new FoundEvent(
            $data['id'],
            $data['case_id'],
            $data['publisher_id'],
            $data['paid_amount'],
            $data['date'],
            $data['payment_id']
        );
    }
}