<?php

namespace App\Controller\BackController;

use App\Entity\Document;
use App\Entity\Equipment;
use App\Entity\RentedEquipment;
use App\Form\RentedEquipmentType;
use App\Form\UploadFileType;

use App\Service\FileManager;
use App\Entity\EquipmentType;
use App\Form\TypeEquipmentType;

use App\Entity\EquipmentCategory;
use App\Service\MessageGenerator;
use App\Form\EquipmentCategoryType;

use App\Repository\EquipmentRepository;
use App\Service\PhotoManager;
use Doctrine\ORM\EntityManagerInterface;
use App\Repository\EquipmentTypeRepository;

use Symfony\Component\EventDispatcher\EventDispatcherInterface;
use Symfony\Component\HttpFoundation\Request;
use App\Repository\EquipmentCategoryRepository;
use Symfony\Component\Routing\Annotation\Route;

use App\Form\EquipmentType as FormEquipmentType;
use App\Form\UploadPhotoType;
use App\Repository\DocumentRepository;
use App\Repository\RentedEquipmentRepository;
use Symfony\Component\String\Slugger\SluggerInterface;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\IsGranted;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;


/**
 * @Route("/materiel")
 * @isGranted("ROLE_OWNER", message="Connexion impossible !")
 */
class EquipmentController extends AbstractController
{


    //*******************************************************
    //*******************************************************
    // SECTION ADMIN

    //Ajout de matériel

    /**
     * @Route("/creer",name="equipment_create")
     * @isGranted("ROLE_EDITOR", message="Vous n'avez pas accès à cette section !")
     */
    public function equipmentCreate(Request $request, SluggerInterface $slugger, MessageGenerator $messageGenerator, EntityManagerInterface $em)
    {

        // nouvelle entité Equipment
        $equipment = new Equipment;

        // formulaire d'ajout d'un matériel
        // ... sur lequel on "map" l'entité equipment
        $form = $this->createForm(FormEquipmentType::class, $equipment);

        // On demande au form de "prendre en charge" la requête
        $form->handleRequest($request);

        // Si form est soumis ? Est-il valide ?
        if ($form->isSubmitted() && $form->isValid()) {
            // A ce stade l'entité $equipment contient déjà toutes les infos du form :)
            // car mappées via le form depuis handleRequest()
            // On sauvegarde le matériel
            //$equipment = $form->getData()->getEquipmentCategories();
            //dd($equipment);

            $toSlug = $form->getData()->getName();

            $equipment->setSlug(strtolower($slugger->slug($toSlug)));
            $equipment->setCreatedAt(new \DateTime());
            $equipment->setUpdatedAt(new \DateTime());


            $em->persist($equipment);
            $em->flush($equipment);

            $this->addFlash('success', $messageGenerator->getHappyMessage());

            // On redirige vers la liste
            return $this->redirectToRoute('equipments_list');
        }

        return $this->render('back/equipment/edit.html.twig', [
            // createView() permet de récupérer
            // la représentation HTML du form
            'form' => $form->createView(),
            'cat' => 'AJOUT DE MATÉRIEL',
            'btnText' => 'Ajouter',
            'btnLabel' => 'bg-aqua_velvet',
            'ico' => 'plus'
        ]);
    }

