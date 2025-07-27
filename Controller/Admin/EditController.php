<?php

declare(strict_types=1);

namespace BaksDev\Ozon\Controller\Admin;

use BaksDev\Core\Controller\AbstractController;
use BaksDev\Core\Listeners\Event\Security\RoleSecurity;
use BaksDev\Ozon\Entity\Event\OzonTokenEvent;
use BaksDev\Ozon\Entity\OzonToken;
use BaksDev\Ozon\UseCase\Admin\NewEdit\OzonTokenDTO;
use BaksDev\Ozon\UseCase\Admin\NewEdit\OzonTokenForm;
use BaksDev\Ozon\UseCase\Admin\NewEdit\OzonTokenHandler;
use Symfony\Bridge\Doctrine\Attribute\MapEntity;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpKernel\Attribute\AsController;
use Symfony\Component\Routing\Annotation\Route;

#[AsController]
#[RoleSecurity('ROLE_OZON_EDIT')]
final class EditController extends AbstractController
{
    #[Route('/admin/ozon/edit/{id}', name: 'admin.newedit.edit', methods: ['GET', 'POST'])]
    public function edit(
        Request $request,
        #[MapEntity] OzonTokenEvent $OzonEvent,
        OzonTokenHandler $OzonHandler,
    ): Response {

        $OzonDTO = new OzonTokenDTO();
        $OzonEvent->getDto($OzonDTO);

        //        /** Запрещаем редактировать чужой токен */
        //        if($this->isAdmin() === true || $this->getProfileUid()?->equals($OzonEvent->getProfile()) === true)
        //        {
        //            $OzonEvent->getDto($OzonDTO);
        //        }

        if($request->getMethod() === 'GET')
        {
            $OzonDTO->getToken()->hiddenToken();
        }

        // Форма
        $form = $this->createForm(OzonTokenForm::class, $OzonDTO, [
            'action' => $this->generateUrl('ozon:admin.newedit.edit', ['id' => $OzonDTO->getEvent()]),
        ]);

        $form->handleRequest($request);

        if($form->isSubmitted() && $form->isValid() && $form->has('ozon_token'))
        {
            $this->refreshTokenForm($form);

            /** Запрещаем редактировать чужой токен */
            if($this->isAdmin() === false && $this->getProfileUid()?->equals($OzonDTO->getProfile()->getValue()) !== true)
            {
                $this->addFlash('breadcrumb.edit', 'danger.edit', 'ozon.admin', '404');
                return $this->redirectToReferer();
            }

            $OzonToken = $OzonHandler->handle($OzonDTO);

            if($OzonToken instanceof OzonToken)
            {
                $this->addFlash('breadcrumb.edit', 'success.edit', 'ozon.admin');

                return $this->redirectToRoute('ozon:admin.index');
            }

            $this->addFlash('breadcrumb.edit', 'danger.edit', 'ozon.admin', $OzonToken);

            return $this->redirectToReferer();
        }

        return $this->render(['form' => $form->createView()]);
    }
}
