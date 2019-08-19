<?php

class DB_TIMETABLE
{
    public $new = "Insert Into TIMETABLES ( perid, custcd, data, year, class ) Values ( ?, ?, ?, ?, ? )";

    public $update = "Update TIMETABLES Set data = ? Where perid = ? And custcd = ? And year = ? And class = ?";

    public $load = "Select data From TIMETABLES Where perid = ? And custcd = ? And year = ? And class = ?";

    public $check = "Select * From userdb Where uid = ? Limit 1";

    public $remove = "Delete From TIMETABLES Where perid = ? And custcd = ? And year = ? And class = ?";
}

?>
