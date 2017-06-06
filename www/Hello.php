<head>
	<link rel="stylesheet" type="text/css" href="Main.css">
</head>
<div id="content">
Hello :>
</div>
<script>
	var content = document.getElementById("content");
	var original_data = content.innerText;
	content.innerHTML = "";
	
	var text_area = document.createElement("span");
	content.appendChild(text_area);
	
	var blink = document.createElement("span");
	blink.innerHTML = "_";
	content.appendChild(blink);
	
	var visible = true;
	var addChar = function()
	{
		if(original_data.length > 0)
		{
			text_area.innerHTML = text_area.innerHTML + original_data[0];
			original_data = original_data.slice(1);
			window.setTimeout(addChar, 50);		
		}
	};
	window.setTimeout(addChar, 100);
	window.setInterval(function()
	{
		visible = !visible;
		blink.style.visibility = visible ? "visible" : "hidden";
	}, 500);
</script>