{strip}
	{if $Used.Image}
	<div class="b-randomimages">
		<h1>Картинки</h1>
		<div class="b-image" style="background-image:url({$Block.Image});">
			{*
			<img src="{$Block.Image}" height="100" width="100" alt="" />
			<div><img src="{$Block.Image.Url}" {$Block.Image.Size} alt="" /></div>
			*}
		</div>
	</div>	
	{/if}
{/strip}