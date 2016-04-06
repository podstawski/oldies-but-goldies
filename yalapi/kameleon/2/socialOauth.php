<?

/**
 * Temporary Requirements:
 * for now, this code requires the oAuth php library 
 * http://oauth.googlecode.com/svn/code/php/
 *
 * This code also requires an active session
 *
 */

require(dirname(__FILE__).'/OAuth.php');

define('activities',	1);
define('albums',		2);
define('attending',		4);
define('books',			8);
define('comments',		16);
define('declined',		32);
define('events',		64);
define('feed',			128);
define('friends',		256);
define('groups',		512);
define('home',			1024);
define('inbox',			2048);
define('insights',		4096);
define('interests',		8192);
define('invited',		16384);
define('likes',			32768);
define('links',			65536);
define('maybe',			131072);
define('members',		262144);
define('movies',		524288);
define('music',			1048576);
define('noreply',		2097152);
define('notes',			4194304);
define('outbox',		8388608);
define('photos',		16777216);
define('picture',		33554432);
define('posts',			67108864);
define('statuses',		134217728);
define('subscriptions',	268435456);
define('tagged',		536870912);
define('television',	1073741824);
define('updates',		2147483648);
define('videos',		4294967296);

define('album', photos + comments);
define('application', feed + posts + tagged + statuses + links + notes + photos + albums + events + videos + picture + insights + subscriptions);
define('event', feed + noreply + maybe + invited + attending + declined + picture);
define('group', feed + members + picture);
define('link', comments);
define('note', comments);
define('page', feed + picture + tagged + links + photos + groups + albums + statuses + videos + notes + posts + events);
define('photo', comments);
define('post', comments);
define('status', comments);
define('user', home + feed + tagged + posts + picture + friends + activities + interests + music + books + movies + television + likes + photos + albums + videos + groups + statuses + links + notes + events + inbox + outbox + updates);
define('video', comments);

define('COOKIE', '_stCookie');
define('SESSION', '_stSession');
define('MYSQL', '_stMysql');

class storage{
  private $storeType;
  private $_validStores = array(COOKIE,SESSION,MYSQL);
  
  public function __construct($store = COOKIE){
    $this->setStoreType($store);
  }
  
  public function setStoreType($store){
    $this->storeType = $store;
  }
  
  public function attr($k, $v=NULL){
    $store = $this->storeType;
    $this->$store($k,$v);
  }

  private function _stCookie($k, $v=NULL){
    if( $v === NULL ){
      return $_COOKIE[$k];
	}else{
	  setcookie($k,$v);
	}
  }
  private function _stSession($k, $v=NULL){
    if( $v === NULL ){
      return $_SESSION[$k];      
	}else{
      $_SESSION[$k] = $v;
	}
  }
  private function _stMysql($k, $v=NULL){
    if( $v === NULL ){
      //return data
	}else{
	  //set data
	}
  }


}

class socialOauth{
  public $serviceName;
  protected $userTempalte = array('username'=>false,);
  private $loadFrom;
  private $consunmer;
  private $classPrefix = 'so_';
  #protected $storage = '_COOKIE'; // or _COOKIE
  
  private $connecttimeout;
  private $timeout;
  private $ssl_verifypeer;
  
  public $token = false;
  
  protected $store;

  public function __construct($service){
    $so = $this->classPrefix.$service;
	#$this->store = new storage(COOKIE);
    if( class_exists($so) ){
      $this->service = new $so(COOKIE);
	}else{
	  die('unknown service [1]');
	}
	$this->prepLoad($service);
  }

  private function prepLoad($service){
    $this->serviceName = $service;
    $settings = $this->getSettings();
    if($settings[$service]){
	  $this->service->settings = $settings[$service];
      $this->service->connecttimeout = $settings['connecttimeout'];
	  $this->service->timeout = $settings['timeout'];
	  $this->service->ssl_verifypeer = $settings['ssl_verifypeer'];
	  $this->service->settings = $settings[$service];
      //
	  $this->service->prep();
      //
	  #$this->service->login();
    }else{
      die('unknown service');
	}
  }
  
  public function login(){
    return $this->service->login();
  }

  public function requestToken(){
    socialOauth::redirect($this->service->requestToken());
  }
  
  public function getAccessToken($oauth_token, $oauth_verifier){
    return $this->service->getAccessToken($oauth_token, $oauth_verifier);
  }



  public function redirect($url){
		header('Location: '.$url);
		exit();
  }

