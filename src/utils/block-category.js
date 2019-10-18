/**
 * WordPress dependencies
 */
import { getCategories, setCategories } from '@wordpress/blocks';
import { __, sprintf } from '@wordpress/i18n';

/**
 * Internal dependencies
 */
//import brandAssets from './brand-assets';

setCategories( [
	{
		slug: 'coil',
		title: 'Coil',
		//icon: brandAssets.categoryIcon,
	},
	...getCategories().filter( ( { slug } ) => slug !== 'coil' ),
] );
