<?php

namespace App\Tests;

use App\Service\EmailSenderService;
use App\Entity\Item;
use App\Entity\ToDoList;
use App\Entity\User;

class ToDOListTest extends \Monolog\Test\TestCase
{

    private User $user;

    protected function setUp(): void
    {
        $this->toDoList = new ToDoList();
        $this->toDoList->setCreatedAt(new \DateTimeImmutable());
        $this->toDoList->setName("To do list");

        $emailSenderService = new EmailSenderService();

        $today = new \DateTime();
        $interval15Y = new \DateInterval('P15Y');
        $this->user = new User('testf', 'testl','test@test.fr', 'testPassword',  $today->sub($interval15Y), $emailSenderService);
        $this->user->setToDoList($this->toDoList);
        $this->toDoList->setValidUser($this->user);
        $this->user->setHasCreatedToDoList(true);


        $item = new Item();
        $item->setName("testItem");
        $item->setContent("testContent");
        $item->setCreatedAt(new \DateTimeImmutable());

        $this->toDoList->addItem($item);

        parent::setUp();
    }

    public function testCheckUnicity()
    {
        $item = new Item();
        $item->setName("testItem");
        $item->setContent("testContent");
        $item->setCreatedAt(new \DateTimeImmutable());

        $result = $this->user->getToDoList()->checkUnicity($this->user->getToDoList(), $item);

        $this->assertFalse($result);

    }


    public function checkLastItemCreation()
    {
        $result = $this->checkLastItemCreation();

        $this->assertEquals(0, $result);

    }
}