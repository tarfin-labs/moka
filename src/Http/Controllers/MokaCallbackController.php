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

        $redirectUrl = $isSuccess
            ? ($request->query('success_url') ?: config('moka.payment_success_url'))
            : ($request->query('failure_url') ?: config('moka.payment_failure_url'));

        return redirect($redirectUrl)->with([
            'other_trx_code' => $payment->other_trx_code,
            'status' => $isSuccess ? 'success' : 'failed',
            'message' => $payment->result_message,
        ]);
    }
}
