<?
PageLayout::removeStylesheet('style.css');
PageLayout::addStylesheet('print.css');
//$layout = $GLOBALS['template_factory']->open('layouts/base_without_infobox.php');
//echo $layout->render(array('content_for_layout' => $content_for_layout));



include 'lib/include/html_head.inc.php';

?>
<div id="layout_container" style="padding:1px;padding-bottom:50px;">
<?
echo $content_for_layout;
?>
</div>
</body>
</html>
