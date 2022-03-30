<?php

namespace App\Controller;

use App\Entity\ToDoList;
use App\Entity\User;
use App\Repository\UserRepository;
use Carbon\Carbon;
use Doctrine\ORM\EntityManager;
use Doctrine\Persistence\ManagerRegistry;
use Faker\Factory;
use phpDocumentor\Reflection\Types\Boolean;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\Validator\Constraints\Date;

class UserController extends AbstractController
{
    #[Route('/user', name: 'user')]
    public function index(): Response
    {
        return $this->render('user/index.html.twig', [
            'controller_name' => 'UserController',
        ]);
    }

    #[Route('/add', name: 'user_add')]
    public function add(ManagerRegistry $registry): Response
    {
        $faker = Factory::create();

        $em = $registry->getManager();

        for ($i = 0; $i < 10; $i++) {

            $user = new User();

            $date = new \DateTime('200'.$i.'-01-02');

            $user->setFirstname($faker->firstName);
            $user->setLastname($faker->lastName);
            $user->setEmail($faker->email);
            $user->setPassword($faker->password);
            $user->setBirthday($date);
            $user->setHasCreatedToDoList(false);


            if (true === $user->isValid($user)) {
                $em->persist($user);
            }
        }
        $em->flush();

        return $this->render('user/index.html.twig', [
            'controller_name' => 'UserController',
        ]);
    }

    #[Route('/add-to-do-list', name: 'user_add_to_do_list')]
    public function addToDoList(ManagerRegistry $registry, UserRepository $repository): Response
    {
        $faker = Factory::create();

        $em = $registry->getManager();

        $users = $repository->findAll();

        foreach ($users as $user) {

            if($user->isValid($user))
            {
                $toDoList = new ToDoList();

                $toDoList->setName($faker->colorName);
                $toDoList->setCreatedAt(new \DateTimeImmutable());

                $em->persist($toDoList);
                $user->setToDoList($toDoList);
            }
        }
        $em->flush();

        return $this->render('user/index.html.twig', [
            'controller_name' => 'UserController',
        ]);
    }

}
