<?php
	session_start();

	$Config = json_decode( file_get_contents( __DIR__ . '/php/files/config.json' ) );
	$CDN = $Config->Assets->Host;
	
	header( 'Content-Security-Policy: script-src \'none\';' );
?><!DOCTYPE html>
<html lang="en">
<head>
	<meta charset="UTF-8">
	<title>Tower Attack</title>
	<link rel="shortcut icon" href="/favicon.ico" type="image/x-icon">

	<link href="<?php echo $CDN; ?>/assets/css/towerattack.css?v=<?php echo hash_file( 'crc32', __DIR__ . '/assets/css/towerattack.css' ); ?>" rel="stylesheet" type="text/css">
	<link href="<?php echo $CDN; ?>/assets/css/towerattack_listgames.css?v=<?php echo hash_file( 'crc32', __DIR__ . '/assets/css/towerattack_listgames.css' ); ?>" rel="stylesheet" type="text/css">
</head>
<body class="flat_page">
	<div class="page_background" style="background-image: url('/assets/promo_bg/08_volcano_page_background.jpg');">

		<div class="section_overview">
			<div class="section_monster">
				<div class="monster_ctn">
					<img class="promo_creep" src="/assets/promo_bg/08_volcano_creep.gif">
					<img class="promo_creep_shadow" src="/assets/promo_bg/shadow_small.png">
					<div class="boss_ctn">
						<img class="promo_boss" src="/assets/promo_bg/08_volcano_boss.gif">
					</div>
					<img class="promo_boss_shadow" src="/assets/promo_bg/shadow_large.png">
				</div>
			</div>


			<div class="section_play">
				<div class="logo">
					<img src="/assets/images/logo_main_english.png">
				</div>


				<!-- Play Button -->
				<div class="current_game">
					<div>
<?php
	if( isset( $_SESSION[ 'SteamID' ] ) ):
?>
						<a href="/play/" class="main_btn">
							<span>Resume Your Game</span>
						</a>
<?php
	else:
?>
						<a href="/login.php" class="main_btn">
							<span>Login with Steam</span>
						</a>
<?php
	endif;
