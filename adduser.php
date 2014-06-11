<?
/**
 * Plugin sub-file
 * Plugin Name: iNaturalist v1
 * Plugin URI: http://www.inaturalist.org
 * Description: This plugin connects your wordpress to inaturalist platform 
 * Version: 1
 * Author: Julià Mestieri for Projecte Ictineo SCCL (http://projecteictineo.com) 
 * Author URI: http://projecteictineo.com
 * License: aGPLv3
 */
/** 
 * Script procesor for add/user form
 */
$states = array();
// Validate
if($_POST['inat_login_usradd_pwd'] != $_POST['inat_login_usradd_pwdc']) {
    //return;
} else {
    // Actions token
    $verb = 'users.json';
    $data = '?user[email]='.$_POST['inat_login_usradd_email'].'&user[login]='.$_POST['inat_login_usradd_login'].'&user[password]='.$_POST['inat_login_usradd_pwd'].'&user[password_confirmation]='.$_POST['inat_login_usradd_pwdc'];

    $url = $_POST['inat_base_url'].'/'.$verb.$data;
    $opt = array('http' => array('method' => 'POST', 'content' => $data, 'header' => 'Content-Type: application/x-www-form-urlencoded'));
    $context  = stream_context_create($opt);
    $result = file_get_contents($url, false, $context);
    $json = json_decode($result);
}
header("Location: ".$_POST['site_url'].'/?'.http_build_query(array('page_id' => $_POST['inat_post_id'])));
exit();
?>
