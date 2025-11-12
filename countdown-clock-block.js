
( function( blocks, element ) {
    var el = element.createElement;
    blocks.registerBlockType( 'countdown/clock', {
        title: 'Countdown Clock',
        icon: 'clock',
        category: 'widgets',
        attributes: {
            id: { type: 'string' }
        },
        edit: function( props ) {
            return el('div', {}, 'Countdown Clock Block – Use shortcode in frontend');
        },
        save: function() {
            return null;
        }
    } );
} )( window.wp.blocks, window.wp.element );
