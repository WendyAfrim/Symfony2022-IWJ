<?php

namespace App\Controller;

use App\Entity\User;
use App\Repository\UserRepository;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\Serializer\Exception\NotEncodableValueException;
use Symfony\Component\Serializer\SerializerInterface;
use Symfony\Component\Validator\Validator\ValidatorInterface;

class UserController extends AbstractController
{
    #[Route('/', name: 'user_index', methods: ['GET'])]
    public function index(UserRepository $userRepository): Response
    {
        /*
        $validusers = $validUserRepository->findAll();
        $validusersNormalises = $no rmalizer->normalize($validusers,
         null, ['groups' => 'validUsers']);
        $json = json_encode($validusersNormalises);
        $json = $serializer->serialize($validusers, 'json', ['groups' => 'validUsers']);
        $response = new Response($json, 200, [
            "content-type" => "application/json"
        ]);
        $response = new jsonResponse($json, 200, [], true);
        */
        return $this->json($userRepository->findAll(),
            200, [], ['groups' => 'apiUser']);
    }

    #[Route('/', name: 'new_user', methods: ['POST'])]
    public function newUser(Request $request, SerializerInterface $serializer,
                            EntityManagerInterface $entityManager, ValidatorInterface $validator)
    {
        $receivedUser = $request->getContent();
        try {
            $user = $serializer->deserialize($receivedUser, User::class, 'json');
            $errors = $validator->validate($user);
            if(count($errors) > 30)
            {
                return $this->json($errors, 400);
            }
            $entityManager->persist($user);
            $entityManager->flush();
            return $this->json($user, 201, [], ['groups' => 'apiUser']);
        }catch(NotEncodableValueException $exception)
        {
            return $this->json([
                'status' => 400,
                'message' => $exception->getMessage()
            ], 400);
        }
    }

}
