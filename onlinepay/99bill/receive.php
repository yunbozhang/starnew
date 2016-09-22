<?php
define('IN_CONTEXT', 1);
require_once("../payment_load.php");

$has_error = false;
$error_msg = '';

$o_payacct =& new PaymentAccount();
$payacct =& $o_payacct->find("`payment_provider_id`='2' AND `enabled`='1'");
if (!$payacct) {
    $has_error = true;
    $error_msg = __('Payment account error! Cannot continue!');
}

if (!$has_error) {
    $merchantAcctId=trim($_REQUEST['merchantAcctId']);
    $key=$payacct->partner_key;
    $version=trim($_REQUEST['version']);
    $language=trim($_REQUEST['language']);
    $signType=trim($_REQUEST['signType']);
    $payType=trim($_REQUEST['payType']);
    $bankId=trim($_REQUEST['bankId']);
    $orderId=trim($_REQUEST['orderId']);
    $orderTime=trim($_REQUEST['orderTime']);
    $orderAmount=trim($_REQUEST['orderAmount']);
    $dealId=trim($_REQUEST['dealId']);
    $bankDealId=trim($_REQUEST['bankDealId']);
    $dealTime=trim($_REQUEST['dealTime']);
    $payAmount=trim($_REQUEST['payAmount']);
    $fee=trim($_REQUEST['fee']);
    $ext1=trim($_REQUEST['ext1']);
    $ext2=trim($_REQUEST['ext2']);
    $payResult=trim($_REQUEST['payResult']);
    $errCode=trim($_REQUEST['errCode']);
    $signMsg=trim($_REQUEST['signMsg']);
    
        $merchantSignMsgVal=appendParam($merchantSignMsgVal,"merchantAcctId",$merchantAcctId);
        $merchantSignMsgVal=appendParam($merchantSignMsgVal,"version",$version);
        $merchantSignMsgVal=appendParam($merchantSignMsgVal,"language",$language);
        $merchantSignMsgVal=appendParam($merchantSignMsgVal,"signType",$signType);
        $merchantSignMsgVal=appendParam($merchantSignMsgVal,"payType",$payType);
        $merchantSignMsgVal=appendParam($merchantSignMsgVal,"bankId",$bankId);
        $merchantSignMsgVal=appendParam($merchantSignMsgVal,"orderId",$orderId);
        $merchantSignMsgVal=appendParam($merchantSignMsgVal,"orderTime",$orderTime);
        $merchantSignMsgVal=appendParam($merchantSignMsgVal,"orderAmount",$orderAmount);
        $merchantSignMsgVal=appendParam($merchantSignMsgVal,"dealId",$dealId);
        $merchantSignMsgVal=appendParam($merchantSignMsgVal,"bankDealId",$bankDealId);
        $merchantSignMsgVal=appendParam($merchantSignMsgVal,"dealTime",$dealTime);
        $merchantSignMsgVal=appendParam($merchantSignMsgVal,"payAmount",$payAmount);
        $merchantSignMsgVal=appendParam($merchantSignMsgVal,"fee",$fee);
        $merchantSignMsgVal=appendParam($merchantSignMsgVal,"ext1",$ext1);
        $merchantSignMsgVal=appendParam($merchantSignMsgVal,"ext2",$ext2);
        $merchantSignMsgVal=appendParam($merchantSignMsgVal,"payResult",$payResult);
        $merchantSignMsgVal=appendParam($merchantSignMsgVal,"errCode",$errCode);
        $merchantSignMsgVal=appendParam($merchantSignMsgVal,"key",$key);
    $merchantSignMsg= md5($merchantSignMsgVal);
    
    if(strtoupper($signMsg)==strtoupper($merchantSignMsg)){
       // Check history
       $spec_code = parse_speccode($ext1);
       $pay_histo = check_history($spec_code[0], $orderId, '2', $spec_code[1], '0');
       if ($pay_histo) {
            switch($payResult){
                  case "10":
                           if (substr($orderId, 0, 3) == 'ord') {
                               $ok_script = 'return_ok_ord.php';
                               $order_id = substr($orderId, 3);
                               $rs = update_order($spec_code[0], $order_id, $payAmount);
                               if (!$rs) {
                                    $has_error = true;
                                    $error_msg = __('Unknown Order!');
                               } else {
                                   $pay_histo->finished = '1';
                                   $pay_histo->return_time = time();
                                   $pay_histo->save();
                               }
                           } else if (substr($outer_oid, 0, 3) == 'sav') {
                               $ok_script = 'return_ok_sav.php';
                               $rs = save_money($spec_code[0], $payAmount, '99bill');
                               if (!$rs) {
                                    $has_error = true;
                                    $error_msg = __('Cannot save your money!');
                               } else {
                                   $pay_histo->finished = '1';
                                   $pay_histo->return_time = time();
                                   $pay_histo->save();
                               }
                           } else {
                                $has_error = true;
                                $error_msg = __('Unknown Order!');
                           }
                      break;
                  default:
                        $has_error = true;
                        $error_msg = __('Unknown Payment!');
                      break;
            }
       } else {
            $has_error = true;
            $error_msg = __('Unknown Payment!');
       }
    }else{
        $has_error = true;
        $error_msg = __('Data verification failed! Cannot continue!');
    }
}

function appendParam($returnStr,$paramId,$paramValue){
    if($returnStr!=""){
        if($paramValue!=""){
            $returnStr.="&".$paramId."=".$paramValue;
        }
    }else{
        If($paramValue!=""){
            $returnStr=$paramId."=".$paramValue;
        }
    }
    return $returnStr;
}

if ($has_error) {
    include_once(ROOT.'/view/onlinepay'.DS.'return_err.php');
} else {
    include_once(ROOT.'/view/onlinepay'.DS.$ok_script);
}
?>