# App Tailor API
Wordpress plugin that exposes website data as custom components. Works with Gutenberg blocks and freeform HTML content.
The primary purpose of these custom components is to facilitate the consumption of website content by native mobile
applications, allowing for the rendering of website content in a manner consistent with its appearance on the website.

## Features
- Expose website data as custom components.
- Compatible with Gutenberg blocks.
- Compatible with freeform HTML (wysiwyg) content.
- Designed for consumption by native mobile applications.
- Enables rendering of website content consistent with its appearance on the website.

## Description
To expose the website data as custom components the plugin extends the WordPress REST API `posts` and `pages` endpoints
with a new `app` context, in addition to existing `edit`, `view` and `embed` contexts. When this context is applied as
a query parameter to the REST API endpoint requests, the plugin will hook into the request and modify the response to
return custom components. The custom components are made according to the Gutenberg blocks and are as follows:
* `core/paragraph` - `Paragraph`
* `core/heading` - `Heading`
* `core/separator` - `Separator`
* `core/image` - `Image`
* `core/columns` - `Columns`
* `core/list` - `Listing`
* `core/buttons` - `Buttons`
* `core/pullquote` - `Pullquote`
* `core/embed` - `Embed`

To extend the list of supported blocks the plugin provides a custom hook named `app_tailor_content_parser`.
For example, the following code section will integrate a new `custom/podcast` block with the existing blocks:
```php
function set_podcasts_block_parser( array $mapping ): array {
	if ( class_exists( Abstract_Parser::class ) ) {
		$mapping['custom/podcast'] = Podcast::get_instance();
	}

	return $mapping;
}
add_filter( 'app_tailor_content_parser', 'set_podcasts_block_parser' );
```

## Installation
1. Upload the `app-tailor-api` folder to the `wp-content/plugins` directory.
2. Run `composer install`.
3. Activate the plugin through the 'Plugins' menu in WordPress.

## How to use
After the plugin is installed and activated, request the `posts` or `pages` endpoints with the `context=app`
query parameter to get dat as custom components. E.g.
```
http://localhost/wp-json/wp/v2/posts?context=app
http://localhost/wp-json/wp/v2/posts/{postId}?context=app
```

The main class responsible for making custom components from data is `AT_API\V1\Content\Content_Transformer`.
The following code segment will return a post content as custom components:
```php
use AT_API\V1\Content\Content_Transformer;

$transformer = new Content_Transformer();
$result      = $transformer->transform( $post->post_content );
```

## Get in Touch
If you need consulting for development, mobile app creation, or just have a question, feel free to contact us. Visit our website at novembit.com or email us at info@novembit.com. We're here to help you with all your development needs!
