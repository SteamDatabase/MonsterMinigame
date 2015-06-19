// <script>
"use strict";

var g_bHalt = false;
var g_IncludeGameStats = false;

window.CServerInterface = function( )
{
	// Get token

	this.m_strSteamID = false;

	this.m_nLastTick = false
	this.m_bRequestUpdates = false;

	var instance = this;

	this.m_WebAPI = false;//new CWebAPI( rgResult.webapi_host, rgResult.webapi_host_secure, rgResult.token );


}

CServerInterface.prototype.Connect = function( callback )
{
	var instance = this;

	$J.ajax({
		url: '/gettoken',
		dataType: 'json'
	}).success(function(rgResult){
		if( rgResult.success == 1)
		{
			instance.m_strSteamID = rgResult.steamid;
			instance.m_strWebAPIHost = rgResult.webapi_host;
			instance.m_WebAPI = new CWebAPI( rgResult.webapi_host, rgResult.webapi_host_secure, rgResult.token );
			callback(rgResult);
		}
	});
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
			g_bHalt = true;
		});

}

CServerInterface.prototype.GetGameData = function( callback, error, bIncludeStats )
{
	var rgParams = {
		gameid: this.m_nGameID,
		include_stats: ( bIncludeStats || g_IncludeGameStats ) ? 1 : 0,
		format: 'json'
	};

	var instance = this;

	$J.ajax({
		url: this.m_WebAPI.BuildURL( 'ITowerAttackMiniGameService', 'GetGameData', false ),
		data: rgParams,
		dataType: 'json'
	}).success(callback)
	.fail( error );
}

CServerInterface.prototype.GetPlayerNames = function( callback, error, rgAccountIDs )
{
	var rgParams = {
		gameid: this.m_nGameID,
		accountids: rgAccountIDs && rgAccountIDs.length < 100 ? rgAccountIDs : null,
	};

	var instance = this;

	var rgRequest = {
		'input_json': V_ToJSON( rgParams ),
		'format': 'json',
	};

	$J.ajax({
		url: this.m_WebAPI.BuildURL( 'ITowerAttackMiniGameService', 'GetPlayerNames', false ),
		data: rgRequest,
		dataType: 'json'
	}).success(callback)
	.fail( error );
}

CServerInterface.prototype.GetPlayerData = function( callback, error, bIncludeTechTree )
{
	var rgParams = {
		gameid: this.m_nGameID,
		steamid: g_steamID,
		include_tech_tree: (bIncludeTechTree) ? 1 : 0,
		format: 'json'
	};

	var instance = this;

	$J.ajax({
		url: this.m_WebAPI.BuildURL( 'ITowerAttackMiniGameService', 'GetPlayerData', false ),
		data: rgParams,
		dataType: 'json'
	}).success(callback)
	.fail( error );
}

CServerInterface.prototype.UseAbilities = function( callback, failed, rgParams )
{
	rgParams["gameid"] = this.m_nGameID;

	var instance = this;

	var rgRequest = {
		'input_json': V_ToJSON( rgParams ),
		'access_token': g_steamID,
		'format': 'json',
	};

	$J.ajax({
		url: this.m_WebAPI.BuildURL( 'ITowerAttackMiniGameService', 'UseAbilities', true ),
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
	var rgParams = {
		'gameid': this.m_nGameID,
		'upgrades': upgrades
	};

	var instance = this;

	var rgRequest = {
		'input_json': V_ToJSON( rgParams ),
		'access_token': g_steamID,
		'format': 'json'
	};

	$J.ajax({
		url: this.m_WebAPI.BuildURL( 'ITowerAttackMiniGameService', 'ChooseUpgrade', true ),
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
	var rgParams = {
		'gameid': this.m_nGameID,
		'ability_items': abilityItems
	};

	var instance = this;

	var rgRequest = {
		'input_json': V_ToJSON( rgParams ),
		'access_token': g_steamID,
		'format': 'json'
	};

	$J.ajax({
		url: this.m_WebAPI.BuildURL( 'ITowerAttackMiniGameService', 'UseBadgePoints', true ),
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

CServerInterface.prototype.QuitGame = function( callback )
{
	var rgParams = {
		'gameid': this.m_nGameID,
	};


	var instance = this;

	var rgRequest = {
		'input_json': V_ToJSON( rgParams )
	};

	this.m_WebAPI.ExecJSONP( 'IMiniGameService', 'LeaveGame',  rgRequest, true, null )
		.done( callback )
		.fail( function(err)
		{
			console.log("FAILED");
			console.log(err);
		});

}
