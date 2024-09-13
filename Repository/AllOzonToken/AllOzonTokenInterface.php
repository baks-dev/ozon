<?php

namespace BaksDev\Ozon\Repository\AllOzonToken;

use BaksDev\Core\Form\Search\SearchDTO;
use BaksDev\Core\Services\Paginator\PaginatorInterface;
use BaksDev\Users\Profile\UserProfile\Type\Id\UserProfileUid;

interface AllOzonTokenInterface
{
    public function search(SearchDTO $search): self;

    public function profile(UserProfileUid|string $profile): self;

    public function findAllPaginator(): PaginatorInterface;
}
