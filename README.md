# Accordion

A [Marketers Delight](https://marketersdelight.com/) drop-in that adds an accordion navigation widget. It lists the terms of a taxonomy (categories by default) as collapsible sections, each showing its latest posts and a link to the full archive. Child terms nest inside their parents.

## Features

- **MD → Accordion Nav** widget for any sidebar or widget area
- Works with categories, tags or any public custom taxonomy. It can also auto-detect the taxonomy from the current post type.
- Nested accordions for child terms, or parent terms only
- Posts per term, newest first, plus a "See more" link to each term's archive
- Limit how many terms show, with a "See all" link to the post type archive
- Order terms by their latest post, so recently updated terms come first
- Can open the current page's term: on a term archive or single post, that term and its parents move to the top and open. Pages without a matching term open nothing.
- Highlights the current post in the list
- Optional post counts per term
- Shows nothing at all, title included, when no term has posts to list
- Post filter: only list posts that share the current page's term in another taxonomy, such as the docs for a product page's category. Filtered lists show every matching post, and a lone category opens. Pages without a matching term show nothing.
- Styles are added to MD's compiled stylesheet, with extra spacing rules for MD panels

## Settings

All settings are in the widget form (Appearance → Widgets):

- **Title**
- **Category type**: the taxonomy to list, or auto-detect
- **List direction**: ascending (default) or descending
- **Order by**: name (default), posts count, slug, ID, term order or latest post (newest first; Descending puts the oldest first)
- **Posts per category**: default 5
- **Categories to show**: blank shows all. When more exist, a **See all** link to the post type archive follows the list.
- **Exclude**: comma-separated term IDs
- **Only show posts that share the page's**: Off, or the taxonomy to match the current page's term in
- **Open the current page's category**
- **Show parent categories only**
- **Show post count per category**
- **See more text**: supports `{count}` and `{category}` (default: "See more in **{category}** →")
- **CSS classes**: extra classes on the accordion wrapper

## Developers

- No hooks or filters. Markup uses `.accordion`, `.accordion-item`, `.accordion-title`, `.accordion-content` and `.accordion-nested` classes.

## Requirements

The Accordion widget shipped inside the Marketers Delight theme from MD 5.2 until it moved to this drop-in.

- Marketers Delight 6.0 or later
- WordPress 6.2 or later
- PHP 7.4 or later

## Install

1. Download the latest `accordion-x.y.z.zip` from the [Releases page](https://github.com/MarketersDelight/accordion/releases).
2. In WordPress, go to MD's Drop-ins screen, click **Add new**, and upload the zip.
3. Activate Accordion, then add the **MD → Accordion Nav** widget to a widget area.

Don't use **Code → Download ZIP**. That zip includes development files and a folder name that MD won't recognize.

## License

GPL-2.0-or-later. See [LICENSE](LICENSE).
