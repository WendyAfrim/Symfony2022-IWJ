<?php


use App\Entity\Item;
use App\Entity\User;
use App\Entity\ToDoList;
use PHPUnit\Framework\TestCase;
use App\Service\EmailSenderService;
use Faker\Factory;

class UserTest extends TestCase
{
    private User $user;

   /** @var EmailSenderService $emailSenderService */
    private $emailSenderService;

    protected function setUp(): void
    {
        $today = new \DateTime();
        $interval15Y = new \DateInterval('P15Y');

        $this->emailSenderService = $this->getMockBuilder(EmailSenderService::class)
            ->onlyMethods(['send'])
            ->getMock();

        $this->user = new User('testf', 'testl','test@test.fr', 'testPassword',  $today->sub($interval15Y), $this->emailSenderService);


        parent::setUp();
    }

    public function testIsNotEmpty()
    {
        $faker = Factory::create();
        $result = $this->user->isNotEmpty($faker->firstName,$faker->lastName);
        $this->assertTrue($result);
    }

    public function testIsValidEmail()
    {
        $faker = Factory::create();

        $result = $this->user->validEmail($faker->email);
        $this->assertTrue($result);
    }

    public function testIsMoreThan13()
    {
        $faker = Factory::create();
        $result = $this->user->isMoreThan13($faker->dateTimeBetween('-20 years', '-13 years'));
        $this->assertTrue($result);
        $result = $this->user->isMoreThan13($faker->dateTimeBetween('-12 years', '-0 years'));
        $this->assertFalse($result);

    }

    public function testIsValidPassword()
    {
         $result = $this->user->validPassword('Yhskqufnns');
         $this->assertTrue($result);
    }

    public function testIsValid()
    {
        $faker = Factory::create();
        $this->user->setBirthday($faker->dateTimeBetween('-20 years', '-13 years'));
        $result = $this->user->isValid($this->user);
        $this->assertTrue($result);

        $this->user->setBirthday($faker->dateTimeBetween('-12 years', '-0 years'));
        $result = $this->user->isValid($this->user);
        $this->assertFalse($result);
    }

    public function testIsValidUserHasNotCreateToDoList()
    {
        $result = $this->user->createToDoList($this->user);
        $this->assertTrue($result);
    }

    public function testIsValidUserHasAlreadyCreateToDoList()
    {
        $this->user->setHasCreatedToDoList(true);

        $result = $this->user->createToDoList($this->user);
        $this->assertFalse($result);
    }

    public function testAddItemToToDoList()
    {
        $toDoList = new ToDoList();
        $toDoList->setCreatedAt(new \DateTimeImmutable());
        $toDoList->setName("To do list");
        $this->user->setToDoList($toDoList);
        $toDoList->setValidUser($this->user);
        $this->user->setHasCreatedToDoList(true);

        $item = new Item();
        $item->setName("test1Item");
        $item->setContent("testContent");
        $item->setCreatedAt(new \DateTimeImmutable());

        $result = $this->user->addItemToToDoList($item);
        $this->assertTrue($result);

        for($i = 0; $i < 9; $i++)
        {
            $item = new Item();
            $item->setName("test". $i. "Item");
            $item->setContent("testContent");
            $item->setCreatedAt(new \DateTimeImmutable());
            $this->user->getToDoList()->addItem($item);
        }

        $item = new Item();
        $item->setName("test1Item");
        $item->setContent("testContent");
        $item->setCreatedAt(new \DateTimeImmutable());
        $result = $this->user->addItemToToDoList($item);
        $this->assertFalse($result);

    }

    public function testHas8Items()
    {

        $toDoList = new ToDoList();
        $toDoList->setCreatedAt(new \DateTimeImmutable());
        $toDoList->setName("To do list");
        $this->user->setToDoList($toDoList);

        for( $i=0; $i < 8; $i++) {

            $item = new Item();

            $item->setName('Item1');
            $item->setContent('testContent');
            $item->setCreatedAt(new \DateTimeImmutable());
            $result = $this->user->getToDoList()->addItem($item);
        }

        $this->emailSenderService->expects($this->once())
                    ->method('send')
                    ->willReturn(true);

        $this->assertTrue($this->user->has8items());
    }
}
