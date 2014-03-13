<?php
/**
 * Plugin Name: Child Theme Wizard
 * Plugin URI: http://wpguru.co.uk
 * Description: Creates a child theme from any theme you have installed
 * Version: 1.0
 * Author: Jay Versluis
 * Author URI: http://wpguru.co.uk
 * License: GPL2
 */
 
/*  Copyright 2013  Jay Versluis (email support@wpguru.co.uk)

    This program is free software; you can redistribute it and/or modify
    it under the terms of the GNU General Public License, version 2, as 
    published by the Free Software Foundation.

    This program is distributed in the hope that it will be useful,
    but WITHOUT ANY WARRANTY; without even the implied warranty of
    MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.  See the
    GNU General Public License for more details.

    You should have received a copy of the GNU General Public License
    along with this program; if not, write to the Free Software
    Foundation, Inc., 51 Franklin St, Fifth Floor, Boston, MA  02110-1301  USA
*/

// Add a new submenu under DASHBOARD
function ctw_menu() {
	
	// using a wrapper function (easy, but not good for adding JS later - hence not used)
	// add_dashboard_page('Plugin Starter', 'Plugin Starter', 'administrator', 'pluginStarter', 'pluginStarter');
	
	// using array - same outcome, and can call JS with it
	// explained here: http://codex.wordpress.org/Function_Reference/wp_enqueue_script
	// and here: http://pippinsplugins.com/loading-scripts-correctly-in-the-wordpress-admin/
	global $starter_plugin_admin_page;
	$starter_plugin_admin_page = add_submenu_page ('tools.php', __('Child Theme Wizard', 'ctw'), __('Child Theme Wizard', 'ctw'), 'manage_options', 'ChildThemeWizard', 'ctwMainFunction');
}
add_action('admin_menu', 'ctw_menu');


