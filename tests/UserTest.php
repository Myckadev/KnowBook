<?php

namespace App\Tests;

use App\Entity\User;
use Symfony\Bundle\FrameworkBundle\Test\KernelTestCase;
use Symfony\Bundle\FrameworkBundle\Test\WebTestCase;
use Symfony\Component\HttpFoundation\Response;

/**@test*/
class UserTest extends WebTestCase
{
    public function testUserCanDestroyBlog(): void
    {

        $user = (new User())
            ->setUsername("Jean")
            ->setEmail(10)
            ->setIsVerified(0)
            ->setPassword("Test")
            ->setRoles(array('ROLE_MEMBER'));

        $kernel = self::bootKernel();
        $error = self::$container->get('validator')->validate($user);
        $this->assertCount(0, $error);
        //$this->assertSame('test', $kernel->getEnvironment());
        //$routerService = self::$container->get('router');
        //$myCustomService = self::$container->get(CustomService::class);
    }

//    public function testUserCantLoadPage(){
//        $client = static::createClient();
//        $client->request('GET', '/admin');
//        $this->assertResponseStatusCodeSame(Response::HTTP_OK);
//
//    }

    public function testUserCanLoadPage(){
        $client = static::createClient();
        $client->request('GET', '/');
        $this->assertResponseStatusCodeSame(Response::HTTP_OK);

    }

    public function testLoginWithBadCredentials(){
        $client = static::createClient();  //Générer un client

        $crawler = $client->request('GET', '/login'); //Envoyé le client sur la route login
        $form = $crawler->selectButton('Connexion')->form([  //tester de remplir le formulaire avec des données
            'email' => "test@test.fr",
            'password' => "fakepassword"
        ]);
        $client->submit($form); //Envoyé le formulaire
        $this->assertResponseRedirects('/login'); //On sait que le mot de passe est faux alors on s'attend à une redirection sur la page login
        $client->followRedirect(); //On dit au client de suivre la redirection
        $this->assertResponseStatusCodeSame(Response::HTTP_OK); //si tout est bon jusque la alors la page login c'est bien rechargé et le status code est 200
    }
}
