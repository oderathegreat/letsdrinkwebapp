<?php

namespace App\Http\Controllers\Front;

use App\Payment;
use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use Illuminate\Support\Facades\Log;

class MpesaCallbacksController extends Controller
{
    public function receivePayment(Request $request)
    {
        $data = $request->getContent();
        Log::info("MPESA__CALLBACKS");
        Log::info($data);
        $dataObject = json_decode($data);
        if ($dataObject->Body->stkCallback->ResultCode=="0"){
            $merchantRequestID=$dataObject->Body->stkCallback->MerchantRequestID;
            $checkoutRequestID=$dataObject->Body->stkCallback->CheckoutRequestID;
            $payment = Payment::where(["checkoutRequestID"=>$checkoutRequestID,"merchantRequestID"=>$merchantRequestID])->first();
            $transactionDate="";
            $mpesaCode="";

            $metaArray = $dataObject->Body->stkCallback->CallbackMetadata->Item;
            foreach ($metaArray as $item){
                if ($item->Name=="MpesaReceiptNumber"){
                    $mpesaCode=$item->Value;
                }
                if ($item->Name=="TransactionDate"){
                    $transactionDate=$item->Value;
                }
            }
            if (!is_null($payment)){
                $payment->fill(["transactionDate"=>$transactionDate,"mpesaCode"=>$mpesaCode,"completed"=>true]);
            }
        }
        return $resultArray=[
                "ResultDesc"=>"Confirmation Service request accepted successfully",
                "ResultCode"=>"0"
            ];
    }

    public function statusCheck(Request $request)
    {
        $merchantRequestID =$request->merchantRequestID;
        $payment =Payment::where(["merchantRequestID"=>$merchantRequestID,"completed"=>true])->first();
        if ($payment){
            return ["status" => "Completed"];
        }else{
            return ["status" => "Processing"];
        }
    }

    public function manualStatusCheck(Request $request)
    {
        $data = $request->getContent();
        Log::info($data);
        return $data;
    }
}

