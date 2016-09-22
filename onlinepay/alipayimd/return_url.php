<?php
define('IN_CONTEXT', 1);
ini_set("display_errors","on");
error_reporting(E_ALL);
require_once("../payment_load.php");
require_once("class/alipay_notify.php");
//echo'<a href="/?_m=mod_order&_a=useraccountstate">返回(goback)</a>';//2011.5.18
header("Content-type:text/html;charset=utf-8");//xqf 

$has_error = false;
$error_msg = '';

$o_payacct =& new PaymentAccount();
$payacct =& $o_payacct->find("`payment_provider_id`='7' AND `enabled`='1'");
if (!$payacct) {
    $has_error = true;
    $error_msg = __('Payment account error! Cannot continue!');
}
if($has_error){
	include_once(P_TPL.DS.'onlinepay'.DS.'return_err.php');
	die;
} 

//构造通知函数信息
$partner        = $payacct->partner_id;       //合作伙伴ID
$security_code  = $payacct->partner_key;       //安全检验码
$seller_email   = $payacct->seller_account;       //卖家支付宝帐户
$_input_charset = "utf-8";  //字符编码格式  目前支持 GBK 或 utf-8
$sign_type      = "MD5";    //加密方式  系统默认(不要修改)
$transport      = "http";  //访问模式,你可以根据自己的服务器是否支持ssl访问而选择http以及https访问模式(系统默认,不要修改)
$alipay = new alipay_notify($partner,$security_code,$sign_type,$_input_charset,$transport);
//计算得出通知验证结果
$verify_result = $alipay->return_verify();

/*
xqf 2012.06.12 用户反馈 通过支付宝支付订单状态不对
*/
if($verify_result) {
	
/*	echo $verify_result.'认证通过';
	echo'<br>outer_oid:'.$_GET['out_trade_no'];
	echo '<br>is_success:'.$_GET['is_success'];
	echo '<br>trade_status:'.$_GET['trade_status'];*/

    //验证成功
    //获取支付宝的通知返回参数
    $outer_oid           = $_GET['out_trade_no'];    //获取订单号
    $total_fee         = $_GET['total_fee'];	    //获取总价格
    $body = $_GET['body'];

    if($_GET['trade_status'] == 'TRADE_FINISHED' || $_GET['trade_status'] == 'TRADE_SUCCESS') {
    		$spec_code = parse_speccode($body);
       		$pay_histo = check_history($spec_code[0], $outer_oid, '7', $spec_code[1], '0');
/*
function parse_speccode($param) {
    $return_parts = explode('>', $param);
    return explode(',', $return_parts[1]);
}
check_history($user_id, $outer_oid, $payment_provider_id, $send_time, $finished) {
    $o_payhisto =& new OnlinepayHistory();
    $curr_histo =& $o_payhisto->find("user_id=? AND outer_oid=? AND payment_provider_id=? AND send_time=? AND finished=?", 
        array($user_id, $outer_oid, $payment_provider_id, $send_time, $finished));
    return $curr_histo;
    
    */     if (substr($outer_oid, 0, 3) == 'ord') 
	    	{
               $ok_script = 'return_ok_ord.php';
               $order_id = substr($outer_oid, 3);
               $rs = update_order($spec_code[0], $order_id, $total_fee);
               if (!$rs) 
               {
                    $has_error = true;
                    $error_msg = __('Unknown Order!');
               } 
               else 
               {
               	   $has_error = false;
                   $pay_histo->finished = '1';
                   $pay_histo->return_time = time();
                   $pay_histo->save();
				  // header("Location:/");//2011.5.18 xqf 有这句会出错
				  echo'<a href="/?_m=mod_order&_a=userlistorder">点此查看(Return to check)</a>';// 2012.7.12
				  echo "&nbsp;&nbsp;支付成功(Pay for success)";
				   exit;
/*				  http://www.bbkj106.com/onlinepay/alipayimd/return_url.php?body=%3E10%2C1305536381&buyer_email=puminli%40126.com&buyer_id=2088002673053631&exterface=create_direct_pay_by_user&is_success=T&notify_id=RqPnCoPT3K9%252Fvwbh3I7xtncchYP%252BTV%252F6oaEzjfoxNERDsu4HBbMtEe3LEqcP7GMLZ9a0&notify_time=2011-05-16+17%3A00%3A13&notify_type=trade_status_sync&out_trade_no=sav20110516165941&payment_type=1&seller_email=aibyv2005%40sina.com&seller_id=2088002241152643&subject=%E5%9C%A8%E7%BA%BF%E5%85%A5%E6%AC%BE&total_fee=0.01&trade_no=2011051685836563&trade_status=TRADE_SUCCESS&sign=be6e2379c21f86443a3c79d9df3f88f3&sign_type=MD5
*/               }
           }
           elseif(substr($outer_oid, 0, 3) == 'sav') 
           {
           	   $ok_script = 'return_ok_sav.php';
               $rs = save_money($spec_code[0], $total_fee, 'alipayimd');
               if (!$rs) 
               {
                    $has_error = true;
                    $error_msg = __('Cannot save your money!');
               } 
               else 
               {
               	   $has_error = false;
                   $pay_histo->finished = '1';
                   $pay_histo->return_time = time();
                   $pay_histo->save();
               }
           }
           else
           {
                $has_error = true;
                $error_msg = __('Unknown Order!');
           }
    }
    else {
      $has_error = true;
      $error_msg = __('Data verification failed! Cannot continue!');
    }
}
else {
    $has_error = true;
   	$error_msg = __('Data verification failed! Cannot continue!');
}
if ($has_error) {
    include_once(P_VIEW.DS.'onlinepay'.DS.'return_err.php');
} else {
    include_once(P_VIEW.DS.'onlinepay'.DS.$ok_script);
}
?>