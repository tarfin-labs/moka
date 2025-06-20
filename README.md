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
MOKA_PAYMENT_SUCCESS_URL=/moka-payment/success
MOKA_PAYMENT_FAILURE_URL=/moka-payment/failed
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
        returnUrl: 'https://your-site.com/moka-callback', // Defaults to route('callback.handle3D')
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

// Without redirect away if you want to handle the payment in your own view
public function checkoutMinimal()
{
    $result = Moka::threeDPayment()->create(
        amount: 100.00,
        cardHolderName: 'John Doe',
        cardNumber: '5555555555555555',
        expMonth: '12',
        expYear: '2025',
        cvc: '123',
        redirectAway: false // Set false to not redirect away
    );

    return $result; // Returns Array with parameters Url and CodeForHash 
}
```

### Handling the 3D Callback

The package automatically sets up a callback route at `POST /moka-callback` (named `moka-callback.handle3D`) to handle the 3D payment result. The callback will:

1. Validate the payment
2. Update the payment status
3. Redirect to your success/failure URL with the payment result

You can configure the success and failure URLs in your `.env` file:

```env
MOKA_PAYMENT_SUCCESS_URL=/moka-payment/success
MOKA_PAYMENT_FAILURE_URL=/moka-payment/failure
```

The callback will redirect to these URLs with the following session data:

```php
[
    'other_trx_code' => 'other_transaction_id',
    'status' => 'success|failed',
    'message' => 'Payment result message'
]
```

### Events Fired After Payment

The package includes an event system to help you manage payment outcomes. When a 3D Secure payment is processed, one of the following events will be dispatched:

```php
// For successful payments
Tarfin\Moka\Events\MokaPaymentSucceededEvent::dispatch($payment);

// For failed payments
Tarfin\Moka\Events\MokaPaymentFailedEvent::dispatch($payment);
```

#### Listening for Payment Events

To react to these events, you can create listeners in your application. There are multiple ways to register event listeners in Laravel.

Then create your listener classes:

```php
// app/Listeners/HandleSuccessfulMokaPayment.php

namespace App\Listeners;

use Tarfin\Moka\Events\MokaPaymentSucceededEvent;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Queue\InteractsWithQueue;

class HandleSuccessfulMokaPayment implements ShouldQueue
{
    use InteractsWithQueue;

    /**
     * Handle the event.
     */
    public function handle(MokaPaymentSucceededEvent $event): void
    {
        $payment = $event->mokaPayment;

        // Access payment details
        $transactionId = $payment->other_trx_code;
        $amount = $payment->amount;

        // Implement your business logic
        // - Complete the order
        // - Generate invoice
        // - Send confirmation email
        // - Update inventory
    }
}
```

```php
// app/Listeners/HandleFailedMokaPayment.php

namespace App\Listeners;

use Tarfin\Moka\Events\MokaPaymentFailedEvent;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Queue\InteractsWithQueue;

class HandleFailedMokaPayment implements ShouldQueue
{
    use InteractsWithQueue;

    /**
     * Handle the event.
     */
    public function handle(MokaPaymentFailedEvent $event): void
    {
        $payment = $event->mokaPayment;

        // Access payment details
        $transactionId = $payment->other_trx_code;
        $failureCode = $payment->result_code;
        $failureMessage = $payment->result_message;

        // Implement your business logic
        // - Update order status
        // - Notify customer
        // - Log payment failure
    }
}
```

### Dynamic Redirect URLs

You can specify custom success and failure URLs for each payment by adding them as query parameters to the return URL:

```php
$result = Moka::threeDPayment()->create(
    amount: 100.00,
    cardHolderName: 'John Doe',
    cardNumber: '5555555555555555',
    expMonth: '12',
    expYear: '2025',
    cvc: '123',
    returnUrl: route('moka-callback.handle3D', [
        'success_url' => 'https://your-site.com/orders/123/payment-success',
        'failure_url' => 'https://your-site.com/orders/123/payment-failed',
    ]),
    installment: 1,
);
```

With this approach, you can dynamically specify different URLs for each payment transaction. The callback handler will:

1. Check for `success_url` or `failure_url` parameters in the request
2. Redirect to these URLs if present
3. Fall back to the configured `MOKA_PAYMENT_SUCCESS_URL` or `MOKA_PAYMENT_FAILURE_URL` if not specified

This is useful for applications that require different redirect destinations based on the payment context, such as returning users to specific order pages or application sections.

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

### Payment Table

You can get payment table information including installment options and commission rates using the `MokaPaymentTable` service:

```php
use Tarfin\Moka\Facades\Moka;

