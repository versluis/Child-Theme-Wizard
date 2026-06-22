<?php
/**
 * Plugin Name: Child Theme Wizard
 * Plugin URI: https://wpguru.tv
 * Description: Creates a child theme from any installed parent theme.
 * Version: 1.5
 * Author: Jay Versluis
 * Author URI: https://wpguru.tv
 * License: GPL2
 * License URI: http://www.gnu.org/licenses/gpl-2.0.html
 * Text Domain: ctw
 */

/*  Copyright 2013-2026  Jay Versluis (email support@wpguru.tv)

    This program is free software; you can redistribute it and/or modify
    it under the terms of the GNU General Public License, version 2, as
    published by the Free Software Foundation.

    This program is distributed in the hope that it will be useful,
    but WITHOUT ANY WARRANTY; without even the implied warranty of
    MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE. See the
    GNU General Public License for more details.

    You should have received a copy of the GNU General Public License
    along with this program; if not, write to the Free Software
    Foundation, Inc., 51 Franklin St, Fifth Floor, Boston, MA 02110-1301 USA
*/

add_action( 'admin_menu', 'ctw_menu' );

function ctw_menu(): void {
	add_submenu_page(
		'tools.php',
		__( 'Child Theme Wizard', 'ctw' ),
		__( 'Child Theme Wizard', 'ctw' ),
		'manage_options',
		'ChildThemeWizard',
		'ctw_main_function'
	);
}

