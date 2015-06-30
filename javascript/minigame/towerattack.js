// <script>
"use strict"

window.k_ScreenWidth = 1280;
window.k_ScreenHeight = 720;

var g_rgTextureCache =
{
	// Particles
	steam_coin:             { url: '/assets/minigame/towerattack/steam_coin.png' },
	pixel3d:                { url: '/assets/minigame/towerattack/3d_pixel.png' },
	black_smoke:            { url: '/assets/minigame/towerattack/black_smoke.png' },
	large_square_pixel:     { url: '/assets/minigame/towerattack/large_square_pixel.png' },
	pixel_bubble:           { url: '/assets/minigame/towerattack/pixel_bubble.png' },
	pixel_bubble_large:     { url: '/assets/minigame/towerattack/pixel_bubble_large.png' },
	white_smoke_puff:       { url: '/assets/minigame/towerattack/white_smoke_puff.png' },
	white_smoke_puff_large: { url: '/assets/minigame/towerattack/white_smoke_puff_large.png' },
	clock:                  { url: '/assets/minigame/towerattack/clock.png' },
	clover:                 { url: '/assets/minigame/towerattack/clover.png' },
	faded_triangle:         { url: '/assets/minigame/towerattack/faded_triangle.png' },
	happy_little_flame:     { url: '/assets/minigame/towerattack/happy_little_flame.png' },
	health_cross:           { url: '/assets/minigame/towerattack/health_cross.png' },
	resurrection_spirit:    { url: '/assets/minigame/towerattack/resurrection_spirit.png' },
	sparkle:                { url: '/assets/minigame/towerattack/sparkle.png' },
	streak:                 { url: '/assets/minigame/towerattack/streak.png' },
	steam_coin_large:       { url: '/assets/minigame/towerattack/steam_coin_large.png' },
	//// Backgrounds
	clouds:                 { url: '/assets/minigame/towerattack/clouds_loop.png' },
	// Desert
	desert_floor:           { url: '/assets/minigame/towerattack/desert_floor.png' },
	desert_clouds:          { url: '/assets/minigame/towerattack/desert_clouds.png' },
	desert_dunes:           { url: '/assets/minigame/towerattack/desert_dunes.png' },
	desert_sky:             { url: '/assets/minigame/towerattack/desert_sky.png' },
	// City
	city_floor:             { url: '/assets/minigame/towerattack/city_floor.png' },
	city_sky:               { url: '/assets/minigame/towerattack/city_sky.png' },
	city_bg_near:           { url: '/assets/minigame/towerattack/city_bg_near.png' },
	city_bg_mid:            { url: '/assets/minigame/towerattack/city_bg_mid.png' },
	city_bg_far:            { url: '/assets/minigame/towerattack/city_bg_far.png' },
	// Ruined city
	cityr_floor:            { url: '/assets/minigame/towerattack/cityr_floor.png' },
	cityr_sky:              { url: '/assets/minigame/towerattack/cityr_sky.png' },
	cityr_bg_near:          { url: '/assets/minigame/towerattack/cityr_bg_near.png' },
	cityr_bg_mid:           { url: '/assets/minigame/towerattack/cityr_bg_mid.png' },
	cityr_bg_far:           { url: '/assets/minigame/towerattack/cityr_bg_far.png' },
	// Ocean
	ocean_floor:            { url: '/assets/minigame/towerattack/ocean_floor.png' },
	ocean_sky:              { url: '/assets/minigame/towerattack/ocean_sky.png' },
	ocean_bg_near:          { url: '/assets/minigame/towerattack/ocean_bg_near.png' },
	ocean_bg_mid:           { url: '/assets/minigame/towerattack/ocean_bg_mid.png' },
	ocean_bg_far:           { url: '/assets/minigame/towerattack/ocean_bg_far.png' },
	// night
	night_floor:            { url: '/assets/minigame/towerattack/night_floor.png' },
	night_sky:              { url: '/assets/minigame/towerattack/night_sky.png' },
	night_bg_near:          { url: '/assets/minigame/towerattack/night_bg_near.png' },
	night_bg_mid:           { url: '/assets/minigame/towerattack/night_bg_mid.png' },
	night_bg_far:           { url: '/assets/minigame/towerattack/night_bg_far.png' },
	// spaaaaaaaaaaaaaaaaaace
	space_floor:            { url: '/assets/minigame/towerattack/space_floor.png' },
	space_sky:              { url: '/assets/minigame/towerattack/space_sky.png' },
	space_bg_near:          { url: '/assets/minigame/towerattack/space_bg_mid.png' },
	space_bg_mid:           { url: '/assets/minigame/towerattack/space_bg_near.png' },
	space_bg_far:           { url: '/assets/minigame/towerattack/space_bg_far.png' },
	// snow
	snow_floor:             { url: '/assets/minigame/towerattack/snow_floor.png' },
	snow_sky:               { url: '/assets/minigame/towerattack/snow_sky.png' },
	snow_bg_mid:            { url: '/assets/minigame/towerattack/snow_bg_mid.png' },
	snow_bg_far:            { url: '/assets/minigame/towerattack/snow_bg_far.png' },
	// statium
	stadium_floor:          { url: '/assets/minigame/towerattack/statium_floor.png' },
	stadium_sky:            { url: '/assets/minigame/towerattack/statium_sky.png' },
	stadium_bg_near:        { url: '/assets/minigame/towerattack/statium_bg_near.png' },
	stadium_bg_mid:         { url: '/assets/minigame/towerattack/statium_bg_mid.png' },
	stadium_bg_far:         { url: '/assets/minigame/towerattack/statium_bg_far.png' },
	// island
	island_floor:           { url: '/assets/minigame/towerattack/island_floor.png' },
	island_sky:             { url: '/assets/minigame/towerattack/island_sky.png' },
	island_bg_mid:          { url: '/assets/minigame/towerattack/island_bg_mid.png' },
	island_bg_far:          { url: '/assets/minigame/towerattack/island_bg_far.png' },
	// volcano
	volcano_floor:          { url: '/assets/minigame/towerattack/volcano_floor.png' },
	volcano_sky:            { url: '/assets/minigame/towerattack/volcano_sky.png' },
	volcano_bg_mid:         { url: '/assets/minigame/towerattack/volcano_bg_mid.png' },
	volcano_bg_far:         { url: '/assets/minigame/towerattack/volcano_bg_far.png' },
	pointer:                { url: '/assets/minigame/towerattack/pointer.png' },

};

