jQuery.fn.extend({
	initEditor: function(){
		CKEDITOR.addStylesSet( 'styleSet',
				[
					// Block Styles
					{ name : 'Плашка', element : 'p', attributes : { 'class' : 'txtbox' } },
					{ name : 'Текст', element : 'p', attributes : { 'class' : '' } },
					{ name : 'Подпись', element : 'p', attributes : { 'class' : 'txtsign' } },
					{ name : 'Выделенный', element : 'p', attributes : { 'class' : 'txtmain' } }
				]);		
			$("textarea[id^='editarea']").each(function(){
				$cmsareaId = $(this).attr("id");
				if (CKEDITOR.instances[$cmsareaId]) CKEDITOR.remove(CKEDITOR.instances[$cmsareaId]);
				CKEDITOR.replace($cmsareaId, {
					//toolbar : 'Full',
                    toolbar :
                    [
                        ['Source','-','Save','NewPage','Preview','-','Templates'],
						['Cut','Copy','Paste','PasteText','PasteFromWord','-','Print', 'SpellChecker', 'Scayt'],
						['Undo','Redo','-','Find','Replace','-','SelectAll','RemoveFormat'],
						['Form', 'Checkbox', 'Radio', 'TextField', 'Textarea', 'Select', 'Button', 'ImageButton', 'HiddenField'],
						'/',
						['Bold','Italic','Underline','Strike','-','Subscript','Superscript'],
						['NumberedList','BulletedList','-','Outdent','Indent','Blockquote'],
						['JustifyLeft','JustifyCenter','JustifyRight','JustifyBlock'],
						['Link','Unlink','Anchor'],
						['Image','Flash','Table','HorizontalRule','Smiley','SpecialChar','PageBreak'],
						'/',
						['Styles','Format','Font','FontSize'],
						['TextColor','BGColor'],
						['Maximize', 'ShowBlocks','-','About']
                    ],
					stylesCombo_stylesSet: 'styleSet',
					filebrowserBrowseUrl : window.location + '/admin/browse.php',
					filebrowserUploadUrl : window.location + '/admin/upload.php'
                });
			});
		$("textarea[id^='fastEditarea']").each(function(){	
			$areaId = $(this).attr("id");
			if (CKEDITOR.instances[$areaId]) CKEDITOR.instances[$areaId].destroy();
			CKEDITOR.replace($areaId, {
				//toolbar : 'Full',
				toolbar: [
					['Source','-','Styles','Format','-','Bold','Italic','Underline','Strike','-','Subscript','Superscript','-','NumberedList','BulletedList','Outdent','Indent','Blockquote','-','JustifyLeft','JustifyCenter','JustifyRight','JustifyBlock','-','Link','Unlink','SpecialChar','-','Image','Flash','Table']
				],
				stylesCombo_stylesSet: 'styleSet',
				filebrowserBrowseUrl : window.location + '/admin/browse.php',
				filebrowserUploadUrl : window.location + '/admin/upload.php'
			});
		});		
	}
});