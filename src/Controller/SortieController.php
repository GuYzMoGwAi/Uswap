<?php

namespace App\Controller;

use App\Entity\Lieu;
use App\Entity\Sortie;
use App\Form\LieuType;
use App\Form\SortieType;
use Doctrine\DBAL\Types\ArrayType;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\Form\Extension\Core\Type\ChoiceType;
use Symfony\Component\Form\Extension\Core\Type\CollectionType;
use Symfony\Component\Form\Extension\Core\Type\DateTimeType;
use Symfony\Component\Form\Extension\Core\Type\IntegerType;
use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

class SortieController extends AbstractController
{
    /**
     * @Route("/sortie/ajouter", name="nouvelle_sortie")
     * @param Request $request
     * @return Response
     */
    public function newSortie(Request $request): Response
    {
        $organisateur = $this->getUser();
        $sortie = new Sortie();
        $form = $this->createForm(SortieType::class, $sortie);

        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            //enregistrement BDD
            $entityManager = $this->getDoctrine()->getManager();
            $sortie->setOrganisateur($organisateur);
            $entityManager->persist($sortie);
            $entityManager->flush();
            $this->addFlash('success', "Sortie créée");

            return $this->redirectToRoute('accueil');
        }
        return $this->render('sortie/creerSortie.html.twig', [
            'sortieForm' => $form->createView(),
            'h1' => 'Créer une sortie',
            'button' => ' Ajouter',
        ]);
    }

    /**
     * @Route("/modifierSortie/{id}", name="modifier_sortie")
     * @param Request $request
     * @param EntityManagerInterface $em
     * @param Sortie $sortie
     * @return Response
     */
    public function editSortie(Request $request, EntityManagerInterface $em, Sortie $sortie): Response
    {
        $form = $this->createForm(SortieType::class, $sortie);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            //enregistrement BDD
            $em = $this->getDoctrine()->getManager();
            $em->persist($sortie);
            $em->flush();
            $this->addFlash('success', "Sortie Modifiée");

            return $this->redirectToRoute('accueil', [
                'id' => $sortie->getId(),
            ]);
        }
        return $this->render('sortie/creerSortie.html.twig', [
            'sortieForm' => $form->createView(),
            'h1' => 'Modifier la sortie',
            'button' => ' Modifier',
        ]);
    }

    /**
     * @route("/sortie/supprimer/{id}", name="delete_sortie")
     * @param $id
     * @return Response
     */
    public function sortieDelete( $id): Response
    {
        $em = $this->getDoctrine()->getManager();
        $sortie = $em->getRepository('App:Sortie')->find($id);

        if (!$sortie) {
            $this->addFlash('error',"L'id n'a pas été trouvé");
        }
        
        $em->remove($sortie);
        $em->flush();
        $this->addFlash('success','La sortie a bien été supprimé');

        return $this->redirectToRoute('accueil');
    }

}
