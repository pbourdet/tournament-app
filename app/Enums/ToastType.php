<?php

declare(strict_types=1);

namespace App\Enums;

enum ToastType: string
{
    case SUCCESS = 'success';
    case DANGER = 'danger';
    case WARNING = 'warning';
    case INFO = 'info';
}
