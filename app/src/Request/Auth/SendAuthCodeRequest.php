<?php

namespace App\Request\Auth;

use App\Request\AbstractRequest;
use App\Validator\PhoneNumber;
use Symfony\Component\Validator\Constraints\NotBlank;
use Symfony\Component\Validator\Constraints\Type;

class SendAuthCodeRequest extends AbstractRequest
{
    #[NotBlank()]
    #[Type('string')]
    #[PhoneNumber()]
    public ?string $phone = null;
}