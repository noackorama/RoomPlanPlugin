<?php
$RELATIVE_PATH_RESOURCES = $GLOBALS['RELATIVE_PATH_RESOURCES'];
require 'application.php';
require_once 'lib/classes/StudipForm.class.php';
require_once 'lib/datei.inc.php';
require_once 'lib/resources/lib/CheckMultipleOverlaps.class.php';
require_once 'lib/resources/views/ScheduleWeek.class.php';
require_once 'lib/resources/views/SemScheduleWeek.class.php';
require_once 'lib/resources/views/SemGroupScheduleDayOfWeek.class.php';

class ShowController extends ApplicationController {

    private $ebnum = 0;

    function before_filter(&$action, &$args)
    {
        ini_set('memory_limit', '256M');
        parent::before_filter($action, $args);


        if(isset($_REQUEST['sem_schedule_choose'])) {
            if ($_SESSION['_default_sem'] == Request::option('sem_schedule_choose')) {
                $this->current_sem_week = Request::int('sem_week_choose');
            }
            $_SESSION['_default_sem'] = Request::option('sem_schedule_choose');
        }
        if(isset($_REQUEST['group_schedule_choose_group'])) $this->group_id = Request::option('group_schedule_choose_group');
        if (count(Request::intArray('weekend_choose'))) $this->weekend_choose = Request::intArray('weekend_choose');
        else $this->weekend_choose = array(6=>6,7=>7);
        $this->semester = Semester::find($_SESSION['_default_sem']);
        if(!$this->semester) {
            $this->semester = Semester::findCurrent();
            $_SESSION['_default_sem'] = $this->semester['semester_id'];
        }

        $this->only_one_week = (Request::int('only_one_week') || $GLOBALS['user']->id === 'nobody'|| $action == 'index_nobody' || $action == 'weekend') && $action != 'index';
        if (Request::int('current_sem_week') && $this->only_one_week == Request::int('only_one_week')) {
            $this->current_sem_week = Request::int('current_sem_week');
        }
        if ($this->semester) {
            if ($this->only_one_week) {
                $current = strtotime('monday this week', $this->semester->beginn);
                $i = 1;
                while($current < $this->semester->ende) {
                    $this->sem_weeks[$i] = sprintf(_("KW %s, ab %s %s"),
                        (int)strftime('%V', $current),
                        strftime('%x', $current),
                        $this->semester->getSemWeekNumber($current) ? sprintf('(%s. Vorlesungswoche)',$this->semester->getSemWeekNumber($current)) : '(vorlesungsfrei)' );
                    if (!$this->current_sem_week && strftime('%V', time()) === strftime('%V', $current)) {
                        $this->current_sem_week = $i;
                    }
                    $i++;
                    $current = strtotime('+1 week', $current);
                }
                if (!$this->current_sem_week) {
                    $this->current_sem_week = 1;
                }
            } else {
                $current = strtotime('monday this week', $this->semester->vorles_beginn);
                $i = 1;
                while($current < $this->semester->vorles_ende) {
                    $this->sem_weeks[$i] = sprintf(_("%s. Vorlesungswoche (KW %s, ab %s)"), $i, (int)strftime('%V', $current), strftime('%x', $current));
                    $i++;
                    $current = strtotime('+1 week', $current);
                }
                if (!$this->current_sem_week && time() >= $this->semester->vorles_beginn && time() <= $this->semester->vorles_ende) {
                    $this->current_sem_week = $this->semester->getSemWeekNumber(time());
                }
                if (!$this->current_sem_week) {
                    $this->current_sem_week = 1;
                }
            }
            if ($this->only_one_week) {
                $this->start_time = strtotime('+'.($this->current_sem_week-1).' week', strtotime('monday this week', $this->semester['beginn']));
                $this->end_time = strtotime('+1 week', $this->start_time);
                if (count(Request::intArray('weekend_choose'))) {
                    $new_start_time = strtotime('+' . min($this->weekend_choose) - 1 . ' day', $this->start_time);
                    $new_end_time = strtotime('+' . max($this->weekend_choose)  . ' day', $this->start_time) - 1;
                    $this->start_time = $new_start_time;
                    $this->end_time = $new_end_time;
                }
            } else {
                $this->start_time = strtotime('+'.($this->current_sem_week-1).' week', strtotime('monday this week', $this->semester['vorles_beginn']));
                $this->end_time = $this->semester['vorles_ende'];
            }
        }
    }

