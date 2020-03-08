<?php

namespace App\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use App\Entity\Task;
use App\Form\TaskType;
use Symfony\Component\Routing\Annotation\Route;

class TaskController extends AbstractController
{
    /**
    * @Route("/task", name="tasks")
    */
    public function index(Request $request = null)
    {
        
        $pdo = $this->getDoctrine()->getManager();
        
        $task = new Task();
        $form = $this->createForm(TaskType::class, $task);
        // Analyse la requete http
        $form->handleRequest($request);
        if ($form->isSubmitted() && $form->isValid()) {
            $pdo->persist($task);  // prepare
            $pdo->flush();            // execute
        }
        
        $tasks = $pdo->getRepository(Task::class)->findAll();
        return $this->render('task/index.html.twig', [
            'tasks' => $tasks,
            'form_ajout' => $form->createView()
            
            ]);
        }
        
        /**
        * @Route("/task/{id}", name="task")
        */
        public function task(Task $task, Request $request)
        {
            if ($task != null) { // Au cas ou l'on cherche un produit qui n'existe pas
                $form = $this->createForm(TaskType::class, $task);
                // Analyse la requete http
                $form->handleRequest($request);
                if ($form->isSubmitted() && $form->isValid()) {
                    $pdo = $this->getDoctrine()->getManager();     // Connexion à la BDD
                    $pdo->persist($task);  // prepare
                    $pdo->flush();            // execute
                }
                
                
                return $this->render('task/task.html.twig', [
                    'task' => $task,
                    'form_edit' => $form->createView()
                    ]);
                } else {
                    return $this->redirectToRoute('task');
                }
                
            }
            
            /**
            * @Route("/task/delete/{id}", name="delete_task")
            */
            public function delete(Task $task = null)
            {
                if ($task != null) {  // Si l'on trouve un task, alors on le supprime
                    $pdo = $this->getDoctrine()->getManager(); // Connexion à la BDD
                    $pdo->remove($task);
                    $pdo->flush();
                }
                return $this->redirectToRoute('home');
            }
        }
        