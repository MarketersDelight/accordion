<style type="text/css">

/* ACCORDION */

.accordion-title:before,
.accordion-title:after {
	display: inline-block;
	font-family: md-icon;
	font-style: normal;
	font-weight: normal;
	line-height: 1;
}

.accordion-item:not(:last-child),
.accordion-item .menu-item:not(:last-child) { border-block-end: 1px solid var(--md-border); }

.accordion-title {
	align-items: center;
	color: var(--md-sidebar-title);
	cursor: pointer;
	display: flex;
	font-size: var(--md-h6);
	font-weight: var(--md-bold);
	line-height: var(--md-h6-line-height);
	padding: var(--md-half);
}

.accordion-title::marker { content: none; }

.accordion-title:after { content: '\e80e'; }

[open] > .accordion-title:after { content: '\e817'; }

.accordion-label { flex: 1; }

.accordion-label-icon {
	color: var(--md-text-muted);
	margin-inline-end: var(--md-third);
}

.accordion-item .menu-item { margin-block-end: 0; }

.accordion-content.small .menu-item a { padding-block: var(--md-third); }

.accordion-title:hover,
.accordion-item .menu-item:hover a,
.accordion-item .current-menu-item a { background-color: rgba(0, 0, 0, 0.03); }

.accordion-nested .accordion-title {
	font-size: inherit;
	line-height: inherit;
}

.accordion-nested > .accordion-item :is(.accordion-title, .menu-item a) { padding-inline-start: var(--md-single); }

/* PANEL */

.panel .widget > .accordion { margin-inline: calc(-1 * var(--md-half)); }

.panel .widget_md_accordion_widget:first-child { margin-block-start: calc(-1 * var(--md-single)); }
