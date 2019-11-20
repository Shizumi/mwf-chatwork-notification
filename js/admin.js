jQuery( function ( $ ) {
	$( '.room_select' ).select2();

	$( '#api_token' ).on( 'change', function () {

		$.ajax( {
			type: 'POST',
			url: get_cn_user.endpoint,
			dataType: 'json',
			data: {
				action: get_cn_user.action,
				token: $( this ).val(),
			}
		} ).done( function ( data ) {

			setChatworkName( data.name );
			document.getElementById( 'cn_name_input' ).value = data.name;

			var roomSelect = document.getElementById( 'room_id' );
			$( '#room_id' ).children().remove();
			for ( room in data.rooms ) {
				var option = document.createElement( 'option' );
				option.value = data.rooms[ room ].room_id;
				option.textContent = data.rooms[ room ].name;

				roomSelect.appendChild( option );
			}
		} ).fail( function ( result ) {
			console.log( result );
			setChatworkName( '正しく情報が取得できませんでした。' );
		} );
	} );

	function setChatworkName( str ) {
		document.getElementById( 'chatwork_name' ).textContent = str;
	}
} );