    //Modification de matériel
    /**
     * @Route("/{id}/editer",name="equipment_edit")
     * @isGranted("ROLE_EDITOR", message="Vous n'avez pas accès à cette section !")
     */
    public function edit($id, EquipmentRepository $equipmentRepository, Request $request, SluggerInterface $slugger, EntityManagerInterface $em, MessageGenerator $messageGenerator)
    {
        $equipment = $equipmentRepository->find($id);


        if (!$equipment) {
            //Gérer les erreurs de requêtes 
            throw $this->createNotFoundException("Ce matériel n'existe pas !");
        }

        $form = $this->createForm(FormEquipmentType::class, $equipment);
        //$form->setData($equipment);

        $form->handleRequest($request);


        if ($form->isSubmitted() && $form->isValid()) {

            $toSlug = $form->getData()->getName();

            $equipment->setSlug(strtolower($slugger->slug($toSlug)));

            $equipment->setUpdatedAt(new \DateTime());

            $em->flush();

            $this->addFlash('success', $messageGenerator->getHappyMessage());

            // On redirige vers la liste
            return $this->redirectToRoute('equipment_show', [
                'type_slug' => $equipment->getEquipmentType()->getSlug(),
                'slug' => $equipment->getSlug(),
                'id' => $equipment->getId()
            ]);
        }


        return $this->render('back/equipment/edit.html.twig', [
            'form' => $form->createView(),
            'item' => $equipment,
            'cat' => 'MODIFIER LE MATÉRIEL',
            'btnText' => 'Modifier',
            'btnLabel' => 'bg-double_dragon_skin',
            'ico' => 'edit'


        ]);
    }
    //Suppression d'un matériel
    /**
     * @Route("/{id}/supprimer",name="equipment_delete")
     * @isGranted("ROLE_ADMIN", message="Votre rôle ne permet pas de supprimer ce matériel !")
     */
    public function equipmentDelete($id, Request $request, Equipment $equipment, DocumentRepository $documentRepository, FileManager $fileManager, EntityManagerInterface $em, PhotoManager $photoManager, EquipmentRepository $equipmentRepository)
    {
        $equipment = $equipmentRepository->findOneBy(['id' => $id]);
        if ($equipment) {

            //Vérification si matériel lié à des tâches
            $tasks = $equipment->getTasks();

            if ($tasks->isEmpty()) {

                //On supprime les documents liés
                $docs = $documentRepository->findBy(['Equipment' => $id]);
                foreach ($docs as $doc) {
                    $equipment->removeDocument($doc);
                    $em->remove($doc);
                    //Service from FileManager
                    $fileManager->delete($doc);
                }
                //Service from FileManager pour supprimer la photo
                $photoManager->delete($equipment->getUploadName());
                $equipment->setUploadName(NULL);

                $em->remove($equipment);
                $em->flush();

                $this->addFlash('success', 'Matériel supprimé !');
                return $this->redirectToRoute('equipments_list');
            } else {
                $this->addFlash('danger', 'Suppression impossible, ce matériel est lié à au moins une tâche !');
                return $this->redirectToRoute('equipment_show', [
                    'id' => $equipment->getId(),
                    'slug' => $equipment->getSlug(),
                    'type_slug' => $equipment->getEquipmentType()->getSlug()
                ]);
            }
        }
    }

    //Ajout d'un type de matériel
    /**
     * @Route("/type/creer", name = "equipment_type_create")
     * @isGranted("ROLE_EDITOR", message="Vous n'avez pas accès à cette section !")
     */
    public function equipmentTypeCreate(Request $request, EntityManagerInterface $em, SluggerInterface $slugger, MessageGenerator $messageGenerator)
    {
        $equipmentType = new EquipmentType;

        $form = $this->createForm(TypeEquipmentType::class, $equipmentType);

        $form->handleRequest($request);


        if ($form->isSubmitted() && $form->isValid()) {


            $toSlug = $form->getData()->getName();

            $equipmentType->setSlug(strtolower($slugger->slug($toSlug)));

            $em->persist($equipmentType);
            $em->flush($equipmentType);

            $this->addFlash('success', $messageGenerator->getHappyMessage());

            // On redirige vers la liste
            return $this->redirectToRoute('equipment_type', [
                'slug' => $equipmentType->getSlug()
            ]);
        }
        return $this->render('back/equipment/edit.html.twig', [
            // createView() permet de récupérer
            // la représentation HTML du form
            'form' => $form->createView(),
            'cat' => 'AJOUT DU TYPE DE MATÉRIEL',
            'btnText' => 'Ajouter',
            'btnLabel' => 'bg-aqua_velvet',
            'ico' => 'plus'

        ]);
    }

    //Modification de type

