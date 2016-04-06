<?php
// Protocol const
const WS_STATUS_NORMAL_CLOSE =             1000;
const WS_STATUS_GONE_AWAY =                1001;
const WS_STATUS_PROTOCOL_ERROR =           1002;
const WS_STATUS_UNSUPPORTED_MESSAGE_TYPE = 1003;
const WS_STATUS_MESSAGE_TOO_BIG =          1004;

// Leave room 

const REASON_LEAVE = 1;
const REASON_KICKED = 2;
const REASON_DISCONNECTED = 3;
const REASON_ROOM_DESTROYED= 4;

require 'Socket.php';

class GN_Game_Base extends GN_Game_Socket{

	protected $magic_key;
	public $maxPlayersAble = true;
	public $maxPlayers = 2;
	protected $json = array();

	protected $scoreUrl,$hash;

	function __construct($ip ="10.11.13.1",$port,$magic_key,$scoreUrl=null,$hash=null)
	{
		$this->adminKey = $magic_key;
		$this->bind('message', 'wsOnMessage');
		$this->bind('open', 'wsOnOpen');
		$this->bind('admin_open', 'wsOnAdminOpen');
		$this->bind('close', 'wsOnClose');
		$this->bind('admin_close', 'wsOnAdminClose');
		$this->users = array();
		$this->registered = array();
		$this->rooms = array();

		$this->scoreUrl=$scoreUrl;
		$this->hash=$hash;

		//$this->score('piotr@promienko.pl','piotr.podstawski@gammanet.pl');

		file_put_contents(sys_get_temp_dir().'/'.strtolower(get_class($this)).'.pid',getmypid());
		$this->wsStartServer($ip, $port);
		//unlink(sys_get_temp_dir().'/'.strtolower(get_class($this)).'.pid');
	}


	protected function score($winners,$losers,$t='')
	{
		if (!$this->scoreUrl) return;

		$w = is_array($winners) ? $winners : array($winners);
		$l = is_array($losers) ? $losers : array($losers);
	
		if (!$t) $t = time();

		foreach ($w AS $winner)
		{
			$url=$this->scoreUrl;

			$vars=array('mail'=>$winner,
						'sig'=>GN_User::getSig($winner, $this->hash),
						'id'=>strtolower(get_class($this)),
						'game_attempt_id'=>$t,
						'score'=>1,
						'game_id' => is_array($losers) ? md5(serialize($losers)) : $losers
			);

			foreach ($vars As $k=>$v) $url=str_replace('{'.$k.'}',$v,$url);
			file($url);
		}

		foreach ($l AS $loser)
		{
			$url=$this->scoreUrl;

			$vars=array('mail'=>$loser,
						'sig'=>GN_User::getSig($loser, $this->hash),
						'id'=>strtolower(get_class($this)),
						'game_attempt_id'=>$t,
						'score'=>-1,
						'game_id' => is_array($winners) ? md5(serialize($winners)) : $winners
			);

			foreach ($vars As $k=>$v) $url=str_replace('{'.$k.'}',$v,$url);
			file($url);
		}


	
	}

	protected function removeUser($clientID)
	{
	$uid = $this->wsClients[$clientID]['uid'];
	if ($uid==-1) return true;
	echo "Remove (#$clientID) from user $uid\n";
	if (count ($this->users[$uid]['Sockets'])==1)
		{
		unset($this->users[$uid]);
		$this->onLogout($uid);
		}
		else
		foreach ($this->users[$uid]['Sockets'] as $ksocket => $socket)
			{
			if ($socket == $clientID)
				{
				unset($this->users[$uid]['Sockets'][$ksocket]);
				echo "unset socket $ksocket-$clientID \n";
				}
			}

	}


