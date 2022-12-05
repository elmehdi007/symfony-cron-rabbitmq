<?php

namespace App\Repository;

use App\Entity\Article;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\Persistence\ManagerRegistry;

/**
 * @extends ServiceEntityRepository<Article>
 *
 * @method Article|null find($id, $lockMode = null, $lockVersion = null)
 * @method Article|null findOneBy(array $criteria, array $orderBy = null)
 * @method Article[]    findAll()
 * @method Article[]    findBy(array $criteria, array $orderBy = null, $limit = null, $offset = null)
 */
class ArticleRepository extends ServiceEntityRepository
{
    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, Article::class);
    }

    public function save(Article $entity, bool $flush = false): void
    {
        $this->getEntityManager()->persist($entity);

        if ($flush) {
            $this->getEntityManager()->flush();
        }
    }

    public function remove(Article $entity, bool $flush = false): void
    {
        $this->getEntityManager()->remove($entity);

        if ($flush) {
            $this->getEntityManager()->flush();
        }
    }

    /**
     * @return Article[] Returns an array of Article objects
     */
     public function search(int $take=10,int $skip=1, ?string $sortColumn ="id",?string $sortDir = "desc"): array
     {
         if($sortColumn == null) $sortColumn ="id";
         if($sortDir == null) $sortColumn ="desc";
        
         return $this->createQueryBuilder('article')
             ->orderBy('article.'.$sortColumn, $sortDir)
             ->setMaxResults($take)
             ->setFirstResult($skip)
             ->getQuery()
             ->getResult();
     }


    /**
     * @return bool Returns an array of Article objects
     */
    public function isTitleArticleExists(?string $title): bool
    {
        return $this->createQueryBuilder('article')
            ->select('count(article.id)')
            ->andWhere('article.title = :val')
            ->setParameter('val', $title)
            ->setMaxResults(1)
            ->getQuery()
            ->getSingleScalarResult()>0;
    }

    /**
     * @return ?Article Returns an array of Article objects
     */
    public function findArticleByTitle(?string $title): ?Article
    {
        $article = null;

        if($this->isTitleArticleExists( $title) === true)  
        
        $article = $this->createQueryBuilder('article')
            ->andWhere('article.title = :val')
            ->setParameter('val', $title)
            ->setMaxResults(1)
            ->getQuery()
            ->getSingleResult();
    
        return $article ;
    }


//    public function findOneBySomeField($value): ?Article
//    {
//        return $this->createQueryBuilder('a')
//            ->andWhere('a.exampleField = :val')
//            ->setParameter('val', $value)
//            ->getQuery()
//            ->getOneOrNullResult()
//        ;
//    }
}
