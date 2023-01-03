<?php

namespace App\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

class ApiController extends AbstractController
{
    #[Route('/doc/api', name: 'doc_api')]
    public function index(): Response
    {
        return $this->render('api/index.html.twig', []);
    }
}