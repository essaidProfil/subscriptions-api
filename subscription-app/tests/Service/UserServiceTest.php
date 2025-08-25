<?php

declare(strict_types=1);

namespace App\Tests\Service;

use App\Entity\Role;
use App\Entity\User;
use App\Repository\RoleRepository;
use App\Service\UserService;
use Doctrine\ORM\EntityManagerInterface;
use PHPUnit\Framework\Attributes\CoversClass;
use PHPUnit\Framework\Attributes\CoversMethod;
use PHPUnit\Framework\Attributes\Group;
use PHPUnit\Framework\MockObject\MockObject;
use PHPUnit\Framework\TestCase;
use Symfony\Component\PasswordHasher\Hasher\UserPasswordHasherInterface;

/**
 * Unit tests for UserService.
 *
 * @internal
 */
#[CoversClass(UserService::class)]
#[Group('unit')]
final class UserServiceTest extends TestCase
{
    /** @var EntityManagerInterface&MockObject */
    private EntityManagerInterface $entityManager;

    /** @var RoleRepository&MockObject */
    private RoleRepository $roleRepository;

    /** @var UserPasswordHasherInterface&MockObject */
    private UserPasswordHasherInterface $passwordHasher;

    private UserService $userService;

    protected function setUp(): void
    {
        /** @var EntityManagerInterface&MockObject $entityManager */
        $this->entityManager  = $this->createMock(EntityManagerInterface::class);
        /** @var RoleRepository&MockObject $roleRepository */
        $this->roleRepository = $this->createMock(RoleRepository::class);
        /** @var UserPasswordHasherInterface&MockObject $passwordHasher */
        $this->passwordHasher = $this->createMock(UserPasswordHasherInterface::class);

        $this->userService = new UserService(
            $this->entityManager,
            $this->roleRepository,
            $this->passwordHasher
        );
    }

    /**
     * Ensure password is hashed and roles are assigned.
     *
     * @return void
     */
    #[Test]
    public function test_create_user_hashes_password_and_assigns_roles(): void
    {
        $this->passwordHasher->method('hashPassword')->willReturn('hashed');

        $roleAdmin = (new Role())->setCode('ROLE_ADMIN')->setName('Administrator');
        $roleUser  = (new Role())->setCode('ROLE_USER')->setName('User');

        $this->roleRepository->method('findOneBy')
            ->willReturnCallback(function (array $criteria) use ($roleAdmin, $roleUser) {
                return match ($criteria['code'] ?? null) {
                    'ROLE_ADMIN' => $roleAdmin,
                    'ROLE_USER'  => $roleUser,
                    default      => null,
                };
            });

        $this->entityManager->expects($this->once())->method('persist')->with($this->isInstanceOf(User::class));
        $this->entityManager->expects($this->once())->method('flush');

        $user = $this->userService->createUser(
            'john.doe@example.com',
            'plain',
            'John',
            'Doe',
            ['ROLE_USER', 'ROLE_ADMIN']
        );

        $this->assertSame('john.doe@example.com', $user->getEmail());
        $this->assertSame('hashed', $user->getPassword());
        $this->assertContains('ROLE_ADMIN', $user->getRoles());
        $this->assertContains('ROLE_USER', $user->getRoles());
    }
}
