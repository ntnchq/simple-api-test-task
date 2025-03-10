<?php

declare(strict_types=1);

namespace App\Article\Entity;

use App\Article\Repository\ArticleRepository;
use App\Shared\Entity\IntegerIdTrait;
use App\Shared\Entity\TimestampableTrait;
use App\Tag\Entity\Tag;
use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;
use Doctrine\Common\Collections\Criteria;
use Doctrine\ORM\Mapping as ORM;

#[ORM\Entity(repositoryClass: ArticleRepository::class)]
#[ORM\HasLifecycleCallbacks]
#[ORM\Index(columns: ['title'])]
class Article
{
    use IntegerIdTrait;
    use TimestampableTrait;

    /** @var Collection<int, ArticleTag> */
    #[ORM\OneToMany(
        targetEntity: ArticleTag::class,
        mappedBy: 'article',
        cascade: ['persist', 'remove'],
        orphanRemoval: true
    )]
    private Collection $articleTags;

    public function __construct(
        #[ORM\Column(length: 255)]
        private string $title,
    ) {
        $this->fillTimestampable();
        $this->articleTags = new ArrayCollection();
    }

    public function getTitle(): string
    {
        return $this->title;
    }

    public function setTitle(string $title): self
    {
        $this->title = $title;

        return $this;
    }

    /** @return Collection<int, ArticleTag> */
    public function getArticleTags(): Collection
    {
        return $this->articleTags;
    }

    /** @return array<int, Tag> */
    public function getTags(): array
    {
        return array_map(
            static fn (ArticleTag $articleTag): Tag => $articleTag->getTag(),
            $this->articleTags->toArray()
        );
    }

    public function addTag(Tag $tag): self
    {
        $criteria = Criteria::create()
            ->where(Criteria::expr()->eq('tag', $tag));

        if (!$this->articleTags->matching($criteria)->count()) {
            $this->articleTags->add(new ArticleTag($this, $tag));
        }

        return $this;
    }

    public function removeTag(Tag $tag): self
    {
        $criteria = Criteria::create()
            ->where(Criteria::expr()->eq('tag', $tag));

        $articleTag = $this->articleTags->matching($criteria)->first();
        if ($articleTag) {
            $this->articleTags->removeElement($articleTag);
        }

        return $this;
    }
}
