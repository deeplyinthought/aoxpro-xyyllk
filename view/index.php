<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Transitional//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-transitional.dtd">
<html xmlns="http://www.w3.org/1999/xhtml">
<head>
<meta http-equiv="Content-Type" content="text/html; charset=utf-8" />
<title>授权后的页面</title>
<script src="http://tjs.sjs.sinajs.cn/t35/apps/opent/js/frames/client.js" language="JavaScript"></script>
<script> 
function authLoad(){
 
	App.AuthDialog.show({
	client_id : '<?=WB_AKEY;?>',    //必选，appkey
	redirect_uri : '<?=CANVAS_PAGE;?>',     //必选，授权后的回调地址
	height: 120    //可选，默认距顶端120px
	});
}
</script>
</head>
<body>
<?php if(!$this->_isAuth): ?>
<script>authLoad()</script>;
<img src="/images/cover.jpg" />
<?php else: ?>
<?php $_r = time() ?>
<div id="flashContent">
<object classid="clsid:d27cdb6e-ae6d-11cf-96b8-444553540000" width="760" height="570" id="LLK" align="middle">
<param name="movie" value="/swf/game.swf?_=<?php echo $_r ?>" />
<param name="quality" value="high" />
<param name="bgcolor" value="#f8c695" />
<param name="play" value="true" />
<param name="loop" value="true" />
<param name="wmode" value="window" />
<param name="scale" value="showall" />
<param name="menu" value="true" />
<param name="devicefont" value="false" />
<param name="salign" value="" />
<param name="allowScriptAccess" value="sameDomain" />
<!--[if !IE]>-->
<object type="application/x-shockwave-flash" data="/swf/game.swf?_=<?php echo $_r ?>" width="760" height="570">
<param name="movie" value="/swf/game.swf?_=<?php echo $_r ?>" />
<param name="quality" value="high" />
<param name="bgcolor" value="#f8c695" />
<param name="play" value="true" />
<param name="loop" value="true" />
<param name="wmode" value="window" />
<param name="scale" value="showall" />
<param name="menu" value="true" />
<param name="devicefont" value="false" />
<param name="salign" value="" />
<param name="allowScriptAccess" value="sameDomain" />
<!--<![endif]-->
<a href="http://www.adobe.com/go/getflash">
<img src="http://www.adobe.com/images/shared/download_buttons/get_flash_player.gif" alt="»ñAdobe Flash Player" />
</a>
<!--[if !IE]>-->
</object>
<!--<![endif]-->
</object>
</div>
<?php endif; ?>
</body>
</html>
