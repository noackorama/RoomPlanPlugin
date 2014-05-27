<h2>
<?php
echo htmlready($semester->name . ' - ' . $sem_weeks[$current_sem_week]);
?>
</h2>
<h2><?php echo htmlready($groupname)?></h2>
<table border="1" width="100%" cellpadding="2" cellspacing="2">
<tr>
<th width="5%">
Datum
</th>
<th width="5%">
Wochentag
</th>
<th width="10%">
Zeit
</th>
<th width="25%">
Raum
</th>
<th width="20%">
VAK
</th>
<th width="35%">
Veranstalter
</th>
</tr>
<? foreach ($data as $one) : ?>
<tr>
    <td>
    <?= htmlReady(strftime('%x', $one['begin']));?>
    </td>
    <td>
    <?= htmlReady(strftime('%A', $one['begin']));?>
    </td>
    <td>
    <?= htmlready(strftime("%R", $one['begin']) . ' - ' . strftime("%R", $one['end']));?>
    </td>
    <td>
    <?= htmlReady($one['room_name']);?>
    </td>
    <td>
    <?= htmlReady($one['instabbr']);?>
    </td>
    <td>
    <?= htmlReady( $one['seminar_id'] ? $one['name'] . ' ('.$one['sem_doz_names'].')' : $one['name']);?>
    </td>
</tr>
<? endforeach; ?>
</table>
<div style="font-style:italic;margin-top: 10px;">
Erstellt: <?=strftime('%x %R')?>
</div>
