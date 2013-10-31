<?php
function callcore($gid, $proc, $end, $isRaw = false) {
    if(isset($_SESSION['_runPlayers'])) {
        unset($_SESSION['_runPlayers']);
    }
    $_SESSION['_runProc'] = $proc;
    $_SESSION['_runEnd'] = $end;
    $_SESSION['_runRaw'] = $isRaw;
    header('location: run.php?gid=' . $gid);
}
function endcore() {
    unset($_SESSION['_run']);
    unset($_SESSION['_runProc']);
    unset($_SESSION['_runEnd']);
    unset($_SESSION['_runRaw']);
    return $_SESSION['_runPlayers'];
}
