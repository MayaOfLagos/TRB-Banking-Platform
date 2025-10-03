<?php

namespace App\Http\Controllers\Gateway\CoinbaseCommerce;

use App\Constants\Status;
use App\Models\Deposit;
use App\Http\Controllers\Controller;
use App\Http\Controllers\Gateway\PaymentController;
use Illuminate\Http\Request;

class ProcessController extends Controller
{
    public static function process($deposit)
    {
        $coinbaseAcc = json_decode($deposit->gatewayCurrency()->gateway_parameter);

        $url = 'https://api.commerce.coinbase.com/charges';
        $array = [
            'name' => auth()->user()->username,
            'description' => "Pay to " . gs('site_name'),
            'local_price' => [
                'amount' => $deposit->final_amount,
                'currency' => $deposit->method_currency
            ],
            'metadata' => [
                'trx' => $deposit->trx
            ],
            'pricing_type' => "fixed_price",
            'redirect_url' => route('home').$deposit->success_url,
            'cancel_url' => route('home').$deposit->failed_url
        ];

        $jsonData = json_encode($array);
        $ch = curl_init();
        $apiKey = $coinbaseAcc->api_key;
        $header = array();
        $header[] = 'Content-Type: application/json';
        $header[] = 'X-CC-Api-Key: ' . "$apiKey";
        $header[] = 'X-CC-Version: 2018-03-22';
        curl_setopt($ch, CURLOPT_URL, $url);
        curl_setopt($ch, CURLOPT_POST, 1);
        curl_setopt($ch, CURLOPT_HTTPHEADER, $header);
        curl_setopt($ch, CURLOPT_POSTFIELDS, $jsonData);
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
        $result = curl_exec($ch);
        $httpCode = curl_getinfo($ch, CURLINFO_HTTP_CODE);
        curl_close($ch);

        // Check for cURL errors
        if ($result === false) {
            $send['error'] = true;
            $send['message'] = 'Network error occurred while connecting to Coinbase Commerce.';
            $send['view'] = '';
            return json_encode($send);
        }

        $result = json_decode($result);
        
        // Check for JSON decode errors
        if ($result === null) {
            $send['error'] = true;
            $send['message'] = 'Invalid response received from Coinbase Commerce.';
            $send['view'] = '';
            return json_encode($send);
        }

        // Check for successful response with proper data structure
        if ($httpCode == 201 && isset($result->data) && isset($result->data->hosted_url)) {
            $send['redirect'] = true;
            $send['redirect_url'] = $result->data->hosted_url;
        } else {
            $send['error'] = true;
            $send['message'] = isset($result->error->message) 
                ? $result->error->message 
                : 'Some problem occurred with Coinbase Commerce API.';
        }

        $send['view'] = '';
        return json_encode($send);
    }

    public function ipn(Request $request)
    {
        $postdata = file_get_contents("php://input");
        $res = json_decode($postdata);
        
        // Validate JSON decode
        if ($res === null) {
            return response('Invalid JSON payload', 400);
        }
        
        // Validate required properties exist
        if (!isset($res->event) || 
            !isset($res->event->data) || 
            !isset($res->event->data->metadata) || 
            !isset($res->event->data->metadata->trx)) {
            return response('Invalid webhook payload structure', 400);
        }
        
        $deposit = Deposit::where('trx', $res->event->data->metadata->trx)->orderBy('id', 'DESC')->first();
        
        if (!$deposit) {
            return response('Deposit not found', 404);
        }
        
        $coinbaseAcc = json_decode($deposit->gatewayCurrency()->gateway_parameter);
        
        if (!$coinbaseAcc || !isset($coinbaseAcc->secret)) {
            return response('Gateway configuration error', 500);
        }
        
        $headers = apache_request_headers();
        $headers = json_decode(json_encode($headers), true);
        
        if (!isset($headers['X-Cc-Webhook-Signature'])) {
            return response('Missing webhook signature', 400);
        }
        
        $sentSign = $headers['X-Cc-Webhook-Signature'];
        $sig = hash_hmac('sha256', $postdata, $coinbaseAcc->secret);
        
        if ($sentSign == $sig) {
            if (isset($res->event->type) && 
                $res->event->type == 'charge:confirmed' && 
                $deposit->status == Status::PAYMENT_INITIATE) {
                PaymentController::userDataUpdate($deposit);
            }
        } else {
            return response('Invalid signature', 403);
        }
        
        return response('OK', 200);
    }
}
