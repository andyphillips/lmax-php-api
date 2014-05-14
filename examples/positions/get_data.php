<?php 
require_once("../../src/lmaxapi.php");


function print_events_json($foo,$instruments)
{
    $data = array();

    $result = $foo->get_events();
    
    if ($result === FALSE) {
	error("Failed to get positions");
    }
      
    $data["timestamp"]=time();
    
    foreach ($result as $key => $value) {
	if (array_key_exists($value->instrument_id,$instruments))
	{
	    $symbol = $instruments[$value->instrument_id]->symbol;
	} else {
	    $symbol = $value->instrument_id;
	}

	$data[] = array ($value->account_id,$symbol,$value->open_quantity);
    }

//    print_r($instruments);
//    foreach ($instruments as $key => $value) print $key." ".$value->symbol."\n";
      
    print json_encode($data);
}

function error($message)
{
	print "{\"error\":\"".$message."\"}";
	return;
}

function get_data_json($username,$password,$live)
{
    
    if ($live) {
	$foo=new lmaxapi("https://api.lmaxtrader.com/");
	$product="CFD_LIVE";
    }
    else
    { //"http://cxaweb02.gs2.tradefair:9090/"
	$foo = new lmaxapi();
	$product="CFD_DEMO";
    }
  
// turn on to get message stream debugging    
//    $foo->VERBOSE=TRUE;
//    $foo->DEBUG=TRUE;
    
    // logs in and provides account id and currency. 
    if ($foo->login($username,$password,$productType="CFD_DEMO")== FALSE) {
	error("unable to login - username or password incorrect");
	exit;
    }
    
    // get the longpollkey. Probably should hide this as its housekeeping.
    $result = $foo->get_longpollkey();
    
    
// gets the list of symbols in a group 
// + is the magic token to get everything
    $instruments = $foo->search_instruments("+");
    
    if ($instruments === FALSE) {
	error("Failed to retrieve instruments");
	exit;
    }
    
    if ($foo->setup_subscription("position") === FALSE) {
	error("unable to set up subscription to position");
	exit;
    } 
    
    print_events_json($foo,$instruments);
    
    $result = $foo->logout();
    
    if ($result) {
	//    print "logged out \n";
    } else {
	error("Failed to log out");
    }
}

#echo $_POST['live'];

if (isset($_POST['live'])) {
    $live=true;
} else { 
    $live=false; 
}

if (isset($_POST['username']) && isset($_POST['password']))
{
    get_data_json($_POST['username'], $_POST['password'],$live );
    exit;
} else {
    error("Must supply username and password");
}

?>