<?php

namespace App\Enums\Shop;

enum ShopStatusEnum: string
{
    case NOT_SYNCED = 'not_synced';

    case QUEUED = 'queued';

    case RUNNING = 'running';

    case FINISHED = 'finished';

    case FAILED = 'failed';
}
