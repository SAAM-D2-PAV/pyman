<?php

namespace App\Controller\BackController;

use App\Repository\DocumentRepository;
use Knp\Component\Pager\PaginatorInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

use Sensio\Bundle\FrameworkExtraBundle\Configuration\IsGranted;

/**
 * @Route("/documents")
 * @isGranted("ROLE_ADMIN", message="Connexion impossible !")
 */
class DocumentController extends AbstractController
{

    #[Route('/', name: 'documents_show')]
    public function show(DocumentRepository $documentRepository,PaginatorInterface $paginatorInterface, Request $request): Response
    {
        $docs = $documentRepository->findAll();

        //Nombre de projets par année
        $docsCount = count($docs);

        $docsPaginated = $paginatorInterface->paginate($docs, $request->query->getInt('page',1), 1000);


        return $this->render('back/document/index.html.twig', [
            'docs' => $docsPaginated,
            'docsCount' => $docsCount
        ]);
    }
}
