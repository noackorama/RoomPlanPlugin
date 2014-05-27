<h2>
<?php
echo htmlready($semester->name . ' - ' . $sem_weeks[$current_sem_week]);
?>
</h2>
<h2><?php echo htmlready($groupname)?></h2>
<?
foreach ($data as $room_id => $room_data) {
    echo '<div style="margin-top:10px">';
    if (count($room_data['room_data'])) {
        echo $this->render_partial('show/weekend_room.php', array('data' => $room_data['room_data'], 'roomname' => $room_data['name'], 'one_room' => false));
    }
    echo '</div>';
}
?>
<div style="font-style:italic;margin-top: 10px;">
Erstellt: <?=strftime('%x %R')?>
</div>
