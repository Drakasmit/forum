{assign var="oUser" value=$oPost->getUser()}
<div class="sv-post{if $oRead and $oRead->getDate()<=$oPost->getDateAdd()} new{/if}" id="post{$oPost->getId()}">
	<span class="sv-corners sv-tl"></span>
	<span class="sv-corners sv-tr"></span>
	<div class="sv-personal">
		<img alt="{$oUser->getLogin()}" src="{$oUser->getProfileAvatarPath(100)}" class="sv-avavtar" />
		<span class="sv-nickname"><a href="{$oUser->getUserWebPath()}">{$oUser->getLogin()}</a></span>
	</div>
	<div class="sv-post_section">
		<div class="sv-post_section1">
			<span class="sv-post_date"><a{if $oTopic->getDateRead()<=$oPost->getDateAdd()} class="new"{/if} href="#post-{$oPost->getId()}" name="post-{$oPost->getId()}">#</a> {date_format date=$oPost->getDateAdd()}</span>
			<div class="sv-post_body">
				{$oPost->getText()}
			</div>
		</div>
	</div>
	<span class="sv-corners sv-bl"></span>
	<span class="sv-corners sv-br"></span>
	<div class="clear"></div>
</div>