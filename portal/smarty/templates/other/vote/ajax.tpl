<div>Ваш голос учтен<br>Результаты:</div>
<input type="hidden" name="module" value="{$vote.Id}">
<table>
    <tbody>
        {foreach key=key item=votes from=$vote.votes name=Votes}
        <tr>
            <td class="f"><span style="color:teal;">{$votes.text} - {$votes.answers}</span></td>
        </tr>
        {/foreach}
    </tbody>
</table>