<?php 
function test_call($verb = 'observations', $param = '', $page = '') {
  /** Get the project information **/
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
  $url = 'http://www.inaturalist.org/observations.json';
  $data = array('key1' => 'value1', 'key2' => 'value2');

  // use key 'http' even if you send the request to https://...
  $options = array(
      'http' => array(
          'header'  => "Content-type: application/x-www-form-urlencoded\r\n",
          'method'  => 'GET',
          'content' => http_build_query(array()),
      ),
  );
  $context  = stream_context_create($options);
  $result = file_get_contents($url, false, $context);

  return json_decode($result);

}

function theme_list_obs($obs) {
  $output = 'hi joe!';
  foreach($obs as $id => $ob) {
    $output .= theme_list_single_obs($id,$ob);
  }
  return $output;
}

function theme_list_single_obs($id,$ob) {
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
      <h2><a href="'.'/inat/observation/' . $ob->id.'">'.$ob->species_guess.'</a></h2>
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

?>