	protected function userAdd($clientID,$registeredID)
	{
					// Register user
					$adduser = true;
					foreach ($this->users as $iuser=>$user)
						{
					
						// Czy taki user jest online
						if ($registeredID == $user["Id"] )
							{
							echo "User istenieje - dopisz \n";
							$add=true;
							foreach ($user['Sockets'] as $socketID)
								{
								if ($socketID == $clientID)
									{
									$add=false;
								
									return $iuser;		
									
									}
								}
							if ($add) 
								{
								
								$this->users[$iuser]['Sockets'][] = $clientID;
								return $iuser;
								}
							
							$adduser = false;
							}
			
						}
						if ($adduser)
							{
							echo "Dodaj uzytkownika \n";
							
							$new_user['Id'] = $registeredID;
							$new_user['Sockets'][] = $clientID;
				
							$new_key = $this->getArrayKey($this->users);
								
							if ($new_key != -1)
								{
								$this->users[$new_key] = $new_user;
								return $new_key;
								}
							return -1;
							}
							
	}
	protected function registerUser($clientID,$message)
	{
	$this->json = json_decode($message,true);
		
		if (isset($this->json['Register']))
			{
				foreach ($this->registered as $k=>$reguser)
				{
				// Czy socket ma prawo do komunikacji ?
				if ($reguser['Key'] == $this->json['Register'])
					{
					// Register socket & addUser
					
					$lastUser = -2;
					$lastUser = $this->userAdd($clientID,$k);
					if ($lastUser!==false)
					$this->wsClients[$clientID]['uid']= $lastUser;
					$this->wsClients[$clientID]['rid']= $k;
					
					
					
					return true;
					}
				}
					if ($this->wsClients[$clientID]['rid']==-1)
					{
					echo  "User not found: " . print_r($this->registered,true);
					$this->wsSendClientClose($clientID,WS_STATUS_PROTOCOL_ERROR);
					$this->dump();
					return false;
					}
			}
			else
			{
			$this->wsSendClientClose($clientID,WS_STATUS_PROTOCOL_ERROR);
			echo "Bad register call\n";
			return false;
			}
			
		
		
		
	}
		
