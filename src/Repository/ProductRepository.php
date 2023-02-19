<?php

namespace App\Repository;

use App\Entity\Product;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\Persistence\ManagerRegistry;

/**
 * @extends ServiceEntityRepository<Product>
 *
 * @method Product|null find($id, $lockMode = null, $lockVersion = null)
 * @method Product|null findOneBy(array $criteria, array $orderBy = null)
 * @method Product[]    findAll()
 * @method Product[]    findBy(array $criteria, array $orderBy = null, $limit = null, $offset = null)
 */
class ProductRepository extends ServiceEntityRepository
{
    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, Product::class);
    }

    public function save(Product $entity, bool $flush = false): void
    {
        $this->getEntityManager()->persist($entity);

        if ($flush) {
            $this->getEntityManager()->flush();
        }
    }

    public function remove(Product $entity, bool $flush = false): void
    {
        $this->getEntityManager()->remove($entity);

        if ($flush) {
            $this->getEntityManager()->flush();
        }
    }

    public function findNews(): array
   {
       return $this->createQueryBuilder('p')
           ->orderBy('p.creation_date', 'ASC')
           ->setMaxResults(6)
           ->getQuery()
           ->getResult()
       ;
   }

//    public function pathToPhoto (Product $product) : string 
//    {
//         $pathToPhoto = 'photo/';
//         $category_name = ($product->getCategory()->getName());
//         // dd($category_name);
//         $path = $pathToPhoto . $category_name . "/" . $product->getPhoto1();
//         // dd($path);
//         return $path;
//    }

   public function findDistinctColorsByReference($reference) :array 
   {
    return $this->createQueryBuilder('p')
                ->join('p.color', 'c')
                ->select('DISTINCT c.name')
                ->andWhere('p.reference = :reference')
                ->setParameter('reference', $reference)
                ->getQuery()
                ->getResult();
   }

   public function findDistinctSizesByReference($reference) :array 
   {
    return $this->createQueryBuilder('p')
                ->join('p.size', 's')
                ->select('DISTINCT s.name')
                ->andWhere('p.reference = :reference')
                ->setParameter('reference', $reference)
                ->getQuery()
                ->getResult();
   }

   public function findByCategoryId(int $categoryId, $order=null): array
    {
        $q = $this->findBy(['category' => $categoryId], $order);
        return $q;
    }

   
//    /**
//     * @return Product[] Returns an array of Product objects
//     */
//    public function findByExampleField($value): array
//    {
//        return $this->createQueryBuilder('p')
//            ->andWhere('p.exampleField = :val')
//            ->setParameter('val', $value)
//            ->orderBy('p.id', 'ASC')
//            ->setMaxResults(10)
//            ->getQuery()
//            ->getResult()
//        ;
//    }

//    public function findOneBySomeField($value): ?Product
//    {
//        return $this->createQueryBuilder('p')
//            ->andWhere('p.exampleField = :val')
//            ->setParameter('val', $value)
//            ->getQuery()
//            ->getOneOrNullResult()
//        ;
//    }
}
