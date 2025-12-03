<?php

class MockController {
    function sayHello() {
        echo json_encode(['message' => 'Hello, World!']);
    }

    function store() {
        echo json_encode(['received' => "data stored"]);
    }
}