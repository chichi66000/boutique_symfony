<?php
class HomeController extends AbstractController
{
    public function getProductCount(SessionInterface $session)
    {
        $nbProductInCart = 0;
        $cart = $session->get('cart');

        if ($cart) {
            foreach ($cart as $item) {
                $nbProductInCart += $item['qty_prod'];
            }
        }
    }
}