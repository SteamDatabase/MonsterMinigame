"use strict";

window.PixiFunctions = function()
{
	this.harlem = false;
	this.shake = false;
	this.slowmo = false;
	this.done = false;
	this.nodes = [];
	this.firstEnemy = null;
	this.currentKeys = [];
	this.shakeData = {};

	var specialKeys = '38,38,40,40,37,39,37,39,66,65';
	var specialEvent;
	var instance = this;
	specialEvent = function( e ) {
		instance.currentKeys.push( e.keyCode );
		var gameInstance = g_Minigame.m_CurrentScene;
		if ( gameInstance && gameInstance.m_rgGameData && gameInstance.m_rgGameData.status == 2 && instance.currentKeys.toString().indexOf( specialKeys ) >= 0 )
		{
			instance.firstEnemy = gameInstance.GetEnemy( gameInstance.m_rgPlayerData.current_lane, gameInstance.m_rgPlayerData.target );
			if ( instance.firstEnemy )
			{
				instance.addCSS();
				instance.playSong();
				$J( document ).unbind( 'keydown' , specialEvent );
			}
		}
	}

	$( document ).bind( 'keydown', specialEvent );
}

PixiFunctions.prototype.addCSS = function() 
{
	var css = document.createElement("link");
	css.setAttribute("type", "text/css");
	css.setAttribute("rel", "stylesheet");
	css.setAttribute("href", g_AssetsHost + '/assets/css/pixi.css');
	css.setAttribute("class", 'mw_added_css');
	document.body.appendChild(css);
}

PixiFunctions.prototype.removeAddedFiles = function()
{
	var addedFiles = document.getElementsByClassName('mw_added_css');
	for (var i=0; i<addedFiles.length; i++) 
	{
		document.body.removeChild(addedFiles[i]);
	}
}

PixiFunctions.prototype.flashScreen = function() 
{
	var flash = document.createElement("div");
	flash.setAttribute("class", 'mw-strobe_light');
	document.body.appendChild(flash);

	setTimeout(function() {
		document.body.removeChild(flash);
	}, 100);
}

PixiFunctions.prototype.playSong = function() 
{
	var audioTag = document.createElement("audio");
	audioTag.setAttribute("class", 'mw_added_css');
	audioTag.src = g_AssetsHost + '/assets/pixi/play.ogg';
	audioTag.loop = false;

	var instance = this;
	instance.nodes = instance.getAllEnemies();
	var len = instance.nodes.length;

	audioTag.addEventListener("timeupdate", function() 
	{
		if (audioTag.currentTime >= 0.5 && !instance.harlem)
		{
			instance.harlem = true;
			instance.shakeFirst( instance.firstEnemy );
		}

		if (audioTag.currentTime >= 15.5 && !instance.shake)
		{
			instance.shake = true;
			instance.stopShakeAll();
			instance.flashScreen();
			for (var i = 0; i < len; i++)
			{
				if ( !instance.nodes[ i ].m_bIsDestroyed )
				{
					g_Minigame.m_CurrentScene.SpawnEmitter( g_rgEmitterCache[ '3coins_smoke' ], instance.nodes[ i ].m_Sprite.x, instance.nodes[ i ].m_Sprite.y );
					g_Minigame.m_CurrentScene.SpawnEmitter( g_rgEmitterCache[ '3coins_coins' ], instance.nodes[ i ].m_Sprite.x, instance.nodes[ i ].m_Sprite.y );
					g_Minigame.m_CurrentScene.SpawnEmitter( g_rgEmitterCache[ '3coins_burst' ], instance.nodes[ i ].m_Sprite.x, instance.nodes[ i ].m_Sprite.y );
				}
				instance.shakeOther( i, instance.nodes[ i ] );
			}
		}

		if (audioTag.currentTime >= 28.4 && !instance.slowmo)
		{
			instance.slowmo = true;
			for (var i = 0; i < len; i++)
			{
				if ( !instance.nodes[ i ].m_bIsDestroyed )
				{
					instance.shakeSlow( i, instance.nodes[ i ] );
				}
			}
		}
	}, true);
	audioTag.addEventListener("ended", function() {
		instance.stopShakeAll();
		instance.removeAddedFiles();
	}, true);
	audioTag.innerHTML = "<p>If you are reading this, it is because your browser does not support the audio element. We recommend that you get a new browser.</p>";
	document.body.appendChild(audioTag);
	audioTag.play();
}

PixiFunctions.prototype.shakeFirst = function( enemy, positive )
{
	var instance = this;
	if ( !instance.shake && !enemy.m_bIsDestroyed )
	{
		setTimeout(function() {
			enemy.m_Sprite.x += positive ? 20 : -20;
			enemy.m_Sprite.height += positive ? 30 : -30;
			enemy.m_Sprite.width += positive ? 30 : -30;
			instance.shakeFirst( enemy, !positive );
		}, 200);
	}
}

