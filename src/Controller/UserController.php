<?php

namespace App\Controller;

use App\Entity\User;
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

            $user->setFirstname($faker->firstName);
            $user->setLastname($faker->lastName);
            $user->setEmail($faker->email);
            $user->setPassword($faker->password);
            $user->setBirthday(Carbon::now()->subYears(15));
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

}
