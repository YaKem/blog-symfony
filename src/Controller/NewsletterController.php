<?php

namespace App\Controller;

use App\Entity\Newsletter;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\Routing\Annotation\Route;

class NewsletterController extends AbstractController
{
    /**
     * @Route("/newsletter", name="app_newsletter", methods={"GET", "POST"})
     */
    public function addNewsletter(Request $request, EntityManagerInterface $em): Response
    {
        $newsletter = new Newsletter();

        $newsletter->setEmail($request->request->get('email'));
        $newsletter->setActive(true);

        $em->persist($newsletter);
        $em->flush();

        $this->addFlash('info', 'Merci de nous avoir rejoint!');
        
        return $this->redirect($request->headers->get('referer'));
    }
}
