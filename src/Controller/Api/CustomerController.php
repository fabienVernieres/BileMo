<?php

namespace App\Controller\Api;

use DateTime;
use App\Entity\User;
use App\Entity\Customer;
use App\Service\ApiService;
use JMS\Serializer\SerializerInterface;
use JMS\Serializer\SerializationContext;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Contracts\Cache\CacheInterface;

#[Route('/api', name: 'api_')]
class CustomerController extends AbstractController
{
    private User $user;
    private CacheInterface $cache;
    private mixed $context;
    private SerializerInterface $serializer;

    public function __construct(
        ApiService $apiService,
        CacheInterface $cache,
        SerializerInterface $serializer
    ) {        // The current user
        $this->user = $apiService->apiUser();

        // The cache
        $this->cache = $cache;

        $this->context = SerializationContext::create()->setGroups(['users']);
        $this->serializer = $serializer;
    }

    #[Route('/v1/customers', name: 'customer_new', methods: ['POST'])]
    /**
     * Add User to API user (client)
     *
     * @param  EntityManagerInterface $entityManager
     * @return JsonResponse
     */
    public function new(EntityManagerInterface $entityManager): JsonResponse
    {
        // Create a new user object
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


    #[Route('/v1/customers/{id}', name: 'customer_delete', methods: ['DELETE'])]
    /**
     * Delete a user from API user (client)
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
}