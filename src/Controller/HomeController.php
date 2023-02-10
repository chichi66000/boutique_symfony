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
        $categories = $categoryRepository->findAll();
        // dd($categories[0]->getName());
        // the recent products in trend
        $newsProducts = $productRepository->findNews();
        $pathToPhoto = 'photo/';
        // dd($newsProducts[1]->getCategory()->getName());
        foreach($newsProducts as $key => $newProduct) {
            // path to product photo1
            // $newProductsPathToPhoto= $pathToPhoto . $newProduct->getCategory()->getName(). "/" . $newProduct->getPhoto1();
            $path = $productRepository->pathToPhoto($newProduct);
        }
        
        return $this->render('home/index.html.twig', [
            'newsProducts' => $newsProducts,
            'categories' => $categories
        ]);
    }
}
