<?php
function require_fields(array $input, array $requiredFields): array
{
    $errors = [];

    foreach ($requiredFields as $field) {
        if (!isset($input[$field]) || $input[$field] === '') {
            $errors[] = [
                'field'   => $field,
                'message' => 'Field wajib diisi.',
            ];
        }
    }

    return $errors;
}
