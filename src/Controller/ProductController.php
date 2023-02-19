<?php

namespace App\Controller;

use App\Entity\Size;
use App\Entity\Color;
use App\Entity\Product;
use App\Entity\Category;
use App\Repository\ProductRepository;
use App\Repository\CategoryRepository;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\HttpFoundation\Session\SessionInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;

class ProductController extends AbstractController

{

    private $entityManager;

    public function __construct(EntityManagerInterface $entityManager)
    {
        $this->entityManager = $entityManager;
    }
    
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
        // $newsProducts = $productRepository->findNews();
        // $newsProducts = $this->getProducts('new');

        $results = $this->getProducts('new');
        $newsProducts = $results['products'];
        $newProductsColors = $results['newProductsColors'];
        $newProductsSizes = $results['newProductsSizes'];
        $srcPhoto = $results['srcPhoto1'];
        // dd($newProducts);

        // // add some empty array
        // $pathToPhoto = 'photo/';
        // $newProductsPathToPhoto = [];
        // $newProductsColors = [];
        // $newProductsSizes = [];
        // $srcPhoto = [];
       
        // // for each newsProduct, we will take the array of the colors, the sizes available
        // foreach($newsProducts as $key => $newProduct) {
        //     // get all color of one newProduct with his reference
        //     $colorsTab = $productRepository->findDistinctColorsByReference($newProduct->getReference());
        //     // then add $colorTab into $newProductsColors with id of this product
        //     $newProductsColors [$newProduct->getId()] = $colorsTab;

        //     // get distinct sizes of a product
        //     $sizeTab = $productRepository->findDistinctSizesByReference($newProduct -> getReference());
        //     $newProductsSizes [$newProduct->getId()] = $sizeTab;

        //     // path to product photo1
        //     $category = $newProduct->getCategory()->getName();
        //     $photo1 = $newProduct->getPhoto1();
        //     $path =  $pathToPhoto . $category . '/' . $photo1;
        //     $srcPhoto[$newProduct->getId()] = $path;
        // }
        

