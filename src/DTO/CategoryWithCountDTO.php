<?php

namespace App\DTO;

class CategoryWithCountDTO
{

    public function __construct(
        public readonly int $id,
        public readonly string $name,
        public readonly int $count
    ){

    }

}
