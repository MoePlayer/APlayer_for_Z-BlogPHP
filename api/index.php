<?php error_reporting(E_ALL^E_NOTICE^E_WARNING); require 'aplayer_music_api.php';

$api = new aplayer_music_api(); $switch = explode('.', $_GET['id']); $id = $switch[0]; $mix = $switch[2];
$url = str_replace('?'.$_SERVER["QUERY_STRING"], '', check_https('http://'.$_SERVER['SERVER_NAME'].$_SERVER["REQUEST_URI"])); 

switch ($switch[1]) {
    case 'mp3': short_header('audio/mp3'); exit(header('Location: '.check_https($api->url($id))));
    case 'jpg': short_header('image/jpg'); exit(header('Location: '.check_https($api->pic($id))));
    case 'lrc': short_header('text/plain'); exit(str_replace('undefined', '', $api->lrc($id, 0)));
    case 'lrc2': short_header('text/plain'); exit(str_replace('undefined', '', $api->lrc($id, 1)));
    case 'song': short_header('application/json'); exit(json_encode($api->song($id, $url, $mix)));
    case 'album': short_header('application/json'); exit(json_encode($api->album($id, $url, $mix)));
    case 'artist': short_header('application/json'); exit(json_encode($api->artist($id, $url, $mix)));
    case 'collect': short_header('application/json'); exit(json_encode($api->collect($id, $url, $mix)));
    default: header('http/1.1 404 Not Found'); exit('error');
}

function short_header($head) { header("Content-Type: $head;charset=UTF-8"); }
function check_https($url) { return ((isset($_SERVER['HTTPS']) and $_SERVER['HTTPS']=='on') or
    (isset($_SERVER['HTTP_X_FORWARDED_PROTO']) and $_SERVER['HTTP_X_FORWARDED_PROTO'] == 'https')) ?
        str_replace('http://','https://',$url) : str_replace('https://','http://',$url);                }