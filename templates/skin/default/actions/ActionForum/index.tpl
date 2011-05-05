{assign var="bNoSidebar" value=true}
{include file='header.tpl'}
<div id="forum">

	<div class="forum-nav">
		<h2>
			Форум
		</h2>
	</div>

	<ul class="sv-tom_menu">
		<li><a href="/forum/unread">Показать непрочитанные сообщения</a></li>
		<li><a href="/forum/markread">Пометить все прочитанными</a></li>
	</ul>

	<div class="clear"></div>

	{foreach from=$aCategories item=oCategory}
		<div class="sv-forum_header">
				<div class="sv-left_bg">
					<h2>{$oCategory.obj->getTitle()}</h2>
				</div>
				<div class="sv-right_bg">
					<span class="sv-last_msg">последнее сообщение</span>
					<span class="sv-answers">ответов</span>
					<span class="sv-views">тем</span>
				</div>
		</div>
		
		
		<div class="sv-table_container">
			<table class="sv-forum_body">
				<tbody>
					{foreach from=$oCategory.forums item=oForum}
						{assign var="oTopic" value=$oForum->getTopic()}
						<tr>
							<td class="sv-icon_col">
								<a class="mrkread" href="{router page='forum'}{$oForum->getUrl()}/"></a>
							</td>
							<td class="sv-main_col">
								<h3><a href="{router page='forum'}{$oForum->getUrl()}/">{$oForum->getTitle()}</a></h3>
								<p class="sv-details">Описание</p>
							</td>
							<td class="sv-last_msg">
								<a class="sv-subj" href="/forum/topic/353">{$oTopic->getTitle()}</a><br />
								<a class="sv-author" href="/users/view/1936">Автор</a>
								<span class="sv-date">@ 04.05.2011, 18:32</span>
							</td>
							<td class="sv-answers">346</td>
							<td class="sv-views">11</td>
						</tr>
					{/foreach}
				</tbody>
			</table>
		</div>

	{/foreach}
	<div class="sv-shadow"></div>

</div>
{include file='footer.tpl'}