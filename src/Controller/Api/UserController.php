<?php

namespace App\Controller\Api;

use App\Entity\User;
use App\Service\ApiService;
use App\Service\PaginationService;
use App\Repository\CustomerRepository;
use JMS\Serializer\SerializerInterface;
use JMS\Serializer\SerializationContext;
use Symfony\Contracts\Cache\ItemInterface;
use Symfony\Contracts\Cache\CacheInterface;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;

#[Route('/api', name: 'api_')]
class UserController extends AbstractController
{
    private User $user;
    private CacheInterface $cache;
    private mixed $context;
    private SerializerInterface $serializer;
    private CustomerRepository $customerRepository;
    private PaginationService $pagination;

    public function __construct(
        ApiService $apiService,
        CacheInterface $cache,
        SerializerInterface $serializer,
        CustomerRepository $customerRepository,
        PaginationService $pagination
    ) {
        // The current user
        $this->user = $apiService->apiUser();

        // The cache
        $this->cache = $cache;

        $this->context = SerializationContext::create()->setGroups(['users']);
        $this->serializer = $serializer;
        $this->customerRepository = $customerRepository;
        $this->pagination = $pagination;
    }

    #[Route('/v1/users', name: 'users', methods: ['GET'])]
    /**
     * Get all of the API user's (client) users
     *
     * @param  Request $request
     * @return JsonResponse
     */
    public function getAll(Request $request): JsonResponse
    {
        // Current page
        $currentPage = $request->get('page', 1);
        $usersPerPage = 10;

        // Cache the request
        $users = $this->cache->get(
            'users_' . $this->user->getId() . '_' . $currentPage,
            function (ItemInterface $item) use ($currentPage, $usersPerPage) {
                $item->expiresAfter(60);
                // Request to repository with API user as constraint
                $users = $this->customerRepository->findAllWithPagination($currentPage, $usersPerPage, $this->user);
                $pagination = $this->pagination->pagination('users', $usersPerPage, $this->customerRepository->count(['user' => $this->user]));
                $usersWithPagination = array_merge($users, $pagination);

                return $this->serializer->serialize($usersWithPagination, 'json', $this->context);
            }
        );

        // Return OK response with the list of users
        return new JsonResponse($users, 200, [], true);
    }

    #[Route('/v1/users/{id}', name: 'user', methods: ['GET'])]
    /**
     * Get user from API user (client) by id
     *
     * @param  int $id
     * @return JsonResponse
     */
    public function getOne(int $id): JsonResponse
    {
        // Cache the request
        $user = $this->cache->get('user_' . $id, function (ItemInterface $item) use ($id) {
            $item->expiresAfter(3600);
            return $this->serializer->serialize($this->customerRepository->findOneBy([
                'id' => $id,
                'user' => $this->user
            ]), 'json', $this->context);
        });

        // Return OK response with the user file
        return new JsonResponse($user, 200, [], true);
    }
}