<?php


declare(strict_types=1);

namespace BaksDev\Ozon\Controller\Admin;

use BaksDev\Core\Controller\AbstractController;
use BaksDev\Core\Listeners\Event\Security\RoleSecurity;
use BaksDev\Ozon\Entity\OzonToken;
use BaksDev\Ozon\UseCase\Admin\NewEdit\OzonTokenDTO;
use BaksDev\Ozon\UseCase\Admin\NewEdit\OzonTokenForm;
use BaksDev\Ozon\UseCase\Admin\NewEdit\OzonTokenHandler;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\HttpKernel\Attribute\AsController;

#[AsController]
#[RoleSecurity('ROLE_OZON_NEW')]
final class NewController extends AbstractController
{
    #[Route('/admin/ozon/new', name: 'admin.newedit.new', methods: ['GET', 'POST'])]
    public function news(
        Request $request,
        OzonTokenHandler $OzonHandler,
    ): Response {

        $OzonDTO = new OzonTokenDTO();

        if($this->getAdminFilterProfile())
        {
            $OzonDTO->setProfile($this->getAdminFilterProfile());
        }

        // Форма
        $form = $this->createForm(OzonTokenForm::class, $OzonDTO, [
            'action' => $this->generateUrl('ozon:admin.newedit.new'),
        ]);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid() && $form->has('ozon_token'))
        {
            $handle = $OzonHandler->handle($OzonDTO);

            $this->addFlash(
                'page.new',
                $handle instanceof OzonToken ? 'success.new' : 'danger.new',
                'ozon.admin',
                $handle
            );

            return $handle instanceof OzonToken ? $this->redirectToRoute('ozon:admin.index') : $this->redirectToReferer();
        }

        return $this->render(['form' => $form->createView()]);
    }
}
