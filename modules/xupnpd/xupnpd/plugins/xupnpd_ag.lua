-- Copyright (C) 2011 Anton Burdinuk
-- clark15b@gmail.com
-- https://tsdemuxer.googlecode.com/svn/trunk/xupnpd

-- MP4-lo
-- MP4-hi (VIP)
cfg.ag_fmt='MP4-lo'

-- videos, videos/top100pop, videos/top100best, videos/selected_by_ag
function ag_updatefeed(feed,friendly_name)
    local rc=false

    local feed_url='http://www.ag.ru/files/'..feed
    local feed_name='ag_'..string.gsub(feed,'/','_')
    local feed_m3u_path=cfg.feeds_path..feed_name..'.m3u'
    local tmp_m3u_path=cfg.tmp_path..feed_name..'.m3u'

    local feed_data=http.download(feed_url)

    if feed_data then
        local dfd=io.open(tmp_m3u_path,'w+')
        if dfd then
            dfd:write('#EXTM3U name=\"',friendly_name or feed_name,'\" type=mp4 plugin=ag\n')

            for game,id,name in string.gmatch(feed_data,'href=/files/videos/([%w_]+)/%w+#(%w+)>(.-)</a>') do
                local url=string.format('http://www.ag.ru/files/videos/%s/%s/flash',game,id)
                dfd:write('#EXTINF:0,',util.win1251toUTF8(name),'\n',url,'\n')
            end

            dfd:close()

            if util.md5(tmp_m3u_path)~=util.md5(feed_m3u_path) then
                if os.execute(string.format('mv %s %s',tmp_m3u_path,feed_m3u_path))==0 then
                    if cfg.debug>0 then print('AG feed \''..feed_name..'\' updated') end
                    rc=true
                end
            else
                util.unlink(tmp_m3u_path)
            end
        end

        feed_data=nil
    end

    return rc
end

function ag_strip_url(url)
    local n=string.find(url,'#')
    if not n then return url end
    return string.sub(url,0,n-1)
end

function ag_sendurl(ag_url,range)

    local url=nil

    if plugin_sendurl_from_cache(ag_url,range) then return end

    local clip_page=plugin_download(ag_strip_url(ag_url))

    if clip_page then
        local playlist_id=string.match(clip_page,'http://video%.ag%.ru/playlist[%w_]*/(%w+)%.xml')
        clip_page=nil

        if playlist_id then
            local ss=''
            if cfg.ag_fmt=='MP4-hi' then ss='_vip' end

            clip_page=plugin_download(string.format('http://video.ag.ru/playlist%s/%s.xml',ss,playlist_id))
            if clip_page then
                url=string.match(clip_page,'<mp4>(.-)</mp4>')
                clip_page=nil
            end
        end
    else
        if cfg.debug>0 then print('AG clip is not found') end
    end

    if url then
        if cfg.debug>0 then print('AG Real URL: '..url) end

        plugin_sendurl(ag_url,url,range)
    else
        if cfg.debug>0 then print('AG Real URL is not found') end

        plugin_sendfile('www/corrupted.mp4')
    end
end

plugins['ag']={}
plugins.ag.sendurl=ag_sendurl
plugins.ag.updatefeed=ag_updatefeed
