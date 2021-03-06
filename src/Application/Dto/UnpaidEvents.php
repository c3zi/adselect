<?php

declare(strict_types = 1);

namespace Adshares\AdSelect\Application\Dto;

final class UnpaidEvents extends Events
{
    protected const REQUIRED_FIELDS = [
        'id',
        'case_id',
        'publisher_id',
        'user_id',
        'tracking_id',
        'zone_id',
        'campaign_id',
        'banner_id',
        'keywords',
        'time',
        'type',
    ];
}
