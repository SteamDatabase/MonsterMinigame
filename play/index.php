<?php
	session_start();

	if( !isset( $_SESSION[ 'SteamID' ] ) )
	{
		header( 'Location: /' );
		die;
	}

	$Config = json_decode( file_get_contents( __DIR__ . '/../php/files/config.json' ) );
	$CDN = $Config->Assets->Host;

	header( 'Content-Security-Policy: ' .
		'default-src \'none\'; ' .
		'connect-src \'self\' ' . $CDN . '; ' .
		'img-src \'self\' data: ' . $CDN . ' https://steamcdn-a.akamaihd.net https://www.google-analytics.com; ' .
		'script-src ' . ( empty( $CDN ) ? "'self'" : $CDN ) . ' https://www.google-analytics.com; ' .
		'style-src \'unsafe-inline\' ' . ( empty( $CDN ) ? "'self'" : $CDN ) . '; ' .
		'font-src ' . ( empty( $CDN ) ? "'self'" : $CDN ) . '; ' .
		'media-src ' . ( empty( $CDN ) ? "'self'" : $CDN ) . '; '
	);
?><!DOCTYPE html>
<html lang="en">
<head>
	<meta charset="UTF-8">
	<title>Tower Attack</title>
	<link rel="shortcut icon" href="/favicon.ico" type="image/x-icon">
	<link href="<?php echo $CDN; ?>/assets/css/towerattack.css?v=<?php echo hash_file( 'crc32', __DIR__ . '/../assets/css/towerattack.css' ); ?>" rel="stylesheet" type="text/css">
