# Crypto Rates PHP

PHP Cryptocurrency ticker & calculator website. It supports multiple cryptocurrencies and 80 fiat currencies, with the ability to change the default crypto and currency.

## Features

-   **Multi-Crypto Support:** Supports multiple cryptocurrencies (ETH, BTC, LTC, BNB, XRP, SOL, ADA, and LINK by default).
-   **80 Fiat Currencies:** Exchange rates for 80 fiat currencies.
-   **Live Rates:** API updates every 60 seconds.
-   **JSON Data Cache:** Caches the API response to improve performance.
-   **Configurable:**
    -   Change the default cryptocurrency and fiat currency.
    -   Customize the list of popular currencies.
    -   Change the color scheme of the website.
    -   Configure the title and meta description.
-   **Individual Currency Pages:** View the rates for a specific fiat currency.
-   **Mobile Responsive Design:** The website is designed to work on both desktop and mobile devices.
-   **Built-in API:** The application comes with a built-in JSON API.

## Screenshots

### Desktop
![Example Print1](https://raw.githubusercontent.com/s3spyd3r/Ethereum_Rates/master/Screens/screencapture-Desktop.png)

### Mobile
![Example Print2](https://raw.githubusercontent.com/s3spyd3r/Ethereum_Rates/master/Screens/screencapture-Mobile.png)

## Local Development Setup

### Prerequisites

-   A web server with PHP 8.3 or higher (e.g., [XAMPP](https://www.apachefriends.org/index.html), [WAMP](http://www.wampserver.com/en/), [MAMP](https://www.mamp.info/en/)).
-   [Composer](https://getcomposer.org/) installed.
-   The `curl` and `intl` PHP extensions must be enabled in your `php.ini` file.

### Installation

1.  Clone this repository or download and extract the source code to your web server's document root.
2.  Navigate to the `api/v1` directory in your terminal:
    ```bash
    cd api/v1
    ```
3.  Install the PHP dependencies using Composer:
    ```bash
    composer install
    ```

### Configuration

-   The main configuration file is located at `library/config.php`.
-   **Base URL:** The `BASE_URL` is determined automatically. If you are using a custom domain or a different directory structure, you may need to adjust it.
-   **Cryptocurrencies:** You can customize the list of supported cryptocurrencies by editing the `SUPPORTED_CRYPTOS` array. The default crypto can be changed with the `DEFAULT_CRYPTO` constant.
-   **Fiat Currencies:** You can change the default fiat currency with the `MAIN_CURRENCY` constant and the list of popular currencies with the `POPULAR_CURRENCIES` array.
-   **Appearance:** You can change the color scheme of the website by modifying the `TEMPLATE_COLOR` constant.

### Running the Application

-   Open your web browser and navigate to the root directory of the project (e.g., `http://localhost/crypto`).

## API Information

The application includes a built-in JSON API. By default, the API is accessible under the `/api/v1` path.

-   `/{crypto}/rates`: Get all the current rates for a specific cryptocurrency.
    -   Example: `/api/v1/btc/rates`
-   `/{crypto}/rates/{currency}`: Get the current rate for a specific cryptocurrency and fiat currency.
    -   Example: `/api/v1/btc/rates/usd`
-   `/{crypto}/calculate/{amount}/{currency}`: Calculate the value of a given amount of cryptocurrency in a specific fiat currency.
    -   Example: `/api/v1/btc/calculate/2/usd`