<?
function wpb_initialize_cmb_meta_boxes() {
	if ( ! class_exists( 'cmb_Meta_Box' ) )
		require_once dirname( __FILE__ ) . '/init.php';
}

add_action( 'init', 'wpb_initialize_cmb_meta_boxes', 9999 );

//Add Meta Boxes

function wpb_sample_metaboxes( $meta_boxes ) {
	$prefix = '_wpb_'; // Prefix for all fields

	$meta_boxes[] = array(
		'id' => 'product_info',
		'title' => 'Product Information',
		'pages' => array('page'), // post type
		'context' => 'normal',
		'priority' => 'high',
		'show_names' => true, // Show field names on the left
		'fields' => array(
			array(
				'name' => 'Product Price',
				'desc' => 'Insert the products price',
				'id' => $prefix . 'product_money',
				'type' => 'text_money'
			),
			array(
				'name' => 'Product Shipping',
				'desc' => 'Shipping price, only enter a price if product is not a Digital Download.',
				'id' => $prefix . 'product_shipping',
				'type' => 'text_money'
			),
			array(
				'name' => 'Product Stock',
				'desc' => 'Product Stock, only enter a price if product is not a Digital Download.',
				'id' => $prefix . 'product_stock',
				'type' => 'text_small'
			),
			array(
				'name'    => 'Digital Download',
				'desc'    => 'Please select if this is a digital download or not',
				'id'      => $prefix . 'digital_download',
				'type'    => 'select',
				'options' => array(
					array( 'name' => 'Yes', 'value' => 'yes', ),
					array( 'name' => 'No', 'value' => 'no', ),
				),
			),
			array(
				'name' => 'Product File',
				'desc' => 'Enter file name including the file extention: download.pdf',
				'id'   => $prefix . 'product_download',
				'type' => 'text_small',
			),
			array(
				'name' => 'Product Image',
				'desc' => 'Upload an image or enter an URL.',
				'id'   => $prefix . 'product_image',
				'type' => 'file',
			),
		),
	);

	return $meta_boxes;
}
add_filter( 'cmb_meta_boxes', 'wpb_sample_metaboxes' );
?>