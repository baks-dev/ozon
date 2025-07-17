<?php

declare(strict_types=1);

namespace BaksDev\Ozon\Repository\AllOzonToken;

use BaksDev\Auth\Email\Entity\Account;
use BaksDev\Auth\Email\Entity\Event\AccountEvent;
use BaksDev\Auth\Email\Entity\Status\AccountStatus;
use BaksDev\Core\Doctrine\DBALQueryBuilder;
use BaksDev\Core\Form\Search\SearchDTO;
use BaksDev\Core\Services\Paginator\PaginatorInterface;
use BaksDev\Ozon\Entity\Event\Active\OzonTokenActive;
use BaksDev\Ozon\Entity\Event\Card\OzonTokenCard;
use BaksDev\Ozon\Entity\Event\Modify\DateTime\OzonTokenModifyDateTime;
use BaksDev\Ozon\Entity\Event\Name\OzonTokenName;
use BaksDev\Ozon\Entity\Event\Profile\OzonTokenProfile;
use BaksDev\Ozon\Entity\Event\Stocks\OzonTokenStocks;
use BaksDev\Ozon\Entity\Event\Type\OzonTokenType;
use BaksDev\Ozon\Entity\OzonToken;
use BaksDev\Users\Profile\UserProfile\Entity\Event\Avatar\UserProfileAvatar;
use BaksDev\Users\Profile\UserProfile\Entity\Event\Info\UserProfileInfo;
use BaksDev\Users\Profile\UserProfile\Entity\Event\Personal\UserProfilePersonal;
use BaksDev\Users\Profile\UserProfile\Entity\Event\UserProfileEvent;
use BaksDev\Users\Profile\UserProfile\Entity\UserProfile;
use BaksDev\Users\Profile\UserProfile\Type\Id\UserProfileUid;
use BaksDev\Yandex\Market\Entity\Event\Card\YaMarketTokenCard;
use BaksDev\Yandex\Market\Entity\Event\Stocks\YaMarketTokenStocks;
use BaksDev\Yandex\Market\Entity\Event\Type\YaMarketTokenType;

final class AllOzonTokenRepository implements AllOzonTokenInterface
{
    private ?SearchDTO $search = null;

    private ?UserProfileUid $profile = null;

    public function __construct(
        private readonly DBALQueryBuilder $DBALQueryBuilder,
        private readonly PaginatorInterface $paginator,
    ) {}

    public function search(SearchDTO $search): self
    {
        $this->search = $search;
        return $this;
    }

    public function profile(UserProfileUid|string $profile): self
    {
        if(is_string($profile))
        {
            $profile = new UserProfileUid($profile);
        }

        $this->profile = $profile;

        return $this;
    }

    public function findAllPaginator(): PaginatorInterface
    {
        $dbal = $this->DBALQueryBuilder
            ->createQueryBuilder(self::class)
            ->bindLocal();

        $dbal
            ->select('token.id')
            ->addSelect('token.event')
            ->from(OzonToken::class, 'token');

        $dbal->join(
            'token',
            OzonTokenProfile::class,
            'profile',
            'profile.event = token.event'.($this->profile ? ' AND profile.value = :profile ' : ''),
        );

        $dbal
            ->addSelect('date.value AS date')
            ->leftJoin(
                'token',
                OzonTokenModifyDateTime::class,
                'date',
                'date.event = token.event',
            );


        $dbal
            ->addSelect('name.value AS name')
            ->leftJoin(
                'token',
                OzonTokenName::class,
                'name',
                'name.event = token.event',
            );


        /** Если не админ - только токен профиля */

        if($this->profile)
        {
            $dbal->setParameter('profile', $this->profile, UserProfileUid::TYPE);
        }


        $dbal
            ->addSelect('active.value AS active')
            ->join(
                'token',
                OzonTokenActive::class,
                'active',
                'active.event = token.event',
            );


        $dbal
            ->addSelect('card.value AS card')
            ->leftJoin(
                'token',
                OzonTokenCard::class,
                'card',
                'card.event = token.event',
            );


        $dbal
            ->addSelect('stocks.value AS stocks')
            ->leftJoin(
                'token',
                OzonTokenStocks::class,
                'stocks',
                'stocks.event = token.event',
            );

        $dbal
            ->addSelect('type.value AS type')
            ->leftJoin(
                'token',
                OzonTokenType::class,
                'type',
                'type.event = token.event',
            );

        // ОТВЕТСТВЕННЫЙ

        // UserProfile
        $dbal
            ->addSelect('users_profile.event as users_profile_event')
            ->leftJoin(
                'token',
                UserProfile::class,
                'users_profile',
                'users_profile.id = profile.value',
            );


        // Info
        $dbal
            ->addSelect('users_profile_info.status as users_profile_status')
            ->leftJoin(
                'token',
                UserProfileInfo::class,
                'users_profile_info',
                'users_profile_info.profile = profile.value',
            );

        // Event
        $dbal->leftJoin(
            'users_profile',
            UserProfileEvent::class,
            'users_profile_event',
            'users_profile_event.id = users_profile.event'
        );


        // Personal
        $dbal
            ->addSelect('users_profile_personal.username AS users_profile_username')
            ->leftJoin(
                'users_profile_event',
                UserProfilePersonal::class,
                'users_profile_personal',
                'users_profile_personal.event = users_profile_event.id',
            );

        // Avatar

        $dbal->addSelect("CASE 
            WHEN users_profile_avatar.name IS NOT NULL 
            THEN CONCAT ( '/upload/".$dbal->table(UserProfileAvatar::class)."' , '/', users_profile_avatar.name) 
            ELSE NULL 
        END AS users_profile_avatar");

        $dbal
            ->addSelect("users_profile_avatar.ext AS users_profile_avatar_ext")
            ->addSelect('users_profile_avatar.cdn AS users_profile_avatar_cdn')
            ->leftJoin(
                'users_profile_event',
                UserProfileAvatar::class,
                'users_profile_avatar',
                'users_profile_avatar.event = users_profile_event.id',
            );

        /** ACCOUNT */

        $dbal->leftJoin(
            'users_profile_info',
            Account::class,
            'account',
            'account.id = users_profile_info.usr'
        );

        $dbal
            ->addSelect('account_event.email AS account_email')
            ->leftJoin(
                'account',
                AccountEvent::class,
                'account_event',
                'account_event.id = account.event AND account_event.account = account.id',
            );

        $dbal
            ->addSelect('account_status.status as account_status')
            ->leftJoin(
                'account_event',
                AccountStatus::class,
                'account_status',
                'account_status.event = account_event.id',
            );

        /* Поиск */
        if($this->search?->getQuery())
        {
            $dbal
                ->createSearchQueryBuilder($this->search)
                ->addSearchEqualUid('token.id')
                ->addSearchEqualUid('token.event')
                ->addSearchLike('account_event.email')
                ->addSearchLike('users_profile_personal.username');
        }

        return $this->paginator->fetchAllAssociative($dbal);

    }

}
