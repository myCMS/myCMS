var $str, $ElemId, $dataId, $dataField, $dataAction, $dataType, $comboNum;
var $qLoadFlag = 1;
var $url = document.location.href;
jQuery.fn.extend({
	admSplitId: function() { /* split element id and take data from it */
		$ElemId = $(this).attr("id");
		$dataArr = $ElemId.split("-");
		if($dataArr[0] == "admedt") { /* if "fast edit" element */
            $qLoadFlag = 1;
			$dataId = Number($dataArr[1]);
			$dataType = $dataArr[2];
		} else if ($dataArr[0] == "upfield" || $dataArr[0] == "admimg") { /* if file upload fields */
			$dataId = Number($dataArr[1]);
		} else if ($dataArr[0] == "admbtn") {  /* if one of "fast edit" buttons */
            $dataAction = $dataArr[1];
			$dataType = $dataArr[2];
		} else if ($dataArr[0] == "admpnl") {  /* if one of "admin panel" buttons */
            $qLoadFlag = 0;
			$dataAction = $dataArr[1];
			$dataType = $dataArr[2];
		} else if ($dataArr[0] == "admwin") {  /* if one of "admin window" buttons */
            $dataType = $dataArr[1];
			$dataField = $dataArr[2];
		} else if ($dataArr[0] == "admcon") {  /* if one of "admin wraped" elements */
			$dataId = Number($dataArr[1]);
		}
		return this;
	},
	winData: function(){ /* take data for sending from admin window selected by types */
		$dataValue = {};
		$totalBox = $("#adm-total");
		if ($(document).is(":has(*[id^=adm-total-])")) $multiBoxes = true;
		else $multiBoxes = false;
		$(this).each(function(i){
			$(this).admSplitId();
			if($dataType == 'text') { /* if text */
				$dataValue[$dataField] = $(this).find("textarea").val();
            } else if($dataType == 'textline') { /* if text */
				$dataValue[$dataField] = $(this).find("input:text").val();
			} else if ($dataType == 'date') { /* if date */
				$d = $(this).find("select.date-day").val();
				$m = $(this).find("select.date-mounth").val();
				$y = $(this).find("input.date-year").val();
				$date = $y+"-"+$m+"-"+$d;
				$dataValue[$dataField] = $date;
			} else if ($dataType == 'check') { /* if checkbox */
				if($("input:checkbox.check-active").is(":checked")) $dataValue[$dataField] = 1;
				else $dataValue[$dataField] = 0;				
			} else if ($dataType == 'select') { /* if selector */
                $dataValue[$dataField] = $(this).find("select option:selected").val();
				$comboNum = $dataValue[$dataField];
				if ($comboNum && $multiBoxes) $totalBox = $("#adm-total-"+$comboNum);
            } else if ($dataType == 'winfile') { /* if file */
				$dataValue[$dataField] = 1;
			}
			$str = $.toJSON($dataValue); /* data array collected from fields */			
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
			button.admSplitId();
			new AjaxUpload(this, {
				action: '',
				name: 'upload',
				data: {},
				id: $dataId,
				onSubmit: function(file, ext){					
					this.disable();
					if ($("div.ui-dialog").is(":visible")) button.before('<img src="img/blank.gif" class="uploading" alt="Загрузка" />');
					else $("#admimg-"+$dataId).before('<img src="img/blank.gif" class="uploading" alt="Загрузка" />');
					$("input[id^='upfield']:visible").winData();
					this.setData({'ajax':'file', 'qload':$qLoadFlag, 'id':$dataId});
					if (! (ext && /^(jpg|png|jpeg|gif)$/.test(ext))){
						$(".uploading").remove();
						alert('Ошибка: Недопустимое расширение файла.');
						this.enable();
						return false;
					}			
				},
				onComplete: function(file, response){
					if ($.secureEvalJSON(response).status == "success") {
					$resp = $.secureEvalJSON(response).link;
					if ($("div.ui-dialog").is(":visible")) button.attr("src", $resp);
					else $("#admimg-"+$dataId).attr("src", $resp);
					$(".uploading").remove();
					} else alert("Ошибка сервера: "+$.secureEvalJSON(response).reason);
					this.enable();
				}	
			});			
		});
	}		
});
// check and uncheck
jQuery.fn.extend({
	check: function() {return this.each(function() {this.checked = true;});},
	uncheck: function() {return this.each(function() {this.checked = false;});},
	togglecheck: function(){return this.each(function() {
		if (!this.checked) this.checked = true;
		else this.checked = false;
		});
	},
	disable: function() {return this.attr("disabled","disabled");},
	enable: function() {return this.removeAttr("disabled");}
});
var $checkedArr = new Array();
jQuery.fn.extend({
	admHandle: function(){
		$(document).click (function(e){
			$clicked=$(e.target);
			e.stopPropagation();
			$clicked.clkCheck();			
		});
	},
	admCheck: function(){
		if ($("*[id^='admcon-']")) {
			$adminFlag = true;
			if ($(".b-adm-container").is(".b-single")) $singleFlag = true;
			else $singleFlag = false;
		} else $adminFlag = false;
		if ($adminFlag) {
			$admH = $("#admbar").height();
			$("#admbar, #admpnlplace").css({"height":($admH/16)+"em"});
			$("*[id^='admimg-']").addClass("upload").upload();
			if (!$(document).is(":has(*[id^='adm-total'])")) $("#admpnl-add").disable();
			else $("#admpnl-add").enable();
			$("*[id^='admedt-']").bind("mouseenter", function(){$(this).addClass("adm-hover");}).bind("mouseleave", function(){$(this).removeClass("adm-hover");});
			$checkboxes = $("input:checkbox[id^='admchk-']");
			$checksOn = $("input:checkbox[id^='admchk-']:checked").size();
			$checksAll = $checkboxes.size();
			$("*[id^='admcon-']").arrCheck();	
			$("#admwindow").show().dialog({width:550, height:500}).dialog("close");
			$statusbar = $("div.b-admin-status-bar");
			$panelFlag = false;
			$panelBar = $("div.b-admin-actions-bar");
			$("div.b-admin-actions-bar input:button").each(function(){
				if ($(this).is(':enabled')) $panelFlag = true;
			});
			if (!$panelFlag) $panelBar.hide();
			else $panelBar.show();
			$("#admbar, #admpnlplace").height("auto");
			$admH = $("#admbar").height();
			$("#admbar, #admpnlplace").css({"height":($admH/16)+"em"});
		}
		return this;
	},
	arrCheck: function() {
		if (!$singleFlag) {
			$checkboxes = $("input:checkbox[id^='admchk-']");
			$checksOn = $("input:checkbox[id^='admchk-']:checked").size();
			$checksAll = $checkboxes.size();
			if (!$checksAll) $("#admpnl-sel").disable();
			if (!$checksOn) $("#admpnl-del, #admpnl-act, #admpnl-edt").disable();
			else if ($checksOn == 1) $("#admpnl-del, #admpnl-act, #admpnl-edt").enable();
			else if ($checksOn > 1) {
				$("#admpnl-del, #admpnl-act").enable();
				$("#admpnl-edt").disable();
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
			$("#admpnl-del, #admpnl-act, #admpnl-edt").enable();
			$("#admpnl-sel").disable();			
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
                        $('#admwindow').html(json.html)
						$('#admwindow').find("input:checkbox.check-active").check();
						$('#admwindow').dialog("open").dialog('option', 'title', 'Добавление');
						$('#admwindow .upload').upload();
						$("#admwindow textarea").autogrow();
						$("#admpnl-saveEdit").hide();
						$("#admpnl-saveAdd").show();
						$statusbar.removeClass("error").html('<span class="status-hd">Статус:</span> Данные получены.');
						setTimeout('$statusbar.slideUp(200);', 750);
					} else $statusbar.addClass("error").html('<span class="status-hd">Статус:</span> Ошибка, ответ сервера: «'+json.reason+'».');
				}, "json"
			);			
		} else if ($dataAction == "saveAdd") {	// admin panel "save add"
			$clicked.disable();
            $qLoadFlag = 0;
			$("div[id^='admwin-']").winData();
			$statusbar.removeClass("error").html('<span class="status-hd">Статус:</span> Сохранение новых данных.').slideDown(150);
			$.post($url,{'ajax':'add', 'json':$str, 'file':$resp},
				function(json, textStatus){
					if (json.status == 'success') {
                        $("#admwindow").dialog("close");
						$statusbar.removeClass("error").html('<span class="status-hd">Статус:</span> Данные сохранены.');
                        setTimeout('$statusbar.slideUp(200);', 750);						
						if (!$singleFlag) {
							$totalBox.prepend(json.html);
                            $totalBox.find("*[id^='admcon-']:first").hide().addClass("fresh").slideDown(200);
							$(".fresh").find("*[id^='admimg-']").addClass(".upload").upload();
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
                        $('#admwindow').html(json.html).dialog("open").dialog('option', 'title', 'Редактирование');
						$('#admwindow .upload').upload();
						$("#admwindow textarea").autogrow();
						$("#admpnl-saveAdd").hide();
						$("#admpnl-saveEdit").show();
						$statusbar.removeClass("error").html('<span class="status-hd">Статус:</span> Данные получены.');
						setTimeout('$statusbar.slideUp(200);', 750);
					} else $statusbar.addClass("error").html('<span class="status-hd">Статус:</span> Ошибка, ответ сервера: «'+json.reason+'».');
				}, "json"
			);
		} else if ($dataAction == "saveEdit") { // admin panel "save edit"
			$clicked.disable();
            $qLoadFlag = 0;
            $statusbar.removeClass("error").html('<span class="status-hd">Статус:</span> Сохранение отредактированных данных.').slideDown(150);
			$("div[id^='admwin-']").winData();
			$.post($url,{'ajax':'edit', 'json':$str, 'id':$ThisIdNum, 'file':$resp},
				function(json, textStatus){
					if (json.status == 'success') {
						$("#admwindow").dialog("close");
						if (!$singleFlag) {
							$box = $("input:checkbox[id^='admchk']:checked").parent().parent();
							if ($multiBoxes){
								$totalBox.prepend(json.html);
								$box.remove();
							} else $box.replaceWith(json.html);
							$box.find(".upload").upload();
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
			$clicked.disable();
			$clicked.siblings("input:button").disable();
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
			$clicked.disable();
			$clicked.siblings("input:button").disable();
            $('#admwindow').dialog("close");
            $checkboxes.uncheck().arrCheck();
            $statusbar.removeClass("error").html('<span class="status-hd">Статус:</span> Удаление данных отменено.');
            setTimeout('$statusbar.slideUp(200);', 750);
		} else if ($dataAction == "act") {  // admin panel "activity"
            $statusbar.removeClass("error").html('<span class="status-hd">Статус:</span> Изменение активности выбранных элементов.').slideDown(150);
			$checkboxes.arrCheck();
			$str = $.toJSON($checkedArr);
			$.post($url,{'ajax':'activity', 'json':$str}, function(json, textStatus){
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
		$checking = $clicked.parent();
		for (k=1;k<=5;k++) {
			if ($checking.is("*[id^='admedt-']") && !$clicked.is("*[id^='adm']") && !$clicked.is(".cmsfield") && !$clicked.is("input, textarea")) $clicked = $checking;
			else $checking = $checking.parent();								
		}
		if ($clicked.is("*[id^='admedt-']") && $clicked.is(":not(.editable)")) { // if admin edit & editable
			$dataAction = "load";
			$clicked.admSplitId();
			$clicked.prepend('<div class="uploading"><img src="img/blank.gif" alt="Загрузка" /></div>');
			$.post($url,{'ajax':$dataAction, 'type':$dataType, 'id':$dataId}, 
				function(json, textStatus){
                    if (json.status == 'success') {
						$clicked.html(json.html);
						$clicked.children("textarea").autogrow();
						$clicked.find(".upload").upload();
						$("div.uploading").remove();						
                    }
				}, "json"
			);						
		} else if ($clicked.is("input:button[id^='admbtn-']")) { // if admin button
			$clicked.admSplitId().disable().siblings("input:button").disable();
			$checked = $clicked.parent();
			$inputBox = 0;
			for (j=1;j<=5;j++) {
				if ($checked.is("*[id^='admedt-']")) $inputBox = $checked;
				else $checked = $checked.parent();								
			}
			if ($dataType == "date") {/* fast edit of date */				
				$d = $("#date-day").val();$m = $("#date-mounth").val();$y = $("#date-year").val();
				$dataValue = $y+"-"+$m+"-"+$d;				
			} else if ($dataType == "text") {/* fast edit of text */
				$dataValue = $inputBox.find("textarea").val();
			} else $dataValue = "";			
			$inputBox.admSplitId();
			$.post($url,{'ajax':$dataAction, 'type':$dataType, 'id':$dataId, 'value':$dataValue}, 
				function(json, textStatus){
					if (json.status == 'success') {
						$inputBox.removeClass("adm-hover").empty().html(json.html);
						$("div.uploading").remove();						
					}
				}, "json"
			);
			$inputBox.removeClass("editable").removeClass("adm-hover");
		} else if ($clicked.is("input:checkbox") && $clicked.is("*[id^='admchk-']")) $checkboxes.arrCheck();
		else if ($clicked.is("input:button[id^='admpnl-']")) $clicked.btnCheck();
		else if ($clicked.is(".upload")) {$clicked.upload();}
		return this;
	}
});