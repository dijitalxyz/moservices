-- Copyright (C) 2011 Anton Burdinuk
-- clark15b@gmail.com
-- https://tsdemuxer.googlecode.com/svn/trunk/xupnpd

http_mime={}
http_err={}
http_vars={}

-- http_mime types
http_mime['html']='text/html'
http_mime['htm']='text/html'
http_mime['xml']='text/xml; charset="UTF-8"'
http_mime['txt']='text/plain'
http_mime['cpp']='text/plain'
http_mime['h']='text/plain'
http_mime['lua']='text/plain'
http_mime['jpg']='image/jpeg'
http_mime['png']='image/png'
http_mime['ico']='image/vnd.microsoft.icon'
http_mime['mpeg']='video/mpeg'
http_mime['css']='text/css'
http_mime['json']='application/json'
http_mime['js']='application/javascript'
http_mime['m3u']='audio/x-mpegurl'

-- http http_error list
http_err[100]='Continue'
http_err[101]='Switching Protocols'
http_err[200]='OK'
http_err[201]='Created'
http_err[202]='Accepted'
http_err[203]='Non-Authoritative Information'
http_err[204]='No Content'
http_err[205]='Reset Content'
http_err[206]='Partial Content'
http_err[300]='Multiple Choices'
http_err[301]='Moved Permanently'
http_err[302]='Moved Temporarily'
http_err[303]='See Other'
http_err[304]='Not Modified'
http_err[305]='Use Proxy'
http_err[400]='Bad Request'
http_err[401]='Unauthorized'
http_err[402]='Payment Required'
http_err[403]='Forbidden'
http_err[404]='Not Found'
http_err[405]='Method Not Allowed'
http_err[406]='Not Acceptable'
http_err[407]='Proxy Authentication Required'
http_err[408]='Request Time-Out'
http_err[409]='Conflict'
http_err[410]='Gone'
http_err[411]='Length Required'
http_err[412]='Precondition Failed'
http_err[413]='Request Entity Too Large'
http_err[414]='Request-URL Too Large'
http_err[415]='Unsupported Media Type'
http_err[416]='Requested range not satisfiable'
http_err[500]='Internal Server http_error'
http_err[501]='Not Implemented'
http_err[502]='Bad Gateway'
http_err[503]='Out of Resources'
http_err[504]='Gateway Time-Out'
http_err[505]='HTTP Version not supported'

http_vars['fname']=cfg.name
http_vars['manufacturer']=util.xmlencode('Anton Burdinuk <clark15b@gmail.com>')
http_vars['manufacturer_url']=''
http_vars['description']=ssdp_server
http_vars['name']='xupnpd'
http_vars['version']=cfg.version
http_vars['url']='http://xupnpd.org'
http_vars['uuid']=ssdp_uuid
http_vars['interface']=ssdp.interface()
http_vars['port']=cfg.http_port

http_templ=
{
    '/dev.xml',
    '/wmc.xml',
    '/index.html'
}

dofile('xupnpd_soap.lua')

function http_send_headers(err,ext,len)
    http.send(
        string.format(
            'HTTP/1.1 %i %s\r\nPragma: no-cache\r\nCache-control: no-cache\r\nDate: %s\r\nServer: %s\r\nAccept-Ranges: none\r\n'..
            'Connection: close\r\nContent-Type: %s\r\nEXT:\r\n',
            err,http_err[err] or 'Unknown',os.date('!%a, %d %b %Y %H:%M:%S GMT'),ssdp_server,http_mime[ext] or 'application/x-octet-stream')
    )
    if len then http.send(string.format("Content-Length: %s\r\n",len)) end
    http.send("\r\n")

    if cfg.debug>0 and err~=200 then print('http error '..err) end

end

function get_soap_method(s)
    local i=string.find(s,'#',1)
    if not i then return s end
    return string.sub(s,i+1)
end

