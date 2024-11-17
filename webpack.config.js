const defaultConfig = require( '@wordpress/scripts/config/webpack.config' );
const Barn2Configuration = require( '@barn2plugins/webpack-config' );

const config = new Barn2Configuration(
	[
		'posts-data-table-main/index.js',
	],
	[
		'admin/posts-data-table-admin.scss',
		'posts-data-table-main.scss',
	],
	defaultConfig
);

const b2Config = {
	...config.getWebpackConfig(),
	module: {
		rules: [
			...config.getWebpackConfig().module.rules,
			{
				test: /\.(woff|woff2|eot|ttf|otf)$/i,
				type: 'asset/resource',
				generator: {
					filename: '../lib/assets/fonts/[name][ext]',
				},
			},
		],
	},
};

module.exports = b2Config;