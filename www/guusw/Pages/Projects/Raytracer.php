<meta name="title" content="Path Tracer">
<meta name="image" content="Images/tracer.png">
<meta name="index" content=3>
<meta name="lang" content="C++">
<meta name="platform" content="Windows (with NVIDIA GPU)">
<meta name="tools" content="Microsoft Visual Studio 2013,CUDA">
<meta name="duration" content="10 weeks">
</head>

<p>This is a path tracer I made to render a scene with accurate reflections, reflections and in other ways simulate the behaviour of light.</p>
<p>This was one of the most fun projects I did, and at the time I never heard of the concept of raytracing before. After making a basic raytracer, I also added Monte Carlo sampling to increase the quality and accuracy of the shadows and to simulate light bouncing.</br>
During this I also experimented with GPU-acceleration and decided to implement my final version in CUDA which provides a pretty big speed-up for parallel tasks like ray/path tracing.</br>
I wanted to add different shapes than just spheres and cubes so I did some research about Bounding Volume Hierarchies and implemented that in my path tracer to load and display scenes loaded from .obj files.</p>
<p>Below are some images that took about 1 minute to render each.</p>

<?php $gallery_path = "Images/Raytracer"; require "Gallery.php"; ?>