    function index_action()
    {
        if ($GLOBALS['user']->id === 'nobody') {
            throw new AccessDeniedException(_("Keine Berechtigung."));
        }
        Navigation::activateItem("/".$this->plugin->me."/show");
        $room_groups = new NobodyRoomGroups();
        $group = $room_groups->getGroupContent($this->group_id);
        foreach($group as $resource_id){
            $this->data[$resource_id]['name'] = getResourceObjectName($resource_id);
        }
        $this->group_name = $room_groups->getGroupName($this->group_id);
        $this->room_groups = $room_groups;
        UrlHelper::addLinkParam('current_sem_week', $this->current_sem_week);
    }

    function index_nobody_action()
    {
        if ($GLOBALS['user']->id !== 'nobody') {
            Navigation::activateItem("/".$this->plugin->me."/show_week");
            $room_groups = new NobodyRoomGroups();
        } else {
            $layout = 'layout_nobody.php';
            Navigation::activateItem("/".$this->plugin->me."/show_week");
            $room_groups = new NobodyRoomGroups();
        }
        $group = $room_groups->getGroupContent($this->group_id);
        foreach($group as $resource_id){
            $this->data[$resource_id]['name'] = getResourceObjectName($resource_id);
        }
        $this->group_name = $room_groups->getGroupName($this->group_id);
        $this->room_groups = $room_groups;
        UrlHelper::addLinkParam('current_sem_week', $this->current_sem_week);
        UrlHelper::addLinkParam('only_one_week', 1);
        if ($layout) return $this->render_template('show/index.php', $layout);
        else return $this->render_template('show/index.php', $this->layout);
    }

    function weekend_action()
    {
        if ($GLOBALS['user']->id !== 'nobody') {
            Navigation::activateItem("/".$this->plugin->me."/show_weekend");
            $room_groups = new NobodyRoomGroups();
        } else {
            Navigation::activateItem("/".$this->plugin->me."/show_weekend");
            $layout = 'layout_nobody.php';
            $room_groups = new NobodyRoomGroups();
        }
        $group = $room_groups->getGroupContent($this->group_id);
        foreach($group as $resource_id){
            $this->data[$resource_id]['name'] = getResourceObjectName($resource_id);
        }
        $this->group_name = $room_groups->getGroupName($this->group_id);
        $this->room_groups = $room_groups;
        UrlHelper::addLinkParam('current_sem_week', $this->current_sem_week);
        UrlHelper::addLinkParam('only_one_week', 1);
        UrlHelper::addLinkParam('weekend_choose', $this->weekend_choose);
        if ($layout) return $this->render_template('show/weekend.php', $layout);
        else return $this->render_template('show/weekend.php', $this->layout);
    }

    function room_print_weekend_action($room_id, $group_id)
    {
        $this->ebnum = 0;
        $this->ftnum = 0;
        //return $this->render_text('<pre>'.strftime('%x %X',$this->start_time) .strftime('%x %X',$this->end_time) .print_r($this->get_room_data($room_id),1).'</pre>');
        $data = $this->get_room_data($room_id);
        $this->roomname = getResourceObjectName($room_id);
        if ($GLOBALS['user']->id !== 'nobody') {
            $room_groups = new NobodyRoomGroups();
        } else {
            $room_groups = new NobodyRoomGroups();
        }
        $this->groupname = $room_groups->getGroupName((int)$group_id);
        $this->one_room = true;
        $this->data = $data;
        $this->render_template('show/weekend_room.php', 'printlayout.php');
    }

