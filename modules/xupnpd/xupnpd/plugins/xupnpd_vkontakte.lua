-- Copyright (C) 2011-2012 I. Sokolov
-- happy.neko@gmail.com
-- Licensed under GNU GPL version 2 
-- https://www.gnu.org/licenses/gpl-2.0.html

-- config example:
--feeds=
--{
--    { "vkontakte", "my", "VK - Videos from my page" },
--    { "vkontakte", "group/group_id", "VK - Videos from group" },
--    { "vkontakte", "group/group_id/album_id", "VK - Videos from group's album" },
--    { "vkontakte", "user/user_id", "VK - Videos from user" },
--    { "vkontakte", "user/user_id/album_id", "VK - Videos from user's album" },
--    { "vkontakte", "search/searchstring", "VK - Search videos" },
--    { "vkontakte", "search_hd/searchstring", "VK - Search only HD videos" }
--}

vk_app_id='2432090'
vk_app_scope='groups,friends,video,offline,nohttps'
vk_api_server='http://api.vk.com'
vk_api_request_get_videos='/method/video.get?width=160&count=50'
vk_api_request_search_videos='/method/video.search?count=50'
vk_api_request_get_user_info='/method/getProfiles?'
vk_api_request_get_user_groups='/method/groups.get?'
vk_api_request_get_user_friends='/method/friends.get?'
--FIXME return local files
error_file_video_disabled='http://dl.dropbox.com/u/127793/access-denied.mp4'
error_file_video_unsupported_format='http://dl.dropbox.com/u/127793/unsupported-flv.mp4'
-- ffmpeg -loop_input -t 3 -i ./localmedia/access-denied.png -vcodec libx264 -crf 24 -x264opts bluray-compat=1:level=41:ref=4:b-pyramid=strict:keyint=10 -threads 0 ./localmedia/access-denied.mp4


function vk_updatefeed(feed,friendly_name)
    local rc=false

    if not vk_is_signed_in() then
        return false -- TODO show nice please sign-in message
    end

    local feed_name='vk_'..string.gsub(feed,'[/%s]','_')
    local feed_m3u_path=cfg.feeds_path..feed_name..'.m3u'
    local tmp_m3u_path=cfg.tmp_path..feed_name..'.m3u'

    local feed_url=nil
    local vk_api_request=nil
    local tfeed=split_string(feed,'/')

    local logo_label="thumb"
    if tfeed[1]=='group' then
        if tfeed[3] then
            feed_url=vk_api_format_url(vk_api_request_get_videos,{['gid']=tfeed[2],['aid']=tfeed[3]})
        else
            feed_url=vk_api_format_url(vk_api_request_get_videos,{['gid']=tfeed[2]})
        end     
        logo_label="image"
    elseif tfeed[1]=='user' then
        if tfeed[3] then
            feed_url=vk_api_format_url(vk_api_request_get_videos,{['uid']=tfeed[2],['aid']=tfeed[3]})
        else
            feed_url=vk_api_format_url(vk_api_request_get_videos,{['uid']=tfeed[2]})
        end     
        logo_label="image"
    elseif tfeed[1]=='search' then
        feed_url=vk_api_format_url(vk_api_request_search_videos,
            { ['sort']=get_sort_order(tfeed[2]), ['q']=tfeed[3] })
    elseif tfeed[1]=='search_hd' then
        feed_url=vk_api_format_url(vk_api_request_search_videos,
            { ['hd']='1', ['sort']=get_sort_order(tfeed[2]), ['q']='"'..tfeed[3]..'"' })
    else
        -- my
        feed_url=vk_api_format_url(vk_api_request_get_videos,{['uid']=cfg.vk_api_user_id})
        logo_label="image"
    end

    local feed_data=http.download(feed_url)
    if feed_data then
        local x=json.decode(feed_data)
        feed_data=nil

        if vk_check_response_for_errors(x) then
            return false
        end

        if x and x.response then
            local dfd=io.open(tmp_m3u_path,'w+')
            if dfd then
                dfd:write('#EXTM3U name=\"',friendly_name or feed_name,'\" type=mp4 plugin=vkontakte\n')

                for i,j in pairs(x.response) do
                    if type(j) == 'table' then
                        dfd:write('#EXTINF:0 logo=',string.gsub(j[logo_label],"vkontakte%.ru/","vk.com/"),',',unescape_html_string(j.title),'\n',string.gsub(j.player,"vkontakte%.ru/","vk.com/"),'\n')
                    end
                end
                dfd:close()

                if util.md5(tmp_m3u_path)~=util.md5(feed_m3u_path) then
                    if os.execute(string.format('mv %s %s',tmp_m3u_path,feed_m3u_path))==0 then
                        if cfg.debug>0 then print('VK feed \''..feed_name..'\' updated') end
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

