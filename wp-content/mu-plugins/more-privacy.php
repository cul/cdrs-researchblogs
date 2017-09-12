<?php
/*
Plugin Name:   More Privacy Options
Version:       2.9.1
Description:   Adds more privacy options to the options-privacy and wpmu-blogs pages. Sitewide "Users Only" switch at SiteAdmin-->Options page. Just drop in mu-plugins.
Author:        D Sader
Author URI:    http://www.snowotherway.org/

 This program is free software; you can redistribute it and/or modify
 it under the terms of the GNU General Public License as published by
 the Free Software Foundation; either version 2 of the License, or
 (at your option) any later version.

 This program is distributed in the hope that it will be useful,
 but WITHOUT ANY WARRANTY; without even the implied warranty of
 MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.  See the
 GNU General Public License for more details.

TODO add if(is_feed) graceful_fail( 'Private Blog' ); // if is valid xml?

2.7 added a Sitewide "Users Only" switch to SiteAdmin-->Options, props Boonika
2.8 added action hook for writing robots.txt exclusions, props Zinj Guo 
	http://en.wikipedia.org/wiki/Robots.txt
2.9 tweaked for WPMU 2.7
2.9.1 added feed authentication so registered_users get feeds from feed readers such as Safari/Mail. Props: Greg
	
*/
//------------------------------------------------------------------------//
//---Hooks-----------------------------------------------------------------//
//------------------------------------------------------------------------//
// hooks into Misc Blog Actions in wpmu_blogs.php.
add_action('wpmueditblogaction', 'wpmu_blogs_add_privacy_options');
// add_action('wpmublogsaction', 'wpmu_blogs_add_privacy_options_messages' );

// hook into options-privacy.php.
add_action('blog_privacy_selector', 'add_privacy_options');

// all three add_privacy_option get a redirect and a message
		$number = intval(get_site_option('ds_sitewide_privacy'));

if (( '-1' == $current_blog->public ) || ($number == '-1')) { // add exclusion of main blog if desired
	add_action('template_redirect', 'ds_users_authenticator');
	add_action('login_form', 'registered_users_login_message'); 
}
if ( '-2' == $current_blog->public ) {
	add_action('template_redirect', 'ds_members_authenticator');
	add_action('login_form', 'registered_members_login_message'); 
}
if ( '-3' == $current_blog->public ) {
	add_action('template_redirect', 'ds_admins_authenticator');
	add_action('login_form', 'registered_admins_login_message');

}

// hook robot.txt props Zinj Guo
remove_action('do_robots', 'do_robots');
add_action('do_robots', 'writerobottxt');
function writerobottxt(){
	global $current_blog;
	header('Content-Type: text/plain; charset=utf-8');
	do_action( 'do_robotstxt' );

	if ( '0' >= $current_blog->public ) {
		echo "User-agent: *\n";
		echo "Disallow: /\n";
	} else {
		echo "User-agent: *\n";
		echo "Disallow:\n";
	}
}
//------------------------------------------------------------------------//
//---Functions hooked into wpmu-blogs.php---------------------------------//
//---TODO add messages to wpmu-blogs.php table----------------------------//
function wpmu_blogs_add_privacy_options() { 
	global $details;
	?>
<h3>More Privacy Options</h3>
		<input type='radio' name='blog[public]' value='1' <?php if( $details[ 'public' ] == '1' ) echo " checked"?>> <?php _e('Google-able') ?>&nbsp;&nbsp;
<br />
	    <input type='radio' name='blog[public]' value='0' <?php if( $details[ 'public' ] == '0' ) echo " checked"?>> <?php _e('No Google') ?> &nbsp;&nbsp;	    
<br />
	    <input type='radio' name='blog[public]' value='-1' <?php if( $details[ 'public' ] == '-1' ) echo " checked"?>> <?php _e('Site Registered Users Only') ?> &nbsp;&nbsp;
<br />
	    <input type='radio' name='blog[public]' value='-2' <?php if( $details[ 'public' ] == '-2' ) echo " checked"?>> <?php _e('Blog Members Only') ?> &nbsp;&nbsp;
<br />
	    <input type='radio' name='blog[public]' value='-3' <?php if( $details[ 'public' ] == '-3' ) echo " checked"?>> <?php _e('Blog Admins Only') ?> &nbsp;&nbsp;
<br />
<br />
	<?php
}
function wpmu_blogs_add_privacy_options_messages() {
global $blog;
if ( '1' == $blog[ 'public' ] ) {
_e('Visible(1)');
}
if ( '0' == $blog[ 'public' ] ) {
_e('No Search(0)');
}
if ( '-1' == $blog[ 'public' ] ) {
_e('Users Only(-1)');
}
if ( '-2' == $blog[ 'public' ] ) {
_e('Members Only(-2)');
}
if ( '-3' == $blog[ 'public' ] ) {
_e('Admins Only(-3)');
}
echo '<br class="clear" />';
}

