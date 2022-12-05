<?php

namespace App\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\Serializer\SerializerInterface;
use Doctrine\ORM\EntityManagerInterface;
use \Symfony\Component\HttpFoundation\Response;
use App\Entity\User;
use App\Entity\Article;
use Symfony\Component\PasswordHasher\Hasher\UserPasswordHasherInterface;
use App\Repository\UserRepository;
use App\Repository\ArticleRepository;

use Symfony\Component\Security\Http\Authentication\AuthenticationUtils;

class AuthController extends AbstractController
{

    #[Route('/', name: 'home')]
    public function home(AuthenticationUtils $authenticationUtils): Response
    {
        return $this->redirectToRoute('app_login');
    }

    #[Route('/login', name: 'app_login')]
    public function login(AuthenticationUtils $authenticationUtils): Response
    {
         // get the login error if there is one
         $error = $authenticationUtils->getLastAuthenticationError();
         // last username entered by the user
         $lastUsername = $authenticationUtils->getLastUsername();
         
        // if($error)dd( $error,$lastUsername );

         
        return $this->render('auth/login.html.twig', [
                            'last_username' => $lastUsername,
                            'error'=> $error]);
    }

    #[Route('/login-user', name: 'appCheckLogin')]
    public function checkLogin(AuthenticationUtils $authenticationUtils): Response
    {
        //not used
        // get the login error if there is one
         $error = $authenticationUtils->getLastAuthenticationError();
         // last username entered by the user
         $lastUsername = $authenticationUtils->getLastUsername();
         dd( $error,$lastUsername );
        return $this->render('auth/login.html.twig', []);
    }

    #[Route('/auth/register-user-demo', name: 'registerUser')]
    public function register(SerializerInterface $serializer,
                            UserPasswordHasherInterface $passwordHasher,
                            UserRepository $userRepository,
                            ArticleRepository $articleRepository,
                            ): JsonResponse
    {

        //only for regiser demo data
        $user = new User();
        $user->setEmail("UsernameAdmin@Passwrord.com");
        $user->setRoles(["ROLE_USER","ROLE_ADMIN"]);
        $user->setUsername("UsernameAdmin");
        $plaintextPassword = "Passwrord@123";
        $hashedPassword = $passwordHasher->hashPassword($user,$plaintextPassword);
        $user->setPassword($hashedPassword);
        $userRepository->save($user, true);

        $user = new User();
        $user->setEmail("UsernameUser@user.com");
        $user->setRoles(["ROLE_USER"]);
        $user->setUsername("UsernameUser");
        $plaintextPassword = "Passwrord@123";
        $hashedPassword = $passwordHasher->hashPassword($user,$plaintextPassword);
        $user->setPassword($hashedPassword);
        $userRepository->save($user, true);

        for ($i=0; $i < 50; $i++) { 
            $article = new Article();
            $article->setTitle($this->generateRandomString());
            $article->setDescription($this->generateRandomString());
            $article->setPicture("https://f.hellowork.com/blogdumoderateur/2013/09/google-logo.png");
            $articleRepository->save($article, true);
        }
        $userList = $userRepository->findAll();
        
        $userList = $serializer->serialize($userList, 'json');
        return new JsonResponse($userList, Response::HTTP_OK, [], true);
    }


    private function generateRandomString($length = 100) {
        $characters = '0123456789abcdefghijklmnopqrstuvwxyzABCDEFGHIJKLMNOPQRSTUVWXYZ';
        $charactersLength = strlen($characters);
        $randomString = '';
        for ($i = 0; $i < $length; $i++) {
            $randomString .= $characters[rand(0, $charactersLength - 1)];
        }
        return $randomString;
    }
}
