<?php

namespace App\Http\Controllers\Front;

use App\Payment;
use Illuminate\Http\Request;
use App\Http\Controllers\Controller;

class MpesaCallbacksController extends Controller
{
    public function receivePayment()
    {
        $data = file_get_contents('php://input');
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
        //return ["merchantRequestID" => "It works"];
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
}

