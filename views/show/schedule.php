<h1><?php echo htmlready($roomname)?></h1>
<h2><?php echo htmlready($groupname)?></h2>
<h2>
<?php
if ($only_one_week) {
    echo htmlready($semester->name . ' - ' . $sem_weeks[$current_sem_week]);
} else {
    echo htmlready($semester->name);
    echo '<div style="font-size:70%">Einzelbelegungen ab ' .  $sem_weeks[$current_sem_week] . '</div>';
}
?>
</h2>

<?php
$schedule->showSchedule("html", true);
if (!$only_one_week) {
    ?>
    <div style="font-weight:bold">Regelm‰ﬂige Belegungen von Lehrveranstaltungen:</div>
    <table cellspacing="2" cellpadding="2" border="0" width="100%">
    <?php foreach (array_filter($data, create_function('$a', 'return isset($a["metadate_id"]);')) as $one) { ?>
        <tr>
        <td width="10%">
        <?php echo htmlready($one['instabbr'])?>
        </td>
        <td width="50%">
        <?php echo htmlready($one['name'])?>
        </td>
        <td width="20%">
        <?php echo htmlready($one['sem_doz_names'])?>
        </td>
    	<td width="5%">
        <?php echo htmlready($one['cycle'])?>
        </td>
        <td nobreak width="15%">
        <?php echo htmlready(strftime("%a %R", $one['begin']) . ' - ' . strftime("%R", $one['end']))?>
        </td>
        </tr>
    <?php }?>
    </table>
    <div style="font-weight:bold">Zus‰tzliche Belegungen:</div>
    <table cellspacing="2" cellpadding="2" border="0" width="100%">
    <?php foreach (array_filter($data, create_function('$a', 'return !isset($a["metadate_id"]);')) as $one) { ?>
        <tr>
        <td width="10%">
        <?php echo htmlready($one['instabbr'])?>
        </td>
        <td width="50%">
        <?php echo htmlready($one['shortname']  ? $one['shortname'] . ':'. $one['name'] : $one['name'])?>
        </td>
        <td width="20%">
        <?php echo htmlready($one['sem_doz_names'])?>
        </td>
    	<td width="5%">
        <?php echo htmlready($one['cycle'])?>
        </td>
        <td nobreak width="15%">
        <?php echo htmlready(strftime("%a, %x %R", $one['begin']) . ' - ' . strftime("%R", $one['end']))?>
        </td>
        </tr>
    <?php }?>
    </table>
<?php
}