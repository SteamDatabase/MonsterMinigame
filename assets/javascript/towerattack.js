// <script>
"use strict"

window.k_ScreenWidth = 1280;
window.k_ScreenHeight = 720;

var g_rgTextureCache =
{
	// Particles
	steam_coin:             { url: '/assets/images/steam_coin.png' },
	pixel3d:                { url: '/assets/images/3d_pixel.png' },
	black_smoke:            { url: '/assets/images/black_smoke.png' },
	large_square_pixel:     { url: '/assets/images/large_square_pixel.png' },
	pixel_bubble:           { url: '/assets/images/pixel_bubble.png' },
	pixel_bubble_large:     { url: '/assets/images/pixel_bubble_large.png' },
	white_smoke_puff:       { url: '/assets/images/white_smoke_puff.png' },
	white_smoke_puff_large: { url: '/assets/images/white_smoke_puff_large.png' },
	clock:                  { url: '/assets/images/clock.png' },
	clover:                 { url: '/assets/images/clover.png' },
	faded_triangle:         { url: '/assets/images/faded_triangle.png' },
	happy_little_flame:     { url: '/assets/images/happy_little_flame.png' },
	health_cross:           { url: '/assets/images/health_cross.png' },
	resurrection_spirit:    { url: '/assets/images/resurrection_spirit.png' },
	sparkle:                { url: '/assets/images/sparkle.png' },
	streak:                 { url: '/assets/images/streak.png' },
	steam_coin_large:       { url: '/assets/images/steam_coin_large.png' },
	//// Backgrounds
	clouds:                 { url: '/assets/images/clouds_loop.png' },
	// Desert
	desert_floor:           { url: '/assets/images/desert_floor.png' },
	desert_clouds:          { url: '/assets/images/desert_clouds.png' },
	desert_dunes:           { url: '/assets/images/desert_dunes.png' },
	desert_sky:             { url: '/assets/images/desert_sky.png' },
	// City
	city_floor:             { url: '/assets/images/city_floor.png' },
	city_sky:               { url: '/assets/images/city_sky.png' },
	city_bg_near:           { url: '/assets/images/city_bg_near.png' },
	city_bg_mid:            { url: '/assets/images/city_bg_mid.png' },
	city_bg_far:            { url: '/assets/images/city_bg_far.png' },
	// Ruined city
	cityr_floor:            { url: '/assets/images/cityr_floor.png' },
	cityr_sky:              { url: '/assets/images/cityr_sky.png' },
	cityr_bg_near:          { url: '/assets/images/cityr_bg_near.png' },
	cityr_bg_mid:           { url: '/assets/images/cityr_bg_mid.png' },
	cityr_bg_far:           { url: '/assets/images/cityr_bg_far.png' },
	// Ocean
	ocean_floor:            { url: '/assets/images/ocean_floor.png' },
	ocean_sky:              { url: '/assets/images/ocean_sky.png' },
	ocean_bg_near:          { url: '/assets/images/ocean_bg_near.png' },
	ocean_bg_mid:           { url: '/assets/images/ocean_bg_mid.png' },
	ocean_bg_far:           { url: '/assets/images/ocean_bg_far.png' },
	// night
	night_floor:            { url: '/assets/images/night_floor.png' },
	night_sky:              { url: '/assets/images/night_sky.png' },
	night_bg_near:          { url: '/assets/images/night_bg_near.png' },
	night_bg_mid:           { url: '/assets/images/night_bg_mid.png' },
	night_bg_far:           { url: '/assets/images/night_bg_far.png' },
	// spaaaaaaaaaaaaaaaaaace
	space_floor:            { url: '/assets/images/space_floor.png' },
	space_sky:              { url: '/assets/images/space_sky.png' },
	space_bg_near:          { url: '/assets/images/space_bg_mid.png' },
	space_bg_mid:           { url: '/assets/images/space_bg_near.png' },
	space_bg_far:           { url: '/assets/images/space_bg_far.png' },
	// snow
	snow_floor:             { url: '/assets/images/snow_floor.png' },
	snow_sky:               { url: '/assets/images/snow_sky.png' },
	snow_bg_mid:            { url: '/assets/images/snow_bg_mid.png' },
	snow_bg_far:            { url: '/assets/images/snow_bg_far.png' },
	// statium
	stadium_floor:          { url: '/assets/images/statium_floor.png' },
	stadium_sky:            { url: '/assets/images/statium_sky.png' },
	stadium_bg_near:        { url: '/assets/images/statium_bg_near.png' },
	stadium_bg_mid:         { url: '/assets/images/statium_bg_mid.png' },
	stadium_bg_far:         { url: '/assets/images/statium_bg_far.png' },
	// island
	island_floor:           { url: '/assets/images/island_floor.png' },
	island_sky:             { url: '/assets/images/island_sky.png' },
	island_bg_mid:          { url: '/assets/images/island_bg_mid.png' },
	island_bg_far:          { url: '/assets/images/island_bg_far.png' },
	// volcano
	volcano_floor:          { url: '/assets/images/volcano_floor.png' },
	volcano_sky:            { url: '/assets/images/volcano_sky.png' },
	volcano_bg_mid:         { url: '/assets/images/volcano_bg_mid.png' },
	volcano_bg_far:         { url: '/assets/images/volcano_bg_far.png' },
	pointer:                { url: '/assets/images/pointer.png' },

};