    /**
     * @Route("/type/{id}/editer",name="equipment_type_edit")
     * @isGranted("ROLE_EDITOR", message="Vous n'avez pas accès à cette section !")
     */
    public function typEdit($id, EquipmentTypeRepository $equipmentTypeRepository, Request $request, SluggerInterface $slugger, EntityManagerInterface $em, MessageGenerator $messageGenerator)
    {
        $equipmentType = $equipmentTypeRepository->find($id);


        if (!$equipmentType) {
            //Gérer les erreurs de requêtes 
            throw $this->createNotFoundException("Ce type n'existe pas !");
        }

        $form = $this->createForm(TypeEquipmentType::class, $equipmentType);
        //$form->setData($equipment);

        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {

            $toSlug = $form->getData()->getName();

            $equipmentType->setSlug(strtolower($slugger->slug($toSlug)));


            $em->flush();

            $this->addFlash('success', $messageGenerator->getHappyMessage());

            // On redirige vers la liste
            return $this->redirectToRoute('equipment_type', [

                'slug' => $equipmentType->getSlug()
            ]);
        }


        return $this->render('back/equipment/edit.html.twig', [
            'form' => $form->createView(),
            'item' => $equipmentType,
            'cat' => 'MODIFIER LE TYPE DE MATÉRIEL',
            'btnText' => 'Modifier',
            'btnLabel' => 'bg-double_dragon_skin',
            'ico' => 'edit'

        ]);
    }

    //Ajout d'une catégorie de matériel
    /**
     * @Route("/categorie/creer", name = "equipment_category_create")
     * @isGranted("ROLE_EDITOR", message="Vous n'avez pas accès à cette section !")
     */
    public function equipmentCatCreate(Request $request, EntityManagerInterface $em, SluggerInterface $slugger, MessageGenerator $messageGenerator)
    {
        $equipmentCat = new EquipmentCategory;

        $form = $this->createForm(EquipmentCategoryType::class, $equipmentCat);

        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {


            $toSlug = $form->getData()->getName();

            $equipmentCat->setSlug(strtolower($slugger->slug($toSlug)));

            $em->persist($equipmentCat);
            $em->flush($equipmentCat);

            $this->addFlash('success', $messageGenerator->getHappyMessage());

            // On redirige vers la liste
            return $this->redirectToRoute('equipment_category', [
                'slug' => $equipmentCat->getSlug()
            ]);
        }

        return $this->render('back/equipment/edit.html.twig', [
            // createView() permet de récupérer
            // la représentation HTML du form
            'form' => $form->createView(),
            'cat' => 'AJOUT D\'UNE CATÉGORIE DE MATÉRIEL',
            'btnText' => 'Ajouter',
            'btnLabel' => 'bg-aqua_velvet',
            'ico' => 'plus'
        ]);
    }

    //Modification de catégorie

