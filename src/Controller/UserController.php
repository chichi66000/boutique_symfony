<?php

namespace App\Controller;

use App\Entity\User;
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
        // $data = $session->get('shared_data');
        // $nbProductInCart = $data['nbProductInCart'];
        // $categories = $data['categories'];
        $nbProductInCart = $session->get('nbProductInCart');
        $categories = $session->get('categories');
        
        // if user connected is not admin, redirect to home
        if (!$this->isGranted('ROLE_ADMIN')) {
            throw new AccessDeniedException();
        }

        // $choosenUser = $tokenStorage->getToken()->getUser();
        $form = $this->createForm(SearchUserType::class);
        $form->handleRequest($request);
        $userSearch = $form['search']->getData();
        // dd($userSearch);
        // get list of users with UserRepository
        $users = $userRepository->findusersWithSearch($form['search']->getData());
        // the insert the system paginator with users
        $users = $paginator->paginate(
            $users,
            $request->query->getInt('page', 1),
            2 /*limit per page*/
        );

        return $this->render('user/admin.html.twig', [
            'form' => $form->createView(),
            'nbProductInCart' => $nbProductInCart,
            'categories' => $categories,
            "users" => $users,
            "userSearch" => $userSearch
        ]);
    }

    // #[Security("is_granted('ROLE_USER')")]
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
            // if userId ne correspond with user connected => error
            if ($this->getUser() != $user) {
                return $this->redirectToRoute('app.home');
            }
            else {
                
                $form = $this->createForm(ProfilUserType::class, $user);
                $form->handleRequest($request);
                
                if ($form->isSubmitted() && $form->isValid() ) {
                    $user = $form->getData();
                    $manager->persist($user);
                    $manager->flush();
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

    #[Route('/modify_pass', name:'app.password', methods: ['GET', 'POST'])]
    public function modifyPassword (
        SessionInterface $session
    ) : Response 
    {
        $nbProductInCart = $session->get('nbProductInCart');
        $categories = $session->get('categories');
        $formPaswword = $this->createForm(ModifyPasswordType::class);

        return $this->render('user/password.html.twig', compact('nbProductInCart', 'categories', 'formPaswword'));
    }
}
