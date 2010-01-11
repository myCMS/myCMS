<div class="b-addimgs" id="addImgBox-{$Images.Id}">
	{if isset($Images.AdditionalImages)}
		{foreach key=keyAddImg item=itemAddImg from=$Images.AdditionalImages.Small}
			<div class="b-addimg" id="addImg-{$itemAddImg.Id}-{$itemAddImg.Key}">
				<i class="b-btn b-delbtn png" id="admico-additionalFileRemove-{$itemAddImg.Id}-{$itemAddImg.Key}" title="Удалить"></i>
				<a href="{$Images.AdditionalImages.Big.$keyAddImg.Url}" class="fancy" rel="addimg{$itemAddImg.Id}"><i class="png b-btn b-zoombtn" style="left:20px;" title="Увеличить"></i></a>
				<img src="{$itemAddImg.Url}" {$itemAddImg.Size} alt="" />				
			</div>
		{/foreach}
	{/if}
	<img class="clear" src="img/blank.gif" alt="" />
</div>