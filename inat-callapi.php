<?php 
/**
 * Plugin Name: iNaturalist v1
 * Plugin URI: http://www.inaturalist.org
 * Description: This plugin connects your wordpress to inaturalist platform 
 * Version: 1
 * Author: Julià Mestieri for Projecte Ictineo SCCL (http://projecteictineo.com) 
 * Author URI: http://projecteictineo.com
 * License: aGPLv3
 */
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
  $prev_url = site_url(). '/?'.http_build_query(array('page_id' => get_option('inat_post_id') , 'verb' => $params['verb'], 'page' => $params['page'] - 1));
  $next_url = site_url(). '/?'.http_build_query(array('page_id' => get_option('inat_post_id') , 'verb' => $params['verb'], 'page' => $params['page'] + 1));
  $output .= '<div class="clearfix"> </div>
  <div class="pager-wrapper">';
    if($params['page'] > 1) {
      $output .= '<span id="prev-link" class="pager link"><a href="'.$prev_url.'">'.__('Prev','inat').'</a></span>&nbsp;&nbsp;';
    }
    $output .= '<span id="next-link" class="pager link"><a href="'.$next_url.'">'.__('Next','inat').'</a></span>
  </div>';

  return $output;
}

function theme_list_single_obs($id,$ob, $params) {
  $output = '  
    <div class="inat_observation row" id="obs_'.$ob->id.'">
      <div class="photo">';
        if (is_array($ob) && array_key_exists('photos_count',$ob) && $ob->photos_count == 0) {
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
      } else {

      $output .= '<figure-default> <img src="'.home_url().'/wp-content/plugins/inaturalist/img/default.png"/> </figure-default>';    
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
  $output = '<div class="observation_single_wrapper">
  <div class="inat_observation_single" id="obs_'.$observation->id.'">
    <figure class="photo_single">';
    if ( observation_photos_count == 0) {
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
    $output .= '</div> </div>     
      </div>'; //wrapper

  return $output;
}

function theme_list_places($places, $params) {
  $output = '';
  $prev_url = $base_url . '/inat/places/';
  $next_url = $prev_url;
  if($current_page > 1) {
    $prev_url .= $current_page - 1;
  } else {
    $prev_url .= '1';
  }
  $next_url .= $current_page + 1;
  foreach($places as $id => $place) {
    $output .= '<div class="inat_place row row-'.$id.'" id="prj_'.$place->id.'">
      <div id="map-'.$place->id.'" style="width: 175px; height: 175px;"></div>
      <script type="text/javascript">
        var map = L.map("map-'.$place->id.'").setView([51.505, -0.09], 13);
          L.tileLayer("http://{s}.tile.osm.org/{z}/{x}/{y}.png", {
          maxZoom: 18
        }).addTo(map);
        var bounds = new Array();
        bounds.push(new Array(['.$place->nelat.', '.$place->nelng.']));
        bounds.push(new Array(['.$place->swlat.', '.$place->swlng.']));
        L.marker().setLatLng(['.$place->latitude.', '.$place->longitude.']).addTo(map);
        map.fitBounds(bounds);
      </script>
    <h2><a href="'.site_url(). '/?'.http_build_query(array('page_id' => get_option('inat_post_id'), 'verb' => 'places', 'id' => $place->id)).'">'.$place->display_name.'</a></h2>
    </div>';
  }
  $prev_url = site_url(). '/?'.http_build_query(array('page_id' => get_option('inat_post_id') , 'verb' => $params['verb'], 'page' => $params['page'] - 1));
  $next_url = site_url(). '/?'.http_build_query(array('page_id' => get_option('inat_post_id') , 'verb' => $params['verb'], 'page' => $params['page'] + 1));
  $output .= '<div class="clearfix"> </div>
  <div class="pager-wrapper">';
    if($params['page'] > 1) {
      $output .= '<span id="prev-link" class="pager link"><a href="'.$prev_url.'">'.__('Prev','inat').'</a></span>&nbsp;&nbsp;';
    }
    $output .= '<span id="next-link" class="pager link"><a href="'.$next_url.'">'.__('Next','inat').'</a></span>
  </div>';

  return $output;
}
function theme_place($place) {
  $output .= '<div class="inat_place" id="place_'.$place->id.'">
    <div id="map-'.$place->id.'" style="width: 400px; height: 400px;"></div>
    <script type="text/javascript">
      var map = L.map("map-'.$place->id.'").setView([51.505, -0.09], 13);
        L.tileLayer("http://{s}.tile.osm.org/{z}/{x}/{y}.png", {
        maxZoom: 18
      }).addTo(map);
      var bounds = new Array();
      bounds.push(new Array(['.$place->nelat.', '.$place->nelng.']));
      bounds.push(new Array(['.$place->swlat.', '.$place->swlng.']));
      L.marker().setLatLng(['.$place->latitude.', '.$place->longitude.']).addTo(map);
      map.fitBounds(bounds);
    </script>
  <h2><a href="'.site_url() . '/?'. http_build_query(array('page_id' => get_option('inat_post_id'), 'verb' => 'places', 'id' => $place->id)).'">'.$place->display_name.'</a></h2>';
  if($place->parent_id != '') {
    $output .= '<a href="'.site_url() .'/?'.http_build_query(array('page_id' => get_option('inat_post_id'), 'verb' => 'places', 'id' => $place->parent_id)).'">'.__('Parent','inat').'</a>';
  }
  $output .= '</div>';

  return $output;
}
function theme_list_projects($list_projects, $params) {
  $output = '';
  foreach($list_projects as $id => $projects) {
    $output .= '<div class="inat_project row" id="prj_'.$projects->id.'">
      <div class="photo">' ;      
        if(empty($projects->icon_url)) {  
           $output .= '<figure-default> <img src="'.home_url().'/wp-content/plugins/inaturalist/img/default.png"/> </figure-default>';    
        }
        else{  
           $output .='<img src="'.$projects->icon_url.'"/>';
        }
      $output .='</div> <!-- /photo -->
      <h2><a href="'.site_url() . '/?' . http_build_query(array('page_id' => get_option('inat_post_id'), 'verb' => 'projects', 'id' => $projects->id)).'">'.$projects->title.'</a></h2>
      <div class="description">'.$projects->description.'</div>
    </div>';
  }
  $prev_url = site_url(). '/?'.http_build_query(array('page_id' => get_option('inat_post_id') , 'verb' => $params['verb'], 'page' => $params['page'] - 1));
  $next_url = site_url(). '/?'.http_build_query(array('page_id' => get_option('inat_post_id') , 'verb' => $params['verb'], 'page' => $params['page'] + 1));
  $output .= '<div class="clearfix"> </div>
  <div class="pager-wrapper">';
    if($params['page'] > 1) {
      $output .= '<span id="prev-link" class="pager link"><a href="'.$prev_url.'">'.__('Prev','inat').'</a></span>&nbsp;&nbsp;';
    }
    $output .= '<span id="next-link" class="pager link"><a href="'.$next_url.'">'.__('Next','inat').'</a></span>
  </div>';

  return $output;
}
function theme_project($projects) {
  $output = '
  <div class="inat_project row" id="prj_'.$projects->id.'">
    <div class="photo">'; 
      if(empty($projects->icon_url)){ 
      $output .= '<figure-default> <img src="'.home_url().'/wp-content/plugins/inaturalist/img/default.png"/> </figure-default>';    
      }
      else { 
        $output .='<img src="'.$projects->icon_url.'"/>';
      }
    $output .='</div> <!-- /photo -->
    <h2><a href="'.site_url().'/?'. http_build_query(array('page_id' => get_option('inat_post_id'), 'verb' => 'taxa', 'id' => $projects->id)).'">'.$projects->title.'</a></h2>
    <div class="description">'.$projects->description.'</div>
  </div>';
  return $output;
}
function theme_list_taxa($taxons, $params) {
  $output = '<div id="taxa-wrapper">';
  foreach($taxons as $id => $taxa) {
    $output .= '<div class="inat_taxa row row-'.$id.'" id="prj_'.$taxa->id.'">
      <div class="photo">
        <img src="'.$taxa->photo_url.'"/>
      </div> <!-- /photo -->
      <h2><a href="'.site_url() . '/?'. http_build_query(array('page_id' => get_option('inat_post_id'), 'verb' => 'taxa', 'id' => $taxa->id)).'">'.$taxa->common_name->name.' ('.$taxa->observations_count.') </a></h2>

      <div class="description">'.$taxa->wikipedia_summary.'</div>
  </div>';
  }
  $output .= '</div>';
  return $output;
}
function theme_user($user) {
  $output .= '<div class="inat_project row" id="prj_'.$user->id.'">
    <div class="photo">
      <img src="'.$user->medium_user_icon_url.'"/>
    </div> <!-- /photo -->
    <h2><a href="'.site_url() . '/?'. http_build_query(array('page_id' => get_option('inat_post_id'), 'verb' => 'users', 'id' => $user->id)).'">'.$user->name.'</a></h2>
    <div class="description">'.$user->description.'</div>
  </div>';

  return $output;
}
function theme_taxon($taxa) {
  $output = '
  <div class="inat_project row" id="prj_'.$taxa->id.'">
      <img src="'.$taxa->photo_url.'"/>
    <h2><a href="'.site_url() .'/?' . http_build_query(array('page_id' => get_option('inat_post_id'), 'verb' => 'taxa', 'id' => $taxa->id)).'">'.$taxa->name.'</a></h2>
    <div class="description">'.$taxa->wikipedia_summary.'</div>';
    if($taxa->id != 48460){
      $output .= '<a href="'.site_url(). '/?'. http_build_query(array('page_id' => get_option('inat_post_id'), 'verb' => 'taxa', 'id' => $taxa->parent_id)).'">'.__('Parent','inat').'</a>';
    }
  $output .= '</div>';

  return $output;
}

/**
 * add observation form
 */

function theme_add_obs () {
  $output = '
<form accept-charset="UTF-8" id="inat-obs-add" method="post" action="'.site_url().'/wp-content/plugins/inaturalist/addobs.php">
  <div>
    <div class="form-item form-type-textfield form-item-inat-obs-add-species-guess">
      <label for="edit-inat-obs-add-species-guess">'.__('What did you see?', 'inat').' </label>
      <input type="text" class="form-text" maxlength="128" size="60" value="" name="inat_obs_add_species_guess" id="edit-inat-obs-add-species-guess">
    </div>
    <div class="form-item form-type-textfield form-item-inat-obs-add-taxon-id">
      <label for="edit-inat-obs-add-taxon-id">'.__('Taxon ', 'inat').'</label>
      <input type="text" class="form-text" maxlength="128" size="60" value="" name="inat_obs_add_taxon_id" id="edit-inat-obs-add-taxon-id">
    </div>
    <div class="form-item form-type-radios form-item-inat-obs-add-id-please">
      <label for="edit-inat-obs-add-id-please">'.__('ID Please? ', 'inat').'</label>
        <div class="form-radios" id="edit-inat-obs-add-id-please"><div class="form-item form-type-radio form-item-inat-obs-add-id-please">
          <input type="radio" class="form-radio" value="0" name="inat_obs_add_id_please" id="edit-inat-obs-add-id-please-0">  <label for="edit-inat-obs-add-id-please-0" class="option">'.__('No ', 'inat').'</label>
        </div>
        <div class="form-item form-type-radio form-item-inat-obs-add-id-please">
          <input type="radio" class="form-radio" value="1" name="inat_obs_add_id_please" id="edit-inat-obs-add-id-please-1">  <label for="edit-inat-obs-add-id-please-1" class="option">'.__('Yes ', 'inat').'</label>
        </div>
      </div>
    </div>
    <div class="form-item form-type-textfield form-item-inat-obs-add-observed-on-string">
      <label for="edit-inat-obs-add-observed-on-string">'.__('Observed on ', 'inat').'</label>
      <input type="text" class="form-text" maxlength="128" size="60" value="'.date('Y-m-d').'" name="inat_obs_add_observed_on_string" id="edit-inat-obs-add-observed-on-string">
      <div class="description">'.__('YYYY-MM-DD, p.e. 2014-04-28', 'inat').'</div>
    </div>
    <div class="form-item form-type-textfield form-item-inat-obs-add-time-zone">
      <label for="edit-inat-obs-add-time-zone">'.__('Time zone ', 'inat').'</label>
      <input type="text" class="form-text" maxlength="128" size="60" value="Europe/Berlin" name="inat_obs_add_time_zone" id="edit-inat-obs-add-time-zone">
    </div>
    <div class="form-item form-type-textarea form-item-inat-obs-add-description">
      <label for="edit-inat-obs-add-description">'.__('Description ', 'inat').'</label>
      <div class="form-textarea-wrapper resizable textarea-processed resizable-textarea">
        <textarea class="form-textarea" rows="5" cols="60" name="inat_obs_add_description" id="edit-inat-obs-add-description"></textarea>
      </div>
    </div>
    <div class="form-item form-type-textfield form-item-inat-obs-add-place-guess">
      <label for="edit-inat-obs-add-place-guess">'.__('Place ', 'inat').'</label>
      <input type="text" class="form-text" maxlength="128" size="60" value="" name="inat_obs_add_place_guess" id="edit-inat-obs-add-place-guess">
    </div>
    <div class="form-item form-type-textfield form-item-inat-obs-add-latitude">
      <label for="edit-inat-obs-add-latitude">'.__('Latitude ', 'inat').'</label>
      <input type="text" class="form-text" maxlength="128" size="60" value="" name="inat_obs_add_latitude" id="edit-inat-obs-add-latitude">
      <div class="description">'.__('Latitude of the observation. Presumed datum is WGS84.', 'inat').'</div>
    </div>
    <div class="form-item form-type-textfield form-item-inat-obs-add-longitude">
      <label for="edit-inat-obs-add-longitude">'.__('Longitude ', 'inat').'</label>
      <input type="text" class="form-text" maxlength="128" size="60" value="" name="inat_obs_add_longitude" id="edit-inat-obs-add-longitude">
      <div class="description">'.__('Longitide of the observation. Presumed datum is WGS84.', 'inat').'</div>
    </div>
    <input type="hidden" value="form-wgvLgl_girxRCnRkMKXJ6FAoQrNvYibo5lvowsTUbJo" name="form_build_id">
    <input type="hidden" value="__3eDu39QL78w2-XZHT9yxiGC5t3_zN2j5-BZAlLctg" name="form_token">
    <input type="hidden" value="inat_obs_obs_add" name="form_id">
    <div id="edit-actions" class="form-actions form-wrapper">
      <input type="submit" class="form-submit" value="'.__('Add observation', 'inat').'" name="op" id="edit-submit">
    </div>
  </div>
  <input type="hidden" name="inat_base_url" value="'.get_option('inat_base_url').'" />
  <input type="hidden" name="inat_login_id" value="'.get_option('inat_login_id').'" />
  <input type="hidden" name="site_url" value="'.site_url().'" />
  <input type="hidden" name="inat_login_callback" value="'.get_option('inat_login_callback').'" />
  <input type="hidden" name="inat_post_id" value="'.get_option('inat_post_id').'" />
</form>
    ';
  return $output;
}
?>
