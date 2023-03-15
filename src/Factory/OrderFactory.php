<?php

namespace App\Factory;

use App\Entity\User;
use App\Entity\Order;
use App\Entity\Product;
use App\Entity\OrderItem;

class OrderFactory {
    
    /**
     * Creates an order.
     *
     * @return Order
     */
    public function create (User $user) :Order
    {
        $order = new Order();
        $order
            ->setUser($user)
            ->setStatus(Order::STATUS_CART)
            ->setCreatedAt(new \DateTimeImmutable())
            ->setUpdatedAt(new \DateTimeImmutable());

        return $order;
    }

      /**
     * Creates an item for a product.
     *
     * @param Product $product
     *
     * @return OrderItem
     */
    public function createItem(Product $product): OrderItem 
    {
        $item = new OrderItem();
        $item->setProduct($product);
        $item->setQuantity(1);
        $item->setOrderRef(1);
        return $item;
    }
}