    /**
     * @Route("/categorie/{id}/editer",name="equipment_category_edit")
     * @isGranted("ROLE_EDITOR", message="Vous n'avez pas accès à cette section !")
     */
    public function catEdit($id, EquipmentCategoryRepository $equipmentCategoryRepository, Request $request, SluggerInterface $slugger, EntityManagerInterface $em, MessageGenerator $messageGenerator)
    {
        $equipmentCat = $equipmentCategoryRepository->find($id);


        if (!$equipmentCat) {
            //Gérer les erreurs de requêtes 
            throw $this->createNotFoundException("Cette catégorie n'existe pas !");
        }

        $form = $this->createForm(EquipmentCategoryType::class, $equipmentCat);
        //$form->setData($equipment);

        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {

            $toSlug = $form->getData()->getName();

            $equipmentCat->setSlug(strtolower($slugger->slug($toSlug)));

            $em->flush();

            $this->addFlash('success', $messageGenerator->getHappyMessage());

            // On redirige vers la liste
            return $this->redirectToRoute('equipment_category', [

                'slug' => $equipmentCat->getSlug()
            ]);
        }


        return $this->render('back/equipment/edit.html.twig', [
            'form' => $form->createView(),
            'item' => $equipmentCat,
            'cat' => 'MODIFIER LA CATÉGORIE DE MATÉRIEL',
            'btnText' => 'Modifier',
            'btnLabel' => 'bg-double_dragon_skin',
            'ico' => 'edit'

        ]);
    }
    /**
     * @Route("/pret-materiel/{id}",name="equipment_rent", defaults={"id"=null})
     * @isGranted("ROLE_EDITOR", message="Vous n'avez pas accès à cette section !")
     */
    public function rent($id = "", RentedEquipmentRepository $rentedEquipmentRepository, Request $request, SluggerInterface $slugger, MessageGenerator $messageGenerator, EntityManagerInterface $em, EventDispatcherInterface $dispatcher)
    {
        if ($id == "") {
            // nouvelle entité Equipment
            $cat = 'PRET DE MATÉRIEL';
            $rent = new RentedEquipment();
        } else {
            $cat = "MODIFICATION DU PRET DE MATERIEL";
            $rent = $rentedEquipmentRepository->find($id);
        }



        // formulaire d'ajout d'un prêt
        // ... sur lequel on "map" l'entité RentedEquipment
        $form = $this->createForm(RentedEquipmentType::class, $rent);
        $form->handleRequest($request);

        // Si form est soumis ? Est-il valide ?
        if ($form->isSubmitted() && $form->isValid()) {
            // A ce stade l'entité $rent contient déjà toutes les infos du form :)
            // car mappées via le form depuis handleRequest()

            if ($id != "") {
                $rent->setUpdatedBy($this->getUser());
                $em->flush();
            } else {
                $rent->setCreatedBy($this->getUser());

                $em->persist($rent);
                $em->flush($rent);

                $this->addFlash('success', $messageGenerator->getHappyMessage());
            }
            // On redirige vers la liste
            return $this->redirectToRoute('add_equipment_to_rent', ['id' => $rent->getId()]);
        }

        return $this->render('back/equipment/edit.html.twig', [
            // createView() permet de récupérer
            // la représentation HTML du form
            'form' => $form->createView(),
            'cat' => $cat,
            'btnText' => 'Suivant',
            'btnLabel' => 'bg-aqua_velvet',
            'ico' => 'fa-solid fa-arrow-right-long'
        ]);
    }
    //Suppression d'une réservation
    /**
     * @Route("/pret-materiel/{id}/supprimer",name="rent_delete")
     * @isGranted("ROLE_EDITOR", message="Votre rôle ne permet pas de supprimer cette réservation !")
     */
    public function rentDelete($id, Request $request, RentedEquipment $rentedEquipment, RentedEquipmentRepository $rentedEquipmentRepository, EntityManagerInterface $em)
    {


        if ($this->isCsrfTokenValid('delete' . $rentedEquipment->getId(), $request->request->get('_token'))) {



            foreach ($rentedEquipment->getEquipment() as $equipment) {
                $rentedEquipment->removeEquipment($equipment);
            }

            $em->remove($rentedEquipment);
            $em->flush();
        }
        //Redirection vers la liste de réservations
        return $this->redirectToRoute('rentals_list');
    }

