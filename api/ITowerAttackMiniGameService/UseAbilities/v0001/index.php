<?php
	if( !isset( $_POST[ 'format' ] ) || $_POST[ 'format' ] !== 'json' )
	{
		http_response_code( 400 );
		die;
	}
	
	header( 'Content-type: application/json' );
	echo file_get_contents( 'UseAbilities.json' );
