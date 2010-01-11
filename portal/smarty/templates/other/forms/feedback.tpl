<div class="b-feedback">
	<div class="b-note">
	{if isset($Feedback.Send)}
		{if $Feedback.Status}<p>Письмо отправлено удачно</p>
		{else}<p>Письмо не отправлено</p>
		{/if}
	{/if}
	</div>
    <form method="post" action="">
		<input type="hidden" name="action" value="sendfeedback">
		<input type="hidden" name="module" value="feedback">
		<input type="hidden" name="module_type" value="main_feedback">
		<label for="field-name">Имя</label>
		<input type="text" id="field-name" name="name" class="txtline"/>
		<label for="field-email">Электронная почта</label>
		<input type="text" name="email" class="txtline" id="field-email"/>		
		<label for="field-message">Сообщение</label>
		<textarea name="message" id="field-message" rows="6"></textarea><br/>
		<input type="submit" value="Отправить" name="sendmessage" id="sendmessage"/>
	</form>	
</div>