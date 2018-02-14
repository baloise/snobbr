<?php include("session/session.php"); ?>
<?php

    if(!isset($mysqli)){
        include("../database/connect.php");   
    }
    
?>
<nav class="navbar navbar-expand-lg navbar-inverse bg-color fixed-top" id="slideMe" style="display: none;">
    <div class="container">
        <a class="navbar-brand" href="modul/dashboard.php">
            <img src="img/logo.svg" width="150" alt="Logo">
		</a>
		<button class="navbar-toggler custom-toggler" type="button" data-toggle="collapse" data-target="#navbarResponsive" aria-controls="navbarResponsive" aria-expanded="false" aria-label="Toggle navigation">
            <span class="navbar-toggler-icon"></span>
		</button>
		<div class="collapse navbar-collapse" id="navbarResponsive">
            <ul class="navbar-nav ml-auto">
                
                <?php
                    
                    $userID = ($mysqli->query("SELECT ID FROM tb_user WHERE bKey = '$session_username'")->fetch_assoc());
                    
                    $sql1 = "SELECT * FROM tb_ind_nav AS mg INNER JOIN tb_modul AS mm ON mm.ID = mg.tb_modul_ID WHERE mg.tb_user_ID = " . $userID['ID'] . " ORDER BY mg.position";
                    
                    $result = $mysqli->query($sql1);
                    
                    if (isset($result) && $result->num_rows > 0) {
                        
                        while($row = $result->fetch_assoc()) {
                            $link = '
                            <li class="nav-item">
                                <a class="nav-link" href="'. $row["file_path"].'">'. $row["title"].'</a>
                            </li>
                            ';
                            echo $link;
                            
                        }
						
                    } else {
						
						$link = '
                        <li class="nav-item">
                            <a class="nav-link" href="modul/settings.php">Navigation bearbeiten</a>
                        </li>
                        ';
                        echo $link;
                        
                    }
                    
                    
                
                ?>
                
			</ul>
		</div>
	</div>
</nav>  