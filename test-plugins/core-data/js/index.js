import React from 'react';
import { compose } from '@wordpress/compose';
import { render } from 'react-dom';

import '@wordpress/core-data';
import { withSelect, withDispatch } from '@wordpress/data';

const Candidates = function( { candidates, toggleNeedsAction } ) {
	return candidates.map( ( c ) => (
		<div key={ c.id }>
			<input
				type="checkbox"
				checked={ c.json.needsAction || false }
				onChange={ () => {
					toggleNeedsAction( c );
				} }
			/>
			Name: { c.json.name }
		</div>
	) );
};

const CandidateContainer = function( { candidates, toggleNeedsAction } ) {
	return (
		<Candidates
			candidates={ candidates }
			toggleNeedsAction={ toggleNeedsAction }
		/>
	);
};

const App = compose( [
	withSelect( ( select ) => {
		const args = [ 'postType', 'candidate', { per_page: 300 } ];

		const candidates = select( 'core' ).getEntityRecords( ...args ) || [];

		return {
			candidates,
		};
	} ),
	withDispatch( ( dispatch ) => {
		const { saveEntityRecord } = dispatch( 'core' );

		return {
			toggleNeedsAction: ( candidate ) => {
				saveEntityRecord( 'postType', 'candidate', {
					id: candidate.id,
					json: {
						...candidate.json,
						needsAction: ! candidate.json.needsAction,
					},
				} );
			},
		};
	} ),
] )( CandidateContainer );

const renderApp = () => {
	render( <App />, document.getElementById( 'core-data' ) );
};

setTimeout( renderApp, 0 );
