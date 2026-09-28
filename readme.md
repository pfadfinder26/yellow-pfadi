# Pfadi 0.4.5

Pfadi is a theme for scout group websites in the PPÖ design. Designed by Liam Perlaki.

It follows the [official PPÖ website template](https://das-ppoe.at/website/) and the group websites built with it: red header bar, full width banner with the group scarf below it, Kumbh Sans, centered headings in PPÖ red `#9d2632`, grey bands, white cards, red footer. Works on phones and desktops, no JavaScript, no external requests.

## How to install an extension

[Download ZIP file](https://github.com/pfadfinder26/yellow-pfadi/archive/refs/heads/main.zip) and copy it into your `system/extensions` folder. [Learn more about extensions](https://github.com/annaesvensson/yellow-update).

## How to use the theme

**Logo:** upload your group logo as `media/images/logo.png`, it shows up in the red header bar. Use
the white version of your PPÖ lockup, it already carries the group name, so the site name is hidden
for the eye and kept for screen readers. Without a logo the site name is shown as text.

**Banner:** a page with a `Banner` setting gets a full width image below the header, followed by the
scarf strip. Several images, separated by commas, become a carousel: it can be swiped on a phone and
scrolled with the mouse, and `pfadi.js` moves it along every six seconds, pausing while a visitor
touches or hovers it. It loops: the script appends a copy of the first image, and once the scroll
ends on that copy it silently sets the position back to the start, so the carousel keeps going
forward instead of rewinding. Without JavaScript it stays a carousel to swipe, under
`prefers-reduced-motion` it stops moving on its own:

    ---
    Title: Unsere Gruppe
    Banner: media/images/lager.jpg, media/images/sola.jpg
    Subtitle: Seit 1954 in Beispieldorf
    ---

**Scarf:** below the banner hangs a group scarf, the theme draws a generic blue one. `PfadiHalstuch`
in `system/extensions/yellow-system.ini` decides for the whole website, a `Halstuch` setting on a
page decides for that page:

| Value | What you get |
|:------|:-------------|
| `default` | the scarf drawn by the theme, this is the default |
| `none` | no scarf, the banner ends at the content |
| `halstuch.png` | your own scarf from `media/images` |

A photo of your own scarf is cut like the one in the [PPÖ template](https://das-ppoe.at/website/):
wide and flat, transparent below the tips. Pages without a banner never show a scarf.

**Band:** a full width grey section, for the parts of a page that should stand out. No HTML needed,
Yellow's Markdown makes blocks with a `!` at the start of the line:

    ! {.band}
    ! ## Aktuelles
    ! Was bei uns los ist.

**Cards:** a grid of white boxes, for sections, news or contacts. One `!` per nesting level, an empty
block line separates the cards. They wrap into one column on phones:

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

**Navigation:** the top level pages are the menu. A page with subpages gets a dropdown that fades in on hover, on phones the subpages start collapsed and slide open when the arrow next to the page is tapped. The subpages of the page you are on start open.

**Cards from pages:** writing the same facts twice is no fun. With the [Cards extension](https://github.com/pfadfinder26/yellow-cards) a card is generated from another page and its settings, `[cards /stufen/ stufe]` makes one card per section page. This theme styles `.cards` and `.card`, the card markup comes from your own template.

**Factsheet:** a card template can put a round section button next to a list of facts, `.factsheet`
with an image and a `<dl>`, side by side on wide screens, stacked on phones.

**Line breaks:** `.nowrap` on a piece of text keeps it together, so `Mittwoch,` breaks but
`19:00 bis 20:30 Uhr` does not.

**Cards as links:** `.card-link` with a `.stretch` link inside makes the whole card clickable, the
link covers the card without changing how anything looks. Other links in the card, a mail address
for example, stay on top and keep working.

**Footer:** the footer text page starts with a paragraph across the full width, then a
`! {.footer-row}` block with `! ! {.footer-col}` blocks inside it becomes a row of columns, logo,
social buttons, address, like the group websites do it.

**File lists:** `.files` puts name, type, size and date in aligned columns, `.files-folder` is a
folder that folds open, its name is plain text because it is not a link.

**Calendar:** a list of dates puts its columns on one grid, so date, title, calendar and link line
up across all rows, and stacks on phones. A month view is a table of weeks, `.calendar-month`, with
today outlined and the events as small labels in their calendar color.

**Month links:** `.calendar-pagination` above a month view is styled as buttons, previous month on
the left, next on the right, back to this month in the middle.

**Calendar labels:** `.calendar-name` shows which calendar a date comes from, and the subscribe
links below a list use the same look. The color comes from the calendar itself, as the custom
properties `--calendar-color` and `--calendar-text`.

**Social links:** a link with `{.social.instagram}`, `{.social.facebook}` or `{.social.mail}`
becomes a square button with that icon, in the color of the text around it, so it works in the red
footer and in the content. An icon is an SVG mask, to add one put the SVG next to the others and
write three lines of CSS like the ones under `/* Social links */`.

**Section buttons:** the round button of a section is drawn as SVG from whatever you pass it, so it
always says what the page says:

    <?php $this->yellow->layout("stufe-button", "Guides & Späher", "10 bis 13 Jahre", "gusp") ?>

Name, age and section: the name breaks into two lines at an `&` or an `und` like the printed
buttons, the size follows the longest line, and the color comes from the section. Call it from a
layout, not from a page, because Yellow removes SVG from content. In a card or another piece of
content leave a placeholder instead, it is replaced by the button of that page:

    <span data-stufe-button="/stufen/gusp/"></span>

`banner` is a layout of its own too, so a layout of your own can show the same banner as the
default one.

**Editing:** with the [edit extension](https://github.com/annaesvensson/yellow-edit) installed, an
editor gets a rail at the right edge of the window instead of the bar at the top: it stays in place
while scrolling, holds the editing buttons and a button that leaves editing without logging out,
and expands into a page tree that shows the unlisted pages too. The rail remembers whether it was
expanded, branches of the tree can be collapsed, and a page in the tree shows four buttons when the
mouse is over it: edit it, add a page, show or hide it, delete it. The window that edits a page is
centred in the space next to the rail and stays open when something next to it is clicked.

**Blog entries:** with the [blog extension](https://github.com/annaesvensson/yellow-blog) the
`Image` of an entry stands above its title.

**Announcements:** `Layout: ausschreibung` lays a page out like a printed announcement of a camp,
and a blog entry with a setting `Ausschreibung` is laid out the same way, so an announcement stands
in the news like every other entry:
the title and the `Termin` of the camp above the text, the image of the camp beside the text, and a
definition list as the two columns that say where, when and what it costs. The image of the camp
goes into the page itself, right before that list, `![](camp.jpg){.ausschreibung-image}`, so it
stands beside it. A block `{.closing}`
holds the lines at the end, a block `{.note}` the small print below them.

**News as cards:** the list of blog entries shows the same cards as an overview elsewhere, with
the image, the date and the description of an entry. `BlogPaginationLimit` decides how many fit on
one page.

**News that waits:** in the list of blog entries an entry whose `Published` date is still ahead is
left out, until that day. While someone is editing the website it is shown, marked as waiting, so an
announcement can be written now and go online by itself.

**Printing:** printed, a page drops the header, the navigation and the footer, and an announcement
gets the letterhead that belongs on paper: the logo of the group on the left, the shared page
`briefkopf` on the right. The logo of the header is white, an `images/logo-print.png` is used for
the letterhead when there is one.

**Portraits:** an image with `class="portrait"` in a card template is shown as a round avatar, for cards of people.

**Section labels:** `{.stufe.biber}`, `{.stufe.wiwoe}`, `{.stufe.gusp}`, `{.stufe.caex}`,
`{.stufe.raro}` and `{.stufe.pwa}` turn a heading into a colored label, in the official PPÖ section
colors (#904837, #fbbb21, #159a34, #0b4697, #e62336, #e6007e).

**Buttons:** `{.button}` after a link.

**Header and footer text:** the pages `content/shared/header.md` and `content/shared/footer.md` are
shown below the site name and in the red footer. Footer headings become columns on wide screens.

**Footer links:** `content/shared/footerlinks.md` fills the right side of the dark bottom line, next
to the copyright. One line of links, separated by a middle dot, for the pages nobody wants in the
menu and for services elsewhere:

    [Impressum](/impressum/) · [Datenschutz](/datenschutz/) · [Cloud](https://cloud.example.org)

Give those legal pages `Status: unlisted` in their page settings, then they stay out of the
navigation but the footer link still works.

The last link in that line can open the editor for the page a visitor is looking at, `[edit - Edit]`
from the [Edit extension](https://github.com/annaesvensson/yellow-edit). Signed out it leads to the
login, so leaders find their way in from any page.

## How to customise a theme

You can customise the appearance of your website with HTML and CSS. All HTML files are stored in your `system/layouts` folder. All CSS files are stored in your `system/themes` folder. Colors and fonts are CSS variables at the top of `pfadi.css`, page-specific rules belong at the bottom under `/* Custom */`. [Learn more about HTML and CSS](https://datenstrom.se/yellow/help/how-to-customise-html-and-css).

The default theme is defined in file `system/extensions/yellow-system.ini`. A different theme can be defined in the [page settings](https://github.com/annaesvensson/yellow-core#settings-page) at the top of each page, for example `Theme: pfadi`.

## Acknowledgements

This extension includes Kumbh Sans by the Kumbh Sans project authors. Thank you for the beautiful font.

The handwriting is [Gloria Hallelujah](https://fonts.google.com/specimen/Gloria+Hallelujah) by
Kimberly Geswein, the second typeface of the PPÖ design next to Kumbh Sans. Thank you as well.

The social icons are [Font Awesome 6 Free](https://fontawesome.com/), `fa6-brands/instagram`,
`fa6-brands/facebook-f` and `fa6-solid/envelope`, taken from [Iconify](https://iconify.design/) and
used under [CC BY 4.0](https://creativecommons.org/licenses/by/4.0/). They are files in this theme,
nothing is loaded from another server.

The favicon and `pfadi-lilie.png` are the official PPÖ lily, the mark of the Pfadfinder und
Pfadfinderinnen Österreichs. Use them on a website of a PPÖ group, not anywhere else.

The PPÖ logo and the section logos are not part of this theme, ask your group for the files.

Do you have questions? [Get help](https://datenstrom.se/yellow/help/).
