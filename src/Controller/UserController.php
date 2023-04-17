<?php

namespace App\Controller;

use App\Entity\User;
use App\Form\DeleteUserType;
use App\Form\ProfilUserType;
use App\Form\SearchUserType;
use Doctrine\ORM\EntityManager;
use App\Form\ModifyPasswordType;
use App\Repository\UserRepository;
use App\Controller\HeaderController;
use App\Repository\ProductRepository;
use Doctrine\ORM\EntityManagerInterface;
use Knp\Component\Pager\PaginatorInterface;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\Security\Core\User\UserInterface;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Security;
use Symfony\Component\HttpFoundation\Session\SessionInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\Security\Core\Exception\AccessDeniedException;
use Symfony\Component\HttpKernel\Exception\AccessDeniedHttpException;
use Symfony\Component\Security\Http\Authentication\AuthenticationUtils;
use Symfony\Component\PasswordHasher\Hasher\UserPasswordHasherInterface;
use Symfony\Component\Security\Core\Authentication\Token\Storage\TokenStorageInterface;

class UserController extends AbstractController
{
    
    #[Security("is_granted('ROLE_ADMIN')")]
    #[Route('/admin.user', name: 'app.admin.user', methods: ['GET', 'POST'])]
    /**
     * function to searc/get list of user, reserve for admin only
     */
    public function index(
        // User $choosenUser,
        Request $request,
        TokenStorageInterface $tokenStorage,
        SessionInterface $session,
        UserRepository $userRepository,
        PaginatorInterface $paginator
        ): Response
    {
        
        $nbProductInCart = $session->get('nbProductInCart');
        $categories = $session->get('categories');
        
        // if user connected is not admin, redirect to home
        if (!$this->isGranted('ROLE_ADMIN')) {
            throw new AccessDeniedException();
        }

        $form = $this->createForm(SearchUserType::class);
        $form->handleRequest($request);
        $userSearch = $form['search']->getData();
        
        // get list of users with UserRepository
        $users = $userRepository->findusersWithSearch($form['search']->getData());
        // the insert the system paginator with users
        $users = $paginator->paginate(
            $users,
            $request->query->getInt('page', 1),
            2 /*limit 2 per page*/
        );

        return $this->render('user/admin.html.twig', [
            'form' => $form->createView(),
            'nbProductInCart' => $nbProductInCart,
            'categories' => $categories,
            "users" => $users,
            "userSearch" => $userSearch
        ]);
    }

    
    /**
     * Function to modify user's profil
     *
     * @param SessionInterface $session
     * @param Request $request
     * @param EntityManagerInterface $manager
     * @param User $user
     * @return Response
     */
    #[Security("is_granted('ROLE_ADMIN')||is_granted('ROLE_USER')")]
    #[Route('/profil/{id}',  name:'app.profil', methods: ['GET', 'POST'])]
    public function profilUser (
        SessionInterface $session,
        Request $request,
        EntityManagerInterface $manager,
        User $user
        
    ) :Response 
    {
        $nbProductInCart = $session->get('nbProductInCart');
        $categories = $session->get('categories');

        // if user isn't has role_user or admin, refuse access
        if (!$this->isGranted('ROLE_USER') && !$this->isGranted('ROLE_ADMIN')) {
            throw new AccessDeniedException('Accès refusé.');
        }
        else {
            // dd($this->getUser()->getRoles()['0']);
            // if userId ne correspond with user connected => error
            if ($this->getUser() != $user ) {
                if ($this->getUser()->getRoles()['0'] != "ROLE_ADMIN") {
                    return $this->redirectToRoute('app.home');
                }
                else {
                    $form = $this->createForm(ProfilUserType::class, $user);
                    $form->handleRequest($request);
                    
                    if ($form->isSubmitted() && $form->isValid() ) {
                        $user = $form->getData();
                        $manager->persist($user);
                        $manager->flush();
                        $this->addFlash('success', 'Profil a été modifié');
                        return $this->redirectToRoute('app.home');
                    }
                }
            }
            else {
                
                $form = $this->createForm(ProfilUserType::class, $user);
                $form->handleRequest($request);
                
                if ($form->isSubmitted() && $form->isValid() ) {
                    $user = $form->getData();
                    $manager->persist($user);
                    $manager->flush();
                    $this->addFlash('success', 'Profil a été modifié');
                    return $this->redirectToRoute('app.home');
                }
            }
            return $this->render('user/profil.html.twig', [
                'form' => $form->createView(),
                'nbProductInCart' => $nbProductInCart,
                "categories" => $categories
            ]);
            
        }
        
    }

    
    /**
     * Function to modify user's password
     *
     * @param SessionInterface $session
     * @param User $choosenUser
     * @param EntityManagerInterface $manager
     * @param Request $request
     * @param UserPasswordHasherInterface $hasher
     * @return Response
     */
    #[Security("is_granted('ROLE_USER')||is_granted('ROLE_ADMIN') and user === choosenUser")]
    #[Route('/reset_password/{id}', name:'app.password', methods: ['GET', 'POST'])]
    public function modifyPassword (
        SessionInterface $session,
        User $choosenUser,
        EntityManagerInterface $manager,
        Request $request,
        UserPasswordHasherInterface $hasher
    ) : Response 
    {
        $nbProductInCart = $session->get('nbProductInCart');
        $categories = $session->get('categories');
        
        // if user isn't has role_user or admin, refuse access
        if (!$this->isGranted('ROLE_USER') && !$this->isGranted('ROLE_ADMIN')) {
            throw new AccessDeniedException('Accès refusé.');
        }
        // if user connected is not the choosenUser (with bad id in URL for example)
        if ($this->getUser() !== $choosenUser) {
            throw new AccessDeniedException('Accès refusé.');
            $this->addFlash('error', 'Accès refusé.');
            return $this->redirectToRoute('app.home');
        }
            $form = $this->createForm(ModifyPasswordType::class);
            $form->handleRequest($request);
            if ($form->isSubmitted() && $form->isValid()) {
                $oldPassword = $form->getData()['oldPassword'];
                // check password with hash password
                if ($hasher->isPasswordValid($choosenUser, $oldPassword)) {
                    $choosenUser->setPassword($hasher->hashPassword($choosenUser, $form->getData()['newPassword']));
                    // save in database
                    $manager->persist($choosenUser);
                    $manager->flush();
                    $this->addFlash('success', 'Le mot de passe a été modifié');
                    return $this->redirectToRoute('app.home');
                }

                // password incorrect
                else {
                    $this->addFlash('error', 'Le mot de passe incorrect');
                }

            }
            
            return $this->render('user/password.html.twig', compact('nbProductInCart', 'categories', 'form'));
        
    }

