<?php

namespace App\Entity;

use App\Repository\ToDoListRepository;
use Carbon\Carbon;
use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;
use Doctrine\ORM\Mapping as ORM;

/**
 * @ORM\Entity(repositoryClass=ToDoListRepository::class)
 */
class ToDoList
{
    /**
     * @ORM\Id
     * @ORM\GeneratedValue
     * @ORM\Column(type="integer")
     */
    private $id;

    /**
     * @ORM\OneToOne(targetEntity=User::class, inversedBy="toDoList", cascade={"persist", "remove"})
     */
    private $validUser;

    /**
     * @ORM\OneToMany(targetEntity=Item::class, mappedBy="toDoList")
     */
    private $items;

    /**
     * @ORM\Column(type="string", length=255)
     */
    private $name;

    /**
     * @ORM\Column(type="datetime_immutable")
     */
    private $created_at;

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

    public function getCreatedAt(): ?\DateTimeImmutable
    {
        return $this->created_at;
    }

    public function setCreatedAt(\DateTimeImmutable $created_at): self
    {
        $this->created_at = $created_at;

        return $this;
    }

    public function checkUnicity(ToDoList $toDoList, Item $newItem ) : bool
    {
        $items = $toDoList->getItems();

        foreach ($items as $item)
        {
            if ($item->getName() === $newItem->getName()) {
                return false;
            }
        }

        return true;
    }

    public function checkLastCreation() : int
    {
        $now = new \DateTime("now");

        $itemCreationDate = $this->getItems()->last()->getCreatedAt();

        return $itemCreationDate->diff($now)->i;
    }

 }
