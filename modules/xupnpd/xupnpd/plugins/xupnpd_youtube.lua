-- Copyright (C) 2011 Anton Burdinuk
-- clark15b@gmail.com
-- https://tsdemuxer.googlecode.com/svn/trunk/xupnpd

-- 18 - 360p  (MP4,h.264/AVC)
-- 22 - 720p  (MP4,h.264/AVC) hd
-- 37 - 1080p (MP4,h.264/AVC) hd
-- 82 - 360p  (MP4,h.264/AVC)    stereo3d
-- 83 - 480p  (MP4,h.264/AVC) hq stereo3d
-- 84 - 720p  (MP4,h.264/AVC) hd stereo3d
-- 85 - 1080p (MP4,h.264/AVC) hd stereo3d

cfg.youtube_fmt=22
cfg.youtube_region='*'

youtube_api_url='http://gdata.youtube.com/feeds/mobile/'
youtube_common='alt=json&start-index=1&max-results=50'  -- &racy=include&restriction=??

function youtube_find_playlist(user,playlist)
    local start_index=1
    local max_results=50

    while(true) do
        local feed_data=http.download(youtube_api_url..'users/'..user..'/playlists?alt=json&start-index='..start_index..'&max-results='..max_results)

        if not feed_data then break end

        local x=json.decode(feed_data)
        feed_data=nil

        if not x or not x.feed or not x.feed.entry then break end

        for i,j in ipairs(x.feed.entry) do
            if j.title['$t']==playlist then
                return string.match(j.id['$t'],'.+/(%w+)$')
            end
        end
        start_index=start_index+max_results
    end

    return nil
end

-- username, favorites/username, playlist/username/playlistname, channel/channelname, search/searchstring
-- channels: top_rated, top_favorites, most_viewed, most_recent, recently_featured
function youtube_updatefeed(feed,friendly_name)
    local rc=false

    local feed_url=nil
    local feed_urn=nil

    local tfeed=split_string(feed,'/')

    local feed_name='youtube_'..string.lower(string.gsub(feed,"[/ :\'\"]",'_'))

    if tfeed[1]=='channel' then
        local region=''
        if cfg.youtube_region and cfg.youtube_region~='*' then region=cfg.youtube_region..'/' end
        feed_urn='standardfeeds/'..region..tfeed[2]..'?'..youtube_common
    elseif tfeed[1]=='favorites' then
        feed_urn='users/'..tfeed[2]..'/favorites'..'?'..youtube_common
    elseif tfeed[1]=='playlist' then
        local playlist_id=youtube_find_playlist(tfeed[2],tfeed[3])

        if not playlist_id then return false end

        feed_urn='playlists/'..playlist_id..'?'..youtube_common

    elseif tfeed[1]=='search' then
        feed_urn='videos?vq='..util.urlencode(tfeed[2])..'&'..youtube_common
    else
        feed_urn='users/'..tfeed[1]..'/uploads?orderby=published&'..youtube_common
    end

    local feed_m3u_path=cfg.feeds_path..feed_name..'.m3u'
    local tmp_m3u_path=cfg.tmp_path..feed_name..'.m3u'

    local feed_data=http.download(youtube_api_url..feed_urn)

    if feed_data then
        local x=json.decode(feed_data)

        feed_data=nil

        if x and x.feed and x.feed.entry then
            local dfd=io.open(tmp_m3u_path,'w+')

            if dfd then
                dfd:write('#EXTM3U name=\"',friendly_name or feed_name,'\" type=mp4 plugin=youtube\n')

                for i,j in ipairs(x.feed.entry) do
                    local title=j.title['$t']

                    local url=nil
                    for ii,jj in ipairs(j.link) do
                        if jj['type']=='text/html' then url=jj.href break end
                    end

                    local logo=nil
                    for ii,jj in ipairs(j['media$group']['media$thumbnail']) do
                        if tonumber(jj['width'])<480 then logo=jj.url break end
                    end

                    if cfg.feeds_fetch_length==false then
                        dfd:write('#EXTINF:0 logo=',logo,' ,',title,'\n',url,'\n')
                    else
                        dfd:write('#EXTINF:0 logo=',logo)
                        local real_url=youtube_get_video_url(url)
                        if real_url~=nil then
                            local len=plugin_get_length(real_url)
                            if len>0 then dfd:write(' length=',len) end
                        end
                        dfd:write(' ,',title,'\n',url,'\n')
                    end
                end

                dfd:close()

                if util.md5(tmp_m3u_path)~=util.md5(feed_m3u_path) then
                    if os.execute(string.format('mv %s %s',tmp_m3u_path,feed_m3u_path))==0 then
                        if cfg.debug>0 then print('YouTube feed \''..feed_name..'\' updated') end
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

function youtube_sendurl(youtube_url,range)
    local url=nil
    if plugin_sendurl_from_cache(youtube_url,range) then return end

    url=youtube_get_video_url(youtube_url)
    if url then
        if cfg.debug>0 then print('YouTube Real URL: '..url) end

        plugin_sendurl(youtube_url,url,range)
    else
        if cfg.debug>0 then print('YouTube clip is not found') end

        plugin_sendfile('www/corrupted.mp4')
    end
end

function youtube_get_video_url(youtube_url)
    local url=nil

    local clip_page=plugin_download(youtube_url)
    if clip_page then
        local stream_map=util.urldecode(string.match(clip_page,'url_encoded_fmt_stream_map=(.-)\\u0026amp;'))
        clip_page=nil

        local fmt=string.match(youtube_url,'&fmt=(%w+)$')

        if not fmt then fmt=cfg.youtube_fmt end

        if stream_map then
            local url_18=nil
            for i in string.gmatch(stream_map,'([^,]+)') do
                local item={}
                for j in string.gmatch(i,'([^&]+)') do
                    local name,value=string.match(j,'(%w+)=(.+)')
                    if name then
                        --print(name,util.urldecode(value))
                        item[name]=util.urldecode(value)
                    end
                end
                if item['itag']==tostring(fmt) then
                    url=item['url']..'&signature='..item['sig']
                    break
                elseif item['itag']=="18" then
                    url_18=item['url']..'&signature='..item['sig']
                end
                --print('\n')
            end
            if not url then url=url_18 end
        end
        return url
    else
        if cfg.debug>0 then print('YouTube clip is not found') end
        return nil
    end
end

plugins['youtube']={}
plugins.youtube.sendurl=youtube_sendurl
plugins.youtube.updatefeed=youtube_updatefeed
plugins.youtube.getvideourl=youtube_get_video_url
