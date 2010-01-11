{strip}
{if $Used.Admin}
    {if !isset($Deep)}
		<div id="adm-total">
			<ul id="sitemap" class="cms-sitemap">
	{else}
		<ul>
	{/if}
		{foreach key=key item=item from=$Static}
			{if $PageUrl.absCurrent!=$item.Url}
				{capture name=mapitem}
				<li class="b-{if $item.IsModule}module{else}static{/if}item" id="mapitem-{$item.Id}">
					<dl class="b-mapitem">
						<dt><a href="{$item.Url}">{$item.Name}</a></dt>
						{if !$item.IsModule}<dd><i class="b-btn b-addbtn png" id="admico-mapItemAdd-{$item.Id}" title="Добавить"></i></dd>{/if}
						<dd><i class="b-btn b-edtbtn png" id="admico-mapItemEdit-{$item.Id}" title="Редактировать"></i></dd>
						<dd><i class="b-btn b-delbtn png" id="admico-mapItemRemove-{$item.Id}" title="Удалить"></i></dd>
						<img class="clear" src="img/blank.gif" alt="" />
					</dl>	
					{if $item.Sub}{include file="file:`$Template.Module`" Static=$item.Sub Deep=2}{/if}				
				</li>
				{/capture}{assign var='mapitem' value=`$smarty.capture.mapitem`}{$mapitem}
			{/if}
        {/foreach}
	</ul>	
	{if !isset($Deep)}</div>{/if}
{else}
	<p>Войдите как администратор, чтобы редактировать данный раздел сайта.</p>
{/if}
{/strip}
