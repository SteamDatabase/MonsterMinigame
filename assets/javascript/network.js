// <script>
"use strict";

var g_IncludeGameStats = false;

window.CServerInterface = function( )
{

}

CServerInterface.prototype.BuildURL = function( strInterface, strMethod, bSecure, strVersion )
{
	if ( !strVersion )
		strVersion = 'v0001';

	return '/api/' + strInterface + '/' + strMethod + '/' + strVersion + '/';
}

CServerInterface.prototype.GetGameTuningData = function( callback )
{
	var rgParams = {
		game_type: 1,
		gameid: this.m_nGameID
	};

	var instance = this;

	this.m_WebAPI.ExecJSONP( 'ITowerAttackMiniGameService', 'GetTuningData',  rgParams, true, null, 15 )
		.done( callback )
		.fail( function(err)
		{
			console.log("FAILED");
			console.log(err);
		});

}

CServerInterface.prototype.GetGameData = function( callback, error, bIncludeStats )
{
	var rgParams =
	{
		gameid: this.m_nGameID,
		include_stats: ( bIncludeStats || g_IncludeGameStats ) ? 1 : 0
	};

	var instance = this;

	$J.ajax({
		url: this.BuildURL( 'ITowerAttackMiniGameService', 'GetGameData', false ),
		data: rgParams,
		dataType: 'json'
	}).success(callback)
	.fail( error );
}

CServerInterface.prototype.GetPlayerData = function( callback, error, bIncludeTechTree )
{
	var rgParams =
	{
		gameid: this.m_nGameID,
		steamid: g_steamID,
		include_tech_tree: bIncludeTechTree ? 1 : 0,
		include_stats: 1
	};

	var instance = this;

	$J.ajax({
		url: this.BuildURL( 'ITowerAttackMiniGameService', 'GetPlayerData', false ),
		data: rgParams,
		dataType: 'json'
	}).success(callback)
	.fail( error );
}

CServerInterface.prototype.UseAbilities = function( callback, failed, rgParams )
{
	var instance = this;

	var rgRequest =
	{
		gameid: this.m_nGameID,
		access_token: g_steamID,
		requested_abilities: JSON.stringify( rgParams )
	};

	$J.ajax({
		url: this.BuildURL( 'ITowerAttackMiniGameService', 'UseAbilities', true ),
		method: 'POST',
		data: rgRequest,
		dataType: 'json'
	}).success(function(result){
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

CServerInterface.prototype.ChooseUpgrades = function( callback, upgrades )
{
	var instance = this;

	var rgRequest =
	{
		gameid: this.m_nGameID,
		access_token: g_steamID,
		upgrades: JSON.stringify( upgrades )
	};

	$J.ajax({
		url: this.BuildURL( 'ITowerAttackMiniGameService', 'ChooseUpgrade', true ),
		method: 'POST',
		data: rgRequest,
		dataType: 'json'
	}).success(function(result){
		if ( result.response.tech_tree )
		{
			result.response.tech_tree.unlocked_abilities_bitfield = result.response.tech_tree.unlocked_abilities_bitfield ? parseInt( result.response.tech_tree.unlocked_abilities_bitfield ) : 0;
		}
		callback( result );
	} )
	.fail( function(err)
	{
		console.log("FAILED");
		console.log(err);
	});
}

CServerInterface.prototype.UseBadgePoints = function( callback, abilityItems )
{
	var instance = this;

	var rgRequest =
	{
		gameid: this.m_nGameID,
		access_token: g_steamID,
		ability_items: JSON.stringify( abilityItems )
	};

	$J.ajax({
		url: this.BuildURL( 'ITowerAttackMiniGameService', 'UseBadgePoints', true ),
		method: 'POST',
		data: rgRequest,
		dataType: 'json'
	}).success(function(result){
		if ( result.response.tech_tree )
		{
			result.response.tech_tree.unlocked_abilities_bitfield = result.response.tech_tree.unlocked_abilities_bitfield ? parseInt( result.response.tech_tree.unlocked_abilities_bitfield ) : 0;
		}
		callback( result );
	} )
	.fail( function(err)
	{
		console.log("FAILED");
		console.log(err);
	});
}

CServerInterface.prototype.ChatMessage = function( message )
{
	$J.ajax({
		url: g_Server.BuildURL( 'ITowerAttackMiniGameService', 'ChatMessage', true ),
		method: 'POST',
		data: {
			gameid: this.m_nGameID,
			access_token: g_steamID,
			message: message
		},
		dataType: 'json'
	});
}
