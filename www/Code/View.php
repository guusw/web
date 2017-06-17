<head>
<title><?php echo "tdrz.nl - ".get_friendly_name(); ?></title>
<script type="text/javascript" src="code/sh/shCore.js"></script>
<link href="code/sh/shCore.css" rel="stylesheet" type="text/css" />
<link href="code/sh/shCoreFadeToGrey.css" rel="stylesheet" type="text/css" />
<style>
    body
    {
        background-color: black;
        font-family: "Segoe UI";
        margin: 20px 15px;
        margin-bottom: 0px;
        padding-bottom: 0px;
    }
    .content
    {
        border-radius: 1px;
        border: solid 1px #3fed36;
        border-top: 0px solid;
    }
    .title 
    {
        padding: 8px;
        padding-left: 14px;
        padding-right: 8px;
        font-size: 30px;
        color: white;
        display: flex;
        background-color: #1f241f;
        border-radius: 4px;
        border: solid 2px #3fed36;
    }
    .title .left
    {
        justify-content: flex-start;
        flex: 2;
    }
    .title .right
    {
        color: #a6a6a6;
        justify-content: flex-end;
        margin-right: 15px;
    }
    .title .dl
    {
        border-radius: 2px;
        border: solid 1px #3fed36;
        padding: 0px 5px;
        font-size: 90%;
        color: #a6a6a6;
        justify-content: flex-end;
        cursor: pointer;
        text-decoration: none;
    }
</style>
</head>
<body>
<?php
    class LangDef
    {
        function __construct($name, $brush_script, $brush_name)
        {
            $this->name = $name;
            $this->brush_script = $brush_script;
            $this->brush_name = $brush_name;
        }
    }
    
    $fn = pathinfo($full_path, PATHINFO_FILENAME);
    $extension = pathinfo($full_path, PATHINFO_EXTENSION);
    
    function get_friendly_name()
    {
        global $fn, $friendly_name;
        if(isset($friendly_name))
        {
            $fn = pathinfo($friendly_name, PATHINFO_FILENAME);
        }
        return $fn;
    }
    function select_language($ext)
    {
        $languages = array();
        $languages["sh"] = new LangDef("XML", "shBrushBash.js", "xml");
        $languages["js"] = new LangDef("JavaScript", "shBrushJScript.js", "js");
        $languages["php"] = new LangDef("PHP", "shBrushPhp.js", "php");
        $languages["xml"] = new LangDef("XML", "shBrushXml.js", "xml");
        $languages["c"] = new LangDef("C", "shBrushCpp.js", "c");
        $languages["h"] = $languages["hpp"] = $languages["cpp"] = new LangDef("C++", "shBrushCpp.js", "cpp");
        $languages["py"] = new LangDef("Python", "shBrushPython.js", "py");
        $languages["cs"] = new LangDef("C#", "shBrushCSharp.js", "csharp");
        if(!isset($languages[$ext]))
        {
            $lang = new LangDef("plain text", "shBrushPlain.js", "plain");
        }
        else
        {
            $lang = $languages[$ext];
        }
        if(count($lang->brush_script) > 0)
        {
            $brush_script_path = "code/sh/".$lang->brush_script;
            echo "<script type=\"text/javascript\" src=\"$brush_script_path\"></script>";
        }
        return $lang;
    }
    
    $lang = select_language($extension);
    echo "<div class=\"title\">";
    echo "<div class=\"left\">";
    echo get_friendly_name();
    echo "</div>";
    echo "<div class=\"right\">";
    echo $lang->name;
    echo "</div>";
    echo "<a class=\"dl\" href=\"Request.php?f=$fn&cv=0\">download</a>";
    echo "</div>";
    
    echo "<div class=\"content\">";
    echo "<pre class=\"brush: ".$lang->brush_name."\">";
    $dataFormated =  htmlspecialchars(file_get_contents("$full_path"));
    
    // Remove possible BOM
    $dataFormated = str_replace("\xEF\xBB\xBF",'',$dataFormated);
    
    echo "$dataFormated";
    echo "</pre></div>";
 ?>
<script type="text/javascript">
    var pre = document.getElementsByClassName("content")[0].children[0];
    SyntaxHighlighter.highlight(pre);
    var highlighter = document.getElementsByClassName("syntaxhighlighter")[0];
    //highlighter.style.overflow = "hidden";
    highlighter.setAttribute('style', 'overflow-y:hidden !important; min-height: 100%; margin: 0 !important; padding-top:10px; padding-bottom: 20px;');
    console.log(highlighter);
</script>
</body>