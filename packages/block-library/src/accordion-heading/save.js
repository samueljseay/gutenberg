/**
 * WordPress dependencies
 */
import {
	useBlockProps,
	__experimentalGetSpacingClassesAndStyles as getSpacingClassesAndStyles,
	RichText,
} from '@wordpress/block-editor';

export default function save( { attributes } ) {
	const { level, title, iconPosition, showIcon } = attributes;
	const TagName = 'h' + ( level || 3 );

	const blockProps = useBlockProps.save();
	const spacingProps = getSpacingClassesAndStyles( attributes );

	return (
		<TagName { ...blockProps }>
			<button
				className="wp-block-accordion-heading__toggle"
				style={ spacingProps.style }
			>
				{ showIcon && iconPosition === 'left' && (
					<span
						className="wp-block-accordion-heading__toggle-icon"
						aria-hidden="true"
					>
						+
					</span>
				) }
				<RichText.Content
					className="wp-block-accordion-heading__toggle-title"
					tagName="span"
					value={ title }
				/>
				{ showIcon && iconPosition === 'right' && (
					<span
						className="wp-block-accordion-heading__toggle-icon"
						aria-hidden="true"
					>
						+
					</span>
				) }
			</button>
		</TagName>
	);
}
