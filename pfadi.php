<?php
// Pfadi extension, https://github.com/pfadfinder26/yellow-pfadi
// Based on Datenstrom Yellow, https://datenstrom.se/yellow/

class YellowPfadi {
    const VERSION = "0.2.5";
    public $yellow;         // access to API

    // Handle initialisation
    public function onLoad($yellow) {
        $this->yellow = $yellow;
        $this->yellow->system->setDefault("pfadiHalstuch", "default");
    }

    // Handle page content in HTML format, draw the buttons left as placeholders
    // the content filter removes SVG, so the button is drawn after that
    public function onParseContentHtml($page, $text) {
        if (strposu($text, "data-stufe-button")===false) return null;
        return preg_replace_callback("/<span data-stufe-button=\"(.*?)\"><\/span>/", function ($matches) {
            $pageButton = $this->yellow->content->find(html_entity_decode($matches[1], ENT_QUOTES, "UTF-8"));
            if (is_null($pageButton)) return "";
            ob_start();
            $this->yellow->layout("stufe-button", $pageButton->get("title"),
                $pageButton->get("alter"), $pageButton->get("stufe"));
            return ob_get_clean();
        }, $text);
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
