<?php

namespace App\Controller;

use App\Entity\User;
use Doctrine\ORM\EntityManager;
use App\Repository\UserRepository;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\PasswordHasher\Hasher\UserPasswordHasherInterface;

class UserController extends AbstractController
{
    private $manager;

    private $user;

    public function __construct(EntityManagerInterface $manager, UserRepository $user)
    {
        $this->manager = $manager;
        $this->user = $user;
    }
    //Création d'un nouveau utilisateur 
    #[Route('/api/userCreate', name: 'userCreate', methods:'POST')]
    public function userCreate(Request $request, UserPasswordHasherInterface $passwordHasher): Response
    {
       $data = json_decode($request->getContent(),true);

       $decoded = json_decode($request->getContent());
       $email = $decoded->email;
       $plaintextPassword = $decoded->password;

       $email_exist=$this->user->findOneByEmail($email);

        if ($email_exist) 
        {
            return new JsonResponse(
            
                [
                    "status" =>false,
                    "message"=>"Email already exist!"
                ]
                );
        }
        else
        {
            $user = new User();
            $hashedPassword = $passwordHasher->hashPassword(
                $user,
                $plaintextPassword
            );
            $user->setEmail($email)
                ->setPassword($hashedPassword)
                ->setUsername($email);
            
            $this->manager->persist($user);
            $this->manager->flush();    

            return new JsonResponse(
            
                [
                    "status" =>true,
                    "message"=>"L\'utilisateur créé avec succés!"
                ]
                );

        }            
        return $this->render('user/index.html.twig', [
            'controller_name' => 'UserController',
        ]);
    }
    
}