	protected function  ProcessMessage($clientID,$message)
	{

	$this->json=array();
	if ($this->json = json_decode($message,true))
	{

	// Is command
		if (isset($this->json["Command"]))
		{
		
		//dump
		if  ($this->json["Command"]=="dump")
			{
			$this->dump();
			}
		
			// User list
			if  ($this->json["Command"]=="user_list")
			{
				$helper="";
				$helper2="";
				$znak=0;
				foreach ( $this->users as $user)
				{
				$id = $user['Id'];
				
				if ($znak!=0)$helper.=",";
					$helper2.='"#' . print_r($user['Sockets'],true) . 'User:  ' . $this->registered[$id]['Name'] ."(".$this->registered[$id]['Mail'] .')"' ;
					$helper.='{"Name":"' . $this->registered[$id]['Name'] ."(".$this->registered[$id]['Mail'] .')","Id":"'.$id.'"}' ;
					$znak++;
				}
				$send = '{"user_list":['.$helper.']}';
				$this->wsSend($clientID, $send);
				echo $helper2;
						return true;
			}
			
			// User list
			if  ($this->json["Command"]=="player_list")
			{
			echo "player_list\n";	
			return true;
			}
			
			

			// Room list
			if  ($this->json["Command"]=="room_list")
			{
				$helper="";
				$znak=0;
				foreach ( $this->rooms as $iroom=>$room)
				{
							var_dump($room);
				if (count($room['Players'])< $room['maxPlayers'])
					{
			
						$id = $iroom;
						$oid = $room['Owner'];
						$isPassword = "";
						$isPassword = ($room['Password']!="")? "true":"false";
						if ($znak!=0)$helper.= ",";
						$helper.= '{"roomName":"'.$room['roomName'].'","owner":{"name":"'.$this->getClientUserName($oid).'","id":'.$oid.'},"id":'.$iroom.',"Password":'.$isPassword.',"Players":'.count($room['Players']).',"maxPlayers":'.$room["maxPlayers"].'}';
						$znak++;
					}
				}
				$send = '{"room_list":['.$helper.']}';
				$this->wsSend($clientID, $send);
				echo $helper;
						return true;
			}
			
			// Room list
			if  ($this->json["Command"]=="room_list_all")
			{
				$helper="";
				$znak=0;
				foreach ( $this->rooms as $iroom=>$room)
				{
				var_dump($room);
					$id = $iroom;
					$oid = $room['Owner'];
					$isPassword = "";
					$isPassword = ($room['Password']!="")? "true":"false";
					if ($znak!=0)$helper.= ",";
					$helper.= '{"roomName":"'.$room['roomName'].'","owner":{"name":"'.$this->getClientUserName($oid).'","id":'.$oid.'},"id":'.$iroom.',"Password":'.$isPassword.'}';
					$znak++;
				}
				$send = '{"room_list":['.$helper.']}';
				$this->wsSend($clientID, $send);
				echo $helper;
						return true;
			}
			
			// Room leave
			
			
			if  ($this->json["Command"]=="room_leave")
			{
			try {$reason = $this->leaveRoom(REASON_LEAVE,$clientID); 	}
			catch (Exception $e)
				{
				echo $e->getMessage() . "\n";
				$send = '{"room_leave":-1,"error":"'. $e->getMessage() .'"}';
				$this->wsSend($clientID, $send);
				return false;
				}
				$send = '{"room_leave":'.$reason.'}';
				$this->wsSend($clientID, $send);
			return true;
			}
			
			// room_state
			if  ($this->json["Command"]=="player_ready")
			{
			try {	$this->onPlayerReady($clientID);}
			catch (Exception $e)
				{
				echo $e->getMessage() . "\n";
				$send = '{"player_ready":"false","error":"'. $e->getMessage() .'"}';
				$this->wsSend($clientID, $send);
				return false;
				}
			return true;
			}
			
			
			
			// Room join
			if  ($this->json["Command"]=="room_join" && isset($this->json["Arguments"]) && $this->json["Arguments"]["RoomId"]!="")
			{
			if ($this->getRoom($clientID) != -1){	echo "User already in a room.\n";	return false;	}
			if (!isset($this->json["Arguments"]["Password"]))
			$password="";
			else
			$password = $this->json["Arguments"]["Password"];
			$roomID = (int)$this->json["Arguments"]["RoomId"];
			try{$return = $this->joinRoom($roomID,$password,$clientID); } catch (Exception $e)
			{
			echo "Exception: " . $e->getMessage() . "\n";
			$send = '{"room_join":"false","error":"'. $e->getMessage() .'"}';
			$this->wsSend($clientID, $send);
			return false;
			}
			if ($this->json = json_encode($return))
				{
				$send = '{"room_join":"true","Arguments":' . $this->json . '}';
				echo $send . "\n";
				$this->wsSend($clientID, $send);
				//
				
				$helper="";
				$znak=0;
				foreach ( $this->rooms[$roomID]['Players'] as $player)
				{
				if ($znak!=0)$helper.=",";
				$helper.='{"name":"' . $this->getClientUserName($player) .'","id":"'.$player.'","mail":"'. $this->getClientMail($player) .'"}' ;
				$znak++;
				}
				$send = '{"player_list":['.$helper.']}';
				$this->wsSend($clientID,$send);
				//
				$send = '{"player_join":{"name":"'.$this->getClientUserName($clientID).'","id":'.$clientID.',"mail":"'.$this->getClientMail($clientID).'"}}';
				$this->wsSendToOthers($clientID,$send);
				$this->onJoinRoom($clientID);
				return true;
				}
			return false;
			}
			
		
	// User count

			if  ($this->json["Command"]=="user_count")
			{
			$count=count($this->users);
			$send = '{"user_count":'.$count.'}';
			$this->wsSend($clientID, $send);
			return true;
			}
			
			// Room count

			if  ($this->json["Command"]=="room_count")
			{
			
			$count=count($this->rooms);
			$send = '{"room_count":'.$count.'}';
			$this->wsSend($clientID, $send);
			return true;
			}
			

		
			
			
	// Create room
			if  ($this->json["Command"]=="room_create" && isset($this->json["Arguments"]) && $this->json["Arguments"]["roomName"]!="")
			{
			//Czy warunki konkretnej gry s¹ spe³nione
			if ($this->onBeforeRoomCreate($clientID))
			{
			
			
			if ($this->getRoom($clientID) != -1){	echo "User already Has a room.\n";	return false;	}
			
			if (!isset($this->json["Arguments"]["Password"])) $this->json["Arguments"]["Password"]="";
			$lastRoomID = $this->addRoom($clientID);
			
			if ($lastRoomID !=-1)
					try	{	
			
							$return = $this->joinRoom($lastRoomID,$this->json["Arguments"]["Password"] ,$clientID);
							if ($this->json = json_encode($return))
								{
								
								$send = '{"room_join":"true","Arguments":' . $this->json . '}';
								$this->wsSend($clientID, $send);
								$this->onRoomCreate($lastRoomID);
								return true;
								}
								
							} catch (Exception $e)
							
						{
						echo "Exception while creating room=> joining: " . $e->getMessage() . "\n";
						$this->removeRoom($lastRoomID);
						return false;
						}
					
				return true;
			}
			else
			return false;
			}		
			
		return false;
		}
		
		if  (isset($this->json["data"]))
			{
			
			if ($this->OnGameData($this->json["data"],$clientID))
			return true;
			return false;
			}

			
	return true;		
		}
		return false;
	}

