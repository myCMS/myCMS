{strip}
<div class="b-menusub2">
	{foreach key=keyMenu item=itemMenu from=$Menu.Level3}
		{if $keyMenu == $PageId.Level3}
			<div class="active"><span>{$itemMenu.Name}</span></div>
		{else}
			<div><a href="{$itemMenu.Url}">{$itemMenu.Name}</a></div>
		{/if}
    {/foreach}
</div>
{/strip}