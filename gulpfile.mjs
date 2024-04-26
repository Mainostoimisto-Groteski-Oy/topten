/* eslint no-console: 0 */
import _ from 'lodash';
import gulp from 'gulp';
import autoprefixer from 'autoprefixer';
import browserSync from 'browser-sync';
import bump from 'gulp-bump';
import compiler from 'webpack';
import cssnano from 'cssnano';
import * as dartSass from 'sass';
import { deleteAsync } from 'del';
import gulpSass from 'gulp-sass';
import imagemin from 'gulp-imagemin';
import path from 'path';
import postcss from 'gulp-postcss';
import rename from 'gulp-rename';
import sourcemaps from 'gulp-sourcemaps';
import tap from 'gulp-tap';
import webp from 'gulp-webp';
import webpack from 'webpack-stream';
import webpackConfig from './webpack.config.js'; // eslint-disable-line

const { dest, parallel, series, src, watch } = gulp;
const sass = gulpSass(dartSass);

const config = {
	assets: {
		dest: './assets/dist',
		src: './assets/src/**/*',
		watch: ['./assets/src/**/*'],
	},
	bs: {
		opts: {
			files: ['./css/*.min.css', './js/*.min.js', './**/*.php', './*.php'],
			proxy: `http://${path.basename(process.cwd())}.local`,
		},
	},
	bump: {
		regex:
			/([<|'|"]?(TOPTEN_VERSION)[>|'|"]?[ ]*[:=,]?[ ]*['|"]?[a-z]?)(\d+.\d+.\d+)(-[0-9A-Za-z.-]+)?(\+[0-9A-Za-z.-]+)?(['|"|<]?)/i,
	},
	clean: ['./js/dist/**', './css/dist/**', './assets/dist/**'],
	cssnano: {
		preset: [
			'default',
			{
				autoprefixer: true,
				discardUnused: true,
				mergeIdents: true,
			},
		],
	},
	js: {
		dest: './js/dist',
		src: ['./js/src/*.js', './js/src/**/*.js', '!./js/src/admin/*.js', '!./js/src/admin/**/*.js'],
		watch: ['./js/src/*.js', './js/src/**/*.js', '!./js/src/admin/*.js', '!./js/src/admin/**/*.js'],
		admin: {
			dest: './js/dist',
			src: ['./js/src/admin/*.js', './js/src/admin/**/*.js'],
			watch: ['./js/src/admin/*.js', './js/src/admin/**/*.js'],
		},
	},
	rename: {
		suffix: '.min',
	},
	sass: {
		outputStyle: 'compressed',
	},
	styles: {
		dest: './css/dist',
		src: ['./css/src/site.scss', './css/src/gutenberg.scss', './css/src/admin.scss'],
		watch: ['./css/src/*.scss', './css/src/**/*.scss'],
	},
	uglify: {
		compress: true,
		mangle: true,
	},
};

function styles() {
	return src(config.styles.src)
		.pipe(sourcemaps.init())

		.pipe(sass(config.sass))

		.pipe(postcss([autoprefixer(), cssnano(config.cssnano)]))

		.pipe(sourcemaps.write())

		.pipe(rename(config.rename))

		.pipe(dest(config.styles.dest))

		.pipe(browserSync.stream());
}

function productionStyles() {
	return src(config.styles.src)
		.pipe(sass(config.sass))

		.pipe(postcss([autoprefixer(), cssnano(config.cssnano)]))

		.pipe(rename(config.rename))

		.pipe(dest(config.styles.dest));
}

function js() {
	const wpConfig = _.clone(webpackConfig);

	wpConfig.mode = 'development';
	wpConfig.devtool = 'source-map';

	return src(config.js.src)
		.pipe(webpack(wpConfig, compiler))

		.on('error', (err) => {
			console.error(err.message);
			this.emit('end');
		})
		.pipe(dest(config.js.dest))

		.pipe(browserSync.stream());
}

function adminJs() {
	const wpConfig = _.clone(webpackConfig);

	wpConfig.mode = 'development';
	wpConfig.devtool = 'source-map';
	wpConfig.output.filename = 'admin.min.js';

	return src(config.js.admin.src)
		.pipe(webpack(wpConfig, compiler))

		.on('error', function () {
			this.emit('end');
		})

		.pipe(dest(config.js.admin.dest))

		.pipe(browserSync.stream());
}

function productionJs() {
	const wpConfig = _.clone(webpackConfig);

	wpConfig.mode = 'production';
	wpConfig.devtool = false;

	return src(config.js.src)
		.pipe(webpack(wpConfig, compiler))

		.on('error', function () {
			this.emit('end');
		})

		.pipe(dest(config.js.dest));
}

function adminProductionJs() {
	const wpConfig = _.clone(webpackConfig);

	wpConfig.mode = 'production';
	wpConfig.devtool = false;
	wpConfig.output.filename = 'admin.min.js';

	return src(config.js.admin.src)
		.pipe(webpack(wpConfig, compiler))

		.on('error', function () {
			this.emit('end');
		})

		.pipe(dest(config.js.admin.dest));
}

function bumpPackagePatch() {
	return src('./package.json')
		.pipe(bump({ type: 'patch' }))

		.pipe(dest('./'));
}

function bumpPackageMinor() {
	return src('./package.json')
		.pipe(bump({ type: 'minor' }))

		.pipe(dest('./'));
}

function bumpPackagePre() {
	return src('./package.json')
		.pipe(bump({ type: 'prerelease' }))

		.pipe(dest('./'));
}

function bumpFunctionsPatch() {
	config.bump.type = 'patch';

	return src('./theme-version.php')
		.pipe(bump(config.bump))

		.pipe(dest('./'));
}

function bumpFunctionsMinor() {
	config.bump.type = 'minor';

	return src('./theme-version.php')
		.pipe(bump(config.bump))

		.pipe(dest('./'));
}

function bumpFunctionsPre() {
	config.bump.type = 'prerelease';

	return src('./theme-version.php')
		.pipe(bump(config.bump))

		.pipe(dest('./'));
}

function bumpStylesheetPatch() {
	return src('./style.css')
		.pipe(bump({ type: 'patch' }))

		.pipe(dest('./'));
}

function bumpStylesheetMinor() {
	return src('./style.css')
		.pipe(bump({ type: 'minor' }))

		.pipe(dest('./'));
}

function bumpStylesheetPre() {
	return src('./style.css')
		.pipe(bump({ type: 'prerelease' }))

		.pipe(dest('./'));
}

function optimizeAssets() {
	const files = [];

	return src(config.assets.src)
		.pipe(
			tap((f) => {
				const file = f;

				files.push({
					basename: path.parse(file.basename).name,
					ext: path.extname(file.path),
				});
			}),
		)

		.pipe(imagemin())

		.pipe(dest(config.assets.dest))

		.pipe(webp())

		.pipe(
			rename((fp) => {
				const filepath = fp;

				if (filepath.extname && filepath.extname !== '.svg' && filepath.extname !== '.gif') {
					const ext = files.find((file) => file.basename === filepath.basename);

					filepath.basename += ext.ext;
				}
			}),
		)

		.pipe(dest(config.assets.dest))

		.pipe(browserSync.stream());
}

function cleanDist() {
	return deleteAsync(config.clean);
}

function initBrowserSync() {
	browserSync.init(config.bs.opts);

	watch(config.styles.watch, styles);

	watch(config.js.watch, js);

	watch(config.js.admin.watch, adminJs);

	watch(config.assets.watch, optimizeAssets);
}

function watchFiles() {
	watch(config.styles.watch, styles);

	watch(config.js.watch, js);

	watch(config.js.admin.watch, adminJs);

	watch(config.assets.watch, optimizeAssets);
}

export const prod = series(
	cleanDist,
	parallel(optimizeAssets, productionStyles, series(productionJs, adminProductionJs)),
);

export const generate = series(
	cleanDist,
	parallel(optimizeAssets, productionStyles, series(productionJs, adminProductionJs)),
);

export const bumpVersionPre = series(bumpFunctionsPre, bumpStylesheetPre, bumpPackagePre); // 1.0.0-dev.1 -> 1.0.0-dev.2
export const bumpVersionPatch = series(bumpFunctionsPatch, bumpStylesheetPatch, bumpPackagePatch); // 1.0.0 -> 1.0.1
export const bumpVersionMinor = series(bumpFunctionsMinor, bumpStylesheetMinor, bumpPackageMinor); // 1.0.0 -> 1.1.0

export const bs = initBrowserSync;

export const gitProd = series(cleanDist, prod, bumpVersionPatch);
export const gitProdDev = series(cleanDist, prod);

export default watchFiles;