    /**
     * @Route("/{id}/ajouter/equipement", name="add_equipment_to_rent")
     * @isGranted("ROLE_EDITOR", message="Votre rôle ne permet pas de modifier cette demande !")
     */
    public function equipmentToTaskShow($id, RentedEquipmentRepository $rentRepository, EquipmentRepository $equipmentRepository, Request $request, MessageGenerator $messageGenerator)
    {

        $rent = $rentRepository->findOneBy([
            'id' => $id
        ]);
        if (!$rent) {
            //Gérer les erreurs de requêtes 
            throw $this->createNotFoundException("Cette demande n'existe pas !");
        }
        $equipmentList = $equipmentRepository->findAll();

        return $this->render('back/rent/rent_equipment_add.html.twig', [
            //'form' => $form->createView(),

            'equipmentList' => $equipmentList,
            'rent' => $rent,
            'cat' => 'Ajout de matériel à la demande ' . $rent->getId(),
            'btnText' => 'Ajouter',
            'btnLabel' => 'bg-aqua_velvet',
            'ico' => 'plus'

        ]);
    }
    /**
     * @Route("/{rid}/ajouter/equipement/{id}", name="rent_equipment_add")
     * @isGranted("ROLE_EDITOR", message="Votre rôle ne permet pas de modifier cette demande !")
     */
    public function addEquipmentToTask($rid, $id, RentedEquipmentRepository $rentRepository, EquipmentRepository $equipmentRepository, EntityManagerInterface $em)
    {
        $rent = $rentRepository->findOneBy([
            'id' => $rid
        ]);

        $equipmentList = $equipmentRepository->findAll();
        if (!$rent) {
            //Gérer les erreurs de requêtes 
            throw $this->createNotFoundException("Cette demande n'existe pas !");
        } else {
            $equipment = $equipmentRepository->find($id);

            if ($equipment) {


                //AJOUT DU MATERIEL A LA DEMANDE
                $rent->addEquipment($equipment);
                $em->flush();

                // redirect to a route with parameters
                return $this->redirectToRoute('add_equipment_to_rent', [

                    'equipmentList' => $equipmentList,
                    'rent' => $rent,
                    'id' => $rent->getId(),
                    'cat' => 'Ajout de matériel à la demande ' . $rent->getId(),
                    'btnText' => 'Ajouter',
                    'btnLabel' => 'bg-aqua_velvet',
                    'ico' => 'plus'
                ]);
            } else {
                //Gérer les erreurs de requêtes 
                throw $this->createNotFoundException("Problème de liaison avec la base de donnée !");
            }
        }
    }
    /**
     * @Route("/{rid}/retirer/equipement/{id}", name="rent_equipment_remove")
     * @isGranted("ROLE_EDITOR", message="Votre rôle ne permet pas de modifier cette demande !")
     */
    public function RemoveEquipmentFromTask($rid, $id, RentedEquipmentRepository $rentRepository, EquipmentRepository $equipmentRepository, EntityManagerInterface $em)
    {
        $rent = $rentRepository->findOneBy([
            'id' => $rid
        ]);

        $equipmentList = $equipmentRepository->findAll();
        if (!$rent) {
            //Gérer les erreurs de requêtes 
            throw $this->createNotFoundException("Cette demande n'existe pas !");
        } else {
            $equipment = $equipmentRepository->find($id);

            if ($equipment) {
                //AJOUT DU MATERIEL A LA TACHE
                $rent->removeEquipment($equipment);

                $em->flush();

                return $this->redirectToRoute('add_equipment_to_rent', [

                    'equipmentList' => $equipmentList,
                    'rent' => $rent,
                    'id' => $rent->getId(),
                    'cat' => 'Ajout de matériel à la demande ' . $rent->getId(),
                    'btnText' => 'Ajouter',
                    'btnLabel' => 'bg-aqua_velvet',
                    'ico' => 'plus'
                ]);
            } else {
                //Gérer les erreurs de requêtes 
                throw $this->createNotFoundException("Problème de liaison avec la base de donnée !");
            }
        }
    }
    // FIN DE SECTION ADMIN
    //*******************************************************
    //*******************************************************


    // LISTE DU MATERIEL

