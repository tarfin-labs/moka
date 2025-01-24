# Moka Payment Integration for Laravel

This package provides an easy way to integrate Moka Payment system into your Laravel application.

## Installation

You can install the package via composer:

```bash
composer require tarfin/moka
```

## Configuration

Publish the config file:

```bash
php artisan vendor:publish --tag="moka-config"
```

Add your Moka credentials to your `.env` file:

```env
MOKA_DEALER_CODE=your-dealer-code
MOKA_USERNAME=your-username
MOKA_PASSWORD=your-password
MOKA_SANDBOX_MODE=true

# Optional: Configure redirect URLs
MOKA_PAYMENT_SUCCESS_URL=/payment/success
MOKA_PAYMENT_FAILED_URL=/payment/failed
```

## Usage

### Creating a 3D Payment

```php
use Tarfin\Moka\Facades\Moka;

// With minimal parameters
public function checkoutMinimal()
{
    $result = Moka::threeDPayment()->create(
        amount: 100.00,
        cardHolderName: 'John Doe',
        cardNumber: '5555555555555555',
        expMonth: '12',
        expYear: '2025',
        cvc: '123'
    );

    return $result; // Returns RedirectResponse
}

// With all parameters
public function checkout()
{
    $result = Moka::threeDPayment()->create(
        amount: 100.00,
        cardHolderName: 'John Doe',
        cardNumber: '5555555555555555',
        expMonth: '12',
        expYear: '2025',
        cvc: '123',
        software: 'Tarfin',
        // Optional parameters
        returnUrl: 'https://your-site.com/callback', // Defaults to route('callback.handle3D')
        installment: 1,
        otherTrxCode: 'your-unique-id', // If not provided, a UUID will be generated
        isPoolPayment: 0,
        isTokenized: 0,
        currency: 'TL',
        redirectType: 1,
        language: 'TR',
        description: 'Payment description'
    );

    // The user will be redirected to Moka's 3D secure page
    return $result; // Returns RedirectResponse
}

// With buyer information using method chaining
public function checkoutWithBuyerInfo()
{
    $result = Moka::threeDPayment()
        ->buyerInformation(
            fullName: 'John Doe',
            gsmNumber: '5551234567',
            email: 'john@example.com',
            address: '123 Main St, City'
        )
        ->create(
            amount: 100.00,
            cardHolderName: 'John Doe',
            cardNumber: '5555555555555555',
            expMonth: '12',
            expYear: '2025',
            cvc: '123'
        );

    return $result; // Returns RedirectResponse
}
```

### Handling the 3D Callback

The package automatically sets up a callback route at `POST /callback` (named `callback.handle3D`) to handle the 3D payment result. The callback will:

1. Validate the payment
2. Update the payment status
3. Redirect to your success/failure URL with the payment result

You can configure the success and failure URLs in your `.env` file:

```env
MOKA_PAYMENT_SUCCESS_URL=/payment/success
MOKA_PAYMENT_FAILED_URL=/payment/failed
```

The callback will redirect to these URLs with the following session data:
```php
[
    'other_trx_code' => 'other_transaction_id',
    'status' => 'success|failed',
    'message' => 'Payment result message'
]
```

### Calculating Payment Amount

You can calculate the payment amount including commission rates and bank card details using the `MokaPaymentAmount` service:

```php
use TarfinMokaServicesInformationMokaPaymentAmount;

// With minimal parameters
$paymentAmount = app(MokaPaymentAmount::class);
$result = $paymentAmount->calculate(
    binNumber: '526911',
    amount: 100.00
);

// With all parameters
$result = $paymentAmount->calculate(
    binNumber: '526911',
    amount: 100.00,
    installment: 3,
    isThreeD: 0,
    currency: 'USD'
);
```

The service will return an array containing detailed payment information:

```php
[
    'PaymentAmount' => 101.56,
    'DealerDepositAmount' => 95.0,
    'DealerCommissionRate' => 6.46,
    'DealerCommissionAmount' => 6.56,
    'DealerCommissionFixedAmount' => 0.0,
    'DealerGroupCommissionRate' => 1.54,
    'DealerGroupCommissionAmount' => 1.56,
    'DealerGroupCommissionFixedAmount' => 0.0,
    'GroupRevenueRate' => 5.0,
    'GroupRevenueAmount' => 5.0,
    'BankCard' => [
        'BankName' => 'FINANSBANK',
        'BankCode' => '111',
        'BinNumber' => '526911',
        'CardType' => 'MASTER',
        'CreditType' => 'CreditCard',
        'ProductCategory' => 'Bireysel'
    ]
]

```
If there's an error with the calculation, the service will throw a `MokaException` with the error code and message.
```

### Storing Failed Payments

By default, failed payments are not stored in the database. If you want to store them, set this in your `.env`:

```env
MOKA_STORE_FAILED_PAYMENTS=true
```

### BIN Inquiry

You can use the BIN inquiry service to get information about a credit card based on its BIN number (first 6 digits):

```php
use Tarfin\Moka\Facades\Moka;

$binInfo = Moka::binInquiry()->get('526911');

// Response structure
[
    'BankName' => 'FİNANSBANK',
    'BankCode' => '111',
    'BinNumber' => '526911',
    'CardName' => '',
    'CardType' => 'MASTER',
    'CreditType' => 'CreditCard',
    'CardLogo' => 'https://cdn.moka.com/Content/BankLogo/CARDFINANS.png',
    'CardTemplate' => 'https://cdn.moka.com/Content/BankCardTemplate/FINANS-MASTER-CREDIT.png',
    'ProductCategory' => 'Bireysel',
    'GroupName' => 'CARDFINANS'
]
```

The BIN inquiry service provides information about:
- Bank details (name and code)
- Card type (MASTER/VISA)
- Credit type (CreditCard/DebitCard)
- Card logos and templates
- Product category and group name

If the BIN inquiry fails, a `MokaException` will be thrown with the error message and code from Moka.

## Testing

```bash
composer test
```

## License

The MIT License (MIT). Please see [License File](LICENSE.md) for more information.
