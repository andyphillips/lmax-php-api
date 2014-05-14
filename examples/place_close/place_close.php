<?php

// illustrates how to place a market order and then close it out. 

require_once("../../src/lmaxapi.php");
require_once("../../src/get_password.php");

function bailout($reason)
{
    print $reason;
    if (isset($foo)) {
	$foo->logout();
    }
    exit(0);
}


// instantiate, login, housekeeping. 
get_username_password();
$conn = new lmaxapi();

if ($conn->login($username,$password,$productType="CFD_DEMO") == FALSE) {
    bailout("Error logging in\n");
}

$conn->get_longpollkey();

// find the market id for the cross we're interested in. 

$fx_markets = $conn->search_instruments("GBP");

$myid = 0;
foreach ($fx_markets as $fx_id => $fx_cross) {
    if ($fx_cross->symbol == "GBP/USD") $myid = $fx_id;
}

if (0 == $myid) {
    bailout("Unable to find GBP/USD\n");
}

// place a market order, 
$result = $conn->place_order($myid,order_type::market,fill_strategy::IoC,1);

if (!$result) {
    bailout("Failed to place immediate or cancel market buy order on $myid for 1 contract\n");
} else {
    print "placed immediate or cancel  market buy order on $myid for quantity 1, order id $result\n";
}

$market_order_id = $result;

// close out the market order 

$result = $conn->close_order($myid,$market_order_id,-1);

if (!$result) {
    bailout( "Failed to close out market buy order on $myid for 1 contract\n");
} else {
    print "Closed market order on $myid for quantity 1, order id $result\n";
}

// logout
$result = $conn->logout();

if ($result) {
    print "logged out \n";
} else {
    print "Failed to log out\n";
}

?>
