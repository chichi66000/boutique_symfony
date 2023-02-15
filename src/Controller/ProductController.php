<?php

namespace App\Controller;

use App\Entity\Size;
use App\Entity\Color;
use App\Entity\Product;
use App\Entity\Category;
use App\Repository\ProductRepository;
use App\Repository\CategoryRepository;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\HttpFoundation\Session\SessionInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;

class ProductController extends AbstractController
{

    #[Route('/', name: 'app.home', methods: 'GET')]
    public function index(CategoryRepository $categoryRepository, 
    ProductRepository $productRepository,
    SessionInterface $session
    ): Response
    {
        // get data of cart (if exist)
        $nbProductInCart = 0;
        $cart = $session->get('cart');

        if ($cart) {
            foreach ($cart as $item) {
                $nbProductInCart += $item['qty_prod'];
            }
        }
        // get all categories
        $categories = $categoryRepository->findAll();
        // save in data then save into session. We can share this information for navbar & header in others Controllers
        $data = ['categories' => $categories, 'nbProductInCart' => $nbProductInCart];
        $session->set('shared_data', $data);

        // the recent products in trend
        $newsProducts = $productRepository->findNews();
        
        // add some empty array
        $pathToPhoto = 'photo/';
        $newProductsPathToPhoto = [];
        $newProductsColors = [];
        $newProductsSizes = [];
        $srcPhoto = [];
       
        // for each newsProduct, we will take the array of the colors, the sizes available
        foreach($newsProducts as $key => $newProduct) {
            // get all color of one newProduct with his reference
            $colorsTab = $productRepository->findDistinctColorsByReference($newProduct->getReference());
            // then add $colorTab into $newProductsColors with id of this product
            $newProductsColors [$newProduct->getId()] = $colorsTab;

            // get distinct sizes of a product
            $sizeTab = $productRepository->findDistinctSizesByReference($newProduct -> getReference());
            $newProductsSizes [$newProduct->getId()] = $sizeTab;

            // path to product photo1
            $category = $newProduct->getCategory()->getName();
            $photo1 = $newProduct->getPhoto1();
            $path =  $pathToPhoto . $category . '/' . $photo1;
            $srcPhoto[$newProduct->getId()] = $path;
        }
        

        return $this->render('product/index.html.twig', [
            'newsProducts' => $newsProducts,
            'newProductsSizes' => $newProductsSizes,
            'newProductsColors' => $newProductsColors,
            'nbProductInCart' => $nbProductInCart,
            'categories' => $categories,
            'srcPhoto' => $srcPhoto
        ]);
    }
}
