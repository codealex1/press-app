<?php

namespace App\Controller;

use App\Entity\User;
use App\Entity\Article;
use App\Entity\Comment;
use App\Form\ArticleType;
use App\Form\CommentType;
use App\Entity\ArticleNote;
use App\Repository\ArticleRepository;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Attribute\Route;
use Symfony\Component\Routing\RouterInterface;
use Symfony\Component\ExpressionLanguage\Expression;
use Symfony\Component\HttpFoundation\RedirectResponse;
use Symfony\Component\Form\Extension\Core\Type\SubmitType;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\Security\Http\Attribute\IsGranted;

#[Route('/articles', name:'articles_')]
class ArticleController extends AbstractController
{
    #[Route('/show/{id}', name: 'show')]
    #[IsGranted('show', 'article')]
    public function show(RouterInterface $router,Article $article = null){
        
        $comment = new Comment;
        $comment -> setArticle($article);


        $form = $this->createForm(CommentType::class, $comment, [
            'action'=> $router->generate('comments_create',['article'=>$article->getId()])
        ]);

        return $this->render('article/show.html.twig',[

            'form'=> $form,
            'article' => $article
        ]);
    }

    #[Route('/', name: 'list')]
    public function list(ArticleRepository $articleRepository): Response
    {
        return $this->render('article/list.html.twig',[
            'articles'=> $articleRepository->findAll(),
        ]);
    }

    #[Route('/edit/{id}', name: 'edit')]
    #[Route('/create', name: 'create')]
    #[IsGranted('edit', 'article')]
    public function edit(Request $request, EntityManagerInterface $em, ?Article $article = null): Response
    {
        $isCreate = !$article;
        $article = $article ?? new Article();


        $form = $this->createForm(ArticleType::class, $article);
        
        $form->add('submit',SubmitType::class);
        
        $form->handleRequest($request);
        if ($form->isSubmitted() && $form->isValid()) {
            // $form->getData() holds the submitted values
            // but, the original `$task` variable has also been updated
            $article = $form->getData();
            $article->setStatus('DRAFT');
            $article->setUser($this->getUser());
            
            $em->persist($article);
            $em->flush();
            
            $this->addFlash('success',$isCreate ? 'l\'article à bien été crée':'l\'article à été modifié');
            // ... perform some action, such as saving the task to the database

            return $this->redirectToRoute('articles_list');
        }
        
        
        return $this->render('article/edit.html.twig',[
            'form' => $form,
            'is_create' => $isCreate,
        ]);
    }
    #[Route('/delete/{id}', name: 'delete')]
    #[IsGranted('ROLE_ADMIN')]
    
    public function delete(EntityManagerInterface $em, Article $article): RedirectResponse
    {
        $em->remove($article);
        $em->flush();
        
        $this->addFlash(type:'success',message:'L\'article a été supprimé');
        
        return $this->redirectToRoute('articles_list');
    }
}
