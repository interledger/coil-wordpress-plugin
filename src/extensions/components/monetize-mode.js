/**
 * WordPress dependencies
 */
const { withSelect, withDispatch } = wp.data;
const { compose } = wp.compose;
const { Component } = wp.element;
const { withSpokenMessages } = wp.components;

export class MonetizedMode extends Component {
	componentDidMount() {
		this.sync();
	}

	componentDidUpdate() {
		this.sync();
	}

	sync() {
		const { isActive } = this.props;

		// Adds 'is-monetized' class to the html body if the content's gating is not undefined and does not have monetization disabled.
		if ( typeof isActive !== 'undefined' && isActive !== 'not-monetized' ) {
			document.body.classList.add( 'is-monetized' );
		} else {
			document.body.classList.remove( 'is-monetized' );
		}
	}

	render() {
		return null;
	}
}

export default compose( [
	withSelect( ( select ) => ( {
		isActive: select( 'core/editor' ).getEditedPostAttribute( 'meta' ), // eslint-disable-line
	} ) ),
	withDispatch( ( dispatch ) => {
		return {
			setPostMeta( isActive ) {
				dispatch( 'core/editor' ).editPost( { meta: isActive } );
			},
		};
	} ),
	withSpokenMessages,
] )( MonetizedMode );
