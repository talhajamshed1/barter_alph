<?php

/* 
 * To change this license header, choose License Headers in Project Properties.
 * To change this template file, choose Tools | Templates
 * and open the template in the editor.
 */
//require_once('stripe-php-5.1.3/init.php');
require_once("./api/stripe-php-1.18.0/lib/Stripe.php");

function create_charge($api_key,$token,$amount,$email,$pay_address,$desc,$user_name)
{
    
//  \Stripe\Stripe::setApiKey($api_key);

//$token=\Stripe\Token::create(array(
//  "card" => array(
//     "number" => "4242424242424242",
//    "exp_month" => 8,
//    "exp_year" => 2018,
//    "cvc" => "314"
//  )
//));
//$customer = \Stripe\Customer::create(array(
//  "email" => $email,//"paying.user@example.com",
//  "source" => $token//->id,
//));
//    $trans_details=array('amount' => (int)($amount*100), 'currency' => 'USD',"customer" => $customer->id );
//    
//$charge = \Stripe\Charge::create($trans_details);
//return $charge;

/////////////////////////////  FOR PHP 5.2 ///////////////////////////////////

 Stripe::setApiKey($api_key);

 $customer = Stripe_Customer::create(array(
  "email" => $email,//"paying.user@example.com",
  "source" => $token,//->id,
  "address" => $pay_address,
  "description" => $desc,
  "name" =>$user_name 
));

    $trans_details=array('amount' => (int)($amount*100), 'currency' => PAYMENT_CURRENCY_CODE,"customer" => $customer->id,'description'=>$desc );
    
    try{
    $charge = Stripe_Charge::create($trans_details);
    } catch(Stripe_CardError $e) {
        return $e->getMessage();
      } catch (Stripe_InvalidRequestError $e) {
        // Invalid parameters were supplied to Stripe's API
        return $e->getMessage();
      } catch (Stripe_AuthenticationError $e) {
        // Authentication with Stripe's API failed
        return $e->getMessage();
      } catch (Stripe_ApiConnectionError $e) {
        // Network communication with Stripe failed
        return $e->getMessage();
      } catch (Stripe_Error $e) {
        // Display a very generic error to the user, and maybe send
        // yourself an email
        return $e->getMessage();
      } catch (Exception $e) {
        // Something else happened, completely unrelated to Stripe
        return $e->getMessage();
      } 
    return $charge;


/////////////////////////////////////////////////////////////////////////////////


}

//function create_charge($api_key)
//{
//    
//  \Stripe\Stripe::setApiKey($api_key);
//
//$token=\Stripe\Token::create(array(
//  "card" => array(
//     "number" => "4242424242424242",
//    "exp_month" => 8,
//    "exp_year" => 2018,
//    "cvc" => "314"
//  )
//));
//$customer = \Stripe\Customer::create(array(
//  "email" => "paying.user@example.com",
//  "source" => $token->id,
//));
//    $trans_details=array('amount' => 1000, 'currency' => 'USD',"customer" => $customer->id );
//    
//$charge = \Stripe\Charge::create($trans_details);
//return $charge;
//}
//checking demo
switch (DisplayLookUp('stripedemo')) {
    case 'YES':
        $paymentMode = 'TEST';
        $api_key = DisplayLookUp('stripekey');
        break;

    case 'NO':
        $paymentMode = 'LIVE';
        $api_key = DisplayLookUp('stripekeylive');
        break;
}

$token = $_POST["token"];
switch ($Sscope){
    case 'buy':
        $user_email = $_POST["txtEmail"];
        $user_name = $_POST["txtFirstName"]." ".$_POST["txtLastName"]; 
        $pay_address = ["city" => $City, "country" => $Country, "line1" => $Address, "line2" => "", "postal_code" => $Zip, "state" => $State];
        $desc = "Payment for ".$var_title." quantity - ".$_POST["reqQty"];
        $requested_amount = $amnt;
    break;

    case 'pay':
    case 'paypage':
        $user_email = $_POST["txtEmail"];
        $user_name = $_POST["txtFirstName"]." ".$_POST["txtLastName"]; 
        $pay_address = ["city" => $City, "country" => $Country, "line1" => $Address, "line2" => "", "postal_code" => $Zip, "state" => $State];
        $desc = "Payment for ".TEXT_USER_REGISTRATION." amount - ".$amount;
        $requested_amount = $cost;
    break;
    case 'buycredits':
    case 'successfee':
        $user_email = $_POST["txtEmail"];
        $user_name = $_POST["txtFirstName"]." ".$_POST["txtLastName"]; 
        $pay_address = ["city" => $City, "country" => $Country, "line1" => $Address, "line2" => "", "postal_code" => $Zip, "state" => $State];
        $desc = "Payment for ".$pageTitle." amount - ".$cost;
        $requested_amount = $cost;
    break;
    case 'featuredpay':
        $user_email = $_POST["txtEmail"];
        $user_name = $_POST["txtFirstName"]." ".$_POST["txtLastName"]; 
        $pay_address = ["city" => $City, "country" => $Country, "line1" => $_POST["txtAddress"], "line2" => "", "postal_code" => $Zip, "state" => $State];
        $desc = "Payment for ".TEXT_SALE_ITEM_ADDITION." amount - ".$cost;
        $requested_amount = $cost;
    break;
    case 'planupgrade':
        $user_email = $_POST["txtEmail"];
        $user_name = $_POST["txtFirstName"]." ".$_POST["txtLastName"]; 
        $pay_address = ["city" => $City, "country" => $Country, "line1" => $_POST["txtAddress"], "line2" => "", "postal_code" => $Zip, "state" => $State];
        $desc = "Payment for ".$pageTitle." amount - ".$cost;
        $requested_amount = $cost;
    break;
    }

$charge_details=create_charge($api_key,$token,$requested_amount,$user_email,$pay_address,$desc,$user_name);

if($charge_details->id) {
    $cc_tran=$charge_details->id;
    $method = 'stripe';                
    $cc_flag = true;
}else {
    $cc_err=$charge_details;
    $cc_flag= false;
    $status = 0;
}