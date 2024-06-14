<?php

namespace App\Controller\Admin;

use App\Entity\Article;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Attribute\Route;
use Symfony\Component\Security\Http\Attribute\IsGranted;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;


#[Route('/admin/articles', name: 'admin_articles_')]
class AdminController extends AbstractController
{
    #[Route('/validate/{id}', name: 'validate')]
    public function list(EntityManagerInterface $em,Article $article = null): Response
    {
        $article->setStatus('Published');
        $em->persist($em);
        $em->flush();

        return $this->redirectToRoute('articles_show', ['id'=>$article->getId()]);
    }
}
