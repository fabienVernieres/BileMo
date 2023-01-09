<?php

namespace App\Controller;

use App\Entity\User;
use App\Form\RegistrationFormType;
use App\Repository\UserRepository;
use App\Security\AppCustomAuthenticator;
use App\Service\JWTService;
use App\Service\SendMailService;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\PasswordHasher\Hasher\UserPasswordHasherInterface;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\Security\Http\Authentication\UserAuthenticatorInterface;

#[Route('/register', name: 'app_register_')]
class RegistrationController extends AbstractController
{
    #[Route('/', name: 'index')]
    public function register(Request $request, UserPasswordHasherInterface $userPasswordHasher, UserAuthenticatorInterface $userAuthenticator, AppCustomAuthenticator $authenticator, EntityManagerInterface $entityManager, SendMailService $mail, JWTService $jwt): Response
    {
        $user = new User();
        $form = $this->createForm(RegistrationFormType::class, $user);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            // encode the plain password
            $user->setPassword(
                $userPasswordHasher->hashPassword(
                    $user,
                    $form->get('plainPassword')->getData()
                )
            );

            $entityManager->persist($user);
            $entityManager->flush();

            // do anything else you need here, like send an email

            // the header
            $header = [
                'typ' => 'JWT',
                'alg' => 'HS256'
            ];

            // the payload
            $payload = [
                'user_id' => $user->getId()
            ];

            // the token
            $token = $jwt->generate($header, $payload, $this->getParameter(('app.jwtsecret')));

            // send mail
            $mail->send(
                'contact@bilemo.fabienvernieres.com',
                $user->getEmail(),
                'Activate your account',
                'register',
                compact('user', 'token')
            );

            return $userAuthenticator->authenticateUser(
                $user,
                $authenticator,
                $request
            );
        }

        return $this->render('registration/register.html.twig', [
            'registrationForm' => $form->createView(),
        ]);
    }

    #[Route('/verify/{token}', name: 'verify')]
    public function verifyUser($token, JWTService $jwt, UserRepository $userRepository, EntityManagerInterface $em): Response
    {
        // check token not modified, not expired
        if ($jwt->isValid($token) && !$jwt->isExpired($token) && $jwt->check($token, $this->getParameter('app.jwtsecret'))) {
            // get the payload
            $payload = $jwt->getPayload($token);

            // get user token
            $user = $userRepository->find($payload['user_id']);

            // check if user exist and is not verified
            if ($user && !$user->getIs_verified()) {
                $user->setIs_verified(true);
                $user->setRoles(['ROLE_USER_API']);
                $em->flush($user);

                $this->addFlash('success', 'User verified');
                return $this->redirectToRoute('app_user');
            }
        }

        // token issue
        $this->addFlash('danger', 'Invalid token or expired');
        return $this->redirectToRoute('app_login');
    }

    #[Route('/resend-verif', name: 'resend_verif')]
    public function resendVerif(JWTService $jwt, SendMailService $mail, UserRepository $userRepository): Response
    {
        $user = $this->getUser();

        if (!$user) {
            $this->addFlash('danger', 'Please log in to access this page');
            return $this->redirectToRoute('app_login');
        }

        if ($user->getIs_verified()) {
            $this->addFlash('warning', 'This user is already verified');
            return $this->redirectToRoute('app_login');
        }

        // the header
        $header = [
            'typ' => 'JWT',
            'alg' => 'HS256'
        ];

        // the payload
        $payload = [
            'user_id' => $user->getId()
        ];

        // the token
        $token = $jwt->generate($header, $payload, $this->getParameter(('app.jwtsecret')));

        // send mail
        $mail->send(
            'contact@bilemo.fabienvernieres.com',
            $user->getEmail(),
            'Activate your account',
            'register',
            compact('user', 'token')
        );

        $this->addFlash('success', 'Email sent');
        return $this->redirectToRoute('app_user');
    }
}