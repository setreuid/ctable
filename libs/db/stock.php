<?php

class DB_STOCK
{
    public $live = "
        Select a.name, a.code, b.cost, b.updn, b.rate
        From STOCK.names a
            Left Join STOCK.live b On a.code = b.code
        Where a.flag = ? And ( a.name like ? or a.code = ? ) And b.cost > 0
    ";

    public $live_up = "
        Select a.name, a.code, b.cost, b.updn, b.rate
        From STOCK.names a
            Left Join STOCK.live b On a.code = b.code
        Where a.flag = ? And b.cost > 0
        Order by b.cost DESC Limit 10
    ";

    public $mylive = "
        Select b.name, b.code, c.cost, c.updn, c.rate, a.counts, a.cost as stcost
        From CTABLE.STOCK_STORE a
            Left Join STOCK.names b On a.code = b.code
            Left Join STOCK.live c On a.code = c.code
        Where a.uid = ?
        Order by ( a.counts * c.cost ) DESC
    ";

    public $detail_buy = "
        Select a.uid, a.money, c.code, c.name, b.cost, b.updn, b.rate
        From CTABLE.userdb a, STOCK.names c, STOCK.live b
        Where a.uid = ? And c.code = ? And b.code = ?
    ";

    public $detail_pay = "
        Select c.uid, c.money, d.name, d.code, b.cost, b.updn, b.rate, a.counts
        From CTABLE.STOCK_STORE a
            Left Join STOCK.names d On a.code = d.code
            Left Join STOCK.live b On a.code = b.code
            Left Join CTABLE.userdb c On a.uid = c.uid
        Where a.uid = ? And a.code = ?
    ";

    /***************************************************************/

    public $q_detail_buy = "
        Select a.uid, a.money, c.name, c.code, b.cost, b.updn, b.rate
        From CTABLE.userdb a, STOCK.names c, STOCK.live b
        Where a.uid = '%s' And c.code = '%s' And b.code = '%s'
    ";

    public $q_detail_buy_now_user = "
        Update CTABLE.userdb Set money = '%s' Where uid = '%s'
    ";

    public $q_detail_buy_now_add_stock = "
        Update CTABLE.userdb Set money = '%s' Where uid = '%s'
    ";

    public $q_isexist_stock = "
        Select a.code, a.counts, a.cost
        From STOCK_STORE a
        Where a.uid = '%s' And a.code = '%s'
    ";

    public $q_detail_buy_exist = "
        Update STOCK_STORE Set counts = '%s', cost = '%s'
        Where uid = '%s' And code = '%s'
    ";

    public $q_detail_buy_new = "
        Insert Into STOCK_STORE ( uid,  code, counts, cost )
                         Values ( '%s', '%s', '%s',   '%s' )
    ";

    public $q_my_info = "
        SELECT a.money, ifnull( SUM( c.cost * b.counts ), 0 ) as stmoney
        FROM CTABLE.userdb a
            Left Join CTABLE.STOCK_STORE b On a.uid = b.uid
            Left Join STOCK.live c On b.code = c.code
        Where a.uid = '%s'
    ";

    public $q_detail_pay = "
        Select c.uid, c.money, d.name, d.code, b.cost, b.updn, b.rate, a.counts, a.cost as stcost
        From CTABLE.STOCK_STORE a
            Left Join STOCK.names d On a.code = d.code
            Left Join STOCK.live b On a.code = b.code
            Left Join CTABLE.userdb c On a.uid = c.uid
        Where a.uid = '%s' And a.code = '%s'
    ";

    public $q_detail_pay_now_user = "
        Update CTABLE.userdb Set money = '%s' Where uid = '%s'
    ";

    public $q_detail_pay_remove = "
        Delete From CTABLE.STOCK_STORE Where uid = '%s' And code = '%s'
    ";

    public $q_detail_pay_update = "
        Update CTABLE.STOCK_STORE Set counts = '%s', cost = '%s' Where uid = '%s' And code = '%s'
    ";

    public $q_my_stmoney = "
        SELECT a.money + ifnull( SUM( c.cost * b.counts ), 0 ) as stmoney
        FROM CTABLE.userdb a
            Left Join CTABLE.STOCK_STORE b On a.uid = b.uid
            Left Join STOCK.live c On b.code = c.code
        Where a.uid = '%s'
    ";

    // Select g.uid, g.stmoney, g.nick
    // From (
    //     SELECT a.uid, a.nick, a.money + ifnull( SUM( c.cost * b.counts ), 0 ) as stmoney
    //     FROM CTABLE.userdb a
    //         Left Join CTABLE.STOCK_STORE b On a.uid = b.uid
    //         Left Join STOCK.live c On b.code = c.code
    //     Group by a.uid, a.nick
    // ) g
    // Where g.stmoney > 0
    // Order by g.stmoney DESC
    // Limit 10
    public $q_rank = "
        Select g.uid, g.stmoney, g.nick
        From RANK g
        Order by g.stmoney DESC
    ";

    public $q_reset_user_stock1 = "
        Delete From STOCK_STORE Where uid = '%s'
    ";

    public $q_reset_user_stock2 = "
        Update userdb Set money = '20000000', nick = '' Where uid = '%s'
    ";
}

?>
