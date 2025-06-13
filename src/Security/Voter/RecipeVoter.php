<?php

namespace App\Security\Voter;

use App\Entity\Recipe;
use App\Entity\User;
use Symfony\Component\Security\Core\Authentication\Token\TokenInterface;
use Symfony\Component\Security\Core\Authorization\Voter\Voter;

class RecipeVoter extends Voter
{
    public const EDIT = 'RECIPE_EDIT';

    public const VIEW = 'RECIPE_VIEW';

    public const CREATE = 'RECIPE_CREATE';

    public const LIST = 'RECIPE_LIST';

    public const LIST_ALL = 'RECIPE_ALL';

    protected function supports(string $attribute, mixed $subject): bool
    {
        return in_array($attribute, [self::CREATE, self::LIST, self::LIST_ALL]) ||
            (
                in_array($attribute, [self::EDIT, self::VIEW])
                && $subject instanceof \App\Entity\Recipe
            );
    }

    /**
     * @param Recipe|null $subject
     */
    protected function voteOnAttribute(string $attribute, mixed $subject, TokenInterface $token): bool
    {
        $user = $token->getUser();
        // if the user is anonymous, do not grant access
        if (! $user instanceof User) {
            return false;
        }
        // ... (check conditions and return true to grant permission) ...
        return match ($attribute) {
            self::EDIT => $subject->getUser()->getId() === $user->getId(),
            self::LIST, self::CREATE, self::VIEW => true,
            default => false,
        };
    }
}
