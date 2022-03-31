<?php

namespace App\Tests;

use App\Entity\User;
use PHPUnit\Framework\TestCase;
use Faker\Factory;

class UserTest extends TestCase
{
    private User $user;

    protected function setUp(): void
    {
        $today = new \DateTime();
        $interval15Y = new \DateInterval('P15Y');
        $this->user = new User('testf', 'testl','test@test.fr', 'testPassword',  $today->sub($interval15Y));
        parent::setUp();
    }

    public function testIsNotEmpty()
    {
        $faker = Factory::create();
        $this->user->setFirstname($faker->firstName);
        $this->user->setLastname($faker->lastName);
        $result = $this->user->isNotEmpty();
        $this->assertTrue($result);
    }

    public function testIsValidEmail()
    {
        $faker = Factory::create();
        $this->user->setEmail($faker->email);
        $result = $this->user->validEmail();
        $this->assertTrue($result);
    }

    public function testis13atLeast()
    {
        $faker = Factory::create();
        $this->user->setBirthday($faker->dateTimeBetween('-20 years', '-13 years'));
        $result = $this->user->is13atLeast();
        $this->assertTrue($result);
        $this->user->setBirthday($faker->dateTimeBetween('-12 years', '-1 years'));
        $result = $this->user->is13atLeast();
        $this->assertFalse($result);
    }

    public function testIsValidPassword()
    {
        $password = "";
        $this->user->setPassword($password);
        $result = $this->user->validPassword();
        $this->assertFalse($result);
        $password = "1234567";
        $this->user->setPassword($password);
        $result = $this->user->validPassword();
        $this->assertFalse($result);
        $password = "*****************************************";
        $this->user->setPassword($password);
        $result = $this->user->validPassword();
        $this->assertFalse($result);
        $faker = Factory::create();
        $this->user->setPassword($faker->password);
        $result = $this->user->validPassword();
        $this->assertTrue($result);
    }

    public function testIsValid()
    {
        $faker = Factory::create();
        $this->user->setBirthday($faker->dateTimeBetween('-20 years', '-14 years'));

        $result = $this->user->isValid();
        $this->assertTrue($result);
        dd($this->user->getBirthday());
    }

    public function testIsValidUserHasNotCreateToDoList()
    {
        $today = new \DateTime();
        $interval15Y = new \DateInterval('P15Y');
        $this->user->setBirthday($today->sub($interval15Y));

        $result = $this->user->createToDoList();
        $this->assertTrue($result);
    }

    public function testIsValidUserHasAlreadyCreateToDoList()
    {
        $faker = Factory::create();
        $today = new \DateTime();
        $interval15Y = new \DateInterval('P15Y');
        $this->user->setBirthday($today->sub($interval15Y));
        $this->user->setHasCreatedToDoList(true);

        $result = $this->user->createToDoList();
        $this->assertFalse($result);
    }
}
