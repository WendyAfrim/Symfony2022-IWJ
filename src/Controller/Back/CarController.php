<?php

namespace App\Controller\Back;

use App\Entity\Car;
use App\Form\CarType;
use App\Repository\CarRepository;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

#[Route('/car', name:'car_')]
class CarController extends AbstractController
{
    #[Route('/', name: 'index', methods: ['GET'])]
    public function index(CarRepository $carRepository): Response
    {
        $cars = $carRepository->findAll();

        return $this->render('back/car/index.html.twig', [
            'cars' => $cars
        ]);
    }

    #[Route('/create', name: 'create', methods: ['GET', 'POST'])]
    public function create(Request $request, EntityManagerInterface $manager): Response
    {
        $car = new Car();
        $form = $this->createForm(CarType::class, $car);

        $form->handleRequest($request);
        if ($form->isSubmitted() && $form->isValid()) {
            $manager->persist($car);
            $manager->flush();

            return $this->redirectToRoute('back_car_index');
        }

        return $this->render('back/car/create.html.twig', [
            'form' => $form->createView()
        ]);
    }

    #[Route(
        '/update/{matriculation}',
        name: 'update',
        requirements: ['matriculation'=> '[A-Z]{1,3}-[A-Z]{1,3}-[0-9]{1,4}'],
        methods: ['GET', 'POST'])
    ]
    public function update(Car $car, Request $request, EntityManagerInterface $manager): Response
    {
        $form = $this->createForm(CarType::class, $car);

        $form->handleRequest($request);
        if ($form->isSubmitted() && $form->isValid()) {
            $manager->flush();

            return $this->redirectToRoute('back_car_show', [
                'matriculation' => $car->getMatriculation()
            ]);
        }

        return $this->render('back/car/update.html.twig', [
            'car' => $car,
            'form' => $form->createView()
        ]);
    }

    #[Route(
        '/{matriculation}',
        name: 'show',
        requirements: ['matriculation'=> '[A-Z]{1,3}-[A-Z]{1,3}-[0-9]{1,4}'],
        methods: ['GET'])
    ]
    public function show(Car $car): Response
    {
        return $this->render('back/car/show.html.twig', [
            'car' => $car
        ]);
    }

    #[Route(
        '/delete/{matriculation}/{token}',
        name: 'delete',
        requirements: ['matriculation'=> '[A-Z]{1,3}-[A-Z]{1,3}-[0-9]{1,4}'],
        methods: ['GET']
    )]
    public function delete(Car $car, string $token, EntityManagerInterface $manager): Response
    {
        if (!$this->isCsrfTokenValid('delete' . $car->getId(), $token)) {
            throw new \Exception('Try again!');
        }

        $manager->remove($car);
        $manager->flush();

        return $this->redirectToRoute('back_car_index');
    }
}
