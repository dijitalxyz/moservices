cfg={}

-- multicast interface for SSDP exchange, 'eth0', 'br0', 'br-lan' for example
cfg.ssdp_interface='br0'

-- 'cfg.ssdp_loop' enables multicast loop (if player and server in one host)
cfg.ssdp_loop=1

-- SSDP announcement interval
cfg.ssdp_notify_interval=15

-- SSDP announcement age
cfg.ssdp_max_age=1800

-- HTTP port for incoming connections
cfg.http_port=4044

-- syslog facility (syslog,local0-local7)
cfg.log_facility='syslog'

-- 'cfg.daemon' detach server from terminal
cfg.daemon=true

-- silent mode - no logs, no pid file
cfg.embedded=true

-- 'cfg.debug' enables SSDP debug output to stdout (if cfg.daemon=false)
-- 0-off, 1-basic, 2-messages
cfg.debug=1

-- external 'udpxy' url for multicast playlists (udp://@...)
--cfg.udpxy_url='http://127.0.0.1:8080'

-- downstream interface for builtin multicast proxy (comment 'cfg.udpxy_url' for processing 'udp://@...' playlists)
cfg.mcast_interface='br0'

-- 'cfg.proxy' enables proxy for injection DLNA headers to stream
-- 0-off, 1-radio, 2-radio/TV
cfg.proxy=2

-- User-Agent for proxy
cfg.user_agent='Mozilla/5.0'

-- I/O timeout
cfg.http_timeout=15

-- 'cfg.dlna_extras' enables DLNA extras
cfg.dlna_extras=true

-- XBox360 compatible mode
cfg.xbox360=false

-- WDTV Live compatible mode
cfg.wdtv=false

-- enables UPnP/DLNA notify when reload playlist
cfg.dlna_notify=true

-- group by 'group-title'
cfg.group=true

-- sort files
cfg.sort_files=false

-- Device name
cfg.name='UPnP-IPTV'

-- static device UUID, '60bd2fb3-dabe-cb14-c766-0e319b54c29a' for example or nil
cfg.uuid='60bd2fb3-dabe-cb14-c766-0e319b54c29a'

-- max url cache size
cfg.cache_size=8

-- url cache item ttl (sec)
cfg.cache_ttl=900

-- default mime type (mpeg, mpeg1, mpeg2, ts)
cfg.default_mime_type='mpeg'

-- feeds update interval (seconds, 0 - disabled)
cfg.feeds_update_interval=0
cfg.playlists_update_interval=0

-- fetch file length when feed update (slow!!!)
cfg.feeds_fetch_length=false

-- playlist (m3u file path or path with alias
playlist=
{
    { './playlists/mozhay.m3u',             'Mozhay.tv' },
--    { './localmedia', 'Local Media Files', '127.0.0.1;192.168.1.1' }
}

-- feeds list (plugin, feed name, feed type)
feeds=
{
    { 'vimeo',          'channel/hd',           'Vimeo HD Channel' },
    { 'vimeo',          'channel/hdxs',         'HD Xtreme sports' },
    { 'vimeo',          'channel/mtb',          'Mountain Bike Channel' },
    { 'youtube',        'channel/top_rated',    'YouTube Top Rated' },
--    { 'gametrailers',   'ps3/review',           'GT - PS3 - Review' },
--    { 'gametrailers',   'ps3/preview',          'GT - PS3 - Preview' },
--    { 'gametrailers',   'ps3/gameplay',         'GT - PS3 - Gameplay' },
--    { 'giantbomb',      'all',                  'GiantBomb - All' },
--    { 'ag',             'videos',               'AG - New' },
--    { 'ivi',            'new',                  'IVI - New' },
}

-- log ident, pid file end www root
cfg.version='1.0-rc12'
cfg.log_ident=arg[1] or 'xupnpd'
cfg.pid_file='/var/run/'..cfg.log_ident..'.pid'
cfg.www_root='./www/'
cfg.tmp_path='/tmp/'
cfg.plugin_path='./plugins/'
cfg.config_path='./config/'
cfg.playlists_path='./playlists/'
--cfg.feeds_path='/tmp/xupnpd-feeds/'
cfg.ui_path='./ui/'

dofile('xupnpd_main.lua')