//------------------------------------------------------------------------//
//---Functions hooked into blog privacy selector(options-privacy.php)-----//
//------------------------------------------------------------------------//
function add_privacy_options($options) { ?>
<p>
<input id="blog-private" type="radio" name="blog_public" value="-1" <?php checked('-1', get_option('blog_public')); ?> />
<label for="blog-private"><?php _e('I would like my blog to be visible only to registered users from blog community'); ?></label>
</p>
<p>
<input id="blog-private" type="radio" name="blog_public" value="-2" <?php checked('-2', get_option('blog_public')); ?> />
<label for="blog-private"><?php _e('I would like my blog to be visible only to <a href="users.php">registered members</a> of this blog'); ?></label>
</p>
<p>
<input id="blog-private" type="radio" name="blog_public" value="-3" <?php checked('-3', get_option('blog_public')); ?> />
<label for="blog-private"><?php _e('I would like my blog to be visible only to administrators'); ?></label>
</p>
<?php 
}

//------------------------------------------------------------------------//
//---Functions for Registered Community Users Only Blog-------------------//
//------------------------------------------------------------------------//
function ds_users_authenticator () {
global $user_level;
if ( !isset($user_level) ) {
      if( is_feed()) {
           $credentials = array();
           $credentials['user_login'] = $_SERVER['PHP_AUTH_USER'];
           $credentials['user_password'] = $_SERVER['PHP_AUTH_PW'];

           $user = wp_signon( $credentials );

           if ( is_wp_error( $user ) )
           {
                header( 'WWW-Authenticate: Basic realm="' . $_SERVER['SERVER_NAME'] . '"' );
                header( 'HTTP/1.0 401 Unauthorized' );
                die();
           }
       } else {
		nocache_headers();
		header("HTTP/1.1 302 Moved Temporarily");
		header('Location: ' . get_settings('siteurl') . '/wp-login.php?redirect_to=' . urlencode($_SERVER['REQUEST_URI']));
        	header("Status: 302 Moved Temporarily");
		exit();
		}
	}
}
function registered_users_login_message () {
echo '<p>';
echo '' . bloginfo(name) . ' can be viewed by registered users of this community only.';
echo '</p>';
}

//------------------------------------------------------------------------//
//---Shortcut Function for logged in users to add timed "refresh"--------//
//------------------------------------------------------------------------//
function ds_login_header() {
			nocache_headers();
			header( 'Content-Type: text/html; charset=utf-8' );
		?>
		<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Transitional//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-transitional.dtd">
		<html xmlns="http://www.w3.org/1999/xhtml" <?php if ( function_exists('language_attributes') ) language_attributes(); ?>>
			<head>
				<title><?php _e("Private Blog Message"); ?></title>
				<meta http-equiv="refresh" content="5;URL=<?php echo get_settings('siteurl'); ?>/wp-login.php" />
				<?php wp_admin_css( 'css/login' );
				wp_admin_css( 'css/colors-fresh' );	?>				
				<link rel="stylesheet" href="css/install.css" type="text/css" />
				<?php do_action('login_head'); ?>
			</head>
			<body class="login">
				<div id="login">
					<h1><a href="<?php echo apply_filters('login_headerurl', 'http://' . $current_site->domain . $current_site->path ); ?>" title="<?php echo apply_filters('login_headertitle', $current_site->site_name ); ?>"><span class="hide"><?php bloginfo('name'); ?></span></a></h1>
<?php
}

