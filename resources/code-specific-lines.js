Prism.hooks.add( 'complete', function ( env ) {
    var showLineRanges = env.element.parentElement.getAttribute( 'data-show-lines' );

    if (showLineRanges) {
        var showLines = [];

        showLineRanges.replace( /\s+/g, '' ).split( ',' ).filter( Boolean ).forEach( function ( currentRange ) {
            if (currentRange.includes( '-' )) {
                var range = currentRange.split( '-' );

                if ( range[ 0 ] > range[ 1 ] ) {
                    [ range[ 0 ], range[ 1 ] ] = [ range[ 1 ], range[ 0 ] ];
                }

                for ( var counter = range[0]; counter <= range[1]; counter++ ) {
                    showLines.push( counter );
                }
            }
            else {
                showLines.push( Number(currentRange) );
            }
        });

        var tokenList = env.element.querySelector( '.token' ).innerHTML.split( /\n(?!$)/g );
        var lineNumberRows = env.element.querySelectorAll( '.line-numbers-rows span' );

        tokenList.forEach ( function( value, index ) {
            if ( showLines.includes ( index + 1 ) ) {
                tokenList[ index ] += '\n';
            }
            else {
                tokenList[ index ] = '<span class="hide-line">' + value + '</span>';
                lineNumberRows[ index ].classList.add( 'hide-line' );
            }
        });

        env.element.querySelector( '.token' ).innerHTML = tokenList.join( '' );
    }
});