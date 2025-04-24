<?php
ini_set('display_errors', 1);
error_reporting(E_ALL);

function getCryptoPrices() {
    $ids = 'bitcoin,ethereum,ripple,litecoin,cardano,polkadot,solana,dogecoin,chainlink,stellar';
    $endpoint = "https://api.coingecko.com/api/v3/coins/markets?vs_currency=usd&ids={$ids}";

    $ch = curl_init($endpoint);
    curl_setopt_array($ch, [
        CURLOPT_RETURNTRANSFER => true,
        CURLOPT_TIMEOUT        => 10,
        CURLOPT_USERAGENT      => 'Mozilla/5.0',
    ]);

    $response = curl_exec($ch);
    if ($response === false) {
        http_response_code(500);
        return ['error' => 'cURL error: ' . curl_error($ch)];
    }

    $httpCode = curl_getinfo($ch, CURLINFO_HTTP_CODE);
    curl_close($ch);
    if ($httpCode !== 200) {
        http_response_code($httpCode);
        return ['error' => "HTTP error: {$httpCode}"];
    }

    $data = json_decode($response, true);
    if (json_last_error() !== JSON_ERROR_NONE) {
        http_response_code(500);
        return ['error' => 'JSON parse error: ' . json_last_error_msg()];
    }

    $result = [];
    foreach ($data as $coin) {
        $result[] = [
            'symbol' => strtoupper($coin['symbol']),
            'name'   => $coin['name'],
            'price'  => number_format($coin['current_price'], 2),
            'change' => number_format($coin['price_change_percentage_24h'], 2) . '%',
        ];
    }
    return $result;
}

header('Content-Type: application/json');
echo json_encode(getCryptoPrices());
