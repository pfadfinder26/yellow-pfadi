<?php
// Pfadi extension, https://github.com/pfadfinder26/yellow-pfadi
// Based on Datenstrom Yellow, https://datenstrom.se/yellow/

class YellowPfadi {
    const VERSION = "0.3.0";
    public $yellow;         // access to API

    // Handle initialisation
    public function onLoad($yellow) {
        $this->yellow = $yellow;
        $this->yellow->system->setDefault("pfadiHalstuch", "default");
        $this->yellow->language->setDefault("PfadiPages", "Pages", "en");
        $this->yellow->language->setDefault("PfadiPages", "Seiten", "de");
        $this->yellow->language->setDefault("PfadiExit", "Leave editing", "en");
        $this->yellow->language->setDefault("PfadiExit", "Bearbeiten beenden", "de");
    }

    // Handle page extra data, the rail with the editing buttons and the page tree
    public function onParsePageExtra($page, $name) {
        if ($name!="footer" || !$this->isEditable()) return null;
        $output = "<div class=\"editrail\" id=\"editrail\">\n";
        $output .= "<input class=\"editrail-toggle\" type=\"checkbox\" id=\"editrail-toggle\" />\n";
        $output .= "<label class=\"editrail-item editrail-expand\" for=\"editrail-toggle\">".
            $this->yellow->language->getTextHtml("pfadiPages")."</label>\n";
        $output .= "<div class=\"editrail-actions\"></div>\n";
        $output .= "<div class=\"editrail-tree\">\n".$this->getTreeHtml($this->yellow->content->getRootLocation($page->location), $page)."</div>\n";
        $output .= "<a class=\"editrail-item editrail-exit\" href=\"#\" data-action=\"submit\" data-arguments=\"action:logout\">".
            $this->yellow->language->getTextHtml("pfadiExit")."</a>\n";
        $output .= "</div>\n";
        return $output;
    }

    // Return page tree HTML, the unlisted pages are shown too
    public function getTreeHtml($location, $page) {
        $pages = $this->yellow->content->getChildren($location, true);
        if (count($pages)==0) return "";
        $output = "<ul>\n";
        foreach ($pages as $pageTree) {
            $class = array();
            if ($pageTree->isActive()) $class[] = "active";
            if (!$pageTree->isVisible()) $class[] = "unlisted";
            $output .= "<li><a".(count($class) ? " class=\"".implode(" ", $class)."\"" : "").
                " href=\"".$pageTree->getLocation(true)."\">".$pageTree->getHtml("title")."</a>";
            $output .= $this->getTreeHtml($pageTree->getLocation(), $page);
            $output .= "</li>\n";
        }
        return $output."</ul>\n";
    }

    // Check if the website can be edited right now
    public function isEditable() {
        return $this->yellow->extension->isExisting("edit") && $this->yellow->extension->get("edit")->editable;
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
