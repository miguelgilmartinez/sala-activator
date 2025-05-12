<?php

namespace App\Controller;

use App\Entity\SwitchSalas;
use App\Form\SwitchSalasType;
use App\Repository\SwitchSalasRepository;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

#[Route('/switch-salas')]
class SwitchSalasController extends AbstractController
{
    #[Route('/', name: 'app_switch_salas_index', methods: ['GET'])]
    public function index(SwitchSalasRepository $switchSalasRepository): Response
    {
        return $this->render('switch_salas/index.html.twig', [
            'switches' => $switchSalasRepository->findAll(),
        ]);
    }

    #[Route('/new', name: 'app_switch_salas_new', methods: ['GET', 'POST'])]
    public function new(Request $request, EntityManagerInterface $entityManager): Response
    {
        $switch = new SwitchSalas();
        $form = $this->createForm(SwitchSalasType::class, $switch);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $entityManager->persist($switch);
            $entityManager->flush();

            $this->addFlash('success', '¡Switch creado con éxito!');

            return $this->redirectToRoute('app_switch_salas_index');
        }

        return $this->render('switch_salas/new.html.twig', [
            'form' => $form->createView(),
        ]);
    }

    #[Route('/{id}', name: 'app_switch_salas_show', methods: ['GET'])]
    public function show(SwitchSalas $switch): Response
    {
        return $this->render('switch_salas/show.html.twig', [
            'switch' => $switch,
        ]);
    }

    #[Route('/{id}/edit', name: 'app_switch_salas_edit', methods: ['GET', 'POST'])]
    public function edit(Request $request, SwitchSalas $switch, EntityManagerInterface $entityManager): Response
    {
        $form = $this->createForm(SwitchSalasType::class, $switch);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $entityManager->flush();

            $this->addFlash('success', '¡Switch actualizado con éxito!');

            return $this->redirectToRoute('app_switch_salas_index');
        }

        return $this->render('switch_salas/edit.html.twig', [
            'switch' => $switch,
            'form' => $form->createView(),
        ]);
    }

    #[Route('/{id}', name: 'app_switch_salas_delete', methods: ['POST'])]
    public function delete(Request $request, SwitchSalas $switch, EntityManagerInterface $entityManager): Response
    {
        if ($this->isCsrfTokenValid('delete'.$switch->getId(), $request->request->get('_token'))) {
            $entityManager->remove($switch);
            $entityManager->flush();

            $this->addFlash('success', '¡Switch eliminado con éxito!');
        }

        return $this->redirectToRoute('app_switch_salas_index');
    }
}