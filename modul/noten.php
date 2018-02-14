<?php include("session/session.php"); ?>
<?php include("../database/connect.php"); ?>

<?php if($session_usergroup == 1) : //HR ?>

    <h1 class="mt-5">Notensammlung</h1>
    <h3>Lehrlinge</h3>
    
    <?php
    
        
        $llEntries = "";
        
        
        $sql1 = "SELECT ID, bKey, firstname, lastname, deleted FROM `tb_user` WHERE tb_group_ID IN (3, 4, 5) AND deleted IS NULL;";
        $result1 = $mysqli->query($sql1);
        if ($result1->num_rows > 0) {
            while($row1 = $result1->fetch_assoc()) {
                
                $llid = $row1['ID'];
                $llbkey = $row1['bKey'];
                $llfirst = $row1['firstname'];
                $lllast = $row1['lastname'];
                $llsubjs = 0;
                $llallavg = 0;

                $gradesunderEntries = "";
                $subEntries = "";
                
                $sql2 = "SELECT us.subjectName, us.ID, us.correctedGrade, sem.semester FROM `tb_user_subject` AS us 
                        INNER JOIN tb_semester AS sem ON us.tb_semester_ID = sem.ID
                        WHERE us.tb_user_ID = $llid ORDER BY sem.semester DESC, us.creationDate DESC";
                $result2 = $mysqli->query($sql2);
                if ($result2->num_rows > 0) {
                    while($row2 = $result2->fetch_assoc()) {
                        
                        $llsubjs = $llsubjs + 1;
                        $llsubname = $row2['subjectName'];
                        $llsubid = $row2['ID'];
                        $llsubsem = $row2['semester'];
                        $llsubcorrgrade = $row2['correctedGrade'];
                        
                        $sql3 = "SELECT ID, grade, weighting FROM `tb_subject_grade` WHERE tb_user_subject_ID = $llsubid";
                        $result3 = $mysqli->query($sql3);
                        if ($result3->num_rows > 0) {
                            
                            $countgrades = 0;
                            $grades = 0;
                            $weights = 0;
                            
                            while($row3 = $result3->fetch_assoc()) {
                                
                                $gradeid = $row3['ID'];
                                $grade = $row3['grade'];
                                $gradeweight = $row3['weighting'];
                                
                                $grades = $grades + ($grade*$gradeweight);
                                $weights = $weights + $gradeweight;
                                $countgrades = $countgrades + 1;
                                
                            }
                            
                            $subgradeavg = floor(($grades / $weights) * 100) / 100;
                            $llallavg = $llallavg + $subgradeavg;
                            
                        } else {
                            $subgradeavg = "Keine Noten gefunden.";
                        }
                        
                        $countgradesunder = 0;
                        
                        $sql4 = "SELECT grade, title, reasoning FROM `tb_subject_grade` WHERE tb_user_subject_ID = $llsubid AND grade < 4";
                        $result4 = $mysqli->query($sql4);
                        if ($result4->num_rows > 0) {
                            while($row4 = $result4->fetch_assoc()) {
                                
                                $gradesunderEntry = '
                                        <div class="row gradeBelow">
                                            <div class="col-lg-4">
                                                <b>'. $llsubname .'</b>
                                            </div>
                                            <div class="col-lg-4">
                                                <b>Titel:</b> '. $row4['title'] .'
                                            </div>
                                            <div class="col-lg-4">
                                                <b>Note:</b> '. $row4['grade'] .'
                                            </div>
                                            <div class="col-lg-12">
                                                <b>Begründung:</b> '. $row4['reasoning'] .'<br/><br/>
                                            </div>
                                        </div>
                                ';
                                
                                $countgradesunder = $countgradesunder + 1;
                                $gradesunderEntries = $gradesunderEntries . $gradesunderEntry;
                            }
                        } else {
                            $countgradesunder = 0;
                        }
                        
                        if($llsubcorrgrade){
                            $subgradeavg = "<s>" . $subgradeavg . "</s> <b style='color:red;'>" . $llsubcorrgrade . "</b>";
                        }
                        
                        $subEntry = '
                                <tr>
                                    <th scope="row">'. $llsubname .'</th>
                                    <td>'. $countgrades .'</td>
                                    <td>'. $countgradesunder .'</td>
                                    <td>'. $llsubsem .'</td>
                                    <td class="subAvg" subjid="'. $llsubid .'">'. $subgradeavg .'</td>
                                    <td>
                                        <div class="row">
                                            <div class="col-lg-10">
                                                <input placeholder="Schnitt" subjid="'. $llsubid .'" type="number" class="form-control corrSubAvgNum"/>
                                            </div>
                                            <div class="col-lg-2" style="padding-left: 0;">
                                                <button type="button" subjid="'. $llsubid .'" class="btn btn-secondary corrSubAvg"><i class="fa fa-pencil-square-o" aria-hidden="true"></i></button>
                                            </div>
                                        </div>
                                    </td>
                                </tr>
                        ';
                        
                        $subEntries = $subEntries . $subEntry;
                        
                    }
                } else {
                    $subEntries = "<tr><td colspan='5'>Keine Fächer gefunden.</td><tr/>";
                }
                
                if($llsubjs > 0){
                    $calcavg = number_format((float)($llallavg/$llsubjs), 2, '.', '');
                } else {
                    $calcavg = "Keine Daten";
                }
                
                if($gradesunderEntries){
                    $gradesunderEntries = "<hr/><h3>Ungenügende Noten</h3>" . $gradesunderEntries;
                }
                
                $llEntry = '
                <div class="row">
                    <div class="card col-lg-12 userContentBox">
                        <div class="row userGradesHead header" containerID="'. $llid .'">
                            <div class="col-lg-6"><b>'. $llfirst . ' ' . $lllast .' ('. $llbkey .')</b></div>
                            <div class="col-lg-2">Schnitt: '. $calcavg .'</div>
                            <div class="col-lg-4 text-right"><i class="fa fa-chevron-down toggleDetails" style="margin-top: 5px;" aria-hidden="true"></i></div>
                        </div>
                        <div class="row">
                            <div class="col-lg-12 detailedGrades" containerID="'. $llid .'">
                                <div class="row">
                                    <div class="col-12">
                                        <hr/>
                                    </div>
                                </div>
                                <div class="card">
                                    <table class="table">
                                        <thead>
                                            <tr>
                                                <th scope="col">Fach/Modul</th>
                                                <th scope="col">Noten</th>
                                                <th scope="col">ungenügende</th>
                                                <th scope="col">Semester</th>
                                                <th scope="col">Schnitt</th>
                                                <th scope="col">Korrektur</th>
                                            </tr>
                                        </thead>
                                        <tbody>
                                            
                                            '. $subEntries .'
                                            
                                        </tbody>
                                    </table>
                                </div>
                                <div class="row">
                                    <div class="col-lg-12">
                                        '. $gradesunderEntries .'
                                    </div>
                                </div>
                                <div class="row">
                                    <div class="col-lg-12">
                                        <hr/>
                                        <div class="row">
                                            <div class="col-lg-12">
                                                Fächer/Module: '. $llsubjs .'
                                            </div>
                                            <br/>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
                ';
                
                $llEntries = $llEntries . $llEntry;
                
            }
            
        } else {
            $llEntries = "Keine Lehrlinge gefunden. <br/>";
        }
        
        echo $llEntries;
    
    ?>
    
    
    
    <script type="text/javascript" src="modul/noten/noten.js"></script>  
    