    /**
     * @Route("/", name="equipments_list")
     */
    public function equipmentList(EquipmentRepository $equipmentRepository)
    {
        $emptyList = false;
        $equipmentList = $equipmentRepository->findAll();

        if (!$equipmentList) {
            $emptyList = true;
        }
        return $this->render('/back/equipment/equipments_list.html.twig', [
            'equipmentList' => $equipmentList,
            'emptyList' => $emptyList,
            'missing' => false,
            'rented' => false,
            'cat' => "Inventaire du matériel audiovisuel."
        ]);
    }
    /**
     * @Route("/accueil", name="equipments_list_2")
     */
    public function equipmentList2(EquipmentRepository $equipmentRepository)
    {
        $emptyList = false;
        $equipmentList = $equipmentRepository->findAll();

        if (!$equipmentList) {
            $emptyList = true;
        }
        $cats = "";
        if ($equipmentList) {
            //Filters by category
            $equipmentCatName = [];
            foreach ($equipmentList as $equipment) {
                $cat = $equipment->getEquipmentType()->getName();
                array_push($equipmentCatName, $cat);
            }
            $array2 = array_count_values($equipmentCatName);

            $cats = array_keys($array2);
        }
        return $this->render('/back/equipment/equipments_list_2.html.twig', [
            'equipmentList' => $equipmentList,
            'emptyList' => $emptyList,
            'missing' => false,
            'rented' => false,
            'cat' => "Inventaire du matériel audiovisuel.",
            'cats' => $cats
        ]);
    }
    /**
     * @Route("/manquant", name="missing_equipments_list")
     */
    public function missingEquipmentList(EquipmentRepository $equipmentRepository)
    {
        $emptyList = false;
        $equipmentList = $equipmentRepository->missingEquipments();

        if (!$equipmentList) {
            $emptyList = true;
        }
        return $this->render('/back/equipment/equipments_list.html.twig', [
            'equipmentList' => $equipmentList,
            'emptyList' => $emptyList,
            'missing' => true,
            'rented' => false,
            'cat' => "Inventaire du matériel manquant."
        ]);
    }
    /**
     * @Route("/prets", name="rented_equipments_list")
     */
    public function rentedEquipmentList(EquipmentRepository $equipmentRepository)
    {
        $emptyList = false;
        $equipmentList = $equipmentRepository->ratedEquipments();

        if (!$equipmentList) {
            $emptyList = true;
        }
        return $this->render('/back/equipment/equipments_list.html.twig', [
            'equipmentList' => $equipmentList,
            'emptyList' => $emptyList,
            'missing' => false,
            'rented' => true,
            'cat' => "Inventaire du matériel en prêt."
        ]);
    }


    // LISTE DES CATEGORIES DU MATERIEL

    /**
     * @Route("/categorie/{slug}", name ="equipment_category")
     */
    public function category($slug, EquipmentCategoryRepository $equipmentCategoryRepository)
    {
        $category = $equipmentCategoryRepository->findOneBy([
            'slug' => $slug
        ]);

        if (!$category) {
            //Gérer les erreurs de requêtes 
            throw $this->createNotFoundException("La catégorie demandée n'existe pas !");
        }

        return $this->render('back/equipment/equipment_categories.html.twig', [
            'category' => $category
        ]);
    }

    // LISTE DES TYPES DU MATERIEL

    /**
     * @Route("/type/{slug}", name ="equipment_type")
     */
    public function type($slug, EquipmentTypeRepository $equipmentTypeRepository)
    {
        $type = $equipmentTypeRepository->findOneBy([
            'slug' => $slug
        ]);

        if (!$type) {
            //Gérer les erreurs de requêtes 
            throw $this->createNotFoundException("Le type demandé n'existe pas !");
        }

        return $this->render('back/equipment/equipment_types.html.twig', [
            'type' => $type
        ]);
    }

    // AFFICHER UN MATERIEL
    /**
     * @Route("/{type_slug}/{slug}/{id}", name="equipment_show")
     */
    public function show($id, $slug, EquipmentRepository $equipmentRepository, Request $request, FileManager $fileManager, EntityManagerInterface $em, PhotoManager $photoManager)
    {
        $equipment = $equipmentRepository->findOneBy([
            'id' => $id,
            'slug' => $slug
        ]);

        if (!$equipment) {
            throw $this->createNotFoundException("Ce matériel n'existe pas !");
        }
        $form = $this->createForm(UploadFileType::class);
        $form->handleRequest($request);


        $photoForm = $this->createForm(UploadPhotoType::class);
        $photoForm->handleRequest($request);

        if ($this->isGranted("ROLE_EDITOR")) {

            if ($form->isSubmitted() && $form->isValid()) {
                //gestion du chargement de fichier via //FileManager service 
                $document = $form->get('uploadName')->getData();


                if ($document != null) {
                    $FileName = $fileManager->upload($document);

                    $document = new Document;
                    $document->setUploadName($FileName);
                    //$document->setProject($project);
                    //$document->setTask($task);
                    $document->setEquipment($equipment);
                    $document->setUploadedBy($this->getUser());

                    $em->persist($document);

                    $em->flush();
                }
                return $this->redirectToRoute('equipment_show', [
                    'id' => $id,
                    'slug' => $equipment->getSlug(),
                    'type_slug' => $equipment->getEquipmentType()->getSlug()
                ]);
            }
            if ($photoForm->isSubmitted() && $photoForm->isValid()) {
                //gestion du chargement de la photo via //FileManager service
                $photo = $photoForm->get('uploadPhotoName')->getData();
                if ($photo != null) {
                    $photoName = $photoManager->upload($photo);
                    $equipment->setUploadName($photoName);

                    $em->persist($equipment);

                    $em->flush();

                    return $this->redirectToRoute('equipment_show', [
                        'id' => $id,
                        'slug' => $equipment->getSlug(),
                        'type_slug' => $equipment->getEquipmentType()->getSlug()
                    ]);
                }
            }
        }

        return $this->render('back/equipment/equipment_show.html.twig', [
            'equipment' => $equipment,
            'form' => $form->createView(),
            'photoForm' => $photoForm->createView(),
            'btnText' => 'Ajouter un document',
            'btnLabel' => 'bg-aqua_velvet',
            'btnText2' => 'Ajouter une photo',
            'btnLabel2' => 'bg-pastel_chart_0',
            'ico' => 'plus'
        ]);
    }

