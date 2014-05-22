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
add_action( 'admin_menu', 'my_plugin_menu' );



function my_plugin_menu() {
	$inat_options = add_options_page( 'My Plugin Options', 'INature', 'manage_options', 'inature', 'inat_options' );
}


function inat_options() {
	if ( !current_user_can( 'manage_options' ) )  {
		wp_die( __( 'You do not have sufficient permissions to access this page.' ) );
	}

	if(isset($_POST['this'])){
		update_option( 'inat_id',$_POST['this'] );		
	}

	echo '<div class="wrap">';
	echo '<form action="" method="post">';
	echo '<input type="text" value="'.get_option( 'inat_id' ).'" name="this">';
	echo '<input type="submit" name="dp_submit" value="Save Settings" />';
	echo '</form>';
	echo '</div>';
}


//Filtrar the_content de la pàgina


function my_the_content_filter($content) {
  if (isset($GLOBALS['_REQUEST']['inat'])) {
    //$ret_cont .= 'inat in!';
    $cont = test_call();
    return theme_list_obs($cont);
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
