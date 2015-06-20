// <script>
"use strict"

window.k_ScreenWidth = 1280;
window.k_ScreenHeight = 720;

var g_JSCacheKey = Math.random();

var g_rgTextureCache = {
	// Particles
	steam_coin: { url: '/assets/minigame/towerattack/steam_coin.png?v='+g_CacheKey },
	pixel3d: { url: '/assets/minigame/towerattack/3d_pixel.png?v='+g_CacheKey },
	black_smoke: { url: '/assets/minigame/towerattack/black_smoke.png?v='+g_CacheKey },
	large_square_pixel: { url: '/assets/minigame/towerattack/large_square_pixel.png?v='+g_CacheKey },
	pixel_bubble: { url: '/assets/minigame/towerattack/pixel_bubble.png?v='+g_CacheKey },
	pixel_bubble_large: { url: '/assets/minigame/towerattack/pixel_bubble_large.png?v='+g_CacheKey },
	white_smoke_puff: { url: '/assets/minigame/towerattack/white_smoke_puff.png?v='+g_CacheKey },
	white_smoke_puff_large: { url: '/assets/minigame/towerattack/white_smoke_puff_large.png?v='+g_CacheKey },
	clock: { url: '/assets/minigame/towerattack/clock.png?v='+g_CacheKey },
	clover: { url: '/assets/minigame/towerattack/clover.png?v='+g_CacheKey },
	faded_triangle: { url: '/assets/minigame/towerattack/faded_triangle.png?v='+g_CacheKey },
	happy_little_flame: { url: '/assets/minigame/towerattack/happy_little_flame.png?v='+g_CacheKey },
	health_cross: { url: '/assets/minigame/towerattack/health_cross.png?v='+g_CacheKey },
	resurrection_spirit: { url: '/assets/minigame/towerattack/resurrection_spirit.png?v='+g_CacheKey },
	sparkle: { url: '/assets/minigame/towerattack/sparkle.png?v='+g_CacheKey },
	streak: { url: '/assets/minigame/towerattack/streak.png?v='+g_CacheKey },
	steam_coin_large: { url: '/assets/minigame/towerattack/steam_coin_large.png?v='+g_CacheKey },
	//// Backgrounds
	clouds: { url: '/assets/minigame/towerattack/clouds_loop.png?v='+g_CacheKey },
	// Desert
	desert_floor: { url: '/assets/minigame/towerattack/desert_floor.png?v='+g_CacheKey },
	desert_clouds: { url: '/assets/minigame/towerattack/desert_clouds.png?v='+g_CacheKey },
	desert_dunes: { url: '/assets/minigame/towerattack/desert_dunes.png?v='+g_CacheKey },
	desert_sky: { url: '/assets/minigame/towerattack/desert_sky.png?v='+g_CacheKey },
	// City
	city_floor: { url: '/assets/minigame/towerattack/city_floor.png?v='+g_CacheKey },
	city_sky: { url: '/assets/minigame/towerattack/city_sky.png?v='+g_CacheKey },
	city_bg_near: { url: '/assets/minigame/towerattack/city_bg_near.png?v='+g_CacheKey },
	city_bg_mid: { url: '/assets/minigame/towerattack/city_bg_mid.png?v='+g_CacheKey },
	city_bg_far: { url: '/assets/minigame/towerattack/city_bg_far.png?v='+g_CacheKey },
	// Ruined city
	cityr_floor: { url: '/assets/minigame/towerattack/cityr_floor.png?v='+g_CacheKey },
	cityr_sky: { url: '/assets/minigame/towerattack/cityr_sky.png?v='+g_CacheKey },
	cityr_bg_near: { url: '/assets/minigame/towerattack/cityr_bg_near.png?v='+g_CacheKey },
	cityr_bg_mid: { url: '/assets/minigame/towerattack/cityr_bg_mid.png?v='+g_CacheKey },
	cityr_bg_far: { url: '/assets/minigame/towerattack/cityr_bg_far.png?v='+g_CacheKey },
	// Ocean
	ocean_floor: { url: '/assets/minigame/towerattack/ocean_floor.png?v='+g_CacheKey },
	ocean_sky: { url: '/assets/minigame/towerattack/ocean_sky.png?v='+g_CacheKey },
	ocean_bg_near: { url: '/assets/minigame/towerattack/ocean_bg_near.png?v='+g_CacheKey },
	ocean_bg_mid: { url: '/assets/minigame/towerattack/ocean_bg_mid.png?v='+g_CacheKey },
	ocean_bg_far: { url: '/assets/minigame/towerattack/ocean_bg_far.png?v='+g_CacheKey },
	// night
	night_floor: { url: '/assets/minigame/towerattack/night_floor.png?v='+g_CacheKey },
	night_sky: { url: '/assets/minigame/towerattack/night_sky.png?v='+g_CacheKey },
	night_bg_near: { url: '/assets/minigame/towerattack/night_bg_near.png?v='+g_CacheKey },
	night_bg_mid: { url: '/assets/minigame/towerattack/night_bg_mid.png?v='+g_CacheKey },
	night_bg_far: { url: '/assets/minigame/towerattack/night_bg_far.png?v='+g_CacheKey },	
	// spaaaaaaaaaaaaaaaaaace
	space_floor: { url: '/assets/minigame/towerattack/space_floor.png?v='+g_CacheKey },
	space_sky: { url: '/assets/minigame/towerattack/space_sky.png?v='+g_CacheKey },
	space_bg_near: { url: '/assets/minigame/towerattack/space_bg_mid.png?v='+g_CacheKey },
	space_bg_mid: { url: '/assets/minigame/towerattack/space_bg_near.png?v='+g_CacheKey },
	space_bg_far: { url: '/assets/minigame/towerattack/space_bg_far.png?v='+g_CacheKey },
	// snow
	snow_floor: { url: '/assets/minigame/towerattack/snow_floor.png?v='+g_CacheKey },
	snow_sky: { url: '/assets/minigame/towerattack/snow_sky.png?v='+g_CacheKey },
	snow_bg_mid: { url: '/assets/minigame/towerattack/snow_bg_mid.png?v='+g_CacheKey },
	snow_bg_far: { url: '/assets/minigame/towerattack/snow_bg_far.png?v='+g_CacheKey },
	// statium
	stadium_floor: { url: '/assets/minigame/towerattack/statium_floor.png?v='+g_CacheKey },
	stadium_sky: { url: '/assets/minigame/towerattack/statium_sky.png?v='+g_CacheKey },
	stadium_bg_near: { url: '/assets/minigame/towerattack/statium_bg_near.png?v='+g_CacheKey },
	stadium_bg_mid: { url: '/assets/minigame/towerattack/statium_bg_mid.png?v='+g_CacheKey },
	stadium_bg_far: { url: '/assets/minigame/towerattack/statium_bg_far.png?v='+g_CacheKey },
	// island
	island_floor: { url: '/assets/minigame/towerattack/island_floor.png?v='+g_CacheKey },
	island_sky: { url: '/assets/minigame/towerattack/island_sky.png?v='+g_CacheKey },
	island_bg_mid: { url: '/assets/minigame/towerattack/island_bg_mid.png?v='+g_CacheKey },
	island_bg_far: { url: '/assets/minigame/towerattack/island_bg_far.png?v='+g_CacheKey },
	// volcano
	volcano_floor: { url: '/assets/minigame/towerattack/volcano_floor.png?v='+g_CacheKey },
	volcano_sky: { url: '/assets/minigame/towerattack/volcano_sky.png?v='+g_CacheKey },
	volcano_bg_mid: { url: '/assets/minigame/towerattack/volcano_bg_mid.png?v='+g_CacheKey },
	volcano_bg_far: { url: '/assets/minigame/towerattack/volcano_bg_far.png?v='+g_CacheKey },
	pointer: { url: '/assets/minigame/towerattack/pointer.png?v='+g_CacheKey },

};

