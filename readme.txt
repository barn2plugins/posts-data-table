=== Posts Table with Search & Sort ===
Contributors: andykeith, barn2media
Donate link: http://barn2.co.uk
Tags: posts, table, tables, shortcode, search, sort, wpml
Requires at least: 3.0.1
Tested up to: 4.8.1
Stable tag: 1.1
License: GPLv2 or later
License URI: http://www.gnu.org/licenses/gpl-2.0.html

A simple plugin to display all your posts in a searchable and sortable table or list.

== Description ==

Posts Table with Search & Sort provides an easy way to list all of your site's posts in a searchable and sortable data table.
Simply add the shortcode `[posts_data_table]` to any page.

It uses the [jQuery DataTables](http://datatables.net/) plugin to provide the searching and sorting features, as well as pagination and
responsive layouts for smaller screens.

> **[Posts Table Pro](https://barn2.co.uk/wordpress-plugins/posts-table-pro/) now available, with lots more features including:**
>
> *   Support for for pages and custom post types (e.g. courses, products, staff, music, books, etc)
> *   Featured images
> *   Custom taxonomies, terms and posts tags
> *   Custom fields & support for Advanced Custom Fields
>
> **[WooCommerce Product Table](https://barn2.co.uk/wordpress-plugins/woocommerce-product-table/) now available - create tables of products from your WooCommerce store:**
>
> * Include Add to Cart buttons, quantity, price, reviews, stock level, categories, tags, weight, dimensions, and more!

Translations currently provided for French, Spanish and German (more to follow).

It's compatible with WPML which means that, if you're using this, posts will be shown for the current language only.

There are a few options available with the shortcode:

*   `columns` - the columns you'd like to show in your table. This can be any of the following columns, given as a comma-separated list:
id, title, content, category, tags, author, and date. Defaults to 'title,content,date,author,category'
*   `rows_per_page` - the number of posts to show on each page of results in the table. Set to 'false' to disable pagination. Defaults to 20 rows.
*   `category` - restrict the table to this category only. Use the category ID or 'slug' here, NOT the name of the category. You can find the slug from the
Posts -> Categories menu in the WordPress admin.
*   `tag` - restrict the table to this tag only. Use the tag 'slug' or ID here. You can find the slug from the Posts -> Tags menu.
*   `sort_by` - the column to sort by. Defaults to 'date'. If the column you want to sort by isn't shown in the table, it will be added as a hidden column.
This means, for example, that you can sort by date without actually showing the date column.
*   `sort_order` - whether to sort ascending ('asc') or descending ('desc'). If you order by date, it will default to 'desc' (newest posts first).
*   `search_on_click` - whether to enable automatic searching for categories and authors when clicking on links in the table. Default: true
*   `wrap` - whether the table content wraps onto more than one line. Set to 'false' to keep everything on one line or 'true' to allow the content to wrap. Default: true
*   `content_length` - the number of words of post content to show in the table (if you've included the 'content' column). Defaults to 15 words.
*   `scroll_offset` - advanced: the table scrolls back to the top each time you navigate forward or backwards through the list of posts. This
value controls the 'offset' for the scroll. For example, if your site uses a sticky header you can adjust the scroll amount here to compensate.
Enter a whole number (e.g. 50) or set to 'false' to disable scrolling to top.

### Examples

The following will list your posts in a table with 3 columns (title, content and date columns) showing the first 10 words of each post in the content column:

`[posts_data_table columns='title,content,date' content_length="10"]`

The following will list posts with 4 columns (ID, title, tags, and author columns), and will be sorted by date in ascending order (oldest posts first):

`[posts_data_table columns='id,title,tags,author' sort_by="date" sort_order="asc"]`

### Demo

Please see [the demo](https://barn2.co.uk/posts-table/) for examples of the plugin in action.

== Installation ==

1. Upload the plugin files to the `/wp-content/plugins/posts-data-table` directory, or install the plugin through the WordPress plugins screen directly.
1. Activate the plugin through the 'Plugins' screen in WordPress
1. Add the shortcode `[posts_data_table]` to any page

== Frequently Asked Questions ==

= How do I display the posts table? =
Simply add the shortcode `[posts_data_table]` to any page.

= Does it show all posts or can I restrict it to a certain category? =
By default it will list all of your posts, but you can use the 'category' option in the shortcode to restrict the table to that category only.

= What are the shortcode options? =
See the main [plugin description](https://wordpress.org/plugins/posts-data-table/) for the list of options.

= Can I see a demo of the plugin? =
Yes, please visit https://barn2.co.uk/posts-table/ to see the posts table in action.

= Will the posts table work with my theme? =
The plugin has been designed to work with different WordPress themes and will take the styling from your theme for the fonts etc. where possible.
If any parts don't match your site as well as you would like, you can restyle it using CSS or [contact us](https://barn2.co.uk/contact-a-wordpress-designer/)
about our customization service.

= Does the posts table work with custom post types? =
No, it only displays standard Posts at the moment. Our [Pro Version](https://barn2.co.uk/wordpress-plugins/posts-table-pro/) supports custom post types, as well
as taxonomies, custom fields, and much more.

= Can I change the width of the columns? =
The column widths are calculated automatically by the plugin, based on the contents of each column. However you can override this for one (or more) columns
by setting an exact width. You would need to add some code to your theme (or in a custom plugin) to do this. The filter to hook into is 'posts_data_table_column_default'.
Here's an example setting the title column to 80px;

`
add_filter( 'posts_data_table_column_defaults', 'pdt_change_posts_table_defaults' );

function change_posts_table_defaults( $column_defaults ) {
    $column_defaults['title']['width'] = '80px';
    return $column_defaults;
}
`

Bear in mind that the plugin might still override your column width if there isn't enough room for the data it contains, or the rest of the columns in the table.

= Does it work on mobiles/tablets? =
Yes, the table will automatically adapt to fit different screen sizes. If your table has too many columns to fit on smaller screens then a '+' icon will
appear alongside each post, allowing you to click to view the hidden columns.

= When I click to the next page on my posts list, I can't see the top of the table =
This is probably because you have a sticky header (your header sticks to the top of the screen when you scroll down). This means it's covering the
top of your posts table. You can add a 'scroll offset' to push the table down to prevent this from happening. For example, if your sticky header
is 50 pixels high then use `[posts_data_table scroll_offset="50"]`

= How do I use the posts table with WPML? =
If you have a multilingual site using WPML then the plugin will display your posts in the correct language automatically.

= Can you customize the plugin for me? =
We have developed this free plugin to be flexible and easy to configure so that it will be suitable for many different websites. If you would
like us to modify the plugin to suit your exact requirements, please [contact us](https://barn2.co.uk/contact-a-wordpress-designer/) with the
details and we'll be happy to provide a quote.

== Screenshots ==

1. Posts sorted by date and with wrapping disabled. Categories are collapsed automatically to fit data on single line.
2. Different column order and with wrapping enabled (default).
3. Filtered by post author and sorted alphabetically by title (ascending).

== Changelog ==

= 1.1 =
7 September 2017

 * Added support for a new 'tags' column, to display the post tags in the table.
 * Added a new shortcode option 'tag' to allow posts to be restricted by tag.
 * Added a 'date_format' shortcode option to allow the date format to be set. See [PHP date formats](http://php.net/manual/en/function.date.php) for examples.
 * Category can now be specified by ID or slug, depending on whether a numeric or text value is specified.
 * Fix bug with sort_by option when column not present in table.
 * Code restructure and improvement.
 * Update DataTables library to 1.10.15.

= 1.0.6 =
 * Added Greek translation (credit: Yofis Florentin)
 * Added additional language locales for French/German

= 1.0.5 =
* Upgrade to DataTables 1.10.12
* Allow column default (priorities, widths, etc) to be set per table.
* Prevent conflict if both free and Pro version of plugin are activated.

= 1.0.4 =
Fix bug with localization of main javascript file.

= 1.0.3 =
Fix a bug with the search and replace of column data, which produced invalid post URLs in some instances.

= 1.0.2 =
Added 'category' option to allow table to show posts from a single category only.

= 1.0.1 =
* Changed default for 'wrap' so content will now wrap onto multiple lines by default.
* Changed default conent length to 15 words.
* Additional FAQs.

= 1.0 =
Initial release.


