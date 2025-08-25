<?php
namespace App\Service;

use App\Entity\User;
use App\Entity\Role;
use App\Repository\RoleRepository;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Component\PasswordHasher\Hasher\UserPasswordHasherInterface;

/**
 * User related operations (create user, assign roles).
 */
class UserService
{
    public function __construct(
        private EntityManagerInterface $entityManager,
        private RoleRepository $roleRepository,
        private UserPasswordHasherInterface $passwordHasher
    ) {}

    /**
     * Create a new user with optional role assignment.
     *
     * @param string $email User email (unique).
     * @param string $plainPassword Plain password to hash.
     * @param string|null $firstName Optional first name.
     * @param string|null $lastName Optional last name.
     * @param string[] $roleCodes Role codes to assign (e.g. ["ROLE_USER","ROLE_ADMIN"]).
     * @return User The persisted user.
     */
    public function createUser(
        string $email,
        string $plainPassword,
        ?string $firstName = null,
        ?string $lastName = null,
        array $roleCodes = ['ROLE_USER']
    ): User {
        $user = new User();
        $user->setEmail($email);
        $user->setPassword($this->passwordHasher->hashPassword($user, $plainPassword));
        $user->setFirstName($firstName);
        $user->setLastName($lastName);

        foreach ($roleCodes as $roleCode) {
            $role = $this->roleRepository->findOneBy(['code' => strtoupper($roleCode)]);
            if ($role instanceof Role) {
                $user->addRole($role);
            }
        }

        $this->entityManager->persist($user);
        $this->entityManager->flush();

        return $user;
    }
}
