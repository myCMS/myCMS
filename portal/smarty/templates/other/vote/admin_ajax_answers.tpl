<div>Ответы</div>
<br>
<table>
    <tbody>
        <tr>
            <td>Вопрос</td>
            <td>Количество ответов</td>
            <td>Активность</td>
            <td>Порядок отображения</td>
            <td></td>
            <td></td>
        </tr>
        <tr>
            <td><span style="color:teal;"><input type="text" value="" id="add_text"> </span></td>
            <td><span style="color:teal;"><input type="text" value="" id="add_answers"> </span></td>
            <td><span style="color:teal;"><input type="text" value="" id="add_active_answer"> </span></td>
            <td><span style="color:teal;"><input type="text" value="" id="add_vote_order"> </span></td>
            <td><span style="color:teal;"><input type="button" value="Добавить" onclick="addAnswer()"> </span></td>
        </tr>
        <br>
        {foreach key=key item=answers from=$vote_answers name=Answers}
        <tr>
            <td><span style="color:teal;"><input type="text" value="{$answers.text}" id="text{$answers.Id}"><input type="button" value="save" onclick="saveText('{$answers.Id}')"> </span></td>
            <td><span style="color:teal;"><input type="text" value="{$answers.answers}" id="answers{$answers.Id}"><input type="button" value="save" onclick="saveAnswers('{$answers.Id}')"> </span></td>
            <td><span style="color:teal;"><input type="text" value="{$answers.Active}" id="active{$answers.Id}"><input type="button" value="save" onclick="saveActive('{$answers.Id}')"> </span></td>
            <td><span style="color:teal;"><input type="text" value="{$answers.vote_order}" id="vote_order{$answers.Id}"><input type="button" value="save" onclick="saveVoteOrder('{$answers.Id}')"> </span></td>
            <td><span style="color:teal;"><input type="button" value="Удалить" onclick="removeAnswer('{$answers.Id}')"> </span></td>
        </tr>
        {/foreach}
    </tbody>
</table>

{literal}
<script type="text/javascript">

    function addAnswer(){

        $url = document.location.href;
        $voteId     = $("#currentVoteId").val();
        $text       = $("#add_text").val();
        $answers    = $("#add_answers").val();
        $active     = $("#add_active_answer").val();
        $voteOrder  = $("#add_vote_order").val();

        $.getJSON($url, {'ajax':'vote', 'action':'addAnswer', 'voteId':$voteId, 'text':$text, 'answers':$answers, 'active':$active, 'voteOrder':$voteOrder}, function(json, textStatus){

            if (json.status == 'success'){

                alert('ok');
                //$("#answer_list").html(json.html);

            }

        });

    }

    function removeAnswer(voteId){
        
        $url = document.location.href;

        $.getJSON($url, {'ajax':'vote', 'action':'removeAnswer', 'voteId':voteId}, function(json, textStatus){

            if (json.status == 'success'){

                alert('ok');
                //$("#answer_list").html(json.html);

            }

        });

    }

    function saveText(voteId){

        $url = document.location.href;
        $value = $("#text"+voteId).val();

        $.getJSON($url, {'ajax':'vote', 'action':'saveAnswers', 'voteId':voteId, 'itemName':'text', 'itemValue':$value}, function(json, textStatus){

            if (json.status == 'success'){

                alert('ok');
                //$("#answer_list").html(json.html);

            }

        });

    }

    function saveAnswers(voteId){

        $url = document.location.href;
        $value = $("#answers"+voteId).val();

        $.getJSON($url, {'ajax':'vote', 'action':'saveAnswers', 'voteId':voteId, 'itemName':'answers', 'itemValue':$value}, function(json, textStatus){

            if (json.status == 'success'){

                alert('ok');
                //$("#answer_list").html(json.html);

            }

        });

    }

    function saveActive(voteId){

        $url = document.location.href;
        $value = $("#active"+voteId).val();

        $.getJSON($url, {'ajax':'vote', 'action':'saveAnswers', 'voteId':voteId, 'itemName':'active', 'itemValue':$value}, function(json, textStatus){

            if (json.status == 'success'){

                alert('ok');
                //$("#answer_list").html(json.html);

            }

        });

    }

    function saveVoteOrder(voteId){

        $url = document.location.href;
        $value = $("#vote_order"+voteId).val();

        $.getJSON($url, {'ajax':'vote', 'action':'saveAnswers', 'voteId':voteId, 'itemName':'voteOrder', 'itemValue':$value}, function(json, textStatus){

            if (json.status == 'success'){

                alert('ok');
                //$("#answer_list").html(json.html);

            }

        });

    }
</script>
{/literal}