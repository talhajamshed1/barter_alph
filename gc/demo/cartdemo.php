<?php

/**
 * Copyright (C) 2006 Google Inc.
 * 
 * Licensed under the Apache License, Version 2.0 (the "License");
 * you may not use this file except in compliance with the License.
 * You may obtain a copy of the License at
 * 
 *      http://www.apache.org/licenses/LICENSE-2.0
 * 
 * Unless required by applicable law or agreed to in writing, software
 * distributed under the License is distributed on an "AS IS" BASIS,
 * WITHOUT WARRANTIES OR CONDITIONS OF ANY KIND, either express or implied.
 * See the License for the specific language governing permissions and
 * limitations under the License.
 */

  // Point to the correct directory
  chdir("..");
  // Include all the required files
  require_once('library/googlecart.php');
  require_once('library/googleitem.php');
  require_once('library/googleshipping.php');
  require_once('library/googletax.php');

  // Invoke any of the provided use cases
  UseCase1();
  // UseCase2();
  // UseCase3();

  function UseCase1() {
    // Create a new shopping cart object
    $merchant_id = "";  // Your Merchant ID
    $merchant_key = "";  // Your Merchant Key
    $server_type = "sandbox";
    $currency = "USD";
    $cart = new GoogleCart($merchant_id, $merchant_key, $server_type, $currency); 

    // Add items to the cart
    $item_1 = new GoogleItem("MegaSound 2GB MP3 Player", // Item name
                             "Portable MP3 player - stores 500 songs", // Item description
                             1, // Quantity
                             175.49); // Unit price
    $item_2 = new GoogleItem("AA Rechargeable Battery Pack", 
                             "Battery pack containing four AA rechargeable batteries", 
                             1 , // Quantity
                             11.59); // Unit price
    $cart->AddItem($item_1);
    $cart->AddItem($item_2);

    // Add US shipping options
    $ship_1 = new GoogleFlatRateShipping("UPS Ground", 5.0);
	$restriction_1 = new GoogleShippingFilters();
	$restriction_1->SetAllowedCountryArea("CONTINENTAL_48");
	$ship_1->AddShippingRestrictions($restriction_1);

    $ship_2 = new GoogleFlatRateShipping("UPS 2nd Day", 10.0);
	$restriction_2 = new GoogleShippingFilters();
	$restriction_2->SetAllowedStateAreas(array("CA", "AZ", "CO", "WA", "OR"));
	$ship_2->AddShippingRestrictions($restriction_2);

    // Add international shipping options
    $ship_3 = new GoogleFlatRateShipping("Canada 3 Business Days", 5.0);
    $restriction_3 = new GoogleShippingFilters();
    $restriction_3->AddAllowedPostalArea("CA");
    $restriction_3->SetAllowUsPoBox("false");
    $ship_3->AddShippingRestrictions($restriction_3);

    $ship_4 = new GoogleFlatRateShipping("Europe 3 Business Days", 10.0);
    $restriction_4 = new GoogleShippingFilters();
    $restriction_4->AddAllowedPostalArea("GB", "SW*");
    $ship_4->AddShippingRestrictions($restriction_4);

    $cart->AddShipping($ship_1);
    $cart->AddShipping($ship_2);
    $cart->AddShipping($ship_3);
    $cart->AddShipping($ship_4);

    // Add US tax rules
    $tax_rule_1 = new GoogleDefaultTaxRule(0.0825);
    $tax_rule_1->SetStateAreas(array("CA", "NY"));
    $cart->AddDefaultTaxRules($tax_rule_1);

    // Add International tax rules
    $tax_rule_2 = new GoogleDefaultTaxRule(0.15);
    $tax_rule_2->AddPostalArea("GB");
    $tax_rule_2->AddPostalArea("FR");
    $tax_rule_2->AddPostalArea("DE");
    $cart->AddDefaultTaxRules($tax_rule_2);

    // Define rounding policy
    $cart->AddRoundingPolicy("HALF_UP", "PER_LINE");

    // Display XML data
    // echo "<pre>";
    // echo htmlentities($cart->GetXML());
    // echo "</pre>";

    // Display Google Checkout button
    echo $cart->CheckoutButtonCode("LARGE");
  }

  function UseCase2() {
    // Create a new shopping cart object
    $merchant_id = "";  // Your Merchant ID
    $merchant_key = "";  // Your Merchant Key
    $server_type = "sandbox";
    $currency = "USD";
    $cart = new GoogleCart($merchant_id, $merchant_key, $server_type, $currency); 

    // Add items to the cart
    $item_1 = new GoogleItem("Dry Food Pack AA1453", 
        "A pack of highly nutritious dried food for emergency", 2, 24.99);
    $item_1->SetTaxTableSelector("food");

    $item_2 = new GoogleItem("MegaSound 2GB MP3 Player", 
        "Portable MP3 player - stores 500 songs", 1, 175.49);
    $item_2->SetMerchantPrivateItemData("<color>blue</color><weight>3.2</weight>");
    $item_2->SetMerchantItemId("Item#012345");

    $cart->AddItem($item_1);
    $cart->AddItem($item_2);

    // Add shipping options
    $ship_1 = new GoogleFlatRateShipping("Ground", 15);
    $restriction_1 = new GoogleShippingFilters();
    $restriction_1->SetAllowedWorldArea("true");
    $ship_1->AddShippingRestrictions($restriction_1);

    $ship_2 = new GooglePickup("Pick Up", 5);

    $cart->AddShipping($ship_1);
    $cart->AddShipping($ship_2);

    // Add default tax rules
    $tax_rule_1 = new GoogleDefaultTaxRule(0.17);
    $tax_rule_1->AddPostalArea("GB", "SW*");
    $tax_rule_1->AddPostalArea("FR");
    $tax_rule_1->AddPostalArea("DE");

    $tax_rule_2 = new GoogleDefaultTaxRule(0.10);
    $tax_rule_2->SetWorldArea("true");

    $cart->AddDefaultTaxRules($tax_rule_1);
    $cart->AddDefaultTaxRules($tax_rule_2);

    // Add alternate tax table
    $tax_table = new GoogleAlternateTaxTable("food");

    $tax_rule_1 = new GoogleAlternateTaxRule(0.05);
    $tax_rule_1->AddPostalArea("GB");
    $tax_rule_1->AddPostalArea("FR");
    $tax_rule_1->AddPostalArea("DE");

    $tax_rule_2 = new GoogleAlternateTaxRule(0.03);
    $tax_rule_2->SetWorldArea("true");

    $tax_table->AddAlternateTaxRules($tax_rule_1);
    $tax_table->AddAlternateTaxRules($tax_rule_2);

    $cart->AddAlternateTaxTables($tax_table);

    // Add <merchant-private-data>
    $cart->SetMerchantPrivateData("<cart-id>ABC123</cart-id>");

    // Specify <edit-cart-url>
    $cart->SetEditCartUrl("http://www.example.com/edit");

    // Specify "Return to xyz" link
    $cart->SetContinueShoppingUrl("http://www.example.com/continue");

    // Request buyer's phone number
    $cart->SetRequestBuyerPhone("true");

    // Define rounding policy
    $cart->AddRoundingPolicy("CEILING", "TOTAL");

    // Display XML data
    // echo "<pre>";
    // echo htmlentities($cart->GetXML());
    // echo "</pre>";

    // Display a medium size button
    echo $cart->CheckoutButtonCode("MEDIUM");
  }

  function UseCase3() {
    //Create a new shopping cart object
    $merchant_id = "";  // Your Merchant ID
    $merchant_key = "";  // Your Merchant Key
    $server_type = "sandbox";
    $currency = "USD";
    $cart = new GoogleCart($merchant_id, $merchant_key, $server_type, $currency); 

    // Add items to the cart
    $item = new GoogleItem("MegaSound 2GB MP3 Player", 
        "Portable MP3 player - stores 500 songs", 1, 175.49);
    $item->SetMerchantPrivateItemData("<color>blue</color><weight>3.2</weight>");
    $cart->AddItem($item);

    // Add merchant calculations options
    $cart->SetMerchantCalculations(
        "https://www.example.com/merchant-calculations", // merchant-calculations-url
        "true", // merchant-calculated tax
        "true", // accept-merchant-coupons
        "true"); // accept-merchant-gift-certificates

    // Add merchant-calculated-shipping option
    $ship = new GoogleMerchantCalculatedShipping("2nd Day Air", // Shippping method
                                                 10.00); // Default, fallback price

    $restriction = new GoogleShippingFilters();
    $restriction->AddAllowedPostalArea("GB");
    $restriction->AddAllowedPostalArea("US");
    $restriction->SetAllowUsPoBox("false");
    $ship->AddShippingRestrictions($restriction);

    $address_filter = new GoogleShippingFilters();
    $address_filter->AddAllowedPostalArea("GB");
    $address_filter->AddAllowedPostalArea("US");
    $address_filter->SetAllowUsPoBox("false");
    $ship->AddAddressFilters($address_filter);
    
    $cart->AddShipping($ship);

    // Set default tax options
    $tax_rule = new GoogleDefaultTaxRule(0.15);
    $tax_rule->SetWorldArea("true");
    $cart->AddDefaultTaxRules($tax_rule);

    $cart->AddRoundingPolicy("UP", "TOTAL");

    // Display XML data
    // echo "<pre>";
    // echo htmlentities($cart->GetXML());
    // echo "</pre>";

    // Display a disabled, small button
    echo $cart->CheckoutButtonCode("SMALL", false);
  }

?>
