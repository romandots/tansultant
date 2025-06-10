<?php

namespace App\Support;

class Shared
{
    public static function generatePassword(int $length = 8): string
    {
        return \Str::random($length);
    }

    /**
     * Format phone number to mask format
     * @param string $phone
     * @return string
     */
    public static function phoneFormat(string $phone): string
    {
        $digits = \preg_replace('/\D/', '', $phone);
        return \preg_replace('/^([78]?)(\d{3})(\d{3})(\d{2})(\d+)/', '+7-\2-\3-\4-\5', $digits);
    }

    /**
     * @param array $failed
     * @return array
     */
    public static function formatValidationErrors(array $failed): array
    {
        $errors = [];

        foreach ($failed as $field => $rules) {
            $errors[$field] = [];

            foreach ((array)$rules as $rule => $ruleData) {
                $ruleName = \Str::snake($rule);
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

    /**
     * @param \Carbon\Carbon $date
     * @return string
     */
    public static function weekday(\Carbon\Carbon $date): string
    {
        return \Carbon\Carbon::getDays()[$date->dayOfWeek];
    }

    /**
     * @param object|string $object
     * @return string
     */
    public static function baseClassname($object): string
    {
        if (\is_object($object)) {
            $object = \get_class($object);
        }

        return \basename(\str_replace('\\', '/', $object));
    }

    /**
     * @return string
     * @throws \Exception
     */
    public static function uuid(): string
    {
        return \Ramsey\Uuid\Uuid::uuid4()->toString();
    }

    /**
     * @param string $table
     * @param string $column
     * @param object[] $values
     */
    public static function convertPostgresColumnTextToEnum(string $table, string $column, array $values): void
    {
        $type = "{$table}_{$column}";

        $values = \array_map(function (object $item) {
            return "'{$item->value}'";
        }, $values);
        $valueString = \implode(',', $values);

        \DB::unprepared("CREATE TYPE {$type} AS ENUM({$valueString})");
        \DB::unprepared(
            "ALTER TABLE {$table} ALTER COLUMN {$column} TYPE {$type} USING
        {$column}::text::{$type}"
        );
    }

    /**
     * @param string $table
     * @param string $column
     * @param array $values
     */
    public static function convertPostgresColumnEnumToEnum(string $table, string $column, array $values): void
    {
        $type = "{$table}_{$column}";

        $values = \array_map(function ($item) {
            return "'{$item}'";
        }, $values);
        $valueString = \implode(',', $values);

        \DB::unprepared("ALTER TYPE {$type} RENAME TO {$type}_old");
        \DB::unprepared("CREATE TYPE {$type} AS ENUM({$valueString})");
        \DB::unprepared(
            "ALTER TABLE {$table} ALTER COLUMN {$column} TYPE {$type} USING
        {$column}::text::{$type}"
        );
        \DB::unprepared('DROP TYPE IF EXIST {$type}_old');
    }

    /**
     * @param string|integer $phoneNumber
     * @return string
     */
    public static function normalizePhoneNumber($phoneNumber): string
    {
        $phoneNumber = (string)$phoneNumber;

        return \preg_replace('/\D/', '', $phoneNumber);
    }

    /**
     * @param mixed|null $data
     * @param int $status
     * @param array $headers
     * @return \Illuminate\Http\JsonResponse
     */
    public static function jsonResponse(
        $data = null,
        int $status = 200,
        array $headers = []
    ): \Illuminate\Http\JsonResponse {
        return new \Illuminate\Http\JsonResponse(
            $data,
            $status,
            $headers,
            \JSON_UNESCAPED_UNICODE | \JSON_UNESCAPED_SLASHES | \JSON_UNESCAPED_LINE_TERMINATORS
        );
    }

    public static function getStatusMessage(string $status, string $keyPrefix = ''): array
    {
        return [
            'status' => $status,
            'message' => \trans(($keyPrefix ? $keyPrefix . '.' : '') . 'status.' . $status)
        ];
    }

    /**
     * @param int $page
     * @param int $perPage
     * @param int $total
     * @return array
     */
    public static function formatPagination(int $page, int $perPage, int $total): array
    {
        $lastPage = (int)\ceil($total / $perPage);
        $to = $page * $perPage;
        $from = $to - $perPage + 1;
        if ($to > $total) {
            $to = $total;
        }
        return [
            'current_page' => $page,
            'per_page' => $perPage,
            'last_page' => $lastPage,
            'from' => $from,
            'to' => $to,
            'total' => $total
        ];
    }

    /**
     * @param string $prefix
     * @param string|object $messageKey
     * @return string
     */
    public static function translate(string $prefix, mixed $messageKey): string
    {
        return \trans($prefix . '.' . (is_string($messageKey) ? $messageKey : $messageKey->value));
    }

    /**
     * @param string|Closure $fieldOrCallback
     * @param object $record
     * @return mixed
     */
    public static function propertyOrCallback(object $record, string|\Closure $fieldOrCallback): mixed
    {
        return is_string($fieldOrCallback)
            ? $record->{$fieldOrCallback}
            : $fieldOrCallback($record);
    }

    public static function isEnum(object $object): bool
    {
        return $object instanceof \UnitEnum;
    }

    public static function inProduction(): bool
    {
        return env('APP_ENV') === 'production';
    }

}