<?php

namespace Requests;

class EmailRequest
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

        // Validate 'module'
        if (empty($this->data['module']) || !is_string($this->data['module'])) {
            $this->errors['module'] = 'Module is required and must be a string.';
        }

        // Validate 'emailId'
        if (empty($this->data['emailId']) || !is_string($this->data['emailId'])) {
            $this->errors['emailId'] = 'Email ID is required and must be a string.';
        }

        // Validate 'sender'
        if (empty($this->data['sender']) || !is_string($this->data['sender'])) {
            $this->errors['sender'] = 'Sender is required and must be a string.';
        }

        // Validate 'recepient'
        if (empty($this->data['recipient']) || !is_string($this->data['recipient'])) {
            $this->errors['recipient'] = 'Recipient is required and must be a string.';
        }

        // Validate 'subject'
        if (empty($this->data['subject']) || !is_string($this->data['subject'])) {
            $this->errors['subject'] = 'Subject is required and must be a string.';
        }

        // Validate 'content'
        if (empty($this->data['content']) || !is_string($this->data['content'])) {
            $this->errors['content'] = 'Content is required and must be a string.';
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
