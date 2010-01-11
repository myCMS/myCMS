{if $Used.Admin}
	{if $Single == '1'}	
		{* one stuff of content *}
		<div class="b-adm-container b-single {if $Active == 0}b-active{$Active}{/if}" id="admcon-{$Id}">{$Content}</div>
	{else}	
		{* list of content *}
		<dl class="b-adm-wrapper {if $Active == 0}b-active{$Active}{/if}" id="admcon-{$Id}">
			<dt class="b-adm-check"><input type="checkbox" id="admchk-{$Id}" /></dt>
			<dd class="b-adm-container">{$Content}</dd>
		</dl>		
	{/if}
{else}
	{$Content}
{/if}