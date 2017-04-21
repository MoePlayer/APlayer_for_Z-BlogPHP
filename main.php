<?php
require '../../../zb_system/function/c_system_base.php';
require '../../../zb_system/function/c_system_admin.php';
$zbp->Load();
if (!$zbp->CheckRights('root')) {$zbp->ShowError(6);exit();}
if (!$zbp->CheckPlugin('APlayer')) {$zbp->ShowError(48);die();}
require '../../../zb_system/admin/admin_header.php';
require '../../../zb_system/admin/admin_top.php';

if(isset($_POST['api'])){
	foreach($_POST as $k => $v) $$k = $v; $tips = '';
	
	if(empty($api)){
	    $zbp->ShowHint('bad', 'API 地址不允许为空！');
	} else {
		if ($api != ($zbp->Config('APlayer')->api)) {
			$zbp->Config('APlayer')->api = $api;
			$tips = 'API 地址设置成功;';
		}
	}
	$mutex = in_array('mutex', $options) ? 1 : 0;
	$narrow = in_array('narrow', $options) ? 1 : 0;
	$autoplay = in_array('autoplay', $options) ? 1 : 0;
	$mix = in_array('mix', $options) ? 1 : 0;
	if ($narrow != $zbp->Config('APlayer')->narrow) {
	    $zbp->Config('APlayer')->narrow = $narrow;
	    $tips .= '设置已应用;';
	}
	if ($autoplay != $zbp->Config('APlayer')->autoplay) {
	    $zbp->Config('APlayer')->autoplay = $autoplay;
	    $tips .= '设置已应用;';
	}
	if ($mutex != $zbp->Config('APlayer')->mutex) {
	    $zbp->Config('APlayer')->mutex = $mutex;
	    $tips .= '设置已应用;';
	}
	if ($theme != $zbp->Config('APlayer')->theme) {
	    $zbp->Config('APlayer')->theme = $theme;
	    $tips .= '设置已应用;';
	}
	if ($mode != $zbp->Config('APlayer')->mode) {
	    $zbp->Config('APlayer')->mode = $mode;
	    $tips .= '设置已应用;';
	}
	if ($preload != $zbp->Config('APlayer')->preload) {
	    $zbp->Config('APlayer')->preload = $preload;
	    $tips .= '设置已应用;';
	}
	if ($mix != $zbp->Config('APlayer')->mix) {
	    $zbp->Config('APlayer')->mix = $mix;
	    $tips .= '设置已应用;';
	}
	$zbp->SaveConfig('APlayer');
	
	if (!empty($tips)) {
	    $tips = explode(";",$tips);
	    for ($i=0;$i<count($tips)-1;$i++) $zbp->ShowHint('good', $tips[$i]);
	} else $zbp->ShowHint('bad', '设置未更改');
}
?>
<link rel="stylesheet" href="jcolor/jcolor.min.css" type="text/css" />
<style>table,td,th,tr,.api,tr.color1,tr.color2,tr.color3,tr.color4 { background: rgba(0,0,0,0)!important; border: 2px solid rgba(100,100,100,0.2)!important; }</style>
<script type="text/javascript" src="jcolor/jcolor.min.js"></script>
<!-- 背景图取自 pixiv，作品ID：62477678。 （https://www.pixiv.net/member_illust.php?mode=medium&illust_id=62477678） -->
<div id="divMain" style="border-radius: 3px; padding: 10px; background: white url(<?php echo $zbp->host; ?>zb_users/plugin/APlayer/bg.png) no-repeat right bottom;">
    <div class="divHeader"><a href="https://app.zblogcn.com/?id=1321" target="_blank">APlayer for Z-BlogPHP</a> - 插件配置</div>
	    <div id="divMain2">
	        <form id="form1" name="form1" method="post">
                <table width="90%" style='padding:0px;margin:0px;' cellspacing='0' cellpadding='0' class="tableBorder">
                    <tr>
                        <th width='20%'><p align="center">设置</p></th>
                        <th width='70%'><p align="center">选项</p></th>
                    </tr>
                    <?php
                        $config = array(
		                    'api' => $zbp->Config('APlayer')->api,
                            'narrow' => $zbp->Config('APlayer')->narrow,
                            'autoplay' => $zbp->Config('APlayer')->autoplay,
                            'mutex' => $zbp->Config('APlayer')->mutex,
                            'theme' => $zbp->Config('APlayer')->theme,
                            'mode' => $zbp->Config('APlayer')->mode,
                            'preload' => $zbp->Config('APlayer')->preload,
                            'mix' => $zbp->Config('APlayer')->mix
		                );
                    ?>
                    <tr>
                        <td><b><p align="center">API 地址</p></b></td>
                        <td><p align="left"><input class="api" name="api" type="text" size="100%" value="<?php echo $config['api']; ?>" /></p></td>
                    </tr>
                    <tr>
                        <td><b><p align="center">附加/默认设置</p></b></td>
                        <td>
                            <p align="left"></p>
                            <p align="left">&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;预加载：&nbsp;&nbsp;
                                <input type="radio" name="preload" value="0" <?php if($config['preload']==0){echo 'checked="checked"';} ?>/>自动
                                &nbsp;&nbsp;&nbsp;
                                <input type="radio" name="preload" value="1" <?php if($config['preload']==1){echo 'checked="checked"';} ?>/>开启
                                &nbsp;&nbsp;&nbsp;
                                <input type="radio" name="preload" value="2" <?php if($config['preload']==2){echo 'checked="checked"';} ?>/>关闭
                            </p>
                            <p align="left">&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;播放模式：&nbsp;&nbsp;
                                <input type="radio" name="mode" value="0" <?php if($config['mode']==0){echo 'checked="checked"';} ?>/>顺序
                                &nbsp;&nbsp;&nbsp;
                                <input type="radio" name="mode" value="1" <?php if($config['mode']==1){echo 'checked="checked"';} ?>/>随机
                                &nbsp;&nbsp;&nbsp;
                                <input type="radio" name="mode" value="2" <?php if($config['mode']==2){echo 'checked="checked"';} ?>/>单曲循环
                                &nbsp;&nbsp;&nbsp;
                                <input type="radio" name="mode" value="3" <?php if($config['mode']==3){echo 'checked="checked"';} ?>/>列表循环
                            </p>
                            <p align="left">&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;
                                简洁模式&nbsp;<input type="checkbox" name="options[]" value="narrow" <?php if($config['narrow']==1){echo 'checked="checked"';} ?>/>&nbsp;&nbsp;
                                自动播放&nbsp;<input type="checkbox" name="options[]" value="autoplay" <?php if($config['autoplay']==1){echo 'checked="checked"';} ?>/>&nbsp;&nbsp;
                                静音其他实例&nbsp;<input type="checkbox" name="options[]" value="mutex" <?php if($config['mutex']==1){echo 'checked="checked"';} ?>/>&nbsp;&nbsp;
                                获取歌词翻译(如有)&nbsp;<input type="checkbox" name="options[]" value="mix" <?php if($config['mix']==1){echo 'checked="checked"';} ?>/>&nbsp;&nbsp;
                            </p>
                            <p align="left">---------------------------------------------------------</p>
                            <p align="left">&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;
                                自定义颜色&nbsp;&nbsp;
                                <input type="text" size="6" id="color" name="theme" value="<?php echo $zbp->Config('APlayer')->theme; ?>"/>&nbsp;&nbsp;
                                <a onclick="color_picker($('#color').val());">预览颜色</a>
                                <a class="APlayer-theme-color" style="float:left;padding:6px 10px"></a>
                            </p> 
                            <p align="left">&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;
                                推荐配色&nbsp;&nbsp;
                                <a onclick="color_picker('#FADFA3');" style="background-color:#FADFA3;border:1px solid #aaa">&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;</a>&nbsp;&nbsp;
                                <a onclick="color_picker('#7addeb');" style="background-color:#7addeb;border:1px solid #aaa">&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;</a>&nbsp;&nbsp;
                                <a onclick="color_picker('#dab3db');" style="background-color:#dab3db;border:1px solid #aaa">&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;</a>&nbsp;&nbsp;
                                <a onclick="color_picker('#e69184');" style="background-color:#e69184;border:1px solid #aaa">&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;</a>&nbsp;&nbsp;
                                <a onclick="color_picker('#acec8e');" style="background-color:#acec8e;border:1px solid #aaa">&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;</a>&nbsp;&nbsp;
                                <a onclick="color_picker('#ffffff');" style="background-color:#ffffff;border:1px solid #aaa">&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;</a>&nbsp;&nbsp;
                            </p>
                            <script>
                                function color_picker(hex) {
                                    $("#color").val(hex);$("#color").css("background-color",hex);
                                    $('.APlayer-theme-color').colorpicker().destroy();
                                    $('.APlayer-theme-color').colorpicker({
                                        labels: true,
                                        color: hex,
                                        colorSpace: 'rgb',
                                        expandEvent: 'mouseenter',
                                        collapseEvent: 'mouseleave mousewheel'
                                    });
                                    $('.APlayer-theme-color').on('newcolor', function (ev, colorpicker) {
                                        $("#color").val(colorpicker.toString('rgb'));$("#color").css("background-color",colorpicker.toString('rgb'));
                                    });
                                }
                                color_picker('<?php echo $zbp->Config('APlayer')->theme; ?>');
                            </script>
                            <p align="left">---------------------------------------------------------</p>
                            <p align="left">&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&loz;&nbsp;&nbsp;<a href="http://diygod.me" target="_blank">关于作者</a>&nbsp;&nbsp;|&nbsp;&nbsp;<a href="https://github.com/DIYgod/APlayer/issues" target="_blank">意见反馈</a>&nbsp;&nbsp;|&nbsp;&nbsp;<a href="https://www.anotherhome.net/2167" target="_blank">关于 APlayer 播放器</a></p>
                            <p align="left"></p>
                        </td>
                    </tr>
                </table>
                <div style="width:90%;float:inherit">
                    <div style="float:left;padding:10px 0">
                        &copy;2017 <a href="https://www.fghrsh.net" target="_blank" style="color:#333333">FGHRSH</a> - <a href="https://www.fghrsh.net/post/77.html" target="_blank" style="color:#333333">APlayer for Z-BlogPHP V1.3</a> (APlayer 1.6.0)
                    </div>
                    <div style="float:right;padding:5px 0;">
                        <input type="Submit" class="button" value="保存设置" />
                    </div>
                </div>
            </form>
        </div>
    </div>
</div>

<?php require $blogpath . 'zb_system/admin/admin_footer.php'; RunTime();