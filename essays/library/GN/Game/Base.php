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
const REASON_ROOM_DROPPED= 5;

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
			@file($url);
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
			@file($url);
		}


	
	}

	protected function removeUser($clientID)
	{
	$uid = $this->wsClients[$clientID]['uid'];
	if ($uid==-1) return true;
	echo "[removeUser] Remove (#$clientID) from user $uid\n";
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
				echo "[removeUser] unset socket $ksocket-$clientID \n";
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
				
						return true;
			}
			
			// User list
			if  ($this->json["Command"]=="player_list")
			{
			
			return true;
			}
			
			

			// Room list
			if  ($this->json["Command"]=="room_list")
			{
				$helper="";
				$znak=0;
				
				echo "[room_list]\n";
						$reconnects = $this->getReconnectRooms($clientID);
						if (is_array($reconnects) && !empty($reconnects))
						{
						foreach ($reconnects as $kreco=>$reco)
							{
							$oid = $this->rooms[$reco]['Owner']['Id'];
							if ($znak!=0)$helper.= ",";
							$helper.= '{"roomName":"'.$this->rooms[$reco]['roomName'].'","owner":{"name":"'.$this->getClientUserName($oid).'","id":'.$oid.'},"id":'.$reco.',"Reconnect":true,"Players":'.count($this->rooms[$reco]['Players']).',"maxPlayers":'.$this->rooms[$reco]["maxPlayers"].'}';
							$znak++;
							}
						}
						
				foreach ( $this->rooms as $iroom=>$room)
				{
							
				if (count($room['Players'])< $room['maxPlayers'] && $room['Playing']==false)
					{
			
					
						$id = $iroom;
						$oid = $room['Owner']['Id'];
						$isPassword = "";
						$isPassword = ($room['Password']!="")? "true":"false";
						if ($znak!=0)$helper.= ",";
						$helper.= '{"roomName":"'.$room['roomName'].'","owner":{"name":"'.$this->getClientUserName($oid).'","id":'.$oid.'},"id":'.$iroom.',"Password":'.$isPassword.',"Players":'.count($room['Players']).',"maxPlayers":'.$room["maxPlayers"].'}';
						$znak++;
					}
				}
				$send = '{"room_list":['.$helper.']}';
				$this->wsSend($clientID, $send);
				
						return true;
			}
			
			// Room list
			if  ($this->json["Command"]=="room_list_all")
			{
				$helper="";
				$znak=0;
				foreach ( $this->rooms as $iroom=>$room)
				{
				
					$id = $iroom;
					$oid = $room['Owner']['Id'];
					$isPassword = "";
					$isPassword = ($room['Password']!="")? "true":"false";
					if ($znak!=0)$helper.= ",";
					$helper.= '{"roomName":"'.$room['roomName'].'","owner":{"name":"'.$this->getClientUserName($oid).'","id":'.$oid.'},"id":'.$iroom.',"Password":'.$isPassword.'}';
					$znak++;
				}
				$send = '{"room_list":['.$helper.']}';
				$this->wsSend($clientID, $send);
				
						return true;
			}
			
			// Room leave
			
			
			if  ($this->json["Command"]=="room_leave")
			{
			try {$reason = $this->clientLeaveRoom(REASON_LEAVE,$clientID); 	}
			catch (Exception $e)
				{
				echo $e->getMessage() . "\n";
				$send = '{"client_leave":'.$clientID.',"reason":-1,"error":"'. $e->getMessage() .'"}';
				$this->wsSend($clientID, $send);
				
				
				return false;
				}
				$send = '{"client_leave":'.$clientID.',"reason":'.$reason.'}';
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
			$roomID = (int)$this->json["Arguments"]["RoomId"];
			
			if ($this->inReconnects($roomID,$clientID))
			{
			echo "[room_join] in reco!\n";
			$this->json["Arguments"]["Password"] = $this->rooms[$roomID]['Password'];
			}
			else
			if ($this->getRoom($clientID) != -1){	echo "User already in a room.\n";	return false;	}
			
			
			if (!isset($this->json["Arguments"]["Password"]))
			$password="";
			else
			$password = $this->json["Arguments"]["Password"];
			
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
				$this->wsSend($clientID, $send);
				//
				
				$helper="";
				$znak=0;
				foreach ( $this->rooms[$roomID]['Players'] as $player)
				{
				if ($znak!=0)$helper.=",";
				$helper.='{"name":"' . $this->getUserName($player['UserID']) .'","id":"'.$player['Id'].'","mail":"'. $this->getUserMail($player['UserID']) .'"}' ;
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
			//Czy warunki konkretnej gry są spełnione
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
			try
			{
			if ($this->OnGameData($this->json["data"],$clientID)) return true;
			return false;
			}
			catch (Exception $e)
			{
			echo  $e->getMessage() . "\n";
			return false;
			}
			
			return false;
			}

			
	return true;		
		}
		return false;
	}

	protected function clientLeaveRoom($reason,$clientID)
	{
	echo "clientLeaveRoom($reason,$clientID)\n";
	// If Client Left Already
	if (!$this->isConnected($clientID)) {	echo "[clientLeaveRoom] Client (-1) has left before. \n";	return true;	}
	
	//Get Room
	$roomID = $this->getRoom($clientID);
	$clientRoomID = $this->getClientRoom($clientID);
	// If Client Left Already 2
	if ( $clientRoomID== -1)
	{
	echo "[clientLeaveRoom] Client(".$clientID.") already left. \n";
	return true;
	}

	
	// Exceptions
	if (!isset($this->rooms[$roomID]) || ($roomID==-1) ) throw new Exception('[clientLeaveRoom] Room doesnt exist anymore. ('.$clientID.')');
	if ($this->wsClients[$clientID]['uid']==-1) throw new Exception('[clientLeaveRoom] User doesnt exist.');

	
	$this->onClientLeave($clientID,$reason);


	// If leaver == host
		
		if ($clientID===$this->rooms[$roomID]['Owner']['Id']) 
		{
				$this->setRoom($clientID,-1);
				$send = '{"client_leave":'.$clientID.',"reason":'.$reason.'}';
				$this->wsSendRoom($roomID, $send);
					foreach ($this->rooms[$roomID]['Players'] as $iplayers =>$players)
					{
					if ($players['Id']== $clientID)
					$this->rooms[$roomID]['Players'][$iplayers]['Connected'] = false;
					}
				
				if (($this->isPlaying($roomID)===false || $this->isPlaying($roomID)===-1) && $reason==REASON_LEAVE)
				{
				echo "[clientLeaveRoom] Room not started.\n";
				$this->userLeaveRoom(REASON_LEAVE,$clientID,$this->getUserId($clientID),$roomID);
				}
				return $reason;
		}

		
	
		
		
		foreach ($this->rooms[$roomID]['Players'] as $iplayers =>$players)
			{
			// Found user to kick
			if ($players['Id']==$clientID) 
				{
					$this->setRoom($clientID,-1);
					echo "[clientLeaveRoom] Physicaly removed client.($clientID)\n";
					$send = '{"client_leave":'.$clientID.',"reason":'.$reason.'}';
					$this->wsSendRoom($roomID, $send);
					echo "[clientLeaveRoom]->wsSendRoom\n";
					$this->rooms[$roomID]['Players'][$iplayers]['Connected'] = false;
					print_r($this->rooms[$roomID]['Players']);
					return $reason;
					
				}
			}
			

		
		
		echo "Client to remove - not found \n";
		return -1;

	}
	
	protected function isPlaying($roomID)
	{
	if (isset($this->rooms[$roomID]['Playing']))
	return $this->rooms[$roomID]['Playing'] ;
	else
	return -1;
	}
	
	protected function setPlaying($arg,$roomID)
	{
		if (isset($this->rooms[$roomID]['Playing']))
	{
	$this->rooms[$roomID]['Playing'] = $arg;
	return true;
	}
	else
	return -1;
	
	}
	
	protected function userLeaveRoom($reason,$clientID,$userID,$roomID)
	{
	try
	{

	$this->setPlaying(false,$roomID);
	echo "[userLeaveRoom] Client: " . $clientID . " Room: " .$roomID ." Reason: ".$reason."\n";
	
		if ($clientID == $this->rooms[$roomID]['Owner']['Id'])
		{
			// Custom behavior
			if ($this->onOwnerLeave($reason,$clientID))
			{
				
				// Tutaj Connected => 
			$reason = $this->clientLeaveRoom($reason,$clientID);
			if (isset($this->rooms[$roomID]['Players']))
			{
				
			foreach ($this->rooms[$roomID]['Players'] as $iplayers =>$players)
				{
				if ($players['Id'] == $clientID)
				unset ($this->rooms[$roomID]['Players'][$iplayers]);
				}
			}
				
			echo "#before removeRoom\n";
			$this->removeRoom($roomID);
			echo "#after removeRoom\n";
			$this->userRemoveRoom($clientID,$userID,$roomID);
			return true;
			}
		}
	if ($this->onPlayerLeave($reason,$clientID))
		{
		$userID = $this->getUserId($clientID);
		echo "[userLeaveRoom] User " . $userID . " has left the room $roomID\n";
		$reason = $this->clientLeaveRoom($reason,$clientID);
		foreach ($this->rooms[$roomID]['Players'] as $iplayers =>$players)
				{
				if ($players['Id'] == $clientID)
					{
					unset ($this->rooms[$roomID]['Players'][$iplayers]);
					$owner = $this->rooms[$roomID]['Owner']['Id'];
					;
					if ($this->getClientRoom($owner) == -1)
						{
						$this->userLeaveRoom(REASON_ROOM_DESTROYED,$owner,$this->rooms[$roomID]['Owner']['UserID'],$roomID);
						}
					break;
					}
				}
		
		$this->userRemoveRoom($clientID,$userID,$roomID);
		
		return true;
		}
	}
				catch (Exception $e)
		{
		echo "[userLeaveRoom]->" . $e->getMessage() . "\n";
		}	
	}


	protected function joinRoom($roomID,$password,$clientID)
	{

	// Failures
	if ($this->wsClients[$clientID]['uid']==-1) throw new Exception('User doesnt exist.');
	if (!isset($this->rooms[$roomID])) throw new Exception('Room doesnt exist.');
	if ($password!=$this->rooms[$roomID]['Password']) throw new Exception('Password is incorrect.');
	$disconnects = false;
	$disconnects = $this->getDisconnected($roomID);
	if (!is_array($disconnects))
	if (count($this->rooms[$roomID]['Players'])>=$this->rooms[$roomID]['maxPlayers']) throw new Exception('Too many players.');

	$uid = $this->getUserId($clientID);

	// Ready to go
	$oid = $this->rooms[$roomID]['Owner']['Id'];
	$owner = array("Id"=>$oid,"Name"=>$this->getClientUserName($oid));
	$players = array();
	foreach ($this->rooms[$roomID]['Players'] as $player)
		{
		$players[] = array("Id"=>$player['Id'] ,"Name"=>$this->getUserName($player['UserID']),"User"=> $player['UserID'] );
		}

	if (isset($this->rooms[$roomID]) && isset($this->rooms[$roomID]['Constructor']) && ($constructor = json_encode($this->rooms[$roomID]['Constructor'])))
	$return = array("players"=>$players,"owner"=>$owner,"Constructor"=>$constructor);
	else
	$return = array("players"=>$players,"owner"=>$owner);

		if (count($disconnects)==0 || !is_array($disconnects))
	{
	
	$this->rooms[$roomID]['Players'][] = array("Id"=>$clientID,"UserID"=>$this->getUserId($clientID),"Connected"=>true);
	
	echo "[joinRoom] Room: $roomID '$password' - join user ($clientID) " . $this->getClientUserName($clientID) . "\n";
	$this->setRoom($clientID,$roomID);
	// User
	$this->userAddRoom($clientID,$roomID);
	}
	else
	{
	
	foreach($disconnects as $kdisconnect=>$disconnect)
	{
	if ($disconnect['UserID']==$uid)  $this->rooms[$roomID]['Players'][$disconnect['kplayer']]['Connected'] = true;
	break;
	}
	
	echo "[joinRoom] Room: $roomID '$password' - join user ($clientID) " . $this->getClientUserName($clientID) . "\n";
	$this->setRoom($clientID,$roomID);
	
	}
	
	
	
	
	

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


	//Dynamiczne wartości poprosze :)
	echo "[addRoom] Create room: $ownerClientID $roomname with password:$password \n";

		$new_room = $json;
		
		$new_room['Owner'] = array('Id'=>$ownerClientID,'UserID'=>$this->getUserId($ownerClientID));
		$new_room['roomName'] = $roomname;
		$new_room['Password'] = $password;
		$new_room['Playing'] = false;
		$new_room['Players'] = array();

		$new_room['maxPlayers'] = $this->maxPlayers;
		// Is maxPlayers given and allowed to change the default
		if (isset($maxPlayers) && $maxPlayers!="" && $this->maxPlayersAble)
		{
		$new_room['maxPlayers'] = $maxPlayers;
		}
		$new_room['Constructor'] = $constructor;
		echo "[addRoom] MaxPlayers: " .$new_room['maxPlayers'] ."\n";

	$new_key = $this->getArrayKey($this->rooms);
								
		if ($new_key != -1)
		{
		$this->rooms[$new_key] = $new_room;
		return $new_key;
		}
		return -1;
							
		
		
		
		
	}
	
	protected function userAddRoom($clientID,$roomID)
	{
	$userID = $this->getUserId($clientID);
	if (!empty($this->users[$userID]['rooms']))
	{
	foreach ($this->users[$userID]['rooms'] as $kroom=>$room)
		{
		if ($room["clientID"] == $clientID) return true;
		if ($room["clientID"] == -1)  {$this->users[$userID]['rooms'][$kroom]["clientID"] == $clientID; return true;}
		}
	}
	$this->users[$userID]['rooms'][] = array("roomID"=>$roomID,"clientID"=>$clientID);
	
	return true;
	}
	
	
	protected function userRemoveRoom($clientID,$userID,$roomID)
	{
	echo "[userRemoveRoom]\n";
	foreach ($this->users[$userID]['rooms'] as $kroom=>$room)
		{
		if ($room["clientID"] == $clientID)	{unset($this->users[$userID]['rooms'][$kroom]);break;}
		}
		
	}

	protected function removeRoom($roomID)
	{

	if (isset($this->rooms[$roomID]['Players']))
	{
		
		foreach ($this->rooms[$roomID]['Players'] as $player)
	{	
		
		if ($player['Id']!= $this->rooms[$roomID]['Owner']['Id'] )
		{
		
		$reason = $this->clientLeaveRoom(REASON_ROOM_DESTROYED,$player['Id']);
		$this->userLeaveRoom(REASON_ROOM_DESTROYED,$player['Id'],$player['UserID'],$roomID);
		
		}
	}
	}
	
	unset($this->rooms[$roomID]);
	$this->onRoomDestroy($roomID);
	echo "[removeRoom]Removed room: $roomID \n";
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
	try {	$this->clientLeaveRoom(REASON_DISCONNECTED,$clientID);	}
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
	if (!is_array($players))  throw new Exception('[wsSendToOthers] Rooms player table non existent. ('.$roomID.')');
	foreach ($players as $client)
		{
		if ($client['Id']!=$clientID)
		$this->wsSend($client['Id'],$send);
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
		$this->wsSend($client['Id'],$send);
		}
	return true;
	}

	public function getPlayerKey($clientID)
	{
	if ($clientID==-1) return -1;
	$roomID = $this->getRoom($clientID);
	$players = $this->getPlayers($roomID);
	foreach ($players as $kplayer=>$player)
		{
		if ($player['Id'] == $clientID) return $kplayer;
		}
	return -1;
	}
	public function getPlayers($roomID)
	{
	if ($roomID == -1 || !isset($this->rooms[$roomID]) )  throw new Exception('[getPlayers] Room is non existent. ('.$roomID.')');
	return $this->rooms[$roomID]['Players'];
	}

	protected function getUserId($clientID)
	{
	if (isset($this->wsClients[$clientID]) && isset($this->wsClients[$clientID]['uid']))
	return $this->wsClients[$clientID]['uid'];
	else
	 throw new Exception('[getUserId] Client uid cannot be found. ('.$clientID.')');
	}


	protected function setRoom($clientID,$roomID)
	{
	$this->wsClients[$clientID]['roomID'] = $roomID;
	return true;
	}

	protected function getRoom($clientID)
	{
	$userID = $this->getUserId($clientID);
	if (!empty($this->users[$userID]['rooms']))
	{
	foreach ($this->users[$userID]['rooms'] as $kroom=>$room)
		{
		if ($room["clientID"] == $clientID)
			{
			return $room["roomID"];
			}
		
		}
	}
	return -1;
	}
	
	protected function getClientRoom($clientID)
	{
	if (isset($this->wsClients[$clientID]['roomID']))
	return $this->wsClients[$clientID]['roomID'];
	else
	return -1;
	}

