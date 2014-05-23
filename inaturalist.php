<?php
/**
 * Plugin Name: iNaturalist
 * Plugin URI: 
 * Description: 
 * Version: 0.1
 * Author: Bacterio
 * Author URI: 
 * License: 
 */
//Include widget
require_once('inat-widgets.php');
require_once('inat-callapi.php');
//Afegir pàgina d'opcions i salvar-les
add_action( 'admin_menu', 'add_inat_menu' );

function add_inat_menu() {
	$inat_options = add_options_page( 'iNaturalist configuration page', 'iNaturalist', 'manage_options', 'inaturalist', 'inat_options' );
}


function inat_options() {
	if ( !current_user_can( 'manage_options' ) )  {
		wp_die( __( 'You do not have sufficient permissions to access this page.' ) );
	}
	if(isset($_POST['inat_base_url'])){
		update_option( 'inat_base_url',$_POST['inat_base_url'] );		
	}
	if(isset($_POST['inat_reduce_project'])){
		update_option( 'inat_reduce_project',$_POST['inat_reduce_project'] );		
	}
	if(isset($_POST['inat_reduce_user'])){
		update_option( 'inat_reduce_user',$_POST['inat_reduce_user'] );		
	}
	if(isset($_POST['inat_login_callback'])){
		update_option( 'inat_login_callback',$_POST['inat_login_callback'] );		
	}
	if(isset($_POST['inat_login_id'])){
		update_option( 'inat_login_id',$_POST['inat_login_id'] );		
	}
	if(isset($_POST['inat_login_secret'])){
		update_option( 'inat_login_secret',$_POST['inat_login_secret'] );		
	}
	if(isset($_POST['inat_login_app'])){
		update_option( 'inat_login_app',$_POST['inat_login_app'] );		
	}

	echo '<div class="wrap">';
  echo '<h2>'.__('iNaturalist configuration page', 'inat').'</h2>';
	echo '<form action="" method="post">';
  echo '<table class="form-table"><tbody>';
  echo '<tr><th scope="row"><label for="this">'.__('Base URL of iNaturalist','inat').' </label></th>';
	echo '<td><input type="text" class="regular-text" value="'.get_option( 'inat_base_url' ).'" name="inat_base_url">';
  echo '<p class="description">'.__('The URL used to access iNaturalist data, for example http://www.inaturalist.org','inat').'</p></td></tr>';
  echo '<tr><th scope="row"><label for="this">'.__('Reduce plugin behavior to this project ','inat').' </label></th>';
	echo '<td><input type="text" class="regular-text" value="'.get_option( 'inat_reduce_project' ).'" name="inat_reduce_project">';
  echo '<p class="description">'.__('The project id to reduce the plugin behavior','inat').'</p></td></tr>';
  echo '<tr><th scope="row"><label for="this">'.__('Reduce plugin behavior to this user ','inat').' </label></th>';
	echo '<td><input type="text" class="regular-text" value="'.get_option( 'inat_reduce_user' ).'" name="inat_reduce_user">';
  echo '<p class="description">'.__('The user loginname to reduce the plugin behavior','inat').'</p></td></tr>';
  echo '<tr><th colspan=2><h3>'.__('Configurations for login as iNaturalist application', 'inat').'</h3></th></tr>';
  echo '<tr><th scope="row"><label for="this">'.__('Callback url','inat').' </label></th>';
	echo '<td><input type="text" class="regular-text" value="'.get_option( 'inat_login_callback' ).'" name="inat_login_callback">';
  echo '<p class="description">'.__('iNat application callback url','inat').'</p></td></tr>';
  echo '<tr><th scope="row"><label for="this">'.__('Application Id','inat').' </label></th>';
	echo '<td><input type="text" class="regular-text" value="'.get_option( 'inat_login_id' ).'" name="inat_login_id">';
  echo '<p class="description">'.__('iNat application identifyer','inat').'</p></td></tr>';
  echo '<tr><th scope="row"><label for="this">'.__('Secret','inat').' </label></th>';
	echo '<td><input type="text" class="regular-text" value="'.get_option( 'inat_login_secret' ).'" name="inat_login_secret">';
  echo '<p class="description">'.__('iNat application secret key','inat').'</p></td></tr>';
  echo '<tr><th scope="row"><label for="this">'.__('Numeric id of your application','inat').' </label></th>';
	echo '<td><input type="text" class="regular-text" value="'.get_option( 'inat_login_app' ).'" name="inat_login_app">';
  echo '<p class="description">'.__('Get it at list of applications http://www.inaturalist.org/oauth/applications','inat').'</p></td></tr>';
	//echo '<input type="text" value="'.get_option( 'inat_' ).'" name="">';
  echo '</tbody></table>';
	echo '<input type="submit" name="dp_submit" value="Save Settings" />';
	echo '</form>';
	echo '</div>';
}


