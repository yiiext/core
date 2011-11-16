<h2>{$repo->name|escape} {*strip*}
    {foreach $readmeFiles as $readmeLang => $file}
        {CHtml::link($readmeLang, ['site/repoReadme', 'name'=>$repo->name, 'lang'=>$readmeLang])}
    {/foreach}
{*/strip*}</h2>

<div id="repo">
    {$this->renderPartial('_repo', [
        'repo' => $repo
    ])}

    {$readme|default:""}

    {*<pre>
    {print_r($repo, 1)}
    </pre>*}
</div>

<div id="repo-sidebar">
    <h2>Downloads (Tags)</h2>
    <ul>
    {foreach $tags as $tag}
        <li>{$tag->name} -
        {CHtml::link('tar', $tag->tarball_url)},
        {CHtml::link('zip', $tag->zipball_url)}
        </li>
    {/foreach}
    </ul>
</div>

<br style="clear: both;"/>

