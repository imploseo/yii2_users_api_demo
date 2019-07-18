<?php

namespace common\models;


abstract class UserStatus
{
    const CODE = null;

    /**
     * @var array
     */
    public static $allStatusCodes = [
        UserStatusActive::CODE,
        UserStatusInactive::CODE,
        UserStatusDeleted::CODE
    ];

    /**
     * @param User              $user
     * @param String|UserStatus $status
     *
     * @return mixed
     */
    public static function setNewStatus(User $user, $status): bool
    {
        if (!in_array((string) $status, self::$allStatusCodes))
            return false;

        return static::{'setUserStatus' . $status}($user);
    }

    /**
     * @param User $user
     *
     * @return bool
     */
    abstract static function setUserStatusActive(User $user): bool;

    /**
     * @param User $user
     *
     * @return bool
     */
    abstract static function setUserStatusInactive(User $user): bool;

    /**
     * @param User $user
     *
     * @return bool
     */
    abstract static function setUserStatusDeleted(User $user): bool;

    /**
     * @return null
     */
    public function __toString()
    {
        return static::CODE;
    }
}