var g_rgEmitterCache = {};

var g_rgSkeletonCache =
{
	spawner_spaceship:   { url: '/assets/minigame/towerattack/skeletons/spawner_spaceship.json' },
	boss_space:          { url: '/assets/minigame/towerattack/skeletons/boss_space.json' },
	boss_desert:         { url: '/assets/minigame/towerattack/skeletons/boss_desert.json' },
	boss_island:         { url: '/assets/minigame/towerattack/skeletons/boss_island.json' },
	boss_stadium:        { url: '/assets/minigame/towerattack/skeletons/boss_stadium.json' },
	boss_volcano:        { url: '/assets/minigame/towerattack/skeletons/boss_volcano.json' },
	boss_city_day:       { url: '/assets/minigame/towerattack/skeletons/boss_city_day.json' },
	boss_city_night:     { url: '/assets/minigame/towerattack/skeletons/boss_city_night.json' },
	boss_ocean_floor:    { url: '/assets/minigame/towerattack/skeletons/boss_ocean_floor.json' },
	boss_snow:           { url: '/assets/minigame/towerattack/skeletons/boss_snow.json' },
	boss_city_destroyed: { url: '/assets/minigame/towerattack/skeletons/boss_city_destroyed.json' },
	creep:               { url: '/assets/minigame/towerattack/skeletons/creep.json' }
};