  public function request($obj, $params=array(), $typeExplicit=false){
    return $this->service->get($obj, $params, $typeExplicit);
  }
  
  public function me(){
    switch($this->serviceName){
		case 'facebook':
			return $this->service->get('me');		
			break;	
		case 'twitter':
			return $this->service->get('tendrid');
			break;
		case 'default':
			die('this shouldnt happen');
			break;

	}
  }

  public function getSettings($settings=false){
    if( $settings == false ){
      return parse_ini_file('settings.ini', true);
	}else{
	  // TODO: check to make sure the settings match the ini layout.
      return $settings;
	}
  }
  
  function http($url, $method='GET', $postfields = NULL) {
    $curl = curl_init();
    curl_setopt_array($curl, array(
      CURLOPT_CONNECTTIMEOUT => $this->connecttimeout,
      CURLOPT_TIMEOUT        => $this->timeout,
      CURLOPT_RETURNTRANSFER => true,
      CURLOPT_URL            => $url,
	  CURLOPT_SSL_VERIFYPEER => $this->ssl_verifypeer,
	  CURLOPT_AUTOREFERER    => true,
	  CURLOPT_FOLLOWLOCATION => true
    ));

    switch (strtoupper($method)) {
      case 'GET':
        curl_setopt($curl, CURLOPT_POST, FALSE);
        if (!empty($postfields)) {
          curl_setopt($curl, CURLOPT_URL, $url.'&'.http_build_query($postfields));
        }
        break;
      case 'POST':
        curl_setopt($curl, CURLOPT_POST, TRUE);
        if (!empty($postfields)) {
          curl_setopt($curl, CURLOPT_POSTFIELDS, $postfields);
        }
        break;
      case 'DELETE':
        curl_setopt($curl, CURLOPT_CUSTOMREQUEST, 'DELETE');
        if (!empty($postfields)) {
          $url = "{$url}?{$postfields}";
        }
    }

    $result = curl_exec($curl);
    curl_close($curl);
    return $result; 
  }
}

class so_twitter{
  public $settings = array();
  public $token = false;
  private $store;

  public function __construct($store){
    $this->store = new storage($store);
  }

  public function prep(){
    $this->sha1_method = new OAuthSignatureMethod_HMAC_SHA1();
    $this->consumer = new OAuthConsumer(  $this->settings['consumer_key'],
                                          $this->settings['consumer_secret'],
                                          $this->settings['callback_url'] );  
  }
  
  public function login(){
    if( !isset($_GET['oauth_token'],$_GET['oauth_verifier']) ){
      $this->requestToken();
    }else{
      $this->getAccessToken($_GET['oauth_token'], $_GET['oauth_verifier']);
      return $this->get('account/verify_credentials');
    }
  }

  function get($item, $parameters = array()) {
    $response = $this->oAuthRequest($item, 'GET', $parameters);
    return json_decode($response);
  }

  function oAuthRequest($url, $method, $parameters) {
    if (strrpos($url, 'https://') !== 0 && strrpos($url, 'http://') !== 0) {
      $url = "{$this->settings['URL_api']}{$url}.json";
    }
    $request = OAuthRequest::from_consumer_and_token($this->consumer, $this->token, $method, $url, $parameters);
    $request->sign_request($this->sha1_method, $this->consumer, $this->token);
    switch ($method) {
    case 'GET':
      return socialOauth::http($request->to_url(), 'GET');
    default:
      return socialOauth::http($request->get_normalized_http_url(), $method, $request->to_postdata());
    }
  }

  public function requestToken(){
    #$storedData = $GLOBALS[$this->storage];
    $request = $this->oAuthRequest( $this->settings['URL_requestToken'],
                                    'GET',
                                    array('oauth_callback'=>$this->settings['callback_url']) );
    $token = OAuthUtil::parse_parameters($request);
    $this->token = new OAuthConsumer($token['oauth_token'], $token['oauth_token_secret']);
    #$GLOBALS[$this->storage]['oauth_token_secret'] = $token['oauth_token_secret'];
    $this->store->attr('oauth_token_secret', $token['oauth_token_secret']);
    #return $this->settings['URL_authenticate'].'?oauth_token='.$token['oauth_token'];
    socialOauth::redirect($this->settings['URL_authenticate'].'?oauth_token='.$token['oauth_token']);
  }
  
