//<script>
"use strict"

window.$J = jQuery.noConflict(); // valve

var CMinigameManager = function( ){}

CMinigameManager.prototype.Initialize = function(ele)
{
	//this.Stage = new PIXI.Stage( 0x000000, true);


	var rendererOptions = {
		antialiasing:false,
		transparent:false,
		resolution:1
	};

	this.Renderer = new PIXI.autoDetectRenderer(k_ScreenWidth, k_ScreenHeight, rendererOptions);
	//this.Renderer = new PIXI.CanvasRenderer(k_ScreenWidth, k_ScreenHeight, rendererOptions);
	//this.Renderer.roundPixels = true;

	ele.appendChild( this.Renderer.view );

	PIXI.ticker.shared.add(function()
	{
		g_Minigame.Render();
	});
}

CMinigameManager.prototype.EnterScene = function( NewScene )
{
	if( this.m_CurrentScene != undefined )
		this.m_CurrentScene.Exit();

	this.m_CurrentScene = NewScene;
	this.m_CurrentScene.Enter();
}

CMinigameManager.prototype.Render = function()
{
	if( window.g_Stats )
		window.g_Stats.begin();

	if( this.m_CurrentScene != undefined )
	{
		this.m_CurrentScene.Tick();
		this.Renderer.render(this.m_CurrentScene.m_Container);
	}

	if( window.g_Stats )
		window.g_Stats.end();

}

CMinigameManager.prototype.GetMiniGameStatusString = function( $status )
{
	switch( $status )
	{
		case 1: /* k_EMiniGameStatus_WaitingForPlayers */ 	return 'Waiting for players';
		case 2: /* k_EMiniGameStatus_Running*/				return 'Running';
		case 3: /* k_EMiniGameStatus_Ended*/				return 'Ended';
	}
	return '';
}

var CSceneMinigame = function( manager )
{
	this.m_Manager = manager;
	this.m_Container = new PIXI.Container();
};

CSceneMinigame.prototype.Enter = function()
{
}

CSceneMinigame.prototype.Exit = function()
{
}


CSceneMinigame.prototype.Tick = function()
{
}

function SortContainerByY(container)
{
	container.children.sort(function(a,b) {
		a.position.y = a.position.y || 0;
		b.position.y = b.position.y || 0;
		return a.position.y - b.position.y
	});
}

if( document.body.dataset.ga.length )
{
	(function(i,s,o,g,r,a,m){i['GoogleAnalyticsObject']=r;i[r]=i[r]||function(){
	(i[r].q=i[r].q||[]).push(arguments)},i[r].l=1*new Date();a=s.createElement(o),
	m=s.getElementsByTagName(o)[0];a.async=1;a.src=g;m.parentNode.insertBefore(a,m)
	})(window,document,'script','https://www.google-analytics.com/analytics.js','ga');

	ga('set', 'forceSSL', true);
	ga('create', document.body.dataset.ga, 'auto');
	ga('send', 'pageview');
}
