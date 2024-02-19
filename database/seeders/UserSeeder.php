<?php

namespace Database\Seeders;

use App\Enums\RoleEnum;
use App\Repositories\User\UserCreateDTO;
use App\Repositories\User\UserRepository;
use App\Services\Role\RoleService;
use Illuminate\Database\Seeder;

class UserSeeder extends Seeder
{
    public function __construct(
        protected UserRepository $userRepository,
        protected RoleService $roleService,
    ) {
    }

    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $adminDTO = new UserCreateDTO(
            env('ADMIN_LOGIN'),
            env('ADMIN_EMAIL'),
            env('ADMIN_PASSWORD')
        );

        $userDTO = new UserCreateDTO(
            env('USER_LOGIN'),
            env('USER_EMAIL'),
            env('USER_PASSWORD')
        );

        $admin = $this->userRepository->create($adminDTO);
        $user = $this->userRepository->create($userDTO);

        $this->roleService->submitRoles($admin->getId(), RoleEnum::Administrator);
        $this->roleService->submitRoles($user->getId(), RoleEnum::User);
    }
}
