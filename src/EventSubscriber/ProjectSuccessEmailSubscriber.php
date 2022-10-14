<?php

namespace App\EventSubscriber;

use App\Event\ApplicantRatingSuccessEvent;
use App\Event\ProjectSuccessEvent;
use Symfony\Component\Mime\Address;
use Symfony\Bridge\Twig\Mime\TemplatedEmail;
use Symfony\Component\Mailer\MailerInterface;
use Symfony\Component\Security\Core\Security;
use Symfony\Component\EventDispatcher\EventSubscriberInterface;
use App\Entity\LogEvent;
use Doctrine\ORM\EntityManagerInterface;

class ProjectSuccessEmailSubscriber implements EventSubscriberInterface
{
    protected $mailer;
    protected $security;
    protected $adminEmail;
    protected $teamEmail;
	protected $em;


    public function __construct(MailerInterface $mailer, Security $security, string $adminEmail, string $teamEmail, EntityManagerInterface $em) {

        $this->adminEmail = $adminEmail;
        $this->mailer = $mailer;
        $this->security = $security;
        $this->teamEmail = $teamEmail;
		$this->em = $em;
        
        
    }

    
    public function sendSuccessEmail(ProjectSuccessEvent $projectSuccessEvent)
    {
        //On récupère le current user grace au composent security
        /**@var User */
        $currentUser = $this->security->getUser();
        //On récupère également le projet 
        $project = $projectSuccessEvent->getProject();
		//******************************************
		//Log Event
		//******************************************
		$logEvent = new LogEvent;
		
	    $logEvent->setProject($project);
		$logEvent->setCreatedBy($currentUser);
		$logEvent->setType('Nouveau projet');
		
		$this->em->persist($logEvent);
		$this->em->flush($logEvent);
		//******************************************
		
       $email = (new TemplatedEmail())
            ->from(new Address($this->adminEmail))
            ->to($currentUser->getEmail(), $this->teamEmail )
            ->subject('Nouveau projet sur GDP')
            ->htmlTemplate('mail/email_template.html.twig')
            ->context([
                'project' => $project,
                'user' => $currentUser,
                'mailType' => 'new_project'
            ])
        ;
        $this->mailer->send($email);
		

    }
    public function sendSuccessUpdateEmail(ProjectSuccessEvent $projectSuccessEvent)
    {
        //On récupère le current user grace au composent security
        /**@var User */
       $currentUser = $this->security->getUser();
        //On récupère également le projet 
       $project = $projectSuccessEvent->getProject();
        //******************************************
		//Log Event
		//******************************************
		$logEvent = new LogEvent;
		
	    $logEvent->setProject($project);
		$logEvent->setCreatedBy($currentUser);
		$logEvent->setType('Projet mis à jour');
		
		$this->em->persist($logEvent);
		$this->em->flush($logEvent);
		//******************************************
       $email = (new TemplatedEmail())
            ->from(new Address($this->adminEmail))
            ->to($currentUser->getEmail(), $this->teamEmail )
            ->subject('Projet mis à jour sur GDP')
            ->htmlTemplate('mail/email_template.html.twig')
            ->context([
                'project' => $project,
                'user' => $currentUser,
                'mailType' => 'updated_project'
            ])
        ;
        //$this->mailer->send($email);

    }

    public function sendSuccessCommentedEmail(ProjectSuccessEvent $projectSuccessEvent)
    {
        //On récupère le current user grace au composent security
        /**@var User */
       $currentUser = $this->security->getUser();
        //On récupère également le projet 
       $project = $projectSuccessEvent->getProject();
        //******************************************
        //Log Event
        //******************************************
        $logEvent = new LogEvent;
        
        $logEvent->setProject($project);
        $logEvent->setCreatedBy($currentUser);
        $logEvent->setType('Nouvelle actualité');
        
        $this->em->persist($logEvent);
        $this->em->flush($logEvent);
        //******************************************
       $email = (new TemplatedEmail())
            ->from(new Address($this->adminEmail))
            ->to($currentUser->getEmail(), $this->teamEmail )
            ->subject('Nouvelle actualité sur le projet '.$project->getName())
            ->htmlTemplate('mail/email_template.html.twig')
            ->context([
                'project' => $project,
                'user' => $currentUser,
                'mailType' => 'commented_project'
            ])
        ;
        //$this->mailer->send($email);

    }

