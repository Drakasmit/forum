				{assign var="oUser" value=$oPost->getUser()}
				<div class="sv-post" id="post_id_{$oPost->getId()}">
					<span class="sv-corners sv-tl"></span>
					<span class="sv-corners sv-tr"></span>
					<div class="sv-personal">
						{if $oTopic->getDateRead()<=$oPost->getDate()}newest post{else}readed post{/if}<br />
						/{$oTopic->getDateRead()}\
						<img alt="{$oUser->getUserLogin()}" src="{$oUser->getProfileAvatarPath(100)}" class="sv-avavtar" />
						<span class="sv-nickname"><a href="{$oUser->getUserWebPath()}">{$oUser->getUserLogin()}</a></span>
						<span class="sv-msg_count">{$aLang.articles}: <span>{$oUser->getCountArticles()}</span></span>
						<span class="sv-msg_count">{$aLang.comments}: <span>{$oUser->getCountComments()}</span></span>
						<span class="sv-msg_count">{$aLang.topics}: <span>{$oUser->getCountTopics()}</span></span>
						<span class="sv-sbj_count">{$aLang.posts}: <span>{$oUser->getCountPosts()}</span></span>
					</div>
					<div class="sv-post_section">
						<div class="sv-post_section1">
							<span class="sv-post_date"><a {if $oTopic->getDateRead()<=$oPost->getDate()}class="new"{/if} href="#post-{$oPost->getId()}" name="post-{$oPost->getId()}">#</a> {date_format date=$oPost->getDate()}</span>
							<div class="sv-post_body">
								{$oPost->getText()}
							</div>
						</div>
					</div>
					<span class="sv-corners sv-bl"></span>
					<span class="sv-corners sv-br"></span>
					<div class="clear"></div>
				</div>