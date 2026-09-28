<?php
// Pfadi extension, https://github.com/pfadfinder26/yellow-pfadi
// Based on Datenstrom Yellow, https://datenstrom.se/yellow/

class YellowPfadi {
    const VERSION = "0.3.4";
    public $yellow;         // access to API
    public $number;         // number of the page in the tree

    // Handle initialisation
    public function onLoad($yellow) {
        $this->yellow = $yellow;
        $this->yellow->system->setDefault("pfadiHalstuch", "default");
        $this->yellow->language->setDefault("PfadiPages", "Pages", "en");
        $this->yellow->language->setDefault("PfadiPages", "Seiten", "de");
        $this->yellow->language->setDefault("PfadiExit", "Leave editing", "en");
        $this->yellow->language->setDefault("PfadiExit", "Bearbeiten beenden", "de");
        $this->yellow->language->setDefault("PfadiCreate", "New page", "en");
        $this->yellow->language->setDefault("PfadiCreate", "Neue Seite", "de");
        $this->yellow->language->setDefault("PfadiDelete", "Delete page", "en");
        $this->yellow->language->setDefault("PfadiDelete", "Seite löschen", "de");
        $this->yellow->language->setDefault("PfadiEditPage", "Edit page", "en");
        $this->yellow->language->setDefault("PfadiEditPage", "Seite bearbeiten", "de");
        $this->yellow->language->setDefault("PfadiHidePage", "Hide page", "en");
        $this->yellow->language->setDefault("PfadiHidePage", "Seite verstecken", "de");
        $this->yellow->language->setDefault("PfadiShowPage", "Show page", "en");
        $this->yellow->language->setDefault("PfadiShowPage", "Seite zeigen", "de");
    }

    // Handle page extra data, the rail with the editing buttons and the page tree
    public function onParsePageExtra($page, $name) {
        if ($name!="footer" || !$this->isEditable()) return null;
        $this->number = 0;
        $output = "<div class=\"editrail\" id=\"editrail\"".
            " data-label-create=\"".$this->yellow->language->getTextHtml("pfadiCreate")."\"".
            " data-label-delete=\"".$this->yellow->language->getTextHtml("pfadiDelete")."\">\n";
        $output .= "<input class=\"editrail-toggle\" type=\"checkbox\" id=\"editrail-toggle\" />\n";
        $output .= "<label class=\"editrail-item editrail-expand\" for=\"editrail-toggle\">".
            $this->yellow->language->getTextHtml("pfadiPages")."</label>\n";
        $output .= "<div class=\"editrail-actions\"></div>\n";
        $output .= "<div class=\"editrail-tree\">\n".
            $this->getTreeHtml($this->yellow->content->getRootLocation($page->location))."</div>\n";
        $output .= "<a class=\"editrail-item editrail-exit\" href=\"".$this->getLocationPlain($page)."\">".
            $this->yellow->language->getTextHtml("pfadiExit")."</a>\n";
        $output .= "</div>\n";
        return $output;
    }

    // Return page tree HTML, the unlisted pages are shown too
    public function getTreeHtml($location) {
        $pages = $this->yellow->content->getChildren($location, true);
        if (count($pages)==0) return "";
        $output = "<ul>\n";
        foreach ($pages as $pageTree) {
            $treeHtml = $this->getTreeHtml($pageTree->getLocation());
            $id = "editrail-branch-".(++$this->number);
            $output .= "<li>";
            if (!is_string_empty($treeHtml)) {
                $output .= "<input class=\"editrail-branch\" type=\"checkbox\" id=\"".$id."\" checked />";
            }
            $output .= "<span class=\"editrail-page\">";
            if (!is_string_empty($treeHtml)) {
                $output .= "<label class=\"editrail-twisty\" for=\"".$id."\" aria-hidden=\"true\"></label>";
            }
            $class = array("editrail-title");
            if ($pageTree->isActive()) $class[] = "active";
            if (!$pageTree->isVisible()) $class[] = "unlisted";
            $output .= "<a class=\"".implode(" ", $class)."\" href=\"".$pageTree->getLocation(true)."\">".
                $pageTree->getHtml("title")."</a>";
            $output .= $this->getToolsHtml($pageTree);
            $output .= "</span>";
            $output .= $treeHtml;
            $output .= "</li>\n";
        }
        return $output."</ul>\n";
    }

    // Return the buttons of a page in the tree, they show up on hover
    public function getToolsHtml($pageTree) {
        $output = "<span class=\"editrail-tools\">";
        // the button that shows or hides a page says which of the two it is now
        $status = $pageTree->isVisible() ? "status" : "status-hidden";
        $tools = array("edit" => "pfadiEditPage", "create" => "pfadiCreate",
            $status => $pageTree->isVisible() ? "pfadiHidePage" : "pfadiShowPage",
            "delete" => "pfadiDelete");
        foreach ($tools as $tool=>$text) {
            $text = $this->yellow->language->getTextHtml($text);
            $action = $tool=="status-hidden" ? "status" : $tool;
            $output .= "<a class=\"editrail-tool editrail-tool-".$tool."\" href=\"".
                htmlspecialchars($pageTree->get("editPageUrl"))."#pfadi-".$action."\"".
                " title=\"".$text."\" aria-label=\"".$text."\"></a>";
        }
        return $output."</span>";
    }

    // Return the page location without the editing prefix
    public function getLocationPlain($page) {
        return $this->yellow->lookup->normaliseUrl(
            $this->yellow->system->get("coreServerScheme"),
            $this->yellow->system->get("coreServerAddress"),
            $this->yellow->system->get("coreServerBase"), $page->location);
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
