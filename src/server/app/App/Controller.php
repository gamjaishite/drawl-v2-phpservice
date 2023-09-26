<?php

class Controller
{
    public function view(string $view, array $data = []): void
    {
        require_once __DIR__ . '/../View' . $view . '.php';
    }
}