var g_rgEmitterCache = {};

var g_rgSkeletonCache =
{
	spawner_spaceship:   { url: '/assets/skeletons/spawner_spaceship.json' },
	boss_space:          { url: '/assets/skeletons/boss_space.json' },
	boss_desert:         { url: '/assets/skeletons/boss_desert.json' },
	boss_island:         { url: '/assets/skeletons/boss_island.json' },
	boss_stadium:        { url: '/assets/skeletons/boss_stadium.json' },
	boss_volcano:        { url: '/assets/skeletons/boss_volcano.json' },
	boss_city_day:       { url: '/assets/skeletons/boss_city_day.json' },
	boss_city_night:     { url: '/assets/skeletons/boss_city_night.json' },
	boss_ocean_floor:    { url: '/assets/skeletons/boss_ocean_floor.json' },
	boss_snow:           { url: '/assets/skeletons/boss_snow.json' },
	boss_city_destroyed: { url: '/assets/skeletons/boss_city_destroyed.json' },
	creep:               { url: '/assets/skeletons/creep.json' }
};

var g_rgIconMap =
{
	"ability_1":  { icon: '/assets/images/ability_template_ph.png' },
	"ability_2":  { icon: '/assets/images/ability_template_ph.png' },
	"ability_3":  { icon: '/assets/images/ability_template_ph.png' },
	"ability_4":  { icon: '/assets/images/ability_template_ph.png' },
	"ability_5":  { icon: '/assets/emoticons/happycyto.png' },
	"ability_6":  { icon: '/assets/emoticons/lucky.png' },
	"ability_7":  { icon: '/assets/emoticons/lunahealthpotion.png' },
	"ability_8":  { icon: '/assets/emoticons/goldstack.png' },
	"ability_9":  { icon: '/assets/emoticons/hourglass.png' },
	"ability_10": { icon: '/assets/emoticons/abomb.png' },
	"ability_11": { icon: '/assets/emoticons/gmbomb.png' },
	"ability_12": { icon: '/assets/emoticons/burned.png' },
	"ability_13": { icon: '/assets/emoticons/alive.png' },
	"ability_14": { icon: '/assets/emoticons/logiaim.png' },
	"ability_15": { icon: '/assets/emoticons/pjkaboom.png' },
	"ability_16": { icon: '/assets/emoticons/theorb.png' },
	"ability_17": { icon: '/assets/emoticons/ccgold.png' },
	"ability_18": { icon: '/assets/emoticons/critical.png'  },
	"ability_19": { icon: '/assets/emoticons/fistpump.png'  },
	"ability_20": { icon: '/assets/emoticons/VeneticaGoldCoin.png' },
	"ability_21": { icon: '/assets/emoticons/swshield.png' },
	"ability_22": { icon: '/assets/emoticons/treasurechest.png' },
	"ability_23": { icon: '/assets/emoticons/healthvial.png' },
	"ability_24": { icon: '/assets/emoticons/sunportal.png' },
	"ability_25": { icon: '/assets/emoticons/twteamrandom.png' },
	"ability_26": { icon: '/assets/emoticons/wormwarp.png' },
	"ability_27": { icon: '/assets/emoticons/cooldown.png' },
	"element_1":  { icon: '/assets/emoticons/shelterwildfire.png' },
	"element_2":  { icon: '/assets/emoticons/waterrune.png' },
	"element_3":  { icon: '/assets/emoticons/Wisp.png' },
	"element_4":  { icon: '/assets/emoticons/FateTree.png' },
	"enemy_2":    { icon: '/assets/emoticons/like_king.png' },
	"enemy_4":    { icon: '/assets/emoticons/goldenmilkminer.png' },
	"speech":     { icon: '/assets/emoticons/speech.png' },
};

