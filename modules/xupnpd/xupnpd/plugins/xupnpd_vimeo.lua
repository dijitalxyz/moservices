-- Copyright (C) 2011 Anton Burdinuk
-- clark15b@gmail.com
-- https://tsdemuxer.googlecode.com/svn/trunk/xupnpd

-- username, channel/channelname, group/groupname, album/album_id
function vimeo_updatefeed(feed,friendly_name)
    local rc=false

    local feed_url='http://vimeo.com/api/v2/'..feed..'/videos.json'
    local feed_name='vimeo_'..string.gsub(feed,'/','_')
    local feed_m3u_path=cfg.feeds_path..feed_name..'.m3u'
    local tmp_m3u_path=cfg.tmp_path..feed_name..'.m3u'

    local feed_data=http.download(feed_url)

    if feed_data then
        local x=json.decode(feed_data)

        feed_data=nil

        if x then
            local dfd=io.open(tmp_m3u_path,'w+')
            if dfd then
                dfd:write('#EXTM3U name=\"',friendly_name or feed_name,'\" type=mp4 plugin=vimeo\n')

                for i,j in ipairs(x) do
                    if cfg.feeds_fetch_length==false then
                        dfd:write('#EXTINF:0 logo=',j.thumbnail_medium,' ,',j.title,'\n',j.url,'\n')
                    else
                        dfd:write('#EXTINF:0 logo=',j.thumbnail_medium)
                        local real_url=vimeo_get_video_url(j.url)
                        if real_url~=nil then
                            local len=plugin_get_length(real_url)
                            if len>0 then dfd:write(' length=',len) end
                        end
                        dfd:write(' ,',j.title,'\n',j.url,'\n')
                    end
                end
                dfd:close()

                if util.md5(tmp_m3u_path)~=util.md5(feed_m3u_path) then
                    if os.execute(string.format('mv %s %s',tmp_m3u_path,feed_m3u_path))==0 then
                        if cfg.debug>0 then print('Vimeo feed \''..feed_name..'\' updated') end
                        rc=true
                    end
                else
                    util.unlink(tmp_m3u_path)
                end
            end
        end
    end

    return rc
end

-- send '\r\n' before data
function vimeo_sendurl(vimeo_url,range)

    if plugin_sendurl_from_cache(vimeo_url,range) then return end

    local url=vimeo_get_video_url(vimeo_url)

    if url==nil then
        if cfg.debug>0 then print('Vimeo clip '..vimeo_id..' is not found') end

        plugin_sendfile('www/corrupted.mp4')
    else
        if cfg.debug>0 then print('Vimeo Real URL: '..url) end

        plugin_sendurl(vimeo_url,url,range)
    end

end

function vimeo_get_video_url(vimeo_url)

    local url=nil

    local vimeo_id=string.match(vimeo_url,'.+/(%w+)$')

    local clip_page=plugin_download(vimeo_url)

    if clip_page then
        local sig,ts=string.match(clip_page,'"signature":"(%w+)","timestamp":(%w+),')
        clip_page=nil

        if sig and ts then
            url=string.format('http://player.vimeo.com/play_redirect?clip_id=%s&sig=%s&time=%s&quality=hd&codecs=H264&type=moogaloop_local&embed_location=',vimeo_id,sig,ts)
        end
    end

    return url

end

plugins['vimeo']={}
plugins.vimeo.sendurl=vimeo_sendurl
plugins.vimeo.updatefeed=vimeo_updatefeed
plugins.vimeo.getvideourl=vimeo_get_video_url
