<?php

namespace App\Controller;

use App\Entity\Item;
use Carbon\Carbon;
use Doctrine\Persistence\ManagerRegistry;
use Faker\Factory;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

class ItemController extends AbstractController
{
    #[Route('/item', name: 'item')]
    public function index(): Response
    {
        return $this->render('item/index.html.twig', [
            'controller_name' => 'ItemController',
        ]);
    }

    #[Route('/add-item', name: 'item_add')]
    public function add(ManagerRegistry $registry): Response
    {
        $faker = Factory::create();

        $em = $registry->getManager();

        for ($i = 0; $i < 10; $i++) {

            $user = new Item();


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
