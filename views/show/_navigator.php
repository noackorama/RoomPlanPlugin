<form method="POST" name="schedule_form" action="<?=$controller->link_for('', array('current_sem_week' => null)) ?>">
<div>
<b><?=_("Semester:")?></b>
</div>
<div>
<?=SemesterData::GetSemesterSelector(array('name' => 'sem_schedule_choose', 'onChange' => 'document.schedule_form.submit()'), $_SESSION['_default_sem'],'semester_id',false)?>
&nbsp;
<select name="sem_week_choose"  onChange="document.schedule_form.submit()">
<? if ($sem_weeks) foreach($sem_weeks as $n => $sem_week) : ?>
<option value="<?=$n?>" <?=($n == $current_sem_week ? 'selected' : '')?>><?=$sem_week?></option>
<? endforeach ?>
</select>
<? echo Studip\Button::create(_("Auswählen"),"jump") ?>
</div>
<div>
<b>
<?=_("Eine Raumgruppe auswählen:")?>
</b>
</div>
<div>
<select name="group_schedule_choose_group" onChange="document.schedule_form.submit()">
<?
foreach($room_groups->getAvailableGroups() as $gid){
    echo '<option value="'.$gid.'" '
    . ($this->group_id == $gid ? 'selected' : '') . '>'
    .htmlReady(my_substr($room_groups->getGroupName($gid),0,100))
    .' ('.$room_groups->getGroupCount($gid).')</option>';
}
?>
</select>
&nbsp;
<? echo Studip\Button::create(_("Auswählen"), "group_schedule_start") ?>
</div>
<? if ($this->controller->current_action == 'weekend') : ?>
<div>
<input onChange="document.schedule_form.submit()" type="checkbox" id="weekend_choose_6" name="weekend_choose[6]" value="6" <?=($this->weekend_choose[6] ? 'checked' : '')?>>
<label for="weekend_choose_6">Samstag</label>
<input onChange="document.schedule_form.submit()" type="checkbox" id="weekend_choose_7" name="weekend_choose[7]" value="7" <?=($this->weekend_choose[7] ? 'checked' : '')?>>
<label for="weekend_choose_7">Sonntag</label>
&nbsp;
<? echo Studip\Button::create(_("Auswählen"), "group_schedule_start") ?>
</div>
<? endif ?>
</form>

