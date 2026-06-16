<?php 

define('BASE_URL', (isset($_SERVER['HTTPS']) && $_SERVER['HTTPS'] === 'on' ? "https" : "http") . "://" . $_SERVER['HTTP_HOST'] . str_replace($_SERVER['DOCUMENT_ROOT'], '', str_replace('\\', '/', dirname(__DIR__))));
define('API_URL', BASE_URL . '/api/v1');
const MY_LOCALE = 'en-US';
const MAIN_CURRENCY = 'USD';
const POPULAR_CURRENCIES = ['EUR', 'USD', 'GBP', 'CHF'];

const BRAND_NAME = 'Crypto Rates';

const TEMPLATE_COLOR = '#4C7595';
const META_TITLE = 'Crypto Rates';
const META_INDIVIDUAL_TITLE = 'Crypto Rates For - ';

const META_DESCRIPTION = 'A PHP app to display live crypto rates for multiple cryptocurrencies.';

const SUPPORTED_CRYPTOS = ['ETH', 'BTC', 'LTC', 'BNB', 'XRP', 'SOL', 'ADA', 'LINK'];
const DEFAULT_CRYPTO = 'ETH';

$debug = true;

if ($debug) {
    ini_set('display_errors', 1);
    ini_set('display_startup_errors', 1);
    error_reporting(E_ALL);
} else {
    ini_set('display_errors', 0);
    ini_set('display_startup_errors', 0);
    error_reporting(0);
}