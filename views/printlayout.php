<?
PageLayout::removeStylesheet('style.css');
PageLayout::addStylesheet('print.css');
$layout = $GLOBALS['template_factory']->open('layouts/base_without_infobox.php');
echo $layout->render(array('content_for_layout' => $content_for_layout));

