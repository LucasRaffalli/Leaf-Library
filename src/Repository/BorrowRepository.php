<?php

namespace App\Repository;

use App\Entity\Borrow;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\Persistence\ManagerRegistry;


class BorrowRepository extends ServiceEntityRepository
{
    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, Borrow::class);
    }

    public function hasActiveBorrow(\App\Entity\Book $book): bool
    {
        $qb = $this->createQueryBuilder('b')
            ->select('COUNT(b.id)')
            ->join('b.status', 's')
            ->where('b.book = :book')
            ->andWhere('s.name IN (:activeStatuses)')
            ->setParameter('book', $book)
            ->setParameter('activeStatuses', ['En cours', 'En retard']);

        return (int) $qb->getQuery()->getSingleScalarResult() > 0;
    }

    public function findActiveBorrowForBook(\App\Entity\Book $book): ?Borrow
    {
        return $this->createQueryBuilder('b')
            ->join('b.status', 's')
            ->where('b.book = :book')
            ->andWhere('s.name IN (:activeStatuses)')
            ->setParameter('book', $book)
            ->setParameter('activeStatuses', ['En cours', 'En retard'])
            ->orderBy('b.borrowDate', 'DESC')
            ->setMaxResults(1)
            ->getQuery()
            ->getOneOrNullResult();
    }

    public function findActiveBorrowsForUser(\App\Entity\User $user): array
    {
        return $this->createQueryBuilder('b')
            ->join('b.status', 's')
            ->where('b.user = :user')
            ->andWhere('s.name IN (:activeStatuses)')
            ->setParameter('user', $user)
            ->setParameter('activeStatuses', ['En cours', 'En retard'])
            ->orderBy('b.borrowDate', 'DESC')
            ->getQuery()
            ->getResult();
    }

    public function findOverdueBorrows(?\App\Entity\User $user = null): array
    {
        $qb = $this->createQueryBuilder('b')
            ->join('b.status', 's')
            ->where('s.name = :status')
            ->andWhere('b.returnDueDate < :today')
            ->setParameter('status', 'En cours')
            ->setParameter('today', new \DateTime());

        if ($user) {
            $qb->andWhere('b.user = :user')
               ->setParameter('user', $user);
        }

        return $qb->orderBy('b.returnDueDate', 'ASC')
            ->getQuery()
            ->getResult();
    }

    //    /**
    //     * @return Borrow[] Returns an array of Borrow objects
    //     */
    //    public function findByExampleField($value): array
    //    {
    //        return $this->createQueryBuilder('b')
    //            ->andWhere('b.exampleField = :val')
    //            ->setParameter('val', $value)
    //            ->orderBy('b.id', 'ASC')
    //            ->setMaxResults(10)
    //            ->getQuery()
    //            ->getResult()
    //        ;
    //    }

    //    public function findOneBySomeField($value): ?Borrow
    //    {
    //        return $this->createQueryBuilder('b')
    //            ->andWhere('b.exampleField = :val')
    //            ->setParameter('val', $value)
    //            ->getQuery()
    //            ->getOneOrNullResult()
    //        ;
    //    }
}
