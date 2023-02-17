<?php

namespace App\Controller;

use App\Entity\User;
use App\Form\SearchUserType;
use App\Repository\UserRepository;
use App\Controller\HeaderController;
use App\Repository\ProductRepository;
use Knp\Component\Pager\PaginatorInterface;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Security;
use Symfony\Component\HttpFoundation\Session\SessionInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\Security\Core\Exception\AccessDeniedException;
use Symfony\Component\Security\Core\Authentication\Token\Storage\TokenStorageInterface;

class UserController extends AbstractController
{
    #[Security("is_granted('ROLE_ADMIN')")]
    #[Route('/admin.user', name: 'app.admin.user', methods: ['GET', 'POST'])]
    public function index(
        // User $choosenUser,
        Request $request,
        TokenStorageInterface $tokenStorage,
        SessionInterface $session,
        UserRepository $userRepository,
        PaginatorInterface $paginator
        ): Response
    {
        $data = $session->get('shared_data');
        $nbProductInCart = $data['nbProductInCart'];
        $categories = $data['categories'];

        // if user connected is not admin, redirect to home
        if (!$this->isGranted('ROLE_ADMIN')) {
            throw new AccessDeniedException();
        }

        // $choosenUser = $tokenStorage->getToken()->getUser();
        $form = $this->createForm(SearchUserType::class);
        $form->handleRequest($request);

        // get list of users with UserRepository
        $users = $userRepository->findusersWithSearch($form['search']->getData());
        // the insert the system paginator with users
        $users = $paginator->paginate(
            $users,
            $request->query->getInt('page', 1),
            3 /*limit per page*/
        );

        return $this->render('user/admin.html.twig', [
            'form' => $form->createView(),
            'nbProductInCart' => $nbProductInCart,
            'categories' => $categories,
            "users" => $users,
            
        ]);
    }
}
