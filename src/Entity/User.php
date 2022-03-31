<?php

namespace App\Entity;

use App\Repository\UserRepository;
use Doctrine\ORM\Mapping as ORM;
use Symfony\Component\Serializer\Annotation\Groups;


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
     * @Groups("apiUser")
     */
    private $id;

    /**
     * @ORM\Column(type="string", length=255)
     * @Groups("apiUser")
     */
    private $firstname;

    /**
     * @ORM\Column(type="string", length=255)
     * @Groups("apiUser")
     */
    private $lastname;

    /**
     * @ORM\Column(type="string", length=255)
     * @Groups("apiUser")
     */
    private $email;

    /**
     * @ORM\Column(type="string", length=255)
     * @Groups("apiUser")
     */
    private $password;


    /**
     * @ORM\Column(type="datetime")
     * @Groups("apiUser")
     */
    private $birthday;

    /**
     * @ORM\OneToOne(targetEntity=ToDoList::class, mappedBy="validUser", cascade={"persist", "remove"})
     * @Groups("apiUser")
     */
    private $toDoList;

    /**
     * @ORM\Column(type="boolean")
     * @Groups("apiUser")
     */
    private $hasCreatedToDoList;


    public function __construct(string $firstname, string $lastname, string $email, string $password, \DateTime $birthday)
    {
        $this->firstname = $firstname;
        $this->lastname = $lastname;
        $this->email = $email;
        $this->password = $password;
        $this->birthday = $birthday;
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

    public function getBirthday(): ?\DateTimeInterface
    {
        return $this->birthday;
    }

    public function setBirthday(\DateTimeInterface $birthday): self
    {
        $this->birthday = $birthday;
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

    public function validEmail() : bool
    {
        if (!empty($this->getEmail()) && filter_var($this->getEmail(), FILTER_VALIDATE_EMAIL)) {
            return true;
        }
        return false;
    }

    public function validPassword() : bool
    {
        if (!empty($this->getPassword()) && strlen($this->getPassword()) >= 8 && strlen($this->getPassword()) <= 40) {
            return true;
        }
        return false;
    }

    public function isNotEmpty() : bool
    {
        if (!empty($this->getFirstname()) && !empty($this->getLastname())) {
            return true;
        }

        return false;
    }

    public function is13atLeast() : bool
    {
        $today = new \DateTime();
         if ($this->getBirthday()->diff($today)->y > 12)
        {
                return true;
        }
        return false;
    }

    public function isValid() : bool
    {
        if (
            $this->isNotEmpty() &&
            $this->validEmail() &&
            $this->validPassword() &&
            $this->is13atLeast()
        ) {
            return true;
        }

        return false;
    }

    public function createToDoList(): bool
    {
        if ($this->isValid() && null === $this->hasCreatedToDoList) {
            // Instanciation de l'objet ToDoList
            $toDoList = new ToDoList();
            $toDoList->setName('Test to do list');
            $toDoList->setCreatedAt(new \DateTime);
            $this->setToDoList($toDoList);
            $this->setHasCreatedToDoList(true);

            return true;
        } else {
            return false;
        }

    }



    public function getLastItemCreation(Item $item) : \DateTime
    {
        $creationDate = $item->getCreatedAt();

        return $creationDate;
    }

    private function isValidName(): bool
    {
        if(!empty($this->getFirstname()) && !empty($this->getLastname()))
        {
            return true;
        }
        return false;
    }
    private function isValidEmail(): bool
    {
        if(!filter_var($this->getEmail(), FILTER_VALIDATE_EMAIL))
        {
            return false;
        }
        return true;
    }

    private function isValidPassword(): bool
    {
        if(!in_array(strlen($this->getPassword()), range(8, 40)))
        {
            return false;
        }
        return true;
    }

    private function isValidAge(): bool
    {
        $today = new \DateTime('now');
        if($today->diff($this->getBirthday() , true)->y > 12)
        {
            return true;
        }
        return false;
    }

    public function isValidUser(): bool
    {
        return $this->isValidAge()
            && $this->isValidEmail()
            && $this->isValidName()
            && $this->isValidPassword();

    }
}
