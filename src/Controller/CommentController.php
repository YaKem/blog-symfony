<?php

namespace App\Controller;

use App\Entity\Comment;
use App\Repository\CommentRepository;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;

class CommentController extends AbstractController
{
    /**
     * @Route("/comment", name="comment")
     */
    public function index(CommentRepository $repo)
    {
        $comments = $repo->findAll();
        return $this->render('comment/index.html.twig', compact('comments'));
    }

    /**
     * @Route("/comment/{id}/delete", name="app_comment_delete")
     */
    public function delete(Comment $comment, EntityManagerInterface $em, Request $request): Response
    {
        $em->remove($comment);
        $em->flush();

        $this->addFlash('warning', 'Commentaire supprimé!');

        $id = substr(strrchr($request->headers->get('referer'), '//'), 1);
        return $this->redirectToRoute('app_show', compact('id'));
    }
}
