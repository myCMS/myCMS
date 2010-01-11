<meta http-equiv="Content-Type" content="text/html; charset=utf-8" />
<meta http-equiv="Content-Language" content="RU-ru, EN-en" />
<meta name="author" content="Azazello, Mart" />
<meta name="revisit-after" content="3 days" />
<meta name="robots" content="follow,index" />
<link rel="stylesheet" type="text/css" href="{$PageUrl.Base}common/css/main.css" />
{if $Used.Admin}{* including all admin styles *}
	<link rel="stylesheet" type="text/css" href="{$PageUrl.Base}../../adm/css/admin.css" />
	<link rel="stylesheet" type="text/css" href="{$PageUrl.Base}../../adm/css/datepicker.css" />
	<link rel="stylesheet" type="text/css" href="{$PageUrl.Base}../../adm/js/markitup/skins/simple/style.css" />
	<link rel="stylesheet" type="text/css" href="{$PageUrl.Base}../../adm/js/markitup/sets/html/style.css" />
	<!--[if lte IE 7]><link rel="stylesheet" href="{$PageUrl.Base}../../adm/css/ie-admin.css" type="text/css" media="screen" /><![endif]-->
{/if}
<!--[if IE 6]><link rel="stylesheet" href="{$PageUrl.Base}common/css/ie6.css" type="text/css" media="screen" /><![endif]-->
<!--[if IE 7]><link rel="stylesheet" href="{$PageUrl.Base}common/css/ie7.css" type="text/css" media="screen" /><![endif]-->	
<link rel="icon" href="{$PageUrl.Base}favicon.ico" />
<script src="{$PageUrl.Base}common/js/jquery.js" type="text/javascript"></script>
<script src="{$PageUrl.Base}common/js/jqtodo.js" type="text/javascript"></script>	
<script src="{$PageUrl.Base}common/js/swfobject.js" type="text/javascript"></script>
{if $Used.Admin}	
	<script type="text/javascript" src="{$PageUrl.Base}../../adm/js/ck/ckeditor.js"></script>
	<script type="text/javascript" src="{$PageUrl.Base}../../adm/js/cms.js"></script>
	<script type="text/javascript" src="{$PageUrl.Base}common/js/editorsetup.js"></script>
{/if}
<!--[if lte IE 6]>
	<script src="{$PageUrl.Base}common/js/pngfixing.js" type="text/javascript"></script>
	<script>DD_belatedPNG.fix('.png');</script>
<![endif]-->