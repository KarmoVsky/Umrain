<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Http\Response;
use Illuminate\Contracts\View\View;
use MyFatoorah\Library\MyFatoorah;
use MyFatoorah\Library\API\Payment\MyFatoorahPayment;
use MyFatoorah\Library\API\Payment\MyFatoorahPaymentEmbedded;
use MyFatoorah\Library\API\Payment\MyFatoorahPaymentStatus;
use Modules\Booking\Gateways\MyFatoorahGateway;
use Illuminate\Support\Facades\Session;
use Modules\Booking\Models\Booking;
use Modules\Booking\Models\Payment;
use Exception;
use Illuminate\Support\Facades\Http;

class MyFatoorahController extends Controller {

    /**
     * @var array
     */
    public $mfConfig = [];
//-----------------------------------------------------------------------------------------------------------------------------------------

    /**
     * Initiate MyFatoorah Configuration
     */
    public function __construct() {
        $this->mfConfig = [
            'apiKey'      => config('myfatoorah.api_key'),
            'isTest'      => config('myfatoorah.test_mode'),
            'countryCode' => config('myfatoorah.country_iso'),
        ];
    }

//-----------------------------------------------------------------------------------------------------------------------------------------

    /**
     * Redirect to MyFatoorah Invoice URL
     * Provide the index method with the order id and (payment method id or session id)
     *
     * @return Response
     */
    public function index() {
        // dd(request('data'));
        try {
            //For example: pmid=0 for MyFatoorah invoice or pmid=1 for Knet in test mode
            $paymentId = request('pmid') ?: 0;
            $sessionId = request('sid') ?: null;
            $orderId  = request('oid') ?: 147;

            // $curlData = $this->getPayLoadData($orderId);
            $curlData = request('data');
            $mfObj   = new MyFatoorahPayment($this->mfConfig);
            $payment = $mfObj->getInvoiceURL($curlData, $paymentId, $orderId, $sessionId);

            return redirect($payment['invoiceURL']);
        } catch (Exception $ex) {

            $exMessage = __('myfatoorah.' . $ex->getMessage());
            return response()->json(['IsSuccess' => 'false', 'Message' => $exMessage]);
        }
    }

//-----------------------------------------------------------------------------------------------------------------------------------------

    /**
     * Example on how to map order data to MyFatoorah
     * You can get the data using the order object in your system
     *
     * @param int|string $orderId
     *
     * @return array
     */
    private function getPayLoadData($orderId = null) {

        $callbackURL = route('myfatoorah.callback');
        //You can get the data using the order object in your system
        $order = $this->getTestOrderData($orderId);
    }

    public function testData($data){
        $this->data = $data;
    }
//-----------------------------------------------------------------------------------------------------------------------------------------

    /**
     * Get MyFatoorah Payment Information
     * Provide the callback method with the paymentId
     *
     * @return Response
     */
    public function callback()
    {
        try {
            $paymentId = request('paymentId');


            if (!$paymentId) {
                throw new Exception('Payment ID is missing.');
            }
            $apiKey = setting_item('g_myfatoorah_api_key');
            $testMode = setting_item('g_myfatoorah_test_mode');
            $endpoint = $testMode ? 'https://apitest.myfatoorah.com/v2/GetPaymentStatus' : 'https://api-sa.myfatoorah.com/v2/GetPaymentStatus';

            $data = [
                'Key' => $paymentId,
                'KeyType' => 'PaymentId'
            ];

            $response = Http::withHeaders([
                'Authorization' => 'Bearer ' . $apiKey,
                'Content-Type' => 'application/json'
            ])->post($endpoint, $data);
            // dd($response['Data']);

            // 4. إعداد الاستجابة
            $response = [
                'IsSuccess' => $response['IsSuccess'],
                'Message' => $response['Data']['InvoiceStatus'],
                'Data' => $response['Data']
            ];

            // 5. البحث عن الحجز بناءً على CustomerReference
            $booking = Booking::where('code', $response['Data']['CustomerReference'])->first();

            // 6. التحقق من وجود الحجز
            if ($booking && $response['IsSuccess']) {
                // 7. تحديث gateway وحالة الفاتورة
                $booking->gateway = 'myfatoorah';
                // $booking->payment_id = $response['Data']['InvoiceTransactions'][0]['PaymentId'];  // معرف الدفع من MyFatoorah
                $booking->paid = $response['Data']['InvoiceValue'];  // قيمة الدفع
                $booking->is_paid = 1;  // تأكيد أن الفاتورة مدفوعة

                // dd($booking);
                if ($response['Data']['InvoiceStatus'] == 'Paid') {
                    $booking->status = 'paid'; // إذا كانت الفاتورة مدفوعة
                    $booking->payment_gateway = $response['Data']['InvoiceTransactions'][0]['PaymentGateway'];

                    $invoiceTransactions = $response['Data']['InvoiceTransactions'] ?? null;
                    $lastTransaction = end($invoiceTransactions);
                    $booking->invoice_status = $lastTransaction['TransactionStatus'];
                    $payment = Payment::where('booking_id', $booking->id)->first();
                    if($payment) {
                        $payment->status = 'completed';
                        $payment->save();
                    }
                } else {
                    $booking->status = 'unpaid';
                }

                // 8. حفظ التغييرات في قاعدة البيانات
                $booking->save();
                // dd('stop');
            } else {
                // إذا لم يتم العثور على الحجز
                $response['IsSuccess'] = false;
                $response['Message'] = 'Booking not found';
            }

            // dd($response);
            // dd('try: ' . json_encode($response['Data']));

            // 9. إعادة توجيه الزبون إلى صفحة التقرير
            return redirect()->route('payment.report', [
                'status' => $response['Data']['InvoiceStatus'],
                'customer_name' => $response['Data']['CustomerName'],
                'invoice_value' => $response['Data']['InvoiceValue'],
                'payment_gateway' => $response['Data']['InvoiceTransactions'][0]['PaymentGateway'] ?? 'N/A',
                'transaction_status' => $response['Data']['InvoiceTransactions'][0]['TransactionStatus'] ?? 'N/A',
                'message' => $response['Message']
            ]);

        } catch (Exception $ex) {
            // 10. التعامل مع الاستثناءات
            $exMessage = __('myfatoorah.' . $ex->getMessage());
            return redirect()->route('payment.report', [
                'status' => 'Failed',
                'customer_name' => 'Unknown',
                'invoice_value' => '0.00',
                'payment_gateway' => 'N/A',
                'transaction_status' => 'N/A',
                'message' => $exMessage
            ]);
        }
    }