function vk_sendurl(vk_page_url,range)
    local url=nil

    if plugin_sendurl_from_cache(vk_page_url,range) then return end

    local clip_page=http.download(vk_page_url)

    if clip_page then
        if string.match(clip_page, "(position:absolute; top:50%%; text%-align:center;)") then
            url=error_file_video_disabled
        else
            local host=string.match(clip_page,"video_host%s*=%s*'(.-)'")
            local uid=string.match(clip_page,"uid%s*=%s*'(%w-)'")
            local vtag=string.match(clip_page,"vtag%s*=%s*'([%w%-_]-)'")
            local no_flv=string.match(clip_page,"video_no_flv%s*=%s*(%d-)%D")
            local max_hd=string.match(clip_page,"video_max_hd%s*=%s*'(%d-)'")
            url=vk_get_video_url(host, uid, vtag, no_flv, max_hd)
        end
        clip_page=nil
    else
        if cfg.debug>0 then print('VK Clip page is not found') end
    end

    if url then
        if cfg.debug>0 then print('VK Clip real URL: '..url) end
        plugin_sendurl(vk_page_url,url,range)
    else
        if cfg.debug>0 then print('VK Clip real URL is not found') end
    end
end

function vk_api_format_url(base_url, params, params_noencode)
    local url=nil
    if string.sub(base_url, -1) == '?' then
        url=vk_api_server..base_url..'access_token='..cfg.vk_api_access_token
    else
        url=vk_api_server..base_url..'&access_token='..cfg.vk_api_access_token
    end
    if params then
        for key,value in pairs(params) do
            url=url..'&'..key..'='..util.urlencode(value)
        end    
    end
    if params_noencode then
        for key,value in pairs(params_noencode) do
            url=url..'&'..key..'='..value
        end    
    end

    local params_for_sig=nil
    if string.sub(base_url, -1) == '?' then
        params_for_sig=base_url..'access_token='..cfg.vk_api_access_token
    else
        params_for_sig=base_url..'&access_token='..cfg.vk_api_access_token
    end    
    if params then
        for key,value in pairs(params) do
            params_for_sig=params_for_sig..'&'..key..'='..value
        end    
    end
    if params_noencode then
        for key,value in pairs(params_noencode) do
            params_for_sig=params_for_sig..'&'..key..'='..value
        end    
    end
    url=url..'&sig='..vk_api_request_signature(params_for_sig, cfg.vk_api_secret)
    if cfg.debug>0 then print('VK API request URL '..url) end
    return url
end

function vk_api_request_signature(request, secret)
    return(util.md5_string_hash(request..secret))
end

function unescape_html_string(str)
    if str then
        result = string.gsub(str, "&quot;", '"')
        result = string.gsub(result, "&apos;", "'")
        result = string.gsub(result, "&amp;", '&')
        result = string.gsub(result, "&lt;", '<')
        result = string.gsub(result, "&gt;", '>')
        return result
    else
        return nil
    end
end

