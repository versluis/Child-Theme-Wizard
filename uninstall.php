<?php
// PLugin Starter uninstall script
// deletes all database options when plugin is removed
// @since 1.0
// 
// Direct calls to this file are Forbidden when core files are not present
// Thanks to Ed from ait-pro.com for this  code 

if ( !function_exists('add_action') ){
header('Status: 403 Forbidden');
header('HTTP/1.1 403 Forbidden');
exit();
}

if ( !current_user_can('manage_options') ){
header('Status: 403 Forbidden');
header('HTTP/1.1 403 Forbidden');
exit();
}
// if uninstall is not called from WordPress then exit
if (!defined('WP_UNINSTALL_PLUGIN')) exit();

// delete all options
    delete_option ('zendash_widget1');
	delete_option ('zendash_widget2');
	delete_option ('zendash_widget3');
	delete_option ('zendash_widget4');
	delete_option ('zendash_widget5');
	delete_option ('zendash_widget6');
	delete_option ('zendash_widget7');
	delete_option ('zendash_widget8');

// Thanks for using this plugin
// If you'd like to try again someday check out http://wpguru.co.uk where it lives and grows

?>