<?
/** 
 * Script procesor for add/user form
 */
$states = array();
// Validate
if($_POST['inat_login_usradd_pwd'] != $_POST['inat_login_usradd_pwdc']) {
    //add_filter( 'display_post_states','inat_state1');
    return;
} else {
    // Actions token
    $verb = 'users.json';
    $data = '?user[email]='.$_POST['inat_login_usradd_email'].'&user[login]='.$_POST['inat_login_usradd_login'].'&user[password]='.$_POST['inat_login_usradd_pwd'].'&user[password_confirmation]='.$_POST['inat_login_usradd_pwdc'];

    $url = $_POST['inat_base_url'].'/'.$verb.$data;
    $opt = array('http' => array('method' => 'POST', 'content' => $data, 'header' => 'Content-Type: application/x-www-form-urlencoded'));
    //DEBUG
    //echo '<pre>';
    //echo var_dump($url);
    //echo var_dump($opt);
    $context  = stream_context_create($opt);
    $result = file_get_contents($url, false, $context);
    $json = json_decode($result);
    if(array_key_exists('errors',$json)) {
      //add_filter( 'display_post_states','inat_state2');
    } else {
      //add_filter( 'display_post_states','inat_state3');
    }
}

      // redirect
      // http://codex.wordpress.org/Function_Reference/wp_redirect
// DEBUG
header("Location: ".$_POST['site_url'].'/?'.http_build_query(array('page_id' => $_POST['inat_post_id'])));
exit();
//echo var_dump($_POST);
//echo '<br /><hr><br /><pre>';
//echo var_dump($json);
function inat_state1($states) {
    $states[] = 'Passwords not match';
    return $states;
}
function inat_state2($states) {
    $states[] = 'Unexpected error has been produced';
    return $states;
}
function inat_state3($states) {
    $states[] = 'iNaturalist user has been corectly created';
    $states[] = 'In order to login and authorize this app visit: '.$_POST['inat_base_url'].'/oauth/authorize?client_id='.$_POST['inat_login_id'].'&redirect_uri='.$_POST['inat_login_callback'].'&response_type=code';
    return $states;
}
?>
