<?php

namespace App\Entity;

use App\Repository\UserRepository;
use App\Service\EmailSenderService;
use Carbon\Carbon;
use Doctrine\ORM\Mapping as ORM;
use Faker\Factory;
use phpDocumentor\Reflection\Types\Boolean;
use Symfony\Component\Validator\Constraints\Date;


/**
 * @ORM\Entity(repositoryClass=UserRepository::class)
 * @ORM\Table(name="`user`")
 */
class User
{
    /**
     * @ORM\Id
     * @ORM\GeneratedValue
     * @ORM\Column(type="integer")
     */
    private $id;

    /**
     * @ORM\Column(type="string", length=255)
     */
    private $firstname;

    /**
     * @ORM\Column(type="string", length=255)
     */
    private $lastname;

    /**
     * @ORM\Column(type="string", length=255)
     */
    private $email;

    /**
     * @ORM\Column(type="string", length=255)
     */
    private $password;
    

    /**
     * @ORM\OneToOne(targetEntity=ToDoList::class, mappedBy="validUser", cascade={"persist", "remove"})
     */
    private $toDoList;

    /**
     * @ORM\Column(type="boolean")
     */
    private $hasCreatedToDoList;

    /**
     * @ORM\Column(type="datetime", nullable="true")
     */
    private $birthday;

    private $faker;



    public function __construct()
    {
        $this->faker = Factory::create();
    }

    public function getId(): ?int
    {
        return $this->id;
    }

    public function getFirstname(): ?string
    {
        return $this->firstname;
    }

    public function setFirstname(string $firstname): self
    {
        $this->firstname = $firstname;

        return $this;
    }

    public function getLastname(): ?string
    {
        return $this->lastname;
    }

    public function setLastname(string $lastname): self
    {
        $this->lastname = $lastname;

        return $this;
    }

    public function getEmail(): ?string
    {
        return $this->email;
    }

    public function setEmail(string $email): self
    {
        $this->email = $email;

        return $this;
    }

    public function getPassword(): ?string
    {
        return $this->password;
    }

    public function setPassword(string $password): self
    {
        $this->password = $password;

        return $this;
    }

    
    public function getToDoList(): ?ToDoList
    {
        return $this->toDoList;
    }

    public function setToDoList(?ToDoList $toDoList): self
    {
        // unset the owning side of the relation if necessary
        if ($toDoList === null && $this->toDoList !== null) {
            $this->toDoList->setValidUser(null);
        }

        // set the owning side of the relation if necessary
        if ($toDoList !== null && $toDoList->getValidUser() !== $this) {
            $toDoList->setValidUser($this);
        }

        $this->toDoList = $toDoList;

        return $this;
    }

    public function getHasCreatedToDoList(): ?bool
    {
        return $this->hasCreatedToDoList;
    }

    public function setHasCreatedToDoList(bool $hasCreatedToDoList): self
    {
        $this->hasCreatedToDoList = $hasCreatedToDoList;

        return $this;

    }

    public function validEmail(string $email) : bool
    {
        if (!empty($email) && filter_var($email, FILTER_VALIDATE_EMAIL)) {
            return true;
        }
        return false;
    }

    public function validPassword(string $password) : bool
    {
        if (!empty($password) && strlen($password) >= 8 && strlen($password) <= 40) {
            return true;
        }
        return false;
    }

    public function isNotEmpty(string $firstname, string $lastname) : bool
    {
        if (!empty($firstname) && !empty($lastname)) {
            return true;
        }

        return false;
    }

    public function isMoreThan13(\DateTime $birthday) : bool
    {
        $today = new \DateTime("now");

        if (!empty($birthday)) {
            $age =  $today->diff($birthday)->y;

            if ($age >= 13) {
                return true;
            }
        }
        return false;
    }

    public function isValid(User $user) : bool
    {
        if (
            $this->isNotEmpty($user->getFirstname(), $user->getLastname()) &&
            $this->validEmail($user->getEmail()) &&
            $this->validPassword($user->getPassword()) &&
            $this->isMoreThan13($user->getBirthday())
        ) {
            return true;
        }

        return false;
    }

    public function createToDoList(User $user): bool
    {
        if ($user->isValid($user) && null === $user->hasCreatedToDoList) {
            // Instanciation de l'objet ToDoList
            $toDoList = new ToDoList();
            $toDoList->setName('Test to do list');
            $toDoList->setCreatedAt(new \DateTimeImmutable());

            $user->setToDoList($toDoList);
            $user->setHasCreatedToDoList(true);

            return true;
        } else {
            return false;
        }

    }

    public function addItemToToDoList(Item $item) : bool
    {
        if(!empty($this->getToDoList()))
        {
            $nbItems = sizeof($this->getToDoList()->getItems());
            if ($nbItems < 10 &&
                $this->getToDoList()->checkUnicity($this->getToDoList(), $item) &&
                ($this->getToDoList()->checkLastItemCreation() > 30) &&
                ($item->isLessThan1000($item))
            )
            {
                    if($nbItems === 8)
                    {
                        EmailSenderService::send($this->getEmail());
                    }
                    $this->getToDoList()->addItem($item);
                    return true;
            }
        }
        return false;
    }

    public function getBirthday(): ?\DateTimeInterface
    {
        return $this->birthday;
    }

    public function setBirthday(\DateTimeInterface $birthday): self
    {
        $this->birthday = $birthday;

        return $this;
    }

}