    function group_print_weekend_action($group_id)
    {
        if ($GLOBALS['user']->id !== 'nobody') {
            $room_groups = new NobodyRoomGroups();
        } else {
            $room_groups = new NobodyRoomGroups();
        }
        $group = $room_groups->getGroupContent((int)$group_id);
        $this->data = array();
        $this->ebnum = 0;
        $this->ftnum = 0;
        foreach($group as $resource_id){
            $res_obj = ResourceObject::Factory($resource_id);
            $this->data[$resource_id]['name'] = $res_obj->getName();
            $this->data[$resource_id]['seats'] = $res_obj->getSeats();
            $this->data[$resource_id]['room_data'] = $this->get_room_data($resource_id);
        }
        $this->groupname = $room_groups->getGroupName($group_id);
        $this->render_template('show/weekend_group.php', 'printlayout.php');

    }

    function room_print_action($room_id, $group_id)
    {
        $this->ebnum = 0;
        $this->ftnum = 0;
        //return $this->render_text('<pre>'.print_r($this->get_room_data($room_id),1).'</pre>');
        $data = $this->get_room_data($room_id);
        $this->roomname = getResourceObjectName($room_id);
        if ($GLOBALS['user']->id !== 'nobody') {
            $room_groups = new NobodyRoomGroups();
        } else {
            $room_groups = new NobodyRoomGroups();
        }
        $this->groupname = $room_groups->getGroupName((int)$group_id);
        if ($this->only_one_week) {
            $schedule = new ScheduleWeek(8, 22, FALSE, $this->start_time, true);
            foreach($data as $one) {
                $schedule->addEvent(null, $one['instabbr'] . ' - ' . $one['name'], $one['begin'], $one['end'],
                        "", $one['sem_doz_names']);
            }
        } else {
            $schedule = new SemScheduleWeek(8, 22, false , $this->start_time);
            foreach($data as $one) {
                if ($one['eb_id']) {
                    $name = $one['shortname'] . ':'. $one['name'];
                } else {
                    $name = $one['instabbr'] . ' - ' . $one['name'];
                }
                $schedule->addEvent(null, $name, $one['begin'], $one['end'],
                        "", $one['sem_doz_names']);
            }
        }
        $this->schedule = $schedule;
        $this->data = $data;
        $this->render_template('show/schedule.php', 'printlayout.php');
    }


    function group_print_action($group_id, $dow = false)
    {
        if ($GLOBALS['user']->id !== 'nobody') {
            $room_groups = new NobodyRoomGroups();
        } else {
            $room_groups = new NobodyRoomGroups();
        }
        $group = $room_groups->getGroupContent((int)$group_id);
        $this->data = array();
        $this->ebnum = 0;
        $this->ftnum = 0;
        foreach($group as $resource_id){
            $res_obj = ResourceObject::Factory($resource_id);
            $this->data[$resource_id]['name'] = $res_obj->getName();
            $this->data[$resource_id]['seats'] = $res_obj->getSeats();
            $this->data[$resource_id]['room_data'] = $this->get_room_data($resource_id, $dow ? (int)$dow : false);
        }
        $this->groupname = $room_groups->getGroupName($group_id);
        if ($dow) {
            $this->weekday = strftime('%A', strtotime("+".(int)($dow-1)." day", strtotime('this monday')));
            $schedule = new SemGroupScheduleDayOfWeek(8, 22, $group , $this->start_time, (int)$dow);
            foreach (array_keys($this->data) as $room_to_show_id => $room_id){
                foreach($this->data[$room_id]['room_data'] as $one) {
                    if ($one['eb_id']) {
                        $name = $one['shortname'] . ':'. $one['name'];
                    } else {
                        $name = $one['instabbr'] . ' - ' . $one['name'];
                    }
                    $schedule->addEvent($room_to_show_id, $name, $one['begin'], $one['end'],
                                        '', $one['sem_doz_names']);
                }
            }
            $this->current_day = strtotime(sprintf('+%d day', $dow-1), $this->start_time);
            $this->schedule = $schedule;
            $this->render_template('show/groupschedule.php', 'printlayout.php');
        } else {
            $data = array();
            foreach ($this->data as $one_room) {
                foreach($one_room['room_data'] as $one) {
                    $one['room_name'] = $one_room['name'];
                    $data[] = $one;
                }
            }
            usort($data, function($a,$b) {
                    if ($a['begin'] == $b['begin']) {
                        return 0;
                    }
                    return ($a['begin'] < $b['begin']) ? -1 : 1;});
            $this->data = $data;
            $this->render_template('show/table_group.php', 'printlayout.php');
        }

    }

