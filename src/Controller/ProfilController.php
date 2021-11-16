<?php

namespace App\Controller;

use App\Entity\BlogWeb;
use App\Entity\Review;
use App\Entity\User;
use App\Form\BlogWebEditType;
use App\Form\ReviewEditFormType;
use App\Form\UserSettingsFormType;
use App\Repository\BlogWebRepository;
use App\Repository\ReviewRepository;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\Security\Csrf\TokenStorage\TokenStorageInterface;

class ProfilController extends AbstractController
{

    /**
     * @Route("/profil/settings", name="AppProfilSettings")
     */
    public function profilSettings(Request $request, EntityManagerInterface $entityManager){

        $form = $this->createForm(UserSettingsFormType::class, $this->getUser());
        $form->handleRequest($request);

        if($form->isSubmitted() && $form->isValid()){

            $entityManager->persist($this->getUser());
            $entityManager->flush();
            return $this->redirectToRoute("AppProfilSettings");

        }

        return $this->render('profil/settings.html.twig', [
            'form'=>$form->createView()
        ]);
    }

    /**
     * @Route("/deluser/{id}", name="AppDeluser")
     */
    public function delUser(Request $request, EntityManagerInterface $entityManager, User $user, BlogWebRepository $blogWebRepository, ReviewRepository $repository, TokenStorageInterface $tokenStorage){


        if($this->getUser()->getId() != $user->getId()){
            return $this->redirectToRoute("AppHome");
        }

        $entityManager->remove($user);
        $entityManager->flush();


        return $this->redirectToRoute("AppHome");
    }


    /**
     * @Route("/profil/vosblogs", name="AppProfilBlog")
     */
    public function homePanel(BlogWebRepository $blogWebRepository): Response
    {

        $listBlogs = $blogWebRepository->findBy(["user"=>$this->getUser()], ["date"=> "DESC"]);

        return $this->render('profil/homePanel.html.twig', [
            "listBlog"=>$listBlogs
        ]);
    }

    /**
     * @Route("/profil/reviews", name="AppProfilReviews")
     */
    public function profilReviews(ReviewRepository $repository){

        $listReviews = $repository->findBy(['author'=>$this->getUser()], ["date"=> "DESC"]);

        return $this->render('profil/reviews.html.twig', [
            'listReview' => $listReviews
        ]);
    }

    /**
     * @Route("/profil/delreview/{id}", name="AppProfilDelreview")
     */
    public function delReview(Review $review, EntityManagerInterface $entityManager){

        if($this->getUser()->getId() != $review->getAuthor()->getId()){
            return $this->redirectToRoute("AppProfilReviews");
        }

        $entityManager->remove($review);
        $entityManager->flush();

        return $this->redirectToRoute("AppProfilReviews");
    }

    /**
     * @Route("/profil/editreview/{id}", name="AppProfilEditReview")
     */
    public function editReview(Review $review, Request $request, EntityManagerInterface $entityManager){

        $user = $this->getUser();
        if($this->getUser() != $user){
            return $this->redirectToRoute("AppHome");
        }

        $form = $this->createForm(ReviewEditFormType::class, $review);
        $form->handleRequest($request);

        if($form->isSubmitted() && $form->isValid()){

            $entityManager->persist($review);
            $entityManager->flush();

            return $this->redirectToRoute("AppProfilReviews");
        }

        return $this->render('back/editreview.html.twig', [
            'form'=>$form->createView()
        ]);
    }


    /**
     * @Route("/profil/delblog/{id}", name="AppDelBlog")
     */
    public function delBlog(BlogWeb $blogWeb, EntityManagerInterface $entityManager, ReviewRepository $repository){

        if($this->getUser()->getId() != $blogWeb->getUser()->getId()){
            return $this->redirectToRoute("AppProfilBlog");
        }

        foreach ( $repository->findBy(['blogWeb'=> $blogWeb->getId()]) as $review){
            $entityManager->remove($review);
        }

        $entityManager->remove($blogWeb);
        $entityManager->flush();

        return $this->redirectToRoute("AppProfilBlog");
    }

    /**
     * @Route("/profil/editblog/{id}", name="AppEditBlog")
     */
    public function editBlog(BlogWeb $blogWeb, Request $request, EntityManagerInterface $entityManager){

        $user = $this->getUser();
        if($this->getUser() != $user){
            return $this->redirectToRoute("AppHome");
        }

        $form = $this->createForm(BlogWebEditType::class, $blogWeb);
        $form->handleRequest($request);

        if($form->isSubmitted() && $form->isValid()){
            $blogWeb->setIsModify(true);
            $blogWeb->setModifyDate(new \DateTime());

            $entityManager->persist($blogWeb);
            $entityManager->flush();

            return $this->redirectToRoute("AppProfilBlog");
        }

        return $this->render('back/editblog.html.twig', [
            'blogWeb'=>$form->createView()

        ]);
    }
}