        return $this->render('product/index.html.twig', [
            'nbProductInCart' => $nbProductInCart,
            'categories' => $categories,
            'newsProducts' => $newsProducts,
            'newProductsSizes' => $newProductsSizes,
            'newProductsColors' => $newProductsColors,
            'srcPhoto' => $srcPhoto
        ]);
    }

    public function getProducts ($criteria, $orderBy=null) {
        $productRepository = $this->entityManager->getRepository(Product::class);
        // get list of products with some criteria
        if ($criteria == 'all') {
            $products = $productRepository->findAll();
        } 
        else if ($criteria == 'new') {
            $products = $productRepository->findNews();
        } 
        // else if ($criteria == 'id' && $id) {
        //     $products = $productRepository->find($id);
        // } 
        else {
            $products = $productRepository->findBy(['category' => $criteria]);
        }

        if ($orderBy == 'asc') {
            usort($products, function($a, $b) {
                return $a->getPrice() - $b->getPrice();
            });
        } else if ($orderBy == 'desc') {
            usort($products, function($a, $b) {
                return $b->getPrice() - $a->getPrice();
            });
        }

        // add some empty array
        $pathToPhoto = 'photo/';
        $newProductsPathToPhoto = [];
        $newProductsColors = [];
        $newProductsSizes = [];
        $srcPhoto1 = [];
        $srcPhoto2 = [];
        $srcPhoto3 = [];
        $srcPhoto4 = [];
        // for each newsProduct, we will take the array of the colors, the sizes available
        foreach($products as $key => $newProduct) {
            // get all color of one newProduct with his reference
            $colorsTab = $productRepository->findDistinctColorsByReference($newProduct->getReference());
            // then add $colorTab into $newProductsColors with id of this product
            $newProductsColors [$newProduct->getId()] = $colorsTab;

            // get distinct sizes of a product
            $sizeTab = $productRepository->findDistinctSizesByReference($newProduct -> getReference());
            $newProductsSizes [$newProduct->getId()] = $sizeTab;

            // path to product photo
            $category = $newProduct->getCategory()->getName();
            $photo1 = $newProduct->getPhoto1();
            $path1 =  $pathToPhoto . $category . '/' . $photo1;
            $srcPhoto1[$newProduct->getId()] = $path1;

            $photo2 = $newProduct->getPhoto2();
            $path2 =  $pathToPhoto . $category . '/' . $photo2;
            $srcPhoto2[$newProduct->getId()] = $path2;

            $photo3 = $newProduct->getPhoto3();
            $path3 =  $pathToPhoto . $category . '/' . $photo3;
            $srcPhoto3[$newProduct->getId()] = $path3;

            $photo4 = $newProduct->getPhoto4();
            $path4 =  $pathToPhoto . $category . '/' . $photo4;
            $srcPhoto4[$newProduct->getId()] = $path4;
        }

        return [
            'products' => $products,
            'newProductsColors' => $newProductsColors,
            'newProductsSizes' => $newProductsSizes,
            'srcPhoto1' => $srcPhoto1,
            'srcPhoto2' => $srcPhoto2,
            'srcPhoto3' => $srcPhoto3,
            'srcPhoto4' => $srcPhoto4
        ];
    }


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
        
        // dd($category);  // chemises

        // get id of the category
        $categoryEntity = $categoryRepository->findBy(['name' => $category]);
        // dd($category);
        // if there is no category => give message erreur then redirect
        if (empty($categoryEntity)) {
            $this->addFlash('error', 'Catégory pas trouvé');
            return $this->redirectToRoute('app.home');
        }
        // get Id of the category choosen
        $categoryId = (int)$categoryEntity[0]->getId();

        // by default, there is no sort, we get all products by id
        // $products = $productRepository->findByCategoryId($categoryId);
        // $products = $this->getProducts($categoryId);

        $results = $this->getProducts($categoryId);
        $products = $results['products'];
        $productsColors = $results['newProductsColors'];
        $productsSizes = $results['newProductsSizes'];
        $srcPhoto = $results['srcPhoto1'];

        // if there is sort by price
        $orderPrice = $request->query->get('orderPrice');
        if ($orderPrice) {
            // get the product but sort by Price
            // $products = $productRepository->findByCategoryId($categoryId, ['price' => $orderPrice]);
            $products = $this->getProducts($categoryId, $orderPrice )['products'];
            
        } 
        // dd($products);
        
        // for each product, we will take the array of the colors, the sizes available
        // $pathToPhoto = 'photo/';
        // foreach($products as $key => $newProduct) {
        //     // get all color of one newProduct with his reference
        //     $colorsTab = $productRepository->findDistinctColorsByReference($newProduct->getReference());
        //     // then add $colorTab into $newProductsColors with id of this product
        //     $newProductsColors [$newProduct->getId()] = $colorsTab;

        //     // get distinct sizes of a product
        //     $sizeTab = $productRepository->findDistinctSizesByReference($newProduct -> getReference());
        //     $newProductsSizes [$newProduct->getId()] = $sizeTab;

        //     // path to product photo1
        //     $category = $newProduct->getCategory()->getName();
        //     $photo1 = $newProduct->getPhoto1();
        //     $path =  $pathToPhoto . $category . '/' . $photo1;
        //     $srcPhoto[$newProduct->getId()] = $path;
        // }

        return $this->render('product/shop.html.twig', [
            'nbProductInCart' => $nbProductInCart,
            'categories' => $categories,
            'category' => $category,
            'products' => $products,
            'srcPhoto' => $srcPhoto,
            'productsSizes' => $productsSizes,
            'productsColors' => $productsColors,
            'orderPrice' => $orderPrice,
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


        
        // add some empty array
        $pathToPhoto = 'photo/';
        $productsPathToPhoto = [];
        $productColors = [];
        $productSizes = [];
        $srcPhoto1 = [];
        $srcPhoto2 = [];
        $srcPhoto3 = [];
        $srcPhoto4 = [];
        // get path to photo
        // get all color of one newProduct with his reference
        $colorsTab = $productRepository->findDistinctColorsByReference($product->getReference());
        
        // get distinct sizes of a product
        $sizeTab = $productRepository->findDistinctSizesByReference($product -> getReference());

        // path to product photo
        $category = $product->getCategory()->getName();

        $photo1 = $product->getPhoto1();
        $path1 =  $category . '/' . $photo1;
        $srcPhoto[]=$path1;
        
        $photo2 = $product->getPhoto2();
        $path2 =  $category . '/' . $photo2;
        $srcPhoto[]=$path2;

        $photo3 = $product->getPhoto3();
        $path3 =  $category . '/' . $photo3;
        $srcPhoto[]=$path3;

        $photo4 = $product->getPhoto4();
        $path4 =  $category . '/' . $photo4;
        $srcPhoto[]=$path4;

        return $this->render('product/detail.html.twig', [
            'nbProductInCart' => $nbProductInCart,
            'categories' => $categories,
            'product' => $product,
            'sizeTab' => $sizeTab,
            'colorTab' => $colorsTab,
            'srcPhoto' => $srcPhoto,
        ]);
    }
   
}
