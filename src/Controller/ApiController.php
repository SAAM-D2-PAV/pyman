<?php

namespace App\Controller;

use App\Repository\EquipmentRepository;
use App\Repository\TaskRepository;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\IsGranted;

// * @isGranted("ROLE_VIEWER", message="Vous devez être connecté !")
/**
* @Route("/apip")
 */
class ApiController extends AbstractController
{
   

    /**
     * 
     * @Route("/setEquipment", name="api_provider_equipment", methods={"put"})
     * @isGranted("ROLE_EDITOR", message="Vous devez être connecté !")
     */
    public function addEquipment(Request $request, TaskRepository $taskRepository, EquipmentRepository $equipmentRepository, EntityManagerInterface $em): Response
    {
        
        $data = json_decode( $request->getContent());
        
        if($data){
        
            $params = $data->{'params'};
            //retrun 
            //{action: 'addEq_ToTask', parameter:  {tid: '13', eid: 1}}
            $action = $params->{'action'};  
            //return addEq_ToTask
            $parameters = $params->{'parameters'};
            //return {tid: '13', eid: 1}
            $taskId = $parameters->{'tid'};
            //return 13
            $equipmentId = $parameters->{'eid'};

            $task = $taskRepository->findOneBy([
                'id' => $taskId
            ]);
            //VOTER TaskVoter
            //$this->denyAccessUnlessGranted('TASK_EDIT',$task,'Vous ne pouvez pas modifier cette tâche');

            if (!$task) {
                //Gérer les erreurs de requêtes
                return $this->json('error',404);
            }
            else {
                $equipment = $equipmentRepository->find($equipmentId);

                if ($equipment) {


                    if ($action == "addEq_ToTask") {
                        //VERIFICATION ET AJOUT DU MATERIEL A LA TACHE
                        $equipments = $task->getEquipment();
                        foreach ($equipments as $eq) {
                            if ($equipment == $eq) {
                                return $this->json("linked", 200, [], []);
                            }
                        }
                        $task->addEquipment($equipment);
                        $task->setUpdatedBy($this->getUser());
                        $task->getProject()->setUpdatedAt(new \DateTime());
                        $task->getProject()->setUpdatedBy($this->getUser());

                        $em->flush();
                        

                        return $this->json($equipment, 200, [], []);
                    }
                    elseif ("RemEq_ToTask") {
                        //VERIFIVCATION ET SUPPRESSION DU MATERIEL DE LA TACHE

                        $equipments = $task->getEquipment();
                       
                        $task->removeEquipment($equipment);
                        $task->setUpdatedBy($this->getUser());
                        $task->getProject()->setUpdatedAt(new \DateTime());
                        $task->getProject()->setUpdatedBy($this->getUser());

                        $em->flush();

                        return $this->json("Matériel supprimé", 200, [], []);
                    }
                    else{
                        //Gérer les erreurs de requêtes
                        return $this->json('wrong action',404);
                    }
                    

                }
                else{
                    //Gérer les erreurs de requêtes
                    return $this->json('error',404);
                }

            }
        }
        else{
            return $this->json('error',404);
        }
    }
}
