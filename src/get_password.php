<?php

// borrowed from the internet at http://www.dasprids.de/blog/2008/08/22/getting-a-password-hidden-from-stdin-with-php-cli

$username="";
$password="";

function get_username_password()
{
    global $username, $password;
    
    print "username: ";
    $username = trim(fgets(STDIN));
    
    // Get current style
    $oldStyle = shell_exec('stty -g');
   
    print "password: ";
    shell_exec('stty -icanon -echo min 1 time 0');
   
    $password = '';
    while (true) {
	$char = fgetc(STDIN);
	
	if ($char === "\n") {
	    break;
	} else if (ord($char) === 127) {
	    if (strlen($password) > 0) {
		fwrite(STDOUT, "\x08 \x08");
		$password = substr($password, 0, -1);
	    }
	} else {
	    fwrite(STDOUT, "*");
	    $password .= $char;
	}
    }
    
    // Reset old style
    shell_exec('stty ' . $oldStyle);
    echo "\n";
    return ;
}

//get_username_password();
//
//echo "username: ".$username." password: ".$password . "\n";
?>
