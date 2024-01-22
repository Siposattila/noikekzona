<?php

namespace App\Constant;

class UserConstant
{
    public const ROLE_USER = 'ROLE_USER';
    public const ROLE_ADMIN = 'ROLE_ADMIN';
    public const ROLE_SUPERADMIN = 'ROLE_SUPERADMIN';

    public const USER_ROLES = [
        self::ROLE_USER,
        self::ROLE_ADMIN,
        self::ROLE_SUPERADMIN,
    ];
}
