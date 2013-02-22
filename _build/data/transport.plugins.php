<?php

$plugins = array();

/*
 * New plugin
 */
$plugin = $modx->newObject('modPlugin');
$plugin->set('id', null);
$plugin->set('name', 'editLinksNet');
$plugin->set('description', '');
$plugin->set('plugincode', getSnippetContent($sources['source_core'].'/elements/plugins/editlinksnet.php'));


/* add plugin events */
$events = array(); 
$events['OnWebPagePrerender']= $modx->newObject('modPluginEvent');
$events['OnWebPagePrerender']->fromArray(array(
    'event' => 'OnWebPagePrerender',
    'priority' => 0,
    'propertyset' => 0,
),'',true,true);
$plugin->addMany($events, 'PluginEvents');
$modx->log(xPDO::LOG_LEVEL_INFO,'Packaged in '.count($events).' Plugin Events.'); flush();
 
$plugins[] = $plugin;


return $plugins;

