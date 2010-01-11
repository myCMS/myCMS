{strip}
{* admin popup window *}
<div id="dialog" class="b-admwin">
	<fieldset class="b-textarea-i">
		{foreach item=itemLine key=keyItem from=$AdminWindow.Lines}
				{if $itemLine.Type == 'text'}
					<div class="b-admwin-box" id="admwin-text-{$itemLine.Name}">
						<h3>{$itemLine.Description}</h3>
						<textarea class="cmsfield cms_textarea" id="editarea-{if $AdminWindow.Action == 'Edit'}{$AdminWindow.Id}{else}{$smarty.now}{/if}-{$keyItem}">{$itemLine.Value}</textarea>						
					</div>	
				{elseif $itemLine.Type == 'textline'}
					<div class="b-admwin-box" id="admwin-textline-{$itemLine.Name}">
						<h3>{$itemLine.Description}</h3>
                        <input type="text" value="{$itemLine.Value}" class="cmsfield cms-txtline">
					</div>
				{elseif $itemLine.Type == 'date'}				
					{capture name='day'}{$itemLine.Value|default:$smarty.now|date_format:"%d"}{/capture}
					{capture name='month'}{$itemLine.Value|default:$smarty.now|date_format:"%m"}{/capture}
					{capture name='year'}{$itemLine.Value|default:$smarty.now|date_format:"%Y"}{/capture}
					{assign var='day' value=`$smarty.capture.day`}
					{assign var='month' value=`$smarty.capture.month`}
					{assign var='year' value=`$smarty.capture.year`}				
					<div class="b-admwin-box" id="admwin-date-{$itemLine.Name}">	
						<h3>{$itemLine.Description}</h3>
						<input class="cmsfield cms_dateline datepickfield" id="pickdate-{if $AdminWindow.Action == 'Edit'}{$AdminWindow.Id}{else}{$smarty.now}{/if}-{$keyItem}" type="text" size="7" value="{$day}.{$month}.{$year}" />											
					</div>
				{elseif $itemLine.Type == 'file'}
					<div class="b-admwin-box b-image" id="admwin-filesingle-{$itemLine.Name}">
						<h3>{$itemLine.Description}</h3>
						<div class="b-preview" style="width:60px;height:60px;"><img id="admimg-{if $AdminWindow.Action == 'Edit'}{$AdminWindow.Id}{else}0{/if}-winsingle-small" src="{$itemLine.Img.Small.Url}" {$itemLine.Img.Small.Size} alt="Для изменения кликните" title="Для изменения кликните" /></div>
						<span class="b-comment">(для изменения кликните)</span>
					</div>
				{elseif $itemLine.Type == 'multifile'}
					<div class="b-admwin-box b-image" id="admwin-multifile-{$itemLine.Name}">
						{if $AdminWindow.Action == 'Edit'}
							<span id="admico-additionalFileShow-{$AdminWindow.Id}" class="b-dotlink">{$itemLine.Description}</span>						
						{else}
							<span class="b-comment">{$itemLine.Description} можно добавлять и&nbsp;редактировать после сохранения; в&nbsp;режимах быстрого и&nbsp;полного редактирования.</span>
						{/if}
					</div>
				{elseif $itemLine.Type == 'checkbox'}
					<div class="b-admwin-box" id="admwin-check-{$itemLine.Name}">
						<h3>{$itemLine.Description}</h3>
						<input type="checkbox" class="cmsfield cms_checkbox check-{$itemLine.Name}" {if $itemLine.Value}checked{/if}/>
					</div>	
				{elseif $itemLine.Type == 'select'}
                    <div class="b-admwin-box b-selector" id="admwin-select-{$itemLine.Name}">
                        <h3>{$itemLine.Description}</h3>
                        <select class="cmsfield cms_select">
							{foreach key=keyOption from=$itemLine.Options item=itemOption}
								<option value="{$keyOption}" {if $itemLine.Value == $keyOption}selected{/if}>{$itemOption}</option>
							{/foreach}
                        </select>
                    </div>
				{elseif $itemLine.Type == 'hiddenDataBox'}
					<div class="b-admwin-box b-hiddendata" id="admwin-hidden-{$itemLine.Name}" style="display:none!important;">{$itemLine.Value}</div>
                {/if}
			{/foreach}
			<div class="b-admwin-box b-buttons">				
				<input type="button" value="Сохранить" id="admpnl-save{$AdminWindow.Action}" rel="{$AdminWindow.Id}" />
			</div>
	</fieldset>
</div>
{/strip}