//Filtrar the_content de la pàgina
function my_the_content_filter($content) {
  
  if ($GLOBALS['post']->post_title == 'inat') {
		update_option( 'inat_post_id', (string)$GLOBALS['post']->ID );		
    $output = '';
    $verb = (isset($GLOBALS['_REQUEST']['verb'])) ? $GLOBALS['_REQUEST']['verb'] : 'observations';
    $id = (isset($GLOBALS['_REQUEST']['id'])) ? $GLOBALS['_REQUEST']['id'] : '';
    $page = (isset($GLOBALS['_REQUEST']['page'])) ? $GLOBALS['_REQUEST']['page'] : '1';
    $per_page = (isset($GLOBALS['_REQUEST']['per_page'])) ? $GLOBALS['_REQUEST']['per_page'] : '50';
    $order_by = (isset($GLOBALS['_REQUEST']['order_by'])) ? $GLOBALS['_REQUEST']['order_by'] : 'observed_on';
    $custom = array();
    if(isset($GLOBLAS['_REQUEST']['place_guess'])) { $custom += array('place_guess' => $GLOBALS['_REQUEST']['place_guess']); }
    if(isset($GLOBLAS['_REQUEST']['taxon_id'])) { $custom += array('taxon_id' => $GLOBALS['_REQUEST']['taxon_id']); }
    //if(isset($GLOBLAS['_REQUEST'][''])) { $custom += array('' => $GLOBALS['_REQUEST']['']); }
    //return var_dump($GLOBALS['_REQUEST']);
    //$ret_cont .= 'inat in!';
    $data = inat_get_call($verb, $id, $page, $per_page, $order_by, $custom);
    $params =array('verb' => $verb, 'id' => $id, 'page' => $page, 'per_page' => $per_page, 'order_by' => $order_by, 'custom' => $custom);
    switch($verb) {
      /******
http://www.inaturalist.org/observations.json?per_page=150&order_by=observed_on&page=1
http://www.inaturalist.org/observations.json?per_page=40&order_by=observed_on&page=1
http://www.inaturalist.org/observations.json?per_page=150&order_by=observed_on&page=1
http://www.inaturalist.org/places.json?page=1
http://www.inaturalist.org/projects.json
http://www.inaturalist.org/taxa.json

http://www.inaturalist.org/observations/694370.json
http://www.inaturalist.org/places/61841.json
http://www.inaturalist.org/observations.json?per_page=40&order_by=observed_on&place_guess=61841
http://www.inaturalist.org/projects/101.json
http://www.inaturalist.org/observations/project/101.json?per_page=40&order_by=observed_on
http://www.inaturalist.org/taxa/47686.json
http://www.inaturalist.org/observations.json?per_page=40&order_by=observed_on&taxon_id=47686&page=1
      ********/
      case 'observations':
        if($id == '') {
          $output .= theme_map_obs($data);
          $output .= theme_list_obs($data, $params);
        } else {
          $output .= theme_observation($data);
        }
        break;
      case 'places':
        if($id == '') {
          $output .= theme_list_places($data);
        } else {
          $output .= theme_place($data);
          $custom['place_guess'] = $id;
          $data2 = inat_get_call($verb, $id, $page, $per_page, $order_by, $custom);
          $output .= theme_list_obs($data2, $params);
        }
        break;
      case 'projects':
        if($id == '') {
          $output .= theme_list_projects($data);
        } else {
          $output .= theme_project($data);
          $verb2 = 'observations/project';
          $data2 = inat_get_call($verb2, $id, $page, $per_page, $order_by, $custom);
          $output .= theme_list_obs($data2, $params);
        }
        break;
      case 'taxa':
        if($id == '') {
          $output .= theme_list_taxa($data);
        } else {
          $output .= theme_taxon($data);
          $verb2 = 'observations';
          $custom['taxon_id'] = $id;
          $data2 = inat_get_call($verb2, $id, $page, $per_page, $order_by, $custom);
          $output .= theme_list_obs($data2, $params);
        }
        break;
     default:
          $output .= theme_list_obs($data, $params);
    }
    return $output;
  }
  return $content;
}

add_filter( 'the_content', 'my_the_content_filter' );


//afegir camp a l'usuari

//add_action( 'profile_personal_options', 'inat_user' );
add_action( 'show_user_profile', 'inat_user' );

    
function inat_user( $user ) {

    $inat_user_value = get_user_meta( $user->ID, 'inat_user', true );

    ?>
<h3>INature</h3>
<table class="form-table">
	<tbody><tr>
		<th><label for="user_login">Usuari INature</label></th>
		<td><input type="text" value="<?php echo esc_attr( $inat_user_value ); ?>" name="inat_user" /></td>
	</tr>
</tbody></table>
    <?php
}

 add_action('personal_options_update', 'update_inat_user');
 
 function update_inat_user($user_id) {
     if ( current_user_can('edit_user',$user_id) )
         update_user_meta($user_id, 'inat_user', $_POST['inat_user']);
 }




?>
