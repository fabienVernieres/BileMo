<?php

namespace App\Controller\Web;

use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

class DocApiController extends AbstractController
{
    #[Route('/doc/api', name: 'doc_api')]
    /**
     * To read the documentation
     *
     * @return Response
     */
    public function index(): Response
    {
        return $this->render('api/index.html.twig', []);
    }
}