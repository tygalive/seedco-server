/**Ignore convert suggestion */
const path = require("path");
const webpack = require("webpack");
const MiniCssExtractPlugin = require("mini-css-extract-plugin");
const CssMinimizerPlugin = require("css-minimizer-webpack-plugin");
const WebpackAssetsManifest = require("webpack-assets-manifest");

module.exports = {
	mode: "development",
	entry: ["./app/Interface/index.js"],
	output: {
		filename: "[name].js",
		path: path.resolve(__dirname, "./public/theme"),
	},
	module: {
		rules: [
			{
				test: /\.(js|jsx)$/,
				exclude: /node_modules/,
				loader: "babel-loader",
				options: {
					presets: [
						["@babel/preset-env", { targets: { node: "current" } }],
						[
							"@babel/preset-react",
							{ targets: "defaults", runtime: "automatic" },
						],
					],
				},
			},
			{
				test: /\.css$/,
				use: [MiniCssExtractPlugin.loader, "css-loader"],
			},
			{
				test: /\.svg$/,
				loader: "svg-inline-loader",
			},
		],
	},
	plugins: [
		// fix "process is not defined" error:
		// (do "npm install process" before running the build)
		new webpack.ProvidePlugin({
			React: "react",
		}),
		new webpack.ProvidePlugin({
			process: "process/browser",
		}),
		new WebpackAssetsManifest({
			output: "../manifest.json",
			merge: true,
			sortManifest: false,
			publicPath: "./theme/",
			entrypoints: true,
		}),
		new MiniCssExtractPlugin({
			filename: "[name].css",
			chunkFilename: "[id].css",
		}),
	],
	optimization: {
		minimize: true,
		minimizer: [new CssMinimizerPlugin()],
	},
};
