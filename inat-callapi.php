<?php 
function inat_get_call($verb='observations', $id='', $page='', $per_page='', $order_by='', $custom=array()) {
  /** Get the project information **/
/**
 *
 *
 * place_guess taxon_id
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

 */
  //$verb = 'https://inaturalist.org/';
  //$query = array();
  //$options = array('query' => $query, 'https' => FALSE);
  //$url = url(variable_get('inat_base_url','http://www.inaturalist.org') . '/' . $verb, $options);
  //$options = array('method' => 'GET');
  //dsm('debug info:');
  //dsm($url);
  //$result = drupal_http_request($url, $options);
  //$json_proj = drupal_json_decode($result->data);
  
  //$resp = http_request('HTTP_METH_GET', 'http://www.inaturalist.org/observations.json');
  //$r = new HttpRequest('http://www.inaturalist.org/observations.json', HttpRequest::METH_GET);
  //$url = 'http://www.inaturalist.org/observations.json';
  if($id != '') {$id = '/'.$id;}
  $url = get_option('inat_base_url').'/'.$verb.$id.'.json';
  $data = array();
  if($page != '') { $data += array('page' => $page); }
  if($per_page != '') {$data += array('per_page' => $per_page); }
  if($order_by != '') {$data += array('order_by' => $order_by); }
  if(isset($custom)) {$data += $custom; }

  // use key 'http' even if you send the request to https://...
  $options = array(
      'http' => array(
          'header'  => "Content-type: application/x-www-form-urlencoded\r\n",
          'method'  => 'GET',
          'content' => http_build_query($data),
      ),
  );
  $context  = stream_context_create($options);
  $result = file_get_contents($url, false, $context);
  $data = json_decode($result);
  return $data;
}

function theme_list_obs($obs, $params) {
  $output = '';
  foreach($obs as $id => $ob) {
    $output .= theme_list_single_obs($id,$ob, $params);
  }
  return $output;
}

function theme_list_single_obs($id,$ob, $params) {
  $output = ' 
    <div class="inat_observation row" id="obs_'.$ob->id.'">
      <div class="photo">';
        if (array_key_exists('photos_count',$ob) && $ob->photos_count == 0) {
          $output .= '<span class="no_photo">'.t('No photo').'</span>';
        } elseif(isset($ob->photos[0])){
          $output .= '<div class="cycle-slideshow img-wrapper img-wrapper-'.$id.'"
            data-cycle-slides="> figure"
            data-cycle-fx=fade
          >';
          foreach($ob->photos as $id => $img){
            $output .= '<figure>
              <img src="'.$img->small_url.'" alt="'.$ob->description.'" class="img-'.$id.'"/>
              <figurecaption>'.$img->attribution.'</figurecaption>
            </figure>';
          }
        $output .= '</div>';
      }
      $output .= '</div> <!-- /photo -->
      <h2><a href="'.site_url().'/?'.http_build_query(array('page_id' => get_option('inat_post_id'), 'verb'=>'observations', 'id' => $ob->id, )).'">'.$ob->species_guess.'</a></h2>
      <div class="description">'.$ob->description.'</div>';
      if(isset($ob->user->login)){
        $output .= '<div class="observer"><span class="label">'.__('Observer: ', 'inat') .'</span>'. $ob->user->login.'</div>';
      }

      $output .= '<div class="date"><span class="label">';
      if(isset($ob->observed_on)){
            $d = DateTime::createFromFormat('Y-m-d', $ob->observed_on)->format('l j F Y');
            $output .= __('Date observed: ', 'inat')."</span>".$d;
            }
      $output .= '</div>';


    if(isset($ob->place_guess) && $ob->place_guess != ''){
      $output .= '<div class="place"><span class="label">'. __('Place: ', 'inat')."</span>".$ob->place_guess.'</div>';
    }
    $output .= '</div>';

  return $output;
}
function theme_map_obs($data) {
  return var_dump($data);
}
function theme_observation($data) {
  return var_dump($data);
}

function theme_list_places($data, $params) {
  return var_dump($data);
}
function theme_place($data) {
  return var_dump($data);
}
function theme_list_projects($data, $params) {
  return var_dump($data);
}
function theme_project($data) {
  return var_dump($data);
}
function theme_list_taxa($data, $params) {
  return var_dump($data);
}
function theme_taxon($data) {
  return var_dump($data);
}
?>
