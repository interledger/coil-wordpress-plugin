/**
 * Internal dependencies
 */
import MonetizedMode from './components/monetize-mode';

/**
 * WordPress dependencies
 */
const { registerPlugin } = wp.plugins;

registerPlugin( 'coil-monetized-mode', {
	icon: false,
	render: MonetizedMode,
} );
