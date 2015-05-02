<?php
// Remove that space. 
//
// Classes to connect to the LMAX API and hopefully do some useful 
// things with it. 
//
// There are a couple of helper classes - instrument and price 
// The main class is lmaxapi
// 
//
// copyright atp 2011, 2012
// released under the lgpl vn 3. You should have a copy of the lgpl alongside this file.
// 
// No warranty or fitness for any purpose. Use at your own risk. This program is provided
// for the purpose of learning about the LMAX API and my own personal monitoring projects. 
// 
// You take it on your own head if you use this for trading. This API library was not intended for that.  
// I'd advise you to look at the "proper" LMAX .NET and Java APIs for supported trading apis. 
// They're tested and QA'ed. This is not. Here be dragons and lions and tigers. 
//
// 
// enums of order types and fill strategy. 
class order_type
{
    const market= 0; 
    const limit = 1;
    const market_stop  = 2;
}

class fill_strategy
{
    const fill_or_kill =  0;
    const immediate_or_cancel = 1;
    const good_for_day = 2;
    const good_til_cancel = 3;
    // aliases
    const FoK = 0;
    const IoC = 1;
    const GFD = 2;
    const GTC = 3;
}

// tradable instrument. 
class instrument
{
    public $id;
    public $name;
    public $start_time;
    public $trading_hours = array();
    public $margin;
    public $currency;
    public $unit_price;
    public $minimum_order_quantity;
    public $order_quantity_increment;
    public $minimum_price;
    public $maximum_price;
    public $trusted_spread;
    public $price_increment;
    public $stop_buffer;
    public $asset_class;
    public $underlying_isin;
    public $symbol;
    public $maximum_position_threshold;
    public $aggressive_commission_rate;
    public $passive_commission_rate;
    public $minimum_commission;
    public $funding_rate_percentage;
    public $funding_premium_percentage;
    public $funding_base_rate;
    public $daily_interest_rate_basis;
    public $contract_unit_of_measure;
    public $contract_size;
    public $trading_days;
    public $retail_volatility_band_percentage;
    
    // take a json response object and parse into fields above
    function parse_input($data) 
    {
       // want as input XXX where XXX is res->body[0]->instruments[0]->instrument[n]
       foreach ($data as $tag => $value) {
	   switch ($tag){
	    case 'id': $this->id = $value[0];
	       break;
	    case 'name': $this->name = $value[0];
	       break;
	    case 'startTime': $this->start_time = $value[0];
	       break;
	    case 'tradingHours':
	       $this->trading_hours['opening_offset'] = $value[0]->openingOffset[0];
	       $this->trading_hours['closing_offset'] = $value[0]->closingOffset[0];
	       $this->trading_hours['timezone'] = $value[0]->timezone[0];
	       break;
	    case 'margin': $this->margin = $value[0];
	       break;
	    case 'currency': $this->currency = $value[0];
	       break;
	    case 'unitPrice': $this->unit_price = $value[0];
	       break;
	    case 'minimumOrderQuantity': $this->minimum_order_quantity = $value[0];
	       break;
	    case 'orderQuantityIncrement': $this->order_quantity_increment = $value[0];
	       break;
	    case 'minimumPrice': $this->minimum_price = $value[0];
	       break;
	    case 'maximumPrice': $this->maximum_price = $value[0];
	       break;
	    case 'trustedSpread': $this->trusted_spread = $value[0];
	       break;
	    case 'priceIncrement': $this->price_increment = $value[0];
	       break;
	    case 'stopBuffer': $this->stop_buffer = $value[0];
	       break;
	    case 'assetClass': $this->asset_class = $value[0];
	       break;
	    case 'underlyingIsin': $this->underlying_isin = $value[0];
	       break;
	    case 'symbol': $this->symbol = $value[0];
	       break;
	    case 'maximumPositionThreshold': $this->maximum_position_threshold = $value[0];
	       break;
	    case 'aggressiveCommissionRate': $this->aggressive_commission_rate = $value[0];
	       break;
	    case 'passiveCommissionRate': $this->passive_commission_rate = $value[0];
	       break;
	    case 'minimumCommission': $this->minimum_commission = $value[0];
	       break;
	    case 'fundingRatePercentage': $this->funding_rate_percentage = $value[0];
	       break;
	    case 'fundingPremiumPercentage': $this->funding_premium_percentage = $value[0];
	       break;
	    case 'fundingBaseRate': $this->funding_base_rate = $value[0];
	       break;
	    case 'dailyInterestRateBasis': $this->daily_interest_rate_basis = $value[0];
	       break;
	    case 'contractUnitOfMeasure': $this->contract_unit_of_measure = $value[0];
	       break;
	    case 'contractSize': $this->contract_size = $value[0];
	       break;
	    case 'tradingDays': $this->trading_days = $value[0]->tradingDay;
	       break;
	    case 'retailVolatilityBandPercentage': $this->retail_volatility_band_percentage = $value[0];
	       break;
	    default:
	       // ignore
	   } // switch
       } // foreach $data as $tag=>$value
    } // parse json input
    
