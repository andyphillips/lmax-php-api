<?php

// These tests need a harness and isolation.

require_once("../src/lmaxapi.php");
require_once("../src/get_password.php");

date_default_timezone_set("GMT");

class order_state
{
    const pending = 0;
    const working  = 1;  // for limit
    const rejected  = 2;
    const matched = 3;
    const closed = 4;
}

class my_order
{
    public $order_id;
    public $state;
    
    function __construct ($order_id, $state) 
    {
	$this->order_id = $order_id;
	$this->state = $state;
    }
}

$myorders = array();

function print_events($foo)
{
    global $myorders;
    
    $result = $foo->get_events();

    foreach ($result as $update) {
	print "event of type $update->type\n";
	switch ($update->type) {
	 case "orderbook":
	    // prices are represented as an array of $price and $quantity object.
	    // with top of book being the [0] th item. 
	    if (($update->nbids > 0) && ($update->nasks >0)){
		$best_ask = $update->asks[0]->price;
		$best_bid = $update->bids[0]->price;
		$spread = $best_ask - $best_bid;
		print date("Ymd-h:i:s",($update->exchange_time_stamp)/1000) . "." . sprintf("%03.3dms",$update->exchange_time_stamp %1000) . 
		  "  $update->instrument_id bids: " . $update->bids[0]->price. " ". $update->bids[1]->price .
		  "  asks: " . $update->asks[0]->price. " " . $update->asks[1]->price . " spread: $spread\n";
	    } else {
		print "no bids and/or asks\n";
	    }
	    break;
	 case "account":
	    print_r($update);
	    print "\n";
	    break;
	 case "order":
	    print_r($update);
	    print "\n";
	    break;
	 case "position":
	    print_r($update);
	    print "\n";
	    break;
	 case "rejected_instruction":
	    print_r($update);
	    foreach ($myorders as $myorder){
		if ($myorder->order_id == $update->instruction_id) 
		{
		    echo "$myorder->order_id rejected because $update->reason\n";
		    $myorder->state = order_state::rejected;
		}
	    }
	    print "\n";
	    break;
	 default:
	}
    }
}

function retrieve_market_data($conn)
{
    $quit = 0;
    
    while (!$quit) {
	// This returns an array of objects of type price.
	$result = $conn->get_events();
	if ($result === FALSE) return false;
	print_r($result);
	
	foreach ($result as $update) {
	    if ($update->type == "orderbook") {
		// prices are represented as $price => $quantity 
		if (($update->nbids > 0) && ($update->nasks >0)){
		    $bid = $update->bids[0]->price;
		    $ask = $update->asks[0]->price;
		    $spread = $ask - $bid;
		    
		    print "market data: ".date("Ymd-H:i:s",($update->exchange_time_stamp)/1000) . "." . 
		      sprintf("%03.3dms",$update->exchange_time_stamp %1000) . 
		      "  GBP/USD bids: " . 
		      $update->bids[0]->price ." ". 
		      $update->bids[1]->price .
		      "  asks: " . $update->asks[0]->price . " ". 
		      $update->asks[1]->price . " spread: $spread\n";
		    $quit = 1;
		} else {
		    print "no/single sided market data\n";
		    return false;
		}
	    }
	}
    }
    return $bid; 
}
    
get_username_password();

$foo = new lmaxapi();

// set to true to get more info about the JSON and cookies being sent/received 
$foo->DEBUG=FALSE;
$foo->VERBOSE=FALSE;

print "\n---Login---\n";

// logs in and provides account id and currency. 
if ($foo->login($username,$password,$productType="CFD_DEMO") == FALSE) {
    print "Error logging in\n";
    exit;
}

print "Account id = ".$foo->account_id."\n";
print "Account Currency = ".$foo->account_currency."\n";

print "\n---get_app_param---\n";

// retrieves parameters about this exchange, plus symbol lists by group.
// Should hide this... 
$result = $foo->get_app_param("demoRegistrationEnabled");
print "demoRegistrationEnabled ".$result ."\n";

print "\n---get_symbol_groups---\n";

// gets the symbol groups - FX, INDICES etc. 
$result = $foo->get_symbol_groups();
print "Instrument Groups: ".implode(" ",$result)."\n";

print "\n---get_symbols_by_group---\n";

// gets the list of symbols in a group
$result = $foo->get_symbols_by_group("INDICES");
print "Indices: ".implode (" ",$result)."\n";

