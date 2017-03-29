<?php
require_once "lib/resources/resourcesFunc.inc.php";
require_once "lib/resources/lib/list_assign.inc.php";
require_once "lib/resources/lib/RoomGroups.class.php";
require_once "lib/resources/lib/AssignEventList.class.php";
if (!class_exists('NobodyRoomGroups')) {
        require "NobodyRoomGroups.class.php";
}

class RoomPlanPlugin extends StudipPlugin implements SystemPlugin {

    public $config = array();

    function __construct() {

        parent::__construct();
        $this->me = get_class($this);
        $this->restoreConfig();
        if ($GLOBALS['user']->id !== 'nobody') {
            $n1 = new Navigation($this->getDisplayTitle());
            $n1->setURL(PluginEngine::getURL("$this->me/show"));
            $nstart = clone $n1;
            $n1->setTitle('Semesterpläne');
            $n2 = new Navigation('Wochenpläne');
            $n2->setURL(PluginEngine::getURL("$this->me/show/index_nobody"));
            $n3 = new Navigation('Wochenenden');
            $n3->setURL(PluginEngine::getURL("$this->me/show/weekend"));
            Navigation::addItem("/$this->me", $n1);
            Navigation::addItem("/$this->me/show", clone $n1);
            Navigation::addItem("/$this->me/show_week", $n2);
            Navigation::addItem("/$this->me/show_weekend", $n3);
            Navigation::insertItem("/start/{$this->me}start", $nstart, "search");
        } elseif ($GLOBALS['user']->id == 'nobody') {
            $n2 = new Navigation('Wochenpläne');
            $n2->setURL(PluginEngine::getURL("$this->me/show/index_nobody"));
            $n3 = new Navigation('Wochenenden');
            $n3->setURL(PluginEngine::getURL("$this->me/show/weekend"));
            Navigation::addItem("/$this->me", $n2);
            Navigation::addItem("/$this->me/show_week", clone $n2);
            Navigation::addItem("/$this->me/show_weekend", $n3);
        }
    }

    function getDisplayTitle(){
        return _("Raumpläne drucken");
    }

    function restoreConfig(){
        $config = DBManager::get()
        ->query("SELECT comment FROM config WHERE field = 'CONFIG_" . $this->getPluginName() . "' AND is_default=1")
        ->fetchColumn();
        $this->config = unserialize($config);
        return $this->config != false;
    }

    function storeConfig(){
        $config = serialize($this->config);
        $field = "CONFIG_" . $this->getPluginName();
        $st = DBManager::get()
        ->prepare("REPLACE INTO config (config_id, field, value, is_default, type, range, chdate, comment)
            VALUES (?,?,'do not edit',1,'string','global',UNIX_TIMESTAMP(),?)");
        return $st->execute(array(md5($field), $field, $config));
    }

    /**
    * This method dispatches and displays all actions. It uses the template
    * method design pattern, so you may want to implement the methods #route
    * and/or #display to adapt to your needs.
    *
    * @param  string  the part of the dispatch path, that were not consumed yet
    *
    * @return void
    */
    function perform($unconsumed_path) {
        if(!$unconsumed_path){
            header("Location: " . PluginEngine::getUrl($this), 302);
            return false;
        }
        $trails_root = $this->getPluginPath();
        $dispatcher = new Trails_Dispatcher($trails_root, null, 'show');
        $dispatcher->current_plugin = $this;
        $dispatcher->dispatch($unconsumed_path);

    }

}
