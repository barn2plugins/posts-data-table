
( function( $ ) {

    $( document ).ready( function() {

        let tables = $( '.posts-data-table' );

        const adminBar = $( '#wpadminbar' ),
            clickFilterColumns = ['categories', 'tags', 'author'];

        tables.each( function() {
            let $table = $( this ),
                config = {
                    responsive: true,
                    processing: true // display 'processing' indicator when loading
                };

            // Set language - defaults to English if not specified
            if ( ( typeof posts_data_table !== 'undefined' ) && posts_data_table.langurl ) {
                config.language = { url: posts_data_table.langurl };
            }

            // Initialise DataTable
            let table = $table.DataTable( config );

            // If scroll offset defined, animate back to top of table on next/previous page event
            $table.on( 'page.dt', function() {
                if ( $( this ).data( 'scroll-offset' ) !== false ) {
                    let tableOffset = $( this ).parent().offset().top - $( this ).data( 'scroll-offset' );

                    if ( adminBar.length ) { // Adjust offset for WP admin bar
                        let adminBarHeight = adminBar.outerHeight();
                        tableOffset -= ( adminBarHeight ? adminBarHeight : 32 );
                    }

                    $( 'html,body' ).animate( { scrollTop: tableOffset }, 300 );
                }
            } );

            // If 'search on click' enabled then add click handler for links in category, author and tags columns.
            // When clicked, the table will filter by that value.
            if ( $table.data( 'click-filter' ) ) {
                $table.on( 'click', 'a', function() {
                    let $link = $( this ),
                        idx = table.cell( $link.closest( 'td' ).get( 0 ) ).index().column, // get the column index
                        header = table.column( idx ).header(), // get the header cell
                        columnName = $( header ).data( 'name' ); // get the column name from header

                    // Is the column click filterable?
                    if ( -1 !== clickFilterColumns.indexOf( columnName ) ) {
                        table.search( $link.text() ).draw();
                        return false;
                    }

                    return true;
                } );
            }

        } ); // each table

    } ); // end document.ready

} )( jQuery );