    function __construct($data=FALSE)
    {
	if ($data) $this->parse_input($data);
	return TRUE;
    }
} // instrument class

// helper class for positions

class position 
{
    public $account_id;
    public $instrument_id;
    public $valuation;
    public $short_unfilled_cost;
    public $long_unfilled_cost;
    public $open_quantity;
    public $cumulative_cost;
    public $open_cost;
    public $type;
    
    function parse_input($position) 
    {
	$this->type = "position";
	
	foreach ($position as $tag => $value)
	{
	    switch ($tag) {
	     case 'accountId': $this->account_id = $value[0];
		break;
	     case 'instrumentId': $this->instrument_id = $value[0];
		break;
	     case 'valuation': $this->valuation = $value[0];
		break;
	     case 'shortUnfilledCost': $this->short_unfilled_cost = $value[0];
		break;
	     case 'longUnfilledCost': $this->long_unfilled_cost = $value[0];
		break;
	     case 'openQuantity': $this->open_quantity = $value[0];
		break;
	     case 'cumulativeCost': $this->cumulative_cost = $value[0];
		break;
	     case 'openCost': $this->open_cost = $value[0];
		break;
	     default:
		//ignore
	    } // switch 
	} // foreach $position as $tag => $value
    }// parse json like object. 

    function __construct($data=FALSE)
    {
	if ($data) $this->parse_input($data);
	return TRUE;
    }
    
}

// helper class for rejected instructions
class rejected_instruction 
{
    public $account_id;
    public $instrument_id;
    public $instruction_id;
    public $reason;
    
    function parse_input($reject) 
    {
	$this->type = "rejected_instruction";
	
	foreach ($reject as $tag => $value)
	{
	    switch ($tag) {
	     case 'accountId': $this->account_id = $value[0];
		break;
	     case 'instrumentId': $this->instrument_id = $value[0];
		break;
	     case 'instructionId': $this->instruction_id = $value[0];
		break;
	     case 'reason': $this->reason = $value[0];
		break;
	     default:
		//ignore
	    } // switch 
	} // foreach $position as $tag => $value
    }// parse json like object. 

    function __construct($data=FALSE)
    {
	if ($data) $this->parse_input($data);
	return TRUE;
    }
    
}


// helper class for orderbook/marketdata update - and anywhere we need to store price/quantity pairs. 
class price_and_quantity
{
    public $price;
    public $quantity;
    public $type;
    
    function __construct($price,$quantity)
    {
	$this->price = $price;
	$this->quantity = $quantity;
	$this->type = "price_and_quantity";
    }
}

class execution
{
   public $execution_id;
    // a price_and_quantity 
   public $value;
   public $type;
   
    // This is awkward. We get "execution" tag for an order placement and orderCancelled for cancellations.
    // Ideally one has the same member variables in identically named classes. A new top level "cancellation"
    // class would have been better. 
   function __construct($data)
    {
//	print_r($data);
	$execution_id = $data->executionId;
	if (array_key_exists('execution',$data)) {
	    $this->value = new price_and_quantity($data->execution[0]->price[0],$data->execution[0]->quantity[0]);
	} else {
	    $this->value = new price_and_quantity(0,$data->orderCancelled[0]->quantity[0]);
	}
	$this->type = "execution";
    }
    
}

// helper class for active orders

class active_order 
{
    public $time_in_force;
    public $instruction_id;
    public $original_instruction_id;
    public $order_id;
    public $account_id;
    public $instrument_id;
    public $price;
    public $quantity;
    public $matched_quantity;
    public $cancelled_quantity;
    public $timestamp;
    public $order_type;
    public $open_quantity;
    public $open_cost;
    public $cumulative_cost;
    public $commission;
    public $stop_reference_price;
    public $stop_loss_offset;
    public $stop_profit_offset;
    public $executions = array();
    
    function parse_input($order)
    {
	$this->type = "order";

	foreach ($order as $tag => $value)
	{
	    switch ($tag){
	     case 'timeInForce': $this->time_in_force = $value[0];
		break;
	     case 'instructionId': $this->instruction_id = $value[0];
		break;
	     case 'originalInstructionId': $this->original_instruction_id = $value[0];
		break;
	     case 'orderId': $this->order_id = $value[0];
		break;
	     case 'accountID': $this->account_id = $value[0];
		break;
	     case 'instrumentId': $this->instrument_id = $value[0];
		break;
	     case 'price': $this->price = $value[0];
		break;
	     case 'quantity': $this->quantity = $value[0];
		break;
	     case 'matchedQuantity': $this->matched_quantity = $value[0];
		break;
	     case 'cancelledQuantity': $this->cancelled_quantity = $value[0];
		break;
	     case 'timestamp': $this->timestamp = $value[0];
		break;
	     case 'orderType': $this->order_type = $value[0];
		break;
	     case 'openQuantity': $this->open_quantity = $value[0];
		break;
	     case 'openCost': $this->open_cost = $value[0];
		break;
	     case 'cumulativeCost': $this->cumulative_cost = $value[0];
		break;
	     case 'commission': $this->commission = $value[0];
		break;
	     case 'stopReferencePrice': $this->stop_reference_price = $value[0];
		break;
	     case 'stopLossOffset': $this->stop_loss_offset = $value[0];
		break;
	     case 'stopProfitOffset': $this->stop_profit_offset = $value[0];
		break;
	     case 'executions': 
		// an array of execution objects; 
		foreach ($value as $item){
		    $this->executions[] = new execution($item);
		}
	    default:
	       // ignore
	   } // switch
       } // foreach $data as $tag=>$value
    } // parse order update 

