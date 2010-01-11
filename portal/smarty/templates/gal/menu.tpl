 <ul>
{foreach key=key item=item from=$gallery_menu name=TopLevelMenu}
	{if $item.Active==1}<li>{$item.Name}</li>{else}<li><a href="{$item.Url}">{$item.Name}</a></li>{/if}
		{if $item.sub}
    		{include file="file:$menu_gallery_template_name" gallery_menu=$item.sub}
		{/if}
{/foreach}
</ul>