<?php

namespace App\Controller;

// use App\Entity\User;
// use App\Entity\Order;

use App\Entity\Order;
use App\Entity\Product;
// use Doctrine\ORM\EntityManager;
use App\Entity\OrderItem;
use App\Factory\OrderFactory;
use App\Repository\ProductRepository;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\HttpFoundation\Session\SessionInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpKernel\Exception\NotFoundHttpException;
use Symfony\Component\HttpFoundation\Session\Storage\SessionStorageInterface;

class CartController extends AbstractController
{
    /**
     * function to show the cart
     *
     * @param SessionInterface $session
     * @param ProductRepository $productRepository
     * @return Response
     */
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

    
    /**
     * function add product to cart with id
     *
     * @param Product|null $product
     * @param SessionInterface $session
     * @param Request $request
     * @return Response
     */
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
     * function to remove product from cart with id
     *
     * @param Product|null $product
     * @param SessionInterface $session
     * @param Request $request
     * @return Response
     */
    #[Route('/remove/{id}', name: 'app.cart.remove', methods: ['GET', 'POST'])]
    public function removeCart(
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
            
            // if there is the product in cart we will -1 into cart 
            if (!empty($cart[$id])) {
                // if product is more than 1
                if ($cart[$id] > 1) {
                    $cart[$id]--;
                }
                else {
                    // we will delete this product
                    unset($cart[$id]);
                }
            }
            // then we will save he cart into session
            $session->set('cart', $cart);
            // then add +1 into nbProductInCart
            $nbProductInCart--;
            $session->set("nbProductInCart", $nbProductInCart);

            // stay in the same page;
            $referer = $request->headers->get('referer');
            return $this->redirect($referer);
        }
        
    }

    #[Route('/order', name: 'app.cart.order', methods:['GET', 'POST'])]
    public function command (
        SessionInterface $session, 
        Request $request,
        EntityManagerInterface $manager,
        ProductRepository $productRepository,
        // Product $products = null
        ) :Response
    {
        $nbProductInCart = $session->get('nbProductInCart');
        $categories = $session->get('categories');

        $cart = $session->get('cart');
        // if there is no products => no order => return to cart
        if (empty($cart)) {
            return $this->redirectToRoute('app.cart');
        }
        else {
            // if user is connected
            if ($this->getUser()) {
                // dd($products);
                $user = $this->getUser();
                
                // then save products & user into database order
                $order = new Order($user);
                $manager->persist($order);
                $manager->flush();
                $order_ref_id = $order->getId();

                // then save the products into database orderItem
            
                foreach ($cart as $id => $quantity) {
                    $orderItem = new OrderItem();
                    
                    $product = $productRepository->find($id);
                    $orderItem->setProduct($product);
                    $orderItem->setQuantity($quantity);
                    $orderItem->setOrderRef($order);
                    // $order->addItem($orderItem);
                    $manager->persist($orderItem);
                    $manager->flush();
                }
                
                // reset cart & nbProductInCart
                $session->remove('cart');
                $session->set('nbProductInCart', 0);

                return $this->render('cart/order.html.twig', compact('nbProductInCart', 'categories'));
                // $userId = $user->getId();
                // $order = new OrderFactory();
                // $carts = $order->create($user);
                // $manager->persist($carts);

                
                
                // $cart = new Order();
                // $cart
                // dd($manager->persist($cart));

                // $manager->flush();
            }
            // user not conneced => ask for login
            else {
                return $this->redirectToRoute("app.login");
            }

        }
        

    }

    /**
     * Function to delete all items in cart
     *
     * @param SessionInterface $session
     * @return void
     */
    #[Route('/delete', name:"app.cart.delete")]
    public function deleteAll(SessionInterface $session)
    {
        $session->remove("cart");
        $session->set('nbProductInCart', 0);
        return $this->redirectToRoute("app.cart");
    }

}
