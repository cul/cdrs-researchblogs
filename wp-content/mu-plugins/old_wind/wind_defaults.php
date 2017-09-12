<?php

# enable wind plugin?
define('WIND_ENABLE_PLUGIN', 1);

# super admin unis
define('WIND_SUPER_ADMINS', "jws2135 cak2158 dl415 nco2104 elo2112 er2576 tlk2126");

# do you want to check course affiliations? If so, you must set an nra list location
define('WIND_CHECK_COURSE_AFFILS', 1);
define('WIND_NRA_LIST_LOCATION','/lamp/lito/notserved/nra');

# log file location
$wind_wp_path_parts = explode("wp-content/", dirname(__FILE__));
define ('WIND_WP_PATH', $wind_wp_path_parts[0] ); 
define('WIND_LOG_FILE', WIND_WP_PATH . 'wp-content/plugins/wind_plugin/wind.log');

# wind server settings
define('WIND_SERVICE_NAME','edblogs');
define('WIND_SERVER','wind.columbia.edu');
define('WIND_LOGIN_URI','/login');
define('WIND_LOGOUT_URI','/logout');
define('WIND_VALIDATE_URI','/validate');

# email for help questions
define('WIND_HELP_EMAIL','loginhelp@libraries.cul.columbia.edu');
?>