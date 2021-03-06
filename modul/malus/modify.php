<?php

    include("../../includes/session.php");
    include("./../../database/connect.php");
    include('../../includes/testInput.php');

    if($session_usergroup != 1){
        echo $translate[145];
    } else {

        if($_POST['todo'] == "getSemester"){

            $selUser = test_input($_POST['selUser']);

            $result = execPrepStmt($mysqli, "SELECT se.ID, se.semester FROM `tb_user` AS us
            INNER JOIN tb_group AS gr ON us.tb_group_ID = gr.ID
            INNER JOIN tb_semester AS se ON se.tb_group_ID = gr.ID
            WHERE us.ID = ?;", 'i', $selUser);

            while ($row = $result->fetch_array(MYSQLI_NUM)){
                echo "<option value='". $row[0] ."'>". $row[1] ."</option>";
            }

        } else if($_POST['todo'] == "addEntry"){

            $error = "";

            $fselUser = test_input($_POST['fselUser']);
            $fweigth = test_input($_POST['fweigth']);
            $freasoning = test_input($_POST['freasoning']);
            $semester = test_input($_POST['fselsem']);

            if(!isset($semester)){
                $error = $error . "<li>" . $translate[197] . "</li>";
            }

            if(!isset($fselUser)){
                $error = $error . "<li>" . $translate[95] . "</li>"; //Kein Lernender
            }

            if(!isset($fweigth)){
                $error = $error . "<li>" . $translate[160] . "</li>";
            }

            if(!isset($freasoning)){
                $error = $error . "<li>" . $translate[146] . "</li>";
            }

            if($error){
                echo $error;
            } else {

                execPrepStmt($mysqli, "INSERT INTO `tb_malus` (`description`, `tb_user_ID`, `weight`, `tb_semester_ID`) VALUES (?, ?, ?, ?);", 'siii', $freasoning, $fselUser, $fweigth, $semester);

                //SENDMAIL
                include("../../includes/generateMail.php");
                $msgcontent = array('{weigth}' => $fweigth, '{reason}' => $freasoning);
                $subject = strtr($translate[198], $msgcontent);
                $message = strtr($translate[199], $msgcontent);
                sendMail($subject, $message, $session_userid, $fselUser, $session_appinfo, $mysqli, $translate);

            }

        } else if($_POST['todo'] == "deleteEntry"){

            $error = "";
            $fentryId = test_input($_POST['fentryId']);
            $fselUser = test_input($_POST['$fselUser']);

            if(!isset($fentryId)){
                $error = $error . $translate[161] . ".<br/>";
            }

            if($error){
                echo $error;
            } else {

                execPrepStmt($mysqli, "DELETE FROM `tb_malus` WHERE `tb_malus`.`ID` = ?", 'i', $fentryId);

                //SENDMAIL
                include("../../includes/generateMail.php");
                $subject = ($translate[200]);
                $message = ($translate[201]);
                sendMail($subject, $message, $session_userid, $fselUser, $session_appinfo, $mysqli, $translate);

            }

        } else {
            echo "Unbekannter Befehl: " . $_POST['todo'];
        }

    }

?>
