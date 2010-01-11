{if $Naf.Used.Single}
<div class="info">
    <h1 style="margin-top: 0pt;">{$Naf.Single.Text1}</h1>
    <div style="margin-left: 1px;">{$Naf.Single.Text2}</div>
</div>
{else}
<ul class="article">
    {foreach key=key item=glossary from=$Naf.Content name=ArticlesHeaders}
    <li>
        <a href="?id={$currentPageId}&amp;{$glossary.Url}">{$glossary.Text1}</a>
        <br>{$glossary.Text2}<br><br>
    </li>
    {/foreach}
</ul>
{/if}