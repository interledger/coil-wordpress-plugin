// Get helper functions from global scope
const { registerBlockType } = window.wp.blocks;
const { useBlockProps } = wp.blockEditor;
const { __ } = window.wp.i18n;

// Use registerBlockType to create a custom block
registerBlockType(
	'coil/exclusive-content-divider',
	{
		// Localize title using wp.i18n.__()
		title: __( 'Coil Exclusive Content Divider' ),
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
				<div className={ props.className } >
					<div className="coil-exclusive-content-divider-inner">
						<svg width="24" height="24" viewBox="0 0 24 24" fill="none" xmlns="http://www.w3.org/2000/svg">
							<path d="M0 5C0 2.23858 2.23858 0 5 0H19C21.7614 0 24 2.23858 24 5V19C24 21.7614 21.7614 24 19 24H5C2.23858 24 0 21.7614 0 19V5Z" fill="black" />
							<path d="M16.2587 14.8242C16.5793 14.8242 16.9975 14.9868 17.2903 15.6781C17.3321 15.773 17.36 15.8815 17.36 15.9763C17.36 17.1149 14.6973 18.1451 13.1498 18.2264C13.0383 18.2264 12.9128 18.24 12.8013 18.24C10.5011 18.24 8.36809 17.0472 7.26675 15.1224C6.75094 14.2142 6.5 13.2112 6.5 12.1946C6.5 11.0153 6.84852 9.83601 7.53163 8.8194C8.06139 8.00611 9.09302 6.93528 10.9053 6.35243C11.3654 6.20332 12.174 6 13.0941 6C13.9863 6 14.9761 6.18977 15.8404 6.82684C17.0533 7.70791 17.3042 8.6703 17.3042 9.29382C17.3042 9.56492 17.2624 9.76824 17.2206 9.89023C16.9418 10.8797 16.0077 11.6388 14.9203 11.7879C14.6694 11.815 14.4603 11.8421 14.2651 11.8421C13.2753 11.8421 12.9407 11.4355 12.9407 10.9746C12.9407 10.3511 13.5402 9.61914 13.9723 9.61914C14.0281 9.61914 14.0839 9.63269 14.1257 9.6598C14.2372 9.72757 14.3766 9.75468 14.5021 9.75468C14.5439 9.75468 14.5718 9.75468 14.6136 9.74113C14.9622 9.70046 15.1434 9.47003 15.1434 9.18538C15.1434 8.65674 14.5021 7.93834 13.108 7.93834C12.6619 7.93834 12.1461 8.00611 11.5606 8.18232C10.3338 8.54831 9.65065 9.40226 9.30213 9.9309C8.84208 10.6086 8.61902 11.3948 8.61902 12.1674C8.61902 12.8316 8.78632 13.4958 9.1209 14.0922C9.84583 15.3528 11.2678 16.139 12.8013 16.139C12.885 16.139 12.9547 16.139 13.0383 16.139C15.0319 16.0306 15.4919 15.0546 15.938 14.8648C16.0077 14.8513 16.1332 14.8242 16.2587 14.8242Z" fill="white" />
						</svg>
						<p>Exclusive content for Coil Members starts below</p>
						<svg width="8" height="12" viewBox="0 0 8 12" fill="none" xmlns="http://www.w3.org/2000/svg">
							<path fillRule="evenodd" clipRule="evenodd" d="M4 0.5C4.55228 0.5 5 0.947715 5 1.5V8.08579L6.29289 6.79289C6.68342 6.40237 7.31658 6.40237 7.70711 6.79289C8.09763 7.18342 8.09763 7.81658 7.70711 8.20711L4.70711 11.2071C4.31658 11.5976 3.68342 11.5976 3.29289 11.2071L0.292893 8.20711C-0.0976311 7.81658 -0.0976311 7.18342 0.292893 6.79289C0.683417 6.40237 1.31658 6.40237 1.70711 6.79289L3 8.08579V1.5C3 0.947715 3.44772 0.5 4 0.5Z" fill="#2D333A" />
						</svg>
					</div>
				</div>
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