<?php

namespace App\Controller;

use App\Entity\ToDoList;
use App\Entity\User;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\Serializer\SerializerInterface;

class ToDoListController extends AbstractController
{
    #[Route('/add_to_do_list/{id}', name: 'add_to_do_list_to_user', methods: ['POST'])]
    public function newToDoList(User $user, Request $request, SerializerInterface $serializer,
                                EntityManagerInterface $entityManager)
    {
        if ($user->isValidUser())
        {
            if(is_null($user->getToDoList()))
            {

                $toDoList = new ToDoList();
                $toDoList->setCreatedAt(new \DateTime());
                $toDoList->setName($user->getFirstname() . " to do list");
                $user->setToDoList($toDoList);
                $user->setHasCreatedToDoList(true);
                $entityManager->flush();
                return $this->json($toDoList, 201, [], ['groups' => 'apiUser']);
            }
            return $this->json([
                'status' => 409,
                'message' => 'User already has to do list'
            ], 400);
        }
        else
        {
            return $this->json([
                'status' => 400,
                'message' => 'Not Valid User'
            ], 400);
        }
    }
}
