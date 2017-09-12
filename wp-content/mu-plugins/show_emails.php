<?php
/*
Plugin Name: Flush Permalinks Cache
Version: .1, 2010-02-23
Description: Lets you flush the permalinks cache for all blogs
Author: nco2104@columbia.edu
*/

// add link to "Settings"
add_action('network_admin_menu', 'showemails_addAdminPage');

function showemails_addAdminPage() {
        if (function_exists('add_submenu_page')) {        
add_submenu_page('users.php', 'Admins Email List', 'Admins Email List', 10, 'show_all_blog_emails', 'show_all_emails');
} 
} //showemails_addAdminPage


// print out the page to flush all the permalinks
function show_all_emails () {
global $wpdb, $wp_rewrite;
print "<h2>Contact Emails for All Blogs</h2>";
print "Note: these lists exclude emails containing '+', since those are generally duplicates for site admins.";

print "<h4>Administrators and Editors on all blogs</h4>";
$all_results = $wpdb->get_results( 'select user_email from wp_users where ID in (select user_id from wp_usermeta where meta_key like "%capabilities" and (meta_value like "%administrator%" or meta_value like "%editor%")) and user_email not like "%+%" order by user_email');
foreach ($all_results as $this_row) {
			print $this_row->user_email . ", ";	

	}


print "<h4>Administrators Only, on all blogs</h4>";
$all_results = $wpdb->get_results( 'select user_email from wp_users where ID in (select user_id from wp_usermeta where meta_key like "%capabilities" and meta_value like "%administrator%") and user_email not like "%+%" order by user_email');
foreach ($all_results as $this_row) {
                        print $this_row->user_email . ", ";

        }

} //end function show_all_emails

?>
