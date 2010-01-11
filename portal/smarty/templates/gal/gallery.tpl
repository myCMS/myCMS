{if $gallery_display_single_block}
<div class="info">
    <h1 style="margin-top: 0pt;">{$gallery_single_block.Name}</h1>
    <div style="margin-left: 1px;"><img src="{$gallery_single_block.img.small}" {$gallery_single_block.img.small_size[3]}></div>
	<div style="margin-left: 1px;">
		{$gallery_single_block.description}
		{$gallery_single_block.article}
		{$gallery_single_block.rating}
	</div>
</div>
{else}
<table class="goods-tbl cat-content cells4">
    {foreach key=key item=gallery from=$gallery_header_blocks name=CatalogueHeaders}
		{assign var="iter" value="`$key+1`"}
		<tr>
			 <td>
	        	<a href="{$gallery.b_img}" rel="lbox" name="{$gallery.description}" title="{$gallery.Name}"><img src="{$gallery.img.small}" {$gallery.img.small_size[3]}/></a>
				<a href="{$gallery.Url}" class="link">{$gallery.Name}</a>
			</td>
		</tr>
	{/foreach}
</table>
{/if}