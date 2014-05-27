<? if ($one_room) : ?>
<h2>
    <?php
    echo htmlready($semester->name . ' - ' . $sem_weeks[$current_sem_week]);
    ?>
    </h2>
    <h2><?php echo htmlready($groupname)?></h2>
<? endif; ?>
<div style="font-weight:bold">
Folgende Veranstaltungen finden voraussichtlich statt:
</div>
<table border="1" width="100%" cellpadding="2" cellspacing="2">
<tr>
<th>
Datum
</th>
<th>
Wochentag
</th>
<th>
Zeit
</th>
<th>
Raum
</th>
<th>
VAK
</th>
<th>
Veranstalter
</th>
</tr>
<? foreach($data as $one) : ?>
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
    <?= htmlReady($roomname);?>
    </td>
    <td>
    <?= htmlReady($one['instabbr']);?>
    </td>
    <td>
    <?= htmlReady( $one['seminar_id'] ? $one['sem_doz_names'] : $one['name']);?>
    </td>
</tr>
<? endforeach; ?>
</table>
<? if ($one_room) : ?>
<div style="font-style:italic;margin-top: 10px;">
Erstellt: <?=strftime('%x %R')?>
</div>
<? endif;?>
