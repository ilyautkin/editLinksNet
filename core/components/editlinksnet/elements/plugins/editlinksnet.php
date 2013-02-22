<?php
if ($modx->event->name == 'OnWebPagePrerender') {
    if (!$modx->user->isAuthenticated('mgr')) {return;}
    if ($_COOKIE['editLinksNet']) {
        $modx->runSnippet('FormIt', array(
            'hooks' => 'editLinksNet,redirect',
            'redirectTo' => $modx->resource->get('id'),
            'submitVar'=>'url',
            'validate'=>'outer:allowTags'));
        if ($_GET['act'] == 'cancel') $modx->runSnippet('editLinksNet', array(
                  'id' => $modx->resource->get('id'),
                  'url' => 'cancel',
                  'outer' => $_SESSION[$modx->resource->get('id').'_backup']));
        $html = "\n".$modx->getChunk('editLinksNet');
    } else {
        $html = "\n".'<div style="position:fixed;z-index:1000;right:5px;top:5px;"
         id="editLinksNet"><a href="'.$modx->makeUrl($modx->resource->get('id')).'?act=on"
         style="display: block; color: #fff;
         text-decoration: none; padding: 5px 4px; background: #779937;
         border-radius: 3px; border: 1px solid #5C7F17;
         margin: 3px auto 5px;">Включить перелинковку</a>
        </div>';
        if ($_GET['act'] == 'on') {
            SetCookie("editLinksNet","editLinksNetOn",time()+18000);
            $modx->sendRedirect($modx->makeUrl($modx->resource->get('id')));
        }
    }
    
    $modx->resource->_output .= $html;
}