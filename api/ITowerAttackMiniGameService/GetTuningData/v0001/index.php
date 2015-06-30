<?php
	header( 'Content-type: application/json' );

	// dem ../
	echo file_get_contents( __DIR__ . '/../../../../php/files/tuningData.json' );
