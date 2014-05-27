<div style="padding-left:1em;padding-right:1em">
<?php echo $this->render_partial('show/_navigator.php');?>
<?php if (isset($group_id)) :?>
<h4>
<?php echo htmlReady($group_name)?>
<div style="padding-left:5px;height:1.5em;">
<?php echo Assets::img('icons/16/blue/print.png', array('style' => 'vertical-align:bottom;'))?>
<?php $start = strtotime('this monday');foreach(range(0,6) as $d) : ?>
<a target="_blank" href="<?php echo $this->controller->link_for('show/group_print/' . $group_id . '/' . ($d+1))?>">
 [<?=strftime('%a', strtotime("+$d day", $start));?>]
</a>
<?php endforeach;?>
<? if ($only_one_week) : ?>
    <span style="padding-left:20px;">
    <a target="_blank" href="<?php echo $this->controller->link_for('show/group_print/' . $group_id)?>">
     [alle]
    </a>
    </span>
<? endif; ?>
</div>
<!--<div style="padding-left:5px;">
<?php echo Assets::img('icons/16/blue/file-text.png', array('style' => 'vertical-align:bottom;'))?>
<?php $start = strtotime('this monday');foreach(range(0,6) as $d) : ?>
<a href="<?php echo $this->controller->link_for('show/group_tex/' . $group_id . '/' . ($d+1))?>">
 [<?=strftime('%a', strtotime("+$d day", $start));?>]
</a>
<?php endforeach;?>
</div>
-->
</h4>
<ol>
<?php foreach($data as $room_id => $room) :?>
<li style="height:1.5em;">
<span title="PDF">
<a target="_blank" href="<?php echo $this->controller->link_for('show/room_print/' . $room_id . '/' . $group_id)?>">
<?php echo Assets::img('icons/16/blue/print.png', array('style' => 'vertical-align:bottom;'))?>
</a>
</span>
<!--
<span title="TEX">
<a href="<?php echo $this->controller->link_for('show/room_tex/' . $room_id . '/' . $group_id)?>">
<?php echo Assets::img('icons/16/blue/file-text.png', array('style' => 'vertical-align:bottom;'))?>
</a>
</span>
-->
<span style="padding-left:5px;">
<?php echo htmlReady($room['name'])?>
</span>

</li>
<?php endforeach;?>
</ol>
<?php endif;?>
</div>