    public function callback3()
    {
        try {
            $paymentId = request('paymentId');

            $mfObj = new MyFatoorahPaymentStatus($this->mfConfig);
            dd($mfConfig);
            $data  = $mfObj->getPaymentStatus($paymentId, 'PaymentId');

            $message = $this->getTestMessage($data->InvoiceStatus, $data->InvoiceError);

            $response = [
                'IsSuccess' => $data->InvoiceStatus == 'Paid',
                'Message' => $message,
                'Data' => $data
            ];

            // search for booking depends on CustomerReference
            $booking = Booking::where('code', $response['Data']->CustomerReference)->first();

            if ($booking) {
                $booking->payment_id = $response['Data']->InvoiceTransactions[0]->PaymentId;  // معرف الدفع من MyFatoorah
                $booking->gateway = 'myfatoorah';
                $booking->paid = $response['Data']->InvoiceValue;
                $booking->is_paid = 1;

                if ($response['Data']->InvoiceStatus == 'Paid') {
                    $booking->status = 'paid';
                } else {
                    $booking->status = $response['Data']->InvoiceStatus;
                }

                $payment = Payment::where('booking_id', $booking->id)->first();

                if ($payment) {
                    $payment->status = 'completed';
                    $payment->save();
                }

                $booking->save();

                // $this->storeMyFatoorahInvoice($payment->id, $data);

            } else {
                $response['IsSuccess'] = false;
                $response['Message'] = 'Booking not found';
            }

            return redirect()->route('payment.report', [
                'status' => $response['Data']->InvoiceStatus,
                'customer_name' => $response['Data']->CustomerName,
                'invoice_value' => $response['Data']->InvoiceValue,
                'payment_gateway' => $response['Data']->InvoiceTransactions[0]->PaymentGateway ?? 'N/A',
                'transaction_status' => $response['Data']->InvoiceTransactions[0]->TransactionStatus ?? 'N/A',
                'message' => $response['Message']
            ]);

        } catch (Exception $ex) {
            dd('catch');
            $exMessage = __('myfatoorah.' . $ex->getMessage());
            return redirect()->route('payment.report', [
                'status' => 'Failed',
                'customer_name' => 'Unknown',
                'invoice_value' => '0.00',
                'payment_gateway' => 'N/A',
                'transaction_status' => 'N/A',
                'message' => $exMessage
            ]);
        }
    }


    public function report(Request $request)
{
    // جلب البيانات الممررة من دالة callback
    $status = $request->input('status');
    $customerName = $request->input('customer_name');
    $invoiceValue = $request->input('invoice_value');
    $paymentGateway = $request->input('payment_gateway');
    $transactionStatus = $request->input('transaction_status');
    $message = $request->input('message');

    // عرض التقرير في الـ View
    return view('payment.report', compact('status', 'customerName', 'invoiceValue', 'paymentGateway', 'transactionStatus', 'message'));
}



//-----------------------------------------------------------------------------------------------------------------------------------------

