{strip}
{if $Used.Admin}
    {if !isset($Deep)}
        {assign var="Menu" value=`$Catalogue.Menu`}
		<div id="adm-total">
			<ul id="sitemap">
	{else}
		<ul>
	{/if}
		{foreach key=key item=item from=$Menu}
			<li class="b-{if $item.IsModule}module{else}static{/if}item" id="mapitem-{$item.Id}-{$item.Parent}">
				<dl>
					<dt><a href="{$item.Url}">{$item.Name}</a></dt>
					<dd></dd>
				</dl>	
				{if $item.Sub}{include file="file:`$Template.Module`" Menu=$item.Sub Deep=2}{/if}
			</li>			
        {/foreach}
	</ul>	
	{if !isset($Deep)}</div>{/if}
{else}
	<p>Войдите как администратор, чтобы редактировать данный раздел сайта.</p>
{/if}
{/strip}
