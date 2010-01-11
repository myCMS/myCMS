<form method="post" action="{$PageUrl.Main}search/">
	<input type="search" name="search" id="searchline" value="{$Search.0.WordLine}" style="width:75%;"/>&nbsp;<input type="submit" value="Найти" />
</form>
{if $Search.0.EmptySearchResults == 1}
    <p>Ничего не найдено.</p>
{else}
	<dl class="b-searchres-list">
	{foreach key=keySearch item=itemSearch from=$Search}
		<dt class="b-searchres-num">{$keySearch+1}.</dt>
		<dd class="b-searchres-box">
			{if $itemSearch.Picture}
				<div class="b-img"><a href="{$PageUrl.Main}{$itemSearch.Url}"><img src="{$itemSearch.Picture}" alt="" /></a></div>
			{/if}
            <div class="b-txt"><a href="{$itemSearch.Url}" class="hl">{$itemSearch.Text1|strip_tags}</a></div>
            {*<div class="b-txt hl"><small>{$itemSearch.ChunkedText|strip_tags}</small></div>*}
			{*<div class="b-txt">{$itemSearch.Text1}&nbsp;{$itemSearch.Text2}&nbsp;{$itemSearch.Text3}</div>*}
			<div class="b-sub">
                {foreach key=keyChunk item=itemChunk from=$itemSearch.ChunkedArray}
                    <div class="b-type hl">{$itemChunk}</div>
                {/foreach}
				{*<div class="b-type hl">{$itemSearch.ChunkedText}</div>*}
                <div class="b-type">Найдено в разделе &laquo;<a href="{$itemSearch.TypeUrl}">{$itemSearch.Type}</a>&raquo;</div>
				{*<div class="b-id">{$itemSearch.Id}</div>*}
			</div>
			<img src="{$PageUrl.Base}img/clear.gif" class="clear" />
		</dd>
	{/foreach}
	</dl>
	<script type="text/javascript" src="{$PageUrl.Base}common/js/highlight.js"></script>
	<script type="text/javascript">
		var word;
		{foreach from=$Search.0.Words item=word}
			word = '{$word}';
			if (word.length > 3) $('.hl').highlight(word);
		{/foreach}
	</script>
{/if}