    /**
     * Example on how to Display the enabled gateways at your MyFatoorah account to be displayed on the checkout page
     * Provide the checkout method with the order id to display its total amount and currency
     *
     * @return View
     */
    public function checkout() {
        try {
            //You can get the data using the order object in your system
            $orderId = request('oid') ?: 147;
            $order   = $this->getTestOrderData($orderId);

            //You can replace this variable with customer Id in your system
            $customerId = request('customerId');

            //You can use the user defined field if you want to save card
            $userDefinedField = config('myfatoorah.save_card') && $customerId ? "CK-$customerId" : '';

            //Get the enabled gateways at your MyFatoorah acount to be displayed on checkout page
            $mfObj          = new MyFatoorahPaymentEmbedded($this->mfConfig);
            $paymentMethods = $mfObj->getCheckoutGateways($order['total'], $order['currency'], config('myfatoorah.register_apple_pay'));

            if (empty($paymentMethods['all'])) {
                throw new Exception('noPaymentGateways');
            }

            //Generate MyFatoorah session for embedded payment
            $mfSession = $mfObj->getEmbeddedSession($userDefinedField);

            //Get Environment url
            $isTest = $this->mfConfig['isTest'];
            $vcCode = $this->mfConfig['countryCode'];

            $countries = MyFatoorah::getMFCountries();
            $jsDomain  = ($isTest) ? $countries[$vcCode]['testPortal'] : $countries[$vcCode]['portal'];

            return view('myfatoorah.checkout', compact('mfSession', 'paymentMethods', 'jsDomain', 'userDefinedField'));
        } catch (Exception $ex) {
            $exMessage = __('myfatoorah.' . $ex->getMessage());
            return view('myfatoorah.error', compact('exMessage'));
        }
    }

//-----------------------------------------------------------------------------------------------------------------------------------------

    /**
     * Example on how the webhook is working when MyFatoorah try to notify your system about any transaction status update
     */
    public function webhook(Request $request) {
        try {
            //Validate webhook_secret_key
            $secretKey = config('myfatoorah.webhook_secret_key');
            if (empty($secretKey)) {
                return response(null, 404);
            }

            //Validate MyFatoorah-Signature
            $mfSignature = $request->header('MyFatoorah-Signature');
            if (empty($mfSignature)) {
                return response(null, 404);
            }

            //Validate input
            $body  = $request->getContent();
            $input = json_decode($body, true);
            if (empty($input['Data']) || empty($input['EventType']) || $input['EventType'] != 1) {
                return response(null, 404);
            }

            //Validate Signature
            if (!MyFatoorah::isSignatureValid($input['Data'], $secretKey, $mfSignature, $input['EventType'])) {
                return response(null, 404);
            }

            //Update Transaction status on your system
            $result = $this->changeTransactionStatus($input['Data']);

            return response()->json($result);
        } catch (Exception $ex) {
            $exMessage = __('myfatoorah.' . $ex->getMessage());
            return response()->json(['IsSuccess' => false, 'Message' => $exMessage]);
        }
    }

//-----------------------------------------------------------------------------------------------------------------------------------------
    private function changeTransactionStatus($inputData) {
        //1. Check if orderId is valid on your system.
        $orderId = $inputData['CustomerReference'];

        //2. Get MyFatoorah invoice id
        $invoiceId = $inputData['InvoiceId'];

        //3. Check order status at MyFatoorah side
        if ($inputData['TransactionStatus'] == 'SUCCESS') {
            $status = 'Paid';
            $error  = '';
        } else {
            $mfObj = new MyFatoorahPaymentStatus($this->mfConfig);
            $data  = $mfObj->getPaymentStatus($invoiceId, 'InvoiceId');

            $status = $data->InvoiceStatus;
            $error  = $data->InvoiceError;
        }

        $message = $this->getTestMessage($status, $error);

        //4. Update order transaction status on your system
        return ['IsSuccess' => true, 'Message' => $message, 'Data' => $inputData];
    }

//-----------------------------------------------------------------------------------------------------------------------------------------
    private function getTestOrderData($orderId) {
        return [
            'total'    => 15,
            'currency' => 'KWD'
        ];
    }

//-----------------------------------------------------------------------------------------------------------------------------------------
    private function getTestMessage($status, $error) {
        if ($status == 'Paid') {
            return 'Invoice is paid.';
        } else if ($status == 'Failed') {
            return 'Invoice is not paid due to ' . $error;
        } else if ($status == 'Expired') {
            return $error;
        }
    }

//-----------------------------------------------------------------------------------------------------------------------------------------
}





