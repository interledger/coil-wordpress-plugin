/**
 * WordPress dependencies
 */
import {
	registerBlockType,
} from '@wordpress/blocks';

// Register block category
import './utils/block-category';

// Editor and Frontend Styles
//import './styles/editor.scss';
//import './styles/style.scss';

// Extensions
import './extensions/monetize-block';

// Register Blocks
//import * as splitContent from './blocks/split-content';

/**
 * Function to register an individual block.
 *
 * @param {Object} block The block to be registered.
 *
 */
const registerBlock = ( block ) => {
	if ( ! block ) {
		return;
	}

	const { name, category, settings } = block;

	registerBlockType( name, {
		category: category,
		...settings,
	} );
};

/**
 * Function to register blocks provided by Coil.
 */
export const registerBlocks = () => {
	[
		//splitContent,
	].forEach( registerBlock );
};

registerBlocks();
