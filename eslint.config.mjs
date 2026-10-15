import wpPlugin from '@wordpress/eslint-plugin';
import globals from 'globals';

export default [
	{
		ignores: [ 'assets/**', 'vendor/**', 'node_modules/**', 'wordpress/**' ],
	},
	...wpPlugin.configs[ 'recommended-with-formatting' ],
	{
		languageOptions: {
			globals: {
				...globals.browser,
				...globals.jquery,
				wp: 'readonly',
				jQuery: 'readonly',
				Cookies: 'readonly',
				ga: 'readonly',
				gtag: 'readonly',
			},
		},
	},
];
