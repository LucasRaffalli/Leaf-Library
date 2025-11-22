<?php

namespace App\Tests\Service;

use App\Entity\Role;
use App\Entity\User;
use App\Service\RegistrationService;
use App\Service\RoleInitializerService;
use Doctrine\ORM\EntityManagerInterface;
use PHPUnit\Framework\TestCase;
use Symfony\Component\PasswordHasher\Hasher\UserPasswordHasherInterface;

class RegistrationServiceTest extends TestCase
{
    private UserPasswordHasherInterface $passwordHasher;
    private EntityManagerInterface $entityManager;
    private RoleInitializerService $roleInitializer;
    private RegistrationService $registrationService;

    protected function setUp(): void
    {
        $this->passwordHasher = $this->createMock(UserPasswordHasherInterface::class);
        $this->entityManager = $this->createMock(EntityManagerInterface::class);
        $this->roleInitializer = $this->createMock(RoleInitializerService::class);

        $this->registrationService = new RegistrationService(
            $this->passwordHasher,
            $this->entityManager,
            $this->roleInitializer
        );
    }

    public function testRegisterUserHashesPassword(): void
    {
        $user = new User();
        $plainPassword = 'TestPassword123';
        $hashedPassword = 'hashed_password_xyz';

        $this->passwordHasher
            ->expects($this->once())
            ->method('hashPassword')
            ->with($user, $plainPassword)
            ->willReturn($hashedPassword);

        $userRole = $this->createMock(Role::class);
        $this->roleInitializer
            ->expects($this->once())
            ->method('getRoleByName')
            ->with('USER')
            ->willReturn($userRole);

        $this->entityManager
            ->expects($this->once())
            ->method('persist')
            ->with($user);

        $this->entityManager
            ->expects($this->once())
            ->method('flush');

        $this->registrationService->registerUser($user, $plainPassword);

        $this->assertSame($hashedPassword, $user->getPassword());
    }

    public function testRegisterUserAssignsUserRole(): void
    {
        $user = new User();
        $plainPassword = 'TestPassword123';

        $this->passwordHasher
            ->method('hashPassword')
            ->willReturn('hashed_password');

        $userRole = $this->createMock(Role::class);
        $this->roleInitializer
            ->expects($this->once())
            ->method('getRoleByName')
            ->with('USER')
            ->willReturn($userRole);

        $this->entityManager
            ->expects($this->once())
            ->method('persist')
            ->with($user);

        $this->entityManager
            ->expects($this->once())
            ->method('flush');

        $this->registrationService->registerUser($user, $plainPassword);

        $this->assertTrue($user->getUserRoles()->contains($userRole));
    }

    public function testRegisterUserPersistsAndFlushes(): void
    {
        $user = new User();
        $plainPassword = 'TestPassword123';

        $this->passwordHasher
            ->method('hashPassword')
            ->willReturn('hashed_password');

        $userRole = $this->createMock(Role::class);
        $this->roleInitializer
            ->method('getRoleByName')
            ->willReturn($userRole);

        $this->entityManager
            ->expects($this->once())
            ->method('persist')
            ->with($this->identicalTo($user));

        $this->entityManager
            ->expects($this->once())
            ->method('flush');

        $this->registrationService->registerUser($user, $plainPassword);
    }
}
