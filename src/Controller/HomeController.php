<?php

namespace App\Controller;

use App\Entity\Size;
use App\Entity\Color;
use App\Entity\Product;
use App\Entity\Category;
use App\Repository\CategoryRepository;
use App\Repository\ProductRepository;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;

class HomeController extends AbstractController
{
    #[Route('/', name: 'app.index')]
    public function index(CategoryRepository $categoryRepository, ProductRepository $productRepository): Response
    {
        // $categoryRepo = new Category();
        // $productRepo = new Product();
        // $colorRepo = new Color();
        // $sizeRepo = new Size();

        $nbProductInCart = 0;
        // $categories = $categoryRepository->findAll();
        // dd($categories[0]->getName());

        // the recent products in trend
        $newsProducts = $productRepository->findNews();
        // dd($newsProducts[0]->getCategory()->getId());
        $pathToPhoto = 'photo/';
        $newProductsPathToPhoto = [];
        $newProductsColors = [];
        $newProductsSizes = [];
        // dd($newsProducts[1]->getCategory()->getName());
        // $reference = $newsProducts[1]->getReference();
        
        // $colors = $productRepository->findDistinctColorsByReference($reference);
        
        // for each newsProduct, we will take the array of the colors, the sizes available
        foreach($newsProducts as $key => $newProduct) {
            // get all color of one newProduct with his reference
            $colorsTab = $productRepository->findDistinctColorsByReference($newProduct -> getReference());
            // then add $colorTab into $newProductsColors with id of this product
            $newProductsColors [$newProduct->getId()] = $colorsTab;

            // get distinct sizes of a product
            $sizeTab = $productRepository->findDistinctSizesByReference($newProduct -> getReference());
            $newProductsSizes [$newProduct->getId()] = $sizeTab;
            // path to product photo1
            // $newProductsPathToPhoto= $pathToPhoto . $newProduct->getCategory()->getName(). "/" . $newProduct->getPhoto1();
            // $path = $productRepository->pathToPhoto($newProduct);
        }
        // dd($newProductsSizes);

        return $this->render('home/index.html.twig', [
            'newsProducts' => $newsProducts,
            'newProductsSizes' => $newProductsSizes,
            'newProductsColors' => $newProductsColors
            // 'categories' => $categories
        ]);
    }
}
