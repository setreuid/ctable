<?php

class DB_BOARD
{
    public $new = "Insert Into BOARD ( perid, custcd, board_idx, data, inputdate, latitude, longitude, address, ipaddr, tier, nick ) Values ( ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ? )";

    public $remove = "Delete a, b From BOARD As a
                      Left Join BOARD_IMAGE As b On a.id = b.board_idx
                      Where a.id = ?";

    public $load = "
                        Select a.id,
                            a.custcd,
                            a.board_idx,
                            a.data,
                            a.inputdate,
                            a.req,
                            a.tier,
                            a.nick,
                            b.id As image,
                            (case a.perid when '20114764' then ' root' when ? then ' my' else '' end) as perid,
                            count(case when c.islike = 1 then 1 end) as good,
                            count(case when c.islike = 2 then 1 end) as bad,
                            ifnull(d.islike, 0) as islike
                        From BOARD a
                            Left Join BOARD_IMAGE b On a.id = b.board_idx
                            Left Join BOARD_LIKE c On a.id = c.bid
                            Left Join BOARD_LIKE d On a.id = d.bid And d.perid = ?
                        Where a.custcd = ?
                            And a.board_idx = ?
                            And a.id > 47950
                        Group By a.id, a.custcd, a.board_idx, a.data, a.inputdate, a.req, a.tier, a.nick, b.id, d.islike
                        Order By a.req DESC, a.id DESC
                        Limit ?, 30
                ";

    public $load_last = "
                        Select a.id,
                            a.custcd,
                            a.board_idx,
                            a.data,
                            a.inputdate,
                            a.req,
                            a.tier,
                            a.nick,
                            b.id As image,
                            (case a.perid when '20114764' then ' root' when ? then ' my' else '' end) as perid,
                            count(case when c.islike = 1 then 1 end) as good,
                            count(case when c.islike = 2 then 1 end) as bad,
                            ifnull(d.islike, 0) as islike
                        From BOARD a
                            Left Join BOARD_IMAGE b On a.id = b.board_idx
                            Left Join BOARD_LIKE c On a.id = c.bid
                            Left Join BOARD_LIKE d On a.id = d.bid And d.perid = ?
                        Where a.custcd = ?
                            And a.board_idx = ?
                            And a.id > ?
                        Group By a.id, a.custcd, a.board_idx, a.data, a.inputdate, a.req, a.tier, a.nick, b.id, d.islike
                        Order By a.id DESC
                    ";


    public $get_my_info = "
        Select uid, nick From userdb Where uid = '%s'
    ";

    public $has_like = "Select * From BOARD_LIKE Where bid = '%s' And perid = '%s'";

    public $new_like = "Insert Into BOARD_LIKE ( bid, perid, islike ) Values ( '%s', '%s', '%s' )";

    public $set_like = "Update BOARD_LIKE Set islike = '%s' Where bid = '%s' And perid = '%s'";

    public $get_like = "
        Select a.id,
            count(case when c.islike = 1 then 1 end) as good,
            count(case when c.islike = 2 then 1 end) as bad,
            ifnull(d.islike, 0) as islike
        From BOARD a
            Left Join BOARD_LIKE c On a.id = c.bid
            Left Join BOARD_LIKE d On a.id = d.bid And d.perid = '%s'
        Where a.id = '%s'
        Group By d.islike
    ";
}

?>