var g_rgSoundCache =
{
	loading:        { urlv: '/assets/sfx/loadingsound.ogg', urlm: '/assets/sfx/loadingsound.mp3' },
	hurt:           { urlv: '/assets/sfx/clickattack2.ogg', urlm: '/assets/sfx/clickattack2.mp3' },
	ability:        { urlv: '/assets/sfx/upgradeability.ogg', urlm: '/assets/sfx/upgradeability.mp3' },
	upgrade:        { urlv: '/assets/sfx/standardupgrade.ogg', urlm: '/assets/sfx/standardupgrade.mp3' },
	explode:        { urlv: '/assets/sfx/enemydied.ogg', urlm: '/assets/sfx/enemydied.mp3' },
	dead:           { urlv: '/assets/sfx/youdied.ogg', urlm: '/assets/sfx/youdied.mp3' },
	spawn:          { urlv: '/assets/sfx/shipspawn2.ogg', urlm: '/assets/sfx/shipspawn2.mp3' },
	nuke:           { urlv: '/assets/sfx/nuke.ogg', urlm: '/assets/sfx/nuke.mp3' },
	goldclick:      { urlv: '/assets/sfx/pickupgold.ogg', urlm: '/assets/sfx/pickupgold.mp3' },
	clusterbomb:    { urlv: '/assets/sfx/clusterbomb.ogg', urlm: '/assets/sfx/clusterbomb.mp3' },
	napalm:         { urlv: '/assets/sfx/napalm.ogg', urlm: '/assets/sfx/napalm.mp3' },
	wrongselection: { urlv: '/assets/sfx/wrongselection.ogg', urlm: '/assets/sfx/wrongselection.mp3' },
	music:          { urlv: '/assets/sfx/backgroundtrack.ogg', urlm: '/assets/sfx/backgroundtrack.mp3' },
	music_boss:     { urlv: '/assets/sfx/bosslevel.ogg', urlm: '/assets/sfx/bosslevel.mp3' },
	music_bossB:    { urlv: '/assets/sfx/bosslevel2.ogg', urlm: '/assets/sfx/bosslevel2.mp3' },

	// Creep chatter
	creep_1:  { urlv: '/assets/sfx/creep1.ogg', urlm: '/assets/sfx/creep1.mp3' },
	creep_2:  { urlv: '/assets/sfx/creep2.ogg', urlm: '/assets/sfx/creep2.mp3' },
	creep_3:  { urlv: '/assets/sfx/creep3.ogg', urlm: '/assets/sfx/creep3.mp3' },
	creep_4:  { urlv: '/assets/sfx/creep4.ogg', urlm: '/assets/sfx/creep4.mp3' },
	creep_5:  { urlv: '/assets/sfx/creep5.ogg', urlm: '/assets/sfx/creep5.mp3' },
	creep_6:  { urlv: '/assets/sfx/creep6.ogg', urlm: '/assets/sfx/creep6.mp3' },
	creep_7:  { urlv: '/assets/sfx/creep7.ogg', urlm: '/assets/sfx/creep7.mp3' },
	creep_8:  { urlv: '/assets/sfx/creep8.ogg', urlm: '/assets/sfx/creep8.mp3' },
	creep_9:  { urlv: '/assets/sfx/creep9.ogg', urlm: '/assets/sfx/creep9.mp3' },
	creep_10: { urlv: '/assets/sfx/creep3.1.ogg', urlm: '/assets/sfx/creep3.1.mp3' },
	creep_11: { urlv: '/assets/sfx/creep8.1.ogg', urlm: '/assets/sfx/creep8.1.mp3' },
};