	protected function leaveRoom($reason,$clientID)
	{
	$roomID = $this->getRoom($clientID);

	if (!isset($this->rooms[$roomID]) || ($roomID==-1) ) throw new Exception('Room doesnt exist anymore.');
	if ($this->wsClients[$clientID]['uid']==-1) throw new Exception('User doesnt exist.');

	$this->onPlayerLeave($clientID,$reason);

	var_dump($this->rooms[$roomID]);
	// If leaver == host
		
		if ($clientID===$this->rooms[$roomID]['Owner']) 
		{
		foreach ($this->rooms[$roomID]['Players'] as $iplayers =>$players)
			{
			// Found user to kick
			if ($players==$clientID) 
				{
				unset ($this->rooms[$roomID]['Players'][$iplayers]);
				$this->setRoom($clientID,-1);
				echo "Physicaly removed user.\n";
				}
			}
		// Custom behavior
		if ($this->onOwnerLeave($roomID))
		$this->removeRoom($roomID);
		
		return $reason;
		}
		
		foreach ($this->rooms[$roomID]['Players'] as $iplayers =>$players)
			{
			// Found user to kick
			if ($players==$clientID) 
				{
				
					unset ($this->rooms[$roomID]['Players'][$iplayers]);
					$this->setRoom($clientID,-1);
					echo "Physicaly removed user.\n";
					return $reason;
					
				}
			}
			

		
		
		echo "User to remove - not found \n";
		return -1;
		

	}


	protected function joinRoom($roomID,$password,$clientID)
	{
	// Failures
	if (!isset($this->rooms[$roomID])) throw new Exception('Sorry, room doesnt exist anymore.');
	if ($this->wsClients[$clientID]['uid']==-1) throw new Exception('User doesnt exist.');
	if ($password!=$this->rooms[$roomID]['Password']) throw new Exception('Password is incorrect.');
	if (count($this->rooms[$roomID]['Players'])>=$this->rooms[$roomID]['maxPlayers']) throw new Exception('Too many players.');


	// Ready to go
	$oid = $this->rooms[$roomID]['Owner'];
	$owner = array("Id"=>$oid,"Name"=>$this->getClientUserName($oid));
	$players = array();
	foreach ($this->rooms[$roomID]['Players'] as $player)
		{
		$players[] = array("Id"=>$player ,"Name"=>$this->getClientUserName($player));
		}

	if (isset($this->rooms[$roomID]) && isset($this->rooms[$roomID]['Constructor']) && ($constructor = json_encode($this->rooms[$roomID]['Constructor'])))
	$return = array("players"=>$players,"owner"=>$owner,"Constructor"=>$constructor);
	else
	$return = array("players"=>$players,"owner"=>$owner);

	$this->rooms[$roomID]['Players'][] = $clientID;
	echo "#$roomID($password) - join user " . $this->getClientUserName($clientID) . "\n";
	$this->setRoom($clientID,$roomID);

	return $return;
	}
	protected function addRoom($ownerClientID)
	{
	if ($this->json["Arguments"]["maxPlayers"]== null || $this->json["Arguments"]["maxPlayers"] == 0)
	$this->json["Arguments"]["maxPlayers"] = 2;
	
	$json = $this->json;
	unset($json["Arguments"]);
	unset($json["Command"]);

	$roomname = $this->json["Arguments"]["roomName"];
	$password = $this->json["Arguments"]["Password"];
	
	$maxPlayers = $this->json["Arguments"]["maxPlayers"];
	$constructor = $this->json['Constructor'];


	//Dynamiczne wartoœci poprosze :)
	echo "Create room: $ownerClientID $roomname with password:$password \n";

		$new_room = $json;
		
		$new_room['Owner'] = $ownerClientID;
		$new_room['roomName'] = $roomname;
		$new_room['Password'] = $password;
		$new_room['Players'] = array();

		$new_room['maxPlayers'] = $this->maxPlayers;
		// Is maxPlayers given and allowed to change the default
		if (isset($maxPlayers) && $maxPlayers!="" && $this->maxPlayersAble)
		{
		$new_room['maxPlayers'] = $maxPlayers;
		}
		$new_room['Constructor'] = $constructor;
		echo "MaxPlayers: " .$new_room['maxPlayers'] ."\n";

	$new_key = $this->getArrayKey($this->rooms);
								
		if ($new_key != -1)
		{
		$this->rooms[$new_key] = $new_room;
		return $new_key;
		}
		return -1;
							
		
		
		
		
	}

