<?php

namespace App\Controller;

use App\Entity\User;
use App\Entity\Task;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\Routing\Annotation\Route;
use App\Form\UserType;
use Symfony\Component\HttpFoundation\Request;

class UserController extends AbstractController
{
    /**
    * @Route("/", name="home")
    */
    public function index(Request $request = null)
    {
        
        $pdo = $this->getDoctrine()->getManager();
        
        $user = new User();
        $form = $this->createForm(UserType::class, $user);
        // Analyse la requete http
        $form->handleRequest($request);
        if ($form->isSubmitted() && $form->isValid()) {
            $pdo->persist($user);  // prepare
            $pdo->flush();            // execute
        }
        
        $users = $pdo->getRepository(User::class)->findAll();
        return $this->render('user/index.html.twig', [
            'users' => $users,
            'form_ajout' => $form->createView()
            
            ]);
        }
        /**
        * @Route("/user/{id}", name="user")
        */
        public function user(User $user, Request $request)
        {
            if ($user != null) { // Au cas ou l'on cherche un produit qui n'existe pas
                $form = $this->createForm(UserType::class, $user);
                // Analyse la requete http
                $form->handleRequest($request);
                if ($form->isSubmitted() && $form->isValid()) {
                    $pdo = $this->getDoctrine()->getManager();     // Connexion à la BDD
                    $pdo->persist($user);  // prepare
                    $pdo->flush();            // execute
                }
                
                $pdo = $this->getDoctrine()->getManager();
                $tasks = $pdo->getRepository(Task::class)->findBy(array('user' => $user));
                
                return $this->render('user/user.html.twig', [
                    'tasks' => $tasks,
                    'user' => $user,
                    'form_edit' => $form->createView()
                    ]);
                } else {
                    return $this->redirectToRoute('user');
                }
            }
        }
        