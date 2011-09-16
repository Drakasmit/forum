{assign var="noSidebar" value=true}
{include file='header.tpl'}

<div id="forum">

	<div class="forum-nav">
		<h2>
			Панель администратора
		</h2>
	</div>
	
	<ul class="sv-tom_menu">
		<li class="active"><a href="{router page='forum'}admin/categories/">Категории форумов</a></li>
		<li><a href="{router page='forum'}admin/forums/">Форумы</a></li>
		<li><a href="{router page='forum'}admin/topics/">Топики</a></li>
	</ul>

	<div class="sv-forum-block">

		<div class="sv-forum_header sv-forum_header-subject_page">
			<div class="sv-left_bg">
				<h2>О плагине</h2>
			</div>
			<div class="sv-right_bg"></div>
		</div>
		
		<div class="sv-fast_answer">
			<div class="sv-fast_answer_form">
				Данный плагин имеет открытый код и свободную форму распространения по лиценции GPLv2.
			</div>
			<div class="clear"></div>
		</div>

	</div>

</div>
{include file='footer.tpl'}