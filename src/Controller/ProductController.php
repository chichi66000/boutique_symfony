<?php

namespace App\Controller;

use App\Entity\Size;
use App\Entity\Color;
use App\Entity\Product;
use App\Entity\Category;
use App\Repository\ProductRepository;
use App\Repository\CategoryRepository;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\HttpFoundation\Session\SessionInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;

class ProductController extends AbstractController
{
    /**
     * Route to index, save the info of header & navbar into session
     * Get 6 lastest products
     * for each product, we will take the array of the colors, the sizes available
     *
     * @param CategoryRepository $categoryRepository
     * @param ProductRepository $productRepository
     * @param SessionInterface $session
     * @return Response
     */
    #[Route('/', name: 'app.home', methods: 'GET')]
    public function index(
        CategoryRepository $categoryRepository, 
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
            'nbProductInCart' => $nbProductInCart,
            'categories' => $categories,
            'newsProducts' => $newsProducts,
            'newProductsSizes' => $newProductsSizes,
            'newProductsColors' => $newProductsColors,
            'srcPhoto' => $srcPhoto
        ]);
    }

    // public function getDataForProduct (
    //     CategoryRepository $categoryRepository, 
    //     ProductRepository $productRepository, $products
    // ) 
    // {
    //     $pathToPhoto = 'photo/';

    //     // for each newsProduct, we will take the array of the colors, the sizes available
    //     foreach($products as $key => $newProduct) {
    //         // get all color of one newProduct with his reference
    //         $colorsTab = $productRepository->findDistinctColorsByReference($newProduct->getReference());
    //         // then add $colorTab into $newProductsColors with id of this product
    //         $newProductsColors [$newProduct->getId()] = $colorsTab;

    //         // get distinct sizes of a product
    //         $sizeTab = $productRepository->findDistinctSizesByReference($newProduct -> getReference());
    //         $newProductsSizes [$newProduct->getId()] = $sizeTab;

    //         // path to product photo1
    //         $category = $newProduct->getCategory()->getName();
    //         $photo1 = $newProduct->getPhoto1();
    //         $path =  $pathToPhoto . $category . '/' . $photo1;
    //         $srcPhoto[$newProduct->getId()] = $path;
    //     }

    //     return [$srcPhoto, $newProduct, $newProductsSizes, $newProductsColors ];
    // }

    /**
     * 
     */
    /**
     * function to get all the products of 1 category
     *  order by price if necessary
     * @param SessionInterface $session
     * @param Request $request
     * @param [type] $category
     * @param ProductRepository $productRepository
     * @param CategoryRepository $categoryRepository
     * @return Response
     */
    #[Route('/shop/{category}', name: 'app.shop', methods: ['GET', 'POST'])]
    public function shop (
        SessionInterface $session,
        Request $request, 
        $category,
        ProductRepository $productRepository,
        CategoryRepository $categoryRepository
    ) :Response 
    {
        // get data from session for header & navbar
        $data = $session->get('shared_data');
        $nbProductInCart = $data['nbProductInCart'];
        $categories = $data['categories'];
        
        // get id of the category
        $category = $categoryRepository->findBy(['name' => $category]);

        // if there is no category => give message erreur then redirect
        if (empty($category)) {
            $this->addFlash('error', 'Catégory pas trouvé');
            return $this->redirectToRoute('app.home');
        }
        // get Id of the category choosen
        $categoryId = (int)$category[0]->getId();

        // by default, there is no sort, we get all products by id
        $products = $productRepository->findByCategoryId($categoryId);

        // if there is sort by price
        $orderPrice = $request->query->get('orderPrice');
        if ($orderPrice) {
            // get the product but sort by Price
            $products = $productRepository->findByCategoryId($categoryId, ['price' => $orderPrice]);
        } 

        // for each product, we will take the array of the colors, the sizes available
        $pathToPhoto = 'photo/';
        foreach($products as $key => $newProduct) {
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

        return $this->render('product/shop.html.twig', [
            'nbProductInCart' => $nbProductInCart,
            'categories' => $categories,
            'category' => $category,
            'products' => $products,
            'srcPhoto' => $srcPhoto,
            'productsSizes' => $newProductsSizes,
            'productsColors' => $newProductsColors,
            'orderPrice' => $orderPrice
        ]);
    }

    #[Route("/product/{id}", name: "app.product", methods: ["GET", "POST"])]
    public function showProduct (
        SessionInterface $session,
        CategoryRepository $categoryRepository,
        ProductRepository $productRepository,
        Product $product 
    ) : Response 
    {
        // get data from session for header & navbar
        $data = $session->get('shared_data');
        $nbProductInCart = $data['nbProductInCart'];
        $categories = $data['categories'];

        dd($product);
        
        return $this->render('product/detail.html.twig', [
            'nbProductInCart' => $nbProductInCart,
            'categories' => $categories,
        ]);
    }
   
}