print "\n---get_longpollkey---\n";

// get the longpollkey. Probably should hide this as its housekeeping.
$result = $foo->get_longpollkey();
print "longpollkey: ".$result."\n";

print "\n---searching for GBP instruments---\n";

// see e.g. $foo->get_symbols_by_group for appropriate search terms.
print "search terms for asset class FX: ".implode(',',$foo->get_symbols_by_group("FX"))."\n";

// get a list of instruments for this group/search term
print "GBP groups\n";
$result = $foo->search_instruments("GBP");

//print_r($result);
$instrument_list =array( );

foreach ($result as $id => $instrument){
    print $id.": ".$instrument->symbol." ".$instrument->contract_unit_of_measure."\n";
    if ($instrument->symbol == "GBP/USD") array_push($instrument_list, $id);
    if ($instrument->symbol == "EUR/GBP") {
	array_push($instrument_list, $id);
	print "instrument data for EUR/GBP";
	print_r($instrument);
    }
}

//
// Note. To set up a subscription there is a two step process.
// First you need to subscribe to a type of event. 
// valid types are "order" - orderbook events, aka market data.
//                 "account" - things relating to your account.
//                 "position" - things relating to your position.
// 
print "\n---setting up a subscription---\n";

if ($foo->setup_subscription("account") === FALSE) {
    print "unable to set up subscription to account\n";
} else {
    print "setup subscription to account ok\n";
}

if ($foo->setup_subscription("order") === FALSE) {
    print "unable to set up subscription to order book\n";
} else {
    print "setup subscription to order book ok\n";
}

// subscribe to the market data feed for the instruments listed above.
//
$result = $foo->subscribe_to_orderbook($instrument_list);

//
if ($result) print "Subscribed to ".implode(",",$instrument_list)." ok\n"; 
  else print "Failed to subscribe ".implode(",",$instrument_list)."\n";

print_events($foo);

print "\n---placing order---\n";

//
// Place market order
// 
// 
$result = $foo->place_order($instrument_list[0],order_type::market,fill_strategy::IoC,1);

if (!$result) {
    print "Failed to place immediate or cancel market buy order on $instrument_list[0] for 1 contract\n";
    exit(0);
} else {
    print "placed immediate or cancel  market buy order for quantity 1, order id $result\n";
}

$myorders["place_market"] = new my_order($result, order_state::pending);

// and process them. 
print_events($foo);

//
// close out market order  
// 

// note. we don't have processing for closed order state yet. 
if ($myorders["place_market"]->state != order_state::rejected) {
    print "\n---closing market order---\n";
    $result = $foo->close_order($instrument_list[0],$myorders["place_market"]->order_id,-1);

    if (!$result) {
	print "Failed to close out market buy order on $instrument_list[0] for 1 contract\n";
	exit(0);
    } else {
	print "Closed market order for quantity 1, order id $result\n";
    }
} else {
    print "skipping cancelling of rejected order\n";
}

// 
// get an orderbook update (marketdata) for orderbook gbp/usd so we know where the bid/ask is to place a limit order
//
$bid = retrieve_market_data($foo);

if ($bid) {
    // 
    // place a GTC limit order. 
    // 
    print "\n---placing GTC limit order on gbp/usd 10 pips away from price---\n";
    
    $result = $foo->place_order($instrument_list[0],order_type::limit,fill_strategy::GTC,1,($bid-0.00010));
    
    if (!$result) {
	print "Failed to place GTC limit buy order on gbp/usd for 1 contract\n";
	exit(0);
    } else {
	print "placed GTC limit buy order for quantity 1, order id $result\n";
    }
    
    $limit_order_id = $result;
    
    print_events($foo);
    
    print "\n---cancelling limit order---\n";
    
    $my_cancel_id="1234";
    
    $result = $foo->cancel_order($instrument_list[0],$limit_order_id,$my_cancel_id);
    
    if (!$result) {
	print "Failed to cancel market stop $limit_order_id on $instrument_list[0] \n";
	exit(0);
    } else {
	print "Cancelled market stop on $instrument_list[0] id $limit_order_id cancel id $result\n";
    }
    
    print_events($foo);
    
} else {
    print "unable to place or cancel limit orders due to no market data\n";
}
print "\n---logout---\n";

$result = $foo->logout();

if ($result) {
    print "logged out \n";
} else {
    print "Failed to log out\n";
}

?>
	
