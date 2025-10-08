/**
 * WordPress dependencies
 */
import { MenuItem } from '@wordpress/components';
import { __ } from '@wordpress/i18n';
import { comment as commentIcon } from '@wordpress/icons';

import { privateApis as blockEditorPrivateApis } from '@wordpress/block-editor';

/**
 * Internal dependencies
 */
import { unlock } from '../../lock-unlock';

const { CommentIconSlotFill } = unlock( blockEditorPrivateApis );

const AddCommentMenuItem = ( { onClick } ) => {
	return (
		<CommentIconSlotFill.Fill>
			{ ( { onClose } ) => (
				<MenuItem
					icon={ commentIcon }
					onClick={ () => {
						onClick();
						onClose();
					} }
					aria-haspopup="dialog"
				>
					{ __( 'Comment' ) }
				</MenuItem>
			) }
		</CommentIconSlotFill.Fill>
	);
};

export default AddCommentMenuItem;
