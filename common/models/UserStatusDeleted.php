<?php

namespace common\models;


class UserStatusDeleted extends UserStatus
{
    const CODE = 'Deleted';

    /**
     * @param User $user
     *
     * @return bool
     */
    static function setUserStatusActive(User $user): bool
    {
        $user->addError('status', 'Перевод из удалённого состояния в активное запрещён!');
        // $user->status = UserStatusActive::CODE;
        // $user->setStatusObject(new UserStatusActive);

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
        return true;
    }
}