function ctw_main_function(): void {

	if ( ! current_user_can( 'manage_options' ) ) {
		wp_die( esc_html__( 'You do not have sufficient privileges to access this page.', 'ctw' ) );
	}

	?>
	<div class="wrap">
	<h1><?php esc_html_e( 'Child Theme Wizard', 'ctw' ); ?></h1>

	<?php

	if ( isset( $_POST['hiddenfield'] ) && $_POST['hiddenfield'] === 'Y' ) {

		// Verify nonce — blocks CSRF attacks.
		check_admin_referer( 'ctw_create_child_theme', 'ctw_nonce' );

		// Validate parent against actually-installed parent themes.
		$parent_themes    = ctw_giveme_parents();
		$valid_parents    = array_map( fn( $t ) => $t->get_stylesheet(), $parent_themes );
		$submitted_parent = sanitize_text_field( wp_unslash( $_POST['parent'] ?? '' ) );

		if ( ! in_array( $submitted_parent, $valid_parents, true ) ) {
			wp_die( esc_html__( 'Invalid parent theme selected.', 'ctw' ) );
		}

		$newchildtheme = array(
			'parent'      => $submitted_parent,
			'title'       => sanitize_text_field( wp_unslash( $_POST['title'] ?? '' ) ),
			'description' => sanitize_text_field( wp_unslash( $_POST['description'] ?? '' ) ),
			'url'         => esc_url_raw( wp_unslash( $_POST['url'] ?? '' ) ),
			'author'      => sanitize_text_field( wp_unslash( $_POST['author'] ?? '' ) ),
			'author-url'  => esc_url_raw( wp_unslash( $_POST['author-url'] ?? '' ) ),
			'version'     => sanitize_text_field( wp_unslash( $_POST['version'] ?? '' ) ),
			'include-gpl' => ( isset( $_POST['include-gpl'] ) && $_POST['include-gpl'] === 'on' ) ? 'on' : 'off',
		);

		$status = ctw_create_theme( $newchildtheme );

		if ( $status['alert'] === -1 ) {
			?>
			<div class="notice notice-error">
				<p><strong><?php esc_html_e( 'Yikes — something went wrong:', 'ctw' ); ?></strong></p>
				<?php echo wp_kses_post( $status['message'] ); ?>
			</div>
			<p><a href="<?php echo esc_url( admin_url( 'tools.php?page=ChildThemeWizard' ) ); ?>"><?php esc_html_e( 'Try again?', 'ctw' ); ?></a></p>
			<?php
		} else {
			?>
			<div class="notice notice-success">
				<?php echo wp_kses_post( $status['message'] ); ?>
				<p><strong><?php esc_html_e( 'Your Child Theme was created successfully!', 'ctw' ); ?></strong></p>
			</div>
			<p>
				<?php
				printf(
					/* translators: 1: link to Appearance > Themes, 2: link to Appearance > Editor */
					esc_html__( 'Head over to %1$s to activate it. Add your custom styles in %2$s.', 'ctw' ),
					'<a href="' . esc_url( admin_url( 'themes.php' ) ) . '">' . esc_html__( 'Appearance › Themes', 'ctw' ) . '</a>',
					'<a href="' . esc_url( admin_url( 'theme-editor.php' ) ) . '">' . esc_html__( 'Appearance › Editor', 'ctw' ) . '</a>'
				);
				?>
			</p>
			<?php
		}
	} else {

		$current_user  = wp_get_current_user();
		$parent_themes = ctw_giveme_parents();
		$current_theme = wp_get_theme();
		?>

		<p>
			<?php
			printf(
				/* translators: %s: link to WordPress child theme documentation */
				esc_html__( 'This simple wizard will help you generate a new %s with just one click.', 'ctw' ),
				'<a href="https://developer.wordpress.org/themes/advanced-topics/child-themes/" target="_blank">' . esc_html__( 'Child Theme', 'ctw' ) . '</a>'
			);
			?>
		</p>
		<p><?php esc_html_e( 'Select which theme you want to use as a base, fill in the details and click "Create Child Theme".', 'ctw' ); ?></p>
		<hr>

		<form name="ctwform" method="post" action="">
			<?php wp_nonce_field( 'ctw_create_child_theme', 'ctw_nonce' ); ?>
			<input type="hidden" name="hiddenfield" value="Y">

			<table class="form-table" role="presentation">
				<tr>
					<th scope="row"><label for="ctw-parent"><?php esc_html_e( 'Parent Theme', 'ctw' ); ?></label></th>
					<td>
						<select name="parent" id="ctw-parent">
							<?php foreach ( $parent_themes as $theme ) :
								$stylesheet = $theme->get_stylesheet();
								$name       = $theme->get( 'Name' );
								$is_active  = $current_theme->get( 'Name' ) === $name;
								?>
								<option value="<?php echo esc_attr( $stylesheet ); ?>"<?php selected( $is_active ); ?>>
									<?php
									echo esc_html( $name );
									if ( $is_active ) {
										echo ' (' . esc_html__( 'currently active', 'ctw' ) . ')';
									}
									?>
								</option>
							<?php endforeach; ?>
						</select>
					</td>
				</tr>
				<tr>
					<th scope="row"><label for="ctw-title"><?php esc_html_e( 'Title', 'ctw' ); ?></label></th>
					<td>
						<input type="text" name="title" id="ctw-title" value="" class="regular-text">
						<p class="description"><?php esc_html_e( "What's your new Child Theme called?", 'ctw' ); ?></p>
					</td>
				</tr>
				<tr>
					<th scope="row"><label for="ctw-description"><?php esc_html_e( 'Description', 'ctw' ); ?></label></th>
					<td>
						<input type="text" name="description" id="ctw-description" value="" class="regular-text">
						<p class="description"><?php esc_html_e( 'A few notes about your Child Theme.', 'ctw' ); ?></p>
					</td>
				</tr>
				<tr>
					<th scope="row"><label for="ctw-url"><?php esc_html_e( 'Child Theme URL', 'ctw' ); ?></label></th>
					<td>
						<input type="url" name="url" id="ctw-url" value="" class="regular-text">
						<p class="description"><?php esc_html_e( 'Does it have a website or release post?', 'ctw' ); ?></p>
					</td>
				</tr>
				<tr>
					<th scope="row"><label for="ctw-author"><?php esc_html_e( 'Author', 'ctw' ); ?></label></th>
					<td>
						<input type="text" name="author" id="ctw-author" value="<?php echo esc_attr( $current_user->display_name ); ?>" class="regular-text">
						<p class="description"><?php esc_html_e( "That's you.", 'ctw' ); ?></p>
					</td>
				</tr>
				<tr>
					<th scope="row"><label for="ctw-author-url"><?php esc_html_e( 'Author URL', 'ctw' ); ?></label></th>
					<td>
						<input type="url" name="author-url" id="ctw-author-url" value="<?php echo esc_url( $current_user->user_url ); ?>" class="regular-text">
						<p class="description"><?php esc_html_e( "That's your website.", 'ctw' ); ?></p>
					</td>
				</tr>
				<tr>
					<th scope="row"><label for="ctw-version"><?php esc_html_e( 'Version', 'ctw' ); ?></label></th>
					<td>
						<input type="text" name="version" id="ctw-version" value="1.0" class="small-text">
						<p class="description"><?php esc_html_e( 'Start with 1.0 and work your way up.', 'ctw' ); ?></p>
					</td>
				</tr>
				<tr>
					<th scope="row"><label for="ctw-include-gpl"><?php esc_html_e( 'Include GPL License', 'ctw' ); ?></label></th>
					<td>
						<select name="include-gpl" id="ctw-include-gpl">
							<option value="on"><?php esc_html_e( 'Yes Please!', 'ctw' ); ?></option>
							<option value="off"><?php esc_html_e( 'No Thanks', 'ctw' ); ?></option>
						</select>
					</td>
				</tr>
			</table>

			<p class="submit">
				<input type="submit" name="Submit" class="button-primary" value="<?php esc_attr_e( 'Create Child Theme', 'ctw' ); ?>">
			</p>
		</form>

		<?php
	}
	?>

	<br><hr><br>
	</div><!-- .wrap -->

	<p><a href="https://wpguru.co.uk" target="_blank"><img src="<?php echo esc_url( plugins_url( 'images/guru-header-2013.png', __FILE__ ) ); ?>" width="300" alt="WP Guru"></a></p>

	<p>
		<a href="https://wpguru.co.uk/2014/03/introducing-child-theme-wizard-for-wordpress/" target="_blank">Plugin by Jay Versluis</a>
		| <a href="https://github.com/versluis/Child-Theme-Wizard" target="_blank">Fork me or Contribute on GitHub</a>
		| <a href="https://patreon.com/versluis" target="_blank">Support me on Patreon</a>
	</p>

	<?php
}

