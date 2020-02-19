<?php
/**
 * File: helpers.php
 * Author: Roman Dots <ram.d.kreiz@gmail.com>
 * Date: 2019-07-18
 * Copyright (c) 2019
 */

declare(strict_types=1);

if (!function_exists('phone_format')) {
    /**
     * Format phone number to mask format
     * @param string $phone
     * @return string
     */
    function phone_format(string $phone): string {
        $digits = \preg_replace('/\D/', '', $phone);
        return \preg_replace('/^([78]?)(\d{3})(\d{3})(\d{2})(\d+)/', '+7-\2-\3-\4-\5', $digits);
    }
}

if (!function_exists('format_validation_errors')) {
    /**
     * @param array $failed
     * @return array
     */
    function format_validation_errors(array $failed): array
    {
        $errors = [];

        foreach ($failed as $field => $rules) {
            $errors[$field] = [];

            foreach ((array)$rules as $rule => $ruleData) {
                $ruleName = Illuminate\Support\Str::snake($rule);
                if ('unique' === $ruleName || 'exists' === $ruleName) {
                    $ruleData = [];
                }

                $newRule = ['name' => $ruleName];

                if (0 !== \count($ruleData)) {
                    $newRule['params'] = $ruleData;
                }

                $errors[$field][] = $newRule;
            }
        }

        return $errors;
    }
}

if (!function_exists('weekday')) {
    /**
     * @param \Carbon\Carbon $date
     * @return string
     */
    function weekday(\Carbon\Carbon $date): string
    {
        return \Carbon\Carbon::getDays()[$date->dayOfWeek];
    }
}

if (!function_exists('base_classname')) {
    /**
     * @param object|string $object
     * @return string
     */
    function base_classname($object): string
    {
        if (\is_object($object)) {
            $object = \get_class($object);
        }

        return \basename(\str_replace('\\', '/', $object));
    }
}

if (!function_exists('uuid')) {
    /**
     * @return string
     * @throws Exception
     */
    function uuid(): string
    {
        return \Ramsey\Uuid\Uuid::uuid4()->toString();
    }
}

if (!function_exists('convertPostgresColumnTextToEnum')) {
    /**
     * @param string $table
     * @param string $column
     * @param array $values
     */
    function convertPostgresColumnTextToEnum(string $table, string $column, array $values): void
    {
        $type = "{$table}_{$column}";

        $values = \array_map(function ($item) {
            return "'{$item}'";
        }, $values);
        $valueString = \implode(',', $values);

        \DB::unprepared("CREATE TYPE {$type} AS ENUM({$valueString})");
        \DB::unprepared("ALTER TABLE {$table} ALTER COLUMN {$column} TYPE {$type} USING
        {$column}::text::{$type}");
    }
}

if (!function_exists('convertPostgresColumnEnumToEnum')) {
    /**
     * @param string $table
     * @param string $column
     * @param array $values
     */
    function convertPostgresColumnEnumToEnum(string $table, string $column, array $values): void
    {
        $type = "{$table}_{$column}";

        $values = \array_map(function ($item) {
            return "'{$item}'";
        }, $values);
        $valueString = \implode(',', $values);

        \DB::unprepared("ALTER TYPE {$type} RENAME TO {$type}_old");
        \DB::unprepared("CREATE TYPE {$type} AS ENUM({$valueString})");
        \DB::unprepared("ALTER TABLE {$table} ALTER COLUMN {$column} TYPE {$type} USING
        {$column}::text::{$type}");
        \DB::unprepared('DROP TYPE IF EXIST {$type}_old');
    }
}

if (!function_exists('normalize_phone_number')) {
    /**
     * @param string|integer $phoneNumber
     * @return string
     */
    function normalize_phone_number($phoneNumber): string
    {
        $phoneNumber = (string)$phoneNumber;

        return \preg_replace('/\D/', '', $phoneNumber);
    }
}

if (!function_exists('json_response')) {
    /**
     * @param mixed|null $data
     * @param int $status
     * @param array $headers
     * @return \Illuminate\Http\JsonResponse
     */
    function json_response($data = null, int $status = 200, array $headers = []): \Illuminate\Http\JsonResponse
    {
        return new \Illuminate\Http\JsonResponse(
            $data,
            $status,
            $headers,
            \JSON_UNESCAPED_UNICODE | \JSON_UNESCAPED_SLASHES | \JSON_UNESCAPED_LINE_TERMINATORS
        );
    }
}

if (!function_exists('get_status_message')) {
    function get_status_message(string $status): array
    {
        return [
            'status' => $status,
            'message' => \trans('statuses.' . $status)
        ];
    }
}