<?php
namespace App\veliko;

use App\Entity\User;
use Symfony\Component\Security\Core\Exception\CustomUserMessageAuthenticationException;
use Symfony\Component\Security\Core\User\UserCheckerInterface;
use Symfony\Component\Security\Core\User\UserInterface;

class UserChecker implements UserCheckerInterface
{
    public function checkPreAuth(UserInterface $user): void
    {
        if (!$user instanceof User) {
            return;
        }
        if (!$user->isVerified()) {
            throw new CustomUserMessageAuthenticationException(
                'ton compte n\'est pas encore vérifié, veuillez vérifier votre boîte mail.'
            );
        }
    }
    public function checkPostAuth(UserInterface $user): void
    {
        $this->checkPreAuth($user);
    }
}