var g_rgEmitterCache = {};

var g_rgSkeletonCache = {
	spawner_spaceship: { url: '/assets/minigame/towerattack/skeletons/spawner_spaceship.json?v=2'+g_CacheKey },
	boss_space: { url: '/assets/minigame/towerattack/skeletons/boss_space.json?v='+g_CacheKey },
	boss_desert: { url: '/assets/minigame/towerattack/skeletons/boss_desert.json?v='+g_CacheKey },
	boss_island: { url: '/assets/minigame/towerattack/skeletons/boss_island.json?v='+g_CacheKey },
	boss_stadium: { url: '/assets/minigame/towerattack/skeletons/boss_stadium.json?v='+g_CacheKey },
	boss_volcano: { url: '/assets/minigame/towerattack/skeletons/boss_volcano.json?v='+g_CacheKey },
	boss_city_day: { url: '/assets/minigame/towerattack/skeletons/boss_city_day.json?v='+g_CacheKey },
	boss_city_night: { url: '/assets/minigame/towerattack/skeletons/boss_city_night.json?v='+g_CacheKey },
	boss_ocean_floor: { url: '/assets/minigame/towerattack/skeletons/boss_ocean_floor.json?v='+g_CacheKey },
	boss_snow: { url: '/assets/minigame/towerattack/skeletons/boss_snow.json?v='+g_CacheKey },
	boss_city_destroyed: { url: '/assets/minigame/towerattack/skeletons/boss_city_destroyed.json?v='+g_CacheKey },
	creep: { url: '/assets/minigame/towerattack/skeletons/creep.json?v='+g_CacheKey + '2' }
};

