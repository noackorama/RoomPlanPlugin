<?
# Lifter002: TODO
# Lifter007: TODO
# Lifter003: TODO
/**
* RoomGroups.class.php
*
* class for a grouping of rooms
*
*
* @author       André Noack <noack@data-quest.de>, Suchi & Berg GmbH <info@data-quest.de>
* @access       public
* @modulegroup      resources
* @module        RoomGroups.class.php
* @package      resources
*/

// +---------------------------------------------------------------------------+
// This file is part of Stud.IP
// RoomGroups.class.php
//
// Copyright (C) 2005 André Noack <noack@data-quest.de>, Suchi & Berg GmbH <info@data-quest.de>
// +---------------------------------------------------------------------------+
// This program is free software; you can redistribute it and/or
// modify it under the terms of the GNU General Public License
// as published by the Free Software Foundation; either version 2
// of the License, or any later version.
// +---------------------------------------------------------------------------+
// This program is distributed in the hope that it will be useful,
// but WITHOUT ANY WARRANTY; without even the implied warranty of
// MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.  See the
// GNU General Public License for more details.
// You should have received a copy of the GNU General Public License
// along with this program; if not, write to the Free Software
// Foundation, Inc., 59 Temple Place - Suite 330, Boston, MA  02111-1307, USA.
// +---------------------------------------------------------------------------+

class NobodyRoomGroups {

    private $groups = array();
    private $viewable_property_id = 'c223ff0f31fbdd0e978dafc9a989d2d9';

    function __construct(){
        $this->createNobodyGroups();
    }

    function createNobodyGroups(){
        $db = DBManager::get();
        $res_obj = ResourceObject::Factory();
        $offset = count($this->groups);

        /*$filterfunc = create_function('$a', 'return $a != "Standort Halle";');
        $rs = $db->query("SELECT DISTINCT parent_id,resource_id
            FROM resources_objects LEFT JOIN resources_categories USING (category_id)
            LEFT JOIN resources_objects_properties USING(resource_id)
            WHERE is_room=1 AND property_id='$this->viewable_property_id' AND state='on' ORDER BY resources_objects.name");
        $rs = $db->query("SELECT DISTINCT parent_id,resources_objects.resource_id
            FROM resources_objects LEFT JOIN resources_categories USING (category_id)
            LEFT JOIN resources_objects_properties ON resources_objects_properties.resource_id=resources_objects.resource_id AND property_id='$this->viewable_property_id'
            WHERE is_room=1 AND state IS NULL ORDER BY resources_objects.name");*/
         $rs = $db->query("SELECT DISTINCT parent_id,resources_objects.resource_id
            FROM resources_objects LEFT JOIN resources_categories USING (category_id)
            WHERE is_room=1 ORDER BY resources_objects.name");
        foreach($rs->fetchAll(PDO::FETCH_COLUMN|PDO::FETCH_GROUP) as $parent_id => $resource_ids){
            if (is_array($resource_ids) && count($resource_ids)){
                $res_obj->restore($parent_id);
                //$path = array_filter(array_reverse(array_values($res_obj->getPathArray($include_self))), $filterfunc);
                $path = array_reverse(array_values($res_obj->getPathArray($include_self)));
                $this->groups[$offset]['name'] = join("/", $path);
                foreach ($resource_ids as $resource_id){
                    $res_obj->restore($resource_id);
                    $this->groups[$offset]['resources'][] = $resource_id;
                }
                ++$offset;
            }
        }
        if (count($this->groups)) {
            usort($this->groups, create_function('$a,$b', 'return strnatcasecmp($a["name"], $b["name"]);'));
        }
    }

    function getGroupName($id){
        return (isset($this->groups[$id]) ? $this->groups[$id]['name'] : false);
    }

    function getGroupContent($id){
        return (isset($this->groups[$id]) ? $this->groups[$id]['resources'] : array());
    }

    function getGroupCount($id){
        return count($this->getGroupContent($id));
    }

    function getAvailableGroups(){
        return array_keys($this->groups);
    }

    function isGroup($id){
        return array_key_exists($id, $this->groups);
    }

}
?>
