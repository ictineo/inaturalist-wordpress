<?
/** 
 * Script procesor for add/user form
 */
$states = array();
// Validate
//if($_POST['inat_login_usradd_pwd'] != $_POST['inat_login_usradd_pwdc']) {
    //add_filter( 'display_post_states','inat_state1');
    //return;
//} else {
    // Actions token
    $verb = 'observations.json';
    $data = 'observation[species_guess]='.$_POST['inat_obs_add_species_guess'].
      '&observation[taxon_id]='.$_POST['inat_obs_add_taxon_id'].
      '&observation[id_please]='.$_POST['inat_obs_add_id_please'].
      '&observation[observed_on_string]='.$_POST['inat_obs_add_observed_on_string'].
      '&observation[time_zone]='.$_POST['inat_obs_add_time_zone'].
      '&observation[description]='.$_POST['inat_obs_add_description'].
      '&observation[place_guess]='.$_POST['inat_obs_add_place_guess'].
      '&observation[latitude]='.$_POST['inat_obs_add_latitude'].
      '&observation[longitude]='.$_POST['inat_obs_add_longitude']; 


    $url = $_POST['inat_base_url'].'/'.$verb.'?'.$data;
    //$opt = array('http' => array('method' => 'POST', 'content' => $data, 'header' => 'Content-Type: application/x-www-form-urlencoded\r\nAuthorization: Bearer '.$_COOKIE['inat_access_token']));
    $opt = array('http' => array('method' => 'POST', 'header' => 'Authorization: Bearer '.$_COOKIE['inat_access_token']));
    //DEBUG
    //echo '<pre>';
    //echo var_dump($_COOKIE);
    //echo var_dump($url);
    echo var_dump($opt);
    $context  = stream_context_create($opt);
    $result = file_get_contents($url, false, $context);
    $json = json_decode($result);
    //echo '<br /> <br /> <br /> <br /> <hr />';
    //echo '<pre>';
    //echo var_dump($json);
    if($json != NULL && array_key_exists('errors',$json)) {
      //add_filter( 'display_post_states','inat_state2');
    } else {
      //add_filter( 'display_post_states','inat_state3');
    }
//}

      // redirect
      // http://codex.wordpress.org/Function_Reference/wp_redirect
// DEBUG
//header("Location: ".$_POST['site_url'].'/?'.http_build_query(array('page_id' => $_POST['inat_post_id'], 'verb' => 'observations', 'id' => $json->id)));
header("Location: ".$_POST['site_url'].'/?'.http_build_query(array('page_id' => $_POST['inat_post_id'], 'verb' => 'observations', 'id' => $json[0]->id)));
exit();
?>