var g_rgIconMap =
{
	"ability_1":  { icon: '/assets/minigame/towerattack/ability_template_ph.png' },
	"ability_2":  { icon: '/assets/minigame/towerattack/ability_template_ph.png' },
	"ability_3":  { icon: '/assets/minigame/towerattack/ability_template_ph.png' },
	"ability_4":  { icon: '/assets/minigame/towerattack/ability_template_ph.png' },
	"ability_5":  { icon: '/assets/minigame/towerattack/emoticons/happycyto.png' },
	"ability_6":  { icon: '/assets/minigame/towerattack/emoticons/lucky.png' },
	"ability_7":  { icon: '/assets/minigame/towerattack/emoticons/lunahealthpotion.png' },
	"ability_8":  { icon: '/assets/minigame/towerattack/emoticons/goldstack.png' },
	"ability_9":  { icon: '/assets/minigame/towerattack/emoticons/hourglass.png' },
	"ability_10": { icon: '/assets/minigame/towerattack/emoticons/abomb.png' },
	"ability_11": { icon: '/assets/minigame/towerattack/emoticons/gmbomb.png' },
	"ability_12": { icon: '/assets/minigame/towerattack/emoticons/burned.png' },
	"ability_13": { icon: '/assets/minigame/towerattack/emoticons/alive.png' },
	"ability_14": { icon: '/assets/minigame/towerattack/emoticons/logiaim.png' },
	"ability_15": { icon: '/assets/minigame/towerattack/emoticons/pjkaboom.png' },
	"ability_16": { icon: '/assets/minigame/towerattack/emoticons/theorb.png' },
	"ability_17": { icon: '/assets/minigame/towerattack/emoticons/ccgold.png' },
	"ability_18": { icon: '/assets/minigame/towerattack/emoticons/critical.png'  },
	"ability_19": { icon: '/assets/minigame/towerattack/emoticons/fistpump.png'  },
	"ability_20": { icon: '/assets/minigame/towerattack/emoticons/VeneticaGoldCoin.png' },
	"ability_21": { icon: '/assets/minigame/towerattack/emoticons/swshield.png' },
	"ability_22": { icon: '/assets/minigame/towerattack/emoticons/treasurechest.png' },
	"ability_23": { icon: '/assets/minigame/towerattack/emoticons/healthvial.png' },
	"ability_24": { icon: '/assets/minigame/towerattack/emoticons/sunportal.png' },
	"ability_25": { icon: '/assets/minigame/towerattack/emoticons/twteamrandom.png' },
	"ability_26": { icon: '/assets/minigame/towerattack/emoticons/wormwarp.png' },
	"ability_27": { icon: '/assets/minigame/towerattack/emoticons/cooldown.png' },
	"element_1":  { icon: '/assets/minigame/towerattack/emoticons/shelterwildfire.png' },
	"element_2":  { icon: '/assets/minigame/towerattack/emoticons/waterrune.png' },
	"element_3":  { icon: '/assets/minigame/towerattack/emoticons/Wisp.png' },
	"element_4":  { icon: '/assets/minigame/towerattack/emoticons/FateTree.png' },
	"enemy_2":    { icon: '/assets/minigame/towerattack/emoticons/like_king.png' },
	"enemy_4":    { icon: '/assets/minigame/towerattack/emoticons/goldenmilkminer.png' },
	"speech":     { icon: '/assets/minigame/towerattack/emoticons/speech.png' },
};

