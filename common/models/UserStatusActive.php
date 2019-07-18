<?php

namespace common\models;


class UserStatusActive extends UserStatus
{
    const CODE = 'Active';

    /**
     * @param User $user
     *
     * @return bool
     */
    static function setUserStatusActive(User $user): bool
    {
        return true;
    }

    /**
     * @param User $user
     *
     * @return bool
     */
    static function setUserStatusInactive(User $user): bool
    {
        $user->status = UserStatusInactive::CODE;
        $user->setStatusObject(new UserStatusInactive);

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