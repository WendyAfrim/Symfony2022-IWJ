<?php

namespace App\Controller;

use App\Entity\Item;
use App\Entity\ToDoList;
use App\Entity\User;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\Serializer\Exception\NotEncodableValueException;
use Symfony\Component\Serializer\SerializerInterface;
use Symfony\Component\Validator\Validator\ValidatorInterface;

class ItemController extends AbstractController
{
    #[Route('/add_item/{id}', name: 'add_new_item', methods: ['POST'])]
    public function newItem(ToDoList $toDoList, Request $request,
                            SerializerInterface $serializer,
                            EntityManagerInterface $entityManager,
                            ValidatorInterface $validator)
    {
        if($toDoList->isFull())
        {
            return $this->json([
                'status' => 409,
                'message' => 'Your to do list is already full (maximum number of items is 10'
            ], 409);
        }
        $receivedItem = $request->getContent();
        $sinceLast = $toDoList->lastAddItem();
        if($sinceLast >= 0)
        {
            try {
                $item = $serializer->deserialize($receivedItem, Item::class, 'json');
                $errors = $validator->validate($item);
                if(count($errors) > 0)
                {
                    return $this->json($errors, 400);
                }
                if($item->isTooLongContents())
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
                if($toDoList->iseighth())
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

    public function nameAlreadyExist(ToDoList $toDoList, Item $item): bool
    {
        //dd("testing");
        foreach ($toDoList->getItems() as $i) {
            //dd($i->getName());
            if ($item->getName() == $i->getName()) {
                return true;
            }
        }
        return false;
    }

    public function mySendEmail(User $user): bool
    {
        if($user->getEmail())
        {
            return true;
        }
        return false;
    }

}
