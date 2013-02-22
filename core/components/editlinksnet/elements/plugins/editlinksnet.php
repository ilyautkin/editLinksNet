<?php
if ($modx->event->name == 'OnWebPagePrerender') {
    if (!$modx->user->isAuthenticated('mgr') || !$modx->hasPermission('edit_document')) {return;}
    if ($_COOKIE['editLinksNet']) {
			
		if (!$_GET['act'] || !$_POST) return false;

		if (!$id) $id = (int) $_POST['id'];
		if (!$url) $url = $_POST['url'];
		if (!$outer) $outer = $_POST['outer'];

        if ($_GET['act'] == 'cancel') {
			$id = $modx->resource->get('id');
			$url = 'cancel';
            $outer = $_SESSION[$modx->resource->get('id').'_backup'];
		}
		
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
		  $modx->sendRedirect($modx->makeUrl($id));
		} else {return false;}
		
		$html = "\n".'			<div id="popUpBox" style="position:absolute; display:none;
			background:#fff; cursor: pointer; border:3px solid #ccc;
			color: #333; font: bold 14px arial; padding:5px 15px;">
			<form action="[[~[[*id]]]]?act=editLink" method="post" class="form">
				<input type="text" name="url" id="url"
				 value="[[++site_url]][[*parent:eq=`0`:then=``:else=`[[~[[*parent]]]]`]]">
				<textarea style="display: none;" name="outer" id="outer"></textarea>
				<input type="hidden" name="id" value="[[*id]]">
				<input type="submit" name="submit" value="OK" style="width: 40px; height: 28px;"
				 class="btn primary">
			</form>
			</div>
			<div style="position:fixed;z-index:1000;right:5px;top:5px;" id="editLinksNet">
				<a href="[[~[[*id]]]]?act=cancel" style="display: block; color: #fff;
				 text-decoration: none; padding: 5px 4px; background: #779937;
				 border-radius: 3px; border: 1px solid #5C7F17;
				 margin: 3px auto 5px;">Отмена последней ссылки</a>
			</div>
			<script type="text/javascript" src="/assets/components/editlinksnet/js/selection.js"></script>
			<script type="text/javascript">
			jQuery(function($) {
				var $txt = "";
				$("#content").bind("mouseup", function(e){
						
						var txt = $.selection().get().text // вернет выделенный text
						if (txt!=""){
						$("abbr").replaceWith($("abbr").html());
						// Заменяем выделенный текст на ссылку
						$.selection().set("<abbr style=\"color: #00c; text-decoration: underline;\">"
						  + txt + "</abbr>"); 
						$("#popUpBox").css({"display":"block", "left":e.pageX-60+"px",
						  "top":e.pageY+15+"px"});
						$("#url").focus();
						$("#url").select();
						$("#outer").html($("#content").html());
						} else {
						$("#popUpBox").css({"display":"none"});
						$("abbr").replaceWith($("abbr").html());
						}
				});
			});
			</script>';
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