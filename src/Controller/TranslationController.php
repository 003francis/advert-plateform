<?php

namespace App\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\Translation\Translator;
use Symfony\Contracts\Translation\TranslatorInterface;

///
///
/*
 * Ce contrôleur va servir à traduire les pages
 */
class TranslationController extends AbstractController
{
    /*
     * @Route("/translation", name="translation")
     *
     */
    //@Route("/translation/{_locale}", name="translation")
    public function index(TranslatorInterface $translator)
    {
        //
        $traduction= $translator->trans('Symfony is great');
        //

        return $this->render('translation/index.html.twig', array(
            'traduction' => $traduction,
        ));
    }
}