	protected function removeRoom($roomID)
	{

	foreach ($this->rooms[$roomID]['Players'] as $player)
	{
		$reason = $this->leaveRoom(REASON_ROOM_DESTROYED,$player);
		$send = '{"room_leave":'.$reason.'}';
		$this->wsSend($player, $send);
	}
	unset($this->rooms[$roomID]);
	$this->onRoomDestroy($roomID);
	echo "Removed room: $roomID \n";
	}

	public function wsOnMessage($clientID, $message, $messageLength, $binary) {



		// check if message length is 0
		if ($messageLength == 0) {
			$this->wsClose($clientID);
			return;
		}

		// Is user is registered 
			if ($this->wsClients[$clientID]['rid']>-1)
			{
			
			if ($this->ProcessMessage($clientID,$message)!=true)
			echo "ProcessMessage != true \n";
			
			}
			else
			{
			if ($this->registerUser($clientID,$message))
			$this->onLogin($clientID);
			}
	}

	protected function onLogout($uid)
	{
	echo "User $uid logged out\n";
	}
	public function onLogin($clientID)
	{

		$ip = long2ip( $this->wsClients[$clientID]['IP'] );
		$user = $this->registered[$this->wsClients[$clientID]['rid']]['Name'];
		$this->log( "#$clientID ($ip) User: ". $user ." \n" );
		$this->wsSend($clientID, '{"self_login":"true","Arguments":{"id":'.$clientID.',"name":"'.$this->getClientUserName($clientID).'","mail":"'. $this->getClientMail($clientID) .'"}}');
		//$this->wsSend($clientID, $clientID);	
		//Send a join notice to everyone but the person who joined
			
	}

	// when a client connects
	public function wsOnOpen($clientID)
	{

	/*
		$ip = long2ip( $this->wsClients[$clientID]['IP'] );

		$this->log( "$ip ($clientID) has connected." );

		$this->wsClients[$clientID]['Registered']=false;

	$this->wsSend($clientID, $clientID);	
		//Send a join notice to everyone but the person who joined
		foreach ( $this->wsClients as $id => $client )
			if ( $id != $clientID )
				$this->wsSend($id, "Visitor $clientID ($ip) has joined the room.");
	*/	
	}

	public function wsOnAdminOpen($clientID)
	{
		$ip = long2ip( $this->wsClients[$clientID]['IP'] );
		$this->log( "$ip ($clientID) ADMIN has connected." );
	}

	// when a client closes or lost connection
	public function wsOnClose($clientID, $status) {

	$ip = long2ip( $this->wsClients[$clientID]['IP'] );

	// If user was logged
	if ($this->getUserId($clientID)!=-1)
		{
	$user = $this->getUserName($this->getUserId($clientID));
	echo  "#$clientID ($ip) User: ". $user ." has disconnected. \n" ;

	// If its not Control (Admin) socket


		
	// remove users from rooms
	try {	$this->leaveRoom(REASON_DISCONNECTED,$clientID);	}
	catch (Exception $e)
		{
		echo "wsOnClose Exception : " . $e->getMessage() . "\n";
		}
	// Remoev user from Users
	$this->removeUser($clientID);	

		//Send a user left notice to everyone in the room
		foreach ( $this->wsClients as $id => $client )
			$this->wsSend($id, "User '$user'  [#$clientID] ($ip) has left the room.");
		}
		else
		echo "Unregistered connection($ip) dropped \n";
	}



	public function wsOnAdminClose($clientID, $status) {
	 
	$ip = long2ip( $this->wsClients[$clientID]['IP'] );
	$this->log( "#$clientID ($ip) Admin has disconnected. \n" );

	}

