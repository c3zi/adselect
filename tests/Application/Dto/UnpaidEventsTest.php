<?php

declare(strict_types = 1);

namespace Adshares\AdSelect\Tests\Application\Dto;

use Adshares\AdSelect\Application\Dto\UnpaidEvents;
use Adshares\AdSelect\Lib\ExtendedDateTime;
use PHPUnit\Framework\TestCase;

final class UnpaidEventsTest extends TestCase
{
    /**
     * @dataProvider providerDataForDTO
     */
    public function testIfDtoIsValid(array $data, int $successEvents, int $failuredEvents): void
    {
        $events = new UnpaidEvents([$data]);

        $this->assertCount($successEvents, $events->events());
        $this->assertCount($failuredEvents, $events->failedEvents());
    }

    public function providerDataForDTO(): array
    {
        return [
            [
                [
                    'id' => 1,
                    'case_id' => '43c567e1396b4cadb52223a51796fd00',
                    'publisher_id' => '43c567e1396b4cadb52223a51796fd01',
                    'user_id' => '43c567e1396b4cadb52223a51796fd01',
                    'tracking_id' => '00000000000000000000000000000002',
                    'zone_id' => '43c567e1396b4cadb52223a51796fd01',
                    'campaign_id' => '43c567e1396b4cadb52223a51796fd01',
                    'banner_id' => '43c567e1396b4cadb52223a51796fd01',
                    'keywords' => [],
                    'time' => (new ExtendedDateTime())->toString(),
                    'type' => 'view',
                ],
                1,
                0,
            ],
            [
                [
                    'id' => 1,
                    'case_id' => '43c567e1396b4cadb52223a51796fd00',
                    'publisher_id' => '43c567e1396b4cadb52223a51796fd01',
                    'user_id' => '43c567e1396b4cadb52223a51796fd01',
                    'tracking_id' => '00000000000000000000000000000002',
                    'zone_id' => '43c567e1396b4cadb52223a51796fd01',
                    'campaign_id' => '43c567e1396b4cadb52223a51796fd01',
                    'banner_id' => '43c567e1396b4cadb52223a51796fd01',
                    'keywords' => [
                        'os:device' => ['firefox'],
                    ],
                    'time' => (new ExtendedDateTime())->toString(),
                    'type' => 'view',
                ],
                1,
                0,
            ],
            [
                [
                    'case_id' => '43c567e1396b4cadb52223a51796fd00',
                    'publisher_id' => '43c567e1396b4cadb52223a51796fd01',
                    'user_id' => '43c567e1396b4cadb52223a51796fd01',
                    'tracking_id' => '00000000000000000000000000000002',
                    'zone_id' => '43c567e1396b4cadb52223a51796fd01',
                    'campaign_id' => '43c567e1396b4cadb52223a51796fd01',
                    'banner_id' => '43c567e1396b4cadb52223a51796fd01',
                    'keywords' => [
                        'os:device' => ['firefox'],
                    ],
                    'time' => (new ExtendedDateTime())->toString(),
                    'type' => 'view',
                ],
                0,
                1,
            ],
            [
                [
                    'id' => 1,
                    'case_id' => '43c567e1396b4cadb52223a51796fd00',
                    'publisher_id' => '43c567e1396b4cadb52223a51796fd01',
                    'user_id' => '43c567e1396b4cadb52223a51796fd01',
                    'tracking_id' => '00000000000000000000000000000002',
                    'zone_id' => '43c567e1396b4cadb52223a51796fd01',
                    'campaign_id' => '43c567e1396b4cadb52223a51796fd01',
                    'banner_id' => '43c567e1396b4cadb52223a51796fd01',
                    'keywords' => [
                        'os:device' => ['firefox'],
                    ],
                    'time' => (new ExtendedDateTime())->toString(),
                    'type' => 'click',
                ],
                1,
                0,
            ],
            [
                [
                    'id' => 1,
                    'case_id' => '43c567e1396b4cadb52223a51796fd01',
                    'publisher_id' => '43c567e1396b4cadb52223a51796fd01',
                    'user_id' => '43c567e1396b4cadb52223a51796fd01',
                    'tracking_id' => '00000000000000000000000000000002',
                    'zone_id' => '43c567e1396b4cadb52223a51796fd01',
                    'campaign_id' => '43c567e1396b4cadb52223a51796fd01',
                    'banner_id' => '43c567e1396b4cadb52223a51796fd01',
                    'keywords' => [
                        'os:device' => ['firefox'],
                    ],
                    'time' => (new ExtendedDateTime())->toString(),
                ],
                0,
                1,
            ],
        ];
    }
}
