{if $Element.Type == 'text'}
	<textarea class="cmsfield" id="fastEditarea-{$Element.Id}-{$smarty.now}">{$Element.Value}</textarea>
{elseif $Element.Type == 'date'}
	<input class="cmsfield datepickfield" id="pickdate-{$smarty.now}" type="text" size="7" value="{$Element.Date.Day}.{$Element.Date.Month}.{$Element.Date.Year}" /><br/>
{elseif $Element.Type == 'textline'}
    <input class="wrap-textline cmsfield" value="{$Element.Value}">
{elseif $Element.Type == 'radio'}
	<input class="wrap-radio cmsfield" name="wradioedit" type="radio" value="Да" {if $Element.Value == "Да"}checked{/if}>&nbsp;Да
    <input class="wrap-radio cmsfield" name="wradioedit" type="radio" value="Нет" {if $Element.Value == "Нет"}checked{/if}>&nbsp;Нет
{elseif $Element.Type == 'select'}
    <select class="cmsfield">
		{foreach key=keyOption item=itemOption from=$Element.Options}
			<option class="cmsfield" value="{$keyOption}" {if $Element.Value == $keyOption}selected{/if}>{$itemOption}</option>
		{/foreach}
	</select>
{/if}
<input type="button" id="admbtn-save-{$Element.Type}" value="" class="b-fastbtn b-accbtn">&nbsp;<input type="button" id="admbtn-cancel-{$Element.Type}" value="" class="b-fastbtn b-cnlbtn">	