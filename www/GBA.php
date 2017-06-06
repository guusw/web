<?php
	ini_set('display_startup_errors',1);
	ini_set('display_errors',1);
	error_reporting(-1);
	
	// Get and filter rom name
	$path = "GBA/";
	$emu_path = $path."Emulator/";
	$rom_path = $_REQUEST["f"];
	if(isset($_REQUEST["n"]))
	{
		$rom_name = $_REQUEST["n"];
	}
	else
	{
		$rom_name = pathinfo($rom_path, PATHINFO_FILENAME);
	}
	if(!file_exists($rom_path))
	{
		echo "<h1>ROM not found $rom_path</h1>";
		exit(0);
	}
?>
<head>
	<title>[GBA] <?php echo $rom_name ?></title>
	<meta charset="UTF-8">
	<script>
		window.onload = function()
		{
			initIodine();
			loadHostBIOS();
			loadHostROM(<?php echo "\"$rom_path\""; ?>);
		};
	</script>
	<!--Required Scripts-->
	<script src="GBA/Emulator/IodineGBA/includes/TypedArrayShim.js"></script>
	<script src="GBA/Emulator/IodineGBA/core/Cartridge.js"></script>
	<script src="GBA/Emulator/IodineGBA/core/DMA.js"></script>
	<script src="GBA/Emulator/IodineGBA/core/Emulator.js"></script>
	<script src="GBA/Emulator/IodineGBA/core/Graphics.js"></script>
	<script src="GBA/Emulator/IodineGBA/core/RunLoop.js"></script>
	<script src="GBA/Emulator/IodineGBA/core/Memory.js"></script>
	<script src="GBA/Emulator/IodineGBA/core/IRQ.js"></script>
	<script src="GBA/Emulator/IodineGBA/core/JoyPad.js"></script>
	<script src="GBA/Emulator/IodineGBA/core/Serial.js"></script>
	<script src="GBA/Emulator/IodineGBA/core/Sound.js"></script>
	<script src="GBA/Emulator/IodineGBA/core/Timer.js"></script>
	<script src="GBA/Emulator/IodineGBA/core/Wait.js"></script>
	<script src="GBA/Emulator/IodineGBA/core/CPU.js"></script>
	<script src="GBA/Emulator/IodineGBA/core/Saves.js"></script>
	<script src="GBA/Emulator/IodineGBA/core/sound/FIFO.js"></script>
	<script src="GBA/Emulator/IodineGBA/core/sound/Channel1.js"></script>
	<script src="GBA/Emulator/IodineGBA/core/sound/Channel2.js"></script>
	<script src="GBA/Emulator/IodineGBA/core/sound/Channel3.js"></script>
	<script src="GBA/Emulator/IodineGBA/core/sound/Channel4.js"></script>
	<script src="GBA/Emulator/IodineGBA/core/CPU/ARM.js"></script>
	<script src="GBA/Emulator/IodineGBA/core/CPU/THUMB.js"></script>
	<script src="GBA/Emulator/IodineGBA/core/CPU/CPSR.js"></script>
	<script src="GBA/Emulator/IodineGBA/core/graphics/Renderer.js"></script>
	<script src="GBA/Emulator/IodineGBA/core/graphics/RendererProxy.js"></script>
	<script src="GBA/Emulator/IodineGBA/core/graphics/BGTEXT.js"></script>
	<script src="GBA/Emulator/IodineGBA/core/graphics/BG2FrameBuffer.js"></script>
	<script src="GBA/Emulator/IodineGBA/core/graphics/BGMatrix.js"></script>
	<script src="GBA/Emulator/IodineGBA/core/graphics/AffineBG.js"></script>
	<script src="GBA/Emulator/IodineGBA/core/graphics/ColorEffects.js"></script>
	<script src="GBA/Emulator/IodineGBA/core/graphics/Mosaic.js"></script>
	<script src="GBA/Emulator/IodineGBA/core/graphics/OBJ.js"></script>
	<script src="GBA/Emulator/IodineGBA/core/graphics/OBJWindow.js"></script>
	<script src="GBA/Emulator/IodineGBA/core/graphics/Window.js"></script>
	<script src="GBA/Emulator/IodineGBA/core/graphics/Compositor.js"></script>
	<script src="GBA/Emulator/IodineGBA/core/memory/DMA0.js"></script>
	<script src="GBA/Emulator/IodineGBA/core/memory/DMA1.js"></script>
	<script src="GBA/Emulator/IodineGBA/core/memory/DMA2.js"></script>
	<script src="GBA/Emulator/IodineGBA/core/memory/DMA3.js"></script>
	<script src="GBA/Emulator/IodineGBA/core/cartridge/SaveDeterminer.js"></script>
	<script src="GBA/Emulator/IodineGBA/core/cartridge/SRAM.js"></script>
	<script src="GBA/Emulator/IodineGBA/core/cartridge/FLASH.js"></script>
	<script src="GBA/Emulator/IodineGBA/core/cartridge/EEPROM.js"></script>
	<!--Add your webpage scripts below-->
	<script src="GBA/Emulator/user_scripts/XAudioJS/swfobject.js"></script>
	<script src="GBA/Emulator/user_scripts/XAudioJS/resampler.js"></script>
	<script src="GBA/Emulator/user_scripts/XAudioJS/XAudioServer.js"></script>
	<script src="GBA/Emulator/user_scripts/IodineGBAROMLoadGlueCode.js"></script>
	<script src="GBA/Emulator/user_scripts/IodineGBAJoyPadGlueCode.js"></script>
	<script src="GBA/Emulator/user_scripts/IodineGBASavesGlueCode.js"></script>
	<script src="GBA/Emulator/user_scripts/IodineGBAGraphicsGlueCode.js"></script>
	<script src="GBA/Emulator/user_scripts/IodineGBAAudioGlueCode.js"></script>
	<script src="GBA/Emulator/user_scripts/IodineGBACoreGlueCode.js"></script>
	<script src="GBA/Emulator/user_scripts/IodineGBAWorkerGlueCode.js"></script>
	<script src="GBA/Emulator/user_scripts/base64.js"></script>
	<link rel="stylesheet" href="GBA/Emulator/user_css/main.css">
</head>
<body>
	<div id="main">
		<canvas id="emulator_target" width="240" height="160"></canvas>
	</div>
	<span id="tempMessage">...</span>
	<div id="control_panel">
		<div id="play_controls">
			<form>
				<fieldset>
					<legend>Controls: </legend>
					<div id="playControls">
						<button id="play">Play</button>
						<button id="pause">Pause</button>
						<button id="restart">Restart</button>
					</div>
					<label>Skip Boot Intro: </label><input type="checkbox" id="skip_boot" class="checkbox">
					<label>Smooth Scaling: </label><input type="checkbox" id="toggleSmoothScaling" checked="checked" class="checkbox">
					<label>Dynamic Speed: </label><input type="checkbox" id="toggleDynamicSpeed" class="checkbox">
					<label>Sound: </label><input type="checkbox" id="sound" class="checkbox">
					<label>Volume: </label><input type="range" id="volume" class="checkbox">
				</fieldset>
			</form>
		</div>
		<div id="save_controls">
			<form>
				<fieldset>
					<legend>Saves: </legend>
					<div id="saveControls">
						<div class="fileLoad">
							<label>Import:</label><input type="file" id="import" class="files">
						</div>
						<a href="./" id="export" target="_new">Export All Saves</a>
					</div>
				</fieldset>
			</form>
		</div>
		<div id="speed">
		</div>
	</div>
</body>