  public function getAccessToken($oauth_token, $oauth_verifier){
    #$storedData = $GLOBALS[$this->storage];
	#var_dump($GLOBALS,$_SESSION);die();
    #$this->token = new OAuthConsumer($oauth_token, $storedData['oauth_token_secret']);
    $this->token = new OAuthConsumer($oauth_token, $this->store->attr('oauth_token_secret'));
    $request = $this->oAuthRequest( $this->settings['URL_accessToken'],
                                    'GET',
                                    array('oauth_verifier'=>$oauth_verifier) );
    $token = OAuthUtil::parse_parameters($request);
    $this->token = new OAuthConsumer($token['oauth_token'], $token['oauth_token_secret']);
    return $token;
  }

}

class so_facebook{
  public $settings = array();
  private $service = 'facebook';
  private $store;

  public function __construct($store){
    $this->store = $store;
  }

  public function prep(){
    $this->settings['permissions'] = explode(',',$this->settings['permissions']);
    $this->settings['required_permissions'] = explode(',',$this->settings['required_permissions']);
  }
  
  public function login(){
    if( !isset($_GET['code']) ){
      socialOauth::redirect('https://graph.facebook.com/oauth/authorize?client_id='.$this->settings['consumer_key'].'&redirect_uri='.$this->settings['callback_url']);
    }elseif( isset($_GET['verified']) ){
      //pudel's test: 
		//$this->oauth_token_secret = $_GET['verified']?:$_GET['code'];
		$this->oauth_token_secret = $_GET['verified'];

	}else{
	  $out = json_decode(socialOauth::http("https://graph.facebook.com/oauth/access_token?&client_id=".$this->settings['consumer_key']."&client_secret=".$this->settings['consumer_secret']."&code=".$_GET['code']."&redirect_uri=".$this->settings['callback_url'],'GET'));
	  if( $this->_hasError($out) ){
        die($out->error->type.': '.$out->error->message);
      }
      $this->oauth_token = $_GET['code'];
	  $this->oauth_token_secret = str_replace('access_token=','',$out);
	}
	$item = 'me';
	
	$out = json_decode(socialOauth::http("https://graph.facebook.com/{$item}?access_token=".$this->oauth_token_secret));
	$this->checkPerms($out->id);
	#var_dump($this->oauth_token_secret);
	return $out;
  }
  
  function get($item, $parameters = array(), $typeExplicit=false ){
    $parameters['metadata']=1;
    $out = json_decode(socialOauth::http($this->settings['URL_api'].$item.'?access_token='.$this->oauth_token_secret, 'GET', $parameters),true);
	unset($out['metadata']);
    if( $typeExplicit != false ){
      if( defined($out['type']) ){
	    if( constant($out['type']) != $typeExplicit ){
		  die('type missmatch');
		}
	  }else{
	    die('unsupported type');
	  }
    }
	return $out;
  }
  
  private function _hasError($data){
    if( is_object($data) && isset($data->error) ){
	  return true;
	}else{
	  return false;
	}
  }

  private function checkPerms($fbid){
    $failed = array();
    foreach($this->settings['required_permissions'] as $key => $val){
	  $url = 'https://api.facebook.com/method/users.hasAppPermission?uids='.$fbid.'&ext_perm='.trim($val).'&access_token='.$this->oauth_token_secret;
	  $out = socialOauth::http($url);
	  if( strstr($out,'Method Not Implemented') ){
	    die('invalid permissions type');
	  }
	  $perm = simplexml_load_string($out);
	  if( $perm[0] != 1 ){
	    $failed[] = trim($val);
	  }
	}
    if(count($failed)>0){
      socialOauth::redirect( $this->settings['URL_authorize'].'?client_id='.$this->settings['consumer_key'].'&scope='.implode(',',$failed).'&redirect_uri='.$this->settings['callback_url'].'?verified='.$this->oauth_token_secret );
	}
    
  }
  
}


/* USER

id = 

*/


