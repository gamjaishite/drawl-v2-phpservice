<?php

class TagService
{
    private TagRepository $tagRepository;

    public function __construct(TagRepository $tagRepository)
    {
        $this->tagRepository = $tagRepository;
    }

    public function findAll()
    {
        $query = $this->tagRepository->query();
        $projection = ["id", "name"];
        $tags = $query->get($projection, 1, 100);
        return $tags;
    }
}