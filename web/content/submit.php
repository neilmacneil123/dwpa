<? if (!$_FILES['webfile']): ?>
<h1>Submit WPA handshake captures</h1>
<script type="text/javascript">
function check_file() {
    str=document.getElementById('webfile').value.toUpperCase();
    suffix=".CAP";
    if(!(str.indexOf(suffix, str.length - suffix.length) !== -1)){
        alert('File type not allowed\nAllowed file: *.cap');
        document.getElementById('webfile').value='';
    }
}
</script>
<form id="submitform" class="form" method="post" action="?submit" enctype="multipart/form-data">
<p>
<input class="input" type="file" id="webfile" name="webfile" onchange="check_file()"/>
</p>
<p>
<input class="submitbutton" type="submit" value="Submit capture" />
</p>
</form>
<? else:
    if ($_FILES['webfile']['tmp_name'] != '') {
        require('db.php');
        require('common.php');
        if (submission($mysql, $_FILES['webfile']['tmp_name'])) {
            echo '<h1>Last 20 submitted networks from you</h1>';
            $sql = 'SELECT * FROM nets WHERE ip=? ORDER BY ts DESC LIMIT 20';
            $ip = ip2long($_SERVER['REMOTE_ADDR']);
            $stmt = $mysql->stmt_init();
            $stmt->prepare($sql);
            $stmt->bind_param('i', $ip);
            $stmt->execute();
            $data = array();
            stmt_bind_assoc($stmt, $data);
            write_nets($stmt, $data);
            $stmt->close();    
        } else
            echo 'Bad capture file';
        $mysql->close();
    } else {
        echo 'No capture submitted';
    }
endif;
?>
