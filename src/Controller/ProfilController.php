<?php

namespace App\Controller;

use App\Entity\Participant;
use App\Form\CreerProfilType;
use App\Repository\ParticipantRepository;
use App\Security\AppAuthenticator;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\File\Exception\FileException;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\Security\Core\Encoder\UserPasswordEncoderInterface;
use Symfony\Component\Security\Guard\GuardAuthenticatorHandler;

class ProfilController extends AbstractController
{
    /**
     * @Route("profil/consulter/{id}", name="profil_consulter")
     */
    public function consulter(
        int $id,
        ParticipantRepository $participantRepository) : Response {

        $monProfil = $participantRepository->find($id);

        return $this->render('profil/consulter.html.twig', compact('monProfil')) ;

    }
    /**
     * @Route("/creer", name="profil_creer")
     * @Route("/profil/gerer/{id}", name="profil_gerer")
     */
    public function creerOuGerer(
        Request $request,
        Participant $participant = null,
        EntityManagerInterface $entityManager,
        UserPasswordEncoderInterface $passwordEncoder,
        string $photoDir,
        GuardAuthenticatorHandler $guardHandler,
        AppAuthenticator $authenticator): Response
    {
        $string = "modifié" ;
        if (!$participant){
            $string = "créé" ;
            $participant = new Participant();
        }
        $profilForm = $this->createForm(CreerProfilType::class, $participant);

        $profilForm->handleRequest($request);

        if ($profilForm->isSubmitted() && $profilForm->isValid()) {
            $participant->setPassword(
                $passwordEncoder->encodePassword(
                    $participant,
                    $profilForm->get('plainPassword')->getData()
                )
            );
            if ($photo = $profilForm['photo']->getData()) {
                $photoProfil = bin2hex(random_bytes(6)) . '.' . $photo->guessExtension();
                try {
                    $photo->move($photoDir, $photoProfil);
                    $participant->setPhotoProfil($photoProfil);
                } catch (FileException $e) {
                    // unable to upload the photo, give up
                }
            }
            $entityManager->persist($participant);
            $entityManager->flush();

            $this->addFlash('success', 'Votre profil a bien été ' . $string);

            return $guardHandler->authenticateUserAndHandleSuccess(
                $participant,
                $request,
                $authenticator,
                'main' // firewall name in security.yaml
            );
        }
        return $this->render('profil/creer-gerer.html.twig', [
            'profilForm' => $profilForm->createView(),
            'participant' => $participant,
            'modeGestion'=> $participant->getId() !== null
        ]);
    }
}