    public function sendSuccessRatedEmail(ProjectSuccessEvent $projectSuccessEvent)
    {
        //On récupère le current user grace au composent security
        /**@var User */
        $currentUser = $this->security->getUser();
        //On récupère également le projet 
        $project = $projectSuccessEvent->getProject();
        //******************************************
        //Log Event
        //******************************************
        $logEvent = new LogEvent;
        
        $logEvent->setProject($project);
        $logEvent->setCreatedBy($currentUser);
        $logEvent->setType('Projet noté');
        
        $this->em->persist($logEvent);
        $this->em->flush($logEvent);
        
    }

    public function sendSuccessUpdatedCommentEmail(ProjectSuccessEvent $projectSuccessEvent)
    {
        //On récupère le current user grace au composent security
        /**@var User */
        $currentUser = $this->security->getUser();
        //On récupère également le projet 
        $project = $projectSuccessEvent->getProject();
        //******************************************
        //Log Event
        //******************************************
        $logEvent = new LogEvent;
        
        $logEvent->setProject($project);
        $logEvent->setCreatedBy($currentUser);
        $logEvent->setType('Actualité mise à jour');
        
        $this->em->persist($logEvent);
        $this->em->flush($logEvent);
    }

    public function sendSuccessDocumentUploaded(ProjectSuccessEvent $projectSuccessEvent)
    {
        //On récupère le current user grace au composent security
        /**@var User */
        $currentUser = $this->security->getUser();
        //On récupère également le projet 
        $project = $projectSuccessEvent->getProject();
        //******************************************
        //Log Event
        //******************************************
        $logEvent = new LogEvent;
        
        $logEvent->setProject($project);
        $logEvent->setCreatedBy($currentUser);
        $logEvent->setType('Upload de fichier');
        
        $this->em->persist($logEvent);
        $this->em->flush($logEvent);
    }
    public function sendRatingEmail(ApplicantRatingSuccessEvent $projectRateByApplicant)
    {
        //On récupère le current user grace au composent security
        /**@var User */
        $currentUser = $this->security->getUser();
        //On récupère également le projet
        $rating = $projectRateByApplicant->getProject();
        $projectToRate = $projectRateByApplicant->getProject()->getProject();
        $applicant = $projectRateByApplicant->getProject()->getApplicant();
        $applicantMail = $projectRateByApplicant->getProject()->getApplicant()->getEmail();
		//******************************************
		//Log Event
		//******************************************
		$logEvent = new LogEvent;
		
	    $logEvent->setProject($projectToRate);
		$logEvent->setCreatedBy($currentUser);
		$logEvent->setType('Demande de notation du projet');
		
		$this->em->persist($logEvent);
		$this->em->flush($logEvent);
		//******************************************
		
       $email = (new TemplatedEmail())
            ->from(new Address($this->teamEmail))
            //A modifier une fois validé par $project->getRequestBy()->getMail()
            ->to($applicantMail)
            ->subject('Votre avis sur le projet : '.$projectToRate->getName())
            ->htmlTemplate('mail/applicant_email_template.html.twig')
            ->context([
                'project' => $projectToRate,
                'rating' => $rating,
                'user' => $applicant,
                'mailType' => 'new_applicant_notation'
            ])
        ;
        $this->mailer->send($email);
		
    }
    public function sendRatedEmail(ProjectSuccessEvent $projectSuccessEvent){

       $project = $projectSuccessEvent->getProject();
        //******************************************
        //Log Event
        //******************************************
        $logEvent = new LogEvent;

        $logEvent->setProject($project);
        $logEvent->setCreatedBy($project->getCreatedBy());
        $logEvent->setRatedBy($project->getRequestBy());
        $logEvent->setType('Notation du projet');

        $this->em->persist($logEvent);
        $this->em->flush($logEvent);
    }

    public static function getSubscribedEvents()
    {
        return[
            'project.success' => 'sendSuccessEmail',
            'project.updated' => 'sendSuccessUpdateEmail',
            'project.commented' => 'sendSuccessCommentedEmail',
            'project.rating' => 'sendSuccessRatedEmail',
            'projectComment.update' => 'sendSuccessUpdatedCommentEmail',
            'projectDocument.upload' => 'sendSuccessDocumentUploaded',
            'project.toRate' => 'sendRatingEmail',
            'project.rated' => 'sendRatedEmail',
        ];
    }
}