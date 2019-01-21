<?php
/**
 * Created by PhpStorm.
 * User: zhangshiqiu
 * Date: 2017/4/17
 * Time: PM5:21
 */

header('Content-Type: text/html; charset=utf-8');
$query = isset($_REQUEST['text_query']) ? $_REQUEST['text_query'] : false;
$results = false;


if ($query) {
    require_once('Apache/Solr/Service.php');
    $solr = new Apache_Solr_Service('localhost', 8983, '/solr/myexample/');
    if (!get_magic_quotes_gpc()) {
        $query = stripslashes($query);
    }
    try {
         $results = $solr->suggest($query);
        echo $results->getRawResponse();
    } catch (Exception $e) {
        die("<html><head><title>My Search</title><body><pre>{$e->__toString()}</pre></body></html>");
    }
}

?>

