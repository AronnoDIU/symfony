<?php

// src/Security/Voter/AccessVoter.php

namespace App\Security\Voter;

use App\Entity\User;
use Symfony\Component\Security\Core\Authentication\Token\TokenInterface;
use Symfony\Component\Security\Core\Authorization\Voter\Voter;
use Symfony\Component\Security\Core\Security;

class AccessVoter extends Voter
{
    const READ = 'READ';
    const WRITE = 'WRITE';
    const APPROVE = 'APPROVE';
    private Security $security;

    public function __construct(Security $security)
    {
        $this->security = $security;
    }

    protected function supports(string $attribute, $subject): bool
    {
        return in_array($attribute, [self::READ, self::WRITE, self::APPROVE]);
    }

    protected function voteOnAttribute(string $attribute, $subject, TokenInterface $token): bool
    {
        $user = $token->getUser();

        if (!$user instanceof User) {
            return false; // Access denied if user is not logged in
        }

        switch ($attribute) {
            case self::READ:
                return $this->canRead($user);
            case self::WRITE:
                return $this->canWrite($user);
            case self::APPROVE:
                return $this->canApprove($user);
        }

        return false; // Default to deny access
    }

    private function canRead(User $user): bool
    {
        // Check if the user has the ROLE_ADMIN role
        if ($this->security->isGranted('ROLE_ADMIN')) {
            return true;
        }
        // Any authenticated user can read
        return in_array('SALE_READ', $user->getRoles());
    }

    private function canWrite(User $user): bool
    {
        // Check if the user has the ROLE_ADMIN role
        if ($this->security->isGranted('ROLE_ADMIN')) {
            return true;
        }
        // any authenticated user can write
        return in_array('SALE_WRITE', $user->getRoles());
    }

    private function canApprove(User $user): bool
    {
        // Check if the user has the ROLE_ADMIN role
        if ($this->security->isGranted('ROLE_ADMIN')) {
            return true;
        }
        // Any authenticated user can approve
        return in_array('SALE_APPROVE', $user->getRoles());
    }
}
