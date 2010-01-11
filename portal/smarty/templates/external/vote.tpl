{if $Used.Admin}
	<div id="adm-total" class="b-notvis b-votes">
		{foreach item=itemVote key=keyVote from=$Vote}
			{capture name=wrap}
			<div class="b-vote" id="admbox-{$itemVote.Id}">
				<h2>{$itemVote.Name}</h2>
				<p><strong>Дата начала:</strong>&nbsp;{$itemVote.Date|date_format:"%d/%m/%Y"}.</p>
				<p><strong>Статус:</strong>&nbsp;{if $itemVote.Finished}Голосование окончено{else}Голосование продолжается{/if}.</p>
				<h3>Текущие результаты</h3>
				<ul class="b-results">
					{foreach item=itemAnswer key=keyAnswer from=$itemVote.Answers}
						<li>&#171;{$itemAnswer.Name}&#187;:&nbsp;<strong>{$itemAnswer.Answers}</strong></li>
					{/foreach}
				</ul>
			</div>
			{/capture}{assign var='wrap' value=`$smarty.capture.wrap`}{*{$wrap}*}
			{capture name=admin}{include file="file:`$Template.AdminWrapper`" Content=$wrap Id=$itemVote.Id Active=$itemVote.Active Single='0'}{/capture}
			{assign var='admin' value=`$smarty.capture.admin`}{$admin}
		{/foreach}
	</div>
{else}
	<p>Войдите как администратор, чтобы редактировать данный раздел сайта.</p>
{/if}