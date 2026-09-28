<?php
// Pfadi extension, https://github.com/pfadfinder26/yellow-pfadi
// Based on Datenstrom Yellow, https://datenstrom.se/yellow/

class YellowPfadi {
    const VERSION = "0.11.0";
    public $yellow;         // access to API
    public $number;         // number of the page in the tree

    // Handle initialisation
    public function onLoad($yellow) {
        $this->yellow = $yellow;
        $this->yellow->system->setDefault("pfadiHalstuch", "default");
    }











    // Return the address of a media file with the time it was changed, so a new file
    // under an old name is fetched again instead of taken from the browser
    public function getMediaUrl($location) {
        if (is_string_empty($location)) return "";
        $base = $this->yellow->system->get("coreServerBase");
        $path = substru($location, 0, strlenu($base))==$base ? substru($location, strlenu($base)) : $location;
        $fileName = ltrim($path, "/");
        if (!is_file($fileName)) return $location;
        return $location."?v=".filemtime($fileName);
    }

    // Return a mail address as a link, written the way the markdown parser writes one:
    // most characters as decimal or hex entities, so the simple harvesters find nothing
    public function getMailHtml($email, $text = "") {
        $email = trim($email);
        if (is_string_empty($email)) return "";
        if (is_string_empty($text)) $text = $email;
        return "<a href=\"".$this->getMailObfuscated("mailto:".$email)."\">".
            ($text==$email ? $this->getMailObfuscated($text) : htmlspecialchars($text))."</a>";
    }

    // Return a phone number as a link, hidden the same way as a mail address:
    // the markdown parser leaves a number as it is, a harvester reads both alike
    public function getPhoneHtml($number, $text = "") {
        $number = trim($number);
        if (is_string_empty($number)) return "";
        if (is_string_empty($text)) $text = $number;
        // "+43 (0)664 ..." is dialled without the zero in brackets
        $dial = preg_replace("/[^\d\+]/", "", preg_replace("/\(.*?\)/", "", $number));
        return "<a href=\"".$this->getMailObfuscated("tel:".$dial)."\">".
            $this->getMailObfuscated($text)."</a>";
    }

    // Return a text with most characters as entities, the same mix the parser uses
    public function getMailObfuscated($text) {
        $output = "";
        $seed = intval(abs(crc32($text)/max(1, strlenb($text))));
        for ($number = 0; $number<strlenb($text); ++$number) {
            $char = substrb($text, $number, 1);
            $ord = ord($char);
            if ($ord<128) {
                $random = ($seed*(1+$number))%100;
                if ($random>90 && strposb("@\"&>", $char)===false) {
                    $output .= $char;
                } elseif ($random<45) {
                    $output .= "&#x".dechex($ord).";";
                } else {
                    $output .= "&#".$ord.";";
                }
            } else {
                $output .= $char;
            }
        }
        return $output;
    }


    // Handle page content element, a phone number that is written in a page
    public function onParseContentElement($page, $name, $text, $attributes, $type) {
        if ($name!="phone" || ($type!="block" && $type!="inline")) return null;
        list($number, $label) = $this->yellow->toolbox->getTextList($text, " ", 2);
        $number = trim($text);
        if (is_string_empty($number)) return $this->getErrorHtml("Please add a phone number!");
        $output = $this->getPhoneHtml($number);
        return $type=="block" ? "<p>".$output."</p>\n" : $output;
    }

    // Return error message for authors
    public function getErrorHtml($text) {
        return "<p class=\"error\">Pfadi: ".htmlspecialchars($text)."</p>\n";
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
