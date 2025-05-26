<?php

declare(strict_types=1);

namespace BaksDev\Ozon\Security;

use BaksDev\Menu\Admin\Command\Upgrade\MenuAdminInterface;
use BaksDev\Menu\Admin\Type\SectionGroup\Group\Collection\MenuAdminSectionGroupCollectionInterface;
use BaksDev\Menu\Admin\Type\SectionGroup\Group\MenuGroupSettings;
use Symfony\Component\DependencyInjection\Attribute\AutoconfigureTag;

/**
 * Класс добавляет не кликабельную ссылку модуля (заголовок) в выпадающий список
 */
#[AutoconfigureTag('baks.menu.admin')]
final class Header implements MenuAdminInterface
{
    public function getRole(): string
    {
        return Role::ROLE;
    }

    public function getPath(): false
    {
        return false;
    }

    /**
     * Метод возвращает ключ раздела (для меню телеграм)
     */
    public function getPathKey(): false
    {
        return false;
    }

    /**
     * Метод возвращает секцию, в которую помещается ссылка на раздел
     */
    public function getGroupMenu(): MenuAdminSectionGroupCollectionInterface|bool
    {
        return new MenuGroupSettings();
    }

    /**
     * Метод возвращает позицию, в которую располагается ссылка в секции меню
     */
    public static function getSortMenu(): int
    {
        return 450;
    }

    /**
     * Метод возвращает флаг "Показать в выпадающем меню"
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
