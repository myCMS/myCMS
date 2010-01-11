{strip}
<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.1//EN" "http://www.w3.org/TR/xhtml11/DTD/xhtml11.dtd">
<html xmlns="http://www.w3.org/1999/xhtml" xml:lang="ru">
    <head>
        <title>Polaris</title>
		<meta name="keywords" content="">
        <meta name="description" content="">
		{include file="file:page/head.tpl"}
    </head>
    <body>
		{if $Used.Admin}{include file="file:`$Template.AdminPanelMenu`"}{/if}
		<div id="body">
			<div class="b-head">
				<div class="b-left"><img class="i-logotype" src="img/logotype.jpg" width="181" height="40" alt="You are on the main page" /></div>
				<div class="b-right">
					<div class="b-icons">
						<div class="b-icon b-icon-active"><i class="b-icon1"></i></div>
						<div class="b-icon"><a href="{$PageUrl.Main}search/" title="Search"><i class="b-icon2"></i></a></div>
						<div class="b-icon"><a href="{$PageUrl.Main}feedback/" title="Feedback"><i class="b-icon3"></i></a></div>
						<div class="b-icon"><a href="{$PageUrl.Main}sitemap/" title="Sitemap"><i class="b-icon4"></i></a></div>
						<img class="clear" src="img/blank.gif" alt="" />
					</div>
					<div class="b-enter" id="cusomers_enter">
						{if $Used.Authorized}
							Welcome <font color="#ff0000">{$UserDetails.login}</font>&nbsp;<a href="logout/">Exit</a>
						{else}
							<a href="{$PageUrl.Main}auth" title="Войти">Cusomers enter</a>
						{/if}	
					</div>
				</div>
				<img class="clear" src="img/blank.gif" alt="" />
			</div>
			<div class="b-flags">
				<div class="b-flags-in">
				{foreach item=itemLangs key=keyLangs from=$Languages}
					{if $itemLangs.Selected == 0}
						<div><a href="{$itemLangs.Url}"><img src="img/flag_{$itemLangs.Name}.gif" width="16" height="11" alt="{$itemLangs.Interpretation}" title="{$itemLangs.Interpretation}" /></a></div>
					{/if}
				{/foreach}
				</div>
			</div>
			<div class="b-body b-indexbody">
				<div class="b-left">
					<p class="b-seltext">Group of&nbsp;companies &#147;Polaris&#148;&nbsp;&#151; leader in&nbsp;container shipping, port forwarding, contract maintenance.</p>
					{include file="file:`$Template.Level1`"}
					{include file="file:`$Template.NewsLatest`"}					
				</div>
				<div class="b-right">
					<div class="b-infoflash">
						<div class="b-infobox b-infobox-lightback">
							<div class="b-corns">
								<i class="png b-tl"></i>
								<i class="png b-tr"></i>
							</div>
							<div class="b-infoblock">
								<p><span class="red bold">You are with us for the first time?</span><br>View our <a href="#">presentation</a></p>
							</div>
							<div class="b-corns">
								<i class="png b-bl"></i>
								<i class="png b-br"></i>
							</div>
						</div>
						<div class="b-infobox b-infobox-darkback">
							<div class="b-corns">
								<i class="png b-tl"></i>
								<i class="png b-tr"></i>
							</div>
							<div class="b-infoblock">
								<img src="img/y_bl_container.gif" width="50" height="54" alt="" border="0">
								<h1 class="orange">your container</h1>
								<p>Information on all containers passing through ports of Odessa and Ilyichevsk.</p>
								<img src="img/y_bl_clock.gif" width="39" height="54" alt="" border="0">
								<h1 class="red">актуально</h1>
								<p>Direct service from China to Ilyichevsk. Without trans-shipment ports. Average transit time: 25-28 days.</p>
							</div>
							<div class="b-corns">
								<i class="png b-bl"></i>
								<i class="png b-br"></i>
							</div>
						</div>
					</div>
					<div class="b-flashbox" id="illsbox"></div>
					<script type="text/javascript">
						var so = new SWFObject("img/main.swf", "", "100%", "500", "8", "#FFFFF");
						so.addParam("wmode", "transparent");
						so.write("illsbox");
					</script>
				</div>
				<img class="clear" src="img/blank.gif" alt="" />
			</div>
		</div>
		<div id="base">
			<div class="b-base">
				<div class="b-left">
					<p>&copy;&nbsp;{$Block.Copyright}, Holding &#147;Polaris&#148;</p>
				</div>
				<div class="b-right">
					<p>Ukraine, 65025, Odessa, st. Arnautska B., 17, of. 8a<br>+38&nbsp;(048) 786-0-230, 786-0-869, 786-00-55</p>
					<p class="contact_links"><span><a href="{$PageUrl.Main}feedback">Feedback form</a></span></p>
				</div>
				<img class="clear" src="img/blank.gif" alt="" />
			</div>
			<img class="clear" src="img/blank.gif" alt="" />
		</div>
    </body>
</html>
{/strip}