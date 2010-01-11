{if $Naf.Used.Calendar}
<div class="b-newscalendar">
    <table class="tb-years">
		<tr>
		{foreach key=key item=itemYear from=$Naf.Calendar.Years}
			{if $itemYear.Value == $Naf.Calendar.Selected.Year}
				<td class="active">{$itemYear.Value}</td>
			{else}
				<td><a href="{$itemYear.Url}">{$itemYear.Value}</a></td>
			{/if}
		{/foreach}
		</tr>
    </table>
	{if $Naf.Calendar.Selected.Year != ""}
    <table width="100%" class="tb-monthes">
		<tr>
			{foreach key=keyMonth item=itemMonth from=$Naf.Calendar.Monthes}
				<td>
                    {if $itemMonth.Value == $Naf.Calendar.Selected.Month}
                    <div><font color="red">{$itemMonth.Name}</font></div>
                    {elseif $itemMonth.Url}
                    <div><a href="{$itemMonth.Url}">{$itemMonth.Name}</a></div>
                    {else}
                    <div class="nact">{$itemMonth.Name}</div>
                    {/if}
                </td>
				{if $keyMonth == 6}</tr><tr>{/if}
            {/foreach}
		</tr>
	</table>
	{/if}
</div>
{/if}
<br clear="all"/>
<div class="b-news-ul" id="adm-total">
	{if $Naf.Used.Single}
		{capture name=wrap}
		<table class="tb-news-li"{if $Used.Admin} id="admbox-{$Naf.Single.Id}"{/if}>
			<tr>
				<td class="c-news-date" {if $Used.Admin} id="admedt-{$Naf.Single.Id}-date"{/if}>{$Naf.Single.Date.Day}.{$Naf.Single.Date.Month}.{$Naf.Single.Date.Year}</td>
				<td class="c-news-img"></td>
				<td class="c-news-stxt" {if $Used.Admin} id="admedt-{$Naf.Single.Id}-text2"{/if}><img{if $Used.Admin} id="admimg-{$Naf.Single.Id}-big"{/if} src="{$Naf.Single.Img.Big.Url}" {$Naf.Single.Img.Big.Size} alt="" style="float:left;margin:0 1.2em 0.6em 0;" />{$Naf.Single.Text2}<img class="clear" src="img/blank.gif" /></td>
			</tr>
		</table>
		{/capture}{assign var='wrap' value=`$smarty.capture.wrap`}{*{$wrap}*}
		{capture name=admin}{include file="file:`$Template.AdminWrapper`" Content=$wrap Id=$Naf.Single.Id Active=$Naf.Single.Active Single='1'}{/capture}{assign var='admin' value=`$smarty.capture.admin`}{$admin}

	{else}
	{foreach key=key item=itemNews from=$Naf.Content}
        {capture name=wrap}
		<table class="tb-news-li"{if $Used.Admin} id="admbox-{$itemNews.Id}"{/if}>
			<tr>
				<td class="c-news-date" {if $Used.Admin} id="admedt-{$itemNews.Id}-date"{/if}>{$itemNews.Date.Day}.{$itemNews.Date.Month}.{$itemNews.Date.Year}</td>
				<td class="c-news-img"><img{if $Used.Admin} id="admimg-{$itemNews.Id}-small"{/if} src="{$itemNews.Img.Small.Url}" {$itemNews.Img.Small.Size} alt="" /></td>
				<td class="c-news-stxt" {if $Used.Admin} id="admedt-{$itemNews.Id}-text1"{/if}>{$itemNews.Split.Text1.Start}<a href="{$itemNews.Url}">{$itemNews.Split.Text1.Link}</a>{$itemNews.Split.Text1.End}</td>
			</tr>
		</table>
		{/capture}{assign var='wrap' value=`$smarty.capture.wrap`}{*{$wrap}*}
		{capture name=admin}{include file="file:`$Template.AdminWrapper`" Content=$wrap Id=$itemNews.Id Active=$itemNews.Active Single='0'}{/capture}{assign var='admin' value=`$smarty.capture.admin`}{$admin}
	{/foreach}
	{/if}
</div>