<?php

namespace App\Repository;

use App\Entity\DemandesConges;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\Persistence\ManagerRegistry;

/**
 * @extends ServiceEntityRepository<DemandesConges>
 *
 * @method DemandesConges|null find($id, $lockMode = null, $lockVersion = null)
 * @method DemandesConges|null findOneBy(array $criteria, array $orderBy = null)
 * @method DemandesConges[]    findAll()
 * @method DemandesConges[]    findBy(array $criteria, array $orderBy = null, $limit = null, $offset = null)
 */
class DemandesCongesRepository extends ServiceEntityRepository
{
    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, DemandesConges::class);
    }

    public function findByEmployeeId(int $employeeId): array
{
    $qb = $this->createQueryBuilder('d');
    $qb->innerJoin('d.employe', 'e')
        ->andWhere('e.id = :employeeId')
        ->setParameter('employeeId', $employeeId);
 
    // Optional: Filter by status
    // $qb->andWhere('d.status = :approvedStatus');
    // $qb->setParameter('approvedStatus', 'approved');

    $qb->orderBy('d.id', 'DESC');
  

    return $qb->getQuery()->getResult();
}

public function findOverlapRequests($employe, \DateTime $startDate, \DateTime $endDate): array
{
    return $this->createQueryBuilder('d')
        ->andWhere('d.employe_id = :employe')
        ->andWhere('d.date_debut <= :date_fin')
        ->andWhere('d.date_fin >= :date_debut')
        ->setParameter('employe', $employe)
        ->setParameter('date_debut', $startDate)
        ->setParameter('date_fin', $endDate)
        ->getQuery()
        ->getResult();
}
//    /**
//     * @return DemandesConges[] Returns an array of DemandesConges objects
//     */
//    public function findByExampleField($value): array
//    {
//        return $this->createQueryBuilder('d')
//            ->andWhere('d.exampleField = :val')
//            ->setParameter('val', $value)
//            ->orderBy('d.id', 'ASC')
//            ->setMaxResults(10)
//            ->getQuery()
//            ->getResult()
//        ;
//    }

//    public function findOneBySomeField($value): ?DemandesConges
//    {
//        return $this->createQueryBuilder('d')
//            ->andWhere('d.exampleField = :val')
//            ->setParameter('val', $value)
//            ->getQuery()
//            ->getOneOrNullResult()
//        ;
//    }
}
