<?php

namespace App\Entity;

use App\Repository\TagRepository;
use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;
use Doctrine\DBAL\Types\Types;
use Doctrine\ORM\Mapping as ORM;
use Symfony\Component\Serializer\Annotation\Groups;
use Symfony\Component\Validator\Constraints as Assert;

#[ORM\Entity(repositoryClass: TagRepository::class)]
class Tag
{
    #[ORM\Id]
    #[ORM\GeneratedValue]
    #[ORM\Column]
    #[Groups(['simple-tag', 'complete-tag'])]
    private ?int $id = null;

    #[ORM\Column(length: 255)]
    #[Groups(['simple-tag', 'complete-tag'])]
    private ?string $name = null;

    #[ORM\Column(type: Types::TEXT, nullable: false)]
    #[Groups(['simple-tag', 'complete-tag'])]
    #[Assert\NotBlank()]
    private ?string $description = 'Default description';

    #[ORM\ManyToMany(targetEntity: Question::class, mappedBy: 'tags')]
    #[Groups(['complete-tag'])]
    private Collection $questions;

    #[ORM\ManyToMany(targetEntity: Administrator::class, mappedBy: 'domains')]
    #[Groups(['complete-tag'])]
    private Collection $administrators;

    public function __construct()
    {
        $this->questions = new ArrayCollection();
        $this->administrators = new ArrayCollection();
    }

    public function getId(): ?int
    {
        return $this->id;
    }

    public function getName(): ?string
    {
        return $this->name;
    }

    public function setName(string $name): static
    {
        $this->name = $name;

        return $this;
    }

    public function getDescription(): ?string
    {
        return $this->description;
    }

    public function setDescription(string $description): static
    {
        $this->description = $description;

        return $this;
    }

    /**
     * @return Collection<int, Question>
     */
    public function getQuestions(): Collection
    {
        return $this->questions;
    }

    public function addQuestion(Question $question): static
{
    if (!$this->questions->contains($question)) {
        $this->questions->add($question);
        $question->addTags($this); // Use addTags instead of addTag
    }

    return $this;
}

public function removeQuestion(Question $question): static
{
    if ($this->questions->removeElement($question)) {
        $question->removeTags($this); // Use removeTags instead of removeTag
    }

    return $this;
}

    /**
     * @return Collection<int, Administrator>
     */
    public function getAdministrators(): Collection
    {
        return $this->administrators;
    }

    public function addAdministrator(Administrator $administrator): static
    {
        if (!$this->administrators->contains($administrator)) {
            $this->administrators->add($administrator);
            $administrator->addDomain($this);
        }

        return $this;
    }

    public function removeAdministrator(Administrator $administrator): static
    {
        if ($this->administrators->removeElement($administrator)) {
            $administrator->removeDomain($this);
        }

        return $this;
    }
}