<html>
    <head>
        <title>test covfefe</title>
        <link rel="shortcut icon" type="image/x-icon" href="icon.png" />
        <audio src="unox.mp3" autoplay loop=true />Your browser doe not support coffee</audio>
        <style>
        .video_bg
        {
            position: fixed;
            top: 50%;
            left: 50%;
            min-width: 100%;
            min-height: 100%;
            width: auto;
            height: auto;
            z-index: -100;
            -ms-transform: translateX(-50%) translateY(-50%);
            -moz-transform: translateX(-50%) translateY(-50%);
            -webkit-transform: translateX(-50%) translateY(-50%);
            transform: translateX(-50%) translateY(-50%);
            filter: grayscale(80%) blur(5px);
        }
        .fill
        {
            position: absolute;
            width: 100%;
            height: 100%;
        }
        .cover
        {
            background-color: black;
            opacity: 1;
            z-index: -20;
        }
        .filter
        {
            background-image: url(filter.png);
            background-blend-mode: multiply;
            background-size: 3px;
            opacity: 0.1;
            z-index:-25;
        }
        .subtitle
        {
            position:absolute;
            bottom:5%;
            left:5%;
            color: white;
            font-family: 'Times';
            font-size: 5em;
            text-decoration: underline;
            text-shadow: 1px 1px 1px black;
            z-index:-30;
        }
        body
        {
            margin: 0;
            padding: 0;
            
        }
        </style>
        <script>
            var fadeStepSize=0.01;
            function fade(elem, cb, a, b, dur) {
                var t = 0;
                var timer = setInterval(function(){
                    var f = (t/dur);
                    if(f >= 1)
                        clearInterval(timer);
                    cb(elem, a+(b-a)*f);
                    t += fadeStepSize;
                }, 1000*fadeStepSize);
            }

            window.onload = function(){
                var cover = document.getElementsByClassName("fill cover")[0];
                var vid = document.getElementsByClassName("video_bg")[0];
                var subtitle = document.getElementsByClassName("subtitle")[0];

                var timer = setInterval(function(){
                    fade(cover, function(elem, f){
                        elem.style.opacity = f;
                    },1, 0, 1);
                    fade(vid, function(elem, f){
                        elem.style.filter = "grayscale(" + f + ") blur(" + 20 * f + "px)";
                    },1, 0, 5);
                    fade(subtitle, function(elem, f){
                        elem.style.opacity = f;
                    },0, 0.8, 3);

                    clearInterval(timer);
                }, 200);
            }
        </script>
    </head>
    <body>
        <div class="fill cover"></div>
        <div class="fill filter"></div>
        <video class="video_bg" src="bakje.webm" autoplay loop>Your browser doe not support coffee</video>
        <div class="subtitle">Lekkere koffie</div>
    </body>
</html>
