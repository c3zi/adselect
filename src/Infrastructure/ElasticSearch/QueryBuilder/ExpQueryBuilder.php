<?php
/**
 * @phpcs:disable Generic.Files.LineLength.TooLong
 */
declare(strict_types = 1);

namespace Adshares\AdSelect\Infrastructure\ElasticSearch\QueryBuilder;

class ExpQueryBuilder
{
    /** @var int */
    private $threshold;

    /** @var QueryInterface */
    private $query;

    public function __construct(QueryInterface $query, int $threshold = 3)
    {
        $this->threshold = $threshold;
        $this->query = $query;
    }

    public function build(): array
    {
        return [
            'function_score' => [
                'query' => $this->query->build(),
                'boost_mode' => 'replace',
                'script_score' => [
                    'script' => [
                        'lang' => 'painless',
                        'source' => <<<PAINLESS
                            int paidAmount = (int) doc['stats_paid_amount'].value;
                            
                            if (doc['stats_views'].value < params.threshold && paidAmount === 0) {
                                return (Math.random() + 0.5) / (doc['stats_views'].value + doc['stats_clicks'].value + 1.0)
                            }
                        
                            if (paidAmount > 0) {
                                return ((Math.random() + 0.5) + (0.01 * paidAmount)) / ((doc['stats_clicks'].value + doc['stats_views'].value + 1.0));

                            }
                            
                            return (Math.random() + 0.5) / ((doc['stats_clicks'].value / (doc['stats_views'].value + 0.1)) + 1.0);
PAINLESS
                        ,
                        'params' => [
                            'threshold' => $this->threshold,
                        ],
                    ],
                ],
            ],
        ];
    }
}
