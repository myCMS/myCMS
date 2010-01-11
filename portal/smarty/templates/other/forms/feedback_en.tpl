<div style="width: 450px;" class="info">
	{if $EmailSend}
		{if $EmailSendSuccessfully}
			Письмо отправлено удачно
		{else}
			Письмо не отправлено
		{/if}
	{/if}
    <form method="post" action="">
		<input type="hidden" name="action" value="sendfeedback">
		<input type="hidden" name="module" value="feedback">
		<input type="hidden" name="module_type" value="main_feedback">
        <table>
            <tbody>
                <tr>
                    <td id="messageheader" class="n red" colspan="2">Main feedback</td>
                </tr>
                <tr>
                    <td id="messageheader" class="n red">сообщение:</td>
                    <td class="f"><textarea name="message" id="message"></textarea></td>
                </tr>
                <tr>
                    <td id="nameheader" class="n red">имя:</td>
                    <td class="f"><input type="text" id="name" name="name"/></td>
                </tr>
                <tr>
                    <td id="emailheader" class="n red">эл. почта:</td>
                    <td class="f"><input type="text" name="email" class="red" id="email"/></td>
                </tr>
            </tbody>
        </table>
        <img src="img/blank.gif"/>
        <input type="submit" value="Отправить" name="sendmessage" id="sendmessage"/>
    </form>
    <form method="post" action="">
		<input type="hidden" name="action" value="sendfeedback">
		<input type="hidden" name="module" value="feedback">
		<input type="hidden" name="module_type" value="additional_feedback">
        <table>
            <tbody>
                <tr>
                    <td id="messageheader" class="n red" colspan="2">Additional feedback</td>
                </tr>
                <tr>
                    <td id="messageheader" class="n red">сообщение:</td>
                    <td class="f"><textarea name="message" id="message"></textarea></td>
                </tr>
                <tr>
                    <td id="nameheader" class="n red">имя:</td>
                    <td class="f"><input type="text" id="name" name="name"/></td>
                </tr>
                <tr>
                    <td id="emailheader" class="n red">эл. почта:</td>
                    <td class="f"><input type="text" name="email" class="red" id="email"/></td>
                </tr>
            </tbody>
        </table>
        <img src="img/blank.gif"/>
        <input type="submit" value="Отправить" name="sendmessage" id="sendmessage"/>
    </form>
</div>