    #[Security("is_granted('ROLE_USER')||is_granted('ROLE_USER')")]
    #[Route('/delete/user/{id}',  name:'app.delete.user', methods: ['GET', 'POST'])]
    public function deleteUser (
        SessionInterface $session,
        User $choosenUser,
        EntityManagerInterface $manager,
        Request $request,
        AuthenticationUtils $authenticationUtils,
        UserPasswordHasherInterface $hasher,
        UserRepository $userRepository,
        
    ) :Response
    {
        $nbProductInCart = $session->get('nbProductInCart');
        $categories = $session->get('categories');
        $message = "";
        // if user isn't has role_user or admin, refuse access
        if (!$this->isGranted('ROLE_USER') && !$this->isGranted('ROLE_ADMIN')) {
            throw new AccessDeniedException('Accès refusé.');
        }
        // if user connected is not the choosenUser (with bad id in URL for example)
        if ($this->getUser() !== $choosenUser) {
            // if it's not admin
            if ($this->getUser()->getRoles()['0'] != "ROLE_ADMIN") {
                throw new AccessDeniedException('Accès refusé.');
                $this->addFlash('error', 'Accès refusé.');
                return $this->redirectToRoute('app.home');
            }
            // it's admin 
            else {

            }
        }
        // user is the right one
        else {
            $form = $this->createForm(DeleteUserType::class);
            $form->handleRequest($request);
            if ($form->isSubmitted() && $form->isValid()) {
                // get the login error if there is one
                $error = $authenticationUtils->getLastAuthenticationError();
                // last username entered by the user
                $lastUsername = $authenticationUtils->getLastUsername();
                $email = $form->getData()['email'];
                $password = $form->getData()['password'];
                // email rentée n'est pas user connecté
                if ($email !== $lastUsername) {
                    // $this->addFlash("error","Ceci n'est pas votre compte");
                    $message = "Ceci n'est pas votre compte";
                }
                else {
                    // check password with hash password
                    if ($hasher->isPasswordValid($choosenUser, $password)) {
                        $value = "DELETED";
                        // update inof deleted to user
                        $choosenUser->setFirstName($value);
                        $choosenUser->setLastName($value);
                        $choosenUser->setFirstName($value);
                        $choosenUser->setPassword($value);
                        $choosenUser->setEmail($value.rand());
                        $choosenUser->setPc($value);
                        $choosenUser->setCity($value);
                        $choosenUser->setAddress($value);
                        $choosenUser->setTel($value);
                        $choosenUser->setRoles(["ROLE_DELETED"]);
                        $manager->flush();
                        
                        $this->addFlash('success', 'Votre compte a été supprimé');
                        return $this->redirectToRoute('app.home');
                    }
                    else {
                        $message = "Invalid credential";
                    }
                }
            }  
        }

        return $this->render('user/delete.user.html.twig', [
            'nbProductInCart' => $nbProductInCart,
            'categories' => $categories,
            'form' => $form->createView(),
            'message' => $message
        ]);
    }

    // public function adminClient () : Response 
    // {
    //     return $this->render('user/admin.html.twig', []);
    // }
}
