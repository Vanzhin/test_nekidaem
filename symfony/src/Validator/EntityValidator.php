<?php

namespace App\Validator;

use Symfony\Component\Validator\Validator\ValidatorInterface;

class EntityValidator
{

    private ValidatorInterface $validator;

    public function __construct(ValidatorInterface $validator)
    {
        $this->validator = $validator;
    }

    public function validate($entity): bool|string
    {
        $errors = $this->validator->validate($entity);

        if (count($errors) > 0) {
            return (string)$errors;
        }
        return false;
    }
}