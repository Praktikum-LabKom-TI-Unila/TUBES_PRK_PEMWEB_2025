<?php

function with_prefix($path) {
    return "api/$path";
}

// TODO use real controllers
return [
    'GET' => [
        with_prefix('mock') => ['MockController', 'sayHello']
    ],
    'POST' => [
        with_prefix('mock') => ['MockController', 'store']
    ]
];