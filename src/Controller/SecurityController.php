<?php

namespace App\Controller;

use App\Entity\User;
use App\Form\RegistrationFormType;
use App\Security\AppAuthenticator;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\HttpFoundation\Session\SessionInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\Security\Core\Exception\AccessDeniedException;
use Symfony\Component\Security\Http\Authentication\AuthenticationUtils;
use Symfony\Component\PasswordHasher\Hasher\UserPasswordHasherInterface;
use Symfony\Component\Security\Http\Authentication\UserAuthenticatorInterface;


class SecurityController extends AbstractController
{
    #[Route('/register', name: 'app.register', methods: ['GET', 'POST'])]
    public function register(
        Request $request, 
        UserPasswordHasherInterface $userPasswordHasher, 
        UserAuthenticatorInterface $userAuthenticator, 
        AppAuthenticator $authenticator, 
        EntityManagerInterface $entityManager,
        SessionInterface $session
        ) : Response
    {
        // get data from session into navbar

        // $data = $session->get('shared_data');
        // $nbProductInCart = $data['nbProductInCart'];
        // $categories = $data['categories'];
        $nbProductInCart = $session->get('nbProductInCart');
        $categories = $session->get('categories');
        
        $user = new User();
        // give the role for user
        $user->setRoles(['ROLE_USER']);


        $form = $this->createForm(RegistrationFormType::class, $user);
        $form->handleRequest($request);
        if ($form->isSubmitted() && $form->isValid()) {
            
            // encode the plain password in user
            $user->setPassword(
                $userPasswordHasher->hashPassword(
                    $user,
                    $form->get('plainPassword')->getData()
                )
            );

            $entityManager->persist($user);
            $entityManager->flush();
            // do anything else you need here, like send an email
            // give message 
            $this->addFlash(
                'success',
                'Succès, votre compte a été crée.'
            );
            // return $this->redirectToRoute('app.home');

            return $userAuthenticator->authenticateUser(
                $user,
                $authenticator,
                $request
            );
        }

        return $this->render('security/register.html.twig', [
            'registrationForm' => $form->createView(),
            'nbProductInCart' => $nbProductInCart,
            'categories' => $categories
        ]);
    }
    
    // Login
    #[Route(path: '/login', name: 'app.login')]
    public function login(AuthenticationUtils $authenticationUtils, SessionInterface $session): Response
    {
        // if user is already login, show the actions he can do 
        if ($this->getUser()) {
            $userRole = $this->getUser()->getRoles()[0];
            // dd($userRole);
            $userId = $this->getUser()->getId();
            $categories = $session->get('categories');
            $nbProductInCart = $session->get('nbProductInCart');
            return $this->render('security/account.html.twig', compact("categories", "nbProductInCart", "userRole", "userId"));
            
        }

        // get the login error if there is one
        $error = $authenticationUtils->getLastAuthenticationError();
        // last username entered by the user
        $lastUsername = $authenticationUtils->getLastUsername();

        return $this->render('security/login.html.twig', ['last_username' => $lastUsername, 'error' => $error]);
    }

    #[Route(path: '/logout', name: 'app.logout')]
    public function logout(): void
    {
        throw new \LogicException('This method can be blank - it will be intercepted by the logout key on your firewall.');
    }
}
