<?php

namespace App\Service\Publication;

use App\Repository\PublicationRepository;
use Symfony\Component\String\Slugger\SluggerInterface;

class PublicationSlug
{
    private PublicationRepository $publicationRepository;
    private SluggerInterface $slugger;

    public function __construct(PublicationRepository $publicationRepository, SluggerInterface $slugger)
    {
        $this->publicationRepository = $publicationRepository;
        $this->slugger = $slugger;
    }

    public function create(string $slugCandidate): string
    {
        $slug = $this->slugger->slug($slugCandidate)->lower();
        $i = 1;
        $initialSlug = $slug;
        while ($this->publicationRepository->count(['slug' => $slug]) > 0) {
            $slug = $initialSlug . '-' . $i++;
        }

        return $slug;
    }
}
