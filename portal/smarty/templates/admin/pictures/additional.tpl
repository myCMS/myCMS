<div id="additionalFileBlock-{$Images.Id}">
	<h3>Дополнительные изображения</h3>
	<input type="button" id="admimg-{$Images.Id}-winmulti-small" value="Добавить"><br><br>
	<div class="b-addimgs">
		{if isset($Images.AdditionalImages)}
			{foreach key=keyAddImg item=itemAddImg from=$Images.AdditionalImages.Small}
				<div class="b-addimg" id="addImgBox-{$itemAddImg.Id}-{$itemAddImg.Key}-win">
					<div class="png b-btn b-delbtn" id="admico-additionalFileRemove-{$itemAddImg.Id}-{$itemAddImg.Key}-win" title="Удалить"></div>
					<a href="{$Images.AdditionalImages.Big.$keyAddImg.Url}" class="fancy" rel="addimg{$itemAddImg.Id}"><i class="png b-btn b-zoombtn" style="left:20px;" title="Увеличить"></i></a>
					<img src="{$itemAddImg.Url}" {$itemAddImg.Size} alt="">
				</div>
			{/foreach}
		{/if}
		<img class="clear" src="img/blank.gif" alt="" />
	</div>
	<span id="admico-additionalFileHide-{$Images.Id}" class="b-dotlink">Скрыть блок</span>	
</div>