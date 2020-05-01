<?php
/**
 * Plugin Name: Reproduce Core Data Issue
 * Description: A test plugin to demonstrate issues with @wordpress/core-data
 */

function core_data_issue() {
	?>
	<div id="container">
		<h1>Reproduce Issue with @wordpress/core-data</h1>
		<div id="core-data"></div>
	</div>
	<?php
}

/**
 * Add a menu for the main view, and a submenu item for the Candidates Custom Post Type.
 */
function coredata_issue() {
	add_menu_page(
		'Core data issue',
		'Core data issue',
		'edit_posts',
		'coredata_issue',
		'core_data_issue'
	);
	add_submenu_page(
		'pistachio',
		'Candidates',
		'Candidates',
		'manage_options',
		'edit.php?post_type=candidate',
		null
	);
}
add_action( 'admin_menu', 'coredata_issue' );

/**
 * Enqueue our scripts and styles.
 */
function enqueue_scripts() {
	$dependencies_file = plugin_dir_path( __FILE__ ) . '/dist/index.asset.php';
	$script_dependencies = array( 'wp-polyfill' );
	if ( file_exists( $dependencies_file ) ) {
		$asset_manifest      = include $dependencies_file;
		$script_dependencies = $asset_manifest['dependencies'];
	}

	wp_enqueue_script( 'core_data', plugin_dir_url( __FILE__ ) . 'dist/index.js', $script_dependencies );
	wp_enqueue_style( 'core_data', plugin_dir_url( __FILE__ ) . 'style.css' );
}
add_action( 'admin_enqueue_scripts', 'enqueue_scripts', 10 );

/**
 * Register the Candidate Custom Post Type.
 */
function candidate_post_type() {

	$labels = array(
		'name'           => _x( 'Candidates', 'Post Type General Name', 'pistachio' ),
		'singular_name'  => _x( 'Candidate', 'Post Type Singular Name', 'pistachio' ),
		'menu_name'      => __( 'Pistachio', 'pistachio' ),
		'name_admin_bar' => __( 'Candidates', 'pistachio' ),
		'archives'       => __( 'Candidate Archives', 'pistachio' ),
		'attributes'     => __( 'Candidate Attributes', 'pistachio' ),
		'all_items'      => __( 'All candidates', 'pistachio' ),
		'add_new'        => __( 'Add New', 'pistachio' ),
	);
	$args   = array(
		'label'               => __( 'Candidate', 'pistachio' ),
		'description'         => __( 'Greenhouse Candidate', 'pistachio' ),
		'labels'              => $labels,
		'supports'            => array( 'title' ),
		'hierarchical'        => false,
		'public'              => true,
		'publicly_queryable'  => true,
		'show_ui'             => true,
		'show_in_menu'        => false,
		'menu_position'       => 5,
		'exclude_from_search' => false,
		'searchable'          => true,
		'publicly_queryable'  => true,
		'rewrite'             => false,
		'capability_type'     => 'post',
		'show_in_rest'        => true,
		'rest_base'           => 'candidates',
	);
	register_post_type( 'candidate', $args );

	register_rest_field(
		'candidate',
		'json',
		[
			'get_callback'    =>
			function( $object = '', $field_name = '', $request = array() ) {
				return get_post_meta( $object['id'], $field_name, true );
			},
			'update_callback' =>
			function( $value, $object, $field_name ) {
				return update_post_meta( $object->ID, $field_name, $value );
			},
		]
	);

}
add_action( 'init', 'candidate_post_type' );

/**
 * Increase REST API per page value.
 *
 * @param array $params Default API paramaters.
 *
 * @return array Filtered API parameters.
 */
function change_per_page( $params ) {
	if ( isset( $params['per_page'] ) ) {
		$params['per_page']['maximum'] = 9999;
	}
	return $params;
}
add_filter( 'rest_candidate_collection_params', 'change_per_page', 10, 1 );

// Dummy data


/**
 * Import candidates from candidates.json.
 */
