(function ($) {
	$( function () {

		function urlStripProto( url ) {
			return url.replace( /^https?:/, '' );
		}

		// Decline to log search results or visits to the test blog.
		if ( /^test/.test( location.hostname ) || /[\?&]s=/.test( window.location ) ) {
			return;
		}

		// Attempt to canonicalize the URL by grabbing <link rel="canonical">
		var url = urlStripProto( $( 'link[rel=canonical]' ).attr( 'href' ) || window.location.toString() );

		window.setTimeout( function () {
			var capsule = {
				schema: 'WikimediaBlogVisit',
				revision: 5308166,
				webHost: window.location.hostname,
				wiki: 'blog',
				event: {
					requestUrl: url,
				}
			};

			if ( document.referrer ) {
				capsule[ 'event' ][ 'referrerUrl' ] = urlStripProto( document.referrer );
			}
			var beacon = document.createElement( 'img' );
			beacon.src = '//bits.wikimedia.org/event.gif?' + encodeURIComponent( $.toJSON( capsule ) ) + ';';
		}, 0 );
	} );
}(jQuery));
