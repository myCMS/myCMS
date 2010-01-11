$(document).ready(function(){
//	admin check
	$(document).admCheck();	
//	click handle and check
	$(this).click (function(e){
		$clicked=$(e.target);
		e.stopPropagation();
		$clicked.clkCheck();		
	});
//	vote	
	$("*[id^='vote-']").click(function(){
        $voteArr = $(this).attr("id").split("-");
        $voteId = $voteArr[1];
        $.getJSON($url, {'ajax':'vote', 'voteId':$voteId}, function(json, textStatus){
		if (json.status == 'success'){
                $("#block-vote").html(json.html);
            }
        });
		return false;
    });
//	static textareas autogrow
	$("textarea").autogrow();
});