<?php

$pkg_name = 'editLinksNet';
    
/* define package */
define('PKG_NAME', $pkg_name);
define('PKG_NAME_LOWER',strtolower(PKG_NAME));
define('NAMESPACE_NAME', PKG_NAME_LOWER);

define('PKG_PATH', PKG_NAME_LOWER);
define('PKG_CATEGORY', PKG_NAME);

$pkg_version = '0.1.0';
$pkg_release = 'beta';
define('PKG_VERSION', $pkg_version); 
define('PKG_RELEASE', $pkg_release); 


$mtime= microtime();
$mtime= explode(" ", $mtime);
$mtime= $mtime[1] + $mtime[0];
$tstart = $mtime;

/* define sources */
$root = dirname(dirname(__FILE__)).'/';
$sources = array(
    'root' => $root,
    'build' => $root . '_build/',
    'data' => $root . '_build/data/',
    'chunks' => $root.'assets/components/'.PKG_NAME_LOWER.'/elements/chunks/',
    'templates' => $root.'assets/components/'.PKG_NAME_LOWER.'/elements/templates/',
    'docs' => $root.'assets/components/'.PKG_NAME_LOWER.'/docs/',
    'source_assets' => $root.'assets/components/'.PKG_NAME_LOWER,
);
unset($root);

/* override with your own defines here (see build.config.sample.php) */
require_once $sources['build'] . '/build.config.php';
require_once MODX_CORE_PATH . 'model/modx/modx.class.php';
require_once $sources['build'] . '/includes/functions.php';

$modx= new modX();
$modx->initialize('mgr');
//echo '<pre>'; /* used for nice formatting of log messages */
$modx->setLogLevel(modX::LOG_LEVEL_INFO);
$modx->setLogTarget('ECHO');

$modx->loadClass('transport.modPackageBuilder','',false, true);


/*
 * Create builder
 */
$builder = new modPackageBuilder($modx);

/*
 * create Package
 */
$builder->createPackage(PKG_NAME,PKG_VERSION,PKG_RELEASE);
$builder->registerNamespace(PKG_NAME_LOWER,false,true,'{core_path}components/'.PKG_NAME_LOWER.'/');

/*
 * load lexicon
 */
/*$modx->getService('lexicon','modLexicon');
$modx->lexicon->load(PKG_NAME_LOWER.':properties');*/
 


/* add namespace */
$namespace = $modx->newObject('modNamespace');
$namespace->set('name', NAMESPACE_NAME);
$namespace->set('path',"{core_path}components/".PKG_NAME_LOWER."/");
$namespace->set('assets_path',"{assets_path}components/".PKG_NAME_LOWER."/");
$vehicle = $builder->createVehicle($namespace,array(
    xPDOTransport::UNIQUE_KEY => 'name',
    xPDOTransport::PRESERVE_KEYS => true,
    xPDOTransport::UPDATE_OBJECT => true,
));
$builder->putVehicle($vehicle);
$modx->log(modX::LOG_LEVEL_INFO,"Packaged in ".NAMESPACE_NAME." namespace."); flush();
unset($vehicle,$namespace);


/* create category */
$category= $modx->newObject('modCategory');
$category->set('id',1);
$category->set('category',PKG_NAME);
$modx->log(modX::LOG_LEVEL_INFO,'Packaged in category.'); flush();
  

/* create category vehicle */
$attr = array(
    xPDOTransport::UNIQUE_KEY => 'category',
    xPDOTransport::PRESERVE_KEYS => false,
    xPDOTransport::UPDATE_OBJECT => true,
    xPDOTransport::RELATED_OBJECTS => true,
    xPDOTransport::RELATED_OBJECT_ATTRIBUTES => array (
        'Snippets' => array(
            xPDOTransport::PRESERVE_KEYS => false,
            xPDOTransport::UPDATE_OBJECT => true,
            xPDOTransport::UNIQUE_KEY => 'name',
        ),
        'Chunks' => array(
            xPDOTransport::PRESERVE_KEYS => false,
            xPDOTransport::UPDATE_OBJECT => true,
            xPDOTransport::UNIQUE_KEY => 'name',
        ),
        'Plugins' => array(
            xPDOTransport::PRESERVE_KEYS => false,
            xPDOTransport::UPDATE_OBJECT => true,
            xPDOTransport::UNIQUE_KEY => 'name',
        ),
        'PluginEvents' => array(
            xPDOTransport::PRESERVE_KEYS => true,
            xPDOTransport::UPDATE_OBJECT => false,
            xPDOTransport::UNIQUE_KEY => array('pluginid','event'),
        ),
    )
);


/* add plugins */ 
$plugins = include $sources['data'].'transport.plugins.php';

if (!is_array($plugins)) { $modx->log(modX::LOG_LEVEL_FATAL,'Adding plugins failed.'); } 
else{
    $category->addMany($plugins);
    $modx->log(modX::LOG_LEVEL_INFO,'Packaged in '.count($plugins).' plugins.'); flush();
}

unset($plugins,$plugin,$attributes);


/* add snippets */
$snippets = include $sources['data'].'transport.snippets.php';
if (!is_array($snippets)) {
    $modx->log(modX::LOG_LEVEL_ERROR,'Could not package in snippets.');
} else {
    $category->addMany($snippets);
    $modx->log(modX::LOG_LEVEL_INFO,'Packaged in '.count($snippets).' snippets.');
}

/* add chunks */
$chunks = include $sources['data'].'transport.chunks.php';
if (!is_array($chunks)) {
    $modx->log(modX::LOG_LEVEL_ERROR,'Could not package in chunks.');
} else {
    $category->addMany($chunks);
    $modx->log(modX::LOG_LEVEL_INFO,'Packaged in '.count($chunks).' chunks.');
}

/*
 * Create category vehicle
 */
$vehicle = $builder->createVehicle($category,$attr);

// Add core source
$vehicle->resolve('file',array(
    'source' => $sources['source_core'],
    'target' => "return MODX_CORE_PATH . 'components/';",
));
$modx->log(modX::LOG_LEVEL_INFO,'Packaged in CorePath'); flush();

// Add assets source
$vehicle->resolve('file',array(
    'source' => $sources['source_assets'],
    'target' => "return MODX_ASSETS_PATH . 'components/';",
));
$modx->log(modX::LOG_LEVEL_INFO,'Packaged in AssetsPath'); flush();
 

$modx->log(modX::LOG_LEVEL_INFO,'Packaged in resolvers.'); 

flush();


$builder->putVehicle($vehicle);

/* now pack in the license file, readme and setup options */
$builder->setPackageAttributes(array(
    'license' => file_get_contents($sources['docs'] . 'license.txt'),
    'readme' => file_get_contents($sources['docs'] . 'readme.txt'),
    'changelog' => file_get_contents($sources['docs'] . 'changelog.txt'), 
));
$modx->log(modX::LOG_LEVEL_INFO,'Packaged in package attributes.'); flush();

$modx->log(modX::LOG_LEVEL_INFO,'Packing...'); flush();
$builder->pack();

$mtime= microtime();
$mtime= explode(" ", $mtime);
$mtime= $mtime[1] + $mtime[0];
$tend= $mtime;
$totalTime= ($tend - $tstart);
$totalTime= sprintf("%2.4f s", $totalTime);

$modx->log(modX::LOG_LEVEL_INFO,"\n<br />Package Built.<br />\nExecution time: {$totalTime}\n");

exit ();
