module.exports = {
	root: true,
	parser: '@babel/eslint-parser',
	parserOptions: {
		requireConfigFile: false,
	},
	extends: ['plugin:@wordpress/eslint-plugin/recommended', 'plugin:prettier/recommended'],
	env: {
		browser: true,
		jquery: true,
	},
	rules: {
		'prettier/prettier': [
			'error',
			{
				danglingComma: 'es5',
			},
		],
		indent: ['error', 'tab'],
		'no-console': 'warn',
		'func-names': 0,
		'import/no-extraneous-dependencies': ['error', { devDependencies: true }],
	},
};
