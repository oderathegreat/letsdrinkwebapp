@if($payment == 'cod') 
                                <input type="hidden" name="method" value="Cash On Delivery">


@endif
@if($payment == 'paypal') 
                                <input type="hidden" name="method" value="Paypal">
                                <input type="hidden" name="cmd" value="_xclick">
                                <input type="hidden" name="no_note" value="1">
                                <input type="hidden" name="lc" value="UK">
                                <input type="hidden" name="currency_code" value="{{$curr->name}}">
                                <input type="hidden" name="bn" value="PP-BuyNowBF:btn_buynow_LG.gif:NonHostedGuest">

@endif

@if($payment == 'stripe') 
                                	<input type="hidden" name="method" value="Stripe">
                                  <div class="row" >
                                    <div class="col-lg-6">
                                      <input class="form-control card-elements" name="cardNumber" type="text" placeholder="{{ $langg->lang163 }}" autocomplete="off"  autofocus oninput="validateCard(this.value);" />
                                      <span id="errCard"></span>
                                    </div>
                                    <div class="col-lg-6">
                                      <input class="form-control card-elements" name="cardCVC" type="text" placeholder="{{ $langg->lang164 }}" autocomplete="off"  oninput="validateCVC(this.value);" />
                                      <span id="errCVC"></span>
                                    </div>
                                    <div class="col-lg-6">
                                      <input class="form-control card-elements" name="month" type="text" placeholder="{{ $langg->lang165 }}"  />
                                    </div>
                                    <div class="col-lg-6">
                                      <input class="form-control card-elements" name="year" type="text" placeholder="{{ $langg->lang166 }}"  />
                                    </div>
                                </div>


                                <script type="text/javascript" src="{{ asset('assets/front/js/payvalid.js') }}"></script>
                                <script type="text/javascript" src="{{ asset('assets/front/js/paymin.js') }}"></script>
                                <script type="text/javascript" src="https://js.stripe.com/v2/"></script>
                                <script type="text/javascript" src="{{ asset('assets/front/js/payform.js') }}"></script>


                                <script type="text/javascript">
                                  var cnstatus = false;
                                  var dateStatus = false;
                                  var cvcStatus = false;
                              
                                  function validateCard(cn) {
                                    cnstatus = Stripe.card.validateCardNumber(cn);
                                    if (!cnstatus) {
                                      $("#errCard").html('{{ $langg->lang781 }}');
                                    } else {
                                      $("#errCard").html('');
                                    }

                              
                              
                                  }
                              
                                  function validateCVC(cvc) {
                                    cvcStatus = Stripe.card.validateCVC(cvc);
                                    if (!cvcStatus) {
                                      $("#errCVC").html('{{ $langg->lang782 }}');
                                    } else {
                                      $("#errCVC").html('');
                                    }
            
                                  }
                              
                                </script>


@endif


@if($payment == 'instamojo') 
                                	<input type="hidden" name="method" value="Instamojo">

@endif


@if($payment == 'paystack') 
                              
        <input type="hidden" name="ref_id" id="ref_id" value="">
        <input type="hidden" name="sub" id="sub" value="0">
		    <input type="hidden" name="method" value="Paystack">





@endif

@if($payment == 'razorpay') 

                                  <input type="hidden" name="method" value="Razorpay">

@endif

@if($payment == 'molly') 
                                  <input type="hidden" name="method" value="Molly">

@endif


@if($payment == 'other') 

                                <input type="hidden" name="method" value="{{ $gateway->title }}">

                                  <div class="row" >

<div class="col-lg-12 pb-2 d-none">
	
	{!! $gateway->details !!}

</div>


<div class="col-lg-8">
	   <label class="d-none">{{ $langg->lang167 }} *</label>
	    <input class="form-control d-none" name="txn_id4" type="text" placeholder="{{ $langg->lang167 }}"/>
        <div id="mobile-form-payment">
            <input type="hidden" name="id" value="" id="mobile_transaction_id">
            <label>MPESA Number to pay </label>
            <input class="form-control" id="mpesa_phone" name="mpesa_phone" type="tel" value="{{ Auth::guard('web')->check() ? Auth::guard('web')->user()->phone : '' }}" placeholder="0723 xxx xxx"  />
            <button class="btn btn-success" id="mpesa-btn">Initiate Mpesa Payment</button>
        </div>
         <div id="progress-bar-area" class="d-none">
             <p class="text-center text-info" id="info-prompt">A prompt will appear on your phone, complete the transaction by entering the your PIN</p>
             <p class="text-center text-small" id="transaction-status">Processing Payment</p>
             <div id="pb"></div>
         </div>
</div>


  </div>
    <script>
        $(function(){
            $('#mpesa-btn').on('click', function () {
                $('#mobile-form-payment').addClass('d-none');
                $('#progress-bar-area').removeClass('d-none');
                $("#final-btn").toggle();
                var phone =$('#mpesa_phone').val();
                var total =$('.v-total-cost').data('cost').toLowerCase().replace("kshs","");
                var shipping =$('#shipping-cost').val();
                console.log(phone, total, shipping)
                axios.post('/mobile-payment-submit',{'phone':phone, 'total':total, 'shipping_cost':shipping })
                .then((response)=>{
                    console.log(response.data)
                    if(response.data.status==="0"){
                        const merchantRequestID= response.data.merchantRequestID;
                        console.log(merchantRequestID)
                        $('#mobile_transaction_id').val(merchantRequestID);
                        let timerId=setInterval(function() {
                            axios.post('/status/stk-push',{'merchantRequestID':merchantRequestID })
                            .then((responseCheck)=>{
                                console.log(responseCheck.data)
                                if(responseCheck.data.status==="Completed"){
                                    $("#pb").toggle('slow');
                                    $("#txn_id4").val(responseCheck.data.code);
                                    $("#info-prompt").toggle('slow');
                                    $("#transaction-status").text('Transaction Completed Successfully');
                                    $("#final-btn").click();
                                }
                            });
                        }, 4000);
                        setTimeout(() => { clearInterval(timerId);  }, 15000*4*2);//timeout after two minutes
                    }
                })
                .catch((error)=>{
                    console.log(error)
                });
            });
        });

        $(document).ready(function () {
            $("#pb").progressbar({ value: 100 });
            IndeterminateProgressBar($("#pb"));
        });
        function IndeterminateProgressBar(pb) {
            $(pb).css({ "padding-left": "0%", "padding-right": "90%" });
            $(pb).progressbar("option", "value", 100);
            $(pb).animate({ paddingLeft: "90%", paddingRight: "0%" }, 1000, "linear",
                function () { IndeterminateProgressBar(pb); });
        }
    </script>
@endif