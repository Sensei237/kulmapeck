<?php

namespace App\Controller\Admin;

use App\Entity\NetworkConfig;
use App\Form\NetworkConfigType;
use App\Repository\NetworkConfigRepository;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

#[Route('/admin/network/config')]
class NetworkConfigController extends AbstractController
{
    #[Route('/', name: 'app_admin_network_config_index', methods: ['GET'])]
    public function index(NetworkConfigRepository $networkConfigRepository): Response
    {
        return $this->render('admin/network_config/index.html.twig', [
            'network_configs' => $networkConfigRepository->findAll(),
        ]);
    }

    #[Route('/new', name: 'app_admin_network_config_new', methods: ['GET', 'POST'])]
    public function new(Request $request, NetworkConfigRepository $networkConfigRepository): Response
    {
        $nc = $networkConfigRepository->findAll();
        if (!empty($nc)) {
            return $this->redirectToRoute('app_admin_network_config_edit', ['id' => $nc[0]->getId()]);
        }

        $networkConfig = new NetworkConfig();
        $form = $this->createForm(NetworkConfigType::class, $networkConfig);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $networkConfigRepository->save($networkConfig, true);

            return $this->redirectToRoute('app_admin_network_config_index', [], Response::HTTP_SEE_OTHER);
        }

        return $this->render('admin/network_config/new.html.twig', [
            'network_config' => $networkConfig,
            'form' => $form->createView(),
        ]);
    }

    #[Route('/{id}', name: 'app_admin_network_config_show', methods: ['GET'])]
    public function show(NetworkConfig $networkConfig): Response
    {
        return $this->render('admin/network_config/show.html.twig', [
            'network_config' => $networkConfig,
        ]);
    }

    #[Route('/{id}/edit', name: 'app_admin_network_config_edit', methods: ['GET', 'POST'])]
    public function edit(Request $request, NetworkConfig $networkConfig, NetworkConfigRepository $networkConfigRepository): Response
    {
        $form = $this->createForm(NetworkConfigType::class, $networkConfig);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $networkConfigRepository->save($networkConfig, true);

            return $this->redirectToRoute('app_admin_network_config_index', [], Response::HTTP_SEE_OTHER);
        }

        return $this->render('admin/network_config/edit.html.twig', [
            'network_config' => $networkConfig,
            'form' => $form->createView(),
        ]);
    }

    #[Route('/{id}', name: 'app_admin_network_config_delete', methods: ['POST'])]
    public function delete(Request $request, NetworkConfig $networkConfig, NetworkConfigRepository $networkConfigRepository): Response
    {
        if ($this->isCsrfTokenValid('delete'.$networkConfig->getId(), $request->request->get('_token'))) {
            $networkConfigRepository->remove($networkConfig, true);
        }

        return $this->redirectToRoute('app_admin_network_config_index', [], Response::HTTP_SEE_OTHER);
    }
}
