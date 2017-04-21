<?php
require dirname(__FILE__).'/api/aplayer_music_api.php';

class aplayer_class
{
    function parseCallback($post, $config) {
        $api = self::check_https($config->api); $js = '';
        $pattern = self::get_shortcode_regex(array('aplayer'));
        preg_match_all("/$pattern/",$post,$matches);
        if (empty($matches[0])) return $post."<i id=\"apajax\" hidden=\"hidden\"></i>";
        for ($i=0;$i<count($matches[0]);$i++) {
            if ($matches[1][$i] == '[' and $matches[6][$i] == ']') {
                $ap["#ap#$i#"] = substr($matches[0][$i], 1, -1);
                $out = empty($out) ? self::str_replace_once($matches[0][$i], "#ap#$i#", $post) : self::str_replace_once($matches[0][$i], "#ap#$i#", $out);
            } else {
                $data = array('id' => md5(self::getUniqueId()));
                $atts = self::shortcode_parse_atts(self::str_replace_nbsp($matches[3][$i]));
                $data['narrow'] = isset($atts['narrow']) ? self::str2bool($atts['narrow']) : (bool)$config->narrow;
                $data['autoplay'] = isset($atts['autoplay']) ? self::str2bool($atts['autoplay']) : (bool)$config->autoplay;
                $data['mutex'] = isset($atts['mutex']) ? self::str2bool($atts['mutex']) : (bool)$config->mutex;
                $data['theme'] = isset($atts['theme']) ? $atts['theme'] : $config->theme;
                $data['mode'] = isset($atts['mode']) ? $atts['mode'] : ($config->mode==1 ? 'random' : ($config->mode==2 ? 'single' : ($config->mode==3 ? 'circulation' : 'order')));
                $data['preload'] = isset($atts['preload']) ? $atts['preload'] : $config->preload==1 ? 'metadata' : ($config->preload==2 ? 'none' : 'auto');
                isset($atts['listmaxheight']) ? $data['listmaxheight'] = $atts['listmaxheight'] : 0;
                $data['music'] = array();
                if ($matches[4][$i] != '/' && $matches[5][$i]) {
                    $regex = self::get_shortcode_regex(array('mp3'));
                    $content = self::str_replace_nbsp($matches[5][$i]);
                    if ((false !== strpos($content, '[')) && preg_match_all("/$regex/", $content , $all)) {
                        foreach ($all[0] as $k=>$v){
                            $atts = self::shortcode_parse_atts($all[3][$k]);
                            if (isset($atts['url'])) {
                                $tmp = array('url' => $atts['url']);
                                $tmp['pic'] = isset($atts['cover']) ? $atts['cover'] : (isset($atts['pic']) ? $atts['pic'] : '');
                                $data['showlrc'] = isset($atts['lrc']) ? ($tmp['lrc'] = $atts['lrc']) ? 3 : 3 :
                                    ($tmp['lrc'] = (preg_match('/\[(lrc)](.*?)\[\/\\1]/si', $all[5][$k], $lrc)) && $lrc[2] ?
                                        $lrc[2] : "[00:00.00]暂无歌词\n[99:00.00] ") ? 1 : 1;
                                $tmp['title'] = isset($atts['title']) ?  $atts['title'] : 'Unknown';
                                $tmp['author'] = isset($atts['author']) ? $atts['author'] : (isset($atts['artist']) ? $atts['artist'] : 'Unknown');
                                $data['music'][] = $tmp;
                            } elseif (isset($atts['id']))
                                $data['showlrc'] = ($data['music'][] = ($t = json_decode(self::curl($api.'?id='.(int)$atts['id'].'.song'), 1)) ? $t[0] : $t[0]) ? 3 : 3;
                        } $data['music'] = count($data['music']) == 1 ? $data['music'][0] : $data['music'];
                    }
                } else {
                    if (isset($atts['id'])) {
                        $data['showlrc'] = 3; $mix = $config->mix ? '.2' : '';
                        switch ($atts['type']) {
                            case 'collect': $data['music'] = json_decode(self::curl($api.'?id='.$atts['id'].'.collect'.$mix), 1); break;
                            case 'artist': $data['music'] = json_decode(self::curl($api.'?id='.$atts['id'].'.artist'.$mix), 1); break;
                            case 'album': $data['music'] = json_decode(self::curl($api.'?id='.$atts['id'].'.album'.$mix), 1); break;
                            default: $data['music'] = json_decode(self::curl($api.'?id='.$atts['id'].'.song'.$mix), 1); break;
                        }
                    } else {
                        isset($atts['url']) ? $data['music']['url'] = $atts['url'] : 0;
                        isset($atts['title']) ? $data['music']['title'] = $atts['title'] : 0;
                        isset($atts['artist']) ? $data['music']['author'] = $atts['artist'] :
                            (isset($atts['author']) ? $data['music']['author'] = $atts['author'] : 0);
                        $data['music']['pic'] = isset($atts['cover']) ? $atts['cover'] : (isset($atts['pic']) ? $atts['pic'] : '');
                        $data['showlrc'] = isset($atts['lrc']) ? 3 : 1;
                        isset($atts['lrc']) ? $data['music']['lrc'] = $atts['lrc'] : $data['showlrc'] = 0;
                    }
                }
                $data['showlrc'] = isset($atts['lrc']) && $atts['lrc']=='false' ? 0 : $data['showlrc'];
                if ($data['music']) $js .= 'APlayerOptions.push('.json_encode($data).');';
                $out = empty($out) ?
                    self::str_replace_once($matches[0][$i], "<div id=\"ap".$data['id']."\" class=\"aplayer\"></div>", $post):
                    self::str_replace_once($matches[0][$i], "<div id=\"ap".$data['id']."\" class=\"aplayer\"></div>", $out);
            }
        }
        $out .= "<i id=\"apajax\" hidden=\"hidden\">".$js."</i>";
        if (isset($ap)) foreach ($ap as $k => $v) $out = str_replace($k, $v, $out); return $out;
    }

