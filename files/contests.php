<?php
if ($judge['value'] != "Lockdown" || (isset($_SESSION['loggedin']) && $_SESSION['team']['status'] == 'Admin')) {
    if (isset($_GET['code'])) {
        $_GET['code'] = addslashes($_GET['code']);
        $query = "select * from contest where code = '$_GET[code]'";
        $contest = DB::findOneFromQuery($query);
        if ($contest) {
            ?>
            <script type='text/javascript'>
                var ctime = <?php echo $contest['starttime'] - time(); ?>;
                function timer() {
                    if (ctime > 0) {
                        $("div#contesttimer").html("<h4>Starts in: "+parseInt(ctime / 3600) + ":" + parseInt((ctime / 60)) % 60 + ":" + (ctime % 60)+"</h4>");
                    } else {
                        $("div#contesttimer").html("<h4>Starts in: NA</h4>");
                    }
                    if (ctime >= 0){
                        ctime--;
                        if(ctime != 0)
                            window.setTimeout("timer();", 1000);
                    }
                }
                timer();
            </script>
            <?php
            echo "<div class='contestheader'><center><h1>$contest[name]</h1></center><div id='contesttimer'><h4>Starts in:</h4></div></div>";
            if ($contest['starttime'] <= time() || (isset($_SESSION['loggedin']) && $_SESSION['team']['status'] == 'Admin')) {
                if(isset($_SESSION['loggedin']) && $_SESSION['team']['status'] == 'Admin'){
                    $query = "select * from problems where pgroup = '$_GET[code]' order by pid"; 
                    echo "<a class='btn btn-primary pull-right' style='margin: 10px 0;' href='".SITE_URL."/admincontest/$_GET[code]'><i class='icon-edit icon-white'></i> Edit</a>";
                }
                else {
                    $query = "select * from problems where pgroup = '$_GET[code]' and status != 'Deleted' order by pid";
                }
            } else {
                $query = "";
            }
            $prob = DB::findAllFromQuery($query);
            echo "<table class='table table-hover'><tr><th>Name</th><th>Score</th><th>Code</th><th>Submissions</th></tr>";
            foreach ($prob as $row) {
                $query = "SELECT (SELECT count(*) FROM runs WHERE pid=$row[pid] AND result='AC' AND access!='deleted') as ac, (SELECT count(*) FROM runs WHERE pid=$row[pid] AND access!='deleted') as tot";
                $subs = DB::findOneFromQuery($query);
                echo "<tr><td><a href='" . SITE_URL . "/problems/$row[code]'>$row[name]</a></td><td><a href='" . SITE_URL . "/problems/$row[code]'>$row[score]</a></td><td><a href='" . SITE_URL . "/submit/$row[code]'>$row[code]</a></td><td><a href='" . SITE_URL . "/problems/$row[code]'>$subs[ac]/$subs[tot]</a></td></tr>";
            }
            echo "</table><h3>Announcements</h3>$contest[announcement]";
        } else {
            echo "<br/><br/><br/><div style='padding: 10px;'><h1>Contest not Found :(</h1>The contest you are looking for is not found. Are you on the wrong website?</div><br/><br/><br/>";
        }
    } else {
        echo "<center><h1>Contests</h1></center>";
        $query = "select * from contest order by starttime desc";
        $result = DB::findAllFromQuery($query);
        echo "<table class='table table-hover'>
            <tr><th>Code</th><th>Name</th><th>Start Time</th><th>End Time</th></tr>";
        foreach ($result as $row) {
            echo "<tr><td><a href='" . SITE_URL . "/contests/$row[code]'>$row[code]</a></td><td><a href='" . SITE_URL . "/contests/$row[code]'>$row[name]</a></td><td><a href='" . SITE_URL . "/contests/$row[code]'>" . date("d-M-Y, h:i:s a", $row['starttime']) . "</a></td><td><a href='" . SITE_URL . "/contests/$row[code]'>" . date("d-M-Y, h:i:s a", $row['endtime']) . "</a></td></tr>";
        }
        echo "</table>";
    }
} else {
    echo "<br/><br/><br/><div style='padding: 10px;'><h1>Lockdown Mode :(</h1>This feature is now offline as Judge is in Lockdown mode.</div><br/><br/><br/>";
}
?>
