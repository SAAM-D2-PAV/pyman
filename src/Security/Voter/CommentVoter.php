<?php

namespace App\Security\Voter;

use Symfony\Component\Security\Core\Authentication\Token\TokenInterface;
use Symfony\Component\Security\Core\Authorization\Voter\Voter;
use Symfony\Component\Security\Core\User\UserInterface;

class CommentVoter extends Voter
{
    protected function supports(string $attribute, $subject): bool
    {
        // replace with your own logic
        // https://symfony.com/doc/current/security/voters.html
        return in_array($attribute, ['COMMENT_EDIT','COMMENT_DELETE'])
            && $subject instanceof \App\Entity\Comment;
    }

    protected function voteOnAttribute(string $attribute, $subject, TokenInterface $token): bool
    {
        $user = $token->getUser();
        // if the user is anonymous, do not grant access
        if (!$user instanceof UserInterface) {
            return false;
        }

        // ... (check conditions and return true to grant permission) ...
        switch ($attribute) {
            case 'COMMENT_EDIT':
                // logic to determine if the user can EDIT
                // return true or false
                $owner = $subject->getCreatedBy();
               
                if ($owner === $user) {
                    return true;
                }
                
                break;
            case 'COMMENT_DELETE':
                // logic to determine if the user can VIEW
                // return true or false
                $owner = $subject->getCreatedBy();
               
                if ($owner === $user) {
                    return true;
                }
                break;
        }

        return false;
    }
}
