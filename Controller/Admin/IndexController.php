<?php


declare(strict_types=1);

namespace BaksDev\Ozon\Controller\Admin;

use BaksDev\Core\Form\Search\SearchDTO;
use BaksDev\Core\Form\Search\SearchForm;
use BaksDev\Ozon\Repository\AllOzonToken\AllOzonTokenInterface;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use BaksDev\Core\Controller\AbstractController;
use Symfony\Component\Routing\Annotation\Route;
use BaksDev\Core\Listeners\Event\Security\RoleSecurity;
use Symfony\Component\HttpKernel\Attribute\AsController;

#[AsController]
#[RoleSecurity('ROLE_OZON')]
final class IndexController extends AbstractController
{
    #[Route('/admin/ozon/{page<\d+>}', name: 'admin.index', methods: ['GET', 'POST'])]
    public function index(
        Request $request,
        AllOzonTokenInterface $allOzon,
        int $page = 0,
    ): Response {

        // Поиск
        $search = new SearchDTO();
        $searchForm = $this->createForm(SearchForm::class, $search);
        $searchForm->handleRequest($request);

        // Фильтр
        // $filter = new ProductsStocksFilterDTO($request, $ROLE_ADMIN ? null : $this->getProfileUid());
        // $filterForm = $this->createForm(ProductsStocksFilterForm::class, $filter);
        // $filterForm->handleRequest($request);

        // Получаем список
        $Ozon = $allOzon
            ->search($search)
            ->findAllPaginator();

        return $this->render(
            [
                'query' => $Ozon,
                'search' => $searchForm->createView(),
            ]
        );
    }
}
