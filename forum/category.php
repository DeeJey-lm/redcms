<?php
include_once '../sys/inc/start.php';
$doc = new document();
$doc->title = __('Форум');
$doc->ret(__('К категориям'), './');
if (!isset($_GET['id']) || !is_numeric($_GET['id'])) {
    header('Refresh: 1; url=./');
    $doc->err(__('Ошибка выбора категории'));
    exit;
}
$id_cat = (int)$_GET['id'];
$q = $db->prepare("SELECT * FROM `forum_categories` WHERE `id` = ? AND `group_show` <= ?");
$q->execute(Array($id_cat, $user->group));
if (!$category = $q->fetch()) {
    header('Refresh: 1; url=./');
    $doc->err(__('Категория не доступна'));
    exit;
}
$doc->title .= ' - ' . $category['name'];
$res = $db->prepare("SELECT COUNT(*) FROM `forum_topics` WHERE `id_category` = ? AND `group_show` <= ?");
$res->execute(Array($category['id'], $user->group));
$pages = new pages;
$pages->posts = $res->fetchColumn(); // количество категорий форума
$q = $db->prepare("SELECT * FROM `forum_topics` WHERE `id_category` = ? AND `group_show` <= ? ORDER BY `time_last` DESC LIMIT " . $pages->limit);
$q->execute(Array($category['id'], $user->group));
$listing = new listing();
while ($topics = $q->fetch()) {
$count_t = $db->query("SELECT COUNT(*) FROM `forum_themes` WHERE `id_topic` = '" . $topics['id']."' AND `group_show` <= '" . $user->group."'")->fetchColumn();
    $post = $listing->post();
    $post->icon('forum.topic.png');
    $post->title = text::toValue($topics['name']);
    $post->counter = $count_t.' тем'; 
    $post->content = text::for_opis($topics['description']);
    $post->url = "topic.php?id={$topics['id']}";
}
$listing->display(__('Доступных Вам разделов нет'));
$pages->display('?id=' . $id_cat . '&amp;'); // вывод страниц
if($user->access('forum_new_razdel'))$doc->act(__('Создать раздел'), 'topic.new.php?id_category=' . $category['id'] . "&amp;return=" . URL);
if($user->access('forum_set_kat'))$doc->act(__('Параметры категории'), 'category.edit.php?id=' . $category['id'] . "&amp;return=" . URL);
if($user->id)$doc->act(__('Статистика'), 'category.stat.php?id=' . $category['id'] . "&amp;return=" . URL);
