'use strict';

var React = require( 'react' );
var Postlist = require( './Postlist.jsx' );

React.render(
    <Postlist
        url="actions/data.php"
        pollInterval={60000}
    />,
    document.getElementById( 'js-app' )
);
