<?php
// Pfadi extension, https://github.com/pfadfinder26/yellow-pfadi
// Based on Datenstrom Yellow, https://datenstrom.se/yellow/

class YellowPfadi {
    const VERSION = "0.2.1";
    public $yellow;         // access to API

    // Handle initialisation
    public function onLoad($yellow) {
        $this->yellow = $yellow;
        $this->yellow->system->setDefault("pfadiHalstuch", "default");
    }

    // Handle update
    public function onUpdate($action) {
        $fileName = $this->yellow->system->get("coreExtensionDirectory").$this->yellow->system->get("coreSystemFile");
        if ($action=="install") {
            $this->yellow->system->save($fileName, array("theme" => "pfadi"));
        } elseif ($action=="uninstall" && $this->yellow->system->get("theme")=="pfadi") {
            $this->yellow->system->save($fileName, array("theme" => $this->yellow->system->getDifferent("theme")));
        }
    }
}
