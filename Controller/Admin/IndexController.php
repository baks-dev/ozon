<?php


declare(strict_types=1);

namespace BaksDev\Ozon\Controller\Admin;

use BaksDev\Core\Controller\AbstractController;
use BaksDev\Core\Form\Search\SearchDTO;
use BaksDev\Core\Form\Search\SearchForm;
use BaksDev\Core\Listeners\Event\Security\RoleSecurity;
use BaksDev\Ozon\Repository\AllOzonToken\AllOzonTokenInterface;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpKernel\Attribute\AsController;
use Symfony\Component\Routing\Annotation\Route;

#[AsController]
#[RoleSecurity('ROLE_OZON')]
final class IndexController extends AbstractController
{
    #[Route('/admin/ozon/{page<\d+>}', name: 'admin.index', methods: ['GET', 'POST'])]
    public function index(
        Request $request,
        AllOzonTokenInterface $allOzon,
        int $page = 0,
    ): Response
    {

        // Поиск
        $search = new SearchDTO();

        $searchForm = $this
            ->createForm(
                type: SearchForm::class,
                data: $search,
                options: ['action' => $this->generateUrl('ozon:admin.index')],
            )
            ->handleRequest($request);


        // Получаем список
        $Ozon = $allOzon
            ->search($search)
            ->profile($this->getProfileUid())
            ->findAllPaginator();

        return $this->render(
            [
                'query' => $Ozon,
                'search' => $searchForm->createView(),
            ],
        );
    }
}
