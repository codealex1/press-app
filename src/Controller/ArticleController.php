<?php

namespace App\Controller;

use App\Entity\Article;
use App\Form\ArticleType;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Component\Form\Extension\Core\Type\SubmitType;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Attribute\Route;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\RedirectResponse;

#[Route('/articles', name:'articles_')]
class ArticleController extends AbstractController
{
    #[Route('/', name: 'list')]
    public function list(): Response
    {
        return $this->render('article/list.html.twig');
    }

    #[Route('/edit/{id}', name: 'edit')]
    #[Route('/create', name: 'create')]
    public function edit(Request $request, EntityManagerInterface $em): Response
    {
        $article = new Article();

        $form = $this->createForm(ArticleType::class, $article);
        
        $form->add('submit',SubmitType::class);
        
        $form->handleRequest($request);
        if ($form->isSubmitted() && $form->isValid()) {
            // $form->getData() holds the submitted values
            // but, the original `$task` variable has also been updated
            $article = $form->getData();
            $article->setStatus('DRAFT');
            
            $em->persist($article);
            $em->flush();
            

            // ... perform some action, such as saving the task to the database

            return $this->redirectToRoute('articles_list');
        }
        
        
        return $this->render('article/edit.html.twig',[
            'form' => $form
        ]);
    }
    #[Route('/delete/{id}', name: 'delete')]
    public function delete(): RedirectResponse
    {
        $this->addFlash(type:'success',message:'L\'article a été supprimé');
        
        return $this->redirectToRoute('articles_list');
    }
}
