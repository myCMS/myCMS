$(document).ready(function(){
//	vote
	check();
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
});
$(window).resize(function(){
	$(document).cmsLayRes();	
});
/* auth and cms */
function check(){
	$(document).bind("keydown", function(e){
		if((e.ctrlKey)&&((e.keyCode == 0xA)||(e.keyCode == 0xD))) $(this).cmsEnter(); 
		else if((e.keyCode == 0xA)||(e.keyCode == 0xD)){
			$clicked = $("#authbutton");
			$clicked.cmsAuth();
		}
	}).bind("click", function(e){
		$clicked=$(e.target);
		$(this).cmsAuth();
	});
	if ($(document).is(":has('#admbar')")) $(document).admCheck().admHandle();
}
jQuery.fn.extend({
	cmsEnter: function(){
		if ($(document).is(':has(#admbar)')) return false;
		$("embed").hide();
		if (!$(document).is(':has(#admenter)')){
			$('body').append('<div class="b-admlayout" id="admlayout"></div><div class="b-admenter" id="admenter"></div>');
			$bH = $(document).height();
			$('#admlayout').height($bH).addClass('authload');
			$url = document.location.href;
			$.post(
				$url,
				{'ajax':'authorizedForm'},
				function(json, textStatus){
					if (json.status == 'success') {
						$('#admlayout').removeClass('authload');
						$('#admenter').html(json.html).cmsPos();
						$(document).cmsLayRes();
						$("#admlogin").focus();
					}
				}, "json"
			);				
		} else {
			if ($('#admenter').is(':visible')) {
				$('#admenter, #admlayout').hide();
				$("embed, object").show();
			}
			else {
				$('#admenter').cmsPos();
				$(document).cmsLayRes();
				$('#admenter, #admlayout').show();
				$("embed, object").hide();
				$("#admlogin").focus();
			}
		}		
	},
	cmsLayRes: function(){
		if (!$(document).is(':has(#admlayout)')) return false;
		if ($('#admlayout').is(':visible')){
			$bH = $(document).height();
			$('#admlayout').hide();
			$('#admlayout').height($bH).show();			
		}	
	},
	cmsPos: function(){
		$wH = $(window).height();
		$eH = $(this).height();
		DoPer($wH, $eH);
		$eHper = $boxper;
		$wW = $(window).width();
		$eW = $(this).width();
		DoPer($wW, $eW);
		$eWper = $boxper;
		$(this).css({"top":$eHper+"%", "left":$eWper+"%", "position":"fixed"});
		function DoPer($total, $box){
			if ($total && $box){
			$oneper = $total/100;
			$boxper = (($total/2)-($box/2))/$oneper;
			return $boxper;
			}
		}
	},
	cmsAuth: function(){
		if ($clicked.is(':disabled')) return false;
		if ($clicked.is('#admlayout')) {
			$('#admenter, #admlayout').hide();
			$("embed, object").show();
		}
		if ($clicked.is('#authbutton')) $clicked.cmsLog();
	},
	cmsLog: function(){
		$dataDef = "Войти";
		$dataCheck = "Проверка данных...";
		$dataLoad = "Получение данных...";
		$dataFail = "Ошибка, неверная пара <strong>логин/пароль</strong>."
		$clicked.attr("disabled","disabled").val($dataCheck);
		$url = document.location.href;
		$log = $("#admlogin").val();
		$pas = $("#admpassword").val();
		if ($log && $pas) {
			$.post($url,{'ajax':'authorizedCheck', 'login':$log, 'password':$pas},
				function(json, textStatus){
					if (json.status == 'success') {
						if (json.check) {
							$clicked.val($dataLoad);
							$('#authform').submit();
						}
						else {								
							$('#authinfo').append('<div class="b-autherror" id="autherror" style="display:none;">'+$dataFail+'</div>');
							$('#autherror').slideDown(250);
							$clicked.removeAttr("disabled").val("Войти");
							$("#admlogin").focus().bind("keypress blur", function(){$('#autherror').remove();});
						}
					}
				}, "json"
			);
		} else {
			$clicked.removeAttr("disabled").val($dataDef);
			if (!$log || (!$log && !$pas)) $("#admlogin").focus();
			else if (!$pas) $("#admpassword").focus();			
		}
	}
});