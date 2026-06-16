<?php

final class RateService {
    private const CACHE_TTL_SECONDS = 60;
    private const API_URL_PATTERN = 'https://api.coinbase.com/v2/exchange-rates?currency=%s';

    public function __construct(
        private readonly string $cacheDir,
        private readonly array $currencies,
    ) {}

    public function getRates(string $crypto, string $currency = ''): array {
        $data = $this->getRatesFromCacheOrApi($crypto);

        if (!empty($currency)) {
            $currency = strtoupper($currency);
            if (isset($data[$currency])) {
                return $this->formatRate($currency, $data[$currency]);
            }
            throw new \InvalidArgumentException('Currency not found');
        }

        $formattedRates = [];
        foreach ($data as $code => $rate) {
            if (isset($this->currencies[$code])) {
                $formattedRates[] = $this->formatRate($code, $rate);
            }
        }

        return $formattedRates;
    }

    public function calculate(string $crypto, float $amount, string $currency): array {
        $rates = $this->getRates($crypto, $currency);
        $calc = $amount * $rates['rate'];

        $formatter = new \NumberFormatter(MY_LOCALE, \NumberFormatter::CURRENCY);
        $formatter->setAttribute(\NumberFormatter::MAX_FRACTION_DIGITS, 8);
        $calcFormatted = $formatter->formatCurrency($calc, $currency);

        return [
            'amount' => $amount,
            'calc' => $calcFormatted,
            'currency' => $currency,
        ];
    }

    private function getRatesFromCacheOrApi(string $crypto): array {
        if (!is_dir($this->cacheDir)) {
            mkdir($this->cacheDir, 0777, true);
        }

        $cacheFile = $this->cacheDir . '/rates_' . $crypto . '.json';
        $cacheTime = $this->cacheDir . '/rates_' . $crypto . '.time';

        if (file_exists($cacheFile) && file_exists($cacheTime) && (time() - filemtime($cacheTime) < self::CACHE_TTL_SECONDS)) {
            $cachedData = json_decode(file_get_contents($cacheFile), true);
            if (json_last_error() === JSON_ERROR_NONE && isset($cachedData['data']['rates'])) {
                return $cachedData['data']['rates'];
            }
        }

        $response = $this->fetchFromApi($crypto);
        file_put_contents($cacheFile, $response);
        touch($cacheTime);

        $data = json_decode($response, true);
        if (json_last_error() !== JSON_ERROR_NONE || !isset($data['data']['rates'])) {
            throw new \RuntimeException('Invalid API response');
        }

        return $data['data']['rates'];
    }

    private function fetchFromApi(string $crypto): string {
        $ch = curl_init();
        if ($ch === false) {
            throw new \RuntimeException('Failed to initialize cURL');
        }

        $apiUrl = sprintf(self::API_URL_PATTERN, $crypto);

        curl_setopt_array($ch, [
            CURLOPT_URL => $apiUrl,
            CURLOPT_RETURNTRANSFER => true,
            CURLOPT_SSL_VERIFYPEER => true,
            CURLOPT_TIMEOUT => 10,
            CURLOPT_CONNECTTIMEOUT => 5,
            CURLOPT_FOLLOWLOCATION => true,
            CURLOPT_MAXREDIRS => 3,
            CURLOPT_USERAGENT => 'Mozilla/5.0',
        ]);

        $response = curl_exec($ch);
        $httpCode = curl_getinfo($ch, CURLINFO_HTTP_CODE);
        $error = curl_error($ch);
        curl_close($ch);

        if ($response === false || $httpCode !== 200) {
            throw new \RuntimeException('Failed to fetch data from API: ' . $error . ' (HTTP Code: ' . $httpCode . ')');
        }

        return $response;
    }

    private function formatRate(string $code, float $rate): array {
        $formatter = new \NumberFormatter(MY_LOCALE, \NumberFormatter::CURRENCY);
        return [
            'code' => $code,
            'name' => $this->currencies[$code],
            'rate' => $rate,
            'rate_formatted' => $formatter->formatCurrency($rate, $code),
        ];
    }
}
