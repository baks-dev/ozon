<?php

declare(strict_types=1);

namespace BaksDev\Ozon\Security;

use BaksDev\Menu\Admin\Command\Upgrade\MenuAdminInterface;
use BaksDev\Menu\Admin\Type\SectionGroup\Group\Collection\MenuAdminSectionGroupCollectionInterface;
use BaksDev\Menu\Admin\Type\SectionGroup\Group\MenuGroupSettings;
use BaksDev\Users\Profile\Group\Security\RoleInterface;
use Symfony\Component\DependencyInjection\Attribute\AutoconfigureTag;

/**
 * Класс добавляет ссылку модуля в выпадающее меню для
 */
#[AutoconfigureTag('baks.security.role')]
#[AutoconfigureTag('baks.menu.admin')]
final class Role implements RoleInterface, MenuAdminInterface
{
    public const string ROLE = 'ROLE_OZON';

    public function getRole(): string
    {
        return self::ROLE;
    }

    /* * Добавляем раздел в меню администрирования. */

    /** Метод возвращает PATH раздела */
    public function getPath(): string
    {
        return 'ozon:admin.index';
    }

    /**
     * Метод возвращает секцию, в которую помещается ссылка на раздел.
     */
    public function getGroupMenu(): MenuAdminSectionGroupCollectionInterface|bool
    {
        return new MenuGroupSettings();
    }

    /**
     * Метод возвращает позицию, в которую располагается ссылка в секции меню.
     */
    public function getSortMenu(): int
    {
        return 451;
    }

    /**
     * Метод возвращает флаг "Показать в выпадающем меню".
     */
    public function getDropdownMenu(): bool
    {
        return true;
    }

    /**
     * Метод возвращает флаг "Модальное окно" (клик по ссылке вызывает модальное окно, вместо редиректа).
     */
    public function getModal(): bool
    {
        return false;
    }

}
