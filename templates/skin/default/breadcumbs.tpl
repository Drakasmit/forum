<span><a href="{router page='forum'}">{$aLang.forums}</a></span>

{foreach from=$aBreadcumbs item=aItem name=breadcumbs}
	{if $smarty.foreach.breadcumbs.last}
		<a href="{$aItem.url}">{$aItem.title}</a>
	{else}
		<span><a href="{$aItem.url}">{$aItem.title}</a></span>
	{/if}
{/foreach}