var g_rgIconMap = {
	"ability_1": 									{ icon: '/assets/minigame/towerattack/stickybomb.png'  },
	"ability_2": 								{ icon: '/assets/minigame/towerattack/medkit.png' },
	"ability_3": 								{ icon: '/assets/minigame/towerattack/medkit.png' },
	"ability_4": 							{ icon: '/assets/minigame/towerattack/medkit.png' },
	"ability_5": 					{ icon: '/assets/minigame/towerattack/emoticons/happycyto.png' },
	"ability_6": 			{ icon: '/assets/minigame/towerattack/emoticons/lucky.png' },
	"ability_7": 							{ icon: '/assets/minigame/towerattack/emoticons/lunahealthpotion.png' },
	"ability_8": 			{ icon: '/assets/minigame/towerattack/emoticons/goldstack.png' },
	"ability_9": 				{ icon: '/assets/minigame/towerattack/emoticons/hourglass.png' },
	"ability_10": 			{ icon: '/assets/minigame/towerattack/emoticons/abomb.png' },
	"ability_11": 				{ icon: '/assets/minigame/towerattack/emoticons/gmbomb.png' },
	"ability_12": 				{ icon: '/assets/minigame/towerattack/emoticons/burned.png' },
	"ability_13": 						{ icon: '/assets/minigame/towerattack/emoticons/alive.png' },
	"ability_14": 							{ icon: '/assets/minigame/towerattack/emoticons/logiaim.png' },
	"ability_15": 							{ icon: '/assets/minigame/towerattack/emoticons/pjkaboom.png' },
	"ability_16": 				{ icon: '/assets/minigame/towerattack/emoticons/theorb.png' },
	"ability_17": 						{ icon: '/assets/minigame/towerattack/emoticons/ccgold.png' },
	"ability_18": 	{ icon: '/assets/minigame/towerattack/emoticons/critical.png'  },
	"ability_19": 				{ icon: '/assets/minigame/towerattack/emoticons/fistpump.png'  },
	"ability_20": 						{ icon: '/assets/minigame/towerattack/emoticons/VeneticaGoldCoin.png' },
	"ability_21": 					{ icon: '/assets/minigame/towerattack/emoticons/swshield.png' },
	"ability_22":		 					{ icon: '/assets/minigame/towerattack/emoticons/treasurechest.png' },
	"ability_23":		 				{ icon: '/assets/minigame/towerattack/emoticons/healthvial.png' },
	"ability_24":		 				{ icon: '/assets/minigame/towerattack/emoticons/sunportal.png' },
	"ability_25":		 				{ icon: '/assets/minigame/towerattack/emoticons/twteamrandom.png' },
	"ability_26":		 					{ icon: '/assets/minigame/towerattack/emoticons/wormwarp.png' },
	"ability_27":	 					{ icon: '/assets/minigame/towerattack/emoticons/cooldown.png' },
	"element_1":									{ icon: '/assets/minigame/towerattack/emoticons/shelterwildfire.png' },
	"element_2":									{ icon: '/assets/minigame/towerattack/emoticons/waterrune.png' },
	"element_3":										{ icon: '/assets/minigame/towerattack/emoticons/Wisp.png' },
	"element_4":									{ icon: '/assets/minigame/towerattack/emoticons/FateTree.png' },
	"enemy_2":									{ icon: '/assets/minigame/towerattack/emoticons/like_king.png' },
	"enemy_4":								{ icon: '/assets/minigame/towerattack/emoticons/goldenmilkminer.png' },
	"speech":								{ icon: '/assets/minigame/towerattack/emoticons/speech.png' },
};

