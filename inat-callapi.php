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

http://www.inaturalist.org/users/18730.json
http://www.inaturalist.org/observations/garrettt331.json?per_page=40&order_by=observed_on

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
function theme_map_obs($data, $context = 'page') {
  $output = '
    <div id="map-'. $context.'" style="height: 400px;"></div>

    <script type="text/javascript">
      var map = L.map("map-'. $context .'").setView([51.505, -0.09], 13);
      L.tileLayer("http://{s}.tile.osm.org/{z}/{x}/{y}.png", {
        maxZoom: 18
      }).addTo(map);
      var bounds = new Array();
    ';

    foreach( $data as $id => $obs) {
      if($obs->latitude != ''){
        if(count($obs->photos) >= 1) {
          $popup = '<div class="photo"><img src="'.$obs->photos[0]->small_url.'" alt="Photo" /> </div> <h2>'.$obs->species_guess.'</h2><div class="place">'.$obs->place_guess.'</div>';
        } else {
          $popup = '<div class="photo">No photo </div> <h2>'.$obs->species_guess.'</h2><div class="place">'.$obs->place_guess.'</div>';
        }
        $popup = str_replace("'","",$popup);
        $output .= "var popup = L.marker().setLatLng([".$obs->latitude.",".$obs->longitude."]).addTo(map).bindPopup('".$popup."');\n";
        $output .= "bounds.push(new Array([".$obs->latitude.",".$obs->longitude."]));";
      }
    }
    $output .= 'map.fitBounds(bounds);
    </script>';

  return $output;

}
function theme_observation($observation) {
  $output = '
  <div class="inat_observation_single" id="obs_'.$observation->id.'">
    <figure class="photo_single">';
    if (array_key_exists('photos_count', $observation) && $observation->photos_count == 0) {
      $output .= '<span class="no_photo">'.__('No photo','inat').'</span>';
    } else {
      $output .= '<div class="cycle-slideshow img-wrapper img-wrapper-'.$id.'"
      data-cycle-slides="> figure"
      data-cycle-fx=fade
      >';
      foreach($observation->observation_photos as $id => $img) {
        $output .= '<figure>
          <img src="'.$img->photo->small_url.'" alt="'.$observation->description.'" class="img-'.$id.'"/>
          <figurecaption>'.$img->photo->attribution.'</figurecaption>
        </figure>';
      }
      $output .= '</div>';
    }
    $output .= '</figure> <!-- /photo -->
    <div class="localitzation">
    <div id="map" style=""></div>

    <script type="text/javascript">
      var map = L.map("map").setView([51.505, -0.09], 13);
      L.tileLayer("http://{s}.tile.osm.org/{z}/{x}/{y}.png", {
        maxZoom: 18,
        zoom: 10
      }).addTo(map);
      var bounds = new Array();';
        $output .= "var popup = L.marker().setLatLng([".$observation->latitude.",".$observation->longitude."]).addTo(map); ";
        $output .= "bounds.push(new Array([".$observation->latitude.",".$observation->longitude."]));"; 
        $output .= 'map.panTo(new L.LatLng('.$observation->latitude.', ' . $observation->longitude.' ));
    </script>
    <h2><a href="'.site_url().'/?'.http_build_query(array('page_id' => get_option('inat_post_id'), 'verb' =>'observations', 'id' => $observation->id)).'">'.$observation->species_guess.'</a></h2>
    <div class="description">'.$observation->description.'</div>';
    if(isset($observation->user_id)){
      $output .= '<div class="observer"><span class="label">'.__('Observer: ','inat').'</span><a href="'.site_url().'/?'.http_build_query(array('page_id' => get_option('inat_post_id'), 'verb' => 'users', 'id' => $observation->user_id)).'">'.$observation->user_login.'</a></div>';
    }
    if(isset($observation->observed_on)) {
      $output .= '<div class="date">';
        $d = DateTime::createFromFormat('Y-m-d', $observation->observed_on)->format('l j F Y');
        $output .= '<span class="label">'.__('Date observed: ', 'inat').$d.'</span>
      </div>';
    }

    if(isset($observation->place_guess)) {
      $output .= '<div class="place"> <span class="label">';
        $output .= __('Place: ','inat').'</span>'.$observation->place_guess;
        $output .= '(<span class="latitude">'.__('Lat: ','inat').$observation->latitude.'</span>
        <span class="longitude">'.__('Lon: ','inat').$observation->longitude.'</span>)
      </div>';
    }

    if(isset($observation->positional_accuracy)) {
      $output .= '<div class="accuracy"><span class="label">'.__('Accuracy: ','inat').'</span>'. $observation->positional_accuracy.'m</div>';
    }
    if(get_option('inat_reduce_project','') == '' && isset($observation->project_observations[0])) {
       // remove project info because is obvius and not needed if project is set for the plugin 
        $output .= '<div class="project"><span class="label">'.__('Project: ', 'inat').'</span> <a href="'.site_url().'/?'.http_build_query(array('page_id' => get_option('inat_post_id'), 'verb' => 'projects', 'id' => $observation->project_observations[0]->project_id)).'">'.$observation->project_observations[0]->project->title.'</a></div>';
    }
    if(isset($observation->taxon_id)) {
      $output .= '<div class="taxon"><span class=label> '.__('Taxon: ','inat').'</span> <a href="'.site_url().'/?'.http_build_query(array('page_id' => get_option('inat_post_id'), 'verb' => 'taxa', 'id' => $observation->taxon_id)).'">'.$observation->species_guess.'</a></div>';
    }
  $output .= '</div> </div>';

  return $output;
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
function theme_project($projects) {
  $output = '
  <div class="inat_project row" id="prj_'.$projects->id.'">
    <div class="photo">
      <img src="'.$projects->icon_url.'"/>
    </div> <!-- /photo -->
    <h2><a href="'.site_url().'/?'. http_build_query(array('page_id' => get_option('inat_post_id'), 'verb' => 'taxa', 'id' => $projects->id)).'">'.$projects->title.'</a></h2>
    <div class="description">'.$projects->description.'</div>
  </div>';
  return $output;
}
function theme_list_taxa($data, $params) {
  return var_dump($data);
}
function theme_user($data) {
  return 'THEME USER::::::' . var_dump($data);
}
function theme_taxon($taxa) {
  $output = '
  <div class="inat_project row" id="prj_'.$taxa->id.'">
    <div class="photo">
      <img src="'.$taxa->photo_url.'"/>
    </div> <!-- /photo -->
    <h2><a href="'.site_url() .'/?' . http_build_query(array('page_id' => get_option('inat_post_id'), 'verb' => 'taxa', 'id' => $taxa->id)).'">'.$taxa->name.'</a></h2>
    <div class="description">'.$taxa->wikipedia_summary.'</div>';
    if($taxa->id != 48460){
      $output .= '<a href="'.site_url(). '/?'. http_build_query(array('page_id' => get_option('inat_post_id'), 'verb' => 'taxa', 'id' => $taxa->parent_id)).'">'.__('Parent','inat').'</a>';
    }
  $output .= '</div>';

  return $output;
}
?>
