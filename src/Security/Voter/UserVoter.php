<?php

namespace App\Security\Voter;

use Symfony\Component\Security\Core\Authentication\Token\TokenInterface;
use Symfony\Component\Security\Core\Authorization\Voter\Voter;
use Symfony\Component\Security\Core\User\UserInterface;

class UserVoter extends Voter
{
    protected function supports($attribute, $subject)
    {
        // replace with your own logic
        // https://symfony.com/doc/current/security/voters.html
        return in_array($attribute, ['USER_DELETE', 'USER_VIEW'])
            && $subject instanceof \App\Entity\User;
    }

    protected function voteOnAttribute($attribute, $subject, TokenInterface $token)
    {
        $user = $token->getUser();
        // if the user is anonymous, do not grant access
        if (!$user instanceof UserInterface) {
            return false;
        }

        // ... (check conditions and return true to grant permission) ...
        switch ($attribute) {
            case 'USER_DELETE':
                // logic to determine if the user can delete is own account
                // return true or false
                $roles = $user->getRoles();
            
                foreach ($roles as $role ) {

                    if ($role <= 'ROLE_ADMIN') {

                        return true;
                    }
                
                }
                if ($user == $subject) {
                    return true;
                }
                break;
            
        }

        return false;
    }
}
