<?php
/**
 * File: verification.php
 * Author: Roman Dots <ram.d.kreiz@gmail.com>
 * Date: 2019-12-5
 * Copyright (c) 2019
 */

declare(strict_types=1);

return [
    'log_messages' => true,
    'send_messages' => false,
    'code_length' => 4,
    'timeout' => 60,
    'use_digits' => true,
    'use_letters' => false,
    'max_tries' => 4,
    'cleanup_timeout' => 60 * 10,
];
