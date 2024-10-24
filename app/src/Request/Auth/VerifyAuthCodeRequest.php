<?php

namespace App\Request\Auth;

use App\Request\AbstractRequest;
use App\Validator\PhoneNumber;
use Symfony\Component\Validator\Constraints as Assert;
use Symfony\Component\Validator\Constraints\NotBlank;

class VerifyAuthCodeRequest extends AbstractRequest
{
    #[NotBlank()]
    #[Assert\Type('string')]
    #[PhoneNumber()]
    public ?string $phone = null;

    #[NotBlank()]
    #[Assert\Type('integer')]
    #[Assert\Positive]
    public ?int $code = null;
}