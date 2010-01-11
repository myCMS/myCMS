{strip}
<div class="b-menusub">
    {foreach key=keyMenu item=itemMenu from=$Menu.Level2}
		{if $keyMenu == $PageId.Level2}
			<div class="active">
				{if $keyMenu == $PageId.Current}
					<span>{$itemMenu.Name}</span>
				{else}	
					<a href="{$itemMenu.Url}">{$itemMenu.Name}</a>
				{/if}				
			</div>
			{if isset($Menu.Level3)}{include  file="file:`$Template.Level3`"}{/if}
		{else}
			<div><a href="{$itemMenu.Url}">{$itemMenu.Name}</a></div>
		{/if}
    {/foreach}
</div>
{/strip}