<?php

namespace App\Repository;

use App\Entity\Sale;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\Persistence\ManagerRegistry;
use Doctrine\ORM\Query\ResultSetMapping;

/**
 * @extends ServiceEntityRepository<Sale>
 *
 * @method Sale|null find($id, $lockMode = null, $lockVersion = null)
 * @method Sale|null findOneBy(array $criteria, array $orderBy = null)
// * @method Sale[]    findAll()
 * @method Sale[]    findBy(array $criteria, array $orderBy = null, $limit = null, $offset = null)
 */
class SaleRepository extends ServiceEntityRepository
{
    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, Sale::class);
    }

    public function add(Sale $entity, bool $flush = false): void
    {
        $this->getEntityManager()->persist($entity);

        if ($flush) {
            $this->getEntityManager()->flush();
        }
    }

    public function remove(Sale $entity, bool $flush = false): void
    {
        $this->getEntityManager()->remove($entity);

        if ($flush) {
            $this->getEntityManager()->flush();
        }
    }

    /**
     * Find sales by status.
     *
     * @param string $status
     * @return Sale[]
     */
    public function findByStatus(string $status): array
    {
        return $this->createQueryBuilder('p')
            ->andWhere('p.status = :status')
            ->setParameter('status', $status)
            ->getQuery()
            ->getResult();
    }

    public function findAll(): array
    {
        return $this->createQueryBuilder('s')
            ->leftJoin('s.products', 'p') // Join with the products association
            ->addSelect('p')
            ->getQuery()
            ->getResult();
    }

//    public function findAll(): array
//    {
//        return $this->createQueryBuilder('s')
//            ->leftJoin('s.product', 'p')
//            ->addSelect('p')
//            ->leftJoin('s.location', 'l')
//            ->addSelect('l')
//            ->getQuery()
//            ->getResult();
//    }

//    public function findAllWithProducts(): array
//    {
//        $rsm = new ResultSetMapping();
//        $rsm->addEntityResult(Sale::class, 's');
//        $rsm->addFieldResult('s', 'id', 'id');
//
//        $query = $this->getEntityManager()->createNativeQuery(
//            'SELECT s.*, p.* FROM sale s LEFT JOIN sale_product p ON s.id = p.sale_id',
//            $rsm
//        );
//
//        return $query->getResult();
//    }


//    /**
//     * @return Sale[] Returns an array of Sale objects
//     */
//    public function findByExampleField($value): array
//    {
//        return $this->createQueryBuilder('s')
//            ->andWhere('s.exampleField = :val')
//            ->setParameter('val', $value)
//            ->orderBy('s.id', 'ASC')
//            ->setMaxResults(10)
//            ->getQuery()
//            ->getResult()
//        ;
//    }

//    public function findOneBySomeField($value): ?Sale
//    {
//        return $this->createQueryBuilder('s')
//            ->andWhere('s.exampleField = :val')
//            ->setParameter('val', $value)
//            ->getQuery()
//            ->getOneOrNullResult()
//        ;
//    }
}
