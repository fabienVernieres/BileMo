<?php

namespace App\Controller;

use App\Entity\Brand;
use App\Entity\Product;
use App\Repository\ProductRepository;
use JMS\Serializer\SerializerInterface;
use JMS\Serializer\SerializationContext;
use Symfony\Contracts\Cache\ItemInterface;
use Symfony\Contracts\Cache\CacheInterface;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;

#[Route('/api', name: 'api_')]
class ProductController extends AbstractController
{
    private CacheInterface $cache;
    private mixed $context;
    private ProductRepository $productRepository;
    private SerializerInterface $serializer;

    public function __construct(CacheInterface $cache, ProductRepository $productRepository, SerializerInterface $serializer)
    {
        $this->cache = $cache;
        $this->context = SerializationContext::create()->setGroups(['products']);
        $this->productRepository = $productRepository;
        $this->serializer = $serializer;
    }

    #[Route('/v1/products', name: 'products', methods: ['GET'])]
    /**
     * getAll products
     *
     * @return JsonResponse
     */
    public function getAll(): JsonResponse
    {
        // cache the request
        $products = $this->cache->get('products', function (ItemInterface $item) {
            $item->expiresAfter(3600);
            return $this->serializer->serialize($this->productRepository->findAll(), 'json', $this->context);
        });

        return new JsonResponse($products, 200, [], true);
    }

    #[Route('/v1/product/{id}', name: 'product', methods: ['GET'])]
    /**
     * getOne product by id
     *
     * @param  Product $id
     * @return JsonResponse
     */
    public function getOne(Product $id): JsonResponse
    {
        // cache the request
        $product = $this->cache->get('product_' . $id->getId(), function (ItemInterface $item) use ($id) {
            $item->expiresAfter(3600);
            return $this->serializer->serialize($this->productRepository->find($id), 'json', $this->context);
        });

        return new JsonResponse($product, 200, [], true);
    }

    #[Route('/v1/products/{brand}', name: 'products_brand', methods: ['GET'])]
    /**
     * brand all products
     *
     * @param  Brand $brand
     * @return JsonResponse
     */
    public function brand(Brand $brand): JsonResponse
    {
        // cache the request
        $products_brand = $this->cache->get('products_brand_' . $brand->getId(), function (ItemInterface $item) use ($brand) {
            $item->expiresAfter(3600);
            return $this->serializer->serialize($this->productRepository->findBy(['brand' => $brand]), 'json', $this->context);
        });
        return new JsonResponse($products_brand, 200, [], true);
    }
}