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
