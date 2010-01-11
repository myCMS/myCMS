<div>Вопросы</div>
<br>
<table>
    <tbody>
        <tr>
            <td>Название опроса</td>
            <td>Дата</td>
            <td>Активность</td>
            <td>Завершен</td>
            <td></td>
            <td></td>
        </tr>
        <tr>
            <td><span style="color:teal;"><input type="text" value="" id="add_name"> </span></td>
            <td><span style="color:teal;"><input type="text" value="" id="add_date"> </span></td>
            <td><span style="color:teal;"><input type="text" value="" id="add_active"> </span></td>
            <td><span style="color:teal;"><input type="text" value="" id="add_finished"> </span></td>
            <td><span style="color:teal;"><input type="button" value="Добавить" onclick="addVote()"> </span></td>
            <td></td>
        </tr>
        <br>
        {foreach key=key item=votes from=$votes name=Votes}
        <tr>
            <td><span style="color:teal;"><input type="text" value="{$votes.Name}" id="name{$votes.Id}"><input type="button" value="save" onclick="saveName('{$votes.Id}')"> </span></td>
            <td><span style="color:teal;"><input type="text" value="{$votes.date}" id="date{$votes.Id}"><input type="button" value="save" onclick="saveDate('{$votes.Id}')"> </span></td>
            <td><span style="color:teal;"><input type="text" value="{$votes.Active}" id="active{$votes.Id}"><input type="button" value="save" onclick="saveActive('{$votes.Id}')"> </span></td>
            <td><span style="color:teal;"><input type="text" value="{$votes.finished}" id="finished{$votes.Id}"><input type="button" value="save" onclick="saveFinished('{$votes.Id}')"> </span></td>
            <td><span style="color:teal;"><input type="button" value="Список ответов" onclick="answersList('{$votes.Id}')"> </span></td>
            <td><span style="color:teal;"><input type="button" value="Удалить" onclick="removeVote('{$votes.Id}')"> </span></td>
        </tr>
        <br>
        {/foreach}
    </tbody>
</table>

{literal}
<script type="text/javascript">

    function addVote(){

        $url = document.location.href;
        $name       = $("#add_name").val();
        $date       = $("#add_date").val();
        $active     = $("#add_active").val();
        $finished   = $("#add_finished").val();

        $.getJSON($url, {'ajax':'vote', 'action':'addVote', 'name':$name, 'date':$date, 'active':$active, 'finished':$finished}, function(json, textStatus){

            if (json.status == 'success'){

                alert('ok');
                //$("#answer_list").html(json.html);

            }

        });

    }

    function removeVote(voteId){

        $url = document.location.href;

        $.getJSON($url, {'ajax':'vote', 'action':'removeVote', 'voteId':voteId}, function(json, textStatus){

            if (json.status == 'success'){

                alert('ok');
                //$("#answer_list").html(json.html);

            }

        });

    }

    function saveName(voteId){

        $url = document.location.href;
        $value = $("#name"+voteId).val();

        $.getJSON($url, {'ajax':'vote', 'action':'saveVote', 'voteId':voteId, 'itemName':'name', 'itemValue':$value}, function(json, textStatus){

            if (json.status == 'success'){

                alert('ok');
                //$("#answer_list").html(json.html);

            }

        });

    }

    function saveDate(voteId){

        $url = document.location.href;
        $value = $("#date"+voteId).val();

        $.getJSON($url, {'ajax':'vote', 'action':'saveVote', 'voteId':voteId, 'itemName':'date', 'itemValue':$value}, function(json, textStatus){

            if (json.status == 'success'){

                alert('ok');
                //$("#answer_list").html(json.html);

            }

        });

    }

    function saveActive(voteId){

        $url = document.location.href;
        $value = $("#active"+voteId).val();

        $.getJSON($url, {'ajax':'vote', 'action':'saveVote', 'voteId':voteId, 'itemName':'active', 'itemValue':$value}, function(json, textStatus){

            if (json.status == 'success'){

                alert('ok');
                //$("#answer_list").html(json.html);

            }

        });

    }

    function saveFinished(voteId){

        $url = document.location.href;
        $value = $("#finished"+voteId).val();

        $.getJSON($url, {'ajax':'vote', 'action':'saveVote', 'voteId':voteId, 'itemName':'finished', 'itemValue':$value}, function(json, textStatus){

            if (json.status == 'success'){

                alert('ok');
                //$("#answer_list").html(json.html);

            }

        });

    }
</script>
{/literal}