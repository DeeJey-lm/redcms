<?php
include_once '../../sys/inc/start.php';
$doc = new document(2);
$doc->title = __('Управление таблицами');
$doc->ret(__('Админка'), '../');
if(!$user->access('dpanel_db_dopusk')) $doc->access_denied(__('У Вас нет доступа!'));
$menu = new menu_ini('db_dpanel');
$menu->display();