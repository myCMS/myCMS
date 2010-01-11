{strip}
<div class="b-articles">
	{if $Naf.Used.Single}
		{capture name=wrap}
		<div class="info"{if $Used.Admin} id="admbox-{$Naf.Single.Id}"{/if}>
			<h1{if $Used.Admin} id="admedt-{$Naf.Single.Id}-text1"{/if}>{$Naf.Single.Text1}</h1>
			<div{if $Used.Admin} id="admedt-{$Naf.Single.Id}-text2"{/if}>{$Naf.Single.Text2}</div>
		</div>
		{/capture}{assign var='wrap' value=`$smarty.capture.wrap`}{*{$wrap}*}
		{capture name=admin}{include file="file:`$Template.AdminWrapper`" Content=$wrap Id=$Naf.Single.Id Active=$Naf.Single.Active Single='1'}{/capture}{assign var='admin' value=`$smarty.capture.admin`}{$admin}
	{else}
		<ul class="article"{if $Used.Admin} id="adm-total"{/if}>
			{foreach key=keyArticles item=itemArticles from=$Naf.Content}
				{capture name=wrap}
					<li{if $Used.Admin} id="admbox-{$itemArticles.Id}"{/if}><a href="{$itemArticles.Url}">{$itemArticles.Text1}</a></li>
				{/capture}{assign var='wrap' value=`$smarty.capture.wrap`}{*{$wrap}*}
				{capture name=admin}{include file="file:`$Template.AdminWrapper`" Content=$wrap Id=$itemArticles.Id Active=$itemArticles.Active Single='0'}{/capture}{assign var='admin' value=`$smarty.capture.admin`}{$admin}
			{/foreach}
		</ul>
	{/if}
</div>
{/strip}