<?php

declare(strict_types=1);

namespace BaksDev\Ozon\Security;

use BaksDev\Users\Profile\Group\Security\RoleInterface;
use BaksDev\Users\Profile\Group\Security\VoterInterface;
use Symfony\Component\DependencyInjection\Attribute\AutoconfigureTag;

#[AutoconfigureTag('baks.security.voter')]
final class VoterIndex implements VoterInterface
{
    public const VOTER = 'INDEX';

    /** Метод возвращает правило, конкатенируя ROLE + VOTER */
    public static function getVoter(): string
    {
        return Role::ROLE . '_' . self::VOTER;
    }

    public function equals(RoleInterface $role): bool
    {
        return $role->getRole() === Role::ROLE;
    }
}
