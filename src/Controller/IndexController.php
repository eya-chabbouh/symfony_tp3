<?php
namespace App\Controller;
use App\Entity\Article;

use Doctrine\ORM\EntityManagerInterface;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\Routing\Annotation\Route;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Method;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Component\Form\Extension\Core\Type\SubmitType;


class IndexController extends AbstractController
{
/**
 *@Route("/",name="article_list")
 */
public function home(EntityManagerInterface $entityManager)
 {
      //récupérer tous les articles de la table article de la BD
      // et les mettre dans le tableau $articles
      $articles = $entityManager->getRepository(Article::class)->findAll();
      return $this->render('articles/index.html.twig', ['articles' => $articles]);
 }

 //function ajout:
    /**
  * @Route("/article/save")
  */
    public function save(EntityManagerInterface $entityManager) {
   //$entityManager = $this->getDoctrine()->getManager();
    $article = new Article();
    $article->setNom('Article 8');
    $article->setPrix(8000);
  
    $entityManager->persist($article);
      $entityManager->flush();
    return new Response('Article enregisté avec id '.$article->getId());
    }


   /**
 * @Route("/article/new", name="new_article")
 * Method({"GET", "POST"})
 */
 public function new(Request $request , EntityManagerInterface $entityManager) {
   $article = new Article();
   $form = $this->createFormBuilder($article)
   ->add('nom', TextType::class)
   ->add('prix', TextType::class)
   ->add('save', SubmitType::class, array( 'label' => 'Créer'))->getForm();


 $form->handleRequest($request);

 if($form->isSubmitted() && $form->isValid()) {
 $article = $form->getData();

 $entityManager->persist($article);
 $entityManager->flush();

 return $this->redirectToRoute('article_list');
 }
 return $this->render('articles/new.html.twig',['form' => $form->createView()]);
 }

//function affiche: 
 /**
 * @Route("/article/{id}", name="article_show")
 */
public function show($id , EntityManagerInterface $entityManager) {
   $article = $entityManager->getRepository(Article::class)->find($id);
   return $this->render('articles/show.html.twig',
   array('article' => $article));
}

//function modif : 
/**
 * @Route("/article/edit/{id}", name="edit_article")
 * Method({"GET", "POST"})
 */
public function edit(Request $request, $id,  EntityManagerInterface $entityManager) {
   $article = new Article();
   $article = $entityManager->getRepository(Article::class)->find($id);
   if (!$article) {
      throw $this->createNotFoundException('Article non trouvé avec l\'id '.$id);}

   $form = $this->createFormBuilder($article)
   ->add('nom', TextType::class)
   ->add('prix', TextType::class)
   ->add('save', SubmitType::class, array(
   'label' => 'Modifier'
   ))->getForm();
  
   $form->handleRequest($request);
   if($form->isSubmitted() && $form->isValid()) {
  
 
   $entityManager->flush();
  
   return $this->redirectToRoute('article_list');
   }

return $this->render('articles/edit.html.twig', ['form' => $form->createView()]);
}

//function supp : 
/**
     * @Route("/article/delete/{id}", name="delete_article", methods={"DELETE"})

 */
public function delete(Request $request, $id, EntityManagerInterface $entityManager) {
   $article = $entityManager->getRepository(Article::class)->find($id);

   $entityManager->remove($article);
   $entityManager->flush();
  
   $response = new Response();
   $response->send();
   return $this->redirectToRoute('article_list');
   }
  
 
}