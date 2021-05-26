<?php


namespace App\Controller;

use App\Form\FiltreAccueilType;
use App\Repository\CampusRepository;
use App\Repository\SortieRepository;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

class MainController extends AbstractController
{
    /**
     * @Route("/accueil", name="main_accueil")
     */
    public function accueil(Request $request, SortieRepository $sortieRepository, CampusRepository $campusRepository) : Response {

        $filtre_campusID = $request->get('filtre_campus') ;
        $filtre_nomSortie = $request->get('filtre_nomSortie') ;

       $sortiesList = $sortieRepository->recherchesSorties(
           is_null($filtre_campusID)? -1 : $filtre_campusID ,
           is_null($filtre_nomSortie)? '' : $filtre_nomSortie ,
           date_create($request->get('filtre_dateDeb')),
           date_create($request->get('filtre_dateFin')),
           is_null($request->get('filtre_CB_organisateur'))? false : true ,
           is_null($request->get('filtre_CB_inscrit'))? false : true,
           is_null($request->get('filtre_CB_pasInscrit'))? false : true,
           is_null($request->get('filtre_CB_passer'))? false : true
       );
       $campusList = $campusRepository->findAll();

        return $this->render('main/accueil.html.twig', compact("sortiesList",'campusList') );

    }
}