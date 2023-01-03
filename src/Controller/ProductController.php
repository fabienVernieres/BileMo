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
    #[Route('/v1/products', name: 'products', methods: ['GET'])]
    public function getAll(ProductRepository $productRepository): JsonResponse
    {
        return $this->json($productRepository->findAll(), 200, [], ['groups' => 'products']);
    }

    #[Route('/v1/product/{id}', name: 'product', methods: ['GET'])]
    public function getOne(Product $id, ProductRepository $productRepository): JsonResponse
    {
        return $this->json($productRepository->find($id), 200, [], ['groups' => 'products']);
    }
}