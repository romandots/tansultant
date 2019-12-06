<?php
/**
 * File: UserService.php
 * Author: Roman Dots <ram.d.kreiz@gmail.com>
 * Date: 2019-07-21
 * Copyright (c) 2019
 */

declare(strict_types=1);

namespace App\Services\User;

use App\Models\User;
use App\Repository\UserRepository;
use App\Services\User\Exceptions\OldPasswordInvalidException;

/**
 * Class UserService
 * @package App\Services\User
 */
class UserService
{
    /**
     * @var UserRepository
     */
    private $userRepository;

    /**
     * UserService constructor.
     * @param UserRepository $userRepository
     */
    public function __construct(UserRepository $userRepository)
    {
        $this->userRepository = $userRepository;
    }

    /**
     * @param User $user
     * @param \App\Http\Requests\ManagerApi\DTO\UpdateUserPassword $dto
     */
    public function updatePassword(User $user, \App\Http\Requests\ManagerApi\DTO\UpdateUserPassword $dto): void
    {
        if (!\Hash::check($dto->old_password, $user->password)) {
            throw new OldPasswordInvalidException();
        }

        $this->userRepository->updatePassword($user, $dto->new_password);
    }
}