var g_rgSoundCache = {
	loading: {urlv: '/assets/minigame/towerattack/sfx/loadingsound.ogg?v='+g_CacheKey, urlm: '/assets/minigame/towerattack/sfx/loadingsound.mp3?v='+g_CacheKey },
	hurt: {urlv: '/assets/minigame/towerattack/sfx/clickattack2.ogg?v='+g_CacheKey, urlm: '/assets/minigame/towerattack/sfx/clickattack2.mp3?v='+g_CacheKey },
	ability: {urlv: '/assets/minigame/towerattack/sfx/upgradeability.ogg?v='+g_CacheKey, urlm: '/assets/minigame/towerattack/sfx/upgradeability.mp3?v='+g_CacheKey },
	upgrade: {urlv: '/assets/minigame/towerattack/sfx/standardupgrade.ogg?v='+g_CacheKey, urlm: '/assets/minigame/towerattack/sfx/standardupgrade.mp3?v='+g_CacheKey },
	explode: {urlv: '/assets/minigame/towerattack/sfx/enemydied.ogg?v='+g_CacheKey, urlm: '/assets/minigame/towerattack/sfx/enemydied.mp3?v='+g_CacheKey },
	dead: {urlv: '/assets/minigame/towerattack/sfx/youdied.ogg?v='+g_CacheKey, urlm: '/assets/minigame/towerattack/sfx/youdied.mp3?v='+g_CacheKey },
	spawn: {urlv: '/assets/minigame/towerattack/sfx/shipspawn2.ogg?v='+g_CacheKey, urlm: '/assets/minigame/towerattack/sfx/shipspawn2.mp3?v='+g_CacheKey },
	nuke: {urlv: '/assets/minigame/towerattack/sfx/nuke.ogg?v='+g_CacheKey, urlm: '/assets/minigame/towerattack/sfx/nuke.mp3?v='+g_CacheKey },
	goldclick: {urlv: '/assets/minigame/towerattack/sfx/pickupgold.ogg?v='+g_CacheKey, urlm: '/assets/minigame/towerattack/sfx/pickupgold.mp3?v='+g_CacheKey },
	clusterbomb: {urlv: '/assets/minigame/towerattack/sfx/clusterbomb.ogg?v='+g_CacheKey, urlm: '/assets/minigame/towerattack/sfx/clusterbomb.mp3?v='+g_CacheKey },
	napalm: {urlv: '/assets/minigame/towerattack/sfx/napalm.ogg?v='+g_CacheKey, urlm: '/assets/minigame/towerattack/sfx/napalm.mp3?v='+g_CacheKey },
	wrongselection: {urlv: '/assets/minigame/towerattack/sfx/wrongselection.ogg?v='+g_CacheKey, urlm: '/assets/minigame/towerattack/sfx/wrongselection.mp3?v='+g_CacheKey },
	music: {urlv: '/assets/minigame/towerattack/sfx/backgroundtrack.ogg?v='+g_CacheKey, urlm: '/assets/minigame/towerattack/sfx/backgroundtrack.mp3?v='+g_CacheKey },
	music_boss: {urlv: '/assets/minigame/towerattack/sfx/bosslevel.ogg?v='+g_CacheKey, urlm: '/assets/minigame/towerattack/sfx/bosslevel.mp3?v='+g_CacheKey },
	music_bossB: {urlv: '/assets/minigame/towerattack/sfx/bosslevel2.ogg?v='+g_CacheKey, urlm: '/assets/minigame/towerattack/sfx/bosslevel2.mp3?v='+g_CacheKey },
	// Creep chatter
	creep_1:  {urlv: '/assets/minigame/towerattack/sfx/creep1.ogg?v='+g_CacheKey, urlm: '/assets/minigame/towerattack/sfx/creep1.mp3?v='+g_CacheKey },
	creep_2:  {urlv: '/assets/minigame/towerattack/sfx/creep2.ogg?v='+g_CacheKey, urlm: '/assets/minigame/towerattack/sfx/creep2.mp3?v='+g_CacheKey },
	creep_3:  {urlv: '/assets/minigame/towerattack/sfx/creep3.ogg?v='+g_CacheKey, urlm: '/assets/minigame/towerattack/sfx/creep3.mp3?v='+g_CacheKey },
	creep_4:  {urlv: '/assets/minigame/towerattack/sfx/creep4.ogg?v='+g_CacheKey, urlm: '/assets/minigame/towerattack/sfx/creep4.mp3?v='+g_CacheKey },
	creep_5:  {urlv: '/assets/minigame/towerattack/sfx/creep5.ogg?v='+g_CacheKey, urlm: '/assets/minigame/towerattack/sfx/creep5.mp3?v='+g_CacheKey },
	creep_6:  {urlv: '/assets/minigame/towerattack/sfx/creep6.ogg?v='+g_CacheKey, urlm: '/assets/minigame/towerattack/sfx/creep6.mp3?v='+g_CacheKey },
	creep_7:  {urlv: '/assets/minigame/towerattack/sfx/creep7.ogg?v='+g_CacheKey, urlm: '/assets/minigame/towerattack/sfx/creep7.mp3?v='+g_CacheKey },
	creep_8:  {urlv: '/assets/minigame/towerattack/sfx/creep8.ogg?v='+g_CacheKey, urlm: '/assets/minigame/towerattack/sfx/creep8.mp3?v='+g_CacheKey },
	creep_9:  {urlv: '/assets/minigame/towerattack/sfx/creep9.ogg?v='+g_CacheKey, urlm: '/assets/minigame/towerattack/sfx/creep9.mp3?v='+g_CacheKey },
	creep_10:  {urlv: '/assets/minigame/towerattack/sfx/creep3.1.ogg?v='+g_CacheKey, urlm: '/assets/minigame/towerattack/sfx/creep3.1.mp3?v='+g_CacheKey },
	creep_11:  {urlv: '/assets/minigame/towerattack/sfx/creep8.1.ogg?v='+g_CacheKey, urlm: '/assets/minigame/towerattack/sfx/creep8.1.mp3?v='+g_CacheKey },
};


