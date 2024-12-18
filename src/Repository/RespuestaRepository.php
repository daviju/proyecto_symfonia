<?php

namespace App\Repository;

use App\Entity\Respuesta;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\Persistence\ManagerRegistry;

use Doctrine\ORM\Query\ResultSetMapping;
use App\Entity\Pregunta;

/**
 * @extends ServiceEntityRepository<Respuesta>
 */
class RespuestaRepository extends ServiceEntityRepository
{
    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, Respuesta::class);
    }

    public function countRespuestasByPregunta(Pregunta $pregunta): array {
    $conn = $this->getEntityManager()->getConnection();

    $sql = '
        SELECT respuesta, COUNT(*) as count 
        FROM respuesta 
        WHERE pregunta_id = :pregunta_id 
        GROUP BY respuesta
    ';

    $resultSet = $conn->executeQuery($sql, ['pregunta_id' => $pregunta->getId()]);
    
    $results = [];
    while ($row = $resultSet->fetchAssociative()) {
        $results[$row['respuesta']] = $row['count'];
    }

    return $results;
}

    //    /**
    //     * @return Respuesta[] Returns an array of Respuesta objects
    //     */
    //    public function findByExampleField($value): array
    //    {
    //        return $this->createQueryBuilder('r')
    //            ->andWhere('r.exampleField = :val')
    //            ->setParameter('val', $value)
    //            ->orderBy('r.id', 'ASC')
    //            ->setMaxResults(10)
    //            ->getQuery()
    //            ->getResult()
    //        ;
    //    }

    //    public function findOneBySomeField($value): ?Respuesta
    //    {
    //        return $this->createQueryBuilder('r')
    //            ->andWhere('r.exampleField = :val')
    //            ->setParameter('val', $value)
    //            ->getQuery()
    //            ->getOneOrNullResult()
    //        ;
    //    }
}
