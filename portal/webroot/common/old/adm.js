var $str;
var $qLoadFlag = 1;
jQuery.fn.extend({
	winData: function(){
		$dataValue = {};
		$(this).each(function(i){
			$(this).admSplitId();
			if($dataType == 'text') {
				$dataValue[$dataField] = $(this).find("textarea").val();
			} else if ($dataType == 'date') {
				$d = $(this).find("select.date-day").val();
				$m = $(this).find("select.date-mounth").val();
				$y = $(this).find("input.date-year").val();
				$date = $y+"-"+$m+"-"+$d;
				$dataValue[$dataField] = $date;
			} else if ($dataType == 'check') {
				if($("input:checkbox.check-active").is(":checked")) $dataValue[$dataField] = 1;
				else $dataValue[$dataField] = 0;				
			} 
			$str = $.toJSON($dataValue);
		});
		return this;
	}
});
//	file upload
jQuery.fn.extend({
	upload: function(){
	  $resp = "";
       $(this).each(function(){
			button = $(this);
			new AjaxUpload(this, {
				action: '',
				name: 'upload',
				data: {'ajax':'file', 'qload':$qLoadFlag},
				onSubmit: function(file, ext){
					button.before('<div class="uploading"></div>');
					this.disable();
					if (! (ext && /^(jpg|png|jpeg|gif)$/.test(ext))){
						$("div.uploading").remove();
						alert('Ошибка: Недопустимое расширение файла.');
						this.enable();
						return false;
					}			
				},
				onComplete: function(file, response){
					$("div.uploading").remove();						
					if ($.secureEvalJSON(response).status == "success") {
					$resp = $.secureEvalJSON(response).link;
					$src = button.attr("src", "img/blank.gif").attr("src", $resp);
					} else alert("Ошибка сервера: "+$.secureEvalJSON(response).reason);
					this.enable();
				}	
			});
		});
	}		
});
// split id and take data from it
var $ElemId, $dataId, $dataField, $dataAction, $dataType;
jQuery.fn.extend({
	admSplitId: function() {
		$ElemId = $(this).attr("id");
		$dataArr = $ElemId.split("-");
		if($dataArr[0] == "admedt") {
            $qLoadFlag = 1;
			$dataId = Number($dataArr[1]);
			$dataType = $dataArr[2];
		} else if ($dataArr[0] == "admbtn") {
            $dataAction = $dataArr[1];
			$dataType = $dataArr[2];
		} else if ($dataArr[0] == "admpnl") {
            $qLoadFlag = 0;
			$dataAction = $dataArr[1];
			$dataType = $dataArr[2];
		} else if ($dataArr[0] == "admwin") {
            $dataType = $dataArr[1];
			$dataField = $dataArr[2];
		} else if ($dataArr[0] == "admcon") {
			$dataId = Number($dataArr[1]);
		}
		return this;
	}
});	
// check and uncheck
jQuery.fn.extend({
	check: function() {
		return this.each(function() { this.checked = true; });
	},
	uncheck: function() {
		return this.each(function() { this.checked = false; });
	},
	togglecheck: function(){
		return this.each(function() { 
			if (!this.checked) this.checked = true; 
			else this.checked = false;
		});
	}
});
// checkboxes 
var $checkedArr = new Array();
var $url = document.location.href;
jQuery.fn.extend({
	admCheck: function(){
		if ($("*[id^='admcon-']")) {
			$adminFlag = true;
			if ($(".b-adm-container").is(".b-single")) $singleFlag = true;
			else $singleFlag = false;
		} else $adminFlag = false;
		if ($adminFlag) {
			$(".upload").upload();
			$("*[id^='admedt-']").hover(
				function(){$(this).addClass("over");},
				function(){$(this).removeClass("over");}
			);
			$admH = $("#admbar").height();
			$("#admpnlplace").css({height:($admH/16)+"em"},300);
			$checkboxes = $("input:checkbox[id^='admchk-']");
			$checksOn = $("input:checkbox[id^='admchk-']:checked").size();
			$checksAll = $checkboxes.size();
			$("*[id^='admcon-']").arrCheck();	
			$("#admwindow").show().dialog({width:550, height:500}).dialog("close");
			$statusbar = $("div.b-admin-status-bar");
		}
		return this;
	},
	arrCheck: function() {
		if (!$singleFlag) {
			$checkboxes = $("input:checkbox[id^='admchk-']");
			$checksOn = $("input:checkbox[id^='admchk-']:checked").size();
			$checksAll = $checkboxes.size();
			if (!$checksAll) $("#admpnl-sel").attr("disabled","disabled");
			if (!$checksOn) $("#admpnl-del, #admpnl-act, #admpnl-edt").attr("disabled","disabled");
			else if ($checksOn == 1) $("#admpnl-del, #admpnl-act, #admpnl-edt").attr("disabled","");
			else if ($checksOn > 1) {
				$("#admpnl-del, #admpnl-act").attr("disabled","");
				$("#admpnl-edt").attr("disabled","disabled");
			}
			$checkedArr = new Array();
			$(this).each(function(i){
				if ($(this).is(":checked")) {
					$id = $(this).attr("id").split("-");
					$checkedArr[$checkedArr.length] = Number($id[1]);					
				}			
			});
		} else {
			$singleObj = $("*[id^='admcon']");
			$singleId = $singleObj.attr("id");
			$id = $singleId.split("-");
			$checkedArr[$checkedArr.length] = Number($id[1]);
			$("#admpnl-del, #admpnl-act, #admpnl-edt").attr("disabled","");
			$("#admpnl-sel").attr("disabled","disabled");
			
		}
		return this;
	},
	btnCheck: function(){
		$(this).admSplitId();
		if ($dataAction == "add") { // admin panel "add"
            $statusbar.removeClass("error").html('<span class="status-hd">Статус:</span> Получение данных для добавления.').slideDown(150);
			$.post($url,{'ajax':'loadWindowAdd'},
                function(json, textStatus){
					if (json.status == 'success') {
                        $('#admwindow').html(json.html);
						$('#admwindow').dialog("open").dialog('option', 'title', 'Добавление');
						$("#admwindow textarea").autogrow();
						$("#admpnl-saveEdit").hide();
						$("#admpnl-saveAdd").show();
						$statusbar.removeClass("error").html('<span class="status-hd">Статус:</span> Данные получены.');
						setTimeout('$statusbar.slideUp(200);', 750);
					} else $statusbar.addClass("error").html('<span class="status-hd">Статус:</span> Ошибка, ответ сервера: «'+json.reason+'».');
				}, "json"
			);			
		} else if ($dataAction == "saveAdd") {	// admin panel "save add"
            $qLoadFlag = 0;
			$("div[id^='admwin-']").winData();
			$statusbar.removeClass("error").html('<span class="status-hd">Статус:</span> Сохранение новых данных.').slideDown(150);
			$.post($url,{'ajax':'add', 'json':$str, 'file':$resp},
				function(json, textStatus){
					if (json.status == 'success') {
                        $statusbar.removeClass("error").html('<span class="status-hd">Статус:</span> Данные сохранены.');
                        setTimeout('$statusbar.slideUp(200);', 750);
						$("#admwindow").dialog("close");
						if (!$singleFlag) {
						    $("#adm-total").prepend(json.html);
                            $("#adm-total *[id^='admcon-']:first").addClass("fresh");
                        }
					} else $statusbar.addClass("error").html('<span class="status-hd">Статус:</span> Ошибка, ответ сервера: «'+json.reason+'».');
				}, "json"
			);
		} else if ($dataAction == "edt") { // admin panel "edit"
            $statusbar.html("Статус: Получение данных для редактирования.").slideDown(150);
			if (!$singleFlag) $ThisId = $("input:checkbox:checked[id^='admchk-']").parent().parent().attr('id').split('-');
			else $ThisId = $singleId.split('-');
			$ThisIdNum = Number($ThisId[1]);
			$.post($url,{'ajax':'loadWindowEdit', 'id':$ThisIdNum},
				function(json, textStatus){
					if (json.status == 'success') {
                        $('#admwindow').html(json.html);
						$('#admwindow').dialog("open").dialog('option', 'title', 'Редактирование');
						$("#admwindow textarea").autogrow();
						$("#admpnl-saveAdd").hide();
						$("#admpnl-saveEdit").show();
						$statusbar.removeClass("error").html('<span class="status-hd">Статус:</span> Данные получены.');
						setTimeout('$statusbar.slideUp(200);', 750);
					} else $statusbar.addClass("error").html('<span class="status-hd">Статус:</span> Ошибка, ответ сервера: «'+json.reason+'».');
				}, "json"
			);
		} else if ($dataAction == "saveEdit") { // admin panel "save edit"
            $qLoadFlag = 0;
            $statusbar.removeClass("error").html('<span class="status-hd">Статус:</span> Сохранение отредактированных данных.').slideDown(150);
			$("div[id^='admwin-']").winData();
			$.post($url,{'ajax':'edit', 'json':$str, 'id':$ThisIdNum, 'file':$resp},
				function(json, textStatus){
					if (json.status == 'success') {
						$("#admwindow").dialog("close");
						if (!$singleFlag) {
							$box = $("input:checkbox[id^='admchk']:checked").parent().parent();
							$box.replaceWith(json.html);
							$checkboxes.uncheck().arrCheck();
						} else $singleObj.replaceWith(json.html);
						$statusbar.removeClass("error").html('<span class="status-hd">Статус:</span> Данные сохранены.');
						setTimeout('$statusbar.slideUp(200);', 750);
					} else $statusbar.addClass("error").html('<span class="status-hd">Статус:</span> Ошибка, ответ сервера: «'+json.reason+'».');
				}, "json"
			);
		} else if ($dataAction == "del") {  // admin panel "delete"
            $statusbar.removeClass("error").html('<span class="status-hd">Статус:</span> Ожидание подтверждения удаления данных.').slideDown(150);
			$checkboxes.arrCheck();
			$str = $.toJSON($checkedArr);
			$.post($url,{'ajax':'loadWindowDelete', 'json':$str},
				function(json, textStatus){
					if (json.status == 'success') {
                        $('#admwindow').html(json.html);
                        $('#admwindow div.b-add-edt').hide().siblings("div.b-del").show();
                        $('#admwindow').dialog("open").dialog('option', 'title', 'Удаление');
					} else $statusbar.addClass("error").html('<span class="status-hd">Статус:</span> Ошибка, ответ сервера: «'+json.reason+'».');
				}, "json"
			);
        } else if ($dataAction == "confDel") {  // admin panel "delete"
            $statusbar.removeClass("error").html('<span class="status-hd">Статус:</span> Удаление данных подтверждено.').slideDown(150);
			$checkboxes.arrCheck();
			$str = $.toJSON($checkedArr);
			$.post($url,{'ajax':'remove', 'json':$str},
				function(json, textStatus){
					if (json.status == 'success') {
					$('#admwindow div.b-add-edt').show().siblings("div.b-del").hide();
					$('#admwindow').dialog("close");
						if (!$singleFlag) {
							$box = $("input:checkbox:checked[id^='admchk-']").uncheck().parent().parent();
							$box.remove();
							$checkboxes.arrCheck();
						} else $singleObj.remove();
						$statusbar.removeClass("error").html('<span class="status-hd">Статус:</span> Данные удалены.');
						setTimeout('$statusbar.slideUp(200);', 750);
					} else $statusbar.addClass("error").html('<span class="status-hd">Статус:</span> Ошибка, ответ сервера: «'+json.reason+'».');
				}, "json"
			);
        } else if ($dataAction == "noDel") {
            $('#admwindow').dialog("close");
            $checkboxes.uncheck().arrCheck();
            $statusbar.removeClass("error").html('<span class="status-hd">Статус:</span> Удаление данных отменено.');
            setTimeout('$statusbar.slideUp(200);', 750);
		} else if ($dataAction == "act") {  // admin panel "activity"
            $statusbar.removeClass("error").html('<span class="status-hd">Статус:</span> Изменение активности выбранных элементов.').slideDown(150);
			$checkboxes.arrCheck();
			$str = $.toJSON($checkedArr);
			$.post($url,{'ajax':'activity', 'json':$str},
				function(json, textStatus){
					if (json.status == 'success') {
						if (!$singleFlag) {
							$box = $("input:checkbox[id^='admchk-']:checked").parent().parent();
							$box.toggleClass("b-active0");
							$("input:checkbox[id^='admchk-']:checked").uncheck();
							$checkboxes.arrCheck();
							$("#admpnl-sel").val("Выбрать все");
						} else $singleObj.toggleClass("b-active0");
						$statusbar.removeClass("error").html('<span class="status-hd">Статус:</span> Активность изменена.');
						 setTimeout('$statusbar.slideUp(200);', 750);
					} else $statusbar.addClass("error").html('<span class="status-hd">Статус:</span> Ошибка, ответ сервера: «'+json.reason+'».');
				}, "json"
			);
		} else if ($dataAction == "sel") {  // admin panel "select" checkboxes
			$checkboxes.arrCheck();
			if ($checksOn != $checksAll) {
				$checkboxes.check().arrCheck();
				$(this).val("Снять все");				
			} else {
				$checkboxes.uncheck().arrCheck();
				$(this).val("Поставить все");
			}			
		}
		return this;
	},
	clkCheck: function(){
		$clicked = $(this);
		$clicked.admSplitId();
		if ($clicked.is("*[id^='admedt-']") && $clicked.is(":not(.editable)")) { // if admin edit & editable
            $clicked.before('<div class="uploading"></div>');
			$dataAction = "load";			
			$.post($url,{'ajax':$dataAction, 'type':$dataType, 'id':$dataId}, 
				function(json, textStatus){
                    if (json.status == 'success') {
                        $clicked.html(json.html);
					   $clicked.find("textarea").autogrow();
					   $("div.uploading").remove();
                    }
				}, "json"
			);
			$clicked.addClass("editable");			
		} else if ($clicked.is("input:button[id^='admbtn-']")) { // if admin button
			$(this).attr("disabled","disabled").siblings("input:button").attr("disabled","disabled");
			if ($dataType == "date") {
				$d = $("#date-day").val();$m = $("#date-mounth").val();$y = $("#date-year").val();
				$dataValue = $y+"-"+$m+"-"+$d;
			} else if ($dataType == "text") {
				$dataValue = $clicked.parent().find("textarea").val();
			} else $dataValue = "";
			$clicked.parent().admSplitId();
			$.post($url,{'ajax':$dataAction, 'type':$dataType, 'id':$dataId, 'value':$dataValue}, 
				function(json, textStatus){if (json.status == 'success') {$clicked.parent().html(json.html);}}, "json"
			);
			$clicked.parent().removeClass("editable");
		} else if ($clicked.is("input:checkbox[id^='admchk-']")) $checkboxes.arrCheck();
		else if ($clicked.is("input:button[id^='admpnl-']")) $clicked.btnCheck();
		else if ($clicked.is(".upload")) {$clicked.upload();}
		return this;
	}
});