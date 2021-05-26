<?php

namespace App\Repository;

use App\Entity\Sortie;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\Persistence\ManagerRegistry;

/**
 * @method Sortie|null find($id, $lockMode = null, $lockVersion = null)
 * @method Sortie|null findOneBy(array $criteria, array $orderBy = null)
 * @method Sortie[]    findAll()
 * @method Sortie[]    findBy(array $criteria, array $orderBy = null, $limit = null, $offset = null)
 */
class SortieRepository extends ServiceEntityRepository
{
    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, Sortie::class);
    }

    /**
     * Fonction qui récupère les sorties sans une recherche.
     * @return Sortie[]
     */
    public function recherchesSorties(): array
    {
        return $this->findAll();
    }

   /* /**
     * Fonction qui récupère les sorties avec une recherche.
     * @return Sortie[]
     */
    //public function accueil(): array
    //{
     //   return $this->findAll();
    //}

}