function plugin_sendurl_from_cache(url,range)
    local c=cache[url]

    if c==nil or c.value==nil then return false end

    if cfg.debug>0 then print('Cache URL: '..c.value) end

    local rc,location,l

    location=c.value

    for i=1,5,1 do
        rc,l=http.sendurl(location,1,range)

        if l then
            location=l
            core.sendevent('store',url,location)
            if cfg.debug>0 then print('Redirect #'..i..' to: '..location) end
        else
            if rc~=0 then return true end

            if cfg.debug>0 then print('Retry #'..i..' location: '..location) end
        end
    end

    return false
end

function plugin_sendurl(url,real_url,range)
    local rc,location,l

    location=real_url

    core.sendevent('store',url,real_url)

    for i=1,5,1 do
        rc,l=http.sendurl(location,1,range)

        if l then
            location=l
            core.sendevent('store',url,location)
            if cfg.debug>0 then print('Redirect #'..i..' to: '..location) end
        else
            if rc~=0 then return true end

            if cfg.debug>0 then print('Retry #'..i..' location: '..location) end
        end
    end

    return false
end

function plugin_sendfile(path)
    local len=util.getflen(path)
    if len then
        http.send(string.format('Content-Length: %s\r\n\r\n',len))
        http.sendfile(path)
    else
        http.send('\r\n')
    end
end

function plugin_download(url)
    local data,location

    location=url

    for i=1,5,1 do
        data,location=http.download(location)

        if not location then
            return data
        else
            if cfg.debug>0 then print('Redirect #'..i..' to: '..location) end
        end
    end

    return nil
end

function plugin_get_length(url)
    local len,location

    location=url

    for i=1,5,1 do
        len,location=http.get_length(location)

        if not location then
            return len
        else
            if cfg.debug>0 then print('Redirect #'..i..' to: '..location) end
        end
    end

    return 0
end

