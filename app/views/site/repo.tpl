<div id="repo">
    {if empty($readme)}
        <h1>Extension {$repo->name}</h1>

        {*<p>{$repo->description|default:"<i>No description available.</i>"}</p>*}


        <p>Sorry, no readme files available jet. Please choose resources on right navigation.</p>


        {*<pre>
        {print_r($repo, 1)}
        </pre>*}

    {else}
        {$readme|default:""}
    {/if}

    <h2 id="changelog">Changelog</h2>

    {$changelog|default:'<i>No changelog available</i>'}

</div>

<div id="repo-sidebar">
    {$this->renderPartial('_repo', [
        'repo' => $repo,
        'noReadme' => true
    ])}

    <h2>Documentation</h2>
    <ul>
    {foreach $readmeFiles as $readmeLang => $file}
        <li>{CHtml::link('readme '|cat:$readmeLang|strtolower, ['site/repoReadme', 'name'=>$repo->name, 'lang'=>$readmeLang])}</li>
    {/foreach}
    <li>{CHtml::link('changelog ', $this->createUrl('site/repo', ['name'=>$repo->name])|cat:'#changelog')}</li>
    </ul>

    <h2>Downloads (Tags)</h2>
    <ul>
    {foreach $this->api->getRepoTags($repo->name) as $tag}
        <li>{$tag->name} -
        {CHtml::link('tar', $tag->tarball_url)},
        {CHtml::link('zip', $tag->zipball_url)}
        </li>
    {/foreach}
    </ul>

    <h2>Resources</h2>
    <ul>
        <li>{CHtml::link('Browse Code', $repo->html_url)}</li>
        {if $repo->has_issues}<li>{CHtml::link('Issues', $repo->html_url|cat:'/issues')} ({$repo->open_issues})</li>{/if}
        {if $repo->has_wiki}<li>{CHtml::link('Wiki', $repo->html_url|cat:'/wiki')}</li>{/if}
        <li>...</li>
    </ul>

</div>

<br style="clear: both;"/>

