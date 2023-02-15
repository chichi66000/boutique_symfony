<?php

namespace App\Controller;

use App\Entity\User;
use App\Form\SearchUserType;
use App\Controller\HeaderController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\DependencyInjection\ContainerInterface;
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
        Request $request,
        TokenStorageInterface $tokenStorage, 
        ContainerInterface $container
        ): Response
    {
        $data = $container->get('shared_data');
        dd($data);
        if (!$this->isGranted('ROLE_ADMIN')) {
            throw new AccessDeniedException();
        }
        $choosenUser = $tokenStorage->getToken()->getUser();
        $form = $this->createForm(SearchUserType::class);
        $form->handleRequest($request);
        
        if ($form->isSubmitted() && $form->isValid()) {
            
            // dd($form->getData());
        }
        return $this->render('user/admin.html.twig', [
            'form' => $form->createView(),
        ]);
    }
}