    function __construct($order=FALSE)
    {
	if ($order) $this->parse_input($order);
	return TRUE;
    }

}


// This class will parse and hold a single instruments OB2 format data
class orderbook_update
{
    public $type;
    public $instrument_id;
    public $exchange_time_stamp;
    // arrays of quantity indexed by price. i.e. 
    //  bid['price'] = quantity
    //  ask['1.4128'] = 10
    //
    public $bids = array();
    public $asks = array();
    public $last_market_close_price;
    public $last_market_close_timestamp;
    public $daily_highest_traded_price;
    public $daily_lowest_traded_price;
    public $valuation_bid_price;
    public $valuation_ask_price;
    public $last_traded_price;
    public $nbids;
    public $nasks;
    
    function parse_ob2($ob2) {
	
	$this->type = "orderbook";
	
	$data = explode('|',$ob2);

	if (count($data) != 10) return FALSE;
	
	$this->instrument_id = $data[0];
	$this->exchange_time_stamp = hexdec($data[1]);
	
	$tmp = explode(';',$data[4]);
	
	$this->last_market_close_price = $tmp[0];
	$this->last_market_close_timestamp = hexdec($tmp[1]);

	$this->daily_highest_traded_price = $data[5];
	$this->daily_lowest_traded_price = $data[6];
	$this->valuation_bid_price = $data[7];
	$this->valuation_ask_price = $data[8];
	$this->last_traded_price = $data[9];
	
	// bids. If the instrument is closed or has no data we'll get an empty string.
	$this->nbids=0;
	if ($data[2] != "" ) {
	    $tmp = explode(';',$data[2]);
	
	    foreach ($tmp as $update) {
		$atom = explode('@',$update);
		$this->bids[]=new price_and_quantity($atom[1],$atom[0]);
		$this->nbids++;
	    }
	}
	// asks. ditto. 
	$this->nasks=0;
	if ($data[3] != "" ) {
	    $tmp = explode(';',$data[3]);
	    
	    foreach ($tmp as $update) {
		$atom = explode('@',$update);
		$this->asks[] = new price_and_quantity($atom[1],$atom[0]);
		$this->nasks++;
	    }
	}
    }
	
    function __construct($data=FALSE)
    {
	if ($data) $this->parse_ob2($data);
	return TRUE;
    }    
}

// Wallet class for use by the account_update class. 
class wallet 
{
    public $type;
    public $currency;
    public $balance;
    
    function __construct($data=FALSE)
    {
	if ($data) {
	    $this->currency = $data->currency[0];
	    $this->balance = $data->balance[0];
	}
	$this->type = "wallet";
    }
}

// account information
class account_update
{
    public $type;
    public $account_id;
    public $balance;
    public $available_funds;
    public $available_to_withdraw;
    public $unrealised_profit_and_loss;
    public $margin;
    public $active;
    public $wallets = array();

    function parse_account($data)
    {
	$this->account_id = $data[0]->accountId[0];
	$this->balance = $data[0]->balance[0];
	$this->available_funds = $data[0]->availableFunds[0];
	$this->available_to_withdraw = $data[0]->availableToWithdraw[0];
	$this->unrealised_profit_and_loss = $data[0]->unrealisedProfitAndLoss[0];
	$this->margin = $data[0]->margin[0];
	$this->active = $data[0]->active[0];

	foreach ($data[0]->wallets[0]->wallet as $wallet){
	    $this->wallets[] = new wallet($wallet);
	}
	
	$this->type = "account";
    }
    
    function __construct($data=FALSE)
    {
	if ($data) $this->parse_account($data);
	return TRUE;
    }    
}


// main class 
class lmaxapi 
{
    protected $version = 1.4;
    protected $my_socket;	// curl session handle. 
    protected $username;
    protected $password;
    protected $url;		// base url web-api.london-demo.lmax.com or api.lmaxtrader.com
    protected $connected=FALSE;    // do we have a live session. 
    protected $session_cookies;    // our session cookie &lb cookie
    protected $longpollkey=FALSE;  // ?
    protected $sequence_no=FALSE;  // data stream sequence number
    
    protected $app_params=array();
    protected $cache=array();      // this is a cache for the synchronous API
    protected $http_std_headers = array();
    
    // Set me to true for some debugging. 
    public $DEBUG=FALSE;
    // set me to true for verbose error messages. 
    public $VERBOSE=FALSE;

    public $symbols=array();  // list of instrument search terms by asset class
      