////////////////////////////////////////////
// here's the code for the actual admin page
function ctwMainFunction () {
	
// check that the user has the required capability 
    if (!current_user_can('manage_options'))
    {
      wp_die( __('You do not have sufficient privileges to access this page. Sorry!') );
    }	
	
	///////////////////////////////////////
	// MAIN AMDIN CONTENT SECTION
	///////////////////////////////////////
	
	// display heading with icon WP style
	?>
    <div class="wrap">
<div id="icon-index" class="icon32"><br></div>
    <h2>Child Theme Wizard</h2>
    
    <?php
    
	// check if the button has been pressed 
	if( isset($_POST[ 'hiddenfield' ]) && $_POST[ 'hiddenfield' ] == 'Y' ) {
		
		// call function with sanitized values
		$newchildtheme = array(
		'parent'      => $_POST['parent'],
		'title'       => sanitize_text_field($_POST['title']),
		'description' => sanitize_text_field($_POST['description']),
		'url'         => sanitize_text_field($_POST['url']),
		'author'      => sanitize_text_field($_POST['author']),
		'author-url'  => sanitize_text_field($_POST['author-url']),
		'version'     => sanitize_text_field($_POST['version']),
		'include-gpl' => $_POST['include-gpl'],
		);
		
		// let's see if this worked
		// ctw_create_theme($newchildtheme);
		ctw_testing($newchildtheme);
		
		// Put a "settings updated" message on the screen 
		?>
		<div class="updated">
		  <p><strong>
		  <?php _e('Create button was clicked!', 'ctw' ); ?>
          </strong></p>
		</div>
		<?php } else {
		?>

    <p>This simple wizard will help you generate a new <a href="https://codex.wordpress.org/Child_Themes" target="_blank">Child Theme</a> with just one click. </p>
    <p>Seletct which theme you want to use as a base, fill in the details and click &quot;Create Child Theme&quot;.</p>
    
    <form name="ctwform" method="post" action="">
    <input type="hidden" name="hiddenfield" value="Y">
    
    
    <?php
	
	// fields for author and title
	// we can pre-populate some data too
	$current_user = wp_get_current_user();
	?>
    <table width="600" border="0">
      <tr>
        <td>Parent Theme</td>
        <td><?php
        // grab a list of all parent themes and iterate through them
		// $themes = wp_get_themes();
		$themes = ctw_giveme_parents();
		echo '<select name="parent">';
		foreach ($themes as $theme) {
			echo '<option value ="' . $theme->get_stylesheet() . '">' . $theme->get('Name') . '</option>';
		}
		echo '</select>';
        ?></td>
        <td><em></em></td>
      </tr>
      <tr>
        <td>Title</td>
        <td><input type="text" name="title" value="" size="20"></td>
        <td><em>what's your new Child Theme called?</em></td>
      </tr>
      <tr>
        <td>Description</td>
        <td><input type="text" name="description" value="" size="20"></td>
        <td><em>a few notes about your Child Theme</em></td>
      </tr>
      <tr>
        <td>Child Theme URL</td>
        <td><input type="text" name="url" value="" size="20"></td>
        <td><em>does it have a website or release post?</em></td>
      </tr>
      <tr>
        <td>Author</td>
        <td><input type="text" name="author" value="<?php echo $current_user->display_name; ?>" size="20"></td>
        <td><em>that's you</em></td>
      </tr>
      <tr>
        <td>Author URL</td>
        <td><input type="text" name="author-url" value="<?php echo $current_user->user_url; ?>" size="20"></td>
        <td><em>that's your website</em></td>
      </tr>
      <tr>
        <td>Version</td>
        <td><input type="text" name="version" value="1.0" size="20"></td>
        <td><em>start with 1.0 and work your way up</em></td>
      </tr>
      <tr>
        <td>Include GPL License</td>
        <td>
        <select name="include-gpl">
        <option value="on">Yes Please!</option>
        <option value="off">No Thanks</option>
        </select>
        </td>
        <td><em></em></td>
      </tr>
    </table>
    
    <p class="submit">
    <input type="submit" name="Submit" class="button-primary" value="<?php esc_attr_e('Create Child Theme') ?>" />
    </p>
    
    <?php // end of else
		}
		?>
    
<div>
  <ul>
        <li>DONE: List all available themes as drop down</li>
        <li>DONE: let user enter values</li>
        <li>DONE: submit button </li>
        <li>DONE: sanitize text fields</li>
        <li>DONE: function that creates the child theme directory and file</li>
        <li>write contents to file</li>
        <li>create screenshot</li>
        <li>DONE: move to Tools and correct main function name</li>
        <li>update footer links</li>
        <li>DONE: create Git reop</li>
        <li>DONE: filter out child themes
     
        </li>
  </ul>
</div>
    </div> <!-- end of main wrap -->

<?php // display the footer ?>
	<p><a href="http://wpguru.co.uk" target="_blank"><img src="<?php  
	echo plugins_url('images/guru-header-2013.png', __FILE__); ?>" width="300"></a> </p>

<p><a href="http://wpguru.co.uk/2010/12/disk-space-pie-chart-plugin/" target="_blank">Plugin by Jay Versluis</a> | <a href="http://www.peters1.dk/webtools/php/lagkage.php?sprog=en" target="_blank">Pie Chart Script by Rasmus Peters</a> | <a href="http://wphosting.tv" target="_blank">WP Hosting</a></p>

<p><span><!-- Social Buttons -->

<!-- Google+ -->
<div class="g-follow" data-annotation="bubble" data-height="20" data-href="//plus.google.com/116464794189222694062" data-rel="author"></div>

<!-- Place this tag after the last widget tag. -->
<script type="text/javascript">
  (function() {
    var po = document.createElement('script'); po.type = 'text/javascript'; po.async = true;
    po.src = 'https://apis.google.com/js/platform.js';
    var s = document.getElementsByTagName('script')[0]; s.parentNode.insertBefore(po, s);
  })();
</script>

&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;

<!-- Twitter -->
<a href="https://twitter.com/versluis" class="twitter-follow-button" data-show-count="true">Follow @versluis</a>
<script>!function(d,s,id){var js,fjs=d.getElementsByTagName(s)[0],p=/^http:/.test(d.location)?'http':'https';if(!d.getElementById(id)){js=d.createElement(s);js.id=id;js.src=p+'://platform.twitter.com/widgets.js';fjs.parentNode.insertBefore(js,fjs);}}(document, 'script', 'twitter-wjs');</script>

<!-- Facebook -->
<iframe src="//www.facebook.com/plugins/like.php?href=https%3A%2F%2Fwww.facebook.com%2Fpages%2FThe-WP-Guru%2F162188713810370&amp;width&amp;layout=button_count&amp;action=like&amp;show_faces=true&amp;share=true&amp;height=21&amp;appId=186277158097599" scrolling="no" frameborder="0" style="border:none; overflow:hidden; height:21px;" allowTransparency="true"></iframe>

</span></p>

<?php
} // end of main function

