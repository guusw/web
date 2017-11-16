<meta name="title" content="Spacegame">
<meta name="image" content="Images/spacegame.png">
<meta name="index" content=9>
<meta name="lang" content="C++">
<meta name="platform" content="Windows, Linux (Server)">
<meta name="tools" content="Microsoft Visual Studio 2013, FreeImage, FreeType, OpenGL">
<meta name="duration" content="10 weeks">
</head>

<p>I made this game together with another programmer as a demonstration for our networking system.</p>
<p>I wrote some gameplay code that synchronizes and interpolates the player positions, handles shooting and synchronizes the scoreboard.
The rendering pipeline I wrote for this project uses OpenGL, has HDR rendering and a bloom filter over the screen. It also supports particle systems and trail renderers (see video below).
I also wrote the GUI system used throughout the game and the menu, it is a basic combination of horizontal and vertical layout containers.
</p>
<p>
For the networking side, I wrote a basic encryption layer for network traffic that uses AES encryption and RSA for exchanging keys. I wrote this system from scratch in order to learn more public key cryptography and symmetric key cryptography.
</p>

<?php $gallery_path = "Videos/spacegame.webm,Images/Spacegame"; require "Gallery.php"; ?>
