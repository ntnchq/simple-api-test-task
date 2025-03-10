<?php

declare(strict_types=1);

namespace App\Article\Entity;

use App\Shared\Entity\IntegerIdTrait;
use App\Shared\Entity\TimestampableTrait;
use App\Tag\Entity\Tag;
use Doctrine\ORM\Mapping as ORM;

#[ORM\Entity]
#[ORM\HasLifecycleCallbacks]
#[ORM\UniqueConstraint(columns: ['article_id', 'tag_id'])]
#[ORM\Index(columns: ['article_id'])]
#[ORM\Index(columns: ['tag_id'])]
class ArticleTag
{
    use IntegerIdTrait;
    use TimestampableTrait;

    public function __construct(
        #[ORM\ManyToOne]
        #[ORM\JoinColumn(nullable: false)]
        private Article $article,
        #[ORM\ManyToOne]
        #[ORM\JoinColumn(nullable: false)]
        private Tag $tag,
    ) {
        $this->fillTimestampable();
    }

    public function getArticle(): Article
    {
        return $this->article;
    }

    public function getTag(): Tag
    {
        return $this->tag;
    }
}
