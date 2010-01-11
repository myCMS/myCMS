{strip}
<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.1//EN" "http://www.w3.org/TR/xhtml11/DTD/xhtml11.dtd">
<html xmlns="http://www.w3.org/1999/xhtml" xml:lang="ru">
    <head>
        <title>Polaris</title>
		<meta name="keywords" content="">
        <meta name="description" content="">
		{include file="file:page/head.tpl"}
    </head>
    <body>
		{if $Used.Admin}{include file="file:`$Template.AdminPanelMenu`"}{/if}
		<div id="body">
			<div class="b-head">
				<div class="b-left"><img class="i-logotype" src="img/logotype.jpg" width="181" height="40" alt="Вы на главной странице" /></div>
				<div class="b-right">
					<div class="b-icons">
						<div class="b-icon b-icon-active"><i class="b-icon1"></i></div>
						<div class="b-icon"><a href="{$PageUrl.Main}search/" title="Поиск по сайту"><i class="b-icon2"></i></a></div>
						<div class="b-icon"><a href="{$PageUrl.Main}feedback/" title="Обратная связь"><i class="b-icon3"></i></a></div>
						<div class="b-icon"><a href="{$PageUrl.Main}sitemap/" title="Карта сайта"><i class="b-icon4"></i></a></div>
						<img class="clear" src="img/blank.gif" alt="" />
					</div>
					<div class="b-enter" id="cusomers_enter">
						{if $Used.Authorized}
							Добро пожаловать <font color="#ff0000">{$UserDetails.Login}</font>&nbsp;<a href="logout/">Выйти</a>
						{else}
							<a href="{$PageUrl.Main}auth" title="Войти">Вход для клиентов</a>
						{/if}	
					</div>
				</div>
				<img class="clear" src="img/blank.gif" alt="" />
			</div>
			<div class="b-flags">
				<div class="b-flags-in">
				{foreach item=itemLangs key=keyLangs from=$Languages}
					{if $itemLangs.Selected == 0}
						<div><a href="{$itemLangs.Url}"><img src="img/flag_{$itemLangs.Name}.gif" width="16" height="11" alt="{$itemLangs.Interpretation}" title="{$itemLangs.Interpretation}" /></a></div>
					{/if}
				{/foreach}
				</div>
			</div>
			<div class="b-body b-indexbody">
				<div class="b-left">
					<p class="b-seltext">Группа компаний &laquo;Полярис&raquo;&nbsp;&#151; лидер в&nbsp;области морских контейнерных перевозок, портового экспедирования, контрактного обслуживания.</p>
					{include file="file:`$Template.Level1`"}
					{include file="file:`$Template.NewsLatest`"}					
				</div>
				<div class="b-right">
					<div class="b-infoflash">
						<div class="b-infobox b-infobox-lightback">
							<div class="b-corns">
								<i class="png b-tl"></i>
								<i class="png b-tr"></i>
							</div>
							<div class="b-infoblock">
								<p><span class="red bold">Вы&nbsp;у&nbsp;нас впервые?</span><br>Посмотрите нашу <a href="#">презентацию</a></p>
							</div>
							<div class="b-corns">
								<i class="png b-bl"></i>
								<i class="png b-br"></i>
							</div>
						</div>
						<div class="b-infobox b-infobox-darkback">
							<div class="b-corns">
								<i class="png b-tl"></i>
								<i class="png b-tr"></i>
							</div>
							<div class="b-infoblock">
								<img src="img/y_bl_container.gif" width="50" height="54" alt="" border="0">
								<h1 class="orange">ваш контейнер</h1>
								<p>Информация по&nbsp;любым контейнерам, следующим через порты Одесса и&nbsp;Ильичевск.</p>
								<img src="img/y_bl_clock.gif" width="39" height="54" alt="" border="0">
								<h1 class="red">актуально</h1>
								<p>Прямой сервис из&nbsp;Китая до&nbsp;Ильичевска. Без портов перевалок. Среднее транзитное время: 25-28&nbsp;дней.</p>
							</div>
							<div class="b-corns">
								<i class="png b-bl"></i>
								<i class="png b-br"></i>
							</div>
						</div>
					</div>
					<div class="b-flashbox" id="illsbox"></div>
					<script type="text/javascript">
						var so = new SWFObject("img/main.swf", "", "100%", "500", "8", "#FFFFF");
						so.addParam("wmode", "transparent");
						so.write("illsbox");
					</script>
				</div>
				<img class="clear" src="img/blank.gif" alt="" />
			</div>
		</div>
		<div id="base">
			<div class="b-base">
				<div class="b-left">
					<p>&copy;&nbsp;{$Block.Copyright}, Холдинг &laquo;Полярис&raquo;</p>
				</div>
				<div class="b-right">
					<p>Украина, 65025, Одесса, ул.&nbsp;Б.&nbsp;Арнаутская, 17,&nbsp;оф.&nbsp;8а<br>+38&nbsp;(048) 786-0-230, 786-0-869, 786-00-55</p>
					<p class="contact_links"><span><a href="{$PageUrl.Main}feedback">Форма обратной связи</a></span></p>
				</div>
				<img class="clear" src="img/blank.gif" alt="" />
			</div>
			<img class="clear" src="img/blank.gif" alt="" />
		</div>
    </body>
</html>
{/strip}