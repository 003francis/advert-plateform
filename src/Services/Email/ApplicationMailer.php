<?php


namespace App\Services\Email;

use App\Entity\Application;
use Symfony\Bundle\MonologBundle\SwiftMailer;
//
class ApplicationMailer
{
    /**
     * @var \Swift_Mailer
     */
    private $mailer;

    public function __construct(\Swift_Mailer $mailer)
    {
        $this->mailer=$mailer;
    }

    public function sendNewNotification(Application $application)
    {
        
    }

}