    public $account_id;           // fixme need to decide if using get/set or public vars - these two are in the wrong place. 
    public $account_currency;
    
    
    
    //
    // This is where we specify the site we're connecting to. 
    // We default to the test site. 
    // 
    function __construct($url="https://web-api.london-demo.lmax.com")
    {
	if (!isset($url)) {
	    throw new Exception ("missing url");
	} else {
	    $this->url = $url;
	}
	$this->set_std_headers();
    }

    function __destruct()
    {
	$this->close();
    }

    function close()
    {
	if ($this->connected) curl_close($this->my_socket);
	$this->connected = 0;
    }
    
    //
    // Set up the standard http headers we use
    // called from the constructor above. 
    //
    function set_std_headers()
    {
	
	$this->http_std_headers[] = "Host: web-api.london-demo.lmax.com";
	$this->http_std_headers[] = "User-Agent: LMAX PHP client $this->version";
//	$this->http_std_headers[] = "User-Agent: Mozilla/5.0 (Windows NT 6.1; WOW64; rv:7.0.1) Gecko/20100101 Firefox/7.0.12011-10-16 20:23:00";
	$this->http_std_headers[] = "Content-Type: application/json; charset=utf-8";
	$this->http_std_headers[] = "Accept: application/json";
	$this->http_std_headers[] = "Keep-Alive:   115";
	$this->http_std_headers[] = "Connection: keep-alive";
	$this->http_std_headers[] = "Accept-Language: en-gb,en;q=0.5";
	$this->http_std_headers[] = "Accept-Charset: ISO-8859-1,utf-8;q=0.7,*;q=0.7";
    }
    //
    // This just sets some defaults and calls curl_init. 
    // 
    // Fixme: - some connection handling needs to be done here. 
    //          The ->connected flag was intended to handle 
    //          disconnects/reconnects. 
    // 
    function connect ()
    {
	$http_header = array( "Content-Type: application/json; charset=utf-8" );
	
	$defaults = array(  CURLOPT_RETURNTRANSFER => true,         // return web page
		            CURLOPT_HEADER         => false,        // don't return headers
	                    CURLOPT_FOLLOWLOCATION => true,         // follow redirects
		            CURLOPT_ENCODING       => "",           // handle all encodings
			    CURLOPT_AUTOREFERER    => true,         // set referer on redirect
			    CURLOPT_CONNECTTIMEOUT => 120,          // timeout on connect
			    CURLOPT_TIMEOUT        => 120,          // timeout on response
			    CURLOPT_MAXREDIRS      => 10,           // stop after 10 redirects
			    CURLOPT_SSL_VERIFYHOST => 1,            // do verify ssl
			    CURLOPT_SSL_VERIFYPEER => false,        //
			    CURLOPT_HTTPGET        => false,        // reset to get
			    CURLOPT_HTTPHEADER     => $http_header,
			    CURLOPT_VERBOSE        => 0);
			  
	if (!$this->connected) {
	    $this->my_socket = curl_init();
	    curl_setopt_array($this->my_socket, $defaults);
	    $this->connected = TRUE;
	}
    }
    //
    // used for POST http requests. 
    //
    // I suspect that a different httpclient class may be needed rather than curl. 
    // 
    function post_request($path,$data)
    {	
	// set up the request header
	$http_header = $this->http_std_headers;
		
	if ($this->longpollkey !== FALSE) $http_header[] = "longPollKey: ".$this->longpollkey;
	if ($this->sequence_no !== FALSE) $http_header[] = "lastReceivedMessageSequence: ".$this->sequence_no;
	
	// It would be nice to pass objects around and encode them here. 
	// Sadly LMAX doesn't accept valid JSON yet. RT 24965 
	// So we have to construct the json strings manually. 
	//$postdata = json_encode(array("req"=>$data));

	$post_opts = array( CURLOPT_POST           => true,           
		            CURLOPT_HEADER         => false,        // do return headers
			    CURLOPT_POSTFIELDS     => $data,
			    CURLOPT_HTTPHEADER     => $http_header);
	  
	curl_setopt_array($this->my_socket,$post_opts);
	curl_setopt($this->my_socket, CURLOPT_URL, $this->url.$path);

	if ($this->DEBUG) {
	    print "POST URL: ".$this->url.$path."\n";
	    print "REQUEST: $data \n";
	    print "COOKIES: $this->session_cookies\n";
	}

	curl_setopt($this->my_socket, CURLOPT_COOKIESESSION, FALSE);
	curl_setopt($this->my_socket, CURLOPT_COOKIE, $this->session_cookies);
	
	$result = curl_exec($this->my_socket);

	// fixme catch 400 bad requests
	// 
	if ($this->DEBUG) print "RESULT: $result \n";
	
	// Fixme: This needs tidying up properly. 
	//
	if (curl_errno($this->my_socket)){
	    echo "error - ". curl_error($this->my_socket);
	    return false;
	}
		
	return json_decode($result);
	
    }
    //
    // A special version of the post request used for the login only. We parse cookies 
    // from the headers as there have been problems trying to do it from the cookie file. 
    // 
    function post_login($path,$data)
    {	
	// set up the request header
	$http_header = $this->http_std_headers;
		
	if ($this->longpollkey !== FALSE) $http_header[] = "longPollKey: ".$this->longpollkey;
	if ($this->sequence_no !== FALSE) $http_header[] = "lastReceivedMessageSequence: ".$this->sequence_no;
	
	// It would be nice to pass objects around and encode them here. 
	// Sadly LMAX doesn't accept valid JSON yet. RT 24965 
	// So we have to construct the json strings manually. 
	//$postdata = json_encode(array("req"=>$data));

	$post_opts = array( CURLOPT_POST           => true,           
		            CURLOPT_HEADER         => true,        // do return headers
			    CURLOPT_POSTFIELDS     => $data,
			    CURLOPT_HTTPHEADER     => $http_header);
	  
	curl_setopt_array($this->my_socket,$post_opts);
	curl_setopt($this->my_socket, CURLOPT_URL, $this->url.$path);

	if ($this->DEBUG) {
	    print "POST URL: ".$this->url.$path."\n";
	    print "REQUEST: $data \n";
	    print "COOKIES: $this->session_cookies\n";
	}
	//
	// we do this at login time only
	//
	curl_setopt($this->my_socket, CURLOPT_COOKIESESSION, TRUE);
	
	$result = curl_exec($this->my_socket);

	// fixme catch 400 bad requests
	// 
	if ($this->DEBUG) print "RESULT: $result \n";
	
	// Fixme: This needs tidying up properly. 
	//
	if (curl_errno($this->my_socket)){
	    echo "error - ". curl_error($this->my_socket);
	}
	
	$cookiestr = "";
	
	// we convert the string into an array. 
	$bits = explode("\n",$result);
	
	// extract the cookie strings making use of the fact that they're all space separated. 
	foreach ($bits as $line) {
	    if (preg_match('/^Set-Cookie: /i',$line)) {
		$fragments = explode(' ',$line);
		$cookiestr .= $fragments[1] . " ";
	    }
	}

	$this->session_cookies = $cookiestr;
	// This relies on the fact that the json string for login is returned on a single line - the last
	// in the payload. 
	return json_decode(end($bits));
    }
    
