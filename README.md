lmax-php-api
============

An example php binding for the lmax api

Classes to connect to the LMAX API and hopefully do some useful 
things with it. 

Released under the lgpl vn 3. You should have a copy of the lgpl
alongside this file.

No warranty or fitness for any purpose. Use at your own risk. This
program is provided for the purpose of learning about the LMAX API
and my own personal monitoring projects. 

You take it on your own head if you use this for trading. This API
library was not intended for that. I've never put real money through
it and strongly advise you don't either. I'd advise you to look at the 
"proper" LMAX .NET and Java APIs for supported trading apis. 

They're tested and QA'ed. This is not.  Here be dragons and lions and
tigers. 

LMAX don't support or even know about this. You can try and ask them
for support on general protocol questions, but they will stare at you
blankly and point you at the Java and .NET api's if you ask about this.

Usage
-----

This is a synchronous, single threaded library that uses longpoll.
As a result you have to poll for events using the get_events method. 
This will return various objects that represent the events returned.

Brief Usage;

Include the library

require_once("lmaxapi.php");

Create a new instance of the class pointing at the default venue (demo)

$foo = new lmaxapi();

login

$foo->login($username,$password,$productType="CFD_DEMO");

logout

$foo->logout();

place order, in this case a limit good til cancel on EUR/GBP, 1
contract at a price of 0.81782.

$foo->place_order(4003,order_type::limit,fill_strategy::GTC,1,(0.81782));

close out order with id of $market_order_id, one contract. 

$foo->close_order(4003,$market_order_id,-1);

return data on all GBP crosses instruments. 

$foo->search_instruments("GBP");

Examples, Test
--------------

All of these require a username and password to the LMAX demo exchange.

test/test.php - has a verbose set of tests to exercise part of the
                library. It can give you an idea of how to use it.
		
examples/ticker - a simple ticker app that prints out the current 
               GBP/USD prices from the demo exchange. 
	      
examples/positions - A web app that logs in, and retrieves data on the 
                currently open positions for an account. 
		

Reminder: this is sample code for learning about the lmax protocol. 
Do not use for trading. This is not officially or unofficially 
supported. If this code eats your dog, then its at your own risk. 

