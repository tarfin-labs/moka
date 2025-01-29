<?php

namespace Tarfin\Moka\Services\Payment;

use Illuminate\Http\RedirectResponse;
use Illuminate\Support\Facades\Redirect;
use Illuminate\Support\Str;
use Tarfin\Moka\Enums\MokaPaymentStatus;
use Tarfin\Moka\Exceptions\MokaPaymentThreeDException;
use Tarfin\Moka\Facades\Moka;
use Tarfin\Moka\Models\MokaPayment;
use Tarfin\Moka\MokaRequest;

class MokaPaymentThreeD extends MokaRequest
{
    private const ENDPOINT_CREATE = '/PaymentDealer/DoDirectPaymentThreeD';

    private ?array $buyerInformation = null;

    public function create(
        float $amount,
        string $cardHolderName,
        string $cardNumber,
        string $expMonth,
        string $expYear,
        string $cvc,
        ?string $software = null,
        ?string $returnUrl = null,
        int $installment = 1,
        ?string $otherTrxCode = null,
        ?int $isPoolPayment = null,
        ?int $isTokenized = null,
        ?string $currency = null,
        ?int $redirectType = null,
        ?string $language = null,
        ?int $isPreAuth = null,
        string $description = '',
        string $cardToken = ''
    ): RedirectResponse {
        $paymentData = [
            'PaymentDealerRequest' => [
                'CardHolderFullName' => $cardHolderName,
                'CardNumber' => $cardNumber,
                'ExpMonth' => $expMonth,
                'ExpYear' => $expYear,
                'CvcNumber' => $cvc,
                'Amount' => $amount,
                'Currency' => $currency ?? config('moka.currency'),
                'InstallmentNumber' => $installment,
                'ClientIP' => request()->ip(),
                'RedirectUrl' => $returnUrl ?? route('callback.handle3D'),
                'RedirectType' => $redirectType ?? config('moka.redirect_type'),
                'Software' => $software ?? config('moka.software'),
                'OtherTrxCode' => $otherTrxCode ?? Str::uuid()->toString(),
                'IsPoolPayment' => $isPoolPayment ?? config('moka.is_pool_payment'),
                'IsTokenized' => $isTokenized ?? config('moka.is_tokenized'),
                'Language' => $language ?? config('moka.language'),
                'IsPreAuth' => $isPreAuth ?? config('moka.is_pre_auth'),
                'ReturnHash' => 1,
                'Description' => $description,
                'CardToken' => $cardToken,
            ],
        ];

        if ($this->buyerInformation !== null) {
            $paymentData['PaymentDealerRequest']['BuyerInformation'] = $this->buyerInformation;
        }

        $response = $this->sendRequest(self::ENDPOINT_CREATE, $paymentData);

        $cardInfo = $this->getCardInfo($cardNumber);

        $paymentData = [
            'other_trx_code' => $paymentData['PaymentDealerRequest']['OtherTrxCode'],
            'card_type' => $cardInfo['card_type'],
            'card_last_four' => $cardInfo['card_last_four'],
            'card_holder' => $cardHolderName,
            'amount' => $amount,
            'result_code' => $response['ResultCode'],
            'result_message' => $response['ResultMessage'],
            'installment' => $installment,
            'three_d' => 1,
        ];

        if ($response['ResultCode'] !== 'Success') {
            if (config('moka.store_failed_payments', false)) {
                MokaPayment::create(array_merge($paymentData, [
                    'status' => MokaPaymentStatus::FAILED,
                ]));
            }

            throw new MokaPaymentThreeDException(
                $response['ResultMessage'],
                $response['ResultCode']
            );
        }

        MokaPayment::create(array_merge($paymentData, [
            'code_for_hash' => $response['Data']['CodeForHash'],
            'status' => MokaPaymentStatus::PENDING,
        ]));

        return Redirect::away($response['Data']['Url']);
    }

    public function buyerInformation(
        ?string $fullName = null,
        ?string $gsmNumber = null,
        ?string $email = null,
        ?string $address = null
    ): self {
        $this->buyerInformation = [
            'BuyerFullName' => $fullName,
            'BuyerGsmNumber' => $gsmNumber,
            'BuyerEmail' => $email,
            'BuyerAddress' => $address,
        ];

        return $this;
    }

    public function getCardInfo(string $cardNumber): array
    {
        $binNumber = substr($cardNumber, 0, 6);
        $response = Moka::binInquiry()->get($binNumber);

        return [
            'card_type' => $response['CardType'],
            'card_last_four' => substr($cardNumber, -4),
        ];
    }
}
