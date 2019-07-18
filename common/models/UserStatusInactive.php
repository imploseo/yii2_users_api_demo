<?php

namespace common\models;


class UserStatusInactive extends UserStatus
{
    const CODE = 'Inactive';

    /**
     * @param User $user
     *
     * @return bool
     */
    static function setUserStatusActive(User $user): bool
    {
        $user->status = UserStatusActive::CODE;
        $user->setStatusObject(new UserStatusActive);

        return true;
    }

    /**
     * @param User $user
     *
     * @return bool
     */
    static function setUserStatusInactive(User $user): bool
    {
        return true;
    }

    /**
     * @param User $user
     *
     * @return bool
     */
    static function setUserStatusDeleted(User $user): bool
    {
        $user->status = UserStatusDeleted::CODE;
        $user->setStatusObject(new UserStatusDeleted);

        return true;
    }
}