	public function notify($clientID,$message)
	{
	$send = '{"notify":"'.$message.'"}';
	$this->wsSend($clientID, $send);	
	}


	public function wsSendToOthers($clientID,$send)
	{
	$roomID = $this->getRoom($clientID);
	$players = $this->getPlayers($roomID);
	foreach ($players as $client)
		{
		if ($client!=$clientID)
		$this->wsSend($client,$send);
		}
	return true;
	}

	public function wsSendToIdle($send)
	{

	foreach ($this->wsClients as $kclient=>$client)
		{
		if ($this->getRoom($kclient)==-1)
		$this->wsSend($kclient,$send);
		}
	return true;
	}

	public function wsSendRoom($roomID,$send)
	{

	$players = $this->getPlayers($roomID);
	foreach ($players as $client)
		{
		$this->wsSend($client,$send);
		}
	return true;
	}


	public function getPlayers($roomID)
	{
	return $this->rooms[$roomID]['Players'];
	}

	protected function getUserId($clientID)
	{
	return $this->wsClients[$clientID]['uid'];
	}


	protected function setRoom($clientID,$roomID)
	{
	$this->wsClients[$clientID]['roomID'] = $roomID;
	return true;
	}

	protected function getRoom($clientID)
	{
	return $this->wsClients[$clientID]['roomID'];
	}

	protected function getUserName($uid)
	{
	if($uid==-1) return -1;
	$rid = $this->users[$uid]['Id'];
	return $this->registered[$rid]["Name"];
	}

	protected function getClientUserName($clientID)
	{
	if ($this->wsClients[$clientID]['uid']==-1) return -1;
	if ($this->wsClients[$clientID]['rid']==-1) return -1;
	$rid = $this->wsClients[$clientID]['rid'];
	return $this->registered[$rid]["Name"];
	}
	
	protected function getClientMail($clientID)
	{
	if ($this->wsClients[$clientID]['uid']==-1) return -1;
	if ($this->wsClients[$clientID]['rid']==-1) return -1;
	$rid = $this->wsClients[$clientID]['rid'];
	return $this->registered[$rid]["Mail"];
	}

	public function log($text)
		{
		echo $text ."\n";
		}
		
	protected function getArrayKey($array)
	{
	if (!isset($array)) return -1;
	if (empty($array)) return 0;
	$max = max(array_keys($array));
	for ($i=0;$i<$max;$i++)
								{
								if (!isset($array[$i]))
								return $i;
								}
	return $max+1;

								
	}
	protected function dump()
	{
	echo "$$$$$ USERS $$$$$";
	print_r($this->users);
	echo "$$$$$ REGISTERED $$$$$";
	print_r($this->registered);
	echo "$$$$$ CLIENTS $$$$$";
	print_r($this->wsClients);
	echo "$$$$$ ROOMS $$$$$";
	print_r($this->rooms);
	}

	protected function onOwnerLeave($roomID){echo "onOwnerLeave";return true;}
	protected function onPlayerLeave($clientID,$reason){echo "onPlayerLeave";return true;}
	protected function onPlayerReady($clientID){echo "onPlayerReady";return true;}
	protected function onBeforeRoomCreate($clientID){echo "onRoomCreate\n";return true;}
	protected function onGameData($data,$clientID){echo "onGameData\n";return true;}
	protected function onJoinRoom($clientID){echo "onJoinRoom \n";return true;}
	protected function _onRoomCreate($roomID){echo "_onRoomCreate\n";return true;}
	protected function onRoomDestroy($roomID)
	{
	$send = '{"room_destroy":{"id":'.$roomID.'}}';
	$this->wsSendToIdle($send);
	return true;
	}
	protected function onRoomCreate($roomID)
	{
	$this->_onRoomCreate($roomID);
	$oid = $this->rooms[$roomID]['Owner'];
	$ownerName = $this->getClientUserName($oid);
	$isPassword = "";
	$isPassword = ($this->rooms[$roomID]['Password']!="")? "true":"false";
	$send = '{"room_new":{"roomName":"'.$this->rooms[$roomID]['roomName'].'","owner":{"name":"'.$ownerName.'","id":'.$oid.'},"id":'.$roomID.',"Password":'.$isPassword.'}}';
	$this->wsSendToIdle($send);
	return true;
	}

}




