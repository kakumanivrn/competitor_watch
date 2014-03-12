<?php
session_start();
set_time_limit(0);
error_reporting(0);

if (isset($_POST['submit']) or isset($_SESSION['user'])) {
    $uname = $_POST["uname"];
    $pass = $_POST["pass"];
    if (($uname == "username" && $pass == "password") or ($_SESSION['user'] == "user")) {
        $_SESSION['user'] = "user";
        ?>
        <title> Competitor Watch</title>
        <center>
            <h1>Competitor Watch</h1>
            <br/>
            <a href="logout.php" title="Logout">Logout</a>
            <br/><br/>
            <table border="1" width="50%">
                <tr><th>S No</th><th>Website</th><th>ALexa Rank</th><th>Fb Likes</th><th>Fb Talks</th></tr>
        <?php
        $file = fopen("sites.txt", "r");
        while (!feof($file)) {
            $line = trim(fgets($file));
            $urls = explode('--', $line);
            $site_url = $urls[0];
            $fb_url = $urls[1];

            $xml = simplexml_load_file('http://data.alexa.com/data?cli=10&dat=snbamz&url=' . $site_url);
            $rank = isset($xml->SD[1]->COUNTRY) ? $xml->SD[1]->COUNTRY->attributes()->RANK : 0;
            $sites["$site_url"]["alexa"] = $rank;

            $fb_url = "https://graph.facebook.com/" . $fb_url . "/";
            $json = file_get_contents($fb_url);
            $fb_stats = json_decode($json, true);
            $sites["$site_url"]["fb_likes"] = $fb_stats["likes"];
            $sites["$site_url"]["fb_talks"] = $fb_stats["talking_about_count"];
        }
        unset($sites[""]);
        $i = 1;
        foreach ($sites as $web1 => $rank1) {
            echo "<tr>";
            echo "<td>$i</td><td>$web1</td><td style='text-align: left;'>" . formatInIndianStyle($rank1["alexa"]) . "</td>";
            echo "<td>" . formatInIndianStyle($rank1["fb_likes"]) . "</td><td>" . formatInIndianStyle($rank1["fb_talks"]) . "</td>";
            echo "</tr>\n";
            $i++;
        }
        ?>
            </table>
        </center>
                <?php
            } else
                die('Yo!');
        }
        else {
            ?>
    <title>Login</title>
    <br/><br/><br/><br/><br/><br/><br/>
    <center>
        <form action="#login" method="POST">
            <table>
                <tr><td>Username : </td><td><input type="text" name="uname"/></td></tr>
                <tr><td>Password : </td><td><input type="password" name="pass"/></td></tr>
                <tr><td colspan="2" align="center"><input type="submit" name="submit" value="Login"></td></tr>
            </table>
        </form>
    </center>
    <?php
}
?>
<?php

function formatInIndianStyle($num) {

    if (strlen($num) > 3 & strlen($num) <= 12) {
        $last3digits = substr($num, -3);
        $numexceptlastdigits = substr($num, 0, -3);
        $formatted = makeComma($numexceptlastdigits);
        $stringtoreturn = $formatted . "," . $last3digits;
    } elseif (strlen($num) <= 3) {
        $stringtoreturn = $num;
    } elseif (strlen($num) > 12) {
        $stringtoreturn = number_format($num, 2);
    }

    if (substr($stringtoreturn, 0, 2) == "-,") {
        $stringtoreturn = "-" . substr($stringtoreturn, 2);
    }

    return $stringtoreturn;
}

function makeComma($input) {
    if (strlen($input) <= 2) {
        return $input;
    }
    $length = substr($input, 0, strlen($input) - 2);
    $formatted_input = makeComma($length) . "," . substr($input, -2);
    return $formatted_input;
}
?>