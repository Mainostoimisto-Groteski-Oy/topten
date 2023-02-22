module.exports = {
	env: {
		browser: true,
		jquery: true,
		node: true,
		es2022: true,
	},
	extends: ['eslint:recommended', 'airbnb-base', 'prettier'],
	parserOptions: { ecmaVersion: 13 },
	rules: {
		indent: ['error', 'tab'],
		'no-console': 0,
		'func-names': 0,
		'import/no-extraneous-dependencies': ['error', { devDependencies: true }],
	},
};
