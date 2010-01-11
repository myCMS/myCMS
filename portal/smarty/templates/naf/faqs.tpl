{strip}
<div class="b-faqs"{if $Used.Admin} id="adm-total"{/if}>
	{foreach key=keyFaqs item=itemFaqs from=$Naf.Content}
		{capture name=wrap}
			<div class="b-faqs-box"{if $Used.Admin} id="admbox-{$itemFaqs.Id}"{/if}>
				<div class="b-qs"{if $Used.Admin} id="admedt-{$itemFaqs.Id}-text1"{/if}>{$itemFaqs.Text1}</div>
				<div class="b-as"{if $Used.Admin} id="admedt-{$itemFaqs.Id}-text2"{/if}>{$itemFaqs.Text2}</div>
			</div>	
		{/capture}{assign var='wrap' value=`$smarty.capture.wrap`}{*{$wrap}*}
		{capture name=admin}{include file="file:`$Template.AdminWrapper`" Content=$wrap Id=$itemFaqs.Id Active=$itemFaqs.Active Single='0'}{/capture}{assign var='admin' value=`$smarty.capture.admin`}{$admin}
	{/foreach}			
</div>
{/strip}