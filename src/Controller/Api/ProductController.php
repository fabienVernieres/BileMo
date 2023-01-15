<?php

namespace App\Controller\Api;

use App\Entity\Brand;
use App\Repository\ProductRepository;
use App\Service\PaginationService;
use JMS\Serializer\SerializerInterface;
use JMS\Serializer\SerializationContext;
use Symfony\Contracts\Cache\ItemInterface;
use Symfony\Contracts\Cache\CacheInterface;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;

#[Route('/api', name: 'api_')]
class ProductController extends AbstractController
{
    private CacheInterface $cache;
    private mixed $context;
    private ProductRepository $productRepository;
    private SerializerInterface $serializer;
    private PaginationService $pagination;

    public function __construct(
        CacheInterface $cache,
        ProductRepository $productRepository,
        SerializerInterface $serializer,
        PaginationService $pagination
    ) {
        $this->cache = $cache;
        $this->context = SerializationContext::create()->setGroups(['products']);
        $this->productRepository = $productRepository;
        $this->serializer = $serializer;
        $this->pagination = $pagination;
    }

    #[Route('/v1/products', name: 'products', methods: ['GET'])]
    /**
     * get All products
     *
     * @param  Request $request
     * @return JsonResponse
     */
    public function getAll(Request $request): JsonResponse
    {
        // Current page
        $currentPage = $request->get('page', 1);
        $numberOfItems = 10;

        // cache the request
        $products = $this->cache->get(
            'products' . $currentPage,
            function (ItemInterface $item) use ($currentPage, $numberOfItems) {
                $item->expiresAfter(3600);
                $products = $this->productRepository->findAllWithPagination($currentPage, $numberOfItems);
                $pagination = $this->pagination->pagination('products', $numberOfItems, $this->productRepository->count([]));
                $productsWithPagination = array_merge($products, $pagination);

                return $this->serializer->serialize($productsWithPagination, 'json', $this->context);
            }
        );

        // return OK response with the list of products
        return new JsonResponse($products, 200, [], true);
    }

    #[Route('/v1/products/{id}', name: 'product', methods: ['GET'])]
    /**
     * get a product by id
     *
     * @param  int $id
     * @return JsonResponse
     */
    public function getOne(int $id): JsonResponse
    {
        // cache the request
        $product = $this->cache->get(
            'product_' . $id,
            function (ItemInterface $item) use ($id) {
                $item->expiresAfter(3600);
                return $this->serializer->serialize($this->productRepository->findby(['id' => $id]), 'json', $this->context);
            }
        );

        // return OK response with the product sheet
        return new JsonResponse($product, 200, [], true);
    }

    #[Route('/v1/products/brands/{brand}', name: 'products_brand', methods: ['GET'])]
    /**
     * list of brand products
     *
     * @param  Brand $brand
     * @return JsonResponse
     */
    public function brand(Brand $brand): JsonResponse
    {
        // cache the request
        $products_brand = $this->cache->get(
            'products_brand_' . $brand->getId(),
            function (ItemInterface $item) use ($brand) {
                $item->expiresAfter(3600);
                return $this->serializer->serialize($this->productRepository->findBy(['brand' => $brand]), 'json', $this->context);
            }
        );

        // return OK response with the list of brand products
        return new JsonResponse($products_brand, 200, [], true);
    }
}