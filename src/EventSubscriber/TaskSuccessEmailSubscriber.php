<?php

namespace App\EventSubscriber;


use App\Event\TaskSuccessEvent;
use Symfony\Component\Mime\Address;
use Symfony\Bridge\Twig\Mime\TemplatedEmail;
use Symfony\Component\Mailer\MailerInterface;
use Symfony\Component\Security\Core\Security;
use Symfony\Component\EventDispatcher\EventSubscriberInterface;
use App\Entity\LogEvent;
use Doctrine\ORM\EntityManagerInterface;

class TaskSuccessEmailSubscriber implements EventSubscriberInterface
{
    protected $mailer;
    protected $security;
    protected $adminEmail;
    protected $teamEmail;


    public function __construct(MailerInterface $mailer, Security $security, string $adminEmail, string $teamEmail, EntityManagerInterface $em) {

        $this->adminEmail = $adminEmail;
        $this->mailer = $mailer;
        $this->security = $security;
        $this->teamEmail = $teamEmail;
        $this->em = $em;
        
    }


    /**
     * @throws \Symfony\Component\Mailer\Exception\TransportExceptionInterface
     */
    public function sendSuccessEmail(TaskSuccessEvent $taskSuccessEvent)
    {
        //On récupère le current user grace au composent security
        /**@var User */
       $currentUser = $this->security->getUser();
        //On récupère également la tâche 
       $task = $taskSuccessEvent->getTask();

        //******************************************
		//Log Event
		//******************************************
		$logEvent = new LogEvent;
		
	    $logEvent->setTask($task);
		$logEvent->setCreatedBy($currentUser);
		$logEvent->setType('Nouvelle tâche');
		
		$this->em->persist($logEvent);
		$this->em->flush($logEvent);
		//******************************************

       $subscriber = $task->getOwners()->getValues();
       $toAddresses = [];

       if (!empty($subscriber)) {
            $toAddresses = [];
            foreach ($subscriber as $sub_mail) {
                $toAddresses[] = $sub_mail->getEmail();
            }
        
            $email = (new TemplatedEmail())
                ->from(new Address($this->adminEmail))
                ->to(...$toAddresses)
                ->subject('Une nouvelle tâche vous a été assignée sur GDP')
                ->htmlTemplate('mail/email_template.html.twig')
                ->context([
                    'task' => $task,
                    'user' => $currentUser,
                    'mailType' => 'new_task'
                ])
            ;
        $this->mailer->send($email);
       }
    }

    public function sendSuccessUpdateEmail(TaskSuccessEvent $taskSuccessEvent)
    {
        //On récupère le current user grace au composent security
        /**@var User */
       $currentUser = $this->security->getUser();
        //On récupère également la tâche 
       $task = $taskSuccessEvent->getTask();

       $subscriber = $task->getOwners();

       //******************************************
		//Log Event
		//******************************************
		$logEvent = new LogEvent;
		
	    $logEvent->setTask($task);
		$logEvent->setCreatedBy($currentUser);
		$logEvent->setType('Tâche mise à jour');
		
		$this->em->persist($logEvent);
		$this->em->flush($logEvent);
		//******************************************
       
        $toAddresses = [];
        if (!empty($subscriber->getValues())) {

            foreach ($subscriber as $sub_mail) {
                $toAddresses[] = $sub_mail->getEmail();
            }

            $email = (new TemplatedEmail())
            ->from(new Address($this->adminEmail))
            ->to(...$toAddresses )
            ->subject('Tâche mise à jour sur GDP')
            ->htmlTemplate('mail/email_template.html.twig')
            ->context([
                'task' => $task,
                'user' => $currentUser,
                'mailType' => 'updated_task'
            ])
            ;
            //$this->mailer->send($email);
        }
    }

    public function sendEraseMail(TaskSuccessEvent $taskSuccessEvent)
    {
       //On récupère le current user grace au composent security
        /**@var User */
        $currentUser = $this->security->getUser();
        //On récupère également la tâche 
       $task = $taskSuccessEvent->getTask();

       $subscriber = $task->getOwners();
        $toAddresses = [];

        if (!empty($subscriber->getValues())) {

            foreach ($subscriber as $sub_mail) {
                $toAddresses[] = $sub_mail->getEmail();
            }
    
            $email = (new TemplatedEmail())
                    ->from(new Address($this->adminEmail))
                    ->to(...$toAddresses )
                    ->subject('Tâche supprimée sur GDP')
                    ->htmlTemplate('mail/email_template.html.twig')
                    ->context([
                        'task' => $task,
                        'user' => $currentUser,
                        'mailType' => 'deleted_task'
                    ])
            ;
            $this->mailer->send($email);
        }

        

    }

    public function sendSuccessDocumentUploaded(TaskSuccessEvent $taskSuccessEvent)
    {
        //On récupère le current user grace au composent security
        /**@var User */
        $currentUser = $this->security->getUser();
        //On récupère également la tâche 
        $task = $taskSuccessEvent->getTask();
        //******************************************
        //Log Event
        //******************************************
        $logEvent = new LogEvent;
        
        $logEvent->setTask($task);
        $logEvent->setCreatedBy($currentUser);
        $logEvent->setType('Upload de fichier');
        
        $this->em->persist($logEvent);
        $this->em->flush($logEvent);
    }

    public static function getSubscribedEvents()
    {
        return[
            'task.success' => 'sendSuccessEmail',
            'task.updated' => 'sendSuccessUpdateEmail',
            'task.deleted' => 'sendEraseMail',
            'taskDocument.upload' => 'sendSuccessDocumentUploaded'
        ];
    }
}