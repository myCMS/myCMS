<div class="b-filebox">
	{if $Used.Admin}
	<form action="" method="post" enctype="multipart/form-data">
        <input type="hidden" name="ajax" value="uploadPricelist">
        <input type="file" name="file">
        <input type="submit" value="Загрузить">
    </form>
    {/if}
	<div class="b-file">
    {if isset($Block.Pricelist) && $Block.Pricelist != ""}
		<form action="" method="post">
			<input type="hidden" name="ajax" value="removePricelist">
			<a href="{$Block.Pricelist.Filename}">Price List</a><span class="b-fileinfo">(size:{$Block.Pricelist.Size}; modified date: {$Block.Pricelist.Date})</span>&nbsp;<input type="submit" value="Удалить">
		</form>
    {else}
        <p class="b-filenote">Прайслист не загружен.</p>
    {/if}
	</div>
</div>
