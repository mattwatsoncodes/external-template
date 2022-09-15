<?php
/**
 * Template.
 *
 * @package @external-template
 */

$curl = curl_init();

/**
 * We will need a caching layer here so it does not
 * get the data on every page load.
 */
curl_setopt_array( $curl, [
	CURLOPT_URL            => $block->context['endpoint'],
	CURLOPT_RETURNTRANSFER => true,
	CURLOPT_ENCODING       => '',
	CURLOPT_MAXREDIRS      => 10,
	CURLOPT_TIMEOUT        => 0,
	CURLOPT_FOLLOWLOCATION => true,
	CURLOPT_HTTP_VERSION   => CURL_HTTP_VERSION_1_1,
	CURLOPT_CUSTOMREQUEST  => 'GET',
] );

$response = curl_exec( $curl );

$external_posts = json_decode( $response, true );

curl_close( $curl );

$classnames = '';
$wrapper_attributes = get_block_wrapper_attributes( array( 'class' => $classnames ) );

foreach ( $external_posts as $external_post ) {

	$block_instance = $block->parsed_block;

	// Render the inner blocks of the Post Template block with `dynamic` set to `false` to prevent calling
	// `render_callback` and ensure that no wrapper markup is included.
	$block_content = (
		new WP_Block(
			$block_instance,
			array(
				'post' => $external_post,
			)
		)
	)->render( array( 'dynamic' => false ) );

	// Wrap the render inner blocks in a `li` element with the appropriate post classes.
	$post_classes = implode( ' ', get_post_class( 'wp-block-post' ) );
	$content     .= '<li class="' . esc_attr( $post_classes ) . '">' . $block_content . '</li>';
}

?>

<?php // We will definitely need some escaping. ?>
<ul <?php echo $wrapper_attributes; ?>>
	<?php echo $content; ?>
</ul>


