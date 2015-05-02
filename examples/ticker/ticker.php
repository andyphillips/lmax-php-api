<?php
// remove that space.
//
// This retrieves data for GBP/USD
//  
//
// copyright atp 2011.
// released under the lgpl vn 3. You should have a copy of the lgpl alongside this file.
// 
// No warranty or fitness for any purpose. Use at your own risk. This program is provided
// for the purpose of learning about the LMAX API. 
//


require_once("../../src/lmaxapi.php");
require_once("../../src/get_password.php");

date_default_timezone_set("UTC");

get_username_password();

// Log in to demo site
// 
// demo is https://web-api.london-demo.lmax.com/
// prod is https://api.lmaxtrader.com/ 
// 
$conn = new lmaxapi("https://web-api.london-demo.lmax.com/");

// logs in and populates a couple of fields like account id and currency.
// username, password and product type 
if (!$conn->login($username,$password,"CFD_DEMO")) {
    print "Unable to login. Incorrect username or password?";
    exit(0);
}

$conn->VERBOSE=TRUE;

// $conn->get_symbol_groups() 
// will return an array of symbol groups; FX, RATES, INDICES  
// 
// we can also get the symbols within a group
//print implode(',',$conn->get_symbols_by_group("FX"));
//
// We know we're looking for GBP however. 
// This returns an id indexed array of instrument objects
$fx_markets = $conn->search_instruments("GBP");

$myid = 0;
foreach ($fx_markets as $fx_id => $fx_cross) {
    if ($fx_cross->symbol == "GBP/USD") $myid = $fx_id;
}

if (0 == $myid) {
    print "Unable to find GBP/USD\n";
    exit(0);
}

// Note. To set up a subscription there is a two step process.
// First you need to subscribe to a type of event. 
// valid types are "order" - orderbook events, aka market data.
//                 "account" - things relating to your account.
//                 "position" - things relating to your position.
//                 "instrument" - things relating to instruments/markets. 
//
if ($conn->setup_subscription("order") === FALSE) {
    print "Unable to set up subscription to order book event stream\n";
    exit(0);
}

// now we get the order book data for this instrument.
// subscribe to the market data feed for a bunch of instruments.
// 
// This takes an array of instruments to subscribe to. 
//
$instrument_list =array( $myid );

if ($conn->subscribe_to_orderbook($instrument_list) === FALSE) {
    print "Failed to subscribe ".implode(",",$instrument_list)."\n";
    exit (0);
}

// listen to the event stream forever.
while (1) {
    // This returns an array of objects of type price.
    $result = $conn->get_events();
    if ($result !== FALSE) 
     foreach ($result as $update) {
	if ($update->type == "orderbook") {
	    // prices are represented as $price => $quantity 
	    if (($update->nbids > 0) && ($update->nasks > 0)) {
		$bid = $update->bids[0]->price;
		$ask = $update->asks[0]->price;
		$spread = $ask - $bid;
		
		print date("Ymd-H:i:s",($update->exchange_time_stamp)/1000) . "." . 
		  sprintf("%03.3dms",$update->exchange_time_stamp %1000) . 
		  "  GBP/USD bids: " . 
		  $update->bids[0]->price ." ". 
		  $update->bids[1]->price .
		  "  asks: " . $update->asks[0]->price . " ". 
		  $update->asks[1]->price . " spread: $spread\n";
	    } else {
		print "no bids and/or asks";
	    }
	}
    }
}

// todo, unsubscribe, log out. 

?>
