<?php

namespace App\Entity;

use App\Controller\EmailSenderService;
use App\Repository\ToDoListRepository;
use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;
use Doctrine\ORM\Mapping as ORM;
use Symfony\Component\Serializer\Annotation\Groups;

/**
 * @ORM\Entity(repositoryClass=ToDoListRepository::class)
 */
class ToDoList
{
    /**
     * @ORM\Id
     * @ORM\GeneratedValue
     * @ORM\Column(type="integer")
     * @Groups("apiUser")
     */
    private $id;

    /**
     * @ORM\OneToOne(targetEntity=User::class, inversedBy="toDoList", cascade={"persist", "remove"})
     */
    private $validUser;

    /**
     * @ORM\OneToMany(targetEntity=Item::class, mappedBy="toDoList")
     * @Groups("apiUser")
     */
    private $items;

    /**
     * @ORM\Column(type="string", length=255)
     */
    private $name;

    /**
     * @ORM\Column(type="datetime")
     */
    private $created_at;

    private EmailSenderService $emailSenderService;

    public function __construct()
    {
        $this->items = new ArrayCollection();
    }

    public function getId(): ?int
    {
        return $this->id;
    }

    public function getValidUser(): ?User
    {
        return $this->validUser;
    }

    public function setValidUser(?User $validUser): self
    {
        $this->validUser = $validUser;

        return $this;
    }

    /**
     * @return Collection<int, Item>
     */
    public function getItems(): Collection
    {
        return $this->items;
    }

    public function addItem(Item $item): self
    {
        if (!$this->items->contains($item)) {
            $this->items[] = $item;
            $item->setToDoList($this);
        }

        return $this;
    }

    public function removeItem(Item $item): self
    {
        if ($this->items->removeElement($item)) {
            // set the owning side to null (unless already changed)
            if ($item->getToDoList() === $this) {
                $item->setToDoList(null);
            }
        }

        return $this;
    }

    public function getName(): ?string
    {
        return $this->name;
    }

    public function setName(string $name): self
    {
        $this->name = $name;

        return $this;
    }

    public function getCreatedAt(): ?\DateTime
    {
        return $this->created_at;
    }

    public function setCreatedAt(\DateTime $created_at): self
    {
        $this->created_at = $created_at;

        return $this;
    }

    public function isFull(): bool
    {
        if(count($this->getItems()) > 9)
        {
            return true;
        }
        return false;
    }

    public function iseighth(): bool
    {
        if(count($this->getItems()) > 7)
        {
            if(count($this->getItems()) === 8)
            {
                $emailSenderService= new EmailSenderService();
                $emailSenderService->emailSender($this->getValidUser()->getEmail());
            }
            return true;
        }
        return false;
    }

    public function lastAddItem(): int
    {
        $lastItem = $this->getItems()->last();
        $now = new \DateTime('now');
        if($lastItem === false)
        {
            return 31;
        }
        //dd($now, $lastItem->getCreationDate(), date_diff($now, $lastItem->getCreationDate() , true)->i );
        return ($now->diff($lastItem->getCreatedAt())->i);
    }
 }
