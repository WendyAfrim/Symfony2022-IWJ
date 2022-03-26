<?php

namespace App\Controller;

use App\Entity\User;
use Carbon\Carbon;
use Doctrine\ORM\EntityManager;
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
    public function add(EntityManager $em): Response
    {
        $user = new User();
        $faker = Factory::create();

        for ($i = 0; $i < 10; $i++) {

            $user->setFirstname($faker->firstName);
            $user->setLastname($faker->lastName);
            $user->setEmail($faker->email);
            $user->setPassword($faker->password);
            $user->setBirthday($faker->dateTime);

            if (true === $this->isValid()) {
                $em->persist($user);
            }
        }

        $em->flush();

        return $this->render('user/index.html.twig', [
            'controller_name' => 'UserController',
        ]);
    }

}
