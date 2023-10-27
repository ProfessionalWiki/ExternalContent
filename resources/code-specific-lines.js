Prism.hooks.add( 'complete', function( env ) {
    var showLineRanges = env.element.parentElement.getAttribute( 'data-show-lines' );

    if (showLineRanges) {
        var showLines = showLines( showLineRanges );

        var tokenList = tokenList( env.element, showLines );

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
                var range = currentRange.split( '-' ).map( Number );
    
                if ( range[0] > range[1] ) {
                    [range[0], range[1]] = [range[1], range[0]];
                }
    
                for ( var counter = range[0]; counter <= range[1]; counter++ ) {
                    showLines.push( counter );
                }
            }
            else {
                showLines.push( Number( currentRange ) );
            }
        });
    
        return showLines;
    }
    
    function tokenList( element, showLines ) {
        var tokenList = element.innerHTML.split( /\n(?!$)/g );
        var tokenCount = tokenList.length;
        var extraLanguageSpan = false;
        const regexExtraLanguageSpan = /<span class="token [^"]*? language-[^"]*?">/gm;
        const regexLineNumberRows = /(<span aria-hidden="true" class="line-numbers-rows">(.*<\/span>))/gm;

        if ( regexExtraLanguageSpan.test( tokenList[0] ) ) {
            tokenList[0] = tokenList[0].replace ( regexExtraLanguageSpan, '' );
            extraLanguageSpan = true;
        }
    
        tokenList.forEach( function( value, index ) {
            if ( index === tokenCount - 1 ) {
                value = value.replace ( regexLineNumberRows, '' );

                if ( extraLanguageSpan ) {
                    value = value.replace( '</span>', '' );
                }
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