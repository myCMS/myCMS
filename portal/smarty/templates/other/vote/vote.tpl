<div id="block-vote" class="info" style="border: dashed green; width:200px;">
    <div>
        {$Vote.Name}
    </div>
    <form method="POST" target="">
    <input type="hidden" name="module" value="vote">
    <input type="hidden" name="action" value="make_vote">
    <input type="hidden" name="voteId" value="{$Vote.Id}">
    <table>
        <tbody>
            {foreach key=kVote item=iVote from=$Vote.Answers}
            <tr>
                {if $Vote.Voted}
                <span style="color:teal;">{$iVote.Name} - {$iVote.Answers} ответов</span><br />
                {else}
                {*
                <td class="f"><span id="vote-{$iVote.Id}" style="cursor:pointer; color:teal;">{$iVote.Name}</span></td>
                *}
                <input type="radio" name="answerId" value="{$iVote.Id}"><span style="color:teal;">{$iVote.Name}</span><br />
                {/if}
            </tr>
            {/foreach}
            <tr>
                <td>
                    {if !$Vote.Voted}
                    <input type="submit">
                    {/if}
                </td>
            </tr>
        </tbody>
    </table>
    </form>
</div>