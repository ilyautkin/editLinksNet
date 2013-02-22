<?php

$snippets = array();


$snippet= $modx->newObject('modSnippet');
$snippet->fromArray(array(
    'name' => 'editLinksNet',
    'description' => '',
    'snippet' => getSnippetContent($sources['source_core'].'/elements/snippets/editlinksnet.php'),
),'',true,true);
$snippets[] = $snippet;


return $snippets;