/* 
 * this function creates the new directory and file
 */
 
function ctw_create_theme($childtheme) {
	echo '<p>This function is outside the main function.</p>';
	
	// create the directory
	$directory = get_theme_root() . '/' . sanitize_file_name($childtheme['title']);
	echo '<p>The child theme directory will be created at ' . $directory;
	if (!mkdir($directory)) {
		echo '<p>Something went wrong!</p>';
	} else {
		echo '<p>Created directory successfully</p>';
	}
	
	// create a file in our directory
	$filename = $directory . '/' . 'style.css';
	$handle = fopen($filename, 'w');
	
	// add meta data 
	$data = "/* \n Theme Name:   " . $childtheme['title'];
	$data = $data . "\n Theme URI:    " . $childtheme['url'];
	$data = $data . "\n Description:  " . $childtheme['description'];
	$data = $data . "\n Author        " . $childtheme['author'];
	$data = $data . "\n Author URI:   " . $childtheme['author-url'];
	$data = $data . "\n Template:     " . $childtheme['parent'];
	$data = $data . "\n Version:      " . $childtheme['version'];
	
	// insert GPL License Terms
	if ($childtheme['include-gpl'] == 'on') {
		$data = $data . "\n License:      GNU General Public License v2 or later";
	    $data = $data . "\n License URI:  http://www.gnu.org/licenses/gpl-2.0.html";
	}
	
	$data = $data . "\n*/\n\n" . '@import url("../' . $childtheme['parent'] . '/style.css");';
	$data = $data . "\n\n /* == Add your own styles below this line ==";
	$data = $data . "\n--------------------------------------------*/\n\n";

	// write data and close the file
	fwrite($handle, $data);
	fclose($handle);
	
	// create a nice screenshot 
}

// return an array only of parent themes
function ctw_giveme_parents() {
	
	// first we'll grab all installed themes
	$allThemes = wp_get_themes();
	$parentThemes = array();
	
	// next we'll add all parents and return the result
	foreach ($allThemes as $theme) {
		
		if ($theme->parent() == false) {
			$parentThemes[] = $theme;
		}
	}
	
	return $parentThemes;
}

// quick tests go here
function ctw_testing($childtheme) {
	
	echo '<p>Include GPL is ' . $childtheme['include-gpl'] . '.</p>';
	if ($childtheme['include-gpl'] == 'on') {
		echo '<p>We will include the terms accordingly.</p>';
	}
	
	// let's test which of our installed themes are child themes
	$allThemes = wp_get_themes();
	
	echo '<ol>';
	foreach ($allThemes as $theme) {
		echo '<li>';
		// print the theme title
		echo $theme->get('Name');
		// determine whether it's a child theme or not
		if ($theme->parent() == false) {
			echo ' is not a Child Theme.</li>';
		} else {
			echo ' is a Child Theme</li>';
		}
	}
	echo '</ol>';
	
	// is the current theme a child theme?
	$currentTheme = wp_get_theme();
	if ($currentTheme->parent() == false) {
		echo 'The current theme is not a child theme.';
	} else {
		echo 'The current theme is a child theme';
	}
}

?>