protected function getDisconnected($roomID)
{
$result = false;
if (isset($this->rooms[$roomID]['Players'])){
	foreach ($this->rooms[$roomID]['Players'] as $kplayer=>$player)
		{
		if ($player['Connected'] == false) $result[]=array('kplayer'=>$kplayer,'UserID'=>$player['UserID']);
		}
return $result;
	}
	throw new Exception('[hasDisconnected] Rooms player table non existent. ('.$roomID.')');
}	
	protected function inReconnects($roomID,$clientID)
	{
	$reconnects = $this->getReconnectRooms($clientID);
	if (is_array($reconnects))
		{
		foreach ($reconnects as $kroom=>$room )
			{
			if ($roomID == $room)
				{
				return true;
				}
			}
		}
	return false;
	}
	protected function getReconnectRooms($clientID)
	{
	$array = -1;
	$userID = $this->getUserId($clientID);
	if (isset($this->users[$userID]['rooms']))
	{
	foreach ($this->users[$userID]['rooms'] as $kroom=>$room)
		{
		$checkID = $room['clientID'];
		if ((isset($this->wsClients[$checkID]) == false) || ($this->wsClients[$checkID]['roomID'] == -1) )
			{
			 if (is_array($array) == false) 
				{
				$array = array();
				}
			$array[] = $room['roomID'];
			}
		
		}
	}
	return $array;
	}

	protected function getUserName($uid)
	{
	if($uid==-1) throw new Exception('[getUserName] User is non existent. ('.$uid.')');
	if ( isset($this->users[$uid]) && isset($this->users[$uid]['Id']) )
		{
		$rid = $this->users[$uid]['Id'];
		return $this->registered[$rid]["Name"];
		}
	return false;
	}

	protected function getClientUserName($clientID)
	{
	if(!isset($this->wsClients[$clientID])) throw new Exception('[getClientUserName] Client is non existent. ('.clientID.')');
	if ($this->wsClients[$clientID]['uid']==-1) return -1;
	if ($this->wsClients[$clientID]['rid']==-1) return -1;
	$rid = $this->wsClients[$clientID]['rid'];
	return $this->registered[$rid]["Name"];
	}
	
	protected function getUserMail($uid)
	{
	if($uid==-1) return -1;
	if (isset($this->users[$uid]) && isset( $this->users[$uid]['Id']))
		{
		$rid = $this->users[$uid]['Id'];
		return $this->registered[$rid]["Mail"];
		}
		else return -1;
	}
	
	protected function isConnected($clientID)
	{
	if ($clientID==-1) return false;
	$roomID = $this->getClientRoom($clientID);
	$kplayer = $this->getPlayerKey($clientID);
	if (!isset($this->rooms[$roomID]['Players'][$kplayer])) return false;
	if ($this->rooms[$roomID]['Players'][$kplayer]['Connected']==true) return true;
	else return false;
	
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

	protected function onOwnerLeave($reason,$clientID){echo "onOwnerLeave";return true;}
	protected function onClientLeave($reason,$clientID){echo "onClientLeave";return true;}
	protected function onPlayerLeave($reason,$clientID){echo "onPlayerLeave";return true;}
	protected function onPlayerReady($clientID){echo "onPlayerReady";return true;}
	protected function onBeforeRoomCreate($clientID){echo "onRoomCreate\n";return true;}
	protected function onGameData($data,$clientID){echo "onGameData\n";return true;}
	protected function onGlobalTimer(){return true;}
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
	$oid = $this->rooms[$roomID]['Owner']['Id'];
	$ownerName = $this->getClientUserName($oid);
	$isPassword = "";
	$isPassword = ($this->rooms[$roomID]['Password']!="")? "true":"false";
	$send = '{"room_new":{"roomName":"'.$this->rooms[$roomID]['roomName'].'","owner":{"name":"'.$ownerName.'","id":'.$oid.'},"id":'.$roomID.',"Password":'.$isPassword.'}}';
	$this->wsSendToIdle($send);
	return true;
	}

	public function GlobalTimer()
	{
	$this->onGlobalTimer();
		if (!empty($this->rooms))
		{
	
			$time = time();
			foreach ($this->rooms as $kroom=>$room)
			{
				if (isset($this->rooms[$kroom]['time']) && isset($this->rooms[$kroom]['Playing']))
				{
					if ($this->rooms[$kroom]['time']!=-1 && $this->rooms[$kroom]['Playing']==true)
					{
						if (isset($this->rooms[$kroom]))
						{
							if ($this->rooms[$kroom]['time']==0)
							{
								$this->rooms[$kroom]['time']=-1;
								$this->dropPlayer($this->rooms[$kroom]['turn'] );
							}
						}
						if (isset($this->rooms[$kroom]['time']))
							$this->rooms[$kroom]['time']-- ;
					}
				}
			}
		}
	}
	
	protected function dropPlayer($clientID)
	{
		$roomID = $this->getRoom($clientID);
		$userID = $this->getUserId($clientID);
	
		echo "[dropPlayer] drop player " . $clientID . "\n";
	
		if ($roomID == -1) throw new Exception('#[dropPlayer] Sorry, room doesnt exist anymore.');
		if ($userID == -1) throw new Exception('#[dropPlayer] Sorry, user for client [$clientID] not found.');
		$this->userLeaveRoom(REASON_ROOM_DROPPED,$clientID,$userID,$roomID);
	}
	
}




