<div class="info">
{if $Catalogue.Used.Single}
		{capture name=wrap}
		<div class="b-catstuff b-onestuff"{if $Used.Admin && $Catalogue.Used.AddButton} id="admbox-{$Catalogue.Single.Id}"{/if}>
			<h2{if $Used.Admin} id="admedt-{$Catalogue.Single.Id}-ProductName"{/if}>{$Catalogue.Single.Name}</h2>
			<img{if $Used.Admin} id="admimg-{$Catalogue.Single.Id}-big"{/if} src="{$Catalogue.Single.Img.Big.Url}" {$Catalogue.Single.Img.Big.Size} />
			<h3>Доп. картинки</h3>
			{if $Used.Admin}
				<input type="button" id="admimg-{$Catalogue.Single.Id}-pagemulti-small" value="Добавить"><br/><br/>
				<div class="invisible" id="admico-additionalFileShow-{$Catalogue.Single.Id}"></div>
			{/if}
			<div class="b-addimgs" id="addImgBox-{$Catalogue.Single.Id}">
			{if isset($Catalogue.Single.Img.AdditionalImages)}
				{foreach key=keyAddImg item=itemAddImg from=$Catalogue.Single.Img.AdditionalImages.Small}
					<div class="b-addimg" id="addImg-{$Catalogue.Single.Id}-{$itemAddImg.Key}">
						{if $Used.Admin}<i class="b-btn b-delbtn png" id="admico-additionalFileRemove-{$Catalogue.Single.Id}-{$itemAddImg.Key}" title="Удалить"></i>{/if}
						{if $Used.Admin}<a href="{$Catalogue.Single.Img.AdditionalImages.Big.$keyAddImg.Url}" class="fancy" rel="addimg{$Catalogue.Single.Id}"><i class="png b-btn b-zoombtn" style="left:20px;" title="Увеличить"></i></a>{/if}<img src="{$itemAddImg.Url}" {$itemAddImg.Size} alt="" />
					</div>
				{/foreach}				
			{/if}
				<img class="clear" src="img/blank.gif" alt="" />
			</div>
			<h3>Артикул:</h3>
			<div{if $Used.Admin} id="admedt-{$Catalogue.Single.Id}-ProductArticle"{/if}>{$Catalogue.Single.Article}</div>
			<h3>Описание:</h3>
			<div{if $Used.Admin} id="admedt-{$Catalogue.Single.Id}-ProductDescription"{/if}>{$Catalogue.Single.Description}</div>
			<h3>Наличие товара:</h3>
			<div{if $Used.Admin} id="admedt-{$Catalogue.Single.Id}-ProductExist"{/if}>{$Catalogue.Single.Exist}</div>
			<h3>Цена:</h3>
			<div{if $Used.Admin} id="admedt-{$Catalogue.Single.Id}-ProductPrice"{/if}>{$Catalogue.Single.Price}</div>
            <div>{$Catalogue.Single.Currency}</div>
			<h3>Имя брэнда:</h3>
			<div{if $Used.Admin} id="admedt-{$Catalogue.Single.Id}-BrandName"{/if}>{$Catalogue.Single.BrandName}</div>
			<h3>Описание брэнда:</h3>
			<div{if $Used.Admin} id="admedt-{$Catalogue.Single.Id}-BrandDescription"{/if}>{$Catalogue.Single.BrandDescription}</div>
		</div>
		{/capture}{assign var='wrap' value=`$smarty.capture.wrap`}{*{$wrap}*}
		{capture name=admin}{include file="file:`$Template.AdminWrapper`" Content=$wrap Id=$Catalogue.Single.Id Active=$Catalogue.Single.Active Single='1'}{/capture}{assign var='admin' value=`$smarty.capture.admin`}{$admin}
{else}
	<table class="tb-catboxes"{if $Used.Admin && $Catalogue.Used.AddButton} id="adm-total"{/if}>
		<tr>
		{foreach key=keyCatalogue item=itemCatalogue from=$Catalogue.Content}
			<td class="b-catbox">
				{capture name=wrap}
				<div class="b-catstuff"{if $Used.Admin} id="admbox-{$itemCatalogue.Id}"{/if}>
					<div class="b-image"><img{if $Used.Admin} id="admimg-{$itemCatalogue.Id}-small"{/if} src="{$itemCatalogue.Img.Small.Url}" {$itemCatalogue.Img.Small.Size} /></div>
					<div class="b-article"{if $Used.Admin} id="admedt-{$itemCatalogue.Id}-ProductArticle"{/if}>{$itemCatalogue.Article}</div>
					<div class="b-name"><a href="{$itemCatalogue.Url}">{$itemCatalogue.Name}</a></div>
				</div>
				{/capture}{assign var='wrap' value=`$smarty.capture.wrap`}{*{$wrap}*}
				{capture name=admin}{include file="file:`$Template.AdminWrapper`" Content=$wrap Id=$itemCatalogue.Id Active=$itemCatalogue.Active Single='0'}{/capture}{assign var='admin' value=`$smarty.capture.admin`}{$admin}
			</td>
			{if ($keyCatalogue+1) % 4 == 0}</tr><tr>{/if}
		{/foreach}
		</tr>
	</table>		
{/if}
</div>