<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.1//EN" "http://www.w3.org/TR/xhtml11/DTD/xhtml11.dtd">
<html xmlns="http://www.w3.org/1999/xhtml" xml:lang="ru">
    <head>
        <title>{if $Content.Title == ""}{$Content.Name}{else}{$Content.Title}{/if}</title>
		<meta name="keywords" content="{$Content.Keywords}">
        <meta name="description" content="{$Content.Description}">
		{include file="file:page/head.tpl"}
    </head>
{if $Used.404error}{include file="file:`$Template.404error`"}
{else}
    <body>
		{if $Used.Admin}{include file="file:`$Template.AdminPanelMenu`"}{/if}
		<div id="body">
			<div class="b-head b-inside">
				<div class="b-left"><a href="{$PageUrl.Main}"><img class="i-logotype" src="img/logotype.jpg" width="181" height="40" alt="" /></a></div>
				<div class="b-right">
					<div class="b-icons">
						<div class="b-icon"><a href="{$PageUrl.Main}"><i class="b-icon1"></i></a></div>
						{if $PageUrl.Current != "search"}
							<div class="b-icon"><a href="{$PageUrl.Main}search/" title="Поиск по сайту"><i class="b-icon2"></i></a></div>
						{else}
							<div class="b-icon b-icon-active"><i class="b-icon2"></i></div>
						{/if}
						{if $PageUrl.Current != "feedback"}
							<div class="b-icon"><a href="{$PageUrl.Main}feedback/" title="Обратная связь"><i class="b-icon3"></i></a></div>
						{else}
							<div class="b-icon b-icon-active"><i class="b-icon3"></i></div>
						{/if}
						{if $PageUrl.Current != "sitemap"}
							<div class="b-icon"><a href="{$PageUrl.Main}sitemap/" title="Карта сайта"><i class="b-icon4"></i></a></div>
						{else}
							<div class="b-icon b-icon-active"><i class="b-icon4"></i></div>
						{/if}
						{if $PageUrl.Current != "search"}
                            <div>
                                <form method="post" action="{$PageUrl.Main}search/">
                                    <input type="search" name="search" id="searchline" style="width:75%;"/>
                                </form>
                            </div>
						{/if}
						<img class="clear" src="img/blank.gif" alt="" />
					</div>
					<div class="b-enter" id="cusomers_enter">
						{if $Used.Authorized}
							Добро пожаловать <font color="#ff0000">{$UserDetails.Login}</font>&nbsp;<a href="logout/">Выйти</a>
						{else}
							<a href="{$PageUrl.Main}auth" title="Войти">Вход для клиентов</a>
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
			<div class="b-body b-inside">
				<div class="b-left">
					{include file="file:`$Template.Level1`"}
					{if $menu_gallery}{include file="file:$menu_gallery_template_name" gallery_menu=$gallery_menu}{/if}
                    {if isset($Block.Vote) && count($Block.Vote)}
                        {foreach item=iVote key=kVote from=$Block.Vote}
                            {include file="file:`$Template.Vote`" Vote=$iVote}
                        {/foreach}
                    {/if}
					{include file="file:`$Template.NewsLatest`"}
					{include file="file:`$Template.CatalogueLatest`"}
					{*{include file="file:`$Template.CatalogueRandom`"}*}
					{include file="file:`$Template.Facts`"}
					{include file="file:`$Template.Image`"}
                    {if count($Block.Pricelist)>1}
                    <div class="b-randomimages">
                        <h1>Прайслист</h1>
                        <a href="{$PageUrl.Main}{$Block.Pricelist.Filename}">Прайслист за {$Block.Pricelist.Date.Day}.{$Block.Pricelist.Date.Month}.{$Block.Pricelist.Date.Year}</a>
                    </div>
                    {/if}
				</div>
				<div class="b-right">
					<div class="b-waybar">
						{if $PageNames.Level1 && ($PageUrl.Level1 != $PageUrl.Current)}<span><a href="{$PageUrl.absLevel1}">{$PageNames.Level1}</a>&nbsp;/&nbsp;</span>{/if}
						{if $PageNames.Level2 && ($PageUrl.Level2 != $PageUrl.Current)}<span><a href="{$PageUrl.absLevel2}">{$PageNames.Level2}</a>&nbsp;/&nbsp;</span>{/if}
						{if $PageNames.Level3 && ($PageUrl.Level3 != $PageUrl.Current)}<span><a href="{$PageUrl.absLevel3}">{$PageNames.Level3}</a>&nbsp;/&nbsp;</span>{/if}
					</div>
					<div class="b-content">
						<h1>{$Content.Name}</h1>
						{if $Used.Module}{include file="file:`$Template.Module`"}
						{elseif $Used.SiteMap}{include file="file:`$Template.SiteMap`" SiteMap=$Display.SiteMap}
                        {elseif $Used.External}{include file="file:`$Template.External`"}
						{else}{$Content.Text}
						{if isset($Template.Feedback)}{include file="file:`$Template.Feedback.main_feedback`"}{/if}
						{/if}
					</div>
				</div>
				<img class="clear" src="img/blank.gif" alt="" />
			</div>
		</div>
		<div id="base">
			<div class="b-base b-inside">
				<div class="b-left">
					<p>&copy;&nbsp;{$Block.Copyright}, Холдинг &laquo;Полярис&raquo;</p>
				</div>
				<div class="b-right">
					<p>Украина, 65025, Одесса, ул.&nbsp;Б.&nbsp;Арнаутская, 17,&nbsp;оф.&nbsp;8а<br>+38&nbsp;(048) 786-0-230, 786-0-869, 786-00-55</p>
					<p class="contact_links"><span><a href="{$PageUrl.Main}feedback">Форма обратной связи</a></span></p>
				</div>
				<img class="clear" src="img/blank.gif" alt="" />
			</div>
			<img class="clear" src="img/blank.gif" alt="" />
		</div>
    </body>
{/if}
</html>