    function shortcode_parse_atts($text) {
        $atts = array();
        $pattern = '/([\w-]+)\s*=\s*"([^"]*)"(?:\s|$)|([\w-]+)\s*=\s*\'([^\']*)\'(?:\s|$)|([\w-]+)\s*=\s*([^\s\'"]+)(?:\s|$)|"([^"]*)"(?:\s|$)|(\S+)(?:\s|$)/';
        $text = preg_replace("/[\x{00a0}\x{200b}]+/u", " ", $text);
        if (preg_match_all($pattern, $text, $match, PREG_SET_ORDER)) {
            foreach ($match as $m) {
                if (!empty($m[1])) $atts[strtolower($m[1])] = stripcslashes($m[2]);
                elseif (!empty($m[3])) $atts[strtolower($m[3])] = stripcslashes($m[4]);
                elseif (!empty($m[5])) $atts[strtolower($m[5])] = stripcslashes($m[6]);
                elseif (isset($m[7]) && strlen($m[7])) $atts[] = stripcslashes($m[7]);
                elseif (isset($m[8])) $atts[] = stripcslashes($m[8]);                    }
            foreach ($atts as &$value) if (false !== strpos($value, '<')) if (1 !== preg_match('/^[^<]*+(?:<[^>]*+>[^<]*+)*+$/', $value)) $value = '';
        } else $atts = ltrim($text);
        return $atts;
    }

    function get_shortcode_regex($tagnames = null) {
        return '\\[(\\[?)('.join('|', array_map('preg_quote', $tagnames)).
        ')(?![\\w-])([^\\]\\/]*(?:\\/(?!\\])[^\\]\\/]*)*?)(?:(\\/)\\]|\\](?:([^\\[]*+(?:\\[(?!\\/\\2\\])[^\\[]*+)*+)\\[\\/\\2\\])?)(\\]?)';     }
    
    function curl($url){
        $curl = curl_init();
        curl_setopt($curl, CURLOPT_URL, $url);
        curl_setopt($curl, CURLOPT_TIMEOUT, 10);
        curl_setopt($curl, CURLOPT_RETURNTRANSFER, 1);
        curl_setopt($curl, CURLOPT_CONNECTTIMEOUT, 10);
        curl_setopt($curl, CURLOPT_ENCODING, 'gzip');
        curl_setopt($curl, CURLOPT_SSL_VERIFYPEER, 0);
        curl_setopt($curl, CURLOPT_SSL_VERIFYHOST, 0);
        $result = curl_exec($curl); curl_close($curl); return $result;      }
    
    function check_https($url) { return ((isset($_SERVER['HTTPS']) and $_SERVER['HTTPS']=='on') or
    (isset($_SERVER['HTTP_X_FORWARDED_PROTO']) and $_SERVER['HTTP_X_FORWARDED_PROTO'] == 'https')) ?
        str_replace('http://','https://',$url) : str_replace('https://','http://',$url);                }
        
    static $playerID = 0; function getUniqueId() { return self::$playerID++; }
    function str2bool($str) { return $str == 'true' ? $str == 'false' ? 0 : 1 : $str; }
    function str_replace_nbsp($str) { return strip_tags(htmlspecialchars_decode(str_replace('&nbsp;',' ',$str))); }
    function str_replace_once($n, $r, $h) { return ($p = strpos($h, $n)) === false ? $h : substr_replace($h, $r, $p, strlen($n)); }
}