    function get_room_data($id, $dow = false)
    {
        $db = DbManager::get();
        $start_time = $this->start_time;
        $end_time = $this->end_time;
        $remaining_sem_weeks = max(array_keys($this->sem_weeks)) - $this->current_sem_week + 1;
        if ($this->only_one_week) {
            $data = array();
            $assign_events = new AssignEventList ($start_time, $end_time, $id, '', '', TRUE, 'all', $dow);
        } else {
            $data = createNormalizedAssigns($id, $this->semester['vorles_beginn'], $this->semester['vorles_ende'], false, $dow);
            $assign_events = new AssignEventList ($start_time, $end_time, $id, '', '', TRUE, 'semschedulesingle', $dow);
        }
        while ($event = $assign_events->nextEvent()) {
            //mehrtägige nur am passenden Tag anzeigen
            if ($dow !== false) {
                if (in_array($event->repeat_mode, array('sd','w','d','m','y')) && date('N', $event->begin) != $dow) continue;
            }
            $assign = AssignObject::Factory($event->getAssignId());
            $this->ebnum++;
            $data[$event->getId()] = array(
                'begin' => $event->begin,
                'end' => $event->end,
                'eb_id' => $this->only_one_week ? null : $this->ebnum,
                'shortname' => 'EB'.$this->ebnum,
                'repeat_interval' => $assign->getRepeatInterval(),
                'repeat_mode' => $assign->getRepeatMode(),
                'repeat_end' => $assign->getRepeatEnd(),
                'name' => trim($event->getUsername(true, false)),
                'owner_type' => $event->getOwnerType(),
                'owner_id' => $event->getAssignUserId()
                );
            if ($data[$event->getId()]['owner_type'] == 'date') {
                $data[$event->getId()]['seminar_id'] = Seminar::GetSemIdByDateId($data[$event->getId()]['owner_id']);
            }
        }
        unset($assign_events);
        foreach($data as $key => $values) {
            if ($values['seminar_id']) {
                $sem = new Seminar($values['seminar_id']);
                $first = array_shift($sem->getMembers('dozent'));
                $data[$key]['sem_doz_names'] = $first['Nachname'];
                $data[$key]['instabbr'] = $sem->seminar_number;
                $metadate = SeminarCycleDate::findByTermin($values['assign_user_id']);
                if ($metadate) {
                    $data[$key]['metadate_id'] = $metadate->getId();
                    $data[$key]['repeat_mode'] = $values['repeat_mode'] = 'w';
                    $data[$key]['repeat_interval'] = $values['repeat_interval'] = $metadate->cycle + 1;
                }
            }
                $add_info = '';
                switch($values['repeat_mode']){
                case 'd':
                    $add_info = ''.sprintf(_("täglich, %s bis %s"), strftime('%x',$values['begin']), strftime('%x',$values['repeat_end'])).')';
                    break;
                case 'w':
                    if($values['repeat_interval'] == 1) $add_info = ''._("wöchentlich").'';
                    else  $add_info = ''.$values['repeat_interval'].'-'._("wöchentlich").'';
                    break;
                case 'm':
                    if($values['repeat_interval'] == 1) $add_info = ''._("monatlich").'';
                    else  $add_info = ''.$values['repeat_interval'].'-'._("monatlich").'';
                    break;
                case 'y':
                    if($values['repeat_interval'] == 1) $add_info = ''._("jährlich").'';
                    else  $add_info = ''.$values['repeat_interval'].'-'._("jährlich").'';
                    break;
                default:
                    $add_info = _("einmalig");
                }
                $data[$key]['cycle'] =  $add_info;
        }
        return $data;
    }

}

