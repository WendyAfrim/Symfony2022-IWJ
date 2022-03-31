<?php

namespace App\Controller;

use App\Entity\Item;
use App\Entity\ToDoList;
use App\Entity\User;
use App\Repository\UserRepository;
use Doctrine\ORM\EntityManagerInterface;
use phpDocumentor\Reflection\Types\True_;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\Serializer\Exception\NotEncodableValueException;
use Symfony\Component\Serializer\SerializerInterface;
use Symfony\Component\Validator\Validator\ValidatorInterface;

#[Route('/myOldapi')]
class ApiController extends AbstractController
{
    #[Route('/', name: 'old_user_index', methods: ['GET'])]
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

    #[Route('/', name: 'old_new_user', methods: ['POST'])]
    public function newUser(Request $request, SerializerInterface $serializer,
                            EntityManagerInterface $entityManager, ValidatorInterface $validator)
    {
        $receivedUser = $request->getContent();
        try {
            $user = $serializer->deserialize($receivedUser, User::class, 'json');
            $errors = $validator->validate($user);
            if(count($errors) > 0)
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

    #[Route('/add_to_do_list/{id}', name: 'old_add_to_do_list_to_user', methods: ['POST'])]
    public function newToDoList(User $user, Request $request, SerializerInterface $serializer,
                                EntityManagerInterface $entityManager)
    {
        if ($this->isValidUser($user))
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
    #[Route('/add_item/{id}', name: 'old_add_new_item', methods: ['POST'])]
    public function newItem(ToDoList $toDoList, Request $request,
                            SerializerInterface $serializer,
                            EntityManagerInterface $entityManager,
                            ValidatorInterface $validator)
    {
        if($this->isFull($toDoList))
        {
            return $this->json([
                'status' => 409,
                'message' => 'Your to do list is already full (maximum number of items is 10'
            ], 409);
        }
        $receivedItem = $request->getContent();
        $sinceLast = $this->lastAddItem($toDoList);
        if($sinceLast >= 0)
        {
            try {
                $item = $serializer->deserialize($receivedItem, Item::class, 'json');
                $errors = $validator->validate($item);
                if(count($errors) > 0)
                {
                    return $this->json($errors, 400);
                }
                if($this->isTooLongContents($item))
                {
                    return $this->json([
                        'status' => 409,
                        'message' => 'Contents maximum number is 1000 characters'
                    ], 409);
                }
                if($this->nameAlreadyExist($toDoList, $item))
                {
                    return $this->json([
                        'status' => 409,
                        'message' => 'Item name should be unique'
                    ], 409);
                }
                $item->setCreatedAt(new \DateTime('now'));
                $item->setToDoList($toDoList);
                $entityManager->persist($item);
                $entityManager->flush();
                $testIfIsEighth = 0;
                if($this->iseighth($toDoList))
                {
                    $this->mySendEmail($toDoList->getValidUser());
                    $testIfIsEighth = 1;
                }
                return $this->json($item, (201 + $testIfIsEighth), [], ['groups' => 'apiUser']);
            }catch(NotEncodableValueException $exception)
            {
                return $this->json([
                    'status' => 400,
                    'message' => $exception->getMessage()
                ], 400);
            }
        }
        else
        {
            return $this->json([
                'status' => 400,
                'message' => 'you have to wait: '.(30 - $sinceLast).' minutes before adding new item'
            ], 400);
        }
    }

    public function lastAddItem(ToDoList $toDoList): int
    {
        $lastItem = $toDoList->getItems()->last();
        $now = new \DateTime('now');
        if($lastItem === false)
        {
            return 31;
        }
        //dd($now, $lastItem->getCreationDate(), date_diff($now, $lastItem->getCreationDate() , true)->i );
        return ($now->diff($lastItem->getCreatedAt())->i);
    }

    public function nameAlreadyExist(ToDoList $toDoList, Item $item): bool
    {
        //dd("testing");
        foreach ($toDoList->getItems() as $i)
        {
            //dd($i->getName());
            if($item->getName() == $i->getName())
            {
                return true;
            }
        }
        return false;
    }

    public function isFull(ToDoList $toDoList): bool
    {
        if(count($toDoList->getItems()) > 9)
        {
            return true;
        }
        return false;
    }

    public function iseighth(ToDoList $toDoList): bool
    {
        if(count($toDoList->getItems()) > 7)
        {
            return true;
        }
        return false;
    }


    private function isValidName(string $firstName, string $lastName): bool
    {
        if(!empty($firstName) && !empty($lastName))
        {
            return true;
        }
        return false;
    }
    private function isValidEmail(string $email): bool
    {
        if(!filter_var($email, FILTER_VALIDATE_EMAIL))
        {
            return false;
        }
        return true;
    }

    private function isValidPassword(string $password): bool
    {
        if(!in_array(strlen($password), range(8, 40)))
        {
            return false;
        }
        return true;
    }

    private function isValidAge(\DateTimeInterface $birthday): bool
    {
        $today = new \DateTime('now');
        if($today->diff($birthday , true)->y > 12)
        {
            return true;
        }
        return false;
    }

    public function isValidUser(User $user): bool
    {
        return $this->isValidAge($user->getBirthday())
            && $this->isValidEmail($user->getEmail())
            && $this->isValidName($user->getFirstName(), $user->getLastName())
            && $this->isValidPassword($user->getPassword());

    }

    public function mySendEmail(User $user): bool
    {
        if($user->getEmail())
        {
            return true;
        }
        return false;
    }

    public function isTooLongContents(Item $item): bool
    {
        if(strlen($item->getContent()) > 1000)
        {
            return true;
        }
        return false;
    }
}