<?php


namespace App\Services\Email;


use App\Model\Contact;
use Symfony\Bundle\MonologBundle\SwiftMailer\MessageFactory;

class ContactMailer
{
    /**
     * @var \Swift_Mailer
     */
    private $mailer;
    //

    /**
     * ContactMailer constructor.
     * @param \Swift_Mailer $mailer
     */
    public function __construct(\Swift_Mailer $mailer)
    {
        $this->mailer = $mailer;
    }
    //
    public function send(Contact $contact)
    {
        $message =new \Swift_Message();
        $message= new MessageFactory();
    }
    //


}