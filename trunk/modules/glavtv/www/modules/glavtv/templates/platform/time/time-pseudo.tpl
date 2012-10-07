<getTime>
    nowRequestCounter = Add(nowRequestCounter, 1);
    if (null == now || nowRequestCounter == 20) {
        nowRequestCounter = 0;
        now = getUrl("<?=$cfg->get('home_url')?>?srv=utils&amp;req=seconds");
    }
</getTime>
<getTimeStr>
    timeStrRequestCounter = Add(timeStrRequestCounter, 1);
    if (null == timeStr || timeStrRequestCounter == 10) {
        timeStrRequestCounter = 0;
        timeStr = getUrl("<?=$cfg->get('home_url')?>?srv=utils&amp;req=time");
    }
</getTimeStr>
<onEnter>
    now     = null;
    timeStr = null;
    nowRequestCounter     = 0;
    timeStrRequestCounter = 0;
</onEnter>
