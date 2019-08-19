function transferTime(time)
{
    var now = new Date();
    var sYear = time.substring(0, 4);
    var sMonth = time.substring(4, 6) - 1;
    var sDate = time.substring(6, 8);
    var sHour = time.substring(8, 10);
    var sMin = time.substring(10, 12);
    var sSecond = '00';
    var sc = 1000;

    var today = new Date(sYear, sMonth, sDate, sHour, sMin, sSecond);
    //지나간 초
    var pastSecond = parseInt((now - today) / sc, 10);

    var date;
    var hour;
    var min;
    var str = "";

    var restSecond = 0;
    if (pastSecond > 86400) {
        date = parseInt(pastSecond / 86400, 10);
        restSecond = pastSecond % 86400;
        str = date + "일전";
    } else if (pastSecond > 3600) {
        hour = parseInt(pastSecond / 3600, 10);
        restSecond = pastSecond % 3600;
        str = str + hour + "시간전";
    } else if (pastSecond > 60) {
        min = parseInt(pastSecond / 60, 10);
        restSecond = pastSecond % 60;
        str = str + min + "분전";
    } else {
        str = "조금전";
    }

    return str;
}

Number.prototype.toRad = function() {
  return this * Math.PI / 180;
}
