<?php

namespace App\Enums;

enum BatchFileStatusEnum: string
{
    case PROGRESS = 'progress';

    case DONE = 'done';

    case PARTIAL = 'partial';

    case ERROR = 'error';
}
