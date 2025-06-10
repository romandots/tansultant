<?php

$lastCommit = env('GIT_LAST_COMMIT', 'dev');
$patch = (int)preg_replace('/[\D\s]/', '', $lastCommit);
$version = [
    'major' => 1,
    'minor' => 0,
    'patch' => $patch,
];

return $version + [
        'full_version' => sprintf('%d.%d.%d', $version['major'], $version['minor'], $version['patch']),
    ];