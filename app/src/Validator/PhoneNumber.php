<?php

namespace App\Validator;

use Symfony\Component\Validator\Constraint;
#[\Attribute]
class PhoneNumber extends Constraint
{
    public string $message = 'Expected valid phone number.';

    public string $mode = 'strict';
}