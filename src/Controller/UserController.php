<?php

namespace App\Controller;

use App\Entity\User;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Security;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\Security\Core\Exception\AccessDeniedException;
use Symfony\Component\Security\Core\Authentication\Token\Storage\TokenStorageInterface;

class UserController extends AbstractController
{
    #[Security("is_granted('ROLE_ADMIN')")]
    #[Route('/admin.user', name: 'app.admin.user', methods: ['GET', 'POST'])]
    public function index(
        // User $choosenUser,
        // Request $request,
        TokenStorageInterface $tokenStorage
        ): Response
    {
        if (!$this->isGranted('ROLE_ADMIN')) {
            throw new AccessDeniedException();
        }
        $choosenUser = $tokenStorage->getToken()->getUser();
        dd($choosenUser);
        return $this->render('user/admin.html.twig', [
            'controller_name' => 'UserController',
        ]);
    }
}
