<?php require dirname(__FILE__).'/NeteaseMusicAPI_mini.php';

class aplayer_music_api {
    function url($id) {
        $api = new NeteaseMusicAPI(); $api = json_decode($api->url((int)$id), 1);
        return $api['code'] == 200 ? $api['data'][0]['url'] : 0;                    }
    function pic($id) {
        $api = new NeteaseMusicAPI(); $t = json_decode($api->detail((int)$id))->songs[0]->al->id; $api = json_decode($api->album((int)$t), 1);
        return $api['code'] == 200 ? $api['album']['picUrl'] : 0;                   }
    function lrc($id, $lang = 0) {
        $api = new NeteaseMusicAPI(); $api = json_decode($api->lyric((int)$id), 1);
        return $api['code'] == 200 ? ($out = $lang ? self::mix_lyric($api) : $api['lrc']['lyric']) ? $out : "[00:00.00]暂无歌词\n[99:00.00] " : 0; }
    function song($ids, $url, $mix) {
        $api = new NeteaseMusicAPI();
        foreach (explode(',', $ids) as $id) if ($t = json_decode($api->detail((int)$id), 1) && $t['code'] == 200) $o[] = $api['songs'][0];
        return self::playlist($o, $url, $mix);                                      }
    function collect($id, $url, $mix) {
        $api = new NeteaseMusicAPI(); $api = json_decode($api->playlist((int)$id), 1);
        return $api['code'] == 200 ? self::playlist($api['playlist']['tracks'], $url, $mix) : 0;        }
    function artist($id, $url, $mix) {
        $api = new NeteaseMusicAPI(); $api = json_decode($api->artist((int)$id), 1);
        return $api['code'] == 200 ? self::playlist($api['hotSongs'], $url, $mix) : 0;                  }
    function album($id, $url, $mix) {
        $api = new NeteaseMusicAPI(); $api = json_decode($api->album((int)$id), 1);
        return $api['code'] == 200 ? self::playlist($api['songs'], $url, $mix) : 0;                     }
    function playlist($arr, $api, $mix) {
        foreach ($arr as $v) $o[] = array(
            'title' => $v['name'],
            'author' => self::ar2str($v['ar']),
            'url' => $api.'?id='.$v['id'].'.mp3',
            'pic' => $api.'?id='.$v['id'].'.jpg',
            'lrc' => $api.'?id='.$v['id'].'.'.($mix ? 'lrc2' : 'lrc')  ); return $o;   }
    function mix_lyric($json) {
        if ($json['tlyric']['lyric']) {
            $lrc = explode("\n", str_replace('undefined', '', $json['lrc']['lyric'])); $cnlrc = explode("\n", $json['tlyric']['lyric']);
            foreach ($lrc as $v) {
                if (!empty($v)) { $lrc_src = explode(']', $v); $lrc_line[self::time_format($lrc_src[0])] = $lrc_src[1]; }}
            foreach ($cnlrc as $v) {
                if (!empty($v)) { $cnlrc_src = explode(']', $v); $cnlrc_line[self::time_format($cnlrc_src[0])] = $cnlrc_src[1]; }}
            foreach ($cnlrc_line as $k => $v) $lrc_line[$k] = !empty($v) && !(($s=$lrc_line[$k]) == $v) ? $s.' ['.self::cnlrc_format($v).']' : '';
            foreach ($lrc_line as $k => $v) $lrc_out .= $k.']'.$v."\n"; } else return str_replace('undefined', '', $json['lrc']['lyric']); return $lrc_out;  }
    function ar2str($r) { foreach ($r as $v) $o .= $v['name'].' / '; return substr($o, 0, -3); }
    function time_format($t) { $e = explode('.', $t); return str_replace($s = $e[1], substr($s, 0, 2), $t); }
    function cnlrc_format($c) { return str_replace(array('/', '[', ']', '【', '】', '〖', '〗'), '', $c); }
}