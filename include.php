<?php
require dirname(__FILE__).'/function.php';
$APlayer = new APlayer_class();
RegisterPlugin("APlayer","ActivePlugin_APlayer");

function ActivePlugin_APlayer() {
	Add_Filter_Plugin('Filter_Plugin_ViewPost_Template','APlayer_Filter_Plugin_ViewPost_Template');
	Add_Filter_Plugin('Filter_Plugin_ViewList_Template','APlayer_Filter_Plugin_ViewList_Template');
	Add_Filter_Plugin('Filter_Plugin_Zbp_MakeTemplatetags','APlayer_Filter_Plugin_Zbp_MakeTemplatetags');
}

function APlayer_Filter_Plugin_ViewPost_Template(&$template) {
    global $APlayer;
	global $zbp;
	$article = $template->GetTags('article');
	$article->Content = $APlayer->parseCallback($article->Content, $zbp->Config('APlayer'));
}

function APlayer_Filter_Plugin_ViewList_Template(&$template) {
    global $APlayer;
	global $zbp;
	$articles = $template->GetTags('articles');
	foreach($articles as $article) { $article->Intro = $APlayer->parseCallback($article->Intro, $zbp->Config('APlayer')); }
}

function APlayer_Filter_Plugin_Zbp_MakeTemplatetags() {
    global $zbp;
    $zbp->footer .= '<script type="text/javascript" src="'.$zbp->host.'zb_users/plugin/APlayer/APlayer.min.js?v=1.6.0"></script>'."\r\n"."<script>function apajaxload(){var APlayers=[];var APlayerOptions=[];if($(\"#apajax\").length>0){eval($(\"#apajax\").text());var len=APlayerOptions.length;for(var i=0;i<len;i++){APlayers[i]=new APlayer({element:document.getElementById('ap'+APlayerOptions[i]['id']),narrow:APlayerOptions[i]['narrow'],autoplay:APlayerOptions[i]['autoplay'],showlrc:APlayerOptions[i]['showlrc'],mutex:APlayerOptions[i]['mutex'],theme:APlayerOptions[i]['theme'],mode:APlayerOptions[i]['mode'],preload:APlayerOptions[i]['preload'],listmaxheight:APlayerOptions[i]['listmaxheight'],music:APlayerOptions[i]['music']})}}}apajaxload();</script>";
}

function InstallPlugin_APlayer() {
	global $zbp,$obj,$bucket;
    if (!$zbp->Config('APlayer')->HasKey('theme')) {
        $zbp->Config('APlayer')->api = 'https://api.fghrsh.net/music/aplayer_music_api/';
        $zbp->Config('APlayer')->narrow = 0;
        $zbp->Config('APlayer')->autoplay = 0;
        $zbp->Config('APlayer')->mutex = 1;
        $zbp->Config('APlayer')->theme = '#FADFA3';
        $zbp->Config('APlayer')->mode = 1;
        $zbp->Config('APlayer')->preload = 0;
        $zbp->Config('APlayer')->mix = 1;
        $zbp->SaveConfig('APlayer');
    }
}

function UninstallPlugin_APlayer() {
	global $zbp;
	$zbp->DelConfig('APlayer');
}