<div class="b-menucat">
	<ul>
	{foreach key=keyMenuCat item=itemMenuCat from=$CatalogueMenu}
		{if $itemMenuCat.Selected == 1}
			<li class="active">{$itemMenuCat.Name}</li>
		{elseif $itemMenuCat.OnActive == 1}
			<li class="active"><a href="{$itemMenuCat.Url}" title="{$itemMenuCat.Name}">{$itemMenuCat.Name}</a></li>
		{else}
			<li><a href="{$itemMenuCat.Url}" title="{$itemMenuCat.Name}">{$itemMenuCat.Name}</a></li>
		{/if}
		{if is_array($itemMenuCat.Sub)}
			{include file="file:`$Template.CatalogueMenu`" CatalogueMenu=$itemMenuCat.Sub}
		{/if}
	{/foreach}
	</ul>
</div>