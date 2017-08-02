<a class="button" style="cursor: pointer; float:right;" onclick="printCV()">Printable Version</a>
<?php require "CVContent.php" ?>
<script>
    function printCV()
    {
        var mywindow = window.open("Pages/CVPrintable.php");
    }
</script>