function vk_is_signed_in()
    if cfg.vk_api_access_token and cfg.vk_api_secret and cfg.vk_api_user_id then
        return true
    else
        return false
    end
end

function vk_check_response_for_errors(response)
    if response and type(response) == "table" and response.error and type(response.error) == "table" then
        if cfg.debug>0 then 
            print('VK API response error code '..response.error.error_code..': '..response.error.error_msg)
        end
        return response.error.error_code
    elseif not response then
        return "NO RESPONSE"
    else
        return nil
    end
end

function vk_get_name()
    if vk_is_signed_in() then
        local url=vk_api_format_url(vk_api_request_get_user_info, 
            {['uids']=cfg.vk_api_user_id, ['fields']='first_name,last_name'})
        local data=http.download(url)
        if data then
            local response=json.decode(data)
            data=nil
            if vk_check_response_for_errors(response) then
                return nil
            else
                return response.response[1].first_name.." "..response.response[1].last_name
            end
        else
            return nil
        end
    else
        return nil
    end
end

function vk_get_groups()
    if vk_is_signed_in() then
        local url=vk_api_format_url(vk_api_request_get_user_groups, {['extended']='1'})
        local data=http.download(url)
        if data then
            local response=json.decode(data)
            data=nil
            if vk_check_response_for_errors(response) then
                return nil
            elseif response and response.response then
                local groups={}
                for i,value in pairs(response.response) do
                    if type(value) == 'table' then
                        groups[value.gid]=value.name
                    end
                end
                return groups
            else
                return nil
            end
        else
            return nil
        end
    else
        return nil
    end
end

function vk_get_friends()
    if vk_is_signed_in() then
        local url=vk_api_format_url(vk_api_request_get_user_friends, {['fields']='uid,first_name,last_name'})
        local data=http.download(url)
        if data then
            local response=json.decode(data)
            data=nil
            if vk_check_response_for_errors(response) then
                return nil
            elseif response and response.response then
                local friends={}
                for i,value in pairs(response.response) do
                    if type(value) == 'table' then
                        friends[value.uid]=value.first_name..'  '..value.last_name
                    end
                end
                return friends
            else
                return nil
            end
        else
            return nil
        end
    else
        return nil
    end
end

function vk_get_video_url(host, uid, vtag, no_flv, max_hd)
    if (host and not string.match(host, '/$')) then
        host=host..'/'
    end
    if (host and uid and vtag and max_hd) then
        if tonumber(max_hd)>=3 then
            return host..'u'..uid..'/video/'..vtag..'.'..'720.mp4'
        end
        if tonumber(max_hd)>=2 then
            return host..'u'..uid..'/video/'..vtag..'.'..'480.mp4'
        end
        if tonumber(max_hd)>=1 then
            return host..'u'..uid..'/video/'..vtag..'.'..'360.mp4'
        end
        if (tonumber(max_hd)>=0 and tonumber(no_flv)==1) then
            return host..'u'..uid..'/video/'..vtag..'.'..'240.mp4'
        end
        return error_file_video_unsupported_format
    end
    return error_file_video_unsupported_format
end

function get_sort_order(label)
    if label=="length" then
        return 1
    elseif label=="rel" then
        return 2
    else
        return 0
    end
end

function vk_api_request_auth(redirect_url)
    return 'http://oauth.vk.com/authorize?client_id='..vk_app_id..'&scope='..vk_app_scope..'&response_type=token&redirect_uri='..
        util.urlencode(redirect_url..'/ui/vk_landing')
end

plugins['vkontakte']={}
plugins.vkontakte.sendurl=vk_sendurl
plugins.vkontakte.updatefeed=vk_updatefeed
plugins.vkontakte.vk_api_request_auth=vk_api_request_auth
plugins.vkontakte.vk_get_name=vk_get_name
plugins.vkontakte.vk_get_groups=vk_get_groups
plugins.vkontakte.vk_get_friends=vk_get_friends

