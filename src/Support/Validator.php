<?php

namespace Leryr\Mymillionpesoproject\Support;

class Validator
{
    private array $errors = [];

    public function require(array $data, string $field, string $label = null): self
    {
        $label = $label ?? ucfirst($field);

        if (empty($data[$field]) && $data[$field] !== '0') {
            $this->errors[$field] = "$label is required";
        }

        return $this;
    }

    public function minLength(array $data, string $field, int $length, string $label = null): self
    {
        $label = $label ?? ucfirst($field);

        if (isset($data[$field]) && strlen((string) $data[$field]) < $length) {
            $this->errors[$field] = "$label must be at least $length characters";
        }

        return $this;
    }

    public function email(array $data, string $field): self
    {
        if (isset($data[$field]) && !filter_var($data[$field], FILTER_VALIDATE_EMAIL)) {
            $this->errors[$field] = 'Invalid email format';
        }

        return $this;
    }

    public function fails(): bool
    {
        return !empty($this->errors);
    }

    public function errors(): array
    {
        return $this->errors;
    }
}
