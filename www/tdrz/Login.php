<body>
    <div class="wrapper">
        <div class="main" id="login">
            <div id="bg"></div>
            <div id="inner">
                <form action="Panel.php" method="post" enctype="multipart/form-data">
                    <center>
                        <table>
                            <tr>
                                <td><input type="password" name="key" placeholder="key" id="key" size=30/></td>
                                <td><input type="submit" name="login" value="Login" id="button"/></td>
                            </tr>
                        </table>
                    </center>
                </form>
            </div>
        </div>
    </div>
</body>
<script>
    // @license magnet:?xt=urn:btih:1f739d935676111cfff4b4693e3816e664797050&dn=gpl-3.0.txt GPL-v3-or-Later
    function getCookie(cname) 
    {
        var name = cname + "=";
        var ca = document.cookie.split(';');
        for(var i=0; i<ca.length; i++) {
            var c = ca[i];
            while (c.charAt(0)==' ') c = c.substring(1);
            if (c.indexOf(name) == 0) return c.substring(name.length,c.length);
        }
        return "";
    }
    window.onload = function()
    {
        var storedKey = getCookie("key");
        var keyField = document.getElementById("key");
        keyField.value = storedKey;
        keyField.onchange = function()
        {
            document.cookie = "key="+this.value;
        }
    };
    // @license-end
</script>