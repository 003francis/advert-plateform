<?php

namespace App\Controller;

use App\Form\ContactType;
use App\Model\Contact;
use App\Services\Antispam\Antispam;
use App\Services\Messages\MessageGenerator;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\Form\FormFactoryInterface;
use Symfony\Component\HttpFoundation\RedirectResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpFoundation\Session\Flash\FlashBagInterface;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\Routing\RouterInterface;

class ServiceController extends AbstractController
{
    /**
     * @Route("/service", name="service")
     */
    ///
    /// Depuis Symfony4, Toute Classe qui est hors de Dossiers: Controller, Entity, Migrations
    /// peut être utilisée comme un service...
    /// Pour l'utiliser comme tel, il suffit de faire
    ///  une Injection de dépendance dans une Action du Controller qui veut l'utiliser
    public function index(Antispam $antispam)
    {
        ///Grâce à l'injection de dépendance, le container va instancier
        /// un Objet Antispam et nous le passer
        ///
         /*
        $messageGenerator=$messageGenerator->getHappyMessage();
        $this->addFlash('success', $messageGenerator);
        //

        return $this->render('service/index.html.twig', [
            'controller_name' => 'ServiceController',
        ]);
        */
         //On récupère le service
      #  $antispam2= $this->get('service_antispam');
         //
        $text="Salut à tous...Moi, c'est Francis N'TATA!!!!!
                J'apprends depuis un certain temps à programmer 
                En PHP, en utilisant Symfony, qui est un de ses Frameworks Professionnels...";
        $textSpam="Ce Texte est un SPAM";
        if ($antispam->isSpam($text)){
            ///
            throw new \Exception('Votre Message: "'.$text.'" a été détecté comme SPAM!!!');
        }
        return new Response('Votre Message: "'.$text.'" n\'est pas SPAM');
        //
    }
    ///

    /**
     * @Route("/contact", name="contact")
     */
    public function contact(Request $request,
                            FormFactoryInterface $formFactory,
                            RouterInterface $router)
    {
        //
        $contact= new Contact();
        //
        $form=$formFactory->create(ContactType::class, $contact);
        //
        $form->handleRequest($request);
        if ($form->isSubmitted() and $form->isValid()){
            $this->addFlash('envoiFormulaire', "Votre formulaire a bien été envoyé!");
            //
            return new RedirectResponse($router->generate("contact"));
        }
        //
        return $this->render('service/index.html.twig', array(
            'form'=>$form->createView()
        ));
    }
    ///

    //
}
