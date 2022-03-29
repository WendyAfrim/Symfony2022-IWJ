<?php

namespace App\Entity;

use App\Repository\UserRepository;
use Carbon\Carbon;
use Doctrine\ORM\Mapping as ORM;
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

    private Carbon $birthday;

    /**
     * @ORM\OneToOne(targetEntity=ToDoList::class, mappedBy="validUser", cascade={"persist", "remove"})
     */
    private $toDoList;

    /**
     * @ORM\Column(type="boolean")
     */
    private $hasCreatedToDoList;


//    public function __construct(string $firstname, string $lastname, string $email, string $password, Date $birthday)
//    {
//        $this->firstname = $firstname;
//        $this->lastname = $lastname;
//        $this->email = $email;
//        $this->password = $password;
//        $this->birthday = $birthday;
//    }

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

    /**
     * @return Carbon
     */
    public function getBirthday(): Carbon
    {
        return $this->birthday;
    }

    /**
     * @param Carbon $birthday
     */
    public function setBirthday(Carbon $birthday): void
    {
        $this->birthday = $birthday;
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

    public function isMoreThan13(Carbon $birthday) : bool
    {
        if (!empty($birthday)) {
            $age =  Carbon::parse($birthday)->age;
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

    public function addItemToToDoList(User $user) : bool
    {
       $items = $user->getToDoList()->getItems();
       $nbItems = sizeof($items);

       if ($nbItems < 10) {

       }

    }

    public function getLastItemCreation(Item $item) : \DateTimeImmutable
    {
        $creationDate = $item->getCreatedAt();

        return $creationDate;
    }
}