/* TWITTER USER

object(stdClass)#6 (29) {
  ["statuses_count"]=>
  int(991)
  ["profile_background_image_url"]=>
  string(59) "http://s.twimg.com/a/1273694933/images/themes/theme9/bg.gif"
  ["profile_sidebar_fill_color"]=>
  string(6) "252429"
  ["description"]=>
  string(62) "I once fought a dragon with my left hand taped behind my soul."
  ["screen_name"]=>
  string(7) "Tendrid"
  ["lang"]=>
  string(2) "en"
  ["followers_count"]=>
  int(159)
  ["status"]=>
  object(stdClass)#8 (13) {
    ["in_reply_to_screen_name"]=>
    string(15) "ToasterSunshine"
    ["favorited"]=>
    bool(false)
    ["truncated"]=>
    bool(false)
    ["in_reply_to_status_id"]=>
    float(14671360888)
    ["source"]=>
    string(63) "<a href="http://www.tweetdeck.com" rel="nofollow">TweetDeck</a>"
    ["created_at"]=>
    string(30) "Tue May 25 17:59:58 +0000 2010"
    ["coordinates"]=>
    NULL
    ["place"]=>
    NULL
    ["geo"]=>
    NULL
    ["in_reply_to_user_id"]=>
    int(23716492)
    ["contributors"]=>
    NULL
    ["id"]=>
    float(14708489330)
    ["text"]=>
    string(67) "@ToasterSunshine Lets talk about this later today.  I'll email you."
  }
  ["contributors_enabled"]=>
  bool(false)
  ["profile_background_tile"]=>
  bool(false)
  ["friends_count"]=>
  int(94)
  ["following"]=>
  bool(false)
  ["created_at"]=>
  string(30) "Wed Apr 23 13:53:40 +0000 2008"
  ["geo_enabled"]=>
  bool(false)
  ["profile_sidebar_border_color"]=>
  string(6) "181A1E"
  ["verified"]=>
  bool(false)
  ["notifications"]=>
  bool(false)
  ["profile_link_color"]=>
  string(6) "2FC2EF"
  ["profile_background_color"]=>
  string(6) "1A1B1F"
  ["protected"]=>
  bool(false)
  ["url"]=>
  string(22) "http://peoplebacon.com"
  ["time_zone"]=>
  string(5) "Quito"
  ["favourites_count"]=>
  int(0)
  ["profile_text_color"]=>
  string(6) "666666"
  ["name"]=>
  string(7) "Tendrid"
  ["profile_image_url"]=>
  string(119) "http://a1.twimg.com/profile_images/53195756/r5nPSuP7KmWdSl-4OaFqI-BjdFvrt8ByT_5zBtYOwPdvUOrEDPXTcErKXCM6vG_y_normal.jpg"
  ["location"]=>
  string(13) "Ann Arbor, MI"
  ["id"]=>
  int(14493751)
  ["utc_offset"]=>
  int(-18000)
}

*/


/* FACEBOOK USER

object(stdClass)#4 (17) {
  ["id"]=>
  string(10) "1015137324"
  ["name"]=>
  string(11) "Jim Deakins"
  ["first_name"]=>
  string(3) "Jim"
  ["last_name"]=>
  string(7) "Deakins"
  ["link"]=>
  string(31) "http://www.facebook.com/tendrid"
  ["birthday"]=>
  string(10) "06/02/1979"
  ["location"]=>
  object(stdClass)#5 (2) {
    ["id"]=>
    float(105479049486620)
    ["name"]=>
    string(19) "Ann Arbor, Michigan"
  }
  ["work"]=>
  array(1) {
    [0]=>
    object(stdClass)#6 (4) {
      ["employer"]=>
      object(stdClass)#7 (2) {
        ["id"]=>
        float(113730041976890)
        ["name"]=>
        string(12) "AnnArbor.com"
      }
      ["location"]=>
      object(stdClass)#8 (2) {
        ["id"]=>
        float(105479049486620)
        ["name"]=>
        string(19) "Ann Arbor, Michigan"
      }
      ["position"]=>
      object(stdClass)#9 (2) {
        ["id"]=>
        float(106351599400540)
        ["name"]=>
        string(32) "Project Manager / Lead Developer"
      }
      ["start_date"]=>
      string(7) "2009-07"
    }
  }
  ["gender"]=>
  string(4) "male"
  ["interested_in"]=>
  array(1) {
    [0]=>
    string(6) "female"
  }
  ["relationship_status"]=>
  string(7) "Married"
  ["significant_other"]=>
  object(stdClass)#10 (2) {
    ["name"]=>
    string(14) "Lauren Deakins"
    ["id"]=>
    string(8) "38503261"
  }
  ["political"]=>
  string(7) "Liberal"
  ["website"]=>
  string(42) "http://twitter.com/Tendrid
http://lyfe.net"
  ["timezone"]=>
  int(-4)
  ["verified"]=>
  bool(true)
  ["updated_time"]=>
  string(24) "2010-02-20T04:38:27+0000"
}

*/

?>