    //
    // used for all http GET requests
    //
    function get_request($path)
    {
	$get_opts = array( CURLOPT_HTTPGET => true,
		           CURLOPT_HEADER  => false,        // do not return headers
			   CURLOPT_POST    => false);
	
	curl_setopt_array($this->my_socket,$get_opts);
	curl_setopt($this->my_socket, CURLOPT_URL, $this->url.$path);

	if (!empty($this->session_cookies)) {
	    curl_setopt($this->my_socket, CURLOPT_COOKIESESSION, FALSE);
	    curl_setopt($this->my_socket, CURLOPT_COOKIE, $this->session_cookies);
	}
	
	if ($this->DEBUG) {
	    print "GET URL: ".$this->url.$path."\n";
	    print "COOKIES: $this->session_cookies\n";
	}
	
	$result = curl_exec($this->my_socket);
	
	// fixme catch 400 bad requests
	if ($this->DEBUG) print "RESULT: $result \n";

	if (curl_errno($this->my_socket)){
	    echo "error - ". curl_error($this->my_socket);
	}
	
	return json_decode($result);
	
    }
    //
    // LOGIN and friends.
    //
    function check_ok($data)
    {
	if ($data == "") return False;
 
	
	//RESULT: {"res":[{"$":"","header":[{"$":"","status":["OK"]}],"body":[""]}]} 
	
	if ($data->res[0]->header[0]->status[0] == "OK")
	  return True;
	else 
	  return False;
	
    }
    //
    // Login to the api 
    // product type could be CFD_LIVE or CFD_DEMO
    // Fixme: error handling here is inconsistent with the rest of the class
    //        
    function login($username, $password, $productType = "CFD_LIVE") 
    {
	if (!isset($username)) {
	    if ($this->VERBOSE) print "LMAXAPI: no username?\n";
	    return FALSE;
	} else {
	    $this->username = $username;
	}
	
	if (!isset($password)) {
	    if ($this->VERBOSE) print "LMAXAPI: no password?\n";
	    return FALSE;
	} else {
	    $this->password = $password;
	}

	if (!$this->connected) $this->connect();

	$data = array("req" => array(array ( "username" => $username,
					     "password" => $password,
					     "productType" => $productType)));
	
	// This one actually is a valid JSON string
	$logindata = json_encode($data);
	
	$result = $this->post_login("/public/security/login",$logindata);
	
	//app error check 
	if (!$this->check_ok($result)){
	    return False;
	}

	$this->extract_login_data($result);

	// retrieve and parse useful data. 
	if (!$this->get_app_params()) {
	    return False;
	}
	
	return True;
    }
    