?>
					</div>
				</div>

				<!-- Player count -->
				<div class="player_count">
					? people playing
				</div>

			</div>
			<br clear="both">

			<div class="section_status">
				<div class="section_yours" style="height: 122px;">
					<div class="your_stats" style="padding-top: 11px;">
						<div class="title">Your stats</div>
						<div class="stat_ctn" style="height:70px">
							<div class="stat">
								Highest Level
								<br>
								<span id="player_stat_highest_level">?</span>
							</div>
							<div class="stat">
								Most Gold
								<br>
								<span id="player_stat_most_gold">?</span>
							</div>
							<div class="stat">
								Most Damage
								<br>
								<span id="player_stat_most_damage">?</span>
							</div>
							<div class="stat">
								Wormholes Used
								<br>
								<span id="player_stat_wormholes_used">?</span>
							</div>
						</div>
					</div>
				</div>
				<div class="section_today" style="height: 95px;">
					<div class="title">Today's Game</div>

					<div class="stat">
						Your Level
						<br>
						<span id="player_current_level">?</span>
					</div>
					<div class="stat two">
						Your Gold
						<br>
						<span id="player_gold">?</span>
					</div>
					<br clear="both">

				</div>
			</div>

		</div>

		<!-- HOW IT WORKS -->
		<div class="section_how_it_works">
			<div class="title">
				<span>How it works</span>
			</div>

			<div class="section_hiw_left">
				<div class="one">
					<div class="title">A fair warning</div>
					<p class="subtitle">This game is a clone, and is not affiliated with Valve. All assets are used without permission.</p>
				</div>
				<div class="two">
					<div class="title"></div>
					<p></p>
				</div>
			</div>

			<div class="section_hiw_right">
				<div class="one">
					<div class="title">Play Monster Game</div>
					<p>Join a game and fight the enemy monsters as you help your team level up, unlock new abilities, and achieve community milestones. Plus, get Summer Sale Trading Cards just for playing.</p>
				</div>
				<div class="two">
					<div class="title">Get Upgrades And
						<br>Help Your Team</div>
					<p>The more damage you do to the enemies, the more your team benefits. Plus, special abilities can heal your teammates or boost everyone’s damage and help the whole team level up faster.</p>
				</div>
				<div class="three">
					<div class="title"></div>
					<p></p>
				</div>
			</div>

		</div>



		<div class="section_credits">
			<div class="title">
				<span>Credits &amp; Acknowledgements</span>
			</div>
			<p>As you can probably tell from the style of the summer game, Valve has been kind of obsessed with a few clicker games that have become popular. It started a while ago with Cookie Clicker, then with <a href="http://store.steampowered.com/app/346900/">AdVenture Capitalist</a>				and more recently with <a href="http://store.steampowered.com/app/363970/">Clicker Heroes</a>. The Monster Summer Game is a bit of an homage to those clicker games as well as old pixel artwork from
				generations of past favorites.</p>
			<p>With the Monster Summer Game, we're also trying to take the opportunity to highlight as many of the great titles available on Steam as we can. One of the ways we're doing this is by utilizing the Steam Emoticons provided by a few titles and using them
				as ability icons within the Monster Summer Game. If you're interested in learning more about the games behind these icons, check out the list here.</p>

			<div class="credits_left">
				<div class="icon_credit">
					<a href="http://steamcommunity.com/market/listings/753/244710-%3Ashelterwildfire%3A" class="icon">
						<img src="http://cdn.steamcommunity.com//economy/emoticon/:shelterwildfire:">
					</a>
					<a href="http://store.steampowered.com/app/244710/" class="name">Shelter</a>
				</div>

				<div class="icon_credit">
					<a href="http://steamcommunity.com/market/listings/753/330450-%3Awaterrune%3A" class="icon">
						<img src="http://cdn.steamcommunity.com//economy/emoticon/:waterrune:">
					</a>
					<a href="http://store.steampowered.com/app/330450/" class="name">Runes of Brennos</a>
				</div>

				<div class="icon_credit">
					<a href="http://steamcommunity.com/market/listings/753/303690-%3AFateTree%3A" class="icon">
						<img src="http://cdn.steamcommunity.com//economy/emoticon/:FateTree:">
					</a>
					<a href="http://store.steampowered.com/app/303690/" class="name">FATE: The Cursed King</a>
				</div>

				<div class="icon_credit">
					<a href="http://steamcommunity.com/market/listings/753/288060-%3AWisp%3A" class="icon">
						<img src="http://cdn.steamcommunity.com//economy/emoticon/:Wisp:">
					</a>
					<a href="http://store.steampowered.com/app/288060/" class="name">Whispering Willows</a>
				</div>

				<div class="icon_credit">
					<a href="http://steamcommunity.com/market/listings/753/346810-%3Alike_king%3A" class="icon">
						<img src="http://cdn.steamcommunity.com//economy/emoticon/:like_king:">
					</a>
					<a href="http://store.steampowered.com/app/346810/" class="name">Pre-Civilization Marble Age</a>
				</div>

				<div class="icon_credit">
					<a href="http://steamcommunity.com/market/listings/753/295590-%3Ahgtreasure%3A" class="icon">
						<img src="http://cdn.steamcommunity.com//economy/emoticon/:hgtreasure:">
					</a>
					<a href="http://store.steampowered.com/app/295590/" class="name">Hero Generations</a>
				</div>

				<div class="icon_credit">
					<a href="http://steamcommunity.com/market/listings/753/263880-%3Agoldenmilkminer%3A" class="icon">
						<img src="http://cdn.steamcommunity.com//economy/emoticon/:goldenmilkminer:">
					</a>
					<a href="http://store.steampowered.com/app/263880/" class="name">Aqua Kitty - Milk Mine Defender</a>
				</div>

				<div class="icon_credit">
					<a href="http://steamcommunity.com/market/listings/753/239220-%3Acoinz%3A" class="icon">
						<img src="http://cdn.steamcommunity.com//economy/emoticon/:coinz:">
					</a>
					<a href="http://store.steampowered.com/app/239220/" class="name">The Mighty Quest For Epic Loot</a>
				</div>

				<div class="icon_credit">
					<a href="http://steamcommunity.com/market/listings/753/270450-%3Add_target%3A" class="icon">
						<img src="http://cdn.steamcommunity.com//economy/emoticon/:dd_target:">
					</a>
					<a href="http://store.steampowered.com/app/270450/" class="name">Robot Roller-Derby Disco Dodgeball</a>
				</div>

				<div class="icon_credit">
					<a href="http://steamcommunity.com/market/listings/753/214360-%3Atwteamrandom%3A" class="icon">
						<img src="http://cdn.steamcommunity.com//economy/emoticon/:twteamrandom:">
					</a>
					<a href="http://store.steampowered.com/app/214360/" class="name">Tower Wars</a>
				</div>

			</div>

			<div class="credits_middle">
				<div class="icon_credit">
					<a href="http://steamcommunity.com/market/listings/753/228260-%3Ahourglass%3A" class="icon">
						<img src="http://cdn.steamcommunity.com//economy/emoticon/:hourglass:">
					</a>
					<a href="http://store.steampowered.com/app/228260/" class="name">Fallen Enchantress: Legendary Heroes</a>
				</div>

				<div class="icon_credit">
					<a href="http://steamcommunity.com/market/listings/753/294370-%3Ahealplz%3A" class="icon">
						<img src="http://cdn.steamcommunity.com//economy/emoticon/:healplz:">
					</a>
					<a href="http://store.steampowered.com/app/294370/" class="name">Crowntakers</a>
				</div>

				<div class="icon_credit">
					<a href="http://steamcommunity.com/market/listings/753/302650-%3Ahappycyto%3A" class="icon">
						<img src="http://cdn.steamcommunity.com//economy/emoticon/:happycyto:">
					</a>
					<a href="http://store.steampowered.com/app/302650/" class="name">Cyto</a>
				</div>

				<div class="icon_credit">
					<a href="http://steamcommunity.com/market/listings/753/277870-%3Alucky%3A" class="icon">
						<img src="http://cdn.steamcommunity.com//economy/emoticon/:lucky:">
					</a>
					<a href="http://store.steampowered.com/app/277870/" class="name">Diehard Dungeon</a>
				</div>

				<div class="icon_credit">
					<a href="http://steamcommunity.com/market/listings/753/243950-%3Agoldstack%3A" class="icon">
						<img src="http://cdn.steamcommunity.com//economy/emoticon/:goldstack:">
					</a>
					<a href="http://store.steampowered.com/app/243950/" class="name">Divinity: Dragon Commander</a>
				</div>

				<div class="icon_credit">
					<a href="http://steamcommunity.com/market/listings/753/25890-%3Aabomb%3A" class="icon">
						<img src="http://cdn.steamcommunity.com//economy/emoticon/:abomb:">
					</a>
					<a href="http://store.steampowered.com/app/25890/" class="name">Hearts of Iron III</a>
				</div>

				<div class="icon_credit">
					<a href="http://steamcommunity.com/market/listings/753/227480-%3Agmbomb%3A" class="icon">
						<img src="http://cdn.steamcommunity.com//economy/emoticon/:gmbomb:">
					</a>
					<a href="http://store.steampowered.com/app/227480/" class="name">God Mode</a>
				</div>

				<div class="icon_credit">
					<a href="http://steamcommunity.com/market/listings/753/35700-%3Ahealthvial%3A" class="icon">
						<img src="http://cdn.steamcommunity.com//economy/emoticon/:healthvial:">
					</a>
					<a href="http://store.steampowered.com/app/35700/" class="name">Trine</a>
				</div>

				<div class="icon_credit">
					<a href="http://steamcommunity.com/market/listings/753/253030-%3Asunportal%3A" class="icon">
						<img src="http://cdn.steamcommunity.com//economy/emoticon/:sunportal:">
					</a>
					<a href="http://store.steampowered.com/app/253030/" class="name">Race The Sun</a>
				</div>

				<div class="icon_credit">
					<a href="http://steamcommunity.com/market/listings/753/329130-%3Awormwarp%3A" class="icon">
						<img src="http://cdn.steamcommunity.com//economy/emoticon/:wormwarp:">
					</a>
					<a href="http://store.steampowered.com/app/329130/" class="name">Reassembly</a>
				</div>

			</div>

			<div class="credits_right">

				<div class="icon_credit">
					<a href="http://steamcommunity.com/market/listings/753/252570-%3Aburned%3A" class="icon">
						<img src="http://cdn.steamcommunity.com//economy/emoticon/:burned:">
					</a>
					<a href="http://store.steampowered.com/app/252570/" class="name">Depths of Fear :: Knossos</a>
				</div>

				<div class="icon_credit">
					<a href=" http://steamcommunity.com/market/listings/753/273070-%3AAlive%3A" class="icon">
						<img src="http://cdn.steamcommunity.com//economy/emoticon/:Alive:">
					</a>
					<a href="http://store.steampowered.com/app/273070/" class="name">The Last Federation</a>
				</div>

				<div class="icon_credit">
					<a href="http://steamcommunity.com/market/listings/753/308360-%3Alogiaim%3A" class="icon">
						<img src="http://cdn.steamcommunity.com//economy/emoticon/:logiaim:">
					</a>
					<a href="http://store.steampowered.com/app/308360/" class="name">LogiGun</a>
				</div>

				<div class="icon_credit">
					<a href="http://steamcommunity.com/market/listings/753/243780-%3Apjkaboom%3A" class="icon">
						<img src="http://cdn.steamcommunity.com//economy/emoticon/:pjkaboom:">
					</a>
					<a href="http://store.steampowered.com/app/243780/" class="name">PixelJunk™ Monsters Ultimate</a>
				</div>

				<div class="icon_credit">
					<a href="http://steamcommunity.com/market/listings/753/249990-%3Atheorb%3A" class="icon">
						<img src="http://cdn.steamcommunity.com//economy/emoticon/:theorb:">
					</a>
					<a href="http://store.steampowered.com/app/249990/" class="name">FORCED</a>
				</div>

				<div class="icon_credit">
					<a href="http://steamcommunity.com/market/listings/753/209670-%3Accgold%3A" class="icon">
						<img src="http://cdn.steamcommunity.com//economy/emoticon/:ccgold:">
					</a>
					<a href="http://store.steampowered.com/app/209670/" class="name">Cortex Command</a>
				</div>

				<div class="icon_credit">
					<a href="http://steamcommunity.com/market/listings/753/231430-%3Acritical%3A" class="icon">
						<img src="http://cdn.steamcommunity.com//economy/emoticon/:critical:">
					</a>
					<a href="http://store.steampowered.com/app/231430/" class="name">Company of Heroes 2</a>
				</div>

				<div class="icon_credit">
					<a href=" http://steamcommunity.com/market/listings/753/293880-%3AFistpump%3A" class="icon">
						<img src="http://cdn.steamcommunity.com//economy/emoticon/:Fistpump:">
					</a>
					<a href="http://store.steampowered.com/app/293880/" class="name">Dark Scavenger</a>
				</div>

				<div class="icon_credit">
					<a href="http://steamcommunity.com/market/listings/753/338140-%3AVeneticaGoldCoin%3A" class="icon">
						<img src="http://cdn.steamcommunity.com//economy/emoticon/:VeneticaGoldCoin:">
					</a>
					<a href="http://store.steampowered.com/app/338140/" class="name">Venetica</a>
				</div>

				<div class="icon_credit">
					<a href="http://steamcommunity.com/market/listings/753/258010-%3Acooldown%3A" class="icon">
						<img src="http://cdn.steamcommunity.com//economy/emoticon/:cooldown:">
					</a>
					<a href="http://store.steampowered.com/app/258010" class="name">Ring Runner: Flight of the Sages</a>
				</div>

			</div>
		</div>
	</div>
</body>
</html>
