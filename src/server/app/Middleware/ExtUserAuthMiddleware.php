<?php

require_once __DIR__ . '/../Common/CustomResponse.php';

class ExtUserAuthMiddleware
{
    public function __construct()
    {
    }

    public function run(): void
    {
        $headers = getallheaders();
        $apiKey = "";
        foreach ($headers as $headerName => $headerValue) {
            if (strtolower($headerName) === 'authorization') {
                $apiKey = explode(' ', $headerValue)[1];
            }
        }

        if (!password_verify($apiKey, getenv('API_KEY'))) {
            http_response_code(401);
            $response = new CustomResponse();
            $response->status = 401;
            $response->message = "Unauthorized";
            $response->data = $apiKey;

            echo json_encode($response);
            exit();
        }
    }
}