function ctw_create_theme( array $childtheme ): array {

	$status = array(
		'alert'   => 0,
		'message' => '',
	);

	global $wp_filesystem;
	if ( empty( $wp_filesystem ) ) {
		require_once ABSPATH . 'wp-admin/includes/file.php';
		WP_Filesystem();
	}

	$directory = get_theme_root() . '/' . sanitize_file_name( $childtheme['title'] );

	if ( ! $wp_filesystem->mkdir( $directory ) ) {
		$status['message'] = '<p>' . esc_html__( 'Could not create directory. Does it exist already?', 'ctw' ) . '</p>';
		$status['alert']   = -1;
		return $status;
	}

	$status['message'] .= '<p>' . esc_html__( 'Directory created successfully.', 'ctw' ) . '</p>';

	// Strip */ so user-supplied values cannot break out of the CSS comment block.
	$safe = array_map( fn( $v ) => str_replace( '*/', '', (string) $v ), $childtheme );

	$style  = "/* \n Theme Name:   " . $safe['title'];
	$style .= "\n Theme URI:    " . $safe['url'];
	$style .= "\n Description:  " . $safe['description'];
	$style .= "\n Author:       " . $safe['author'];
	$style .= "\n Author URI:   " . $safe['author-url'];
	$style .= "\n Template:     " . $safe['parent'];
	$style .= "\n Version:      " . $safe['version'];

	if ( $safe['include-gpl'] === 'on' ) {
		$style .= "\n License:      GNU General Public License v2 or later";
		$style .= "\n License URI:  http://www.gnu.org/licenses/gpl-2.0.html";
	}

	$style .= "\n\n /* == Add your own styles below this line ==";
	$style .= "\n--------------------------------------------*/\n\n";

	if ( ! $wp_filesystem->put_contents( $directory . '/style.css', $style, FS_CHMOD_FILE ) ) {
		$status['message'] .= '<p>' . esc_html__( 'There was a problem writing data to style.css.', 'ctw' ) . '</p>';
		$status['alert']    = -1;
		return $status;
	}

	$status['message'] .= '<p>' . esc_html__( 'Writing data to style.css…', 'ctw' ) . '</p>';

	$functions  = "<?php\n/*\n\n  This file is part of a child theme called " . $safe['title'] . ".";
	$functions .= "\n  Functions in this file will be loaded before the parent theme's functions.";
	$functions .= "\n  For more information, please read";
	$functions .= "\n  https://developer.wordpress.org/themes/advanced-topics/child-themes/";
	$functions .= "\n\n*/\n\n";
	$functions .= "// Loads the parent stylesheet — leave this in place unless you know what you're doing.\n";
	$functions .= "function your_theme_enqueue_styles() {\n\n";
	$functions .= "    \$parent_style = 'parent-style';\n\n";
	$functions .= "    wp_enqueue_style( \$parent_style,\n";
	$functions .= "        get_template_directory_uri() . '/style.css'\n";
	$functions .= "    );\n\n";
	$functions .= "    wp_enqueue_style( 'child-style',\n";
	$functions .= "        get_stylesheet_directory_uri() . '/style.css',\n";
	$functions .= "        array( \$parent_style ),\n";
	$functions .= "        wp_get_theme()->get( 'Version' )\n";
	$functions .= "    );\n";
	$functions .= "}\n";
	$functions .= "add_action( 'wp_enqueue_scripts', 'your_theme_enqueue_styles' );\n\n";
	$functions .= "/*  Add your own functions below this line.\n";
	$functions .= "    ======================================== */\n\n";

	if ( ! $wp_filesystem->put_contents( $directory . '/functions.php', $functions, FS_CHMOD_FILE ) ) {
		$status['message'] .= '<p>' . esc_html__( 'There was a problem writing data to functions.php.', 'ctw' ) . '</p>';
		$status['alert']    = -1;
		return $status;
	}

	$status['message'] .= '<p>' . esc_html__( 'Writing data to functions.php…', 'ctw' ) . '</p>';

	$status['message'] .= ctw_make_thumbnail( $directory );

	return $status;
}

function ctw_giveme_parents(): array {

	$parent_themes = array();

	foreach ( wp_get_themes() as $theme ) {
		if ( $theme->parent() === false ) {
			$parent_themes[] = $theme;
		}
	}

	return $parent_themes;
}

function ctw_make_thumbnail( string $childpath ): string {

	global $wp_filesystem;
	if ( empty( $wp_filesystem ) ) {
		require_once ABSPATH . 'wp-admin/includes/file.php';
		WP_Filesystem();
	}

	$source      = plugin_dir_path( __FILE__ ) . 'images/screenshot.png';
	$destination = $childpath . '/screenshot.png';

	if ( ! $wp_filesystem->copy( $source, $destination ) ) {
		return '<p>' . esc_html__( 'Could not copy thumbnail image.', 'ctw' ) . '</p>';
	}

	return '<p>' . esc_html__( 'Copied thumbnail file — looking good!', 'ctw' ) . '</p>';
}
