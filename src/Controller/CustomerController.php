<?php

namespace App\Controller;

use DateTime;
use App\Entity\User;
use App\Entity\Customer;
use App\Repository\UserRepository;
use App\Repository\CustomerRepository;
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
class CustomerController extends AbstractController
{
    private JWTTokenManagerInterface $jwtManager;
    private TokenStorageInterface $tokenStorageInterface;
    private $decodedJwtToken;
    private User $user;
    private CacheInterface $cache;

    public function __construct(TokenStorageInterface $tokenStorageInterface, JWTTokenManagerInterface $jwtManager, UserRepository $userRepository, CacheInterface $cache)
    {
        $this->jwtManager = $jwtManager;
        $this->tokenStorageInterface = $tokenStorageInterface;
        $this->decodedJwtToken = $this->jwtManager->decode($this->tokenStorageInterface->getToken());

        $this->user = $userRepository->findOneBy([
            'email' => $this->decodedJwtToken['email']
        ]);

        $this->cache = $cache;
    }

    #[Route('/v1/customer', name: 'customer_new', methods: ['POST'])]
    /**
     * Add customer
     *
     * @param  EntityManagerInterface $entityManager
     * @return JsonResponse
     */
    public function new(EntityManagerInterface $entityManager): JsonResponse
    {
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

        // delete the cache
        $this->cache->delete('customers_' . $this->user->getId());

        return $this->json($customer, 201, [], ['groups' => 'customers']);
    }


    #[Route('/v1/customer/{id}', name: 'customer_delete', methods: ['DELETE'])]
    /**
     * delete customer
     *
     * @param  Customer $id
     * @param  EntityManagerInterface $entityManager
     * @param  CustomerRepository $customerRepository
     * @return JsonResponse
     */
    public function delete(Customer $id, EntityManagerInterface $entityManager, CustomerRepository $customerRepository): JsonResponse
    {
        if ($id->getUser() === $this->user) {
            $entityManager->remove($id);
            $entityManager->flush();
        }

        // delete the cache
        $this->cache->delete('customer_' . $id->getId());
        $this->cache->delete('customers_' . $this->user->getId());

        return $this->json($customerRepository->findOneBy([
            'id' => $id,
            'user' => $this->user
        ]), 204, [], ['groups' => 'customers']);
    }


    #[Route('/v1/customers', name: 'customers', methods: ['GET'])]
    /**
     * getAll customers
     *
     * @param  CustomerRepository $customerRepository
     * @return JsonResponse
     */
    public function getAll(CustomerRepository $customerRepository): JsonResponse
    {
        // cache the request
        $customers = $this->cache->get('customers_' . $this->user->getId(), function (ItemInterface $item) use ($customerRepository) {
            $item->expiresAfter(3600);
            return $customerRepository->findBy(['user' => $this->user]);
        });

        return $this->json($customers, 200, [], ['groups' => 'customers']);
    }

    #[Route('/v1/customer/{id}', name: 'customer', methods: ['GET'])]
    /**
     * getOne customer by id
     *
     * @param  Customer $id
     * @param  CustomerRepository $customerRepository
     * @return JsonResponse
     */
    public function getOne(Customer $id, CustomerRepository $customerRepository): JsonResponse
    {
        // cache the request
        $customer = $this->cache->get('customer_' . $id->getId(), function (ItemInterface $item) use ($id, $customerRepository) {
            $item->expiresAfter(3600);
            return $customerRepository->findOneBy([
                'id' => $id,
                'user' => $this->user
            ]);
        });

        return $this->json($customer, 200, [], ['groups' => 'customers']);
    }
}