    // terminate the current session. 
    function logout()
    {
	
	// no real parameters - and hard to construct with json_encode
	$logoutdata = "{\"req\":[{}]}";
	
	$result = $this->post_request("/public/security/logout",$logoutdata);
	
	//app error check 
	if ($this->check_ok($result) === False){
	    return False;
	}
	return True;
	
    }
    //
    // 
    // 
    function extract_login_data($data)
    {
	$this->account_id = $data->res[0]->body[0]->accountId[0];
	$this->account_currency = $data->res[0]->body[0]->currency[0];
    }
    //
    // get application parameters
    //  This call is of use mainly to get the symbols and symbol groups (searchOracles)
    //  which we can use to retrieve the instrument data objects. 
    //
    function get_app_params()
    {
	if (empty($this->app_params)){  
	    $result=$this->get_request("/secure/getApplicationParameters"); // note we can now add a language 
//	    $result=$this->get_request("/public/security/getApplicationParameters?language=en_GB"); 

	    // app error check 
	    if (!$this->check_ok($result)){
		return False;
	    }
	    
	    $tmp = $result->res[0]->body[0];
	     
	    // This is used by the web client to bootstrap the instrument names
	    // its value to other clients is probably limited. 
	    foreach ($tmp as $tag => $value) {
		if ($tag != "searchOracles") {
		    $this->app_params[$tag] = $value;
		} else {
		    foreach ($value[0]->searchOracle as $market_type){
			$group = $market_type->group[0];
			$term  = $market_type->term;
			$this->symbols[$group] = $term;
		    } // run through each searchOracle
		} // is this a searchOracle
		
	    } // foreach
	    
	} // if ! empty
	
	return TRUE;
    }
    
    //
    // Accessor function for internal data. e.g. demo registration 
    //
    // I could override _get and look in the app_params array. 
    // But I don't think this is going to be used heavily
    //
    function get_app_param($param) 
    {
	return $this->app_params[$param];
    }
    //
    //These two run off the search oracle above 
    // retrieve a list of e.g. "FX" instruments.
    // So, this will give us all the things we can search on for a particular 
    // symbol group. 
    //
    function get_symbols_by_group($group)
    {
	if (empty($this->symbols)) {
	    if ($this->get_app_param("$") == False) return False;
	}
	
	return ($this->symbols[$group]);
    }
    // 
    // returns a list of the top level groups e.g. "FX", "RATES" etc..
    //  - These are retrieved at logon time by the get application parameters call. 
    //
    function get_symbol_groups()
    {
	if (empty($this->symbols)) {
	    if ($this->get_app_param("$") == False) return False;
	}
	
	return (array_keys($this->symbols));
    }

    // 
    // An internal function. Gets a "magic" number which is needed in the header. 
    // You can call it, but there's not much you can do with the number. 
    //  
    function get_longpollkey()
    {
	$result= $this->get_request("/secure/longPollKey");
	
	// app error check 
	if (!$this->check_ok($result)){
	    return False;
	}
	
	$tmp = $result->res[0]->body[0];
	$this->longpollkey=$tmp->longPollKey[0];
	return $this->longpollkey;
    }
    
    // 
    // Query the API directly for instruments relating to a given asset class
    // We use the search oracles - symbol groups as search terms.
    // 
    // This can be a heavy call. See get_instrument_by_id for something more precise.
    // 
    // This used to be called search asset class. The whole asset class thing is very 
    // confusing. Its semi random as to when you need to search for assetClass%3Afoo+ 
    // vs searching for just +foo. From poking the ui it appears that searching for +foo
    // seems to work well in all cases.  
    // 
    function search_instruments($search_string)
    {
	$instruments = array();
	$quit=0;
	$offset=0;
	
	if (!isset($search_string)) {
	    print "LMAXAPI: need a term to search for\n";
	    return FALSE;
	}
	
	if ($search_string == "+") {
	    $asset_query = "+";
	} else {
	    $asset_query="+".$search_string;
	}
	
	while (!$quit) {
	    $result = $this->get_request("/secure/instrument/searchCurrentInstruments?q=".$asset_query."&offset=".$offset);
	 
	    // app error check 
	    if (!$this->check_ok($result)){
		return False;
	    }
	    
	    // now check hasmoreresults, set the quit flag and compute the offset for the next call. 
	    if ($result->res[0]->body[0]->hasMoreResults[0] != "true") {
		$quit=1;
	    }
	
	    $tmp = $result->res[0]->body[0]->instruments[0]->instrument;
	    
	    foreach ($tmp as $val) {
		$foo = new instrument($val);
		$instruments[$foo->id]=$foo;
	    }
	    $offset = $foo->id;
	}
	
	return $instruments;
    }
    