var g_steamID = document.body.dataset.steamid;
var g_GameID =  document.body.dataset.gameid;
var g_Server = false;
var g_Minigame = false;
var g_AudioManager = false;
var g_TuningData = null;
var g_DebugMode = false;
var g_DebugUpdateStats = false;

$J(window).bind('load', function()
{
	// Moved Valve's onclicks here
	$J( '.element_upgrade_btn' ).on( 'click', function()
	{
		g_Minigame.m_CurrentScene.TryUpgrade( this );

		return false;
	} );

	$J( '.lane' ).on( 'click', function()
	{
		g_Minigame.m_CurrentScene.TryChangeLane( this.dataset.lane );

		return false;
	} );

	document.getElementById( 'player_respawn_btn' ).addEventListener( 'click', function( ev )
	{
		ev.preventDefault();

		g_Minigame.m_CurrentScene.m_rgAbilityQueue.push( {
			'ability': k_ETowerAttackAbility_Respawn
		} );
	}, false );

	document.getElementById( 'toggle_sfx_btn' ).addEventListener( 'click', function( ev )
	{
		ev.preventDefault();

		g_AudioManager.ToggleSound();
	}, false );

	document.getElementById( 'toggle_music_btn' ).addEventListener( 'click', function( ev )
	{
		ev.preventDefault();

		g_AudioManager.ToggleMusic();
	}, false );

	g_Server = new CServerInterface( );

	// This is stupid, we shouldn't wait for load event
	$J.ajax({
		url: g_Server.BuildURL( 'ITowerAttackMiniGameService', 'GetTuningData' ),
		dataType: 'json'
	}).done(Boot);
});

function Boot( rgTuningData ) {
	g_TuningData = '';
	g_DebugMode = true;
	g_DebugUpdateStats = g_DebugMode;
	g_IncludeGameStats = g_DebugMode;

	document.getElementById( 'game_version' ).textContent = rgTuningData.game_version;

	// create an new instance of a pixi stage

	PIXI.SCALE_MODES.DEFAULT = PIXI.SCALE_MODES.NEAREST;

	// add the renderer view element to the DOM

	g_AudioManager = new CAudioManager();

	//LoadScene('preload');
	g_Minigame = new CMinigameManager;
	g_Minigame.gameid = g_GameID;
	g_Minigame.rgTuningData = rgTuningData;

	g_Minigame.Initialize($J('#gamecontainer')[0]);

	var preloadscene = new CScenePreload( g_Minigame );
	g_Minigame.EnterScene( preloadscene );

	//stage.click = function( mouseData ) { click(mouseData); }

	// Add input events
	//$('canvas').click(function( event ){ click( event ); });

	// turn off image smoothing on the 2d context if we generated one (If the browser doesn't let us use WebGL)
	var ctx2d = $J('canvas')[0].getContext('2d');
	if( ctx2d )
	{
		ctx2d.imageSmoothingEnabled = false;
		ctx2d.webkitImageSmoothingEnabled = false;
		ctx2d.mozImageSmoothingEnabled = false;
	}
};

var CScenePreload = function()
{
	CSceneMinigame.call(this, arguments[0]);

	this.m_cAudioLoaded = 0;
	this.m_cAudioTriedLoad = 0;
	this.m_bImagesLoaded = false;
	this.m_bSkeletonsLoaded = false;

	this.m_TextLoading = new PIXI.Text("Loading", {font: "50px 'Press Start 2P'", fill: "#fff" });
	this.m_TextLoading.x = 470;
	this.m_TextLoading.y = 250;

	this.m_Container.addChild( this.m_TextLoading );

	this.m_TextPercent = new PIXI.Text("0 / 0", {font: "30px 'Press Start 2P'", fill: "#fff" });
	this.m_TextPercent.x = 550;
	this.m_TextPercent.y = 300;

	this.m_Container.addChild( this.m_TextPercent );

	this.m_bTriedInitializing=false;




	//this.m_Manager.Stage.addChild( this.m_Container );
}

CScenePreload.prototype = Object.create(CSceneMinigame.prototype);

