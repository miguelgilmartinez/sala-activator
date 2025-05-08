<?php

namespace App\Repository;

use App\Entity\SwitchSalas;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\Persistence\ManagerRegistry;

/**
 * @extends ServiceEntityRepository<SwitchSalas>
 */
class SwitchSalasRepository extends ServiceEntityRepository {

    public function __construct(ManagerRegistry $registry) {
        parent::__construct($registry, SwitchSalas::class);
    }

    /**
     * Devuelte las IP de los switches configurados en el sistema, sin repetir
     * @return array
     */
    public function getIPsSwitches(): array {
        $result = $this->getEntityManager()->getConnection()
                ->executeQuery("SELECT DISTINCT ip FROM switch_salas;");
        return $result->fetchAllAssociative();
    }

    public function findAllSortedById(): array {
        $salas = $this->findAll();
        usort($salas, function ($a, $b) {
            return $a->getId() <=> $b->getId();
        });
        return $salas;
    }

    //    /**
    //     * @return SwitchSalas[] Returns an array of SwitchSalas objects
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
    //    public function findOneBySomeField($value): ?SwitchSalas
    //    {
    //        return $this->createQueryBuilder('s')
    //            ->andWhere('s.exampleField = :val')
    //            ->setParameter('val', $value)
    //            ->getQuery()
    //            ->getOneOrNullResult()
    //        ;
    //    }
}