    //
    // Synchronous subscription/data routines
    //
    // subscribe to stuff
    //
    // Valid subscription types are: "order", "account", "position"
    // There are others I may be missing. accountstatus for example. 
    function setup_subscription($type="order") 
    {
	if ($this->longpollkey === FALSE) {
	    $this->get_longpollkey();
	}
	
	if (($type != "order") &&
	    ($type != "account") &&
	    ($type != "position")) {
	    // not a valid type
	    if ($this->VERBOSE) print "invalid subscription type, try one of order, account, position\n";
	    return FALSE;
	}
	
	$postdata = "{\"req\":[{";
	$postdata .= "\"subscription\":[{\"type\":\"$type\"}],";
	$postdata .= "\"longPollKey\":\"$this->longpollkey\"}]}";

	$result = $this->post_request("/secure/subscribe",$postdata);

	//app error check 
	if (!$this->check_ok($result)){
	    return False;
	} 

	return True;
    }

    // 
    // Subscribe to an orderbook for market data. 
    function subscribe_to_orderbook($instrument_list)
    {
	if ($this->longpollkey === FALSE) {
	    $this->get_longpollkey();
	}
	  
	$data = "{\"req\":[{";
	
	// manually construct pseudo json string. 
	//
	foreach ($instrument_list as $id)
	{
	    $data .= "\"subscription\":[{\"ob2\":\"$id\"}],";
	}
	
	$data .= "\"longPollKey\":\"$this->longpollkey\"}]}";
		
	$result = $this->post_request("/secure/subscribe",$data);

	//app error check 
	if (!$this->check_ok($result)){
	    return False;
	} 

	return True;
    }
    
    // 
    // retrieve any updates on our subscriptions. 
    // This returns an array of objects. 
    // Each object has a type e.g. $object->type = "account"
    // 
    // See examples for how to interpret these events. 
    //
    function get_events()
    {
	$postdata = "";
	$updates = array();
	
	// need to subscribe to an instrument first
	if ($this->longpollkey === FALSE) return FALSE;
	
	if ($this->sequence_no === FALSE) $this->sequence_no = -1;
	    
	$result = $this->post_request("/push/longPoll",$postdata);

	if ($result == false) return false;
	
//	print_r($result);
	$this->sequence_no = $result->events[0]->header[0]->seq[0];
	
	foreach ($result->events[0]->body[0] as $tag => $value) {
	    if ($this->DEBUG) print "tag: $tag\n";
	    switch ($tag){
	     case "$":
		break;
	     case "ob2":
		// we normally get an array of market data updates, one per instrument
		foreach ($value as $item) {
		    $updates[] = new orderbook_update($item);
		}
		break;
	     case "accountState":
		$updates[] = new account_update($value);
		break;
	     case "orders":
		// See RT 27031 & RT 27033 for brokenness around this call and returned data.
		if (!empty($value[0]->page[0])) {
		    $value = $value[0]->page[0]->order;
		}
	     case "order":
		// we normally get an array of active orders		
		foreach ($value as $item) {
		    $updates[] = new active_order($item);
		}
		break;     
	     case "positions":
		if (empty($value[0]->page[0])) break;
		// See RT 27031 & RT 27033 for brokenness around this call and returned data. 
		$value = $value[0]->page[0]->position;
	     case "position":
		foreach ($value as $item) {
		    $updates[] = new position($item);
		}
		break;
	     case "instructionRejected":
		foreach ($value as $item) {
		    $updates[] = new rejected_instruction($item);
		}
		break;
#		foreach ($value->page as $tag2 =>$order) {
#		    print $tag2;
#		}
	    } // switch 
	} // foreach tag 
	
	return $updates;
    }
    
