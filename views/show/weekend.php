<div style="padding-left:1em;padding-right:1em">
<?php echo $this->render_partial('show/_navigator.php');?>
<?php if (isset($group_id)) :?>
<h4>
<a target="_blank" href="<?php echo $this->controller->link_for('show/group_print_weekend/' . $group_id)?>">
<?php echo Assets::img('icons/16/blue/print.png', array('style' => 'vertical-align:bottom;'))?>
</a>
<?php echo htmlReady($group_name)?>
<span style="padding-left:20px;">
    <a target="_blank" href="<?php echo $this->controller->link_for('show/group_print_weekend/' . $group_id)?>">
     [alle]
    </a>
</span>
</h4>
<ol>
<?php foreach($data as $room_id => $room) :?>
<li style="height:1.5em;">
<span title="print">
<a target="_blank" href="<?php echo $this->controller->link_for('show/room_print_weekend/' . $room_id . '/' . $group_id)?>">
<?php echo Assets::img('icons/16/blue/print.png', array('style' => 'vertical-align:bottom;'))?>
</a>
</span>
<span style="padding-left:5px;">
<?php echo htmlReady($room['name'])?>
</span>

</li>
<?php endforeach;?>
</ol>
<?php endif;?>
</div>