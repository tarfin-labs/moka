<?php

declare(strict_types=1);

namespace Tarfin\Moka\Services\Payment;

use Illuminate\Support\Str;
use Tarfin\Moka\MokaRequest;
use Tarfin\Moka\Facades\Moka;
use Tarfin\Moka\Models\MokaPayment;
use Illuminate\Http\RedirectResponse;
use Illuminate\Support\Facades\Redirect;
use Tarfin\Moka\Enums\MokaPaymentStatus;
use Tarfin\Moka\Exceptions\MokaPaymentThreeDException;

class MokaPaymentThreeD extends MokaRequest
{
    private const ENDPOINT_CREATE = '/PaymentDealer/DoDirectPaymentThreeD';

    private ?array $buyerInformation = null;

    /**
     * @throws \Tarfin\Moka\Exceptions\MokaPaymentThreeDException
     * @throws \Tarfin\Moka\Exceptions\MokaBinInquiryException
     * @throws \Tarfin\Moka\Exceptions\MokaPaymentAmountException
     */
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
        string $cardToken = '',
        int $isIncludedCommissionAmount = 1,
        bool $redirectAway = true
    ): RedirectResponse|array {
        $paymentAmount = Moka::paymentAmount()->calculate(
            binNumber: substr($cardNumber, 0, 6),
            amount: $amount,
            installment: $installment,
            currency: $currency ?? config('moka.currency')
        );

        $chargedAmount = $isIncludedCommissionAmount === 1
            ? (float) $paymentAmount['PaymentAmount']
            : (float) $paymentAmount['DealerDepositAmount'];
        $commissionAmount = $isIncludedCommissionAmount === 1
            ? (float) $paymentAmount['PaymentAmount'] - (float) $paymentAmount['DealerDepositAmount']
            : (float) $paymentAmount['DealerDepositAmount'] - (float) $paymentAmount['PaymentAmount'];

        $paymentData = [
            'PaymentDealerRequest' => [
                'CardHolderFullName' => $cardHolderName,
                'CardNumber'         => $cardNumber,
                'ExpMonth'           => $expMonth,
                'ExpYear'            => $expYear,
                'CvcNumber'          => $cvc,
                'Amount'             => $chargedAmount,
                'Currency'           => $currency ?? config('moka.currency'),
                'InstallmentNumber'  => $installment,
                'ClientIP'           => request()->ip(),
                'RedirectUrl'        => $returnUrl ?? route('moka-callback.handle3D'),
                'RedirectType'       => $redirectType ?? config('moka.redirect_type'),
                'Software'           => $software ?? config('moka.software'),
                'OtherTrxCode'       => $otherTrxCode ?? Str::ulid()->toString(),
                'IsPoolPayment'      => $isPoolPayment ?? config('moka.is_pool_payment'),
                'IsTokenized'        => $isTokenized ?? config('moka.is_tokenized'),
                'Language'           => $language ?? config('moka.language'),
                'IsPreAuth'          => $isPreAuth ?? config('moka.is_pre_auth'),
                'ReturnHash'         => 1,
                'Description'        => $description,
                'CardToken'          => $cardToken,
            ],
        ];

        if ($this->buyerInformation !== null) {
            $paymentData['PaymentDealerRequest']['BuyerInformation'] = $this->buyerInformation;
        }

        $cardInfo = $this->getCardInfo($cardNumber);

        $response = $this->sendRequest(self::ENDPOINT_CREATE, $paymentData);

        $paymentData = [
            'other_trx_code'    => $paymentData['PaymentDealerRequest']['OtherTrxCode'],
            'card_type'         => $cardInfo['card_type'],
            'card_last_four'    => $cardInfo['card_last_four'],
            'bank_name'         => $cardInfo['bank_name'],
            'bank_group_name'   => $cardInfo['bank_group_name'],
            'card_holder'       => $cardHolderName,
            'amount'            => $amount,
            'amount_charged'    => $chargedAmount,
            'amount_commission' => $commissionAmount,
            'result_code'       => $response['ResultCode'],
            'result_message'    => trans()->has('moka::payment-three-d.'.$response['ResultCode']) ? __('moka::payment-three-d.'.$response['ResultCode']) : $response['ResultMessage'],
            'installment'       => $installment,
            'three_d'           => 1,
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
            'status'        => MokaPaymentStatus::PENDING,
        ]));

        if (!$redirectAway) {
            return $response['Data'];
        }

        return Redirect::away($response['Data']['Url']);
    }

    public function buyerInformation(
        ?string $fullName = null,
        ?string $gsmNumber = null,
        ?string $email = null,
        ?string $address = null
    ): self {
        $this->buyerInformation = [
            'BuyerFullName'  => $fullName,
            'BuyerGsmNumber' => $gsmNumber,
            'BuyerEmail'     => $email,
            'BuyerAddress'   => $address,
        ];

        return $this;
    }

    /**
     * @throws \Tarfin\Moka\Exceptions\MokaBinInquiryException
     */
    public function getCardInfo(string $cardNumber): array
    {
        $binNumber = substr($cardNumber, 0, 6);
        $response  = Moka::binInquiry()->get($binNumber);

        return [
            'card_type'       => $response['CardType'],
            'card_last_four'  => substr($cardNumber, -4),
            'bank_name'       => $response['BankName'] ?? null,
            'bank_group_name' => $response['GroupName'] ?? null,
        ];
    }
}
