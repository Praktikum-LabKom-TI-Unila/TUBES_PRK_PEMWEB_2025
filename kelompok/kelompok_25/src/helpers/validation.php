<?php

/**
 * Validation Helper
 */

class Validator
{
    private $data;
    private $rules;
    private $errors = [];
    private $validated = [];

    public function __construct($data, $rules)
    {
        $this->data = $data;
        $this->rules = $rules;
    }

    public function validate()
    {
        foreach ($this->rules as $field => $ruleString) {
            $rules = explode('|', $ruleString);
            $value = $this->data[$field] ?? null;

            foreach ($rules as $rule) {
                $this->applyRule($field, $value, $rule);
            }

            if (!isset($this->errors[$field])) {
                $this->validated[$field] = $value;
            }
        }

        return empty($this->errors);
    }

    private function applyRule($field, $value, $rule)
    {
        // Parse rule dengan parameter (contoh: min:3)
        $params = [];
        if (strpos($rule, ':') !== false) {
            list($rule, $param) = explode(':', $rule, 2);
            $params = explode(',', $param);
        }

        $fieldName = ucwords(str_replace('_', ' ', $field));

        switch ($rule) {
            case 'required':
                if (empty($value) && $value !== '0') {
                    $this->errors[$field][] = "$fieldName wajib diisi.";
                }
                break;

            case 'email':
                if (!empty($value) && !filter_var($value, FILTER_VALIDATE_EMAIL)) {
                    $this->errors[$field][] = "$fieldName harus berupa email yang valid.";
                }
                break;

            case 'min':
                $min = $params[0] ?? 0;
                if (!empty($value) && strlen($value) < $min) {
                    $this->errors[$field][] = "$fieldName minimal $min karakter.";
                }
                break;

            case 'max':
                $max = $params[0] ?? 0;
                if (!empty($value) && strlen($value) > $max) {
                    $this->errors[$field][] = "$fieldName maksimal $max karakter.";
                }
                break;

            case 'numeric':
                if (!empty($value) && !is_numeric($value)) {
                    $this->errors[$field][] = "$fieldName harus berupa angka.";
                }
                break;

            case 'alpha':
                if (!empty($value) && !ctype_alpha($value)) {
                    $this->errors[$field][] = "$fieldName hanya boleh berisi huruf.";
                }
                break;

            case 'alphanumeric':
                if (!empty($value) && !ctype_alnum($value)) {
                    $this->errors[$field][] = "$fieldName hanya boleh berisi huruf dan angka.";
                }
                break;

            case 'confirmed':
                $confirmField = $field . '_confirmation';
                if (!empty($value) && $value !== ($this->data[$confirmField] ?? null)) {
                    $this->errors[$field][] = "$fieldName tidak sama dengan konfirmasi.";
                }
                break;

            case 'unique':
                // Format: unique:table,column
                if (count($params) >= 2 && !empty($value)) {
                    $table = $params[0];
                    $column = $params[1];
                    $except = $params[2] ?? null;

                    $db = Database::getInstance()->getConnection();
                    $sql = "SELECT COUNT(*) FROM $table WHERE $column = ?";
                    $sqlParams = [$value];

                    if ($except) {
                        $sql .= " AND id != ?";
                        $sqlParams[] = $except;
                    }

                    $stmt = $db->prepare($sql);
                    $stmt->execute($sqlParams);
                    $count = $stmt->fetchColumn();

                    if ($count > 0) {
                        $this->errors[$field][] = "$fieldName sudah terdaftar.";
                    }
                }
                break;

            case 'exists':
                // Format: exists:table,column
                if (count($params) >= 2 && !empty($value)) {
                    $table = $params[0];
                    $column = $params[1];

                    $db = Database::getInstance()->getConnection();
                    $stmt = $db->prepare("SELECT COUNT(*) FROM $table WHERE $column = ?");
                    $stmt->execute([$value]);
                    $count = $stmt->fetchColumn();

                    if ($count === 0) {
                        $this->errors[$field][] = "$fieldName tidak ditemukan.";
                    }
                }
                break;
        }
    }

    public function errors()
    {
        return $this->errors;
    }

    public function validated()
    {
        return $this->validated;
    }

    public static function make($data, $rules)
    {
        return new self($data, $rules);
    }
}
