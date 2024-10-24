<?php

namespace App\Request;

use ReflectionClass;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\RequestStack;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpKernel\Exception\HttpException;
use Symfony\Component\Validator\ConstraintViolation;
use Symfony\Component\Validator\Validator\ValidatorInterface;

class AbstractRequest
{
    private const FORMAT_JSON = 'json';

    public function __construct(
        protected ValidatorInterface $validator,
        protected RequestStack       $requestStack,
    )
    {
        $this->populate();
        $this->validate();
    }

    public function getRequest(): Request
    {
        return $this->requestStack->getCurrentRequest();
    }

    protected function populate(): void
    {
        $request = $this->getRequest();
        if (!self::isValidFormat($request)) {
            throw new HttpException(Response::HTTP_BAD_REQUEST, 'expected application/json on header Content-Type request');
        }
        $reflection = new ReflectionClass($this);

        foreach ($request->toArray() as $property => $value) {
            if (property_exists($this, $property)) {
                $reflectionProperty = $reflection->getProperty($property);
                $reflectionProperty->setValue($this, $value);
            }
        }
    }

    protected function validate(): void
    {
        $violations = $this->validator->validate($this);
        if (count($violations) < 1) {
            return;
        }

        $errors = [];

        /** @var ConstraintViolation */
        foreach ($violations as $violation) {
            $errors[] = [
                'property' => $violation->getPropertyPath(),
                'value' => $violation->getInvalidValue(),
                'message' => $violation->getMessage(),
            ];
        }

        throw new HttpException(Response::HTTP_UNPROCESSABLE_ENTITY, json_encode($errors));
    }

    private static function isValidFormat(Request $request): bool
    {
        return in_array($request->getContentTypeFormat(), self::getFormatsAvailable());
    }

    private static function getFormatsAvailable(): array
    {
        return [self::FORMAT_JSON];
    }
}