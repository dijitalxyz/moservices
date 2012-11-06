-- Copyright (C) 2011 I. Sokolov
-- happy.neko@gmail.com
-- Licensed under GNU GPL version 2 
-- https://www.gnu.org/licenses/gpl-2.0.html

-- config example:
--feeds=
--{
--    { "gametrailers", "ps3/review", "GT - PS3 - Review" },
--    { "gametrailers", "ps3/preview", "GT - PS3 - Preview" },
--    { "gametrailers", "ps3/gameplay", "GT - PS3 - Gameplay" },
--    { "gametrailers", "ps3/interview", "GT - PS3 - Interview" },
--    { "gametrailers", "ps3/feature", "GT - PS3 - Feature" },
--    { "gametrailers", "psv/all", "GT - PS Vita - All" },
--    { "gametrailers", "xb360/all", "GT - Xbox 360 - All" },
--    { "gametrailers", "all/all", "GT - All - All" }
--}

gametrailers_download_url='http://gametrailers.com/download'
gametrailers_images_url='http://gametrailers.mtvnimages.com/images'
blastcasta_url='http://www.blastcasta.com/feed-to-json.aspx?feedurl=' -- RSS to JSON converter

function gametrailers_updatefeed(feed,friendly_name)
    local rc=false

    local feed_name='gametrailers_'..string.gsub(feed,'/','_')
    local feed_m3u_path=cfg.feeds_path..feed_name..'.m3u'
    local tmp_m3u_path=cfg.tmp_path..feed_name..'.m3u'

    local feed_url=nil
    local tfeed=split_string(feed,'/')
    local platform=nil
    local article=nil
    if tfeed[1]=='all' then
        platform=''
    else
        platform='&favplats['..tfeed[1]..']='..tfeed[1]
    end
    if tfeed[2]=='all' then
        article=''
    else
        article='&type['..tfeed[2]..']=on'
    end
    feed_url=blastcasta_url..util.urlencode('www.gametrailers.com/rssgenerate.php?s1='..article..platform..'&quality[either]=on&orderby=newest&limit=50')

    local feed_data=http.download(feed_url)

    if feed_data then
        local x=json.decode(feed_data)

        feed_data=nil

        if x and x.rss and x.rss.channel then
            local dfd=io.open(tmp_m3u_path,'w+')
            if dfd then
                dfd:write('#EXTM3U name=\"',friendly_name or feed_name,'\" type=mp4 plugin=gametrailers\n')

                for i,j in ipairs(x.rss.channel) do
                    if j.item then
                        for ii,jj in ipairs(j.item) do
                            --local image_url_rc=false
                            --image_url_rc,image_url=http.sendurl(jj['exInfo:image'],1)
                            local image_url=nil
                            image_url=string.match(jj['exInfo:image'],'(/moses/.-%.jpg)')
                            if image_url then
                                dfd:write('#EXTINF:0 logo=',gametrailers_images_url,image_url,',',jj.title,'\n',jj.link,'\n')
                            else
                                dfd:write('#EXTINF:0 ',jj.title,'\n',jj.link,'\n')
                            end                            
                        end
                    end
                end
                dfd:close()

                if util.md5(tmp_m3u_path)~=util.md5(feed_m3u_path) then
                    if os.execute(string.format('mv %s %s',tmp_m3u_path,feed_m3u_path))==0 then
                        if cfg.debug>0 then print('GameTrailers feed \''..feed_name..'\' updated') end
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

function gametrailers_sendurl(gametrailers_page_url,range)

    local url=nil

    if plugin_sendurl_from_cache(gametrailers_page_url,range) then return end

    local gametrailers_id=string.match(gametrailers_page_url,'.+/video/[%w%-]-/(%d+)')

    local clip_page=http.download(gametrailers_page_url)

    if clip_page then
        local filename_mp4=string.match(clip_page,'/download/'..gametrailers_id..'/([%w_%-]-%.mp4)')
        clip_page=nil

        if filename_mp4 then
            url=gametrailers_download_url..'/'..gametrailers_id..'/'..filename_mp4
        end
    else
        if cfg.debug>0 then print('GameTrailers clip '..gametrailers_id..' page is not found') end
    end

    if url then
        if cfg.debug>0 then print('GameTrailers clip '..gametrailers_id..' real URL: '..url) end

        plugin_sendurl(gametrailers_page_url,url,range)
    else
        if cfg.debug>0 then print('GameTrailers clip '..gametrailers_id..' real URL is not found') end

        plugin_sendfile('www/corrupted.mp4')
    end
end

plugins['gametrailers']={}
plugins.gametrailers.sendurl=gametrailers_sendurl
plugins.gametrailers.updatefeed=gametrailers_updatefeed
