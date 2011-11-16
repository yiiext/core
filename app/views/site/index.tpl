<h2>index.tpl</h2>

Hi, this is yiiext...<br/><br/>

This site will come up soon with more and real content.

<hr/>

list of all extensions...


{foreach $repos as $repo}
    {$this->renderPartial('_repo', [
        'repo' => $repo
    ])}
{/foreach}