    //
    // Order placement
    //
    // instrument_id is the id of the market e.g. 4002 
    // order_type is one of the members of class order_type e.g. order_type::market
    // fill_type  is one of the members of class fill_strategy e.g. fill_strategy::IoC
    // quantity is the quantity of contracts. Positive for buy, negative for sell.
    // price is required for limit orders, and ignored for market orders 
    // order_id is an optional ID you can supply to the exchange.
    // stop price is the price at which we'd like the market stop to execute. 
    // 
    // NOTES: Stops changed. This broke the previous call to placeStop. As a result
    //        any stops here will probably not work. 
    function place_order($instrument_id,$order_type,$fill_type,$quantity,$price=FALSE,$order_id=FALSE,$stop_price=FALSE)
    {
	if ($this->longpollkey === FALSE) {
	    $this->get_longpollkey();
	}
	// decode the order type and strategy into what the API needs.
	$good_until = "";
	$partial_match = ""; // this is misleadingly named "allowUnmatched" in the doc.
	$timeinforce = "ImmediateOrCancel";
	$is_stop = FALSE;
	
	// market orders 
	//   can be good until immediate
	//   can be Fill or Kill, Immediate or Cancel.
	//   have no price
	// limit orders
	//   can be good until cancel or immediate
	//   can be fill or kill, immediate or cancel, good till cancel
	//   need a price 
	//
	// placeStop has been removed. 
	// 
	$sanity = FALSE;
	switch ($order_type) {
	 case order_type::market:
	    if (($fill_type === fill_strategy::IoC)||
		($fill_type === fill_strategy::FoK)) $sanity=TRUE;
	    break;
	 case order_type::limit:
	    if (($fill_type === fill_strategy::IoC)||
		($fill_type === fill_strategy::FoK)||
		($fill_type === fill_strategy::GTC)||
		($fill_type === fill_strategy::GFD)) $sanity=TRUE;
	 case order_type::market_stop:
	     if (($fill_type === fill_strategy::GFD)) $sanity=TRUE;
	    break;
	 default:
	    break;
	}
	// incompatible fill strategy and order type
	if (!$sanity) {
	    if ($this->VERBOSE) print "LMAXAPI: failed sanity test for order type and fill strategy\n";
	    return FALSE;
	}
	
	// now check that if its a limit order, we have a price
	if (($order_type === order_type::limit) && ($price == FALSE)) {
	    if ($this->VERBOSE) print "LMAXAPI: need a price for a limit order\n";
	    return FALSE;
	}
	
	// and that if its a stop, we have a stop price 
	if (($order_type === order_type::market_stop) && ($stop_price == FALSE)) {
	    if ($this->VERBOSE) print "LMAXAPI: need a stop price for a market stop order\n";
	    return FALSE;
	}
	
	// populate the api variables. 
	switch ($fill_type) {
	 case fill_strategy::fill_or_kill:
	    $timeinforce = "FillOrKill";
	    $good_until = "Immediate";
	    $partial_match = "false";
	    break;
	 case fill_strategy::immediate_or_cancel:
	    $timeinforce = "ImmediateOrCancel";
	    $good_until = "Immediate";
	    $partial_match = "true";
	    break;
	 case fill_strategy::good_for_day:
	    $timeinforce = "GoodForDay";
	    $good_until = "Cancelled";
	    $partial_match = "true";
	    break;
	 case fill_strategy::good_til_cancel:
	    $timeinforce = "GoodTilCancelled";
	    $good_until = "Cancelled";
	    break; 
	 default:
	    // we don't support all or nothing (AoN) orders
	    if ($this->VERBOSE) print "LMAXAPI: unknown fill_strategy\n";
	    return FALSE;
	}

	// manually construct pseudo json string. 
	//
	$data = "{\"req\":[{\"order\":[{";
	$data .= "\"instrumentId\":\"$instrument_id\",\"quantity\":\"$quantity\",\"timeInForce\":\"$timeinforce\",";
	// only needed for limit orders 
	if ($order_type === order_type::limit) $data .= "\"price\":\"$price\",";
	// only needed for market stops 
	if ($order_type === order_type::market_stop) $data .= "\"stopCondition\":[{\"price\":\"$stop_price\"}],";
	$data .= "}]}]}";
	
	$result = $this->post_request("/secure/trade/placeOrder",$data);
	
	//app error check 
	if (!$this->check_ok($result)){
	    if ($this->VERBOSE) print "LMAXAPI: request rejected\n";	    
	    return False;
	} 

	// retrieve the instruction id 
	$return_id = $result->res[0]->body[0]->instructionId[0];
	return $return_id;
    }

    //
    // cancel the unmatched part of a previous order. 
    // 
    // note that if instrument_id and original_order_id are not specified 
    // then allegedly all unmatched orders will be cancelled. 
    // 
    function cancel_order ($instrument_id=FALSE, $original_order_id=FALSE, $order_id=FALSE)
    {
	// manually construct pseudo json string. 
	//
	
	if ($instrument_id === FALSE && $original_order_id === FALSE) {
	    return False;
	} else {
	    $data = "{\"req\":[{";
	    if ($instrument_id !== FALSE) $data .= "\"instrumentId\":\"$instrument_id\"";
	    if ($original_order_id !== FALSE) $data .= ",\"originalInstructionId\":\"$original_order_id\"";
	    if ($order_id !== FALSE) $data .= ",\"instructionId\":\"$order_id\"";
	    $data .= "}]}";
	}
	
	$result = $this->post_request("/secure/trade/cancel",$data);

	//app error check 
	if (!$this->check_ok($result)){
	    if ($this->VERBOSE) print "LMAXAPI: request rejected\n";	    
	    return False;
	} 

	// retrieve the instruction id 
	$return_id = $result->res[0]->body[0]->instructionId[0];
	return $return_id;
    }
    
    // 
    // close out an order
    // 
    function close_order ($instrument_id=FALSE, $order_id=FALSE, $quantity=FALSE)
    {
	if ($order_id === FALSE  && $instrument_id === FALSE && $quantity === FALSE )
	{
	    return FALSE;
	    
	} else {
	    $data = "{\"req\":[{";
	    $data .= "\"instrumentId\":\"$instrument_id\"";
	    $data .= ",\"originalInstructionId\":\"$order_id\"";
	    $data .= ",\"quantity\":\"$quantity\"";
	    $data .= "}]}";
	}
	
	$result = $this->post_request("/secure/trade/closeOutOrder",$data);

	//app error check 
	if (!$this->check_ok($result)){
	    if ($this->VERBOSE) print "LMAXAPI: request rejected\n";	    
	    return False;
	} 

	// retrieve the instruction id 
	$return_id = $result->res[0]->body[0]->instructionId[0];
	return $return_id;
    }
	
    
} // class lmaxapi

    
?>
