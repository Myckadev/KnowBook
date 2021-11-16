<?php

namespace App\Controller;

use App\Entity\BlogWeb;
use App\Entity\Review;
use App\Entity\User;
use App\Repository\BlogWebRepository;
use App\Repository\ReviewRepository;
use App\Repository\UserRepository;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

class AdminController extends AbstractController
{
    /**
     * @Route("/admin", name="AppAdminBlog")
     */
    public function homeBlog(BlogWebRepository $blogWebRepository): Response
    {

        $listBlog = $blogWebRepository->findAll();
        
        return $this->render('admin/accueilBlog.html.twig', [
            'listBlog'=>$listBlog
        ]);
    }
    
    /**
     * @Route("/admin/delblog/{id}", name="AppAdminDelBlog")
     */
    public function adminDelBlog(BlogWeb $blogWeb, EntityManagerInterface $entityManager, ReviewRepository $repository){

        if(!in_array("ROLE_ADMIN", $this->getUser()->getRoles())){
            return $this->redirectToRoute("AppHome");
        }

        foreach ( $repository->findBy(['blogWeb'=> $blogWeb->getId()]) as $review){
            $entityManager->remove($review);
        }

        $entityManager->remove($blogWeb);
        $entityManager->flush();

        return $this->redirectToRoute("AppAdminBlog");

    }

    /**
     * @Route("/admin/reviews", name="AppAdminReview")
     */
    public function adminReview(ReviewRepository $repository){

        $listReview = $repository->findAll();

        return $this->render('admin/reviews.html.twig', [
            'listReview'=>$listReview
        ]);

    }


    /**
     * @Route("/admin/delreview/{id}", name="AppAdminDelReview")
     */
    public function adminDelReview(Review $review, EntityManagerInterface $entityManager){

        if(!in_array("ROLE_ADMIN", $this->getUser()->getRoles())){
            return $this->redirectToRoute("AppHome");
        }

        $entityManager->remove($review);
        $entityManager->flush();

        return $this->redirectToRoute("AppAdminReview");

    }


    /**
     * @Route("/admin/users", name="AppAdminUser")
     */
    public function adminUsers(UserRepository $repository){

        return $this->render('admin/users.html.twig', [
            'listUser'=> $repository->findAll()
        ]);
    }

    /**
     * @Route("/admin/banuser/{id}", name="AppAdminBanUser")
     */
    public function banUser(User $user, EntityManagerInterface $entityManager, ReviewRepository $repository, BlogWebRepository $blogWebRepository){
        if(!in_array("ROLE_ADMIN", $this->getUser()->getRoles())){
            return $this->redirectToRoute("AppHome");
        }

        $listOwnReview = $repository->findBy(['author'=> $user]);
        $listOwnBlog = $blogWebRepository->findBy(['user'=> $user]);

        foreach ( $listOwnReview as $ownReview){
            $entityManager->remove($ownReview);
        }

        foreach ($listOwnBlog  as $ownBlog){
            foreach ($repository->findBy(['blogWeb'=>$ownBlog]) as $reviewAttachToOwnBlog){
                $entityManager->remove($reviewAttachToOwnBlog);
            }
            $entityManager->remove($ownBlog);
        }

        $entityManager->remove($user);
        $entityManager->flush();

        return $this->redirectToRoute("AppAdminBlog");

    }


}
