<?php

namespace App\Entity;

use App\Repository\ArticleRepository;
use Doctrine\ORM\Mapping as ORM;

#[ORM\Entity(repositoryClass: ArticleRepository::class)]
class Article
{
    #[ORM\Id]
    #[ORM\Column(type: 'guid')]
    public string $id;

    public function __construct(
        #[ORM\Column(length: 255)]
        public string $title,
    ) {
        $this->id = uuid_create();
    }
}