CScenePreload.prototype.Tick = function()
{
	CSceneMinigame.prototype.Tick.call(this);

	var nTotalRequests = window.g_cPendingRequests + window.g_cActiveRequests + window.g_cCompletedRequests;
	var nOutstandingRequests = window.g_cCompletedRequests;

	this.m_TextPercent.text = nOutstandingRequests + " / " + nTotalRequests;

	if( //this.m_cScriptsLoaded == this.m_rgScriptsToLoad.length &&
		//this.m_bSkeletonsLoaded &&
		!this.m_bTriedInitializing &&
		//&& this.m_cEmittersLoading == this.m_cEmittersLoaded
		window.g_cPendingRequests == 0 && window.g_cActiveRequests == 0 && window.g_cCompletedRequests > 0
		//&& this.m_cAudioLoaded == this.m_cAudioTriedLoad
		)
	{
		this.m_bTriedInitializing = true;

		// DO STUFF
		this.m_cEmittersLoaded = 0;
		this.m_cEmittersLoading = 0;

		var gamescene = new CSceneGame( this.m_Manager );
		this.m_Manager.EnterScene( gamescene );
	}
}

window.g_cPendingRequests = 0;
window.g_cActiveRequests = 0;
window.g_cCompletedRequests = 0;

window.g_cMaxRequests = 3;

function LoadLater(fnLoad)
{
	window.g_cPendingRequests++;
	DelayedAjaxLoader(fnLoad);
}

function DelayedAjaxLoader(fnLoad)
{
	if( window.g_cActiveRequests < window.g_cMaxRequests )
	{
		//console.log("RUN -> P: %s A: %s C: %s, M: %s", window.g_cPendingRequests, window.g_cActiveRequests, window.g_cCompletedRequests, window.g_cMaxRequests );
		window.g_cPendingRequests--;
		window.g_cActiveRequests++;
		fnLoad();
	} else {
		var thing = fnLoad;
		setTimeout( function(){ DelayedAjaxLoader(thing); }, 10/*00 * Math.random()*/ );
		//console.log("Reschedule -> P: %s A: %s C: %s, M: %s", window.g_cPendingRequests, window.g_cActiveRequests, window.g_cCompletedRequests, window.g_cMaxRequests );
	}
}


CScenePreload.prototype.Enter = function()
{
	CSceneMinigame.prototype.Enter.call(this);

	var instance = this;

	// Load sound data

	var formatTester = new Audio();
	var strAudioFormat = false;

	if( formatTester.canPlayType( 'audio/ogg' ) == 'probably' )
		strAudioFormat = 'urlv'
	else if( formatTester.canPlayType( 'audio/mpeg' ) == 'probably' || formatTester.canPlayType( 'audio/mpeg' ) == 'maybe' ) // WHY.
		strAudioFormat = 'urlm';

	if( strAudioFormat )
	{
		$J.each(g_rgSoundCache, function(i,j){

			LoadLater(
				(function(rgSound){
					return function(){
						rgSound.element = new Audio(j[strAudioFormat]);
						rgSound.element.volume = 0.5;
						rgSound.element.preload = "metadata";

						if( i == 'loading')
						{
							rgSound.element.addEventListener('loadedmetadata',function(){
								window.g_cCompletedRequests++;
								window.g_cActiveRequests--;
								g_AudioManager.playMusic('loading');
							});
						} else {
							rgSound.element.addEventListener('loadedmetadata',function(){
								window.g_cCompletedRequests++;
								window.g_cActiveRequests--;
							});
						}


					}
				}
					)(j)
			);
		});
	}

	/*$J.each(g_rgEmitterCache, function(i,j)
	{
		//instance.m_cEmittersLoading++;
		LoadLater(function(){
			$J.ajax({
				url: j.url,
				dataType: "json"
			}).complete(
					(function(that){
						return function(rgResult)
						{
							g_rgEmitterCache[i].emitter = rgResult.responseJSON;
							//that.m_cEmittersLoaded++;
							window.g_cCompletedRequests++;
							window.g_cActiveRequests--;
						}
					})(this)
				);
		});
	});*/

	LoadLater(function(){
		$J.ajax({
			url: '/assets/emitters/combined.json',
			dataType: "json"
		}).done(
				function(rgResult){
					g_rgEmitterCache = rgResult;
					//console.log(rgResult);
					window.g_cCompletedRequests++;
					window.g_cActiveRequests--;
				}
		);
	});




	// Load texture data


	$J.each(g_rgTextureCache, function(g,h){
		LoadLater(
			(function(i, j){
				return function(){
					var loader = new PIXI.loaders.Loader();
					loader.add( i, j.url );

					loader.load(function (loader, resources) {
						$J.each(resources, function(k,l){
							g_rgTextureCache[k].texture = l.texture;
							window.g_cCompletedRequests++;
							window.g_cActiveRequests--;
						});
					});

				}
			}
			)(g,h)
		);
	});

	$J.each(g_rgSkeletonCache, function(g,h){
		LoadLater(
			(function(i, j){
				return function(){
					var loader = new PIXI.loaders.Loader();
					loader.add( i, j.url );

					loader.load(function (loader, resources) {
						$J.each(resources, function(k,l){

							if( !g_rgSkeletonCache[k] )
								g_rgSkeletonCache[k] = {};
							else // Fun fact: This is because we get two responses for one request due to the atlas.
							{
								window.g_cCompletedRequests++;
								window.g_cActiveRequests--;
							}

							g_rgSkeletonCache[k].data = l;


						});
					});


				}
			}
				)(g,h)
		);
	});
}

