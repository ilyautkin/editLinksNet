<?php

error_reporting(E_ALL ^E_NOTICE);
ini_set('display_errors', true);
 
 
define('MODX_BASE_PATH', dirname(dirname(dirname(__FILE__))) . '/');
define('MODX_CORE_PATH', MODX_BASE_PATH . 'core/');
define('MODX_MANAGER_PATH', MODX_BASE_PATH . 'manager/');
define('MODX_CONNECTORS_PATH', MODX_BASE_PATH . 'connectors/');
define('MODX_ASSETS_PATH', MODX_BASE_PATH . 'assets/');

define('MODX_BASE_URL','/modx/');
define('MODX_CORE_URL', MODX_BASE_URL . 'core/');
define('MODX_MANAGER_URL', MODX_BASE_URL . 'manager/');
define('MODX_CONNECTORS_URL', MODX_BASE_URL . 'connectors/');
define('MODX_ASSETS_URL', MODX_BASE_URL . 'assets/');
 
 
 
/*
 * Include MODX config
 */
require_once MODX_CORE_PATH . 'config/config.inc.php';

/* define sources */
$root = MODX_BASE_PATH; 

/*
 * Константы
 */
$sources = array(
    'root' => $root,
    'build' => $root . '_build/',
    'data' => $root . '_build/data/',
    'resolvers' => $root . '_build/resolvers/',
    'chunks' => $root.'core/components/'.PKG_PATH.'/elements/chunks/',
    'snippets' => $root.'core/components/'.PKG_PATH.'/elements/snippets/',
    'plugins' => $root.'core/components/'.PKG_PATH.'/elements/plugins/',
    'lexicon' => $root . 'core/components/'.PKG_PATH.'/lexicon/',
    'docs' => $root.'core/components/'.PKG_PATH.'/docs/',
    'pages' => $root.'core/components/'.PKG_PATH.'/elements/pages/',
    'source_assets' => $root.'assets/components/'.PKG_PATH,
    'source_core' => $root.'core/components/'.PKG_PATH,
    'templates' => $root.'core/components/'.PKG_PATH.'/elements/templates/',
    'model' => $root.'core/components/'.PKG_PATH.'/model/',
);
unset($root);
 
require_once MODX_CORE_PATH . 'model/modx/modx.class.php';
require_once $sources['build'] . 'includes/functions.php';

$modx= new modX();
$modx->initialize('mgr'); 