var g_rgSoundCache =
{
	loading:        { urlv: '/assets/minigame/towerattack/sfx/loadingsound.ogg', urlm: '/assets/minigame/towerattack/sfx/loadingsound.mp3' },
	hurt:           { urlv: '/assets/minigame/towerattack/sfx/clickattack2.ogg', urlm: '/assets/minigame/towerattack/sfx/clickattack2.mp3' },
	ability:        { urlv: '/assets/minigame/towerattack/sfx/upgradeability.ogg', urlm: '/assets/minigame/towerattack/sfx/upgradeability.mp3' },
	upgrade:        { urlv: '/assets/minigame/towerattack/sfx/standardupgrade.ogg', urlm: '/assets/minigame/towerattack/sfx/standardupgrade.mp3' },
	explode:        { urlv: '/assets/minigame/towerattack/sfx/enemydied.ogg', urlm: '/assets/minigame/towerattack/sfx/enemydied.mp3' },
	dead:           { urlv: '/assets/minigame/towerattack/sfx/youdied.ogg', urlm: '/assets/minigame/towerattack/sfx/youdied.mp3' },
	spawn:          { urlv: '/assets/minigame/towerattack/sfx/shipspawn2.ogg', urlm: '/assets/minigame/towerattack/sfx/shipspawn2.mp3' },
	nuke:           { urlv: '/assets/minigame/towerattack/sfx/nuke.ogg', urlm: '/assets/minigame/towerattack/sfx/nuke.mp3' },
	goldclick:      { urlv: '/assets/minigame/towerattack/sfx/pickupgold.ogg', urlm: '/assets/minigame/towerattack/sfx/pickupgold.mp3' },
	clusterbomb:    { urlv: '/assets/minigame/towerattack/sfx/clusterbomb.ogg', urlm: '/assets/minigame/towerattack/sfx/clusterbomb.mp3' },
	napalm:         { urlv: '/assets/minigame/towerattack/sfx/napalm.ogg', urlm: '/assets/minigame/towerattack/sfx/napalm.mp3' },
	wrongselection: { urlv: '/assets/minigame/towerattack/sfx/wrongselection.ogg', urlm: '/assets/minigame/towerattack/sfx/wrongselection.mp3' },
	music:          { urlv: '/assets/minigame/towerattack/sfx/backgroundtrack.ogg', urlm: '/assets/minigame/towerattack/sfx/backgroundtrack.mp3' },
	music_boss:     { urlv: '/assets/minigame/towerattack/sfx/bosslevel.ogg', urlm: '/assets/minigame/towerattack/sfx/bosslevel.mp3' },
	music_bossB:    { urlv: '/assets/minigame/towerattack/sfx/bosslevel2.ogg', urlm: '/assets/minigame/towerattack/sfx/bosslevel2.mp3' },

	// Creep chatter
	creep_1:  { urlv: '/assets/minigame/towerattack/sfx/creep1.ogg', urlm: '/assets/minigame/towerattack/sfx/creep1.mp3' },
	creep_2:  { urlv: '/assets/minigame/towerattack/sfx/creep2.ogg', urlm: '/assets/minigame/towerattack/sfx/creep2.mp3' },
	creep_3:  { urlv: '/assets/minigame/towerattack/sfx/creep3.ogg', urlm: '/assets/minigame/towerattack/sfx/creep3.mp3' },
	creep_4:  { urlv: '/assets/minigame/towerattack/sfx/creep4.ogg', urlm: '/assets/minigame/towerattack/sfx/creep4.mp3' },
	creep_5:  { urlv: '/assets/minigame/towerattack/sfx/creep5.ogg', urlm: '/assets/minigame/towerattack/sfx/creep5.mp3' },
	creep_6:  { urlv: '/assets/minigame/towerattack/sfx/creep6.ogg', urlm: '/assets/minigame/towerattack/sfx/creep6.mp3' },
	creep_7:  { urlv: '/assets/minigame/towerattack/sfx/creep7.ogg', urlm: '/assets/minigame/towerattack/sfx/creep7.mp3' },
	creep_8:  { urlv: '/assets/minigame/towerattack/sfx/creep8.ogg', urlm: '/assets/minigame/towerattack/sfx/creep8.mp3' },
	creep_9:  { urlv: '/assets/minigame/towerattack/sfx/creep9.ogg', urlm: '/assets/minigame/towerattack/sfx/creep9.mp3' },
	creep_10: { urlv: '/assets/minigame/towerattack/sfx/creep3.1.ogg', urlm: '/assets/minigame/towerattack/sfx/creep3.1.mp3' },
	creep_11: { urlv: '/assets/minigame/towerattack/sfx/creep8.1.ogg', urlm: '/assets/minigame/towerattack/sfx/creep8.1.mp3' },
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
			url: '/assets/minigame/towerattack/emitters/combined.json',
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
