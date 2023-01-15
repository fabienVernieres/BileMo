<?php

namespace App\Controller\Api;

use DateTime;
use App\Entity\User;
use App\Entity\Customer;
use App\Repository\UserRepository;
use App\Repository\CustomerRepository;
use App\Service\PaginationService;
use JMS\Serializer\SerializerInterface;
use JMS\Serializer\SerializationContext;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Lexik\Bundle\JWTAuthenticationBundle\Services\JWTTokenManagerInterface;
use Symfony\Component\Security\Core\Authentication\Token\Storage\TokenStorageInterface;
use Symfony\Contracts\Cache\CacheInterface;
use Symfony\Contracts\Cache\ItemInterface;

#[Route('/api', name: 'api_')]
class UserController extends AbstractController
{
    private JWTTokenManagerInterface $jwtManager;
    private TokenStorageInterface $tokenStorageInterface;
    private $decodedJwtToken;
    private User $user;
    private CacheInterface $cache;
    private mixed $context;
    private SerializerInterface $serializer;
    private CustomerRepository $customerRepository;
    private PaginationService $pagination;

    public function __construct(
        TokenStorageInterface $tokenStorageInterface,
        JWTTokenManagerInterface $jwtManager,
        UserRepository $userRepository,
        CacheInterface $cache,
        SerializerInterface $serializer,
        CustomerRepository $customerRepository,
        PaginationService $pagination
    ) {
        $this->jwtManager = $jwtManager;
        $this->tokenStorageInterface = $tokenStorageInterface;
        $this->decodedJwtToken = $this->jwtManager->decode($this->tokenStorageInterface->getToken());

        // The current user
        $this->user = $userRepository->findOneBy([
            'email' => $this->decodedJwtToken['email']
        ]);

        // The cache
        $this->cache = $cache;

        $this->context = SerializationContext::create()->setGroups(['users']);
        $this->serializer = $serializer;
        $this->customerRepository = $customerRepository;
        $this->pagination = $pagination;
    }

    #[Route('/v1/users', name: 'user_new', methods: ['POST'])]
    /**
     * Add a customer
     *
     * @param  EntityManagerInterface $entityManager
     * @return JsonResponse
     */
    public function new(EntityManagerInterface $entityManager): JsonResponse
    {
        // Create a new customer object
        $customer = new Customer();

        $request = new Request($_POST);
        $request = $request->toArray();

        $customer->setUser($this->user);
        $customer->setLastname($request['lastname']);
        $customer->setFirstname($request['firstname']);
        $customer->setEmail($request['email']);
        $customer->setCreationDate(new DateTime());

        $entityManager->persist($customer);
        $entityManager->flush();

        // Return created response with the object
        $customer = $this->serializer->serialize($customer, 'json', $this->context);
        return new JsonResponse($customer, 201, [], true);
    }


    #[Route('/v1/users/{id}', name: 'user_delete', methods: ['DELETE'])]
    /**
     * Delete a customer
     *
     * @param  Customer $id
     * @param  EntityManagerInterface $entityManager
     * @return JsonResponse
     */
    public function delete(Customer $id, EntityManagerInterface $entityManager): JsonResponse
    {
        // Remove the user
        if ($id->getUser() === $this->user) {
            $entityManager->remove($id);
            $entityManager->flush();
        }

        // Delete the cache
        $this->cache->delete('user_' . $id->getId());

        // Return no content response
        return new JsonResponse(null, 204, [], false);
    }


    #[Route('/v1/users', name: 'users', methods: ['GET'])]
    /**
     * Get all of the user's clients
     *
     * @param  Request $request
     * @return JsonResponse
     */
    public function getAll(Request $request): JsonResponse
    {
        // Current page
        $currentPage = $request->get('page', 1);
        $customersPerPage = 10;

        // Cache the request
        $customers = $this->cache->get(
            'users_' . $this->user->getId() . '_' . $currentPage,
            function (ItemInterface $item) use ($currentPage, $customersPerPage) {
                $item->expiresAfter(60);
                $customers = $this->customerRepository->findAllWithPagination($currentPage, $customersPerPage, $this->user);
                $pagination = $this->pagination->pagination('users', $customersPerPage, $this->customerRepository->count(['user' => $this->user]));
                $customersWithPagination = array_merge($customers, $pagination);

                return $this->serializer->serialize($customersWithPagination, 'json', $this->context);
            }
        );

        // Return OK response with the list of customers
        return new JsonResponse($customers, 200, [], true);
    }

    #[Route('/v1/users/{id}', name: 'user', methods: ['GET'])]
    /**
     * Get user client by id
     *
     * @param  int $id
     * @return JsonResponse
     */
    public function getOne(int $id): JsonResponse
    {
        // Cache the request
        $customer = $this->cache->get('user_' . $id, function (ItemInterface $item) use ($id) {
            $item->expiresAfter(3600);
            return $this->serializer->serialize($this->customerRepository->findOneBy([
                'id' => $id,
                'user' => $this->user
            ]), 'json', $this->context);
        });

        // Return OK response with the customer file
        return new JsonResponse($customer, 200, [], true);
    }
}