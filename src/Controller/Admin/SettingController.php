<?php

namespace App\Controller\Admin;

use App\Entity\NotificationTemplate;
use App\Entity\SiteSetting;
use App\Entity\SocialSetting;
use App\Form\EditSocialSettingType;
use App\Form\GeneralSettingsType;
use App\Form\NotificationTemplateType;
use App\Form\SocialSettingsType;
use App\Form\WebSiteSettingsType;
use App\Repository\NotificationTemplateRepository;
use App\Repository\NotificationTypeRepository;
use App\Repository\SiteSettingRepository;
use App\Repository\SocialSettingRepository;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Security;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request as HttpFoundationRequest;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;


#[Route('/admin/setting')]
#[Security("is_granted('ROLE_SUPER_USER')", statusCode: 403, message: "Vous n'avez pas les autorisations suffisantes pour consulter cette page")]
class SettingController extends AbstractController
{
    #[Route('', name: 'app_admin_setting')]
    public function index(HttpFoundationRequest $request, NotificationTemplateRepository $notificationTemplateRepository, NotificationTypeRepository $notificationTypeRepository, SocialSettingRepository $socialSettingRepository, SiteSettingRepository $siteSettingRepository): Response
    {
        $siteSetting = $siteSettingRepository->findOneBy([]);
        if ($siteSetting === null) {
            $siteSetting = new SiteSetting();
        }

        $siteSettingForm = $this->createForm(WebSiteSettingsType::class, $siteSetting);
        $siteSettingForm->handleRequest($request);

        $generalSettingForm = $this->createForm(GeneralSettingsType::class, $siteSetting);
        $generalSettingForm->handleRequest($request);

        if (($siteSettingForm->isSubmitted() && $siteSettingForm->isValid()) || ($generalSettingForm->isSubmitted() && $generalSettingForm->isValid())) {
            $siteSettingRepository->save($siteSetting, true);

            return $this->redirectToRoute('app_admin_setting');
        }

        $socialSetting = new SocialSetting();
        $socialSettingForm = $this->createForm(SocialSettingsType::class, $socialSetting);
        $socialSettingForm->handleRequest($request);
        if ($socialSettingForm->isSubmitted() && $socialSettingForm->isValid()) {
            $socialSettingRepository->save($socialSetting, true);

            return $this->redirectToRoute('app_admin_setting');
        }

        $socials = $socialSettingRepository->findAll();
        
        if ($request->request->get('socialSettings') !==null && $this->isCsrfTokenValid('socialsettings', $request->request->get('_token'))) {
            $data = $request->request->getIterator();
            $socialsData = $data['socials'];
            foreach ($socialsData as $item) {
                // dd($item);
                $s = $socialSettingRepository->find($item['id']);
                if ($s) {
                    $s->setLink($item['link']);
                    $socialSettingRepository->save($s, true);
                }
            }

            return $this->redirectToRoute('app_admin_setting');

        }

        $notificationTypes = $notificationTypeRepository->findAll();
        $notificationTypeForms = [];
        foreach ($notificationTypes as $notificationType) {
            $notificationTemplate = $notificationType->getNotificationTemplate();
            if ($notificationTemplate === null) {
                $notificationTemplate = new NotificationTemplate();
            }
            $notificationTemplate->setType($notificationType);
            $form = $this->createForm(NotificationTemplateType::class, $notificationTemplate);
            $form->handleRequest($request);
            if ($form->isSubmitted() && $form->isValid()) {
                $notificationTemplateRepository->save($notificationTemplate, true);
                $this->addFlash('success', "Template is updated or created");
                return $this->redirectToRoute('app_admin_setting');
            }
            $notificationTypeForms[] = $form->createView();
        }

        return $this->render('admin/setting/index.html.twig', [
            'isSettingController' => true,
            'generalSettingForm' => $generalSettingForm->createView(),
            'siteSettingForm' => $siteSettingForm->createView(), 
            'socials' => $socials,
            'socialSettingForm' => $socialSettingForm->createView(),
            'notificationTypes' => $notificationTypes,
            'notificationTypeForms' => $notificationTypeForms

        ]);
    }
}
