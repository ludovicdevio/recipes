<?php

namespace App\Event;

use App\DTO\ContactDTO;

class ContactRequestEvent
{

    public function __construct(public readonly ContactDTO $data){

    }

}
