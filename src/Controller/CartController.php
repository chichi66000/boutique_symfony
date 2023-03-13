<?php

namespace App\Controller;

use App\Entity\Product;
use App\Repository\ProductRepository;
use Symfony\Component\HttpFoundation\Request;
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
        $categories = $session->get('categories');
        // we add the information
        $dataCart = [];
        $total = 0;
        $nbProductInCart = $session->get('nbProductInCart');
        $srcPhoto = "";
        // find all the products in cart with id; then add into $dataCart
        
        foreach ($cart as $id => $quantity) {
            $product = $productRepository->find($id);
            // dd($product);
            $category = $product->getCategory()->getName();
            $dataCart[] = [
                "product" => $product,
                "quantity" => $quantity,
                "srcPhoto" => 'photo/' . $category . "/" . $product->getPhoto1()
            ];
            $total += $product->getPrice() * $quantity;
            
        }
        return $this->render('cart/index.html.twig', [
            "dataCart" => $dataCart,
            "total" => $total,
            "nbProductInCart" => $nbProductInCart,
            "categories" => $categories
        ]);
    }

    #[Route('/add/{id}', name: 'app.cart.add', methods: ['GET', 'POST'])]
    public function addCart(
        Product $product=null, 
        SessionInterface $session,
        Request $request
        ): Response
    {
        // if there is no product => redirect to home
        if ($product === null) {
            return $this->redirectToRoute('app.home');
            
        }
        else {
            $id = $product->getId();
            // We will take the cart in the session
            $cart = $session->get('cart',[]);
            $nbProductInCart = $session->get('nbProductInCart');
            
            // if there is he product in cart we will +1 into cart 
            if (!empty($cart[$id])) {
                $cart[$id]++;
            }
            else {
                // the product isn't in cart, we will add this product
                $cart[$id] = 1;
            }
            
            // then we will save he cart into session
            $session->set('cart', $cart);
            // then add +1 into nbProductInCart
            $nbProductInCart++;
            $session->set("nbProductInCart", $nbProductInCart);

            // stay in the same page;
            $referer = $request->headers->get('referer');
            return $this->redirect($referer);
        }
        
    }

    /**
     * Function to delete all items in cart
     *
     * @param SessionInterface $session
     * @return void
     */
    #[Route('/delete', name:"cart.delete")]
    public function deleteAll(SessionInterface $session)
    {
        $session->remove("cart");
        return $this->redirectToRoute("app.cart");
    }

}
