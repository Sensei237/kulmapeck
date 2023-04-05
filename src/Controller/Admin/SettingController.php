<?php

namespace App\Controller\Admin;

use App\Entity\SiteSetting;
use App\Entity\SocialSetting;
use App\Form\EditSocialSettingType;
use App\Form\GeneralSettingsType;
use App\Form\SocialSettingsType;
use App\Form\WebSiteSettingsType;
use App\Repository\SiteSettingRepository;
use App\Repository\SocialSettingRepository;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request as HttpFoundationRequest;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;


#[Route('/admin/setting')]
class SettingController extends AbstractController
{
    #[Route('', name: 'app_admin_setting')]
    public function index(HttpFoundationRequest $request, SocialSettingRepository $socialSettingRepository, SiteSettingRepository $siteSettingRepository): Response
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

        return $this->render('admin/setting/index.html.twig', [
            'isSettingController' => true,
            'generalSettingForm' => $generalSettingForm->createView(),
            'siteSettingForm' => $siteSettingForm->createView(), 
            'socials' => $socials,
            'socialSettingForm' => $socialSettingForm->createView(),

        ]);
    }
}