    /**
     * @Route("/{eid}/document/{id}/download", name="equipment_document_download")
     */
    public function downloadEquipmentDocument($eid, $id, Request $request, Document $document, FileManager $fileManager, DocumentRepository $documentRepository, EquipmentRepository $equipmentRepository)
    {
        $valid = $documentRepository->findOneBy(['id' => $id, 'Equipment' => $eid]);
        if ($valid) {
            $filePath = $fileManager->download($document);

            if ($filePath) {
                return $this->file($filePath);
            }
        } else {

            throw $this->createNotFoundException("Erreur de chargement du fichier !");
        }
    }

    /**
     * @Route("/{eid}/document/{id}/supprimer", name="equipment_document_remove", methods={"DELETE"})
     * @isGranted("ROLE_EDITOR", message="Vous n'avez pas accès à cette fonctionnalité !")
     */
    public function removeDocumentFromEquipment($eid, $id, Request $request, Document $document, FileManager $fileManager, EntityManagerInterface $em, EquipmentRepository $equipmentRepository)
    {
        $equipment = $equipmentRepository->findOneBy(['id' => $eid]);

        if ($this->isCsrfTokenValid('delete' . $document->getId(), $request->request->get('_token'))) {

            //Service from FileManager
            $fileManager->delete($document);


            $em->remove($document);
            $em->flush();
        }

        return $this->redirectToRoute('equipment_show', [
            'id' => $equipment->getId(),
            'slug' => $equipment->getSlug(),
            'type_slug' => $equipment->getEquipmentType()->getSlug()
        ]);
    }
    /**
     * @Route("{id}/photo/supprimer", name="equipment_photo_remove", methods={"DELETE"})
     * @isGranted("ROLE_EDITOR", message="Vous n'avez pas accès à cette fonctionnalité !")
     */
    public function removephotoFromEquipment($id, Request $request, PhotoManager $photoManager, EntityManagerInterface $em, EquipmentRepository $equipmentRepository)
    {
        $equipment = $equipmentRepository->findOneBy(['id' => $id]);

        if ($this->isCsrfTokenValid('delete' . $equipment->getId(), $request->request->get('_token'))) {

            //Service from FileManager
            $photoManager->delete($equipment->getUploadName());


            $equipment->setUploadName(NULL);
            $em->flush();
        }

        return $this->redirectToRoute('equipment_show', [
            'id' => $equipment->getId(),
            'slug' => $equipment->getSlug(),
            'type_slug' => $equipment->getEquipmentType()->getSlug()
        ]);
    }

    //LISTE DES DEMANDES DE PRET

    /**
     * @Route("/liste-prêts", name="rentals_list")
     */
    public function rentalsList(RentedEquipmentRepository $rentedEquipment)
    {
        $emptyList = false;
        $rentalsList = $rentedEquipment->findAll();

        if (!$rentalsList) {
            $emptyList = true;
        }
        return $this->render('/back/rent/rentals_list.html.twig', [
            'rentalsList' => $rentalsList,
            'emptyList' => $emptyList
        ]);
    }
}
