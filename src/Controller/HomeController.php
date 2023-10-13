<?php

namespace App\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

class HomeController extends AbstractController
{
    #[Route('/', name: 'app_home')]
    public function index(): Response
    {
        $tableau = ['premier', 'second', 'troisième'];
        dump($tableau);
        $connectedUser = $this->getUser(); 
        dump($connectedUser);
        return $this->render('home/index.html.twig', [
            'controller_name' => 'HomeController',
            'mon_tableau' => $tableau
        ]);
    }
}
