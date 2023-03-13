<?php

namespace App\Controller;

use App\Entity\Product;
use App\Repository\ProductRepository;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\HttpFoundation\Session\SessionInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpKernel\Exception\NotFoundHttpException;
use Symfony\Component\HttpFoundation\Session\Storage\SessionStorageInterface;

class CartController extends AbstractController
{
    #[Route('/cart', name: 'app.cart', methods: ['GET', 'POST'])]
    public function index(
        SessionInterface $session, 
        ProductRepository $productRepository
        ): Response
    {
        $cart = $session->get('cart', []);
        // we add the information
        $dataCart = [];
        $total = 0;

        // 
        foreach ($cart as $id => $quantity) {
            $product = $productRepository->find('id');
            $dataCart[] = [
                "product" => $product,
                "quantity" => $quantity
            ];
            $total = $product->getPrice() * $quantity;
        }
        return $this->render('cart/index.html.twig', [
            'controller_name' => 'CartController',
        ]);
    }

    #[Route('/add/{id}', name: 'app.cart.add', methods: ['GET', 'POST'])]
    public function addCart(
        Product $product=null, 
        SessionInterface $session
        ): Response
    {
        // if there is no product => redirect to home
        if ($product === null) {
            return $this->redirectToRoute('app.home');
            
        }
        else {
            // dd($product);
            $id = $product->getId();
            // dd($id);
            // We will take the cart in the session
            $cart = $session->get('cart',[]);
            
            // if there is he product in cart we will +1 into cart 
            if (!empty($cart[$id])) {
                $cart[$id]++;
                
            }
            else {
                // the product isn't in cart, we will add this product
                $cart[$id] = 1;
                
            }
            // dd($cart);
            // then we will save he cart into session
            $session->set('cart', $cart);
            dd($session);
            return $this->render('cart/index.html.twig', [
                'controller_name' => 'CartController',
        ]);
        }
        
    }

    #[Route('/delete', name:"cart.delete")]
    public function deleteAll(SessionInterface $session)
    {
        $session->remove("cart");

        return $this->redirectToRoute("app.cart");
    }

}
