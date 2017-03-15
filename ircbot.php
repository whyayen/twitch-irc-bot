<?php

class VariableClass {

  #region SETTINGS
	public $chan = "#";
	public $server = "irc.twitch.tv";
	public $port = 6667;
	public $nick = "";
	public $pass = ""; //http://tmi.twitchapps.com <-- Go there for your oauth key.
	#end

	public $socket;

	#region VARIABLES

	//Database variables
}


// Prevent PHP from stopping the script after 30 sec
set_time_limit(0);

// Set the timezone
date_default_timezone_set('Asia/Taipei'); //ENTER YOUR TIMEZONE HERE. FIND AVAILABLE TIMEZONES HERE: http://www.php.net/manual/en/timezones.php

#region VC Vars
$VC = new VariableClass();

$VC->socket = fsockopen($VC->server, $VC->port);

fputs($VC->socket,"PASS $VC->pass\n");
fputs($VC->socket,"NICK $VC->nick\n");
fputs($VC->socket,"JOIN " . $VC->chan . "\n");
#end


function StripTrim($strip, $trim){
	$strippedString = (string)stripcslashes(trim($strip, $trim));
	$strippedString = preg_replace('~[.[:cntrl:][:space:]]~', '', $strippedString);
	return $strippedString;
}




// Set timout to 1 second
if (!stream_set_timeout($VC->socket, 1)) die("Could not set timeout");

while(1) {
	
	while($data = fgets($VC->socket)) {
	    flush();

		//Separate the incoming data by spaces and add them to the the message variable as a list.
		$message = explode(' ', $data);
		//If the server sends us a ping, pong those suckers back!
		if($message[0] == "PING"){
        	fputs($VC->socket, "PONG " . $message[1] . "\n");
	    }
		else {
			echo $data;
		}
		
		if($message[1] == "PRIVMSG"){
			//if($VC->users!=NULL && count($VC->users)>0) {

				$temp = explode('!', (string)$message[0]);
				$sender = StripTrim($temp[0], ":");
				// $temp[0] 是使用者
				$rawcmd = explode(':', $message[3]); //Get the raw command from the message.
				//Get all arguments after the raw command.
				$args = NULL;
				if(count($message) > 4){
					for($i = 4; $i < count($message); $i++){
						$args .= $message[$i] . ' ';
					}
				}
				
				$rawcmd = preg_replace('~[.[:cntrl:][:space:]]~', '', $rawcmd);
				
			
				if($rawcmd[1] == "!say") {
					fputs($VC->socket, "PRIVMSG " . $VC->chan . " :Hello World!\n");	
				}elseif($rawcmd[1] == "!87"){
					fputs($VC->socket, "PRIVMSG " . $VC->chan . " :87 \n");	
				}
		}
	}
	if(!isset($tmp_times)){
		$tmp_times = time();
	}else{
		$now_times = time();
		if($now_times-$tmp_times >= 120){
			$tmp_times = $now_times;
			fputs($VC->socket, "PRIVMSG " . $VC->chan . " :新加入的觀衆歡迎到 FB 按讚哦！ https://www.facebook.com/");
		}
	}
	
	if (!feof($VC->socket)) {
		continue;
	}
	
	sleep(1);
}
?>