<?php elseif($session_usergroup == 2) : ?>

    <h1 class="mt-5">Alle PA-Module</h1>
    <p>Sie sind Praxisausbildner</p>
    
    
<?php elseif($session_usergroup == 3 || $session_usergroup == 4 || $session_usergroup == 5) : //LLKV&IT ?>

    <?php
        
        
        //---------------------------------- Bestehende Fächer generieren ---------------------------------------
        $sql = "SELECT us.*  FROM `tb_user_subject` AS us
            INNER JOIN tb_semester AS ss ON ss.ID = us.tb_semester_ID
            WHERE us.tb_user_ID = $session_userid
            ORDER BY ss.semester DESC, us.`creationDate` DESC";
            
        $result = $mysqli->query($sql);
        $subjects = "";
        
        if (isset($result) && $result->num_rows > 0) {
            
            while($row = $result->fetch_assoc()) {
                
                $sql3 = "SELECT * FROM `tb_semester` WHERE tb_group_ID = $session_usergroup";
                $result3 = $mysqli->query($sql3);
                $semesterList = "";
                
                if (isset($result3) && $result3->num_rows > 0) {
                    
                    while($row3 = $result3->fetch_assoc()) {
                        if($row3['ID'] == $row['tb_semester_ID']){
                            $subjectSemester = "Semester: " . $row3['semester'];
                        }
                        $semesterList = $semesterList . "<option value='". $row3['ID'] ."'>". $row3['semester'] ."</option>";
                    }
                
                }
                
                $subjectId = $row['ID'];
                $grades = "";
                $average = "";
                
                $sql2 = "SELECT * FROM `tb_subject_grade` WHERE tb_user_subject_ID = $subjectId;";
                $result2 = $mysqli->query($sql2);
                
                if (isset($result2) && $result2->num_rows > 0) {
                    $i = 0;
                    $allGrades = 0;
                    $allWeight = 0;
                    
                    $grades = $grades . '
                        <div class="alert alert-danger" fSubject="'. $row['ID'] .'" id="error" style="display: none;" role="alert"></div>
                        <table class="table table-responsive">
                            <thead>
                                <tr>
                                    <th>Datum</th>
                                    <th>Titel</th>
                                    <th>Note</th>
                                    <th>Gewichtung</th>
                                    <th></th>
                                </tr>
                            </thead>    
                            <tbody>
                    ';
                    
                    while($row2 = $result2->fetch_assoc()) {
                        
                        $i = $i + 1;
                        $allGrades = $allGrades + ($row2['grade']*($row2['weighting']));
                        $allWeight = $allWeight + $row2['weighting'];
                        
                        $gradeEntry = '
                            <tr gradeId="'. $row2['ID'] .'" class="gradeEntry">
                                <td>' . date('d.m.Y', strtotime($row2['creationDate'])) . '</td>
                                <td>' . $row2['title'] . '</td>
                                <td>' . $row2['grade'] . '</td>
                                <td>' . $row2['weighting'] . ' %</td>
                                <td><span class="fa fa-trash-o delGrade" gradeId="'. $row2['ID'] .'" aria-hidden="true" style="cursor: pointer;"></span></td>
                            </tr>
                        ';
                        
                        $grades = $grades . $gradeEntry;     
                             
                    }
                    
                    $grades = $grades . '
                            <tr>
                                <td><button type="button" fSubject="'. $row['ID'] .'" class="btn addGrade" style="padding-bottom: 0px; padding-top: 0px; margin-top: 5px;"><span class="fa fa-plus" aria-hidden="true" style="cursor: pointer;"></span></button></td>
                                <td><input fSubject="'. $row['ID'] .'" class="form-control fgradeTitle" type="text" placeholder="Titel"/></td>
                                <td><input fSubject="'. $row['ID'] .'" class="form-control fgradeNote" min="1" max="6" type="number" placeholder="Note"/></td>
                                <td><input fSubject="'. $row['ID'] .'" class="form-control fgradeWeight" min="1" type="number" placeholder="Gewichtung (in %)"/></td>
                                <td></td>
                            </tr>
                            <tr class="badDay" fSubject="'. $row['ID'] .'" style="display:none">
                                <td colspan="5"><textarea fSubject="'. $row['ID'] .'" placeholder="Begründung Note unter 4.0" class="form-control fgradeReason"></textarea></td>
                            </tr>
                        </tbody>
                    </table>  
                    ';
                    
                    if (isset($allGrades)){
                        $average = '<h2>Schnitt: ' . floor(($allGrades / $allWeight) * 100) / 100 . '</h2>';
                    }
                    
                } else {
                    $grades = '
                    <p>Noch keine Noten vorhanden. Note Eintragen:</p>
                    <div class="alert alert-danger" fSubject="'. $row['ID'] .'" id="error" style="display: none;" role="alert"></div>
                    <table>
                        <tbody>
                            <tr>
                                <td><button type="button" fSubject="'. $row['ID'] .'" class="btn addGrade" style="padding-bottom: 0px; padding-top: 0px; margin-top: 5px;"><span class="fa fa-plus" aria-hidden="true" style="cursor: pointer;"></span></button></td>
                                <td><input fSubject="'. $row['ID'] .'" class="form-control fgradeTitle" type="text" placeholder="Titel"/></td>
                                <td><input fSubject="'. $row['ID'] .'" class="form-control fgradeNote" min="1" max="6" type="number" placeholder="Note"/></td>
                                <td><input fSubject="'. $row['ID'] .'" class="form-control fgradeWeight" min="1" type="number" placeholder="Gewichtung (in %)"/></td>
                                <td></td>
                            </tr>
                            <tr class="badDay" fSubject="'. $row['ID'] .'" style="display:none">
                                <td colspan="5"><textarea fSubject="'. $row['ID'] .'" placeholder="Begründung Note unter 4.0" class="form-control fgradeReason"></textarea></td>
                            </tr>
                        </tbody>
                    </table>
                    <br/>
                    ';
                }
                
                $subjectEntry = '
                    <div fSubject="'. $row['ID'] .'" class="col-lg-1 delSubTag"></div>
                    <div fSubject="'. $row['ID'] .'" class="card col-lg-10 delSubTag" style="padding: 20px;margin: 5px;">
                        <div class="row">
                            <div class="col-lg-6">
                                <h2>'. $row['subjectName'] .'</h2>
                            </div>
                            <div class="col-lg-6" style="text-align: right;">
                                '. $average .'
                            </div>
                        </div>
                        <br/>
                        
                        <div class="row">
                            <div class="col-lg-11" style="margin-left: auto; margin-right: auto;">
                            '. $grades .'
                            </div>
                        </div>
                        
                        <div class="row">
                            <div class="col-lg-6">
                                <a href="#" class="deleteSubject" subjectId="'. $row['ID'] .'">
                                    <span class="fa fa-trash-o delSubject" subjectId="'. $row['ID'] .'" aria-hidden="true" style="cursor: pointer; font-size: larger;"></span> Fach löschen
                                </a>
                            </div>
                            <div class="col-lg-6" style="text-align: right;">
                                '. $subjectSemester .'
                            </div>
                        </div>
                    </div>
                ';
                     
                $subjects = $subjects . $subjectEntry;
                            
            }
        } else {
            $subjects = "<p>Noch keine Fächer vorhanden.</p>";
            
            $sql3 = "SELECT * FROM `tb_semester` WHERE tb_group_ID = $session_usergroup";
            $result3 = $mysqli->query($sql3);
            $semesterList = "";
                
            if (isset($result3) && $result3->num_rows > 0) {
                    
                while($row3 = $result3->fetch_assoc()) {
                    $semesterList = $semesterList . "<option value='". $row3['ID'] ."'>". $row3['semester'] ."</option>";
                }
            }
        }
        
        //---------------------------------- Bestehende Fächer generieren ende ---------------------------------------
        
        
        
    ?>

    <h1 class="mt-5">Notensammlung</h1>
    <p></p>
    <div class="row">
        <div class="col-lg-1"></div>
        <div class="alert alert-success col-lg-10" id="addedNotif" style="display: none; margin-bottom: 0px;">
            <strong></strong> Eintrag wurde hinzugefügt.
        </div>
    </div>
    <div class="row">
        
        <?php echo $subjects; ?>
        
        <!-- Neues Fach hinzufügen -->
        <hr/>
        <div class="col-lg-12">
            <div class="row">
                <div class="col-lg-1"></div>
                <div class="alert alert-success col-lg-10" id="addedNotif2" style="display: none; margin-bottom: 0px;">
                    <strong></strong> Fach wurde hinzugefügt.
                </div>
            </div>
        </div>
        <div class="col-lg-1"></div>
        <div class="col-lg-10 card" style="padding: 20px;margin: 5px;">
            <div class="row">
                <div class="col-lg-6">
                    <input type="text" id="newSubNam" class="form-control" placeholder="Fach">
                </div>
                <div class="col-lg-6">
                    <select class="form-control" id="newSubSem" placeholder="Zählt in Semester">
                        <option>Semester:</option>
                        <?php echo $semesterList; ?>
                    </select>
                </div>
            </div>
            
            <div class="row">
                <div class="col-lg-12" style="margin-top: 10px;">
                    <button type="button" class="btn col-lg-12" id="addSubject">
                        <span class="fa fa-plus" aria-hidden="true" style="cursor: pointer;"></span><b> Neues Fach hinzufügen</b>
                    </button>
                    <br/><br/>
                    <div class="alert alert-danger" id="errorForm" style="display: none;" role="alert"></div>
                </div>
            </div>
        </div>
    </div>
    
    <script type="text/javascript" src="modul/noten/noten.js"></script>  
    
<?php else : ?>
    
    <br/><br/>
    
    <div class='alert alert-danger'>
        <strong>Fehler </strong> Ihr Account wurde keiner Gruppe zugewiesen.
        Bitte wenden Sie sich an einen <a href='mailto:elia.reutlinger@baloise.ch'>Administrator</a>.
    </div>
    
<?php endif; ?>