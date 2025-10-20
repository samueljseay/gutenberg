<?php
/**
 * Plugin Name: Gutenberg Test Interactive Blocks
 * Plugin URI: https://github.com/WordPress/gutenberg
 * Author: Gutenberg Team
 *
 * @package gutenberg-test-interactive-blocks
 */
add_action(
	'init',
	function () {
		// Register all blocks found in the `interactive-blocks` folder.
		if ( file_exists( __DIR__ . '/interactive-blocks/' ) ) {
			$block_json_files = glob( __DIR__ . '/interactive-blocks/**/block.json' );

			// Auto register all blocks that were found.
			foreach ( $block_json_files as $block_json_file ) {
				register_block_type( $block_json_file );
			}

			// Manually register script modules for navigation tests.
			$test_router_script_modules = array(
				'test-router-script-modules-alpha-view-script-module',
				'test-router-script-modules-bravo-view-script-module',
				'test-router-script-modules-charlie-view-script-module',
				'test-router-script-modules-wrapper-view-script-module',
			);

			if ( version_compare( get_bloginfo( 'version' ), '6.9.0', '<' ) ) {
				foreach ( $test_router_script_modules as $module_id ) {
					gutenberg_interactive_script_modules_registry( $module_id );
				}
			} else {
				// rely on the script attributes filter
				add_filter(
					'wp_script_attributes',
					function ( $attributes ) use ( $test_router_script_modules ) {
						if ( in_array( $attributes['id'], $test_router_script_modules, true ) ) {
							$attributes['data-wp-router-options'] = wp_json_encode( array( 'loadOnClientNavigation' => true ) );
						}
						return $attributes;
					},
					10,
					2
				);
			}
		}

		/*
		 * Disable the server directive processing during E2E testing. This is
		 * required to ensure that client hydration works even when the rendered
		 * HTML contains unbalanced tags and it couldn't be processed in the server.
		 */
		if (
			isset( $_GET['disable_server_directive_processing'] ) &&
			'true' === $_GET['disable_server_directive_processing']
		) {
			// Ensure the interactivity API is loaded.
			wp_interactivity();
			// But remove the server directive processing.
			add_filter( 'interactivity_process_directives', '__return_false' );
		}
	}
);
