<?php
namespace Modules\Booking\Gateways;

use Illuminate\Http\Request;
use Modules\Booking\Models\Booking;
use Modules\Booking\Models\Payment;
use Illuminate\Support\Facades\Log;
use Modules\User\Models\User;
use Exception;
use Illuminate\Support\Facades\Http;
/* use App\Http\Controllers\MyFatoorahController; */
use Illuminate\Support\Facades\Session;

class MyFatoorahGateway extends BaseGateway
{
    public $name = 'My Fatoorah';
    protected $gateway;


    //Setup gateway settings
    public function getOptionsConfigs()
{
    return [
        [
            'type'  => 'checkbox',
            'id'    => 'enable',
            'label' => __('Enable My Fatoorah?')
        ],
        [
            'type'       => 'input',
            'id'         => 'name',
            'label'      => __('Custom Name'),
            'std'        => __("My Fatoorah"),
            'multi_lang' => "1"
        ],
        [
            'type'  => 'upload',
            'id'    => 'logo_id',
            'label' => __('Custom Logo'),
        ],
        [
            'type'  => 'editor',
            'id'    => 'html',
            'label' => __('Custom HTML Description'),
            'multi_lang' => "1"
        ],
        [
            'type'      => 'input',
            'id'        => 'api_key',
            'label'     => __('API Key'),
        ],
        [
            'type'      => 'checkbox',
            'id'        => 'capture',
            'label'     => __('Capture'),
        ],
        [
            'type'      => 'checkbox',
            'id'        => 'test_mode',
            'label'     => __('Test Mode'),
        ],
/*
        [
            'type'      => 'checkbox',
            'id'        => 'save_card',
            'label'     => __('Save Card'),
        ],
 */
        [
            'type'      => 'checkbox',
            'id'        => 'register_apple_pay',
            'label'     => __('Register Apple Pay'),
        ],
        [
            'type'      => 'input',
            'id'        => 'call_back_url',
            'label'     => __('Call Back Url'),
        ],
/*
        [
            'type'      => 'input',
            'id'        => 'webhook_secret_key',
            'label'     => __('Webhook Secret Key'),
        ],
 */
        // [
        //     'type'      => 'input',
        //     'id'        => 'country_iso',
        //     'label'     => __('Country Iso'),
        // ],
/*
        [
            'type'      => 'checkbox_and_img',
            'id'        => 'qpay',
            'img'       => asset('images/gateways/np.png'),
            'label'     => __('QPay'),
        ],
        [
            'type'      => 'checkbox_and_img',
            'id'        => 'mada',
            'img'       => asset('images/gateways/md.png'),
            'label'     => __('MADA'),
        ],
        [
            'type'      => 'checkbox_and_img',
            'id'        => 'apple_pay',
            'img'       => asset('images/gateways/ap.png'),
            'label'     => __('Apple Pay'),
        ],
        [
            'type'      => 'checkbox_and_img',
            'id'        => 'visa_master',
            'img'       => asset('images/gateways/vm.png'),
            'label'     => __('VISA/MASTER'),
        ],
        [
            'type'      => 'checkbox_and_img',
            'id'        => 'stc_pay',
            'img'       => asset('images/gateways/stc.png'),
            'label'     => __('STC Pay'),
        ],
        [
            'type'      => 'checkbox_and_img',
            'id'        => 'uae_debit_cards',
            'img'       => asset('images/gateways/uaecc.png'),
            'label'     => __('UAE Debit Cards'),
        ],
        [
            'type'      => 'checkbox_and_img',
            'id'        => 'visa_master_direct_3ds_flow',
            'img'       => asset('images/gateways/vm.png'),
            'label'     => __('Visa/Master Direct 3DS Flow'),
        ],
        [
            'type'      => 'checkbox_and_img',
            'id'        => 'visa_master_direct',
            'img'       => asset('images/gateways/vm.png'),
            'label'     => __('Visa/Master Direct'),
        ],
        [
            'type'      => 'checkbox_and_img',
            'id'        => 'amex',
            'img'       => asset('images/gateways/ae.png'),
            'label'     => __('AMEX'),
        ],
        [
            'type'      => 'checkbox_and_img',
            'id'        => 'apple_pay_mada',
            'img'       => asset('images/gateways/ap.png'),
            'label'     => __('Apple Pay (Mada)'),
        ],
        [
            'type'      => 'checkbox_and_img',
            'id'        => 'google_pay',
            'img'       => asset('images/gateways/gp.png'),
            'label'     => __('GooglePay'),
        ],
        [
            'type'      => 'checkbox_and_img',
            'id'        => 'benefit',
            'img'       => asset('images/gateways/b.png'),
            'label'     => __('Benefit'),
        ],
        [
            'type'      => 'checkbox_and_img',
            'id'        => 'knet',
            'img'       => asset('images/gateways/kn.png'),
            'label'     => __('KNET'),
        ],
*/
   ];
}

//Handle payment
public function process(Request $request, $booking, $service)
{
   /*  $mfObj = new MyFatoorahController();
    $mfObj->setBooking($booking); */

    Session::put('bookingSession', $booking);
    // dd(Session::all());

    if (in_array($booking->status, [
        $booking::PAID,
        $booking::COMPLETED,
        $booking::CANCELLED
    ])) {
        throw new Exception(__("Booking status does not need to be paid"));
    }

    if (!$booking->pay_now) {
        throw new Exception(__("Booking total is zero. Can not process payment gateway!"));
    }

    $payment = new Payment();
    $payment->booking_id = $booking->id;
    $payment->payment_gateway = $this->id;
    $payment->status = 'draft';

    $user = User::find($booking->customer_id);
    $currencyCode = session('bc_current_currency', 'sar');

    $countryCodeData = json_decode($request->get('country_code'), true);
    $mobileCountryCode = $countryCodeData['phoneCode'] ?? null;

    $data = [
        'CustomerName' => $user->name,
        'InvoiceValue' => $booking->pay_now,
        'DisplayCurrencyIso' => $currencyCode,
        'CustomerEmail' => $user->email,
        'CallBackUrl' => $this->getOption('call_back_url'),
        'ErrorUrl' => $this->getOption('call_back_url'),
        'MobileCountryCode' =>$mobileCountryCode,
        'CustomerMobile' => $booking->phone, //The gateway does not accept more than 11 digits
        'Language' => app()->getLocale(),
        'CustomerReference' => $booking->code,
        'SourceInfo' => 'Laravel ' . app()::VERSION . ' - MyFatoorah Package ' . MYFATOORAH_LARAVEL_PACKAGE_VERSION,
        'PaymentMethodId' => $request->payment,
        'DisplayCurrencyIso' => 'SAR',
        'NotificationOption' => 'LNK', // added
        'ProcessingDetails' => [
            'AutoCapture' => ($this->getOption('capture') == '1')? false : true
        ]
    ];


    //Call My Fatoorah API

    $response = $this->callMyFatoorahApi($data);
    // dd($response['Data']);
    // dd('stooopppp');
    if ($response['IsSuccess']) {
        $payment->save();
        $booking->status = $booking::UNPAID;
        $booking->payment_id = $payment->id;
        $booking->save();
        // dd($response['Data']);

        //Redirect the user to the payment page
        return response()->json([
            'url' => $response['Data']['InvoiceURL']
            //  'url' => route('myfatoorah', ['data' => $data])
        ])->send();
    } else {
        throw new Exception('My Fatoorah Gateway: ' . $response['Message']);
    }
}

//Handle confirmation and cancellation
public function confirmPayment(Request $request)
{
    $c = $request->query('c');
    $booking = Booking::where('code', $c)->first();
    if (!empty($booking) and in_array($booking->status, [$booking::UNPAID])) {
        // الحصول على تفاصيل الدفع من ماي فاتورة
        $response = $this->getPaymentDetailsFromMyFatoorah($booking->payment);

        if ($response['IsSuccess']) {
            $payment = $booking->payment;
            if ($payment) {
                $payment->status = 'completed';
                $payment->logs = \GuzzleHttp\json_encode($response['Data']);
                $payment->save();
            }
            $booking->markAsPaid();
            return redirect($booking->getDetailUrl())->with("success", __("You payment has been processed successfully"));
        } else {
            $payment = $booking->payment;
            if ($payment) {
                $payment->status = 'fail';
                $payment->logs = \GuzzleHttp\json_encode($response['Data']);
                $payment->save();
            }
            $booking->markAsPaymentFailed();
            return redirect($booking->getDetailUrl())->with("error", __("Payment Failed"));
        }
    }
    return redirect(url('/'));
}

//Call My Fatoorah API
protected function callMyFatoorahApi($data)
{
    //Get API key from settings
    $apiKey = $this->getOption('api_key');
    //Determine test mode
    $testMode = $this->getOption('test_mode');

    //Set endpoint based on test mode
    $endpoint = $testMode ? 'https://apitest.myfatoorah.com/v2/SendPayment' : 'https://api-sa.myfatoorah.com/v2/SendPayment';

    try {
        //Send POST request using Http Facade
        $response = Http::withHeaders([
            'Authorization' => 'Bearer ' . $apiKey,
            'Content-Type' => 'application/json'
        ])->post($endpoint, $data);

        //Return response as PHP array
        if ($response->successful()) {
            return $response->json();
        } else {
            //Log error in case of response failure
            Log::error('My Fatoorah API Error: ' . $response->body());
            return [
                'IsSuccess' => false,
                'Message' => 'Failed to connect to My Fatoorah API: ' . $response->body()
            ];
        }
    } catch (\Exception $e) {
        //Log error in case of exception
        Log::error('My Fatoorah API Error: ' . $e->getMessage());
        return [
            'IsSuccess' => false,
            'Message' => 'Failed to connect to My Fatoorah API'
        ];
    }
}


public function testSelect()
{
    return [
        "aud" => "Australian dollar",
        "brl" => "Brazilian real 2",
        "cad" => "Canadian dollar",
        "cny" => "Chinese Renmenbi 3",
        "czk" => "Czech koruna",
        "dkk" => "Danish krone",
        "eur" => "Euro",
        "hkd" => "Hong Kong dollar",
        "huf" => "Hungarian forint 1",
        "ils" => "Israeli new shekel",
        "jpy" => "Japanese yen 1",
        "myr" => "Malaysian ringgit 2",
        "mxn" => "Mexican peso",
        "twd" => "New Taiwan dollar 1",
        "nzd" => "New Zealand dollar",
        "nok" => "Norwegian krone",
        "php" => "Philippine peso",
        "pln" => "Polish złoty",
        "gbp" => "Pound sterling",
        "rub" => "Russian ruble",
        "sgd" => "Singapore dollar ",
        "sek" => "Swedish krona",
        "chf" => "Swiss franc",
        "thb" => "Thai baht",
        "usd" => "United States dollar",
    ];
}


}