PixiFunctions.prototype.shakeOther = function( id, enemy, positive )
{
	var instance = this;
	if ( !instance.slowmo && !enemy.m_bIsDestroyed )
	{
		var randTime = Math.floor(Math.random() * 150) + 50;
		setTimeout(function() {
			SmackTV();
			var rand = Math.floor(Math.random() * 4) + 1;
			// TODO: @Contex: make the sprite size larger and smaller?
			//enemy.m_Sprite.width = instance.shakeData[ id ].width + ( positive ? instance.shakeData[ id ].size : -instance.shakeData[ id ].size );
			//enemy.m_Sprite.height = instance.shakeData[ id ].height + ( positive ? instance.shakeData[ id ].size : -instance.shakeData[ id ].size );
			if ( rand == 1 )
			{
				enemy.m_Sprite.x = instance.shakeData[ id ].xPos + ( positive ? instance.shakeData[ id ].xDiff : -instance.shakeData[ id ].xDiff );
				enemy.m_Sprite.y = instance.shakeData[ id ].yPos + ( positive ? instance.shakeData[ id ].yDiff : -instance.shakeData[ id ].yDiff );
			}
			else if ( rand == 2 )
			{
				enemy.m_Sprite.x = instance.shakeData[ id ].xPos + ( !positive ? instance.shakeData[ id ].xDiff : -instance.shakeData[ id ].xDiff );
				enemy.m_Sprite.y = instance.shakeData[ id ].yPos + ( positive ? instance.shakeData[ id ].yDiff : -instance.shakeData[ id ].yDiff );
			}
			else if ( rand == 3 )
			{
				enemy.m_Sprite.x = instance.shakeData[ id ].xPos + ( positive ? instance.shakeData[ id ].xDiff : -instance.shakeData[ id ].xDiff );
				enemy.m_Sprite.y = instance.shakeData[ id ].yPos + ( !positive ? instance.shakeData[ id ].yDiff : -instance.shakeData[ id ].yDiff );
			}
			else
			{
				enemy.m_Sprite.x = instance.shakeData[ id ].xPos + ( !positive ? instance.shakeData[ id ].xDiff : -instance.shakeData[ id ].xDiff );
				enemy.m_Sprite.y = instance.shakeData[ id ].yPos + ( !positive ? instance.shakeData[ id ].yDiff : -instance.shakeData[ id ].yDiff );
			}
			instance.shakeOther( id, enemy, !positive );
		}, randTime);
	}
}

PixiFunctions.prototype.shakeSlow = function( id, enemy, positive )
{
	var instance = this;
	if ( !instance.done && instance.slowmo && !enemy.m_bIsDestroyed )
	{
		setTimeout(function() {
			enemy.m_Sprite.x = instance.shakeData[ id ].xPos + ( positive ? 20 : -20 );
			instance.shakeSlow( id, enemy, !positive );
		}, 250);
	}
}

PixiFunctions.prototype.stopShakeAll = function()
{
	// TODO: @Contex: RESET SPRITE SIZE!
	var len = this.nodes.length;
	this.done = true;
	for (var i = 0; i < len; i++)
	{
		if ( !this.nodes[ i ].m_bIsDestroyed )
		{
			this.nodes[ i ].x = this.shakeData[ i ].xPos;
			this.nodes[ i ].y = this.shakeData[ i ].yPos;
		}
	}
}

PixiFunctions.prototype.getAllEnemies = function()
{
	var instance = this;
	var gameInstance = g_Minigame.m_CurrentScene;
	var allEnemies = [];
	for ( var a = 0; gameInstance.m_rgGameData.lanes.length > a; a++ )
	{
		var lane = gameInstance.m_rgGameData.lanes[ a ];
		for ( var b = 0; gameInstance.m_rgGameData.lanes[ a ].enemies.length > b; b++ )
		{
			var enemy = gameInstance.GetEnemy( a, b );
			if ( enemy )
			{
				instance.shakeData[ allEnemies.length ] = {
					width: enemy.m_Sprite.width,
					height: enemy.m_Sprite.height,
					xPos: enemy.m_Sprite.x,
					yPos: enemy.m_Sprite.y,
					xDiff: Math.floor(Math.random() * 20) + 5,
					yDiff: Math.floor(Math.random() * 20) + 5,
					size: Math.floor(Math.random() * 20) + 5
				};
				allEnemies.push( enemy );
			}
		}
	}
	return allEnemies;
}

new PixiFunctions();
