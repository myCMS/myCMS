        <table border="1">
            {foreach key=key item=item from=$search}
			<tr>
                <td>
                    <a href="{$item.link}"> Type:{$item.type} </a>
                </td>
                <td>
                    <a href="{$item.link}"> Id:{$item.Id} </a>
                </td>
                <td>
                    text1:{$item.Text1}
                </td>
                <td>
                    text2:{$item.Text2}
                </td>
                <td>
                    text3:{$item.Text3}
                </td>
			</tr>
            {/foreach}
		</table>


{literal}
<script type="text/javascript">
    function votesList(){

        $url = document.location.href;

        $.getJSON($url, {'ajax':'vote', 'action':'getVotesList'}, function(json, textStatus){

            if (json.status == 'success'){

                $("#vote_list").html(json.html);

            }

        });

    }

    function answersList(voteId){

        $url = document.location.href;

        $.getJSON($url, {'ajax':'vote', 'action':'getAnswersList', 'voteId':voteId}, function(json, textStatus){

            if (json.status == 'success'){

                $("#answer_list").html(json.html);
                $("#currentVoteId").val(voteId);

            }

        });

    }
</script>
{/literal}