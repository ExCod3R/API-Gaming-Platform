<?php

namespace App\Enums;

enum RoleEnum: string
{
    case SUPER_ADMIN = 'super_admin';
    case CHANNEL_ADMIN = 'channel_admin';
    case PLAYER = 'player';
}