//------------------------------------------------------------------------//
//---Functions for Members Only Blog---------------------------------------//
//------------------------------------------------------------------------//
function ds_members_authenticator () {
global $user_level, $current_user;
if (( isset($user_level) ) && (!current_user_can('read'))) {
			ds_login_header(); ?>
					<form name="loginform" id="loginform" /><p>Wait 5 seconds or <a href="<?php echo get_settings('siteurl'); ?>/wp-login.php">click</a> to continue.</p><?php registered_members_login_message (); ?>
					</form>
				</div>
			</body>
		</html>
		<?php 
		exit();
	} elseif (!current_user_can('read')) {
		nocache_headers();
		header("HTTP/1.1 302 Moved Temporarily");
		header('Location: ' . get_settings('siteurl') . '/wp-login.php?redirect_to=' . urlencode($_SERVER['REQUEST_URI']));
        header("Status: 302 Moved Temporarily");
		exit();
	}
}
function registered_members_login_message () {
echo '<p>';
echo '' . bloginfo(name) . __(' can be viewed by members of this blog only.');
echo '</p>';
}

//-----------------------------------------------------------------------//
//---Functions for Admins Only Blog--------------------------------------//
//---WARNING: member users, if they exist, still see the backend---------//
function ds_admins_authenticator () {
global $user_level;
	if (( isset($user_level) ) && (!current_user_can('manage_options'))) {
			ds_login_header(); ?>
					<form name="loginform" id="loginform" /><p>Wait 5 seconds or <a href="<?php echo get_settings('siteurl'); ?>/wp-login.php">click</a> to continue.</p><?php registered_admins_login_message (); ?>
					</form>
				</div>
			</body>
		</html>
		<?php 
		exit();
	} elseif (!current_user_can('manage_options')) {
		nocache_headers();
		header("HTTP/1.1 302 Moved Temporarily");
		header('Location: ' . get_settings('siteurl') . '/wp-login.php?redirect_to=' . urlencode($_SERVER['REQUEST_URI']));
        header("Status: 302 Moved Temporarily");
		exit();
	}
}
function registered_admins_login_message () {
echo '<p>';
echo '' . bloginfo(name) . __(' can be viewed by administrators only.');
echo '</p>';
}

//-----------------------------------------------------------------------//
//---Functions for SiteAdmins Options--------------------------------------//
//---WARNING: member users, if they exist, still see the backend---------//
	function sitewide_privacy_options_page() {
		$number = intval(get_site_option('ds_sitewide_privacy'));
		if ( !isset($number) ) {
			$number = '1';
		}
		echo '<h3>Sitewide Privacy Selector</h3>';
		echo '<table class="form-table"><tr valign="top"> 
			<th scope="row">' . __('Site Privacy') . '</th>';
			$checked = ( $number == "-1" ) ? " checked=''" : "";
		echo '<td><input type="radio" name="ds_sitewide_privacy" id="ds_sitewide_privacy" value="-1" ' . $checked . '/><br /><small>' . __('Site can be viewed by registered users of this community only.') . '</small></td>';
			$checked = ( $number == "1" ) ? " checked=''" : "";
		echo '<td><input type="radio" name="ds_sitewide_privacy" id="ds_sitewide_privacy_1" value="1" ' . $checked . '/><br /><small>' . __('Default: privacy managed per blog.') . '</small></td>
		</tr></table>'; 
	}
	function sitewide_privacy_update() {
		update_site_option('ds_sitewide_privacy', $_POST['ds_sitewide_privacy']);
	}
	add_action( 'update_wpmu_options', 'sitewide_privacy_update');
	add_action( 'wpmu_options', 'sitewide_privacy_options_page');
?>