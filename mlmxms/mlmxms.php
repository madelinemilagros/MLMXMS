<?php

/**
 * The plugin bootstrap file
 *
 * This file is read by WordPress to generate the plugin information in the plugin
 * admin area. This file also includes all of the dependencies used by the plugin,
 * registers the activation and deactivation functions, and defines a function
 * that starts the plugin.
 *
 * @link              MadelineMilagros.com
 * @since             1.0.0
 * @package           Mlmxms
 *
 * @wordpress-plugin
 * Plugin Name:       MLMXMS
 * Plugin URI:        http://madelinemilagros.com/index.php/madelines-star-wars-plugin/
 * Description:       This is a short description of what the plugin does. It's displayed in the WordPress admin area.
 * Version:           1.0.0
 * Author:            Madeline Merced
 * Author URI:        MadelineMilagros.com
 * License:           GPL-2.0+
 * License URI:       http://www.gnu.org/licenses/gpl-2.0.txt
 * Text Domain:       mlmxms
 * Domain Path:       /languages
 */

// If this file is called directly, abort.
if ( ! defined( 'WPINC' ) ) {
	die;
}

/**
 * Currently plugin version.
 * Start at version 1.0.0 and use SemVer - https://semver.org
 * Rename this for your plugin and update it as you release new versions.
 */
define( 'MLMXMS_VERSION', '1.0.0' );

/**
 * The code that runs during plugin activation.
 * This action is documented in includes/class-mlmxms-activator.php
 */
function activate_mlmxms() {
	require_once plugin_dir_path( __FILE__ ) . 'includes/class-mlmxms-activator.php';
	Mlmxms_Activator::activate();
}

/**
 * The code that runs during plugin deactivation.
 * This action is documented in includes/class-mlmxms-deactivator.php
 */
function deactivate_mlmxms() {
	require_once plugin_dir_path( __FILE__ ) . 'includes/class-mlmxms-deactivator.php';
	Mlmxms_Deactivator::deactivate();
}

register_activation_hook( __FILE__, 'activate_mlmxms' );
register_deactivation_hook( __FILE__, 'deactivate_mlmxms' );

/**
 * The core plugin class that is used to define internationalization,
 * admin-specific hooks, and public-facing site hooks.
 */
require plugin_dir_path( __FILE__ ) . 'includes/class-mlmxms.php';

/**
 * Begins execution of the plugin.
 *
 * Since everything within the plugin is registered via hooks,
 * then kicking off the plugin from this point in the file does
 * not affect the page life cycle.
 *
 * @since    1.0.0
 */
function run_mlmxms() {

	$plugin = new Mlmxms();
	$plugin->run();

}

function register_persons_cpt(){
	register_post_type('person', array(
		'label' => 'Persons',
		'public' => true, 
		'capability_type' => 'post',
	));
}

add_action('init', 'register_persons_cpt');
add_action('wp_ajax_nopriv_get_persons_from_api', 'get_persons_from_api');
add_action('wp_ajax_get_persons_from_api', 'get_persons_from_api');

function get_persons_from_api() {
	$current_page = ( ! empty($_POST['current_page'] ) ) ? $_POST['current_page'] : 1;
	$persons = [];

	$results = wp_remote_retrieve_body(wp_remote_get('https://swapi.co/api/people/?page=1'));
	$results = json_decode( $results );   
  	
  
  // Either the API is down or something else spooky happened. Just be done.
  if( ! is_array( $results ) || empty( $results ) ){
    return false;
  }  	    
  
$persons[] = $results;

foreach ($persons[0] as $person) {
	$person_slug = sanitize_title($person->name .' - '. $id);

if( $existing_person === null  ){

	$inserted_person = wp_insert_post( [
		'post_name' => $person_slug,
		'post_title' => $person_slug,
		'post_type' => 'person',
		'post_status' => 'publish'
	] );

	if( is_wp_error($inserted_person) || $inserted_person === 0) {
		continue;
	}

      $fillable = [
        'field_5d8a397330bea' => 'name',
        'field_5d8a398530beb' => 'height',
        'field_5d8a39a1afc66' => 'mass',
        'field_5d8a39a6afc67' => 'hair_color',
        'field_5d8a39b3afc68' => 'skin_color',
        'field_5d8a39bf07c9c' => 'eye_color',
        'field_5d8a39c807c9d' => 'birth_year',
        'field_5d8a39d007c9e' => 'gender',
        'field_5d8a39da07c9f' => 'homeworld',
        'field_5d8a891643e13' => 'films',
        'field_5d8a892943e14' => 'species',
        'field_5d8a893f43e15' => 'vehicles',
        'field_5d8a894943e16' => 'created',
        'field_5d8a895243e17' => 'edited',
  		  'field_5d8a895d43e18' => 'url',
      ];

      foreach( $fillable as $key => $name ) {
        update_field( $key, $person->$name, $inserted_person);
      }

         } else {
      
      $existing_person_id = $existing_person->ID;
      $exisiting_person_timestamp = get_field('updated_at', $existing_person_id);
      if( $person->updated_at >= $exisiting_person_timestamp ){
        $fillable = [
        'field_5d8a397330bea' => 'name',
        'field_5d8a398530beb' => 'height',
        'field_5d8a39a1afc66' => 'mass',
        'field_5d8a39a6afc67' => 'hair_color',
        'field_5d8a39b3afc68' => 'skin_color',
        'field_5d8a39bf07c9c' => 'eye_color',
        'field_5d8a39c807c9d' => 'birth_year',
        'field_5d8a39d007c9e' => 'gender',
        'field_5d8a39da07c9f' => 'homeworld',
        'field_5d8a891643e13' => 'films',
        'field_5d8a892943e14' => 'species',
        'field_5d8a893f43e15' => 'vehicles',
        'field_5d8a894943e16' => 'created',
        'field_5d8a895243e17' => 'edited',
        'field_5d8a895d43e18' => 'url',
        ];

        foreach( $fillable as $key => $name ){
          update_field( $name, $person->$name, $existing_person_id);
        }
      }
    }
  }

  $current_page = $current_page + 1;
  wp_remote_post( admin_url('admin-ajax.php?action=get_starwars_from_api'), [
    'blocking' => false,
    'sslverify' => false, // we are sending this to ourselves, so trust it.
    'body' => [
      'current_page' => $current_page
    ]
  ] );
  
}

run_mlmxms();