</head>
<body
data-steamid="<?php echo $_SESSION[ 'SteamID' ]; ?>"
data-gameid="1"
data-assets="<?php echo $CDN; ?>"
data-ga="<?php echo $Config->Google->Analytics; ?>">
	<a href="https://github.com/SteamDatabase/MonsterMinigame" target="_blank" class="github"><img src="<?php echo $CDN; ?>/assets/emoticons/rfacepalm.png" style="vertical-align:text-bottom;image-rendering:pixelated"> View on GitHub</a>

	<div class="breadcrumbs">
		<a href="/">Monster Game</a> &gt; <span id="game_version" style="color:#9AC0FF"></span>
	</div>

	<style>.disclaimer { text-align: center; padding-top: 17px; box-sizing: border-box; } .disclaimer p { margin: 0; color: #DDD; } .github { position: fixed; right: 10px; top: 10px; z-index: 1000; padding: 10px; background-color: rgba(33, 150, 243, 0.5); } .github:hover { background-color: #2196F3; }</style>
	<div class="game_options disclaimer">
		<p>This minigame is using assets by Valve without permission <i>(we did reach out)</i>, play at your own risk.</p>
		<p>Backend server has been written by <a href="https://contex.me">Contex</a> and <a href="https://xpaw.me">xPaw</a>.</p>
	</div>

	<div id="gamecontainer">
		<div id="uicontainer">
			<div class="tv_ui"></div>
			<div class="scanlines"></div>

			<div id="col_left">
				<div class="gold_count">
					<img src="<?php echo $CDN; ?>/assets/emoticons/coinz.png" alt="Gold">
					<div id="info_gold"></div>
				</div>

				<div id="upgrades">
					<div id="upgradescontainer">
						<div>
							<span class="title_upgrates">Upgrades</span>
							<div class="container_upgrades"></div>
							<span class="title_upgrates abilities">Abilities</span>
							<div class="container_purchase"></div>
						</div>
					</div><div id="upgradesscroll"><div></div></div>
				</div>

				<div id="elements">
					<span class="title_elements">Elemental Damage</span>
					<div class="element_cost">Next Level Cost:<span class="cost" id="element_cost">00,000</span></div>
					<div class="upgrades">
						<div class="element_upgrade" id="upgr_3">
							<img src="<?php echo $CDN; ?>/assets/emoticons/shelterwildfire.png" alt="Fire">
							<span class="level">0</span>
							<br>
							<a class="link element_upgrade_btn" data-type="3" href="#" data-tooltip-func="fnTooltipUpgradeElementDesc">&nbsp;</a>
						</div>
						<div class="element_upgrade"  id="upgr_4">
							<img src="<?php echo $CDN; ?>/assets/emoticons/waterrune.png" alt="Water">
							<span class="level">0</span>
							<br>
							<a class="link element_upgrade_btn" data-type="4" href="#" data-tooltip-func="fnTooltipUpgradeElementDesc">&nbsp;</a>
						</div>
						<div class="element_upgrade" id="upgr_6">
							<img src="<?php echo $CDN; ?>/assets/emoticons/FateTree.png" alt="Earth">
							<span class="level">0</span>
							<br>
							<a class="link element_upgrade_btn" data-type="6" href="#" data-tooltip-func="fnTooltipUpgradeElementDesc">&nbsp;</a>
						</div>
						<div class="element_upgrade" id="upgr_5">
							<img src="<?php echo $CDN; ?>/assets/emoticons/Wisp.png" alt="Air">
							<span class="level">0</span>
							<br>
							<a class="link element_upgrade_btn" data-type="5" href="#" data-tooltip-func="fnTooltipUpgradeElementDesc">&nbsp;</a>
						</div>
					</div>
				</div>
			</div>

			<div id="col_right">
				<div class="lanes desert">
					<a id="lane0" class="lane active" href="#" data-lane="0">
						<span class="label">Lane 1</span>
						<div class="bar"><div></div></div>
						<div class="lane_element" data-tooltip-func="fnTooltipLaneElementDesc"><span></span></div><!--
						--><div class="lane_enemy enemy_icon_2" id="lane0_enemy_icon_2" data-tooltip-content="There is a Boss Monster in this lane!" style="display: none;"><img src="<?php echo $CDN; ?>/assets/images/ability_template_ph.png"></div><!--
						--><div class="lane_enemy enemy_icon_4" id="lane0_enemy_icon_4" data-tooltip-content="There is a Treasure Monster in this lane!<br><br>Treasure Monsters drop lots of gold, but disappear very quickly!" style="display: none;"><img src="<?php echo $CDN; ?>/assets/images/ability_template_ph.png"></div>
					</a><a id="lane1" class="lane" href="#" data-lane="1">
						<span class="label">Lane 2</span>
						<div class="bar"><div></div></div>
						<div class="lane_element" data-tooltip-func="fnTooltipLaneElementDesc"><span></span></div><!--
						--><div class="lane_enemy enemy_icon_2" id="lane1_enemy_icon_2" data-tooltip-content="There is a Boss Monster in this lane!" style="display: none;"><img src="<?php echo $CDN; ?>/assets/images/ability_template_ph.png"></div><!--
						--><div class="lane_enemy enemy_icon_4" id="lane1_enemy_icon_4" data-tooltip-content="There is a Treasure Monster in this lane!<br><br>Treasure Monsters drop lots of gold, but disappear very quickly!" style="display: none;"><img src="<?php echo $CDN; ?>/assets/images/ability_template_ph.png"></div>
					</a><a id="lane2" class="lane" href="#" data-lane="2">
						<span class="label">Lane 3</span>
						<div class="bar"><div></div></div>
						<div class="lane_element" data-tooltip-func="fnTooltipLaneElementDesc"><span></span></div><!--
						--><div class="lane_enemy enemy_icon_2" id="lane2_enemy_icon_2" data-tooltip-content="There is a Boss Monster in this lane!" style="display: none;"><img src="<?php echo $CDN; ?>/assets/images/ability_template_ph.png"></div><!--
						--><div class="lane_enemy enemy_icon_4" id="lane2_enemy_icon_4" data-tooltip-content="There is a Treasure Monster in this lane!<br><br>Treasure Monsters drop lots of gold, but disappear very quickly!" style="display: none;"><img src="<?php echo $CDN; ?>/assets/images/ability_template_ph.png"></div>
					</a>
				</div>

				<div class="teamdpscontainer">
					<span class="title_teamdps">Team DPS</span>
					<div id="teamdps"></div>
				</div><div class="teamhealthcontainer">
					<span class="title_teamhealth">Team Health</span>
					<div><div class="teamhealth" id="teamhealth_0"><div></div></div><div class="teamhealth" id="teamhealth_1"><div></div></div><div class="teamhealth" id="teamhealth_2"><div></div></div><div class="teamhealth" id="teamhealth_3"><div></div></div><div class="teamhealth" id="teamhealth_4"><div></div></div><div class="teamhealth" id="teamhealth_5"><div></div></div><div class="teamhealth" id="teamhealth_6"><div></div></div><div class="teamhealth" id="teamhealth_7"><div></div></div><div class="teamhealth" id="teamhealth_8"><div></div></div><div class="teamhealth" id="teamhealth_9"><div></div></div>					</div>
				</div>

				<div id="activeinlanecontainer">
					<span class="title_active">Abilities active in lane</span>
				</div>

				<div id="activitylog">
					<table class="player_activity">
						<tr>
							<td title="Players in current lane">Lane</td>
							<td title="Active players in game">Active</td>
							<td title="Total players in game">Total</td>
						</tr>
						<tr>
							<td id="players_in_lane">0</td>
							<td id="active_players">0</td>
							<td id="total_players">0</td>
						</tr>
					</table>
					<div id="activitycontainer"><div></div></div><div id="activityscroll"><div></div></div>

					<form id="chatform" class="chatform">
						<textarea name="message" maxlength="500" placeholder="Your chat message"></textarea>
						<button type="submit" name="button">Send</button>
					</form>
				</div>
			</div>

			<div id="row_top">
				<div id="level_container">
					<div class="game_time">
						Game Time<br>
						<span id="game_time"></span>
					</div>

					<div class="level_container2">
						Level <div id="level"></div>
					</div>

					<div class="level_time">
						Level Time<br>
						<span id="level_time"></span>
					</div>
				</div>
			</div>

			<div id="waiting_for_players_dialog" style="display: none">
				<div class="waiting_for_players_ctn">
					<div class="title_waiting">Waiting for more players to join the game</div>
					<div class="num_players_waiting_info">
						<div class="num_players_waiting_bar">
							<div></div>
						</div>
						<span id="num_players_waiting"></span> / <span id="num_players_minimum"></span>
					</div>
				</div>
			</div>

			<div id="game_over_dialog" style="display: none">
				<div class="player_dead_ctn">
					<div class="title_dead">Game Over</div>
					<div class="title_dead_break">This game is over.</div>
				</div>
			</div>

			<div id="player_dead_dialog" style="display: none">
				<div class="player_dead_ctn">
					<div class="title_dead">You are dead</div>
					<div class="title_dead_sub">Don't worry, you'll still be in this game helping to take down the monsters and earning in-game gold and items.</div>
					<div class="cannot_respawn">
						Can respawn in: 						<span class="timeleft"></span>
					</div>
					<span class="btn_respawn" id="player_respawn_btn">
						<span>Respawn Now</span>
					</span>
					<div class="automatically_respawn">
						You will respawn<br>automatically in: 						<span class="timeleft"></span>
					</div>
					<div style="clear: left"></div>
				</div>
			</div>

			<div id="spend_badge_points_dialog" style="display: none">
				<div class="spend_badge_ponts_border">
					<div class="spend_badge_ponts_ctn">
						<div class="welcome_back">Welcome Back!</div>
						<div class="desc">You have badge points to spend. You can use badge points to purchase one-time use special items below.</div>
						<div class="badge_points"><span id="num_badge_points"></span>&nbsp;Badge Points available</div>
						<div id="badge_items"></div>
					</div>
				</div>
			</div>

			<div id="loot_notification" style="display: none">
				<span>Loot dropped: </span><br><span id="loot_name"></span>
			</div>

			<div class="player_ctn">
				<div class="player">
					<div id="avatar_container">
						<img src="<?php echo $_SESSION[ 'Avatar' ]; ?>" alt="You!">
					</div>
					<div id="info_block">
						<div id="info_hp"></div>
						<div class="bar" id="health_bar">
							<div></div>
						</div>
					</div>
				</div>
			</div>

			<div id="row_bottom" style="width: 960px;">
				<div id="abilities">
					<div id="abilitiescontainer"></div>
				</div>
			</div>

			<div id="newplayer">
				Click monsters to attack
			</div>

			<div id="nextlevel">
				Level <span class="level"></span>!
			</div>
		</div>
	</div>

	<div class="game_options">
		<span class="toggle_sfx_btn" id="toggle_sfx_btn">
			<span>Toggle SFX</span>
		</span>

		<span class="toggle_music_btn" id="toggle_music_btn">
			<span>Toggle Music</span>
		</span>

		<a href="#" class="leave_game_btn">
			<span>Close<br>Game</span>
		</a>

		<div class="leave_game_helper" id="leave_game_helper">You can safely close the game or leave this screen at any time—you will continue collecting gold and damaging monsters even while away from your computer. Check back occasionally to see how you're doing and use in-game gold to purchase upgrades.</div>
	</div>

	<div style="width: 700px;margin: 0 auto;">
		<table id="stats" style="margin: 0 auto;float: left;width: 350px;background-color: #222;padding: 6px 20px;text-align: right;"></table>
		<table id="player_stats" style="margin: 0 auto;float: left;width: 350px;background-color: #222;padding: 6px 20px;text-align: right;"></table>	
	</div>

	<div hidden>
		<!-- Templates -->

		<div id="purchase_ability_item_template" class="purchase_ability_item ta_tip" data-tooltip-func="fnTooltipAbilityDesc">
			<img src="<?php echo $CDN; ?>/assets/images/ability_template_ph.png">
			<span class="nameblock">
				<span class="name"></span>
				<span class="cost"></span>
			</span>
			<span class="purchase_options">
				<div class="sub_item ten">
					x10
				</div>
				<div class="sub_item hundred">
					x100
				</div>
			</span>
		</div>

		<div id="upgradetemplate">
			<div class="upgrade">
				<div class="info">
					<div class="name"></div>
					<div class="level"></div>
					<div class="subcontainer">

					</div>
				</div>

				<a class="link" href="#" data-tooltip-func="fnTooltipUpgradeDesc">
					<span class="upgrade_text">Upgrade</span>
					<div class="cost"></div>
				</a>
			</div>
		</div>

		<div id="purchasetemplate">
			<div class="upgrade purchase">
				<div class="info">
					<div>
						<img src="<?php echo $CDN; ?>/assets/images/ability_template_ph.png" class="icon">
						<span class="name"></span>
					</div>
					<div class="level"></div>
					<div class="subcontainer">

					</div>
				</div>

				<a class="link" href="#" data-tooltip-func="fnTooltipUpgradeDesc">
					<div class="cost"></div>
				</a>
			</div>
		</div>

		<div id="abilitytemplate" class="abilitytemplate">
			<a class="link ta_tip" href="#" data-tooltip-func="fnTooltipAbilityDesc">
				<img src="<?php echo $CDN; ?>/assets/images/ability_template_ph.png">
				<div class="timeleft"></div>
			</a>
		</div>

		<div id="abilityitemtemplate" class="abilitytemplate">
			<a class="link ta_tip" href="#" data-tooltip-func="fnTooltipAbilityDesc">
				<img src="<?php echo $CDN; ?>/assets/images/ability_template_ph.png">
				<div class="timeleft abilityitem"></div>
				<div class="abilityitemquantity"></div>
			</a>
		</div>

		<div id="activitytemplate" class="activitytemplate" data-tooltip-func="fnTooltipAbilityDesc">
			<span class="icon"><img src="<?php echo $CDN; ?>/assets/images/ability_template_ph.png"></span>
			<span class="ability_text">
				<span class="name"></span> used <span class="ability"></span>
			</span>
		</div>

		<div id="chattemplate" class="activitytemplate chattemplate">
			<span class="icon"><img src="<?php echo $CDN; ?>/assets/images/ability_template_ph.png"></span>
			<span class="ability_text">
				<span class="name"></span> <span class="action">said:</span> <div class="ability"></div>
			</span>
		</div>

		<div id="servertemplate" class="activitytemplate chattemplate">
			<span class="icon"><img src="<?php echo $CDN; ?>/assets/emoticons/happy_creep.png"></span>
			<span class="ability_text">
				<span class="name">Game Notice</span>
				<div class="ability"></div>
			</span>
		</div>

		<div id="activeinlanetemplate" class="activeinlanetemplate" data-tooltip-func="fnTooltipAbilityDesc">
			<div class="icon">
				<img src="<?php echo $CDN; ?>/assets/images/ability_template_ph.png">
				<div class="quantity"></div>
			</div>
		</div>

		<div id="healthbartemplate" class="healthbartemplate">
			<div>
				<div class="name_wrapper">
					<div class="name"></div>
				</div>
				<div class="barwrapper">
					<div class="bar"><div></div></div>
					<div class="text"></div>
				</div>
			</div>
		</div>
	</div>

	<script type="text/javascript" src="<?php echo $CDN; ?>/assets/javascript/thirdparty/jquery-1.11.1.min.js"></script>
	<script type="text/javascript" src="<?php echo $CDN; ?>/assets/javascript/thirdparty/tooltip.js"></script>
	<script type="text/javascript" src="<?php echo $CDN; ?>/assets/javascript/thirdparty/pixi.min.js"></script>
	<script type="text/javascript" src="<?php echo $CDN; ?>/assets/javascript/thirdparty/pixi-spine.min.js"></script>
	<script type="text/javascript" src="<?php echo $CDN; ?>/assets/javascript/thirdparty/pixi-particles.min.js"></script>
	<script type="text/javascript" src="<?php echo $CDN; ?>/assets/javascript/thirdparty/pixi-functions.js?v=<?php echo hash_file( 'crc32', __DIR__ . '/../assets/javascript/thirdparty/pixi-functions.js' ); ?>"></script>
	<script type="text/javascript" src="<?php echo $CDN; ?>/assets/javascript/minigame.js?v=<?php echo hash_file( 'crc32', __DIR__ . '/../assets/javascript/minigame.js' ); ?>"></script>
	<script type="text/javascript" src="<?php echo $CDN; ?>/assets/javascript/towerattack.js?v=<?php echo hash_file( 'crc32', __DIR__ . '/../assets/javascript/towerattack.js' ); ?>"></script>
	<script type="text/javascript" src="<?php echo $CDN; ?>/assets/javascript/running.js?v=<?php echo hash_file( 'crc32', __DIR__ . '/../assets/javascript/running.js' ); ?>"></script>
	<script type="text/javascript" src="<?php echo $CDN; ?>/assets/javascript/network.js?v=<?php echo hash_file( 'crc32', __DIR__ . '/../assets/javascript/network.js' ); ?>"></script>
	<script type="text/javascript" src="<?php echo $CDN; ?>/assets/javascript/ui.js?v=<?php echo hash_file( 'crc32', __DIR__ . '/../assets/javascript/ui.js' ); ?>"></script>
	<script type="text/javascript" src="<?php echo $CDN; ?>/assets/javascript/easing.js?v=<?php echo hash_file( 'crc32', __DIR__ . '/../assets/javascript/easing.js' ); ?>"></script>
	<script type="text/javascript" src="<?php echo $CDN; ?>/assets/javascript/enemies.js?v=<?php echo hash_file( 'crc32', __DIR__ . '/../assets/javascript/enemies.js' ); ?>"></script>
</body>
</html>
