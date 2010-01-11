{if $Used.NewsLatest}
	<div class="b-newslatestbox">
		<h1>Последние новости</h1>
		<dl class="b-newslatest">
			{foreach key=key item=itemLatest from=$Block.NewsLatest}
				<dt class="date">{$itemLatest.Date.Day}.{$itemLatest.Date.Month}</dt>
				<dd class="txt">
					<p>{$itemLatest.Split.Text1.Start}<a href="{$itemLatest.Url}">{$itemLatest.Split.Text1.Link}</a>{$itemLatest.Split.Text1.End}</p>
				</dd>
			{/foreach}
		</dl>
	</div>
{/if}