<?php

namespace App\Controller;
use App\Entity\Rental;
use App\Form\RentalType;
use App\Repository\RentalRepository;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\Security\Http\Attribute\IsGranted;

class RentalController extends AbstractController
{
    #[Route('/rental', name: 'app_rental')]
    public function index(RentalRepository $rental_repository): Response
    {
        $rentals = $rental_repository->findAll();
        //dump($rentals);
        return $this->render('rental/index.html.twig', [
            'rentals' => $rentals,
        ]);
    }

    //faire la route et la fonction qui n'affiche qu'un seul bien
    #[Route('/rental/{id}', name: 'app_rental_show')]
    public function show(RentalRepository $repository, int $id): Response 
    {
        $rental = $repository->find($id);
        //dump($rental);
        return $this->render('rental/show.html.twig', [
            'rental' => $rental
        ]);
    }


    #[Route('/rental/new', name: 'app_rental_new', priority:2 )]
    public function add(Request $request, EntityManagerInterface $em): Response
    {
        if($this->IsGranted('ROLE_USER')){
        $rental = new Rental(); //on initialise unn objet que le formulaire va remplir
        
        $form = $this->createForm(RentalType::class, $rental);

        $form->handleRequest($request);
        if ($form->isSubmitted() && $form->isValid()) {
            $rental = $form->getData();
            $rental->setUser($this->getUser());
            $em->persist($rental);
            $em->flush();
        }
        return $this->render('rental/new.html.twig', [
            'form' => $form
        ]);
        }else {
            return $this->redirectToRoute('app_login');
        }
    }

    #[Route('/rental/{id}/edit', name: 'app_rental_edit')]
    public function edit(Request $request, EntityManagerInterface $em, int $id): Response
    {
        $rental = $em->getRepository(Rental::class)->find($id);
        if($this->IsGranted('ROLE_USER') && $this->getUser() === $rental->getUser()){
        $form = $this->createForm(RentalType::class, $rental);
        $form->handleRequest($request);
        if ($form->isSubmitted() && $form->isValid()) {
            $rental = $form->getData();
            $em->persist($rental);
            $em->flush();
        }
        return $this->render('rental/edit.html.twig', [
            'form' => $form
        ]);
    } else {
        return $this->redirectToRoute('app_login');
    }
    }

    #[Route('/rental/{id}/delete', name: 'app_rental_delete')]
    public function delete(EntityManagerInterface $em, int $id): Response
    {
        $rental = $em->getRepository(Rental::class)->find($id);
        $em->remove($rental);
        $em->flush();
        return $this->redirectToRoute('app_rental');
    }
}