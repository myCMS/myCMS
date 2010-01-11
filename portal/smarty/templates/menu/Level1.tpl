<div class="b-menumain" style="margin-top:0;">
	{foreach key=KeyMenu item=itemMenu from=$Menu.Level1}
		{if $KeyMenu == $PageId.Level1}
			<div class="active">
				<span>
				{if ($PageUrl.Level2 != $PageUrl.Current) && !$Used.ModuleInside}
					{$itemMenu.Name}
				{else}
					<a href="{$itemMenu.Url}">{$itemMenu.Name}</a>
				{/if}
				</span>				
			</div>
			{if $Menu.Level2}{include file="file:`$Template.Level2`"}{/if}
			{if $Catalogue.Used.Menu}{include file="file:`$Template.CatalogueMenu`" CatalogueMenu=$Catalogue.Menu}{/if}
		{else}
			<div>
				<span><a href="{$itemMenu.Url}">{$itemMenu.Name}</a></span>
			</div>
		{/if}
	{/foreach}
</div>