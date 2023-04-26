<?php

namespace App\Controller;

use App\Entity\Size;
use App\Entity\Color;
use App\Entity\Product;
use App\Entity\Category;
use App\Form\SearchProductType;
use App\Form\UpdateProductType;
use Doctrine\ORM\EntityManager;
use App\Repository\SizeRepository;
use App\Repository\ColorRepository;
use App\Repository\ProductRepository;
use App\Repository\CategoryRepository;
use Doctrine\ORM\EntityManagerInterface;
use Knp\Component\Pager\PaginatorInterface;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\Form\Extension\Core\Type\ChoiceType;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Security;
use Symfony\Component\HttpFoundation\Session\SessionInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\File\Exception\FileException;
use Symfony\Component\Security\Core\Exception\AccessDeniedException;

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
        // get nombre product in cart if exist
        $nbProductInCart = $session->get('nbProductInCart', );
        if($nbProductInCart == null) {
            $nbProductInCart = 0;
        }
        
        // get all categories
        $categories = $categoryRepository->findAll();

        // save in data then save into session. We can share this information for navbar & header in others Controllers
        $session->set('categories', $categories);
        $session->set('nbProductInCart', $nbProductInCart);

        // the recent products in trend
        $results = $this->getProducts('new');
        $newsProducts = $results['products'];
        $newProductsColors = $results['newProductsColors'];
        $newProductsSizes = $results['newProductsSizes'];
        $srcPhoto = $results['srcPhoto1'];

        return $this->render('product/index.html.twig', [
            'nbProductInCart' => $nbProductInCart,
            'categories' => $categories,
            'newsProducts' => $newsProducts,
            'newProductsSizes' => $newProductsSizes,
            'newProductsColors' => $newProductsColors,
            'srcPhoto' => $srcPhoto
        ]);
    }

    /**
     * function to get the products with some criteria : all, news, categories
     *
     * @param [type] $criteria
     * @param [type] $orderBy
     * @return void
     */
    public function getProducts ($criteria, $orderBy=null) {
        $productRepository = $this->entityManager->getRepository(Product::class);
        // get list of products with some criteria
        if ($criteria == 'all') {
            $products = $productRepository->findAll();
        } 
        else if ($criteria == 'new') {
            $products = $productRepository->findNews();
        } 
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
        $nbProductInCart = $session->get('nbProductInCart');
        $categories = $session->get('categories');
        
        // get id of the category
        $categoryEntity = $categoryRepository->findBy(['name' => $category]);
        if (empty($categoryEntity)) {
            $this->addFlash('error', 'Pas de catégory demandé');
            return $this->redirectToRoute('app.home');
        }
       
        // get Id of the category choosen
        $categoryId = (int)$categoryEntity[0]->getId();

        // get all the product of this category with the function getProducts
        $results = $this->getProducts($categoryId);
        $products = $results['products'];
        // if there is no products of this category => give message erreur then redirect
        if (empty($products)) {
            $this->addFlash('error', "Pas de produits disponible pour ce catégory $category. Revenez plus tard");
            return $this->redirectToRoute('app.home');
        }

        $productsColors = $results['newProductsColors'];
        $productsSizes = $results['newProductsSizes'];
        $srcPhoto = $results['srcPhoto1'];

        // if there is sort by price
        $orderPrice = $request->query->get('orderPrice');
        if ($orderPrice) {
            // get the product but sort by Price
            $products = $this->getProducts($categoryId, $orderPrice )['products'];
            
        } 

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

    
    /**
     * function to show detail information of 1 product with his ID
     *
     * @param SessionInterface $session
     * @param CategoryRepository $categoryRepository
     * @param ProductRepository $productRepository
     * @param ColorRepository $colorRepository
     * @param SizeRepository $sizeRepository
     * @param Product $product
     * @return Response
     */
    #[Route("/product/{id}", name: "app.product", methods: ["GET", "POST"])]
    public function showProduct (
        SessionInterface $session,
        // CategoryRepository $categoryRepository,
        ProductRepository $productRepository,
        ColorRepository $colorRepository,
        SizeRepository $sizeRepository,
        Product $product 
    ) : Response 
    {
        // get data from session for header & navbar
        $nbProductInCart = $session->get('nbProductInCart');
        $categories = $session->get('categories');
        // get all color of one newProduct with his reference
        $colorsTab = $productRepository->findDistinctColorsByReference($product->getReference());
        
        // get distinct sizes of a product
        $sizeTab = $productRepository->findDistinctSizesByReference($product -> getReference());

        // get the current color & size of this product
        $colorId = $product->getColor();
        $color = $colorRepository->findBy(['id' => $colorId]);
        
        $sizeId = $product->getSize();
        $size = $sizeRepository->findBy(['id' => $sizeId]);
        
        $category = $product->getCategory()->getName();
        
        // path to product photo
        $pathToPhoto = 'photo/';
        // get the src of all photos
        $photo1 = $product->getPhoto1();
        $path1 =  $pathToPhoto . $category . '/' . $photo1;
        $srcPhoto[]=$path1;
        
        $photo2 = $product->getPhoto2();
        $path2 =  $pathToPhoto . $category . '/' . $photo2;
        $srcPhoto[]=$path2;

        $photo3 = $product->getPhoto3();
        $path3 =   $pathToPhoto . $category . '/' . $photo3;
        $srcPhoto[]=$path3;

        $photo4 = $product->getPhoto4();
        $path4 =   $pathToPhoto . $category . '/' . $photo4;
        $srcPhoto[]=$path4;

        return $this->render('product/detail.html.twig', [
            'nbProductInCart' => $nbProductInCart,
            'categories' => $categories,
            'product' => $product,
            'sizeTab' => $sizeTab,
            'colorTab' => $colorsTab,
            'srcPhoto' => $srcPhoto,
            'color' => $color,
            'size' => $size
        ]);
    }

    #[Security("is_granted('ROLE_ADMIN')")]
    #[Route('/admin/product', name: 'app.admin.product', methods: ['GET', 'POST'])]
    /**
     * function for gestion product, give result of search product then display in template 
     */
    public function gestionProduct (
        SessionInterface $session,
        Request $request,
        ProductRepository $productRepository,
        PaginatorInterface $paginator,
        // $product = null
    ) 
    : Response 
    {
        // get data from session for header & navbar
        $nbProductInCart = $session->get('nbProductInCart');
        $categories = $session->get('categories');

        // if user isn't admin, refuse access
        if (!$this->isGranted('ROLE_ADMIN')) {
            throw new AccessDeniedException('Accès refusé.');
        }
        // admin: he has access to form request
        else {
            $form = $this->createForm(SearchProductType::class);
            $form->handleRequest($request);
            $products = [];
            $productSearch = "";
            if ($form->isSubmitted() && $form->isValid()) {
                $productSearch = $form['searchProduct']->getData();
                // get list of product with ProductRepository
                $products = $productRepository->findProductsWithSearch($productSearch);
                // dd($products);
                // the insert the system paginator with users
                $products = $paginator->paginate(
                    $products,
                    $request->query->getInt('page', 1),
                    2 /*limit 2 per page*/
                );
            }

            return $this->render('product/gestion.product.html.twig', [
                'nbProductInCart' => $nbProductInCart,
                'categories' => $categories,
                'form' => $form,
                'products' => $products,
                'productSearch' => $productSearch
            ]);
        }
    }
   
    #[Security("is_granted('ROLE_ADMIN')")]
    #[Route('/modify/product/{id}', name: "app.modify.product", methods: ['GET', 'POST'])]
    public function modifyProduct (
        SessionInterface $session,
        ProductRepository $productRepository,
        ColorRepository $colorRepository,
        Product $product,
        Request $request,
        EntityManagerInterface $manager
    ) 
    :Response 
    {
        // get data from session for header & navbar
        $nbProductInCart = $session->get('nbProductInCart');
        $categories = $session->get('categories');

        // if user isn't admin, refuse access
        if (!$this->isGranted('ROLE_ADMIN')) {
            throw new AccessDeniedException('Accès refusé.');
        }
        else {
            $message = "";
            // $colors = $colorRepository->findAll();

            // $colorChoices= [];
            // foreach($colors as $color) {
            //     $colorChoices [$color->getId()] = $color->getName(); 
            // }

            // dd($colorChoices);
            $photo1Name = $product->getPhoto1();
            $photo2Name = $product->getPhoto2();
            $photo3Name = $product->getPhoto3();
            $photo4Name = $product->getPhoto4();
            $form = $this->createForm(UpdateProductType::class, $product);
            $form->handleRequest($request);
            if ($form->isSubmitted() && $form->isValid()) {
                // dd($photo1Name);
                // $prod = $form->getData();
                $photo1 = $form['photo1']->getData();
                $photo2 = $form['photo2']->getData();
                $photo3 = $form['photo3']->getData();
                $photo4 = $form['photo4']->getData();
                // dd($photo1name, $extension);
                if ($photo1) {
                    // photoName & extension
                    $extension1 = $photo1->guessExtension();
                    $photo1Name = pathinfo($photo1->getClientOriginalName(), PATHINFO_FILENAME) . '.' . $extension1;
                    // Move the file to the directory where brochures are stored
                    try {
                        $photo1->move(
                            $this->getParameter('photo_product') . $product->getCategory(),
                            $photo1Name
                        );
                    } catch (FileException $e) {
                        // ... handle exception if something happens during file upload
                        $message = "Cannot upload file. Please try later";
                    }
                }
                $product->setPhoto1($photo1Name);
                if ($photo2) {
                    // photoName & extension
                    $extension2 = $photo2->guessExtension();
                    $photo2Name = pathinfo($photo2->getClientOriginalName(), PATHINFO_FILENAME) . '.' . $extension2;
                    // Move the file to the directory where brochures are stored
                    try {
                        $photo2->move(
                            $this->getParameter('photo_product') . $product->getCategory(),
                            $photo2Name
                        );
                    } catch (FileException $e) {
                        // ... handle exception if something happens during file upload
                        $message = "Cannot upload file. Please try later";
                    }
                }
                $product->setPhoto2($photo2Name);
                if ($photo3) {
                    // photoName & extension
                    $extension3 = $photo3->guessExtension();
                    $photo3Name = pathinfo($photo3->getClientOriginalName(), PATHINFO_FILENAME) . '.' . $extension3;
                    
                    // Move the file to the directory where brochures are stored
                    try {
                        $photo3->move(
                            $this->getParameter('photo_product') . $product->getCategory(),
                            $photo3Name
                        );
                    } catch (FileException $e) {
                        // ... handle exception if something happens during file upload
                        $message = "Cannot upload file. Please try later";
                    }
                }
                $product->setPhoto3($photo3Name);
                if ($photo4) {
                    // photoName & extension
                    $extension4 = $photo4->guessExtension();
                    $photo4Name = pathinfo($photo4->getClientOriginalName(), PATHINFO_FILENAME)  . '.' . $extension4;
                    // Move the file to the directory where brochures are stored
                    try {
                        $photo4->move(
                            $this->getParameter('photo_product') . $product->getCategory(),
                            $photo4Name
                        );
                    } catch (FileException $e) {
                        // ... handle exception if something happens during file upload
                        $message = "Cannot upload file. Please try later";
                    }
                }
                $product->setPhoto4($photo4Name);

                $manager->persist($product);
                $manager->flush();
                $this->addFlash('success', "Produit a été modifié avec succès");
                return $this->redirectToRoute('app.admin.product');
            }
        }

        return $this->render('product/modify.product.html.twig', [
            'nbProductInCart' => $nbProductInCart,
            'categories' => $categories,
            'form' => $form->createView(),
            'product' => $product,
            'message' => $message,
        ]);
    }
}
