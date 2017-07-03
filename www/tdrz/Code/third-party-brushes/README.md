# SyntaxHighlighter
Custom brush for SyntaxHighlighter Evolved(<https://github.com/Viper007Bond/syntaxhighlighter>).  
HLSL/GLSL向けのSyntaxHighlighterが無かったので作ってみました。  

# License
This software is distributed under GNU GENERAL PUBLIC LICENSE v3. For details, see LICENCE file.  
ライセンスはSyntaxHighlighter自体がGPLになっているので，本家に合うようGPLにしています。  

## Integration
組み込み方法は3ステップです。  

### Step.1  
SyntaxHighlighter Evolveのthird-party-brushesフォルダに，shBrushHlsl.jsとshBrushGlsl.jsを置きます。  

### Step.2  
syntaxhighlighter.phpを開いて147行目付近に  

~~~~~{.php}
		wp_register_script( 'syntaxhighlighter-brush-hlsl',       plugins_url( 'third-party-brushes/shBrushHlsl.js',              __FILE__ ), array('syntaxhighlighter-core'), '20170108'     );
		wp_register_script( 'syntaxhighlighter-brush-glsl',       plugins_url( 'third-party-brushes/shBrushGlsl.js',              __FILE__ ), array('syntaxhighlighter-core'), '20170109'     );		
~~~~~

を追加します。

### Step.3  

syntaxhighlighter.phpを開いて187行目付近(groovyの後ぐらい)に  
~~~~~{.php}
			'glsl'          => 'glsl',
			'vert'          => 'glsl',
			'frag'          => 'glsl',
			'geom'          => 'glsl',
			'tese'          => 'glsl',
			'tesc'          => 'glsl',
			'comp'          => 'glsl',
			'hlsl'          => 'hlsl',
			'fx'            => 'hlsl',
~~~~~
を追加します。  

## How to use ?

後は普通のSyntaxHighlighterを適用するのと同じように  

~~~~~{.html}
[hlsl]
code
[/hlsl]
~~~~~

や 

~~~~~{.html}
[glsl]
code
[/glsl]
~~~~~~

のように鍵かっこタグで挟めば適用されるはずです。  

