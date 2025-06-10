<?php

$patch = (int)preg_replace(
    '/[\D\s]/',
    '',
    env(
        'GIT_LAST_COMMIT',
        file_get_contents('/git_last_commit') ?? 0
    )
);
$version = [
    'major' => 1,
    'minor' => 0,
    'patch' => $patch,
];

return $version + [
        'full_version' => sprintf('%d.%d.%d', $version['major'], $version['minor'], $version['patch']),
    ];