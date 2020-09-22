<?php

namespace App\Controller;

use App\Entity\Contact;
use App\Form\ContactType;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\Routing\Annotation\Route;

class ContactController extends AbstractController
{
    /**
     * @Route("/contact", name="app_contact")
     */
    public function form(Request $request, EntityManagerInterface $em): Response
    {
        $contact = new Contact();

        $formContact = $this->createForm(ContactType::class, $contact);

        $formContact->handleRequest($request);

        if($formContact->isSubmitted() && $formContact->isValid()) {
            $contact->setCreatedAt(new \DateTime());

            $em->persist($contact);
            $em->flush();

            $this->addFlash('success', 'Votre message est envoyé, nous allons vous contacter rapidement!');

            return $this->redirectToRoute('app_contact');
        }

        return $this->render('article/contact.html.twig', [
            'formContact' => $formContact->createView(),
            'categories' => $this->categories
        ]);
    }
}
