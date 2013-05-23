<html>
    <head>
        <title>Timety Mail Rapor</title>
        <meta http-equiv='Content-Type' content='text/html; charset=utf-8'>
    </head>
    <body>
    <center>
        <b>Mail Gönderim Raporları</b><br><br>
        <style type="text/css">
table.sample {
	border-width: 1px;
	border-spacing: 1px;
	border-style: none;
	border-color: gray;
	border-collapse: collapse;
	background-color: white;
}
table.sample th {
	border-width: 1px;
	padding: 2px;
	border-style: inset;
	border-color: gray;
	background-color: white;
}
table.sample td {
	border-width: 1px;
	padding: 2px;
	border-style: inset;
	border-color: gray;
	background-color: white;
}
</style>

<table class="sample">
            <tr>
                <th>Başlangıç</th>
                <th>Bitiş</th>
                <th>İşlem Süresi(sn)</th>
                <th>İncelenen Kullanıcı Sayısı</th>
                <th>Gönderilen Toplam Etkinlik</th>
                <th>Başarılı</th>
                <th>Başarısız</th>
            </tr>
            <?php
            require_once __DIR__ . '/config/config.php';
            $connection = mysql_connect(DB_SERVER, DB_USERNAME, DB_PASSWORD) or die(mysql_error());
            $database = mysql_select_db(DB_DATABASE) or die(mysql_error());
            $query = "SELECT * FROM MAILREPORTS";
            $result = mysql_query($query);
            $num = mysql_numrows($result);
            mysql_close();
            echo "";
            $i = 0;
            echo "";
            if ($num == 0) {
                echo "<tr><td colspan='8'>G�sterecek veri yok.</tr>";
            } else {
                while ($i < $num) {
                    $field1 = mysql_result($result, $i, "gonderilenToplam");
                    $field2 = mysql_result($result, $i, "incelenenKullanici");
                    $field3 = mysql_result($result, $i, "basariliIslem");
                    $field4 = mysql_result($result, $i, "basarisizIslem");
                    $field5 = mysql_result($result, $i, "islemTarihi");
                    $field6 = mysql_result($result, $i, "bitisTarihi");
                    $field7 = strtotime($field6) - strtotime($field5);
                    echo "<tr>
                    <th>$field5</th>
                    <th>$field6</th>
                    <th>$field7</th>
                    <th>$field2</th>
                    <th>$field1</th>
                    <th>$field3</th>
                    <th>$field4</th>
                </tr>";
                    $i++;
                }
            }
            mysql_close();
            ?>
        </table>
    </center>
</body>
</html>
