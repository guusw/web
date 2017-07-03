/**
 * Desc  : OpenGL Shading Language Brush
 * Autor : Pocol (http://www.project-asura.com/)
 */
SyntaxHighlighter.brushes.Glsl = function () {
    var datatypes = 'float dobule int void bool uint ' +
                    'mat2 mat3 mat4 dmat2 dmat3 dmat4 ' +
                    'mat2x2 mat2x3 mat2x4 dmat2x2 dmat2x3 dmat2x4 ' +
                    'mat3x2 mat3x3 mat3x4 dmat3x2 dmat3x3 dmat3x4 ' +
                    'mat4x2 mat4x2 mat4x4 dmat4x2 dmat4x3 dmat4x4 ' +
                    'vec2 vec3 vec4 ivec3 ivec4 bvec2 bvec3 bvec4 dvec2 dvec3 dvec4 ' +
                    'uvec2 uvec3 uvec4 sampler1D sampler2D sampler3D samplerCube ' +
                    'sampler1DShadow sampler2DShadow samplerCubeShadow ' +
                    'sampler1DArray sampler2DArray sampler1DArrayShadow sampler2DArrayShadow ' +
                    'isampler1D isampler2D isampler3D isamplerCube ' +
                    'isampler1DArray isampler2DArray usampler1D usampler2D usampler3D usamplerCube ' +
                    'usampler1DArray usampler2DArray sampler2DRect sampler2DRectSahdow ' +
                    'isampler2DRect usampler2DRect samplerBuffer isamplerBuffer usamplerBuffer ' +
                    'sampler2DMS isampler2DMS usampler2DMS sampler2DMSArray isampler2DMSArray usampler2DMSArray ' +
                    'samplerCubeArray samplerCubeArrayShadow isamplerCubeArray usamplerCubeArray ' +
                    'image1D iimage1D uimage1D image2D iimage2D uimage2D image3D iimage3D uimage3D ' +
                    'image2DRect iimage2DRect uimage2DRect imageCube iimageCube uimageCube ' +
                    'imageBuffer iimageBuffer uimageBuffer image1DArray iimage1DArray uimage1DArray ' +
                    'image2DArray iimage2DArray uimage2DArray imageCubeArray iimageCubeArray uimageCubeArray ' +
                    'image2DMS iimage2DMS uimage2DMS image2DMSArray iimage2DMSArray uimage2DMSArray sampler3DRect ' +
                    'half hvec2 hvec3 hvec4 fvec2 fvec3 fvec4';

    var keywords = 'attribute const buffer uniform varying shared cherent volatile restrict readonly writeonly atomic_uint ' +
                   'layout centroid flat smooth noperspectivve patch sample break continue do for while switch case default ' +
                   'if else subroutine in out inout true false invariant precise discard return lowp mediump highp precision ' +
                   'struct common partition active asm class union enum typedef template this resource goto inline noiline public ' +
                   'static extern external interface long short fixed unsigned superp input output filter sizeof cast namespace using';

    var functions = 'radians degree sin cos tan asin acos atan sinh cosh tanh asinh acosh atanh ' +
                    'pow exp log exp2 log2 sqrt inversesqrt abs sign floor trunc round roundEven ' +
                    'ceil fract mod modf min max clamp mix step smoothstep isnan isinf ' +
                    'fma frexp ldexp length distance dot cross normalize ftransform faceforward reflect refract ' +
                    'transpose determinant inverse lessThan lessThanEqual greaterThan greaterThanEqual equal notEqual any all not ' +
                    'dFdx dFdy fwidth noise1 noise2 noise3 noise4 barrier';

    
    this.regexList = [
        { regex: SyntaxHighlighter.regexLib.singleLineCComments, css: 'comments' },         // one line comments
        { regex: SyntaxHighlighter.regexLib.multiLineCComments, css: 'comments' },          // multiline comments
        { regex: SyntaxHighlighter.regexLib.doubleQuotedString, css: 'string' },            // strings
        { regex: SyntaxHighlighter.regexLib.singleQuotedString, css: 'string' },            // strings
        { regex: /^ *#.*/gm, css: 'preprocessor' },
        { regex: new RegExp(this.getKeywords(datatypes), 'gm'), css: 'color1 bold' },
        { regex: new RegExp(this.getKeywords(functions), 'gm'), css: 'functions bold' },
        { regex: new RegExp(this.getKeywords(keywords), 'gm'), css: 'keyword bold' }
    ];
};
SyntaxHighlighter.brushes.Glsl.prototype = new SyntaxHighlighter.Highlighter();
SyntaxHighlighter.brushes.Glsl.aliases = ['glsl', 'vert', 'frag', 'geom', 'tese', 'tesc', 'comp'];

