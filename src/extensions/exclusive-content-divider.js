// Get helper functions from global scope
const { registerBlockType } = window.wp.blocks;
const { useBlockProps } = wp.blockEditor;
const { __ } = window.wp.i18n;

import { ExclusiveContentDivider } from './gutenberg-blocks/exclusive-content-divider-component.js';

// Use registerBlockType to create a custom block
registerBlockType(
	'coil/exclusive-content-divider',
	{
		// Localize title using wp.i18n.__()
		title: __( 'Coil Exclusive Content Divider', 'coil-web-monetization' ),
		// Category Options: common, formatting, layout, widgets, embed
		category: 'common',

		// Dashicons Options – https://goo.gl/aTM1DQ
		icon: {
			// Specifying a color for the icon (optional: if not set, a readable color will be automatically defined)
			foreground: '#fff',
			// Specifying an icon for the block
			src: <svg width="32" height="32" viewBox="0 0 32 32" fill="none" xmlns="http://www.w3.org/2000/svg">
				<path d="M0 6.66667C0 2.98477 2.98477 0 6.66667 0H25.3333C29.0152 0 32 2.98477 32 6.66667V25.3333C32 29.0152 29.0152 32 25.3333 32H6.66667C2.98477 32 0 29.0152 0 25.3333V6.66667Z" fill="#2D333A" />
				<path d="M21.0116 19.7656C21.4391 19.7656 21.9967 19.9825 22.3871 20.9042C22.4428 21.0307 22.48 21.1753 22.48 21.3018C22.48 22.8199 18.9297 24.1935 16.8664 24.3019C16.7177 24.3019 16.5504 24.32 16.4017 24.32C13.3347 24.32 10.4908 22.7296 9.02234 20.1632C8.33458 18.9523 8 17.6149 8 16.2594C8 14.687 8.4647 13.1147 9.37551 11.7592C10.0818 10.6748 11.4574 9.24704 13.8738 8.4699C14.4872 8.2711 15.5653 8 16.7921 8C17.9817 8 19.3015 8.25302 20.4539 9.10246C22.0711 10.2772 22.4056 11.5604 22.4056 12.3918C22.4056 12.7532 22.3499 13.0243 22.2941 13.187C21.9224 14.5063 20.677 15.5184 19.2271 15.7172C18.8925 15.7534 18.6137 15.7895 18.3535 15.7895C17.0337 15.7895 16.5876 15.2473 16.5876 14.6328C16.5876 13.8015 17.3869 12.8255 17.9631 12.8255C18.0375 12.8255 18.1118 12.8436 18.1676 12.8797C18.3163 12.9701 18.5022 13.0062 18.6695 13.0062C18.7252 13.0062 18.7624 13.0062 18.8182 12.9882C19.2829 12.934 19.5245 12.6267 19.5245 12.2472C19.5245 11.5423 18.6695 10.5845 16.8107 10.5845C16.2159 10.5845 15.5281 10.6748 14.7474 10.9098C13.1117 11.3977 12.2009 12.5363 11.7362 13.2412C11.1228 14.1448 10.8254 15.1931 10.8254 16.2233C10.8254 17.1088 11.0484 17.9944 11.4945 18.7896C12.4611 20.4704 14.3571 21.5187 16.4017 21.5187C16.5133 21.5187 16.6062 21.5187 16.7177 21.5187C19.3758 21.3741 19.9892 20.0728 20.584 19.8198C20.677 19.8017 20.8443 19.7656 21.0116 19.7656Z" fill="#fff" />
			</svg>,
		},

		//Supports
		supports: {
			multiple: false,
		},
		// Limit to 3 Keywords / Phrases
		keywords: [
			__( 'Coil' ),
			__( 'Exclusive' ),
			__( 'Divider' ),
			__( 'Monetize' ),
			__( 'Monetization' ),
			__( 'Web Monetization' ),
			__( 'Restrict' ),
			__( 'Read' ),
			__( 'More' ),
		],
		// Attributes set for each piece of dynamic data used in your block
		attributes: {
			exampleContent: {
				type: 'array',
				source: 'children',
				selector: 'div.coil-exclusive-content-divider',
			},
		},
		// Determines what is displayed in the editor
		edit: props => {
			const blockProps = useBlockProps( { // eslint-disable-line
				className: 'coil-exclusive-content-divider',
			} );
			return (
				<ExclusiveContentDivider blockTypeClassName={ `
				${ props.className }
				` }
				/>
			);
		},
		// Determines what is displayed on the frontend
		save: props => {
			const blockProps = useBlockProps.save( { // eslint-disable-line
				className: 'coil-exclusive-content-divider',
			} );
			return (
				<span className={ props.className } ></span>
			);
		},
	},
);
