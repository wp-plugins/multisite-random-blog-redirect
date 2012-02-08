<?php
/*
Plugin Name: Multisite Random Blog
Plugin URI: http://www.bicycletouringhub.com/plugins/ms-random-blog
Description: Allows you to randomly visit a blog in your MS network by appending ?randomblog at the end of your URL string. You can exclude by blog ID, mature or public status, and even change the ?randomblog string to whatever you like.
Version: 1.0
Author: Dave Conroy
Author URI: http://www.tiredofit.ca
License: GPL2
  */

/*  Copyright 2011  Dave Conroy  (email : wordpress@tiredofit.ca)

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

// Check for Multisite
if ( !is_multisite() )
   wp_die( __( 'Multisite support is not enabled.' ) );

//Register Plugin Settings on Activation
register_activation_hook( __FILE__, 'ms_random_blog' );

function ms_random_blog_activate()
   {
   add_site_option( 'ms_random_type_public', '1' );
   add_site_option( 'ms_random_type_mature', '0' );
   add_site_option( 'ms_random_exclude', '' );
   add_site_option( 'ms_random_string', 'randomblog' );
   }

// create plugin settings menu
add_action( 'network_admin_menu', 'multisite_random_blog_settings_menu' );

function multisite_random_blog_settings_menu()
   {
   global $wp_version;
   if ( version_compare( $wp_version, '3.0.9', '>' ) )
      {
      add_submenu_page( 'settings.php', __( 'Random Blogs', 'randomblogs' ), __
                       ( 'Random Blogs', 'randomblogs' ),
                       'manage_network_options',
                       'multisite_random_blog_settings',
                       'multisite_random_blog_settings_page' );
      }
   else
      {
      add_submenu_page( 'ms-admin.php', __( 'Random Blogs', 'randomblogs' ), __
                       ( 'Random Blogs', 'randomblogs' ),
                       'manage_network_options',
                       'multisite_random_blog_settings',
                       'multisite_random_blog_settings_page' );
      }
   }

//call register settings function
add_action( 'admin_init', 'multisite_random_blog_register_settings' );

function multisite_random_blog_register_settings()
   {

   register_setting( 'multisite_random_blog', 'ms_random_type_public' );
   register_setting( 'multisite_random_blog', 'ms_random_type_mature' );
   register_setting( 'multisite_random_blog', 'ms_random_exclude' );
   register_setting( 'multisite_random_blog', 'ms_random_string' );

   }

// Create Options Page
function multisite_random_blog_settings_page()
   {
   $ms_random_string = get_site_option( 'ms_random_string' );
   add_thickbox();
   print'<div class="wrap">';

   if ( isset( $_POST ) )
      {
      if ( isset( $_POST[ 'ms_random_type_public' ] ) )
         {
         update_site_option( "ms_random_type_public", stripslashes( $_POST[
                            'ms_random_type_public' ] ) );

         }

      if ( !isset( $_POST[ 'ms_random_type_public' ] ) )
         {
         update_site_option( "ms_random_type_public", '' );

         }

      if ( isset( $_POST[ 'ms_random_type_mature' ] ) )
         {
         update_site_option( "ms_random_type_mature", stripslashes( $_POST[
                            'ms_random_type_mature' ] ) );

         }

      if ( !isset( $_POST[ 'ms_random_type_mature' ] ) )
         {
         update_site_option( "ms_random_type_mature", '' );

         }
      if ( isset( $_POST[ 'ms_random_string' ] ) )
         {

         update_site_option( "ms_random_string", $_POST[ 'ms_random_string' ] );

         }

      if ( isset( $_POST[ 'ms_random_exclude' ] ) )
         {
         update_site_option( "ms_random_exclude", $_POST[ 'ms_random_exclude' ]
                            );

         }
?>
<div id="message" class="updated fade"><p><?php _e('Settings Saved.', 'multisite_random_blog'); ?></p></div>
<?php }

print'<h2>Multisite Random Blogs</h2>';

print'<form name="form" action="" method="post">';

//print '<form name="form" method="post" action="settings.php">';
settings_fields( 'multisite_random_blog_settings' );
do_settings_sections( 'multisite_random_blog_settings' );
print'This plugin allows your users to randomly visit a blog in your multisite
   network by adding ?randomblog at the end of your main site URL (example:
   http://www.yoursite.com/?randomblog) <br />';
print'Select the options of which blogs you wish to display, and optionally
   change the ?randomblog string to whatever you\'d like.<p>';
?>
<p>
<table class="form_table">

<h3>Control Which Blogs are Displayed</h3>
<tr valign="top">
<td><label for="ms_random_type_public"> <b><?php _e('Private') ?></b></label></td>
<label for="ms_random_type_public">
<td><input name="ms_random_type_public" type="checkbox" id="ms_random_type_public" value="1" <?php checked('1', get_site_option('ms_random_type_public')); ?> />
<?php _e('Show Blogs that are marked as "Private"') ?></label><br /></td></tr>


<tr>
<td><label for="ms_random_type_mature"> <b><?php _e('Mature') ?></b></label></td>
<label for="ms_random_type_mature">
<td><input name="ms_random_type_mature" type="checkbox" id="ms_random_type_mature" value="1" <?php checked('1', get_site_option('ms_random_type_mature')); ?> />
<?php _e('Show Blogs that are marked as "Mature"') ?></label><br /></td></tr>
</table>
<p>
<h3>Exclusions <a class="thickbox" href="#TB_inline?height=450&width=450&inlineId=bloglist">(Show Blog ID List)</a>
</label></td></h3>
<table class="form_table">
<tr valign="top">
<td><label for="ms_random_exclude"><?php _e( 'Exclude the following Blog IDs' ); ?>
<td><input type="text" name="ms_random_exclude" rows="1" id="ms_random_exclude" value="<?php echo get_site_option( 'ms_random_exclude' ); ?>" class="regular-text" /><span>Seperate multiple values with a ','</span></td>

</tr>
</table>
<p>
<h3>Custom Activation String</h3>
<table class="form_table">
<tr valign="top">
<td><label for="ms_random_string"><?php _e( 'String to Activate Random Blog Selection' ); ?></label></td>
<td><input type="text" name="ms_random_string" rows="1" id="ms_random_string" value="<?php echo get_site_option( 'ms_random_string' ); ?>" class="large-text" /></td>
</tr>
</table>

<?php do_settings_fields('multisite_random_blog_settings', 'default'); ?>
</table>

<?php do_settings_sections( 'multisite_random_blog_settings' ); ?>
<input type="submit" id="submit" name="submit" class="button-primary" value="<?php _e('Save changes', 'multisite_random_blog') ?>" /></td>
	
</form>
<?php print '<p><br /><div style="float:right">Plugin created by <a href="http://www.tiredofit.ca" target="_blank">Dave Conroy</a> from the inside of a tent while bicycling around the world for <a href="www.bicycletouringhub.com" target="_blank">Bicycle Touring Hub</a></div>';?>

</div>

<div id="bloglist" style="display:none">
<h2>Blog ID Listing</h2>
<?php global $wpdb;

$blogs = $wpdb->get_results( "SELECT * FROM $wpdb->blogs ORDER BY blog_id ASC" )
                            ;
//print_r($blogs);
echo'<table><th><b>ID</b></th><th style="align:middle"><b>URL</b></th>';
foreach( $blogs as $blog )
   {
   //print_r($blog);
   echo'<tr><td>'.$blog->blog_id.'</td><td><a href=
      "http://'.$blog->domain.$blog->path.'">'.$blog->domain.$blog
      ->path.'</a></td></tr>';
   }
echo'</table>';
?>
</div>
<?php }


// Redirection Function
function multisite_random_blog_redirect()
   {
   global $wpdb;
   global $current_site;

   $site = get_current_site();
   $mature = get_site_option( 'ms_random_type_mature' );
   $public = get_site_option( 'ms_random_type_public' );
   $exclude = get_site_option( 'ms_random_exclude' );

   if ( $mature == "1" )
      {


      $mature = "AND mature='0' OR mature='1'";
      }
   else
      {
      $mature = "OR mature !=1";
      }


   if ( $public == "1" )
      {
      $public = "AND public='0' OR public='1'";

      }
   else
      {
      $public = "OR public !=1";
      }

   if ( $exclude != "" )
      {
      $exclude = "AND blog_id NOT IN ($exclude)";
      }

   $query =
      "SELECT * FROM $wpdb->blogs WHERE site_id = '$site->id' $public $mature $exclude ORDER BY RAND() LIMIT 1";
   $random_id = $wpdb->get_row( $query );
   $url = 'http://'.$random_id->domain.$random_id->path;
   wp_redirect( $url );
   exit;
   }

// Checking to see if it is necessary to call multisite_random_blog_redirect function
$randomstring = get_site_option( ms_random_string );
if ( isset( $_GET[ $randomstring ] ) )
   {
   add_action( 'template_redirect', 'multisite_random_blog_redirect' );
   }
?>