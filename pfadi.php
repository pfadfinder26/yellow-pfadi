<?php
// Pfadi extension, https://github.com/pfadfinder26/yellow-pfadi
// Based on Datenstrom Yellow, https://datenstrom.se/yellow/

class YellowPfadi {
    const VERSION = "0.12.1";
    public $yellow;         // access to API

    // Handle initialisation
    public function onLoad($yellow) {
        $this->yellow = $yellow;
        $this->yellow->system->setDefault("pfadiHalstuch", "default");
    }

    // Handle page meta data, a person says what they do and the rest follows from it:
    // "Funktion: gl, slwiwoe" leads the group and the WiWö, so the page is one of the WiWö
    // and one of the section leaders, and the card rows find it by those
    public function onParseMetaData($page) {
        if (is_string_empty($page->get("funktion"))) return;
        $stufen = $leitung = array();
        foreach ($this->getFunctions($page) as $key) {
            $stufe = $this->getFunctionSection($key);
            if (is_string_empty($stufe)) continue;
            $stufen[$stufe] = $stufe;
            if ($stufe!=$key) $leitung[$stufe] = $stufe;
        }
        if (!empty($stufen)) $page->set("stufe", implode(", ", $stufen));
        if (!empty($leitung)) $page->set("stufenleitung", implode(", ", $leitung));
    }

    // Return the functions of a person, written as one list
    public function getFunctions($page) {
        $functions = array();
        foreach (explode(",", strtoloweru($page->get("funktion"))) as $key) {
            $key = trim($key);
            if (!is_string_empty($key)) $functions[] = $key;
        }
        return $functions;
    }

    // Return the sections of a group, the key of a section is the key of a function as well
    public function getSections() {
        return array("biber"=>"Biber", "wiwoe"=>"WiWö", "gusp"=>"GuSp", "caex"=>"CaEx", "raro"=>"RaRo");
    }

    // Return the section a function belongs to, "gusp" and "slgusp" both belong to the GuSp
    public function getFunctionSection($key) {
        $sections = $this->getSections();
        if (isset($sections[$key])) return $key;
        $stufe = substru($key, 0, 2)=="sl" ? substru($key, 2) : "";
        return isset($sections[$stufe]) ? $stufe : "";
    }

    // Return what a function is called, on the card and everywhere else, in the form
    // the person asked for, "Pronomen: sie" makes the GuSp-Leiter*in a GuSp-Leiterin
    public function getFunctionName($key, $pronoun = "") {
        $sections = $this->getSections();
        if (isset($sections[$key])) {
            $name = $sections[$key]."-Leiter*in";
        } else {
            $stufe = substru($key, 0, 2)=="sl" ? substru($key, 2) : "";
            if (isset($sections[$stufe])) {
                $name = $sections[$stufe]."-Stufenleiter*in";
            } else {
                $names = array("gl"=>"Gruppenleiter*in", "ero"=>"Elternrat",
                    "kassier"=>"Kassier*in", "schriftfuehrung"=>"Schriftführung");
                $name = isset($names[$key]) ? $names[$key] : $key;
            }
        }
        return str_replace("*in", $this->getPronounEnding($pronoun), $name);
    }

    // Return the ending a person goes by, the star that says both is the one nobody has to ask for
    public function getPronounEnding($pronoun) {
        list($pronoun) = $this->yellow->toolbox->getTextList(strtoloweru(trim($pronoun)), "/", 2);
        if ($pronoun=="sie") return "in";
        if ($pronoun=="er") return "";
        return "*in";
    }

    // Return the function of a person that a card row is about, and what it is called:
    // in the GuSp row a GuSp leader, in the row of the group leaders the group leader
    public function getFunctionShown($page, $key, $value) {
        $functions = $this->getFunctions($page);
        if ($key=="stufe" || $key=="stufenleitung") {
            if ($value=="*") list($value) = $this->yellow->toolbox->getTextList($page->get($key), ",", 2);
            $value = trim($value);
            if (in_array("sl".$value, $functions)) return "sl".$value;
            if (in_array($value, $functions)) return $value;
        }
        if ($key=="funktion" && in_array($value, $functions)) return $value;
        return count($functions)!=0 ? $functions[0] : "";
    }

    // Return the address of a picture, "team/elly.png" stands in the pictures of this website,
    // "media/images/cloud/x.jpg" and an address of its own stand where they say
    public function getImageUrl($value) {
        $value = trim($value);
        if (is_string_empty($value)) return "";
        if (preg_match("#^(https?:)?//#", $value)) return $value;
        $base = $this->yellow->page->getBase();
        $location = strposu($value, "/")===0 || substru($value, 0, 6)=="media/" ?
            $base."/".ltrim($value, "/") : $base.$this->yellow->system->get("coreImageLocation").$value;
        return $this->getMediaUrl($location);
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
