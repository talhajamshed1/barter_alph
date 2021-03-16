<?php

/* 
 * To change this license header, choose License Headers in Project Properties.
 * To change this template file, choose Tools | Templates
 * and open the template in the editor.
 */
//require_once('stripe-php-5.1.3/init.php');
require_once("stripe-php-1.18.0/lib/Stripe.php");

function create_charge($api_key,$token,$amount,$email)
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
  "source" => $token//->id,
));
    $trans_details=array('amount' => (int)($amount*100), 'currency' => 'USD',"customer" => $customer->id );
    $charge = Stripe_Charge::create($trans_details);
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