// With minimal parameters
$result = Moka::paymentTable()->calculate(
    amount: 100.00
);

// With BIN number
$result = Moka::paymentTable()->calculate(
    amount: 100.00,
    binNumber: '526911'
);

// With all parameters
$result = Moka::paymentTable()->calculate(
    amount: 100.00,
    binNumber: '526911',
    isThreeD: 0,
    isIncludedCommissionAmount: 0,
    currency: 'TL',
);
```

The service will return an array containing available installment options and commission rates:

```php
'BankPaymentInstallmentInfoList' => [
    [
        'BankInfoName' => 'GENEL',
        'PaymentInstallmentInfoList' => [
            [
                'CommissionType' => 'CreditCard',
                'InstallmentNumber' => 1,
                'DealerCommissionRate' => 2.2,
                'DealerCommissionFixedAmount' => 0,
                'DealerCommissionAmount' => 2.2,
                'PerInstallmentAmount' => 100,
                'Amount' => 100,
            ],
            // ... more installment options
        ],
    ],
    [
        'BankInfoName' => 'AXESS',
        'PaymentInstallmentInfoList' => [
            [
                'CommissionType' => 'CreditCard',
                'InstallmentNumber' => 1,
                'DealerCommissionRate' => 3,
                'DealerCommissionFixedAmount' => 0,
                'DealerCommissionAmount' => 3,
                'PerInstallmentAmount' => 100,
                'Amount' => 100,
            ],
            // ... more installment options
        ],
    ],
],
```

### Payment Detail List

You can retrieve detailed information about a payment and its transactions using the `PaymentDetailList` service:

```php
use Tarfin\Moka\Facades\Moka;

// Get payment details using paymentId
$paymentDetails = Moka::paymentDetailList()->get('1170');

// Or get payment details using your transaction code
$paymentDetails = Moka::paymentDetailList()->get(null, 'YOUR_ORDER_CODE_123');
```

The service will return an array containing both the main payment record and transaction details:

```php
[
    'IsSuccessful' => true,
    'ResultCode' => '00',
    'ResultMessage' => '',
    'PaymentDetail' => [
        'DealerPaymentId' => 27405,
        'OtherTrxCode' => 'YOUR_ORDER_CODE_123',
        'CardHolderFullName' => 'John Doe',
        'CardNumberFirstSix' => '554960',
        'CardNumberLastFour' => '5523',
        'PaymentDate' => '2023-06-15T14:42:17.26',
        'Amount' => 100.00,
        'RefAmount' => 0.00,
        'CurrencyCode' => 'TL',
        'InstallmentNumber' => 0,
        'DealerCommissionAmount' => 2.50,
        'IsThreeD' => true,
        'Description' => 'Payment description',
        'PaymentStatus' => 2,
        'TrxStatus' => 1
    ],
    'ListItemCount' => 1,
    'PaymentTrxDetailList' => [
        [
            'DealerPaymentTrxId' => 2971,
            'DealerPaymentId' => 27405,
            'TrxCode' => '26ba712e-6381-4291-8c59-702c13b30d4d',
            'TrxDate' => '2023-06-15T14:42:17.837',
            'Amount' => 100.00,
            'TrxType' => 2,
            'TrxStatus' => 1,
            'PaymentReason' => 1,
            'VoidRefundReason' => 0,
            'VirtualPosOrderId' => 'ORDER-23060RYOG07011948',
            'ResultMessage' => ''
        ]
    ]
]
```

You must provide either the `paymentId` (Moka's internal payment ID) or `otherTrxCode` (your order/transaction code). If both are null, an exception will be thrown.

If the request fails, a `MokaPaymentDetailListException` will be thrown with the error message and code from Moka.

## Testing

```bash
composer test
```

## License

The MIT License (MIT). Please see [License File](LICENSE.md) for more information.
