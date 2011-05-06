{assign var="bNoSidebar" value=true}
{include file='header.tpl'}
<div id="forum">

	<div class="forum-nav">
		<h2>
			<span><a href="{router page='forum'}">{$aLang.main_title}</a></span> {$oForum->getTitle()}
		</h2>
	</div>

	<div class="sv-forum-block">

		<ul class="sv-tom_menu">
			<li><a class="newtopic" href="{router page='forum'}add/{$oForum->getId()}/">{$aLang.add_topic}</a></li>
			<li><a class="notfresh" href="#">{$aLang.mark_all_read}</a></li>
		</ul>


		<div class="sv-forum_nav">
			<div class="sv-numbers">
				<a class="sv-here" href="#">1</a>
				<a href="#">2</a>
				<a href="#">3</a>
				<a href="#">4</a>
			</div>
		</div>

		<div class="clear"></div>


		<div class="sv-forum_header sv-forum_header-section_page">
			<div class="sv-left_bg"><h2>{$oForum->getTitle()}</h2></div>
			<div class="sv-right_bg">
				<span class="sv-answers">{$aLang.replies}</span>
				<span class="sv-views">{$aLang.views}</span>
				<span class="sv-last_msg">{$aLang.last_post}</span>
			</div>
		</div>
		

		<div class="sv-table_container">
			<table class="sv-forum_body sv-forum_body-section_page">
				<tbody>
					{foreach from=$aTopics item=oTopic}
						{assign var="oUser" value=$oTopic->getUser()}
						<tr>
							<td class="sv-icon_col">
								<a class="bbl" href="{router page='forum'}{$oForum->getUrl()}/{$oTopic->getId()}-{$oTopic->getUrl()}.html"></a>
							</td>
							<td class="sv-main_col">
								<h3>
									<a href="{router page='forum'}{$oForum->getUrl()}/{$oTopic->getId()}-{$oTopic->getUrl()}.html">{$oTopic->getTitle()}</a>
									<span class="sv-go_to_page">[ {$aLang.on_page}: 1, 2 ]</span>
								</h3>
								<span class="sv-author"><a href="{$oUser->getUserWebPath()}">{$oUser->getLogin()}</a></span>
							</td>
							<td class="sv-answers">17</td>
							<td class="sv-views">1858</td>
							<td class="sv-last_msg">
								<span class="sv-date">04.05.2011, 19:48</span>
								<span class="sv-author">{$aLang.by} <a href="#">Автор</a></span>
							</td>
						</tr>
					{foreachelse}
						{$aLang.nothing}
					{/foreach}
				</tbody>
			</table>
		</div>

		<div class="sv-shadow"></div>

		<div class="sv-forum_nav">
			<div class="sv-numbers">
				<a class="sv-here" href="#">1</a>
				<a href="#">2</a>
				<a href="#">3</a>
				<a href="#">4</a>
			</div>
		</div>

		<ul class="sv-bottom_menu">
			<li><a class="newtopic" href="{router page='forum'}add/{$oForum->getId()}/">{$aLang.add_topic}</a></li>
			<li><a class="notfresh" href="#">{$aLang.mark_all_read}</a></li>
		</ul>

	</div>

</div>
{include file='footer.tpl'}