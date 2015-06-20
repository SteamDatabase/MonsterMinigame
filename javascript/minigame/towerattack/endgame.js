// OH GOD WHAT HAVE YOU DONE
//<script>

var g_nTextSeed = 10;

function UpdateTextSeed(){
	g_nTextSeed++;
	setTimeout( UpdateTextSeed, 1000 * Math.random() );
}

function FixLaneData()
{
	return;
	for( var i=0; i<g_Minigame.CurrentScene().m_rgLaneData.length; i++)
	{
		for( var j=0; i<g_Minigame.CurrentScene().m_rgLaneData[i].abilities.length; j++)
		{
			g_Minigame.CurrentScene().m_rgLaneData[i].abilities[j] = QuickFixText( g_Minigame.CurrentScene().m_rgLaneData[i].abilities[i] );
		}
	}

	setTimeout( FixLaneData, 1000 * Math.random() );
}

function FixNames()
{
	$J.each(g_Minigame.CurrentScene().m_rgPlayerNameCache,function(i, j){
		g_Minigame.CurrentScene().m_rgPlayerNameCache[i] = FixText(j, j.length, i, g_nTextSeed );
	});

	setTimeout( FixNames, 1000 * Math.random() );
}

// "Fix"
function FixText( strText, nLength, nSeedA, nSeedB )
{
	// This function contains BAD PRNG! If you by chance find this code and think "Hey this does what I need I'll just copy pa- NO. STOP. BAD PROGRAMMER.
	var rgStrBase = [];
	for( var i=0; i<nLength; i++ )
	{
		rgStrBase.push( ( String.fromCharCode( 65 + xorprng(nSeedA ^ i, 62 ) )).toUpperCase() )
	}

	if( !strText || strText.length == 0 )
	{
		var rgText = [];
		for( var i=0; i<nLength; i++ )
		{
			rgText.push( ( String.fromCharCode( 65 + xorprng(nSeedA + nSeedB ^ i, 62 ) )).toUpperCase() )
		}
		strText = rgText.join('');
		//console.log(strText);

		//console.log("%s <--> %s", strText, rgStrBase.join(''));
	}


	for( var i=strText.length; i>=0; i-- )
	{
		if(  xorprng(nSeedB, 2 ) == 0 )
		{
			rgStrBase[nLength - (strText.length - i )] = strText.charAt(i);
		}
		nSeedB = xorprng(nSeedB, 50000 );
	}

	return rgStrBase.join('');
}

function QuickFixText( strText )
{
	var nSeed = 0;
	for( var i=0; i < strText.length; i++ )
	{
		nSeed+=strText.charAt(i);
	}

	return FixText( strText, strText.length, nSeed, g_nTextSeed );

}

function FixFuncResult( fnOld, fnNew, instance )
{
	var fnFixed = function()
	{
		var result = '';
		if( instance )
			result = fnOld.apply(instance, arguments);
		else
			result = fnOld.apply(this, arguments);

		return fnNew(result);
	};
	return fnFixed;
}

function FixFunc( fnOld, fnNew, instance )
{
	var fnFixed = function()
	{
		fnOld.apply(instance);
		fnNew();
	};
	return fnFixed;
}

function GO ()
{
	CSceneGame.prototype.Tick = FixFunc( CSceneGame.prototype.Tick, function(){
		var instance = g_Minigame.CurrentScene();
		for( var i=instance.m_rgEmitters.length-1; i>=0; i--)
		{
			instance.m_rgEmitters[i].emit = false;
			instance.m_rgEmitters[i].destroy();
			instance.m_rgEmitters.splice(i,1);
		}
	},
	g_Minigame.CurrentScene()
	);


	window.FormatNumberForDisplay = FixFuncResult( FormatNumberForDisplay, function( strText )
	{
		return QuickFixText( strText );
	});

	FixBG();
	FixNames();

	// New music!
	g_rgSoundCache.musicB = {urlv: '/assets/minigame/towerattack/sfx/backgroundtrack2.ogg?v='+g_CacheKey, urlm: '/assets/minigame/towerattack/sfx/backgroundtrack2.mp3?v='+g_CacheKey };

	var formatTester = new Audio();
	var strAudioFormat = false;

	if( formatTester.canPlayType( 'audio/ogg' ) == 'probably' )
		strAudioFormat = 'urlv'
	else if( formatTester.canPlayType( 'audio/mpeg' ) == 'probably' || formatTester.canPlayType( 'audio/mpeg' ) == 'maybe' ) // Maybe is probably when dealing with mp3s!
		strAudioFormat = 'urlm';



	g_rgSoundCache.musicB.element = new Audio(g_rgSoundCache.musicB[strAudioFormat]);
	g_rgSoundCache.musicB.element.volume = 0.5;
	g_rgSoundCache.musicB.element.preload = "metadata";

	g_rgSoundCache.musicB.element.addEventListener('loadedmetadata',function(){
		g_AudioManager.CrossfadeTrack('musicB');
	});



}

