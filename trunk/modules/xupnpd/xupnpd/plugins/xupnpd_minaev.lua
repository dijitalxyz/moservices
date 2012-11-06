
-- feed: archive
function minaev_updatefeed(feed,friendly_name)
    local rc=false

    local feed_url='http://www.minaevlive.ru/'..feed
    local feed_name='minaev_'..string.gsub(feed,'/','_')
    local feed_m3u_path=cfg.feeds_path..feed_name..'.m3u'
    local tmp_m3u_path=cfg.tmp_path..feed_name..'.m3u'

    local feed_data=http.download(feed_url)

    if feed_data then
        local dfd=io.open(tmp_m3u_path,'w+')
        if dfd then
            dfd:write('#EXTM3U name=\"',friendly_name or feed_name,'\" type=mp4 plugin=minaev\n')

            local pattern=string.format('<h2><a href="(/%s/%%w+)">(.-)</a></h2>',feed)

            for u,name in string.gmatch(feed_data,pattern) do
                local url=string.format('http://www.minaevlive.ru%s',u)
                dfd:write('#EXTINF:0,',name,'\n',url,'\n')
            end

            dfd:close()

            if util.md5(tmp_m3u_path)~=util.md5(feed_m3u_path) then
                if os.execute(string.format('mv %s %s',tmp_m3u_path,feed_m3u_path))==0 then
                    if cfg.debug>0 then print('MinaevLive feed \''..feed_name..'\' updated') end
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

function minaev_sendurl(minaev_url,range)
    local url=nil

    if plugin_sendurl_from_cache(minaev_url,range) then return end

    local clip_page=http.download(minaev_url)

    if clip_page then
        local u=string.match(clip_page,'src="(http://live.minaevlive.ru/embed/%d+?.-)"')

        if u then
            clip_page=http.download(u)

            if clip_page then
                u=string.match(clip_page,'TM_Player.params.translation_id%s*=%s*(%d+);')

                if u then
                    u=string.format('http://live.minaevlive.ru//tm/session_%s_ru_hd.json?language=ru&quality=hd&translation_id=%s',u,u)

                    clip_page=http.download(u)

                    if clip_page then
                        u=json.decode(clip_page)

                        if u then
                            u=u['file']
                            if u and string.find(u,'%.mp4$') then
                                url=u
                            end
                        end
                    end
                end
            end
        end
    end

    if url then
        if cfg.debug>0 then print('MinaevLive Real URL: '..url) end

        plugin_sendurl(minaev_url,url,range)
    else
        if cfg.debug>0 then print('MinaevLive clip is not found') end

        plugin_sendfile('www/corrupted.mp4')
    end
end

plugins['minaev']={}
plugins.minaev.sendurl=minaev_sendurl
plugins.minaev.updatefeed=minaev_updatefeed