function import_fixtures() {
	if (get_option( 'fixtures_imported', '0' ) == '1') {
		return;
	}

	$candidates = <<<JSON
[
	{
		"application_ids": [ 1 ],
		"applications": [
			{
				"applied_at": "2018-08-13T06:34:19.993Z",
				"candidate_id": 1,
				"credited_to": null,
				"current_stage": {
					"id": 1,
					"name": "Matt Chat"
				},
				"id": 1,
				"jobs": [
					{
						"id": 2,
						"name": "Code Wrangler"
					}
				],
				"last_activity_at": "2018-08-15T06:32:00.565Z",
				"location": null,
				"prospect": false,
				"source": null,
				"status": "active"
			}
		],
		"attachments": [
			{
				"filename": "Bonita Wintheiser - Cover Letter Intro.txt",
				"type": "cover_letter",
				"url": "http://example.com/text.txt"
			},
			{
				"filename": "resume",
				"type": "resume",
				"url": "http://example.com/resume.pdf"
			}
		],
		"coordinator": null,
		"created_at": "2018-08-15T06:02:57.176Z",
		"email_addresses": [
			{
				"value": "Jaclyn_Renner@gmail.com",
				"type": "personal"
			}
		],
		"name": "Bonita Wintheiser",
		"first_name": "Bonita",
		"last_name": "Wintheiser",
		"is_private": false,
		"keyed_custom_fields": {
			"gender": {
				"name": "Gender",
				"value": "Male",
				"type": "short_text"
			},
			"git_hub_username": {
				"name": "GitHub Username",
				"value": null,
				"type": "short_text"
			},
			"job_source": null,
			"region": {
				"name": "Region",
				"value": "Asia - Pacific",
				"type": "short_text"
			},
			"slack_channel": {
				"name": "Slack Channel",
				"value": null,
				"type": "short_text"
			},
			"word_press_com_username": null
		},
		"last_activity": "2018-08-15T06:02:58.054Z",
		"updated_at": "2018-08-15T06:02:58.075Z"
	},
	{
		"application_ids": [ 1 ],
		"applications": [
			{
				"applied_at": "2018-08-13T06:34:19.993Z",
				"candidate_id": 1,
				"credited_to": null,
				"current_stage": {
					"id": 1,
					"name": "Pre-Interview Form"
				},
				"id": 1,
				"jobs": [
					{
						"id": 2,
						"name": "Code Wrangler"
					}
				],
				"last_activity_at": "2018-08-15T06:32:00.565Z",
				"location": null,
				"prospect": false,
				"source": null,
				"status": "active"
			}
		],
		"attachments": [
			{
				"filename": "Sienna Schulist - Cover Letter Intro.txt",
				"type": "cover_letter",
				"url": "http://example.com/text.txt"
			},
			{
				"filename": "resume",
				"type": "resume",
				"url": "http://example.com/resume.pdf"
			}
		],
		"coordinator": null,
		"created_at": "2018-08-15T06:02:57.176Z",
		"email_addresses": [
			{
				"value": "Quinten.Swaniawski@Colten.org",
				"type": "personal"
			}
		],
		"name": "Sienna Schulist",
		"first_name": "Sienna",
		"last_name": "Schulist",
		"is_private": false,
		"keyed_custom_fields": {
			"gender": {
				"name": "Gender",
				"value": null,
				"type": "short_text"
			},
			"git_hub_username": {
				"name": "GitHub Username",
				"value": null,
				"type": "short_text"
			},
			"job_source": null,
			"region": {
				"name": "Region",
				"value": "Asia - Pacific",
				"type": "short_text"
			},
			"slack_channel": {
				"name": "Slack Channel",
				"value": null,
				"type": "short_text"
			},
			"word_press_com_username": null
		},
		"last_activity": "2018-08-15T06:02:58.054Z",
		"updated_at": "2018-08-15T06:02:58.075Z"
	},
	{
		"application_ids": [ 1 ],
		"applications": [
			{
				"applied_at": "2018-08-13T06:34:19.993Z",
				"candidate_id": 1,
				"credited_to": null,
				"current_stage": {
					"id": 1,
					"name": "Interview"
				},
				"id": 1,
				"jobs": [
					{
						"id": 2,
						"name": "Code Wrangler"
					}
				],
				"last_activity_at": "2018-08-15T06:32:00.565Z",
				"location": null,
				"prospect": false,
				"source": null,
				"status": "active"
			}
		],
		"attachments": [
			{
				"filename": "Shad Kulas - Cover Letter Intro.txt",
				"type": "cover_letter",
				"url": "http://example.com/text.txt"
			},
			{
				"filename": "resume",
				"type": "resume",
				"url": "http://example.com/resume.pdf"
			}
		],
		"coordinator": null,
		"created_at": "2018-08-15T06:02:57.176Z",
		"email_addresses": [
			{
				"value": "Mathias_Mante@yahoo.com",
				"type": "personal"
			}
		],
		"name": "Shad Kulas",
		"first_name": "Shad",
		"last_name": "Kulas",
		"is_private": false,
		"keyed_custom_fields": {
			"gender": {
				"name": "Gender",
				"value": null,
				"type": "short_text"
			},
			"git_hub_username": {
				"name": "GitHub Username",
				"value": null,
				"type": "short_text"
			},
			"job_source": null,
			"region": {
				"name": "Region",
				"value": "Asia - Pacific",
				"type": "short_text"
			},
			"slack_channel": {
				"name": "Slack Channel",
				"value": null,
				"type": "short_text"
			},
			"word_press_com_username": "Durgan.Tierra"
		},
		"last_activity": "2018-08-15T06:02:58.054Z",
		"updated_at": "2018-08-15T06:02:58.075Z"
	}
]
JSON;

	$json = json_decode($candidates);
	foreach ( $json as $c ) {
		$post_id = wp_insert_post(
			[
				'post_title'  => $c->name,
				'post_type'   => 'candidate',
				'post_status' => 'publish',
			]
		);
		$result  = update_post_meta( $post_id, 'json', $c );
	}

	update_option('fixtures_imported', '1');
}

add_action('init', 'import_fixtures');




