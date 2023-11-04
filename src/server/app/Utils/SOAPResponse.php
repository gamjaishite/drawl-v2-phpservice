<?php


require_once __DIR__ . '/../Common/CustomResponse.php';

class SOAPResponse
{
    public static function parseFault(string $xml): CustomResponse
    {
        $p = xml_parser_create();
        xml_parse_into_struct($p, $xml, $vals, $index);
        xml_parser_free($p);

        $response = new CustomResponse();
        $response->status = (int)$vals[$index["FAULTCODE"][0]]["value"];
        $response->message = $vals[$index["FAULTSTRING"][0]]["value"];

        http_response_code($response->status);
        return $response;
    }

    public static function parseSuccess(string $xml): CustomResponse
    {
        $p = xml_parser_create();
        xml_parse_into_struct($p, $xml, $vals, $index);
        xml_parser_free($p);

        $response = new CustomResponse();
        $response->status = (int)$vals[$index["STATUS"][0]]["value"];
        $response->message = $vals[$index["MESSAGE"][0]]["value"];
        $response->data = [];

        for ($i = 0; $i < count($index["DATA"]); $i += 2) {
            $temp = [];
            for ($j = $index["DATA"][$i] + 1; $j < $index["DATA"][$i + 1]; $j++) {
                $temp[strtolower($vals[$j]["tag"])] = $vals[$j]["value"];
            }
            $response->data[] = $temp;
        }

        return $response;
    }
}