<?php

namespace Nebula\Validation;

use Nebula\Database\QueryBuilder;

class Validate
{
    /**
     * Note: you can modify these messages to anything you like
     */
    public static $messages = [
        "string" => "%label must be a string",
        "numeric" => "%label must be a number",
        "email" => "You must supply a valid email address",
        "required" => "%label is a required field",
        "match" => "%label does not match",
        "min_length" => "%label is too short (min length: %rule_extra)",
        "max_length" => "%label is too long (max length: %rule_extra)",
        "uppercase" =>
            "%label requires at least %rule_extra uppercase character",
        "lowercase" =>
            "%label requires at least %rule_extra lowercase character",
        "symbol" => "%label requires at least %rule_extra symbol character",
        "reg_ex" => "%label is invalid",
        "unique" => "%label must be unique",
    ];
    public static $errors = [];
    public static $custom = [];

    /**
     * Add arbitrary error to $errors array
     * @param mixed $item
     * @param mixed $replacements
     */
    public static function registerError($item, $replacements): void
    {
        self::$errors[$replacements["%field"]][] = strtr(
            self::$messages[$item],
            $replacements
        );
    }

    /**
     * Add arbitrary error to $errors array
     */
    public static function addError(string $item, string $msg = ""): void
    {
        // having $msg default to "" seems strange,
        // but sometimes we just want to know $item
        // has and error and the message is irrelevant
        self::$errors[$item][] = $msg;
    }

    /**
     * Validate the request data
     * @param array<int,mixed> $request_rules
     */
    public static function request(array $request_rules): bool
    {
        foreach ($request_rules as $request_item => $ruleset) {
            $value = request()->get($request_item) ?? null;
            if (!array_is_list($ruleset)) {
                $label = array_keys($ruleset)[0];
                $ruleset = array_values($ruleset)[0];
            } else {
                $label = null;
            }
            foreach ($ruleset as $rule_raw) {
                $rule_split = explode("=", $rule_raw);
                $rule = $rule_split[0];
                $extra = count($rule_split) == 2 ? $rule_split[1] : "";
                $label = $label ? $label : ucfirst($request_item);
                $result = match ($rule) {
                    "string" => self::isString($value),
                    "numeric" => self::isNumeric($value),
                    "email" => self::isEmail($value),
                    "required" => self::isRequired($value),
                    "match" => self::isMatch($request_item, $value),
                    "min_length" => self::isMinLength($value, $extra),
                    "max_length" => self::isMaxLength($value, $extra),
                    "uppercase" => self::isUppercase($value, $extra),
                    "lowercase" => self::isLowercase($value, $extra),
                    "symbol" => self::isSymbol($value, $extra),
                    "reg_ex" => self::regEx($value, $extra),
                    "unique" => self::unique($value, $extra, $request_item),
                    default => true,
                };
                if (!$result) {
                    self::registerError($rule, [
                        "%rule" => $rule,
                        "%rule_extra" => $extra,
                        "%field" => $request_item,
                        "%value" => $value,
                        "%label" => $label,
                    ]);
                }
                foreach (self::$custom as $custom_rule => $callback) {
                    if ($custom_rule === $rule) {
                        $result = trim($callback($rule, $value));
                        if (!is_null($result) && $result != "") {
                            self::$errors[$rule][] = $result;
                        }
                    }
                }
            }
        }
        return count(self::$errors) == 0;
    }

    /**
     * Request value must be a string
     * @param mixed $value
     */
    public static function isString($value): bool
    {
        return is_string($value);
    }

    /**
     * Request value must be numeric
     * @param mixed $value
     */
    public static function isNumeric($value): bool
    {
        return is_numeric($value);
    }

    /**
     * Request value must be an email
     * @param mixed $value
     */
    public static function isEmail($value): mixed
    {
        return filter_var($value, FILTER_VALIDATE_EMAIL);
    }

    /**
     * Request value is required
     * @param mixed $value
     */
    public static function isRequired($value): bool
    {
        return !is_null($value) && $value != "";
    }

    /**
     * Two request values match
     * You must have a field prefixed with _check for this to work
     * ie) password and password_check (must match)
     * @param mixed $item
     * @param mixed $value
     */
    public static function isMatch($item, $value): bool
    {
        $filtered_name = str_replace("_match", "", $item);
        $match_value = request()->get($filtered_name);
        if (is_null($match_value)) {
            return false;
        }
        return $match_value === $value;
    }

    /**
     * Request value has min length restriction
     * @param mixed $value
     * @param mixed $min_length
     */
    public static function isMinLength($value, $min_length): bool
    {
        return strlen($value) >= $min_length;
    }

    /**
     * Request value has max length restriction
     * @param mixed $value
     * @param mixed $max_length
     */
    public static function isMaxLength($value, $max_length): bool
    {
        return strlen($value) <= $max_length;
    }

    /**
     * Request value is uppercase
     * @param mixed $value
     * @param mixed $count
     */
    public static function isUppercase($value, $count): bool
    {
        preg_match_all("/[A-Z]/", $value, $matches);
        return !empty($matches[0]) && count($matches) >= $count;
    }

    /**
     * Request value is lowercase
     * @param mixed $value
     * @param mixed $count
     */
    public static function isLowercase($value, $count): bool
    {
        preg_match_all("/[a-z]/", $value, $matches);
        return !empty($matches[0]) && count($matches) >= $count;
    }

    /**
     * Request value has a symbol
     * @param mixed $value
     * @param mixed $count
     */
    public static function isSymbol($value, $count): bool
    {
        preg_match_all("/[$&+,:;=?@#|'<>.^*()%!-]/", $value, $matches);
        return !empty($matches[0]) && count($matches) >= $count;
    }

    /**
     * Request value matches regex pattern
     * @param mixed $value
     * @param mixed $pattern
     */
    public static function regEx($value, $pattern): int|bool
    {
        return preg_match("/{$pattern}/", $value);
    }

    /**
     * Request value must be unique
     * @param mixed $value
     * @param mixed $table
     * @param mixed $column
     */
    public static function unique($value, $table, $column): bool
    {
        if (!$table) {
            throw new \Error("unique requires a table name");
        }
        $qb = QueryBuilder::select($table)->where([[$column, "=", $value]]);
        $result = db()->select($qb->build(), ...$qb->values());
        return !$result;
    }
}