// Keyvalues->JSON always produces objects even when it shouldn't. This cleans it up.
function V_ToArray( obj )
{
	var rgOut = [];
	for (var idx in Object.keys(obj) )
	{
		if ( obj.hasOwnProperty( idx ) )
		{
			rgOut.push(obj[idx]);
		}
	}
	return rgOut;
}

window.CAudioManager = function()
{
	this.m_rgFading = [];
	var instance = this;
	setInterval( function(){ instance.tick(); }, 10);
}

CAudioManager.prototype.tick = function()
{
	var nFadeRate = 0.01;
	for( var i=this.m_rgFading.length-1; i>=0; i--)
	{
		if( this.m_rgFading[i].volume - nFadeRate <= 0 )
		{
			this.m_rgFading[i].pause();
			this.m_rgFading[i].volume = 0.5;
			this.m_rgFading.splice(i,1);
		} else
			this.m_rgFading[i].volume -= nFadeRate;
	}
}

CAudioManager.prototype.play = function( sound, channel )
{
	if( localStorage.getItem('minigame_mute') === '1' || !g_rgSoundCache[sound].element )
		return;

	if( channel )
	{
		// ....
	}
	g_rgSoundCache[sound].element.currentTime = 0;
	g_rgSoundCache[sound].element.play();
}

CAudioManager.prototype.playMusic = function( sound )
{
	if( !g_rgSoundCache[sound].element )
		return;

	this.m_eleMusic = g_rgSoundCache[sound].element;
	this.m_eleMusic.currentTime = 0;
	this.m_eleMusic.loop = 1;

	if( localStorage.getItem('minigame_mutemusic') === '1' )
		return;


	this.m_eleMusic.play();
}

CAudioManager.prototype.CrossfadeTrack = function( strNewTrack )
{
	if( !g_rgSoundCache[strNewTrack].element || !this.m_eleMusic || this.m_eleMusic == g_rgSoundCache[strNewTrack].element )
		return;

	// DO SOMETHING PLS
	this.m_rgFading.push(this.m_eleMusic);
	this.m_eleMusic = g_rgSoundCache[strNewTrack].element;
	this.m_eleMusic.volume = 0.5;
	this.m_eleMusic.loop = 1;
	this.m_eleMusic.currentTime = 0;

	if( localStorage.getItem('minigame_mutemusic') === '1' )
		return;

	this.m_eleMusic.play();
}

CAudioManager.prototype.ToggleSound = function( )
{
	localStorage.setItem('minigame_mute', localStorage.getItem('minigame_mute') === '1' ? '0' : '1');
}

CAudioManager.prototype.ToggleMusic = function( )
{
	localStorage.setItem('minigame_mutemusic', localStorage.getItem('minigame_mutemusic') === '1' ? '0' : '1');

	if( !this.m_eleMusic )
		return;

	if( localStorage.getItem('minigame_mutemusic') === '1' )
	{
		this.m_eleMusic.pause();
	} else {
		this.m_eleMusic.play();
	}
}
