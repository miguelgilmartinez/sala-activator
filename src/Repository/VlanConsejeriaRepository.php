<?php

namespace App\Repository;

use App\Entity\VlanConsejeria;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\Persistence\ManagerRegistry;

/**
 * @author Miguel Gil Martínez <miguel.gil.martinez@juntadeandalucia.es>
 * @extends ServiceEntityRepository<VlanConsejeria>
 */
class VlanConsejeriaRepository extends ServiceEntityRepository {

    public function __construct(ManagerRegistry $registry) {
        parent::__construct($registry, VlanConsejeria::class);
    }

    /**
     * Devuelve todos los vlanid, en un array numérico"
     * @return array
     */
    public function getVlansDisponibles(): array {
        return $this->getEntityManager()->getConnection
                        ()->executeQuery("SELECT vlanid FROM vlan_consejeria")
                        ->fetchAllNumeric();
    }

    //    /**
    //     * @return VlanConsejeria[] Returns an array of VlanConsejeria objects
    //     */
    //    public function findByExampleField($value): array
    //    {
    //        return $this->createQueryBuilder('v')
    //            ->andWhere('v.exampleField = :val')
    //            ->setParameter('val', $value)
    //            ->orderBy('v.id', 'ASC')
    //            ->setMaxResults(10)
    //            ->getQuery()
    //            ->getResult()
    //        ;
    //    }
    //    public function findOneBySomeField($value): ?VlanConsejeria
    //    {
    //        return $this->createQueryBuilder('v')
    //            ->andWhere('v.exampleField = :val')
    //            ->setParameter('val', $value)
    //            ->getQuery()
    //            ->getOneOrNullResult()
    //        ;
    //    }
}
