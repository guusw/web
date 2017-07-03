/**
 * Desc  : DirectX High Level Shading Language Brush
 * Autor : Pocol (http://www.project-asura.com/)
 */
SyntaxHighlighter.brushes.Hlsl = function () {
    var datatypes = 'bool int uint dword half float double min16float min10float min16int min12int min16uint ' +
                    'bool1 bool2 bool3 bool4 int1 int2 int3 int4 uint2 uint1 uint3 uint4 dword1 dword2 dword3 dword4 ' +
                    'half1 half2 half3 half4 float1 float2 float3 float4 double1 double2 double3 double4 ' +
                    'int1x1 int1x2 int1x3 int1x4 int2x1 int2x2 int2x3 int2x4 int3x1 int3x2 int3x3 int3x4 int4x1 int4x2 int4x3 int4x4 ' +
                    'uint1x1 uint1x2 uint1x3 uint1x4 uint2x1 uint2x2 uint2x3 uint2x4 uint3x1 uint3x2 uint3x3 uint3x4 uint4x1 uint4x2 uint4x3 uint4x4 ' +
                    'half1x1 half1x2 half1x3 half1x4 half2x1 half2x2 half2x3 half2x4 half3x1 half3x2 half3x3 half3x4 half4x1 half4x2 half4x3 half4x4 ' +
                    'float1x1 float1x2 float1x3 float1x4 float2x1 float2x2 float2x3 float2x4 float3x1 float3x2 float3x3 float3x4 float4x1 float4x2 float4x3 float4x4 ' +
                    'double1x1 double1x2 double1x3 double1x4 double2x1 double2x2 double2x3 double2x4 double3x1 double3x2 double3x3 double3x4 double4x1 double4x2 double4x3 double4x4';

    var keywords = 'AppendStructuredBuffer asm asm_fragment BlendState bool break Buffer ByteAddressBuffer ' +
                    'case cbuffer centroid class column_major compile compile_fragment CompileShader const continue ComputeShader ComsumeStructuredBuffer ' +
                    'default DepthStencil DepthStencilView discard do DomainShader ' +
                    'else export extern false for fxgroup GeometryShader groupshared HullShader ' +
                    'if in inline inout InputPath interface line lineadj linear LineStrem ' +
                    'matrix namespace nointerpolation noperspective NULL out OutPatch packoffset pass pixelfragment PixelShader point PointStream precise ' +
                    'RasterizerState RenderTargetView return register row_major RWBuffer RWByteAddressBuffer RWStructuredBuffer RWTexture1D ' +
                    'RWTexture1DArray RWTexture2D RWTexture2DArray RWTexture3D sample sampler SamplerState SamplerComparisionState shared snorm ' +
                    'stateblock stateblock_state static string struct switch StructuredBuffer tbuffer technique technique10 technique11 ' +
                    'texture Texture1D Texture1DArray Texture2D Texture2DArray Texture2DMS Texture2DMSArray Texture3D TextureCube TextureCubeArray ' +
                    'true typedef triangle triangleadj TriangleStream uniform unorm unsigned vector vertexfragment VertexShader void volatile while';

    var functions = 'auto abort abs acos all any asdouble asfloat asin asint asuint atan atan2 ' +
                    'case catch const_cast ceil clamp cilp cos cosh countbits cross default delete dynamic_cast ddx ddx_coarse ddx_fine ddy ddy_coarse ddy_fine ' +
                    'degrees determinant distant dot dst errorf enum explicit exp exp2 f16tof32 f32tof16 friend goto ' +
                    'long mutable new operator private protected public reinterpret_cast short signed sizeof static_cast' +
                    'faceforward firstbithigh firstbitlow floor fma fmod frac frexp fwidth ' +
                    'isfinite isinf isnan ldexp length lerp lit log log10 log2 mad max min modf msad4 mul ' +
                    'noise normalize pow printf radians rcp reflect refract reservebits round rsqrt saturate ' +
                    'sign sin sincos sinh smoothstep template this throw try typename union using virtual ' +
                    'sqrt step tan tanh tex1D tex1Dbias tex1Dgrad tex1Dlog tex1Dproj tex2D tex2Dbias tex2Dgrad tex2Dlod tex2Dproj ' +
                    'tex3D tex3Dbias tex3Dgrad tex3Dlog tex3Dproj texCUBE texCUBEbias texCUBEgrad texCUBElod texCUBEproj transpose trunc';

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
SyntaxHighlighter.brushes.Hlsl.prototype = new SyntaxHighlighter.Highlighter();
SyntaxHighlighter.brushes.Hlsl.aliases = ['hlsl', 'fx'];

