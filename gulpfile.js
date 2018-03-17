var gulp = require( 'gulp' ),
	pump = require( 'pump' ),
	jshint = require( 'gulp-jshint' ),
	minify = require( 'gulp-uglify' ),
	cleancss = require( 'gulp-clean-css' ),
	fs = require( 'fs' ),
	header = require( 'gulp-header' ),
	rename = require( 'gulp-rename' ),
	run = require( 'gulp-run' ),
	debug = require( 'gulp-debug' ),
	prompt = require( 'gulp-prompt' ),
	checktextdomain = require( 'gulp-checktextdomain' );

const pluginSlug = 'posts-data-table';
const zipFile = pluginSlug + '.zip';
const pluginArchive = '/Users/andy/Dropbox/Barn2 Media/Plugins/Plugin archive/';

var getVersion = function() {
	var readme = fs.readFileSync( 'readme.txt', 'utf8' );
	var version = readme.match( /Stable tag\:\s(.*)\s/i );
	return ( 1 in version ) ? version[1] : false;
};

var getCopyright = function() {
	return fs.readFileSync( 'copyright.txt' );
};

var getPluginSlug = function() {
	var dir = __dirname;
	return dir.substr( dir.lastIndexOf( '/' ) + 1 );
};

gulp.task( 'scripts', function( cb ) {
	pump( [
		gulp.src( ['assets/js/*.js', '!**/*.min.js'], { base: './' } ),
		debug(),
		header( getCopyright(), { 'version': getVersion() } ),
		minify( { compress: { negate_iife: false }, output: { comments: '/^\/*!/' } } ),
		rename( { suffix: '.min' } ),
		gulp.dest( '.' )
	], cb );
} );

gulp.task( 'styles', function( cb ) {
	pump( [
		gulp.src( ['assets/css/*.css', '!**/*.min.css'], { base: './' } ),
		debug(),
		header( getCopyright( ), { 'version': getVersion( ) } ),
		cleancss( { compatibility: 'ie9' } ),
		rename( { suffix: '.min' } ),
		gulp.dest( '.' )
	], cb );
} );

gulp.task( 'lint', function() {
	return gulp.src( ['assets/js/*.js', '!**/*.min.js'] )
		.pipe( jshint() )
		.pipe( jshint.reporter() ); // Dump results
} );

gulp.task( 'zip', ['scripts', 'styles'], function() {
	var zipCommand = `cd .. && rm ${zipFile}; zip -r ${zipFile} ${pluginSlug} -x *vendor* *node_modules* *.git* *.DS_Store *package*.json *gulpfile.js *copyright.txt`;

	return run( zipCommand ).exec();
} );

gulp.task( 'archive', ['zip'], function() {
	var pluginDir = pluginArchive + pluginSlug,
		deployDir = pluginDir + '/' + getVersion();

	if ( !fs.existsSync( pluginDir ) ) {
		fs.mkdirSync( pluginDir );
	}
	if ( !fs.existsSync( deployDir ) ) {
		fs.mkdirSync( deployDir );
	}

	return gulp.src( zipFile, { cwd: '../' } )
		.pipe( debug() )
		.pipe( gulp.dest( deployDir ) );
} );

gulp.task( 'textdomain', function() {
	var textDomain = pluginSlug;
	return gulp
		.src( '**/*.php' )
		.pipe(
			checktextdomain( {
				text_domain: textDomain,
				keywords: [
					'__:1,2d',
					'_e:1,2d',
					'_x:1,2c,3d',
					'esc_html__:1,2d',
					'esc_html_e:1,2d',
					'esc_html_x:1,2c,3d',
					'esc_attr__:1,2d',
					'esc_attr_e:1,2d',
					'esc_attr_x:1,2c,3d',
					'_ex:1,2c,3d',
					'_n:1,2,4d',
					'_nx:1,2,4c,5d',
					'_n_noop:1,2,3d',
					'_nx_noop:1,2,3c,4d'
				]
			} )
			);
} );

gulp.task( 'build', ['scripts', 'styles', 'lint', 'textdomain', 'zip'] );
gulp.task( 'release', ['build', 'archive'] );
gulp.task( 'default', ['build'] );