const TerserPlugin = require('terser-webpack-plugin');

module.exports = {
	optimization: {
		minimize: true,
		minimizer: [
			new TerserPlugin({
				terserOptions: {
					mangle: true,
					output: null,
					format: null,
					toplevel: false,
					nameCache: null,
					keep_classnames: undefined,
					keep_fnames: false,
					ie8: false,
					safari10: false,
				},
			}),
		],
	},
	externals: {
		jquery: 'jQuery',
	},
	module: {
		rules: [
			{
				test: /.js$/,
				exclude: /node_modules/,
				use: {
					loader: 'babel-loader',
					options: {
						presets: ['@babel/preset-env'],
						targets: 'defaults',
						cacheDirectory: true,
					},
				},
			},
			{
				test: /\.css$/i,
				use: ['style-loader', 'css-loader'],
			},
		],
	},
	output: {
		filename: 'main.min.js',
	},
};
