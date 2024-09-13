<?php


declare(strict_types=1);

namespace BaksDev\Ozon\Controller\Admin;

use BaksDev\Core\Controller\AbstractController;
use BaksDev\Core\Listeners\Event\Security\RoleSecurity;
use BaksDev\Ozon\Entity\Event\OzonTokenEvent;
use BaksDev\Ozon\Entity\OzonToken;
use BaksDev\Ozon\UseCase\Admin\Delete\OzonTokenDeleteDTO;
use BaksDev\Ozon\UseCase\Admin\Delete\OzonTokenDeleteForm;
use BaksDev\Ozon\UseCase\Admin\Delete\OzonTokenDeleteHandler;
use Symfony\Bridge\Doctrine\Attribute\MapEntity;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\HttpKernel\Attribute\AsController;

#[AsController]
#[RoleSecurity('ROLE_OZON_DELETE')]
final class DeleteController extends AbstractController
{
    #[Route('/admin/ozon/delete/{id}', name: 'admin.delete', methods: ['GET', 'POST'])]
    public function delete(
        Request $request,
        #[MapEntity] OzonTokenEvent $ozonEvent,
        OzonTokenDeleteHandler $ozonDeleteHandler,
    ): Response {

        $ozonDeleteDTO = new OzonTokenDeleteDTO();
        $ozonEvent->getDto($ozonDeleteDTO);
        $form = $this->createForm(OzonTokenDeleteForm::class, $ozonDeleteDTO, [
            'action' => $this->generateUrl('ozon:admin.delete', ['id' => $ozonDeleteDTO->getEvent()]),
        ]);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid() && $form->has('ozon_token_delete'))
        {
            $handle = $ozonDeleteHandler->handle($ozonDeleteDTO);

            $this->addFlash(
                'page.delete',
                $handle instanceof OzonToken ? 'success.delete' : 'danger.delete',
                'ozon.admin',
                $handle
            );

            return $handle instanceof OzonToken ? $this->redirectToRoute('ozon:admin.index') : $this->redirectToReferer();
        }

        return $this->render([
            'form' => $form->createView()
        ]);
    }
}
