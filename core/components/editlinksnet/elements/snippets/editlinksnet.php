<?php
if (!$_GET['act']) return false;

if (!$id) $id = $hook->getValue('id');
if (!$url) $url = $hook->getValue('url');
if (!$outer) $outer = $hook->getValue('outer');

if (!$id || !$url || !$outer) return false;

$newContent  = str_replace('<abbr style="color: #00c; text-decoration: underline;">','<a href="'.$url.'">',$outer);
$newContent  = str_replace('</abbr>','</a>',$newContent);
$backup = $_SESSION[$id.'_backup'];

if ($_GET['act'] == 'cancel' && $_SESSION[$id.'_backup']) {$content = $backup;} else {$content = $newContent;}

// Получаем ресурс по id ресурса
$resource = $modx->getObject('modResource',$id);

if ($_GET['act'] != 'cancel') $_SESSION[$id.'_backup'] = $resource->get('content');

// Устанавливаем нужные значения
$resource->set('content',$content);

// Сохраняем ресурс
if ($resource->save()) {
  
// Очищаем кеш, чтобы изменения были видны сразу
  $modx->cacheManager->refresh();
  if ($_GET['act'] == 'cancel') {$modx->sendRedirect($modx->makeUrl($id));} else {return true;}
} else {return false;}