var g_Server = false;
var g_Minigame = false;
var g_AudioManager = false;
var g_GameID = 0;
var g_TuningData = null;
var g_DebugMode = false;
var g_DebugUpdateStats = false;

function Boot() {

	// create an new instance of a pixi stage

	PIXI.SCALE_MODES.DEFAULT = PIXI.SCALE_MODES.NEAREST;

	// add the renderer view element to the DOM

	g_AudioManager = new CAudioManager();

	//LoadScene('preload');
	g_Minigame = new CMinigameManager;
	g_Minigame.gameid = g_GameID;
	g_Minigame.rgTuningData = g_TuningData;

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
		g_Server = new CServerInterface( );

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
			url: '/assets/minigame/towerattack/emitters/combined.json?v='+g_CacheKey,
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

function ToggleSound()
{
	WebStorage.SetLocal('minigame_mute', !WebStorage.GetLocal('minigame_mute') );
}

function bIsMuted()
{
	return WebStorage.GetLocal('minigame_mute') == true;
}

function PlaySound( sound )
{
	if( bIsMuted() )
		return;
	g_rgSoundCache[sound].element.currentTime=0;
	g_rgSoundCache[sound].element.play();
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
	if( bIsMuted() || !g_rgSoundCache[sound].element )
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

	if(  WebStorage.GetLocal('minigame_mutemusic') == true )
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

	if(  WebStorage.GetLocal('minigame_mutemusic') == true )
		return;

	this.m_eleMusic.play();
}

CAudioManager.prototype.ToggleMusic = function( )
{
	WebStorage.SetLocal('minigame_mutemusic', !WebStorage.GetLocal('minigame_mutemusic') );

	if( !this.m_eleMusic )
		return;

	if( WebStorage.GetLocal('minigame_mutemusic') == true )
	{
		this.m_eleMusic.pause();
	} else {
		this.m_eleMusic.play();
	}
}
