{* top admin panel code *}
<div id="admwindow"></div>
<div id="admpnlplace" style="visibility:hidden;height:1.875em;">{* places the hole on top *}</div>
{* admin bar *}
	<div class="b-admin-bar" id="admbar" id="admTabBar">        
        <div class="b-admin-tabs-bar">
			<div class="b-admin-tabs-bar-i">
				<table class="b-admin-tabs-tbl">
                    <tr>
				    {foreach item=menuItem from=$Admin.PanelMenu}
                        {if $menuItem.Active}
                            <td class="active"><span><a href="{$menuItem.Url}">{$menuItem.Name}</a></span></td>
                        {else}
                            <td><span><a href="{$menuItem.Url}">{$menuItem.Name}</a></span> </td>
                        {/if}
                    {/foreach}
					</tr>
				</table>
				<div class="b-martcms"><a href="logout/">Выйти</a>&nbsp;|&nbsp;<a href="http://www.mart.com.ua/martcms/">Mart&nbsp;CMS</a></div>
			</div>
		</div>
		<div class="b-admin-actions-bar" id="admActBar">
			<div class="b-admin-actions-bar-i">
				<table class="b-admin-actions-tbl cells3">
					<tr>
						<th style="width:20%;"></th>
						<th style="width:60%;">Действия с выбранными:</th>
						<th style="width:20%;"><span class="c-violet-9035aa">Галочки:</span></th>
					</tr>
					<tr>
						<td>
							<input type="button" value="Добавить" id="admpnl-add" />
						</td>
						<td>
							<input type="button" value="Удалить" id="admpnl-del" />
							<input type="button" value="Изменить активность" id="admpnl-act" />
							<input type="button" value="Полное редактирование" id="admpnl-edt" />
						</td>
						<td class="last-cell">
							<input type="button" value="Выбрать все" id="admpnl-sel" />
						</td>
					</tr>
				</table>
			</div>
		</div>
		<div class="b-admin-status-bar" id="admStatBar" style="display:none;"></div>
		<div class="b-panel-shadow png"><img class="i-panel-shadow" src="img/blank.gif" alt="" width="100%" height="3" /></div>
	</div>	