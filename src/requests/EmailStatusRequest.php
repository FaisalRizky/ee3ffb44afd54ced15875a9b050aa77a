<?php

namespace Requests;

class EmailStatusRequest
{
    private $data;
    private $errors = [];

    public function __construct(array $data)
    {
        $this->data = $data;
    }

    public function validate(): bool
    {
        $this->errors = []; // Reset errors

        // Validate 'email_id'
        if (empty($this->data['email_id']) || !is_string($this->data['email_id'])) {
            $this->errors['email_id'] = 'email_id is required and must be a string.';
        }

        return empty($this->errors);
    }

    public function getErrors(): array
    {
        return $this->errors;
    }

    public function getData(): array
    {
        return $this->data;
    }
}
