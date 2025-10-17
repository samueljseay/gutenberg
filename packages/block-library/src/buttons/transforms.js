/**
 * WordPress dependencies
 */
import { createBlock } from '@wordpress/blocks';
import { __unstableCreateElement as createElement } from '@wordpress/rich-text';

/**
 * Internal dependencies
 */
import { getTransformedAttributes } from '../utils/get-transformed-attributes';

const transforms = {
	from: [
		{
			type: 'block',
			isMultiBlock: true,
			blocks: [ 'core/button' ],
			transform: ( buttons ) =>
				// Creates the buttons block.
				createBlock(
					'core/buttons',
					{},
					// Loop the selected buttons.
					buttons.map( ( attributes ) =>
						// Create singular button in the buttons block.
						createBlock( 'core/button', attributes )
					)
				),
		},
		{
			type: 'block',
			isMultiBlock: true,
			blocks: [ 'core/paragraph' ],
			transform: ( buttons ) =>
				// Creates the buttons block.
				createBlock(
					'core/buttons',
					{},
					// Loop the selected buttons.
					buttons.map( ( attributes ) => {
						const { content } = attributes;
						const element = createElement( document, content );
						// Remove any HTML tags.
						const text = element.innerText || '';
						// Get first url.
						const link = element.querySelector( 'a' );
						const url = link?.getAttribute( 'href' );
						// Create singular button in the buttons block.
						return createBlock( 'core/button', {
							...getTransformedAttributes(
								attributes,
								'core/button',
								( { content: contentBinding } ) => ( {
									text: contentBinding,
								} )
							),
							text,
							url,
						} );
					} )
				),
			isMatch: ( paragraphs ) => {
				return paragraphs.every( ( attributes ) => {
					const element = createElement(
						document,
						attributes.content
					);
					const text = element.innerText || '';
					const links = element.querySelectorAll( 'a' );
					return text.length <= 30 && links.length <= 1;
				} );
			},
		},
	],
};

export default transforms;
