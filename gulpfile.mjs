import gulp from 'gulp';
import autoprefixer from 'autoprefixer';
import browserSync from 'browser-sync';
import bump from 'gulp-bump';
import compiler from 'webpack';
import cssnano from 'cssnano';
import dartSass from 'sass';
import del from 'del';
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
		src: ['./js/src/*.js', './js/src/**/*.js'],
		watch: ['./js/src/*.js', './js/src/**/*.js'],
	},
	rename: {
		suffix: '.min',
	},
	sass: {
		outputStyle: 'compressed',
	},
	styles: {
		dest: './css/dist',
		src: ['./css/src/site.scss', './css/src/gutenberg.scss'],
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
	webpackConfig.mode = 'development';
	webpackConfig.devtool = 'source-map';

	return src(config.js.src)
		.pipe(webpack(webpackConfig, compiler))

		.on('error', () => {
			this.emit('end');
		})

		.pipe(dest(config.js.dest))

		.pipe(browserSync.stream());
}

function productionJs() {
	webpackConfig.mode = 'production';
	webpackConfig.devtool = false;

	return src(config.js.src)
		.pipe(webpack(webpackConfig, compiler))

		.on('error', () => {
			this.emit('end');
		})

		.pipe(dest(config.js.dest))

		.pipe(browserSync.stream());
}

function bumpFunctions() {
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

function bumpStylesheet() {
	return src('./style.css')
		.pipe(bump())

		.pipe(dest('./'));
}

function bumpStylesheetMinor() {
	return src('./style.css')
		.pipe(bump({ type: 'minor' }))

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
			})
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
			})
		)

		.pipe(dest(config.assets.dest))

		.pipe(browserSync.stream());
}

function cleanDist() {
	return del(config.clean);
}

function initBrowserSync() {
	browserSync.init(config.bs.opts);

	watch(config.styles.watch, styles);

	watch(config.js.watch, js);

	watch(config.assets.watch, optimizeAssets);
}

function watchFiles() {
	watch(config.styles.watch, styles);

	watch(config.js.watch, js);

	watch(config.assets.watch, optimizeAssets);
}

export const prod = series(cleanDist, parallel(optimizeAssets, productionStyles, productionJs));

export const generate = series(cleanDist, parallel(optimizeAssets, productionStyles, productionJs));

export const bumpVersion = series(bumpFunctionsMinor, bumpStylesheetMinor);

export const bs = initBrowserSync;

export const gitProd = series(
	cleanDist,
	parallel(optimizeAssets, productionStyles, productionJs, bumpFunctions, bumpStylesheet)
);

export default watchFiles;
