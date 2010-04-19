<?php
require_once(dirname(__FILE__).'/global.php');
UserConfig::setDB(new mysqli( $host, $user, $pass, $db));

if ($facebookAPIKey) {
	require_once(dirname(__FILE__).'/users/modules/facebook/index.php');
	UserConfig::$modules[] = new FacebookAuthenticationModule($facebookAPIKey, $facebookSecret);
}

require_once(dirname(__FILE__).'/users/modules/usernamepass/index.php');
UserConfig::$modules[] = new UsernamePasswordAuthenticationModule();

UserConfig::$SESSION_SECRET = $sessionSecret;

UserConfig::$header = dirname(__FILE__).'/header.php';
UserConfig::$footer = dirname(__FILE__).'/footer.php';