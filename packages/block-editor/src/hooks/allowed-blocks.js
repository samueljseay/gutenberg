/**
 * WordPress dependencies
 */
import { addFilter } from '@wordpress/hooks';
import { hasBlockSupport } from '@wordpress/blocks';
import { useSelect } from '@wordpress/data';

/**
 * Internal dependencies
 */
import { store as blockEditorStore } from '../store';
import { PrivateInspectorControlsAllowedBlocks } from '../components/inspector-controls/groups';
import BlockAllowedBlocksControl from '../components/block-allowed-blocks/allowed-blocks-control';

function BlockEditAllowedBlocksControlPure( { clientId } ) {
	const isContentOnly = useSelect(
		( select ) => {
			return (
				select( blockEditorStore ).getBlockEditingMode( clientId ) ===
				'contentOnly'
			);
		},
		[ clientId ]
	);

	if ( isContentOnly ) {
		return null;
	}

	return (
		<PrivateInspectorControlsAllowedBlocks.Fill>
			<BlockAllowedBlocksControl clientId={ clientId } />
		</PrivateInspectorControlsAllowedBlocks.Fill>
	);
}

export default {
	edit: BlockEditAllowedBlocksControlPure,
	attributeKeys: [ 'allowedBlocks' ],
	hasSupport( name ) {
		return hasBlockSupport( name, 'allowedBlocks' );
	},
};

/**
 * Filters registered block settings, extending attributes with allowedBlocks.
 *
 * @param {Object} settings Original block settings.
 *
 * @return {Object} Filtered block settings.
 */
export function addAttribute( settings ) {
	// Allow blocks to specify their own attribute definition with default values if needed.
	if ( settings?.attributes?.allowedBlocks?.type ) {
		return settings;
	}
	if ( hasBlockSupport( settings, 'allowedBlocks' ) ) {
		// Gracefully handle if settings.attributes is undefined.
		settings.attributes = {
			...settings.attributes,
			allowedBlocks: {
				type: 'array',
			},
		};
	}

	return settings;
}

addFilter(
	'blocks.registerBlockType',
	'core/allowedBlocks/attribute',
	addAttribute
);
