<div class="repo{if isset($noReadme) && $noReadme} noreadme{/if}">
    <div class="repo-head">
        {if !isset($noReadme) || $noReadme==false}
        <div style="text-align: right; float: right;">
            {$readmes = $this->api->getRepoReadmeFilenames($repo->name)}
            {if empty($readmes)}
                no readme
            {else}
                readme:
                {foreach $this->api->getRepoReadmeFilenames($repo->name) as $readmeLang => $file}
                    {CHtml::link($readmeLang|strtolower, ['site/repoReadme', 'name'=>$repo->name, 'lang'=>$readmeLang], ['class'=>'readme'])}
                {/foreach}
            {/if}

            {*<b>{count($this->api->getRepoWatchers($repo->name))}</b>*}
        </div>
        {/if}

        <h3>{CHtml::link($repo->name|escape, ['site/repo', 'name'=>$repo->name])}</h3>
    </div>
    <p>{$repo->description|default:"<i>No description available.</i>"}</p>

    <div class="contributors">
    {foreach $this->api->getRepoContributors($repo->name) as $c}
        <a href="https://github.com/{$c->login}" title="{$c->login}">
            <img src="{$c->avatar_url}" alt="{$c->login}" style="height: 24px; width: 24px;"/>
        </a>
    {/foreach}
    </div>
</div>