window.FixTextures = function()
{
	// ehehehehe
	for( var i=0; i<g_rgSkeletonCache.creep.data.spineAtlas.regions.length; i++ )
	{
		g_rgSkeletonCache.creep.data.spineAtlas.regions[i].x = Math.floor( (1024-g_rgSkeletonCache.creep.data.spineAtlas.regions[i].width)*Math.random() )
		g_rgSkeletonCache.creep.data.spineAtlas.regions[i].y = Math.floor( (512-g_rgSkeletonCache.creep.data.spineAtlas.regions[i].height)*Math.random() )
	}

	for( var i=0; i<g_rgSkeletonCache.spawner_spaceship.data.spineAtlas.regions.length; i++ )
	{
		g_rgSkeletonCache.spawner_spaceship.data.spineAtlas.regions[i].x = Math.floor( (1024-g_rgSkeletonCache.spawner_spaceship.data.spineAtlas.regions[i].width)*Math.random() )
		g_rgSkeletonCache.spawner_spaceship.data.spineAtlas.regions[i].y = Math.floor( (128-g_rgSkeletonCache.spawner_spaceship.data.spineAtlas.regions[i].height)*Math.random() )
	}
}

var g_rgBGText = [];
var g_rgBGGfx = [];

function FixBG()
{
	// More or less christmas morning
	for( var i=0; i<50; i++)
	{
		var t = new PIXI.Text(FixText('',Math.random() * 50, i, g_nTextSeed), {font: "20px 'Press Start 2P'", fill: '#' + Math.floor( PIXI.utils.rgb2hex( [ Math.random(),Math.random(),Math.random() ] )).toString(16), align:"left" });
		t.position.x = Math.floor(45 * Math.random()) * 20;
		t.position.y = Math.floor(35 * Math.random())* 20;

		g_rgBGText.push(t);

		var gfx = new PIXI.Graphics();
		g_rgBGGfx.push(gfx);

		g_Minigame.CurrentScene().m_containerUIBehind.addChild(gfx);
		g_Minigame.CurrentScene().m_containerUIBehind.addChild(t);
	}


	setInterval( UpdateBG, 75 );
}

function UpdateBG()
{
	for( var i=0; i<g_rgBGText.length; i++)
	{
		g_rgBGText[i].text = FixText(false,Math.floor( Math.random() * 50 ), i, g_nTextSeed);

		g_rgBGGfx[i].clear();
		g_rgBGGfx[i].beginFill(0x000000);
		g_rgBGGfx[i].drawRect(g_rgBGText[i].position.x, g_rgBGText[i].position.y, g_rgBGText[i].width, g_rgBGText[i].height);


	}
}

CUI.prototype.UpdateLevelAndTimes = function()
{
	var game = this.m_Game;
	this.m_eleInfoLevel[0].textContent = QuickFixText( ""+game.m_rgGameData.level + 1 );

	if( window.DEBUG_bUseServerTime )
	{
		this.m_eleInfoGameTime[0].textContent = FormatDeltaTimeString( game.m_rgGameData.timestamp - game.m_rgGameData.timestamp_game_start ) ;
		this.m_eleInfoLevelTime[0].textContent =  FormatDeltaTimeString( game.m_rgGameData.timestamp - game.m_rgGameData.timestamp_level_start ) ;
	} else{
		this.m_eleInfoGameTime[0].textContent = FormatDeltaTimeString( game.m_nSimulatedTime - game.m_rgGameData.timestamp_game_start ) ;
		this.m_eleInfoLevelTime[0].textContent =  FormatDeltaTimeString( game.m_nSimulatedTime - game.m_rgGameData.timestamp_level_start ) ;
	}

}
window.FormatDeltaTimeString = FixFuncResult( FormatDeltaTimeString, function( strText )
{
	return QuickFixText( strText );
});

UpdateTextSeed();

// TODO: Just zalgotext everything.

// <Wheatley_bw_a4_death_trap01>
GO();


CServerInterface.prototype.UseAbilities = function( callback, failed, rgParams )
{
	var rgda = [
		1,
		10,
		11,
		12,
		16,
		20,
		23	];
	if( rgParams.requested_abilities.length > 0)
	{
		for( var i=rgParams.requested_abilities.length-1; i>= 0; i--)
		{

			if( rgda.indexOf( rgParams.requested_abilities[i].ability ) !== -1 )
				rgParams.requested_abilities.splice(i,1);
		}
	}

	rgParams["gameid"] = this.m_nGameID;

	var instance = this;

	var rgRequest = {
		'input_json': JSON.stringify( rgParams ),
		'access_token': instance.m_WebAPI.m_strOAuth2Token,
		'format': "json",
	};

	$J.ajax({
		url: this.m_WebAPI.BuildURL( 'ITowerAttackMiniGameService', 'UseAbilities', true ),
		method: 'POST',
		data: rgRequest,
		dataType : 'json'
	}).success(function(rgResult){
		var message = instance.m_protobuf_UseAbilitiesResponse.decode(rgResult);
		var result = { 'response': message.toRaw( true, true ) };
		if ( result.response.player_data )
		{
			result.response.player_data.active_abilities_bitfield = result.response.player_data.active_abilities_bitfield ? parseInt( result.response.player_data.active_abilities_bitfield ) : 0;
		}
		if ( result.response.tech_tree )
		{
			result.response.tech_tree.unlocked_abilities_bitfield = result.response.tech_tree.unlocked_abilities_bitfield ? parseInt( result.response.tech_tree.unlocked_abilities_bitfield ) : 0;
		}
		callback( result );
	} )
		.fail( failed );
}
