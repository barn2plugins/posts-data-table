
( function( $ ) {

	$( document ).ready( function() {

		var tables = $( '.posts-data-table' );
		var adminBarVisible = $( '#wpadminbar' ).length;

		tables.each( function() {

			var config = {
				responsive: true,
				processing: true // display 'processing' indicator when loading
			};

			// Set language - defaults to English if not specified
			if ( ( typeof posts_data_table !== 'undefined' ) && posts_data_table.langurl ) {
				config.language = { url: posts_data_table.langurl };
			}

			// Initialise DataTable
			var table = $( this ).DataTable( config );

			// If scroll offset defined, animate back to top of table on next/previous page event
			$( this ).on( 'page.dt', function() {
				if ( $( this ).data( 'scroll-offset' ) !== false ) {
					var tableOffset = $( this ).parent().offset().top - $( this ).data( 'scroll-offset' );
					if ( adminBarVisible ) { // Adjust offset for WP admin bar
						tableOffset -= 32;
					}
					$( 'html,body' ).animate( { scrollTop: tableOffset }, 300 );
				}
			} );

			// If 'search on click' feature enabled then add click handler for links in category and author columns.
			// When a category or author is clicked, the table will filter by that value
			if ( $( this ).data( 'click-filter' ) ) {
				table.columns( ['category:name', 'author:name'] ).nodes().to$().each( function() {
					$( this ).children( 'a' ).on( 'click', function() {
						table.search( $( this ).text() ).draw();
						return false;
					} );
				} );
			}

		} ); // each table

	} ); // end document.ready

} )( jQuery );