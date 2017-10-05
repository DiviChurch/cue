/*global MediaElementPlayer:false, mejs:false */

(function() {
	var settings = window._wpmejsSettings || {};
	settings.features = settings.features || mejs.MepDefaults.features;
	settings.features.push( 'cuewpmediaelement' );

	MediaElementPlayer.prototype.buildcuewpmediaelement = function( player, controls, layers ) {
		var $container = $( player.container ),
			$controls = $( controls ),
			$layers = $( layers );

		if ( ! $container.hasClass( 'wp-audio-shortcode' ) ) {
			return;
		}

		var data = JSON.parse( $container.next( 'script' ).html() );
		$container.addClass( 'cue-skin-' + data.theme );

		$layers.append( '<div class="mejs-track-details"><span class="mejs-track-artist"></span><span class="mejs-track-title"></span></div>' );
		$layers.find( '.mejs-track-artist' ).text( data.artist );
		$layers.find( '.mejs-track-title' ).text( data.title );

		$( player.media ).on( 'play pause', function() {
			$container.toggleClass( 'is-playing', ! player.media.paused );
		});

		$container.on( 'controlsresize', function() {
			$controls.find( '.mejs-time-rail, .mejs-time-slider' ).css( 'width', '' );
		});

		$container.addClass(function() {
			return ( 'ontouchstart' in window ) || window.DocumentTouch && document instanceof window.DocumentTouch ? 'touch' : 'no-touch';
		});
	};
})();
