<?php

namespace App\Message;

final readonly class RecipePDFMessage
{
    public function __construct(
        public int $id
    ) {

    }
}
