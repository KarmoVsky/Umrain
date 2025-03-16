
    {{-- @include (app_path('Library/Myfatoorah-library-2.2/MyfatoorahLoader.php')) --}}
    {{-- @include (app_path('Library/Myfatoorah-library-2.2/MyfatoorahLibrary.php')) --}}

<div class="form-section">
    <h4 class="form-section-title">{{__('Select Payment Method')}}</h4>
    <div class="gateways-table accordion" id="accordionExample">
        @foreach($gateways as $k=>$gateway)
            <div class="card">
                <div class="card-header">
                    <h4 class="mb-0">
                        <label class="" data-toggle="collapse" data-target="#gateway_{{$k}}" >
                            <input type="radio" name="payment_gateway" value="{{$k}}">
                            @if($logo = $gateway->getDisplayLogo())
                                <img src="{{$logo}}" alt="{{$gateway->getDisplayName()}}">
                            @endif
                            {{$gateway->getDisplayName()}}
                        </label>
                    </h4>
                </div>
                <div id="gateway_{{$k}}" class="collapse" aria-labelledby="headingOne" data-parent="#accordionExample">
                    <div class="card-body" id="payment-myfatoorahgateway">
                        <div class="gateway_name">
                            {!! $gateway->getDisplayName() !!}
                        </div>
                        {!! $gateway->getDisplayHtml() !!}
{{--
                        @if($gateway->getDisplayName() === 'My Fatoorah')


                            @if ($gateway->getOption('qpay'))
                                <input type="radio" id="option1" name="payment" value="7">
                                <label for="option1">
                                    <img src="{{ asset('images/gateways/np.png') }}" alt="QPay">
                                    <div class="tooltip">{{ __('QPay') }}</div> <!-- النص التوضيحي -->
                                </label>
                            @endif

                            @if ($gateway->getOption('mada'))
                                <input type="radio" id="option2" name="payment" value="6">
                                <label for="option2">
                                    <img src="{{ asset('images/gateways/md.png') }}" alt="MADA">
                                    <div class="tooltip">{{ __('MADA') }}</div> <!-- النص التوضيحي -->
                                </label>
                            @endif

                            @if ($gateway->getOption('apple_pay'))
                                <input type="radio" id="option3" name="payment" value="11">
                                <label for="option3">
                                    <img src="{{ asset('images/gateways/ap.png') }}" alt="Apple Pay">
                                    <div class="tooltip">{{ __('Apple Pay') }}</div> <!-- النص التوضيحي -->
                                </label>
                            @endif

                            @if ($gateway->getOption('visa_master'))
                                <input type="radio" id="option4" name="payment" value="2">
                                <label for="option4">
                                    <img src="{{ asset('images/gateways/vm.png') }}" alt="VISA/MASTER">
                                    <div class="tooltip">{{ __('VISA/MASTER') }}</div> <!-- النص التوضيحي -->
                                </label>
                            @endif

                            @if ($gateway->getOption('stc_pay'))
                                <input type="radio" id="option5" name="payment" value="14">
                                <label for="option5">
                                    <img src="{{ asset('images/gateways/stc.png') }}" alt="STC Pay">
                                    <div class="tooltip">{{ __('STC Pay') }}</div> <!-- النص التوضيحي -->
                                </label>
                            @endif

                            @if ($gateway->getOption('uae_debit_cards'))
                                <input type="radio" id="option6" name="payment" value="8">
                                <label for="option6">
                                    <img src="{{ asset('images/gateways/uaecc.png') }}" alt="UAE Debit Cards">
                                    <div class="tooltip">{{ __('UAE Debit Cards') }}</div> <!-- النص التوضيحي -->
                                </label>
                            @endif

                            @if ($gateway->getOption('visa_master_direct_3ds_flow'))
                                <input type="radio" id="option7" name="payment" value="9">
                                <label for="option7">
                                    <img src="{{ asset('images/gateways/vm.png') }}" alt="Visa/Master Direct 3DS Flow">
                                    <div class="tooltip">{{ __('Visa/Master Direct 3DS Flow') }}</div> <!-- النص التوضيحي -->
                                </label>
                            @endif

                            @if ($gateway->getOption('visa_master_direct'))
                                <input type="radio" id="option8" name="payment" value="20">
                                <label for="option8">
                                    <img src="{{ asset('images/gateways/vm.png') }}" alt="Visa/Master Direct">
                                    <div class="tooltip">{{ __('Visa/Master Direct') }}</div> <!-- النص التوضيحي -->
                                </label>
                            @endif

                            @if ($gateway->getOption('amex'))
                                <input type="radio" id="option9" name="payment" value="3">
                                <label for="option9">
                                    <img src="{{ asset('images/gateways/ae.png') }}" alt="AMEX">
                                    <div class="tooltip">{{ __('AMEX') }}</div> <!-- النص التوضيحي -->
                                </label>
                            @endif

                            @if ($gateway->getOption('apple_pay_mada'))
                                <input type="radio" id="option10" name="payment" value="25">
                                <label for="option10">
                                    <img src="{{ asset('images/gateways/ap.png') }}" alt="Apple Pay (Mada)">
                                    <div class="tooltip">{{ __('Apple Pay (Mada)') }}</div> <!-- النص التوضيحي -->
                                </label>
                            @endif

                            @if ($gateway->getOption('google_pay'))
                                <input type="radio" id="option11" name="payment" value="32">
                                <label for="option11">
                                    <img src="{{ asset('images/gateways/gp.png') }}" alt="GooglePay">
                                    <div class="tooltip">{{ __('GooglePay') }}</div> <!-- النص التوضيحي -->
                                </label>
                            @endif

                            @if ($gateway->getOption('benefit'))
                                <input type="radio" id="option12" name="payment" value="5">
                                <label for="option12">
                                    <img src="{{ asset('images/gateways/b.png') }}" alt="Benefit">
                                    <div class="tooltip">{{ __('Benefit') }}</div> <!-- النص التوضيحي -->
                                </label>
                            @endif

                            @if ($gateway->getOption('knet'))
                                <input type="radio" id="option13" name="payment" value="1">
                                <label for="option13">
                                    <img src="{{ asset('images/gateways/kn.png') }}" alt="KNET">
                                    <div class="tooltip">{{ __('KNET') }}</div> <!-- النص التوضيحي -->
                                </label>
                            @endif


                        @endif
--}}
                    </div>
                </div>
            </div>
        @endforeach
    </div>
</div>
