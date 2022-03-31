<?php

namespace App\Controller;

use App\Entity\ToDoList;
use App\Entity\User;
use App\Repository\ToDoListRepository;
use App\Repository\UserRepository;
use Carbon\Carbon;
use Faker\Factory;
use Doctrine\Persistence\ManagerRegistry;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

class ToDoListController extends AbstractController
{
    #[Route('/to/do/list', name: 'to_do_list')]
    public function index(ToDoListRepository $toDoListRepository): Response
    {
        return $this->render('to_do_list/index.html.twig', [
            'to_do_lists' => $toDoListRepository->findAll(),
        ]);
    }

    #[Route('/toDoList/add', name: 'add_to_do_list')]
    public function add(ManagerRegistry $registry, UserRepository $userRepository): Response
    {
        $faker = Factory::create();

        $em = $registry->getManager();

        for ($i = 0; $i < 10; $i++) {

            $users = $userRepository->findAll();

            foreach ($users as $user) {
                $toDoList = new ToDoList();

                $toDoList->setName($faker->colorName);
                $toDoList->setValidUser($user);
                $toDoList->setCreatedAt(new \DateTimeImmutable());

                $em->persist($toDoList);
            }
        }

        $em->flush();

        return $this->render('to_do_list/index.html.twig', [
            'controller_name' => 'ToDoListController',
        ]);
    }
}
