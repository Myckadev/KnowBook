<?php

namespace App\Controller;

use App\Entity\BlogWeb;
use App\Entity\Review;
use App\Form\BlogWebType;
use App\Form\ReviewFormType;
use App\Repository\BlogWebRepository;
use App\Repository\ReviewRepository;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\Form\Extension\Core\Type\SubmitType;
use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

class BlogController extends AbstractController
{
    /**
     * @Route("/", name="AppHome")
     */
    public function home(): Response
    {
        return $this->render('home/appHome.html.twig');
    }

    /**
     * @Route("/handleSearch", name="handleSearch")
     */
    public function handleSearch(Request $request, BlogWebRepository $repo)
    {
        $query = $request->request->get('form')['query'];
        if($query) {
            $blogs = $repo->findArticlesByName($query);
        }
        return $this->render('home/appHome.html.twig', [
            'blogs' => $blogs
        ]);
    }

    public function searchBar(): Response
    {

        $form = $this->createFormBuilder()
            ->setAction($this->generateUrl('handleSearch'))
            ->add('query', TextType::class, [
                'label'=>false,
            ])
            ->add('recherche', SubmitType::class)
            ->getForm();

        return $this->render('search/searchBar.html.twig', [
            'form' => $form->createView()
        ]);

    }


    /**
     * @Route("/createblog", name="AppCreateBlog")
     */
    public function createBlog(Request $request, EntityManagerInterface $entityManager){

        $blogPost = new BlogWeb();

        $blogPostForm = $this->createForm(BlogWebType::class, $blogPost);
        $blogPostForm->handleRequest($request);

        if($blogPostForm->isSubmitted() && $blogPostForm->isValid()){

            $blogPost->setDate(new \DateTime('now'));

            $blogPost->setUser($this->getUser());

            $entityManager->persist($blogPost);
            $entityManager->flush();

            return $this->redirectToRoute("AppHome");
        }

        return $this->render('home/createBlog.html.twig', [
            'blogWeb' => $blogPostForm->createView()
        ]);
    }

    /**
     * @Route("/viewblog/{id}", name="AppViewBlog")
     */
    public function viewBlog(BlogWeb $blogWeb, BlogWebRepository  $blogWebRepository, Request $request, EntityManagerInterface $entityManager, ReviewRepository $repository){

        $review = new Review();

        $blog = $blogWebRepository->find($blogWeb->getId());
        $listBlogs = $blogWebRepository->findBy([], ["date"=> "DESC"], 5);

        $form = $this->createForm(ReviewFormType::class);
        $form->handleRequest($request);

        if($form->isSubmitted() && $form->isValid()){
            $list = $request->get('review_form');

            $review->setDate(new \DateTime());
            $review->setAuthor($this->getUser());
            $review->setBlogWeb($request->get('blogWeb'));

            $review->setContent(reset($list));


            $entityManager->persist($review);
            $entityManager->flush();

            return $this->redirect($request->getUri());
        }


        return $this->render('front/viewblog.html.twig', [
            'blog'=>$blog,
            'listBlog'=> $listBlogs,
            'reviewForm'=>$form->createView(),
            'listReview' => $repository->findBy(["blogWeb"=> $blog->getId()], ["date"=> "DESC"], 5)
        ]);
    }

    /**
     * @Route("/recentblog", name="AppRecentBlog")
     */
    public function recentBlog(BlogWebRepository $blogWebRepository){

        $listBlogs = $blogWebRepository->findBy([], [ "modifyDate"=> "DESC", "date"=> "DESC"], 8);

        return $this->render('home/recentBlog.html.twig', [
            'listBlog'=> $listBlogs
        ]);
    }
}
