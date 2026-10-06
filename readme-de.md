# Pfadi 0.12.2

Pfadi ist ein Theme für Websites von Pfadfindergruppen im PPÖ-Design. Gestaltet von Liam Perlaki.

Es orientiert sich an der [offiziellen PPÖ-Website-Vorlage](https://das-ppoe.at/website/) und an den Gruppenwebsites, die damit gebaut sind: roter Kopfbalken, Banner über die volle Breite mit dem Halstuch darunter, Kumbh Sans, zentrierte Überschriften in PPÖ-Rot `#9d2632`, graue Bänder, weiße Karten, roter Fußbereich. Funktioniert am Handy und am Desktop, ohne JavaScript, ohne externe Anfragen.

## Wie man eine Erweiterung installiert

[ZIP-Datei herunterladen](https://github.com/pfadfinder26/yellow-pfadi/archive/refs/heads/main.zip) und in den Ordner `system/extensions` kopieren. [Mehr über Erweiterungen](https://github.com/annaesvensson/yellow-update).

## Wie man das Theme verwendet

**Logo:** das Gruppenlogo als `media/images/logo.png` hochladen, es erscheint im roten Kopfbalken.
Am besten die weiße Fassung des PPÖ-Logos, sie enthält den Gruppennamen schon, deshalb wird der
Seitenname fürs Auge ausgeblendet und für Screenreader behalten. Ohne Logo steht der Seitenname als
Text da.

**Banner:** eine Seite mit der Einstellung `Banner` bekommt ein großes Bild unter dem Kopfbalken und
darunter das Halstuch. Mehrere Bilder, mit Komma getrennt, werden zu einem Karussell: am Handy
wischbar, am Desktop scrollbar, und `pfadi.js` schaltet alle sechs Sekunden weiter und pausiert,
solange jemand darauf zeigt oder tippt. Es läuft im Kreis: das Skript hängt eine Kopie des ersten
Bildes an und setzt die Position stillschweigend zurück an den Anfang, sobald das Scrollen auf dieser
Kopie endet, so geht es immer vorwärts weiter statt zurückzuspulen. Ohne JavaScript bleibt es ein
Karussell zum Wischen, bei `prefers-reduced-motion` bewegt es sich nicht von selbst:

    ---
    Title: Unsere Gruppe
    Banner: media/images/lager.jpg, media/images/sola.jpg
    Subtitle: Seit 1954 in Beispieldorf
    ---

**Halstuch:** unter dem Banner hängt ein Gruppenhalstuch, das Theme zeichnet ein allgemeines blaues.
`PfadiHalstuch` in `system/extensions/yellow-system.ini` entscheidet für die ganze Website, eine
Einstellung `Halstuch` auf einer Seite entscheidet für diese Seite:

| Wert | Ergebnis |
|:-----|:---------|
| `default` | das gezeichnete Halstuch des Themes, Voreinstellung |
| `none` | kein Halstuch, das Banner endet beim Inhalt |
| `halstuch.png` | das eigene Halstuch aus `media/images` |

Ein Foto des eigenen Halstuchs ist zugeschnitten wie in der
[PPÖ-Vorlage](https://das-ppoe.at/website/): breit und flach, unter den Spitzen transparent. Seiten
ohne Banner zeigen nie ein Halstuch.

**Band:** ein grauer Abschnitt über die volle Breite, für Teile einer Seite, die auffallen sollen.
Ganz ohne HTML, das Markdown von Yellow macht Blöcke mit `!` am Zeilenanfang:

    ! {.band}
    ! ## Aktuelles
    ! Was bei uns los ist.

**Karten:** ein Raster aus weißen Kacheln, für Stufen, Neuigkeiten oder Kontakte. Ein `!` pro
Verschachtelungsebene, eine leere Blockzeile trennt die Karten. Am Handy steht alles untereinander:

    ! {.band}
    ! ## Unsere Stufen
    !
    ! ! {.cards}
    ! ! ! {.card}
    ! ! ! ### WiWö {.stufe.wiwoe}
    ! ! ! Freitag 17:00 bis 18:15
    ! ! !
    ! ! ! [Mehr](stufen/){.button}
    ! !
    ! ! ! {.card}
    ! ! ! ### GuSp {.stufe.gusp}
    ! ! ! Freitag 18:30 bis 20:00

**Navigation:** die obersten Seiten sind das Menü. Eine Seite mit Unterseiten bekommt ein Klappmenü. Wo es eine Maus gibt, blendet es ein, sobald der Zeiger auf der Seite steht, und ein Klick führt direkt auf die Seite. Wo es keine Maus gibt, auf einem Tablet mit dem breiten Menü, klappt der erste Tipp auf die Seite das Menü auf, statt dem Link zu folgen, der zweite führt auf die Seite, und der Pfeil daneben klappt es wieder zu. Ein Tipp neben dem Menü schließt es auch, die Escape-Taste ebenso. Am Handy sind die Unterseiten zugeklappt und fahren am selben Pfeil auf. Die Unterseiten der aktuellen Seite sind schon offen.

**Karten aus Seiten:** dieselben Angaben zweimal schreiben macht keine Freude. Mit der [Cards-Erweiterung](https://github.com/pfadfinder26/yellow-cards) entsteht eine Karte aus einer anderen Seite und deren Einstellungen, `[cards /stufen/ stufe]` macht eine Karte pro Stufenseite. Dieses Theme gestaltet `.cards` und `.card`, das HTML der Karte kommt aus der eigenen Vorlage.

**Steckbrief:** eine Kartenvorlage kann einen runden Stufenbutton neben eine Liste von Angaben
stellen, `.factsheet` mit einem Bild und einer `<dl>`, am breiten Bildschirm nebeneinander, am Handy
untereinander.

**Zeilenumbruch:** `.nowrap` hält einen Textteil zusammen, `Mittwoch,` bricht um,
`19:00 bis 20:30 Uhr` nicht.

**Karten als Link:** `.card-link` mit einem `.stretch`-Link darin macht die ganze Karte anklickbar,
der Link legt sich über die Karte, ohne dass sich das Aussehen ändert. Andere Links in der Karte,
etwa eine Mailadresse, bleiben obenauf und funktionieren weiter.

**Fußbereich:** die Fußzeilenseite beginnt mit einem Absatz über die volle Breite, danach wird ein
Block `! {.footer-row}` mit `! ! {.footer-col}`-Blöcken darin zu einer Reihe von Spalten, Logo,
Social-Buttons, Adresse, so wie es die Gruppenwebsites machen.

**Dateilisten:** `.files` stellt Name, Typ, Größe und Datum in ausgerichtete Spalten,
`.files-folder` ist ein Ordner zum Auf- und Zuklappen, sein Name steht als normaler Text da, weil er
kein Link ist.

**Kalender:** eine Terminliste legt ihre Spalten auf ein gemeinsames Raster, Datum, Titel, Kalender
und Link stehen also über alle Zeilen hinweg untereinander, am Handy untereinander gestapelt. Die
Monatsansicht ist eine Wochentabelle, `.calendar-month`, mit umrandetem heutigen Tag und den
Terminen als kleine Schildchen in ihrer Kalenderfarbe.

**Monatslinks:** `.calendar-pagination` über der Monatsansicht sieht aus wie Buttons, voriger Monat
links, nächster rechts, zurück zu diesem Monat in der Mitte.

**Kalenderschildchen:** `.calendar-name` zeigt, aus welchem Kalender ein Termin kommt, die
Abo-Links unter einer Liste sehen gleich aus. Die Farbe kommt aus dem Kalender selbst, als die
Eigenschaften `--calendar-color` und `--calendar-text`.

**Social-Links:** ein Link mit `{.social.instagram}`, `{.social.facebook}` oder `{.social.mail}`
wird zu einem quadratischen Button mit diesem Zeichen, in der Farbe des Textes ringsum, das passt im
roten Fußbereich wie im Inhalt. Ein Icon ist eine SVG-Maske, für ein weiteres die SVG-Datei dazu
legen und drei Zeilen CSS schreiben wie die unter `/* Social links */`.

**Stufenbuttons:** der runde Button einer Stufe wird als SVG gezeichnet, aus dem, was man ihm gibt,
und sagt damit immer dasselbe wie die Seite:

    <?php $this->yellow->layout("stufe-button", "Guides & Späher", "10 bis 13 Jahre", "gusp") ?>

Name, Alter und Stufe: der Name bricht bei einem `&` oder `und` auf zwei Zeilen wie bei den
gedruckten Buttons, die Größe richtet sich nach der längsten Zeile, die Farbe kommt von der Stufe.
Aufrufen lässt er sich aus einem Layout, nicht aus einer Seite, weil Yellow SVG aus dem Inhalt
entfernt. In einer Karte oder anderem Inhalt setzt man stattdessen einen Platzhalter, an seine
Stelle kommt der Button dieser Seite:

    <span data-stufe-button="/stufen/gusp/"></span>

Auch `banner` ist ein eigenes Layout, ein eigenes Seitenlayout kann also dasselbe Banner zeigen
wie das Standardlayout.

**Blogeinträge:** mit der [Blog-Erweiterung](https://github.com/annaesvensson/yellow-blog) steht
das `Image` eines Eintrags über seinem Titel.

**Ausschreibungen:** `Layout: ausschreibung` setzt eine Seite wie eine gedruckte Ausschreibung,
und ein Blogeintrag mit einer Einstellung `Ausschreibung` wird genauso gesetzt, damit eine
Ausschreibung wie jeder andere Eintrag in den Neuigkeiten steht:
Titel und `Termin` des Lagers über dem Text und eine Definitionsliste
als die zwei Spalten, die Ort, Zeit und Kosten nennen. Das `Image` der Seite steht neben dieser
Liste, das Layout setzt es dorthin, damit es einmal genannt wird und auch auf den Karten
erscheint. Ein Bild mit `class="ausschreibung-image"` in der Seite selbst hat Vorrang.
Ein Block `{.closing}` trägt die Zeilen am
Ende, ein Block `{.note}` das Kleingedruckte darunter. Ganz unten stehen Datum, Autor*innen und
Schlagwörter der Seite, klein und in die Neuigkeiten verlinkt, und nie auf dem Papier.

Eine Vorlage zum Schreiben ist eine geteilte Seite `page-new-ausschreibung`, eine Ausschreibung
trägt `LayoutNew: ausschreibung`, damit die nächste, die von ihr aus begonnen wird, dort anfängt.

**Reihen, die scrollen:** eine Kartenreihe mit der Option `scroll` wird zu einer Reihe, die seitlich
scrollt, mit einem runden Knopf an jeder Seite. Die Knöpfe halten an beiden Enden, sie springen
nicht zurück, und sie verschwinden, wenn ohnehin alles hineinpasst. Eine Reihe beginnt bei der
ersten Karte ohne `entry-scheduled`, Karten, die auf ihr Datum warten, stehen also links davon. Am
schmalen Bildschirm stehen die beiden Knöpfe unter der Reihe statt auf den Karten.

**Neuigkeiten als Karten:** die Liste der Blogeinträge zeigt dieselben Karten wie eine Übersicht
an anderer Stelle, mit Bild, Datum und Beschreibung eines Eintrags. `BlogPaginationLimit` bestimmt,
wie viele auf eine Seite passen.

**Neuigkeiten, die warten:** in der Liste der Blogeinträge fehlt ein Eintrag, dessen
`Published`-Datum noch bevorsteht, bis zu diesem Tag. Wer die Website bearbeitet, sieht ihn
trotzdem, als wartend gekennzeichnet: eine Ausschreibung lässt sich also jetzt schreiben und geht
von selbst online.

**Der Termin einer Ausschreibung:** verlinkt ein Termin im Kalender auf eine Ausschreibung, zeigt
die Seite einen Knopf, der diesen Termin speichert, aus der
[Kalender-Erweiterung](https://github.com/pfadfinder26/yellow-calendar) und nur dann, wenn ein
Kalender die Seite wirklich nennt.

**Formulare:** ein Formular der
[Cloudforms-Erweiterung](https://github.com/pfadfinder26/yellow-cloudforms) steht in einem eigenen
Rahmen, die Frage links, die Antwort rechts, alle Antworten beginnen an derselben Stelle, und gedruckt wird es zu dem Zettel, den es ersetzt: auf einer eigenen Seite, darüber
Briefkopf, Titel und Termin des Lagers, die Felder als Linien und Kästchen zum Ausfüllen, ohne den
Knopf zum Abschicken, aber mit dem Link, der es in der Cloud öffnet.

Die Ränder des Papiers gehören zur Seite, nicht zu `@page`, damit bleibt dem Browser kein Platz,
seine eigene Adresse und das Datum hineinzudrucken.

**Drucken:** gedruckt lässt eine Seite Kopf, Navigation und Fußbereich weg, und eine Ausschreibung
bekommt den Briefkopf, der aufs Papier gehört: links das Logo der Gruppe, rechts die geteilte Seite
`briefkopf`. Das Logo im Kopf ist weiß, für den Briefkopf wird `images/logo-print.png` genommen,
wenn es die Datei gibt.

**Mailadressen:** `getMailHtml` dieser Erweiterung schreibt eine Adresse als Link, so wie der
Markdown-Parser sie schreibt, die meisten Zeichen als dezimale oder hexadezimale Entities. Eine
Kartenvorlage ruft das auf, statt selbst `mailto:` zu schreiben, damit eine Adresse aus einem Layout
genauso schwer zu ernten ist wie eine, die in einer Seite steht. Das hält die groben Sammler auf,
nicht einen entschlossenen: wirklich aus den Listen bleibt nur eine Adresse, die man nicht
veröffentlicht, mit einem Formular an ihrer Stelle.

**Telefonnummern:** der Markdown-Parser versteckt Mailadressen, eine Telefonnummer lässt er stehen.
`getPhoneHtml` versteckt sie genauso, und in einer Seite schreibt man `[phone +43 664 1234567]`.
Eine Zahl in Klammern, die Null von `+43 (0)664`, gehört nicht zur gewählten Nummer.

**Das Logo der Gruppe:** eine Datei `images/logo-gruppe.svg` steht links vom Logo des Verbands, im
Kopf der Seite als weiße Maske, auf dem Papier in ihren eigenen Farben. Ohne die Datei ändert sich
nichts.

**Banner:** das ganze Bild ist zu sehen, nie ein Ausschnitt. Es ist höchstens 800 Pixel hoch, nie
höher als das halbe Fenster, und so breit wie das Fenster, ein Bild im Verhältnis **1,6 : 1**, etwa
2560 × 1600, füllt das Banner einer Seite von 1280 also genau. Ein Bild, das höher ist als das
Banner, steht zwischen zwei Balken in der Farbe des Bandes, ein breiteres macht das Banner einfach
niedriger und die Seite beginnt weiter oben.

**Bilder, die sich ändern:** das Theme schreibt die Änderungszeit einer Datei in ihre Adresse,
`gruppe.jpg?v=1790609511`, damit ein neues Bild unter altem Namen geholt und nicht aus dem
Zwischenspeicher des Browsers genommen wird.

**Bearbeiten:** mit der [Editrail-Erweiterung](https://github.com/pfadfinder26/yellow-editrail)
bekommen Redakteur*innen eine Leiste am Rand des Fensters, in den Farben dieses Themes.

**Porträts:** ein Bild mit `class="portrait"` in einer Kartenvorlage wird als runder Avatar gezeigt, für Karten von Personen.

**Was eine Person macht:** die Seite einer Person trägt eine Einstellung, `Funktion`, eine Liste
dessen, was sie macht: eine Stufe, `biber`, `wiwoe`, `gusp`, `caex`, `raro`; die Leitung einer
Stufe, dasselbe mit `sl` davor; dazu `gl` für die Gruppenleitung und `ero` für den Elternrat. Alles
andere folgt daraus. Das Theme schreibt `Stufe` und `Stufenleitung` in die Seite, `[cards …
stufe:gusp]` und `[cards … stufenleitung:*]` finden sie also, und es sagt, wie eine Funktion heißt,
„GuSp-Leiter*in“ und „GuSp-Stufenleiter*in“. Eine Karte zeigt die Funktion, um die es in der Reihe
geht, dieselbe Person ist in der einen Reihe also Gruppenleitung und in der nächsten Leitung der
WiWö. `Pronomen` sagt, wie eine Person genannt werden möchte, `sie` oder `er`; ohne Angabe trägt das
Schild den Stern, der beides sagt, „GuSp-Leiter*in“. `Rolle` schreibt das Schild anders, für
jemanden, dessen Aufgabe einen eigenen Namen hat.

**Stufenschilder:** `{.stufe.biber}`, `{.stufe.wiwoe}`, `{.stufe.gusp}`, `{.stufe.caex}`,
`{.stufe.raro}` und `{.stufe.pwa}` machen aus einer Überschrift ein farbiges Schild in den offiziellen
PPÖ-Stufenfarben (#904837, #fbbb21, #159a34, #0b4697, #e62336, #e6007e).

**Buttons:** `{.button}` hinter einen Link schreiben.

**Kopf- und Fußzeilentext:** die Seiten `content/shared/header.md` und `content/shared/footer.md`
erscheinen unter dem Seitennamen und im roten Fußbereich. Überschriften im Fußbereich werden am
breiten Bildschirm zu Spalten.

**Fußzeilenlinks:** `content/shared/footerlinks.md` füllt die rechte Seite der dunklen Fußzeile,
neben dem Copyright. Eine Zeile Links, getrennt durch einen Mittelpunkt, für die Seiten, die niemand
im Menü haben will, und für Dienste woanders:

    [Impressum](/impressum/) · [Datenschutz](/datenschutz/) · [Cloud](https://cloud.example.org)

Diesen Rechtsseiten in den Seiteneinstellungen `Status: unlisted` geben, dann bleiben sie aus dem
Menü und der Link in der Fußzeile funktioniert trotzdem.

Der letzte Link in dieser Zeile kann den Editor für die gerade gezeigte Seite öffnen,
`[edit - Bearbeiten]` aus der [Edit-Erweiterung](https://github.com/annaesvensson/yellow-edit).
Abgemeldet führt er zur Anmeldung, so kommen Leiter*innen von jeder Seite aus hinein.

## Wie man ein Theme anpasst

Das Aussehen der Website lässt sich mit HTML und CSS anpassen. Alle HTML-Dateien liegen im Ordner `system/layouts`, alle CSS-Dateien im Ordner `system/themes`. Farben und Schriften sind CSS-Variablen am Anfang von `pfadi.css`, eigene Regeln gehören ans Ende unter `/* Custom */`. [Mehr über HTML und CSS](https://datenstrom.se/yellow/help/how-to-customise-html-and-css).

Das Standard-Theme steht in der Datei `system/extensions/yellow-system.ini`. Ein anderes Theme kann in den [Seiteneinstellungen](https://github.com/annaesvensson/yellow-core#settings-page) am Anfang jeder Seite stehen, zum Beispiel `Theme: pfadi`.

## Danksagungen

Diese Erweiterung enthält Kumbh Sans von den Kumbh-Sans-Projektautoren. Danke für die schöne Schrift.

Die Handschrift ist [Gloria Hallelujah](https://fonts.google.com/specimen/Gloria+Hallelujah) von
Kimberly Geswein, die zweite Schrift des PPÖ-Designs neben Kumbh Sans. Auch dafür danke.

Die Social-Icons sind [Font Awesome 6 Free](https://fontawesome.com/), `fa6-brands/instagram`,
`fa6-brands/facebook-f` und `fa6-solid/envelope`, geholt über [Iconify](https://iconify.design/) und
verwendet unter [CC BY 4.0](https://creativecommons.org/licenses/by/4.0/). Sie liegen als Dateien im
Theme, es wird nichts von einem anderen Server geladen.

Das Favicon und `pfadi-lilie.png` sind die offizielle PPÖ-Lilie, das Zeichen der Pfadfinder und
Pfadfinderinnen Österreichs. Sie gehören auf die Website einer PPÖ-Gruppe, sonst nirgendwohin.

Das PPÖ-Logo und die Stufenlogos sind nicht Teil des Themes, die Dateien gibt es bei der eigenen Gruppe.

Hast du Fragen? [Hier gibt's Hilfe](https://datenstrom.se/yellow/help/).
