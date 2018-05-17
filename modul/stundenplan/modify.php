<?php

    include("../../includes/session.php");
    include("./../../database/connect.php");
    include('../../includes/testInput.php');

    if($session_usergroup != 3){
        die("Sie haben keine Berechtigungen zu diesem Modul");
    }

    $class = test_input($_POST['classSel']);

    if(isset($class)){

        $stmt = $mysqli->prepare("UPDATE `tb_user` SET timetable = ? WHERE ID = ?");
		$stmt->bind_param("si", $class, $session_userid);
		$stmt->execute();

    } else {
        echo "Keine Klasse ausgewählt.";
    }

?>
