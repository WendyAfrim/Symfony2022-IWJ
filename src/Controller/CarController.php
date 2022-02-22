<?php

namespace App\Controller;

use App\Repository\CarRepository;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

class CarController extends AbstractController
{
    #[Route('/car', name: 'car_index')]
    public function index(CarRepository $carRepository): Response
    {
        $cars = $carRepository->findAll();

        return $this->render('car/index.html.twig', [
            'cars' => $cars
        ]);
    }

    #[Route('/car/{id}', name: 'car_show')]
    public function show($id, CarRepository $carRepository): Response
    {
        $car = $carRepository->find($id);

        return $this->render('car/show.html.twig', [
            'car' => $car
        ]);
    }
}
