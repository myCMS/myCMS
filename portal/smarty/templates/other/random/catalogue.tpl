{if $Used.CatalogueLatest}
	<div class="news">
		<h1>Случайные товары</h1>
		{if isset($Block.CatalogueLatest)}
			{foreach key=key item=catalogue from=$Block.CatalogueLatest}
				<div>
					<h3>NAME:{$catalogue.Name} - ID:{$catalogue.Id}</h3>
					<div><a href="{$catalogue.Url}"><img src="{$catalogue.Img.Small}"></a></div>
                    <div>{$catalogue.Article}</div>
                    <div>{$catalogue.Description}</div>
				</div>
			{/foreach}
		{else}<h2>&uarr;&nbsp;Нет переменной</h2>
		{/if}	
	</div>
{/if}