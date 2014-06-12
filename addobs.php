<?
/** 
 * Script procesor for add/user form
 */
$states = array();
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
    $opt = array('http' => array('method' => 'POST', 'header' => 'Authorization: Bearer '.$_COOKIE['inat_access_token']));
    $context  = stream_context_create($opt);
    $result = file_get_contents($url, false, $context);
    $json = json_decode($result);

header("Location: ".$_POST['site_url'].'/?'.http_build_query(array('page_id' => $_POST['inat_post_id'], 'verb' => 'observations', 'id' => $json[0]->id)));
exit();
?>
