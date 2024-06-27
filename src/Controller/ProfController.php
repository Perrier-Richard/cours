<?php

namespace App\Controller;

use App\Entity\Prof;
use App\Form\ProfType;
use App\Repository\ProfRepository;
use Doctrine\ORM\EntityManagerInterface;
use Knp\Component\Pager\PaginatorInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Attribute\Route;

class ProfController extends AbstractController
{
    #[Route('/prof', name: 'app_prof')]
    public function index(ProfRepository $repository, PaginatorInterface $paginator, Request $request): Response
    {
        $profs = $paginator->paginate(
            $repository->findAll(),
            $request->query->getInt('page', 1),
            10
        );

        return $this->render('pages/prof/index.html.twig', [
            'profs' => $profs,
        ]);
    }

    #[Route('/prof/nouveau', 'prof_new', methods: ['GET', 'POST'])]
    public function new(Request $request, EntityManagerInterface $manager): Response
    {
        $prof = new Prof();
        $form = $this->createForm(ProfType::class, $prof);

        $form->handleRequest($request);
        if($form->isSubmitted() && $form->isValid()){
            $prof = $form->getData();

            $manager->persist($prof);
            $manager->flush();

            $this->addFlash(
                'success',
                "Vos changements ont été enregistré !"
            );

            return $this->redirectToRoute('app_prof');
        }

        return $this->render('pages/prof/new.html.twig', [
            'form' => $form,
        ]);
    }

    #[Route('/prof/edit/{id}', 'prof_edit', methods: ['GET', 'POST'])]
    public function edit(Request $request, EntityManagerInterface $manager, Prof $prof): Response
    {
        $form = $this->createForm(ProfType::class, $prof);

        $form->handleRequest($request);
        if($form->isSubmitted() && $form->isValid()){
            $prof = $form->getData();

            $manager->flush();

            $this->addFlash(
                'success',
                "Vos changements ont été enregistré !"
            );

            return $this->redirectToRoute('app_prof');
        }

        return $this->render('pages/prof/edit.html.twig', [
            'form' => $form,
        ]);
    }

    #[Route('/prof/remove/{id}', 'prof_remove', methods: ['GET'])]
    public function remove(Request $request, EntityManagerInterface $manager, Prof $prof): Response
    {
        $manager->remove($prof);
        $manager->flush();

        $this->addFlash(
            'success',
            "Vos changements ont été enregistré !"
        );

        return $this->redirectToRoute('app_prof');
    }
}
