<?php

namespace App\Controller;

use App\Entity\Product;
use App\Repository\ProductRepository;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;

#[Route('/api', name: 'api_')]
class ProductController extends AbstractController
{
    #[Route('/products', name: 'products')]
    public function getAll(ProductRepository $productRepository): JsonResponse
    {
        return $this->json($productRepository->findAll(), 200, [], ['groups' => 'products']);
    }

    #[Route('/product/{id}', name: 'product')]
    public function getOne(Product $id, ProductRepository $productRepository): JsonResponse
    {
        return $this->json($productRepository->find($id), 200, [], ['groups' => 'products']);
    }
}