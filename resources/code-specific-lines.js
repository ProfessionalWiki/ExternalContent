Prism.hooks.add( 'complete', function( env ) {
    var showLineRanges = env.element.parentElement.getAttribute( 'data-show-lines' );

    if (showLineRanges) {
        var showLines = showLines( showLineRanges );

        var tokenList = tokenList( env.element.innerHTML, showLines );

        var lineNumbersActive = Prism.util.isActive( env.element, 'line-numbers' );
        
        if ( lineNumbersActive ) {
            var lineNumberList = lineNumberList( env.element.querySelectorAll( '.line-numbers-rows span' ), showLines );

            env.element.innerHTML = tokenList.join( '' ) + '<span aria-hidden="true" class="line-numbers-rows">' + lineNumberList.join( '' ) + '</span>';
        }
        else {
            env.element.innerHTML = tokenList.join( '' );
        }
    }

    function showLines( showLineRanges ) {
        var showLines = [];
    
        showLineRanges.replace( /\s+/g, '' ).split( ',' ).filter( Boolean ).forEach( function( currentRange ) {
            if (currentRange.includes( '-' )) {
                var range = currentRange.split( '-' );
    
                if ( range[0] > range[1] ) {
                    [range[0], range[1]] = [range[1], range[0]];
                }
    
                for ( var counter = range[0]; counter <= range[1]; counter++ ) {
                    showLines.push( Number( counter ) );
                }
            }
            else {
                showLines.push( Number( currentRange ) );
            }
        });
    
        return showLines;
    }
    
    function tokenList( HTML, showLines ) {
        var tokenList = HTML.split( /\n(?!$)/g );
        var tokenCount = tokenList.length;
        const regex = /(<span aria-hidden="true" class="line-numbers-rows">(.*<\/span>))/gm;
    
        tokenList.forEach( function( value, index ) {
            if ( index === tokenCount - 1 ) {
                value = value.replace (regex, '' );
            }
    
            if ( showLines.includes ( index + 1 ) ) {
                tokenList[index] += '\n';
            }
            else {
                tokenList[index] = '<span class="hide-line">' + value + '</span>';
            }
        });
    
        return tokenList;
    }
    
    function lineNumberList( lineNumberRows, showLines ) {
        var lineNumberList = [];
    
        lineNumberRows.forEach( function( value, index ) {
            if ( !showLines.includes( index + 1 ) ) {
                lineNumberRows[index].classList.add( 'hide-line' );
            }
    
            lineNumberList[index] = lineNumberRows[index].outerHTML;
        });
    
        return lineNumberList;
    }
});