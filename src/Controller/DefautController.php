<?php

namespace App\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Attribute\Route;

class DefautController extends AbstractController
{
    #[Route('/', name: 'app_defaut')]
    public function index(): Response
    {
        return $this->render('pages/index.html.twig');
    }
}
