<?php

namespace App\Event;

use App\Entity\ProjectRateByApplicant;
use Symfony\Contracts\EventDispatcher\Event;

class ApplicantRatingSuccessEvent extends Event
{
    private $projectRateByApplicant;

    public function __construct(ProjectRateByApplicant $projectRateByApplicant) {
        $this->projectRateByApplicant = $projectRateByApplicant;
    }
    public function getProject() : ProjectRateByApplicant
    {
        return $this->projectRateByApplicant;
    }
}