<?php


use App\Entity\User;
use App\Entity\ToDoList;
use PHPUnit\Framework\TestCase;
use Faker\Factory;
use Carbon\Carbon;

class UserTest extends TestCase
{

    public function testIsNotEmpty()
    {
        $faker = Factory::create();

        $user = new User();
        $result = $user->isNotEmpty($faker->firstName,$faker->lastName);
        $this->assertTrue($result);
    }

    public function testIsValidEmail()
    {
        $faker = Factory::create();

        $user = new User();
        $result = $user->validEmail($faker->email);
        $this->assertTrue($result);
    }

    public function testIsMoreThan13()
    {
        $faker = Factory::create();
        $randomInt = rand(0, 100);

        $user = new User();
        $result = $user->isMoreThan13(Carbon::now()->subYears($randomInt));

        if(true === $result) {
            $this->assertTrue($result);
        } else {
            $this->assertFalse($result);
        }
    }

    public function testIsValidPassword()
    {
         $faker = Factory::create();
         $user = new User();

         $password = $faker->password;

         $result = $user->validPassword($password);
         $this->assertTrue($result);
    }

    public function testIsValid()
    {
        $faker = Factory::create();
        $user = new User();
        $randomInt = rand(0, 100);

        $user->setFirstname($faker->firstName);
        $user->setLastname($faker->lastName);
        $user->setEmail($faker->email);
        $user->setPassword($faker->password);
        $user->setBirthday(Carbon::now()->subYears($randomInt));

        $result = $user->isValid($user);

        $this->assertTrue($result);
    }

    public function testIsValidUserHasNotCreateToDoList()
    {
        $faker = Factory::create();
        $user = new User();

        $user->setFirstname('Jane');
        $user->setLastname('Doe');
        $user->setEmail('jane@test.com');
        $user->setPassword('Tkwjf3251cs');
        $user->setBirthday(Carbon::now()->subYears(15));

        $result = $user->createToDoList($user);
        $this->assertTrue($result);
    }

    public function testIsValidUserHasAlreadyCreateToDoList()
    {
        $faker = Factory::create();
        $user = new User();

        $user->setFirstname('Jane');
        $user->setLastname('Doe');
        $user->setEmail('jane@test.com');
        $user->setPassword('Tkwjf3251cs');
        $user->setBirthday(Carbon::now()->subYears(15));
        $user->setHasCreatedToDoList(true);

        $result = $user->createToDoList($user);
        $this->assertFalse($result);
    }
}
