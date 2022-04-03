<?php

namespace App\Tests;

use App\Controller\EmailSenderService;
use App\Entity\Item;
use App\Entity\ToDoList;
use App\Entity\User;
use PHPUnit\Framework\TestCase;

class ToDoListTest extends TestCase
{
    private ToDoList $toDoList;
    private User $user;
    /** @var EmailSenderService $externalApis */
    private $emailSenderService;


    protected function setUp(): void
    {
        $this->toDoList = new ToDoList();

        $today = new \DateTime();
        $interval15Y = new \DateInterval('P15Y');
        $this->user = new User('testf', 'testl','test@test.fr', 'testPassword',  $today->sub($interval15Y));

        $this->emailSenderService = $this->getMockBuilder(EmailSenderService::class)
            ->onlyMethods(['emailSender'])
            ->getMock();


        $this->user->setToDoList($this->toDoList);
        $this->user->getToDoList()->setEmailSenderService($this->emailSenderService);

        $item = new Item();
        $item->setName("testItem");
        $item->setContent("testContent");
        $item->setCreatedAt();

        $this->toDoList->addItem($item);



        parent::setUp();
    }

    public function testisFull()
    {

        for ($i=0; $i < 9; $i++)
        {
            $item = new Item();
            $item->setName("testItem".$i);
            $item->setContent("testContent");
            $item->setCreatedAt();
            $this->toDoList->addItem($item);
            if($i < 8)
            {
                $result = $this->toDoList->isFull();
                $this->assertFalse($result);
            }
        }
        $result = $this->toDoList->isFull();
        $this->assertTrue($result);
    }

    public function testIseighth()
    {
        for ($i=0; $i<9; $i++)
        {
            $item = new Item();
            $item->setName("testItem".$i);
            $item->setContent("testContent");
            $item->setCreatedAt();
            $this->toDoList->addItem($item);

            if($i<6)
            {
                $result = $this->toDoList->iseighth();
                $this->assertFalse($result);
            }
            elseif($i>7)
            {
                $result = $this->toDoList->iseighth();
                $this->assertTrue($result);
            }

        }
    }

    public function testsendEightItemEmail()
    {
        for ($i=0; $i<7; $i++) {
            $item = new Item();
            $item->setName("testItem" . $i);
            $item->setContent("testContent");
            $item->setCreatedAt();
            $this->toDoList->addItem($item);
        }
        $this->emailSenderService->expects($this->any())
            ->method('emailSender')
            ->willReturn(true);
        $result = $this->toDoList->sendEightItemEmail();
        $this->assertTrue($result);
    }

    public function testlastAddItem()
    {
        $result = $this->toDoList->lastAddItem();
        $this->assertEquals(0, $result);
        $this->toDoList->removeItem($this->toDoList->getItems()->last());
        $result = $this->toDoList->lastAddItem();
        $this->assertEquals(31, $result);
    }
}