function http_handler(what,from,port,msg)

    if not msg or not msg.reqline then return end

    local ui_main=cfg.ui_path..'xupnpd_ui.lua'

    if msg.reqline[2]=='/' then
        if util.getflen(ui_main) then
            msg.reqline[2]='/ui'
        else
            msg.reqline[2]='/index.html'
        end
    end

    local head=false

    local f=util.geturlinfo(cfg.www_root,msg.reqline[2])

    if not f or (msg.reqline[3]~='HTTP/1.0' and msg.reqline[3]~='HTTP/1.1') then
        http_send_headers(400)
        return
    end

    if cfg.debug>0 then print(from..' '..msg.reqline[1]..' '..msg.reqline[2]..' \"'..(msg['user-agent'] or '')..'\"') end

    local from_ip=string.match(from,'(.+):.+')

    if string.find(f.url,'^/ui/?') then
        if util.getflen(ui_main) then
            dofile(ui_main)
            ui_handler(f.args,msg.data or '',from_ip,f.url)
        else
            http_send_headers(404)
        end
        return
    end

    if msg.reqline[1]=='HEAD' then head=true msg.reqline[1]='GET' end

    if msg.reqline[1]=='POST' then
        if f.url=='/soap' then

            if cfg.debug>0 then print(from..' SOAP '..(msg.soapaction or '')) end

            http_send_headers(200,'xml')

            local err=true

            local s=services[ f.args['s'] ]

            if s then
                local func_name=get_soap_method(msg.soapaction or '')
                local func=s[func_name]

                if func then

                    if cfg.debug>1 then print(msg.data) end

                    local r=soap.find('Envelope/Body/'..func_name,soap.parse(msg.data))

                    r=func(r or {},from_ip)

                    if r then
                        local resp=
                            string.format(
                                '<?xml version=\"1.0\" encoding=\"utf-8\"?>'..
                                '<s:Envelope xmlns:s=\"http://schemas.xmlsoap.org/soap/envelope/\" s:encodingStyle=\"http://schemas.xmlsoap.org/soap/encoding/\">'..
                                '<s:Body><u:%sResponse xmlns:u=\"%s\">%s</u:%sResponse></s:Body></s:Envelope>',                                                            
                                    func_name,s.schema,soap.serialize_vector(r),func_name)

                        http.send(resp)

                        if cfg.debug>2 then print(resp) end

                        err=false
                    end
                end
            end

            if err==true then
                http.send(
                '<?xml version=\"1.0\" encoding=\"utf-8\"?>'..
                '<s:Envelope xmlns:s=\"http://schemas.xmlsoap.org/soap/envelope/\" s:encodingStyle=\"http://schemas.xmlsoap.org/soap/encoding/\">'..
                   '<s:Body>'..
                      '<s:Fault>'..
                         '<faultcode>s:Client</faultcode>'..
                         '<faultstring>UPnPError</faultstring>'..
                         '<detail>'..
                            '<u:UPnPError xmlns:u=\"urn:schemas-upnp-org:control-1-0\">'..
                               '<u:errorCode>501</u:errorCode>'..
                               '<u:errorDescription>Action Failed</u:errorDescription>'..
                            '</u:UPnPError>'..
                         '</detail>'..
                      '</s:Fault>'..
                   '</s:Body>'..
                '</s:Envelope>'
                )

                if cfg.debug>0 then print('upnp error 501') end

            end

        else
            http_send_headers(404)
        end
    elseif msg.reqline[1]=='SUBSCRIBE' then
        local ttl=1800
        local sid=core.uuid()

        if f.args.s then
            core.sendevent('subscribe',f.args.s,sid,string.match(msg.callback,'<(.+)>'),ttl)
        end

        http.send(
            string.format(
                'HTTP/1.1 200 OK\r\nPragma: no-cache\r\nCache-control: no-cache\r\nDate: %s\r\nServer: %s\r\nAccept-Ranges: none\r\n'..
                'Connection: close\r\nEXT:\r\nSID: uuid:%s\r\nTIMEOUT: Second-%d\r\n\r\n',
                os.date('!%a, %d %b %Y %H:%M:%S GMT'),ssdp_server,sid,ttl))

    elseif msg.reqline[1]=='UNSUBSCRIBE' then

        core.sendevent('unsubscribe',string.match(msg.sid or '','uuid:(.+)'))

        http.send(
            string.format(
                'HTTP/1.1 200 OK\r\nPragma: no-cache\r\nCache-control: no-cache\r\nDate: %s\r\nServer: %s\r\nAccept-Ranges: none\r\n'..
                'Connection: close\r\nEXT:\r\n\r\n',
                os.date('!%a, %d %b %Y %H:%M:%S GMT'),ssdp_server))

    elseif msg.reqline[1]=='GET' then

        if cfg.xbox360==true then
            local t=split_string(f.url,'/')

            if table.maxn(t)==2 then
                if not f.args then f.args={} end
                f.args['s']=util.urldecode(t[2])

                if t[1]=='p' then f.url='/proxy' elseif t[1]=='s' then f.url='/stream' end
            end
        end

        if f.url=='/proxy' then

            local pls=find_playlist_object(f.args['s'] or '')

            if not pls then http_send_headers(404) return end

            http.send(string.format(
                'HTTP/1.1 200 OK\r\nPragma: no-cache\r\nCache-control: no-cache\r\nDate: %s\r\nServer: %s\r\n'..
                'Connection: close\r\nContent-Type: %s\r\nEXT:\r\nTransferMode.DLNA.ORG: Streaming\r\n',
                os.date('!%a, %d %b %Y %H:%M:%S GMT'),ssdp_server,pls.mime[3]))

            http.send('ContentFeatures.DLNA.ORG: '..pls.dlna_extras..'\r\n')

            if cfg.wdtv==true then
                http.send('Content-Size: 65535\r\n')
                http.send('Content-Length: 65535\r\n')
            end

            if head==true then
                http.send('\r\n')
                http.flush()
            else
                if pls.event then core.sendevent(pls.event,pls.url) end

                if cfg.debug>0 then print(from..' PROXY '..pls.url..' <'..pls.mime[3]..'>') end

                core.sendevent('status',util.getpid(),from_ip..' '..pls.name)

                if pls.plugin then
                    http.send('Accept-Ranges: bytes\r\n')
                    http.flush()        -- PS3 YouTube network error fix?
                    plugins[pls.plugin].sendurl(pls.url,msg.range)
                else
                    http.send('Accept-Ranges: none\r\n\r\n')
                    if string.find(pls.url,'^udp://@') then
                        http.sendmcasturl(string.sub(pls.url,8),cfg.mcast_interface,2048)
                    else
                        http.sendurl(pls.url)
                    end
                end
            end

        elseif f.url=='/stream' then

            local pls=find_playlist_object(f.args['s'] or '')

            if not pls or not pls.path then http_send_headers(404) return end

            local flen=pls.length

            local ffrom=0
            local flen_total=flen

            if msg.range and flen and flen>0 then
                local f,t=string.match(msg.range,'bytes=(.*)-(.*)')

                f=tonumber(f)
                t=tonumber(t)

                if not f then f=0 end
                if not t then t=flen-1 end

                if f>t or t+1>flen then http_send_headers(416) return end

                ffrom=f
                flen=t-f+1
            end

            http.send(string.format(
                'HTTP/1.1 200 OK\r\nPragma: no-cache\r\nCache-control: no-cache\r\nDate: %s\r\nServer: %s\r\n'..
                'Connection: close\r\nContent-Type: %s\r\nEXT:\r\nTransferMode.DLNA.ORG: Streaming\r\n',
                os.date('!%a, %d %b %Y %H:%M:%S GMT'),ssdp_server,pls.mime[3]))

            if flen then
                http.send(string.format('Accept-Ranges: bytes\r\nContent-Length: %s\r\n',flen))
            else
                http.send('Accept-Ranges: none\r\n')
            end

            local dlna_extras=pls.dlna_extras
            if dlna_extras~='*' then
                dlna_extras=string.gsub(dlna_extras,'DLNA.ORG_OP=%d%d','DLNA.ORG_OP=11')
            end

            http.send('ContentFeatures.DLNA.ORG: '..dlna_extras..'\r\n')

            if msg.range and flen and flen>0 then
                http.send(string.format('Content-Range: bytes %s-%s/%s\r\n',ffrom,ffrom+flen-1,flen_total))
            end

            http.send('\r\n')
            http.flush()

            if head~=true then
                if pls.event then core.sendevent(pls.event,pls.path) end

                if cfg.debug>0 then print(from..' STREAM '..pls.path..' <'..pls.mime[3]..'>') end

                core.sendevent('status',util.getpid(),from_ip..' '..pls.name)

                http.sendfile(pls.path,ffrom,flen)
            end

        elseif f.url=='/reload' then
            http_send_headers(200,'txt')

            if head~=true then
                http.send('OK')
                core.sendevent('reload')
            end
        else
            if f.type=='none' then http_send_headers(404) return end
            if f.type~='file' then http_send_headers(403) return end

            local tmpl=false

            for i,fname in ipairs(http_templ) do
                if f.url==fname then tmpl=true break end
            end

            local len=nil

            if not tmpl then len=f.length end

            http.send(
                string.format(
                    'HTTP/1.1 200 OK\r\nDate: %s\r\nServer: %s\r\nAccept-Ranges: none\r\nConnection: close\r\nContent-Type: %s\r\nEXT:\r\n',
                    os.date('!%a, %d %b %Y %H:%M:%S GMT'),ssdp_server,http_mime[f.ext] or 'application/x-octet-stream'))

            if len then
                http.send(string.format('Content-Length: %s\r\n',len))
            end

            if tmpl then
                http.send('Pragma: no-cache\r\nCache-control: no-cache\r\n')
            end

            http.send('\r\n')

            if head~=true then
                if cfg.debug>0 then print(from..' FILE '..f.path) end
                if tmpl then http.sendtfile(f.path,http_vars) else http.sendfile(f.path) end
            end
        end
    else
        http_send_headers(405)
    end

    http.flush()
end

events["http"]=http_handler

http.listen(cfg.http_port,"http")
