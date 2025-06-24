=== Accessibility Toolkit ===
Contributors: apos37
Tags: accessibility, alt text, screen reader, WCAG, a11y
Requires at least: 5.9
Tested up to: 6.8
Requires PHP: 7.4
Stable tag: 1.0.1
License: GPLv2 or later
License URI: http://www.gnu.org/licenses/gpl-2.0.txt

Practical accessibility enhancements and diagnostic tools for WordPress. Includes admin tools for alt text, headings, contrast, vague links, and visual enhancements like dark mode and greyscale.

== Description ==
**Accessibility Toolkit** provides a dual approach to accessibility improvements in WordPress: practical diagnostic tools for admins and editors, and optional front-end visual enhancements for users.

This plugin is designed to complement the WAVE browser extension by WebAIM by offering tools that WAVE doesn’t cover or that we wanted to improve on. For a more complete accessibility review, using both is recommended.

**Features:**
- **Skip to Content Link:** Automatically inserts a visually hidden "Skip to main content" link at the top of each page for improved keyboard navigation.
- **Alt Text Column & Inline Editing:** Adds a sortable “Alt Text” column to the Media Library list view, including an edit button for quickly updating missing or incorrect image alt text.
- **Additional Media Columns:** Adds sortable columns for image dimensions, MIME type (e.g. `image/png`, `application/zip`), and file size.
- **Accessibility Admin Bar Tools:** Adds a front-end admin bar menu with toggleable visual checks for accessibility issues:
  - Missing Alt Text
  - Poor Color Contrast (AA/AAA)
  - Vague Link Text (e.g. “click here”)
  - Improper Heading Hierarchy (e.g. skipping from H2 to H4)
  - Links Missing Underlines (excluding buttons and navs)
- **Frontend Mode Switcher:** Adds an accessibility mode switcher for Dark Mode, and Greyscale, optionally placed as:
  - A floating toggle
  - A navigation menu item
  - A shortcode (`[a11ytoolkit_modes]`)
- **Logo Swap in Dark Mode:** Optionally swap logos when dark mode is enabled.
- **Custom Visibility Rules:** Choose who can see the frontend mode switcher — everyone, logged-in users, or just admins.
- **WCAG AAA Option:** Enforce stricter AAA color contrast checks when using the visual contrast tool.
- **Custom Vague Phrases:** Configure your own list of vague link texts to scan for (e.g. “read more, learn more, click here”).

Accessibility Toolkit gives you clear, actionable insights directly in the WordPress UI to improve accessibility compliance faster.

== Installation ==
1. Upload the plugin files to the `/wp-content/plugins/accessibility-toolkit/` directory.
2. Activate the plugin through the 'Plugins' menu in WordPress.
3. Visit the Media Library list view to see the new accessibility columns and editing tools.

== Frequently Asked Questions ==
= Does this plugin automatically make my site accessible? =
No — Accessibility Toolkit is not a one-click solution. It offers some very basic enhancements and provides tools that help you identify and resolve common accessibility issues more efficiently.

= Can I edit alt text in bulk? =
Currently, you can quickly edit alt text one-by-one directly in the Media Library table. A bulk editor is planned for a future release.

= Does it detect decorative images? =
Not yet, but future versions will include a toggle to mark images as decorative by setting empty alt text and/or ARIA roles.

= Will more accessibility tools be added? =
Yes. This plugin is being actively developed. Planned features include a heading audit tool, skip link injector, focus visualizer, and contrast checker.

= Where can I request features and get further support? =
We recommend using our [website support forum](https://pluginrx.com/support/plugin/accessibility-toolkit/) as the primary method for requesting features and getting help. You can also reach out via our [Discord support server](https://discord.gg/3HnzNEJVnR) or the [WordPress.org support forum](https://wordpress.org/support/plugin/accessibility-toolkit/), but please note that WordPress.org doesn’t always notify us of new posts, so it’s not ideal for time-sensitive issues.

== Demo ==
https://youtu.be/61dk4iydfDA

== Screenshots ==
1. Alt text column and inline editor in Media Library list view.
2. Additional metadata columns for image dimensions, type, and file size.

== Changelog ==
= 1.0.1 =
* Initial Release on June 18, 2025