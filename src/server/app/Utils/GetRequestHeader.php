<?php

class GetRequestHeader
{
    public static function getHeader(string $name, int $position): string
    {
        $headers = getallheaders();
        $header = "";
        foreach ($headers as $headerName => $headerValue) {
            if (strtolower($headerName) === $name) {
                $header = explode(' ', $headerValue)[$position - 1];
            }
        }

        return $header;
    }
}