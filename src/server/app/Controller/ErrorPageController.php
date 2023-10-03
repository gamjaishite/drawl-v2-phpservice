<?php
require_once __DIR__ . '/../App/View.php';

class ErrorPageController
{
    public function fourohfour(): void
    {
        View::render('404', [
            'title' => '404',
            'styles' => [
                '/css/error-page.css',
            ],
        ]);
    }

    public function fivehundred(): void
    {
        View::render('500', [
            'title' => '500',
            'styles' => [
                '/css/error-page.css',
            ],
        ]);
    }
}