<?php

namespace App\Controller;

use App\Repository\CategoryRepository;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\DependencyInjection\ContainerInterface;
use Symfony\Component\HttpFoundation\Session\SessionInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;

class HeaderController extends AbstractController
{
    #[Route('/header', name: 'app_header')]
    public function index(
        CategoryRepository $categoryRepository, 
        SessionInterface $session): Response
    {
        $nbProductInCart = 0;
        $cart = $session->get('cart');

        if ($cart) {
            foreach ($cart as $item) {
                $nbProductInCart += $item['qty_prod'];
            }
        }

        $categories = $categoryRepository->findAll();
        $categoryNames = [];
        // dd($categories[0]->getName());
        foreach ($categories as $category) {
            $categoryNames [] = $category->getName();
        }
        
        $data = ['categoryNames' => $categoryNames, 'nbProductInCart' => $nbProductInCart];
        // $container->set('shared_data', $data);
        $session->set('shared_data', $data);

        return $this->render('header/index.html.twig', [
            'shared_data' => $data
        ]);
    }
}
