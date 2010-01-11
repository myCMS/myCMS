<ul class="b-sitemap">
{foreach key=key item=item from=$SiteMap name=SiteMap}
	<li>
		<a href="{$item.Url}">{$item.Name}</a>
		{if $item.Sub}
    		{include file="file:`$Template.SiteMap`" SiteMap=$item.Sub}
		{/if}
	</li>		
{/foreach}
</ul>