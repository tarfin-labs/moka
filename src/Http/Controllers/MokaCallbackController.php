<?php

namespace Tarfin\Moka\Http\Controllers;

use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Routing\Controller;
use Tarfin\Moka\Enums\MokaPaymentStatus;
use Tarfin\Moka\Models\MokaPayment;

class MokaCallbackController extends Controller
{
    public function handle3D(Request $request): RedirectResponse
    {
        $payment = MokaPayment::where('other_trx_code', $request->input('OtherTrxCode'))->firstOrFail();

        $payment->handle3DCallback(
            hashValue: $request->input('hashValue'),
            resultCode: $request->input('resultCode'),
            resultMessage: $request->input('resultMessage'),
            trxCode: $request->input('trxCode')
        );

        $isSuccess = $payment->status === MokaPaymentStatus::SUCCESS;

        $baseUrl = $isSuccess
            ? ($request->query(key: 'success_url') ?: config(key: 'moka.payment_success_url'))
            : ($request->query(key: 'failure_url') ?: config(key: 'moka.payment_failed_url'));

        $separator = (str_contains($baseUrl, '?')) ? '&' : '?';
        $redirectUrl = "$baseUrl{$separator}other_trx_code={$payment->other_trx_code}";

        return redirect($redirectUrl);
    }
}
