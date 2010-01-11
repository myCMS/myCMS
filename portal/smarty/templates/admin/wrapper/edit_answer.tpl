{if isset($Element.Split.Text1)}
	{$Element.Split.Text1.Start}<a href="{$Element.Url}">{$Element.Split.Text1.Link}</a>{$Element.Split.Text1.End}
{elseif isset($Element.Date)}
	<span class="b-day">{$Element.Date.Day}</span><br/><span class="b-month">{$Element.Date.MonthNameP}</span>
{else}
    {$Element.Value}
{/if}