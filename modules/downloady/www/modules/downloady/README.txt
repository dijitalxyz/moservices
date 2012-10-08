Downloady - PHP Download Manger - v1.4
By the paw of CyberLeo, 20050921
AJAX support added by Wesha, 20060101
1.01 - http://www.cyberleo.net/cyberleo/Projects/Downloady/
1.1 - http://sourceforge.net/projects/downloady/
cyberleo@cyberleo.net
1.3 - Support for Firefox 3 (damn you guys, screwed up perfectly good RPC!)
1.4 - Support for wget 1.12 (partially thanks to billybuerger)
1.5 - Support for wget 1.12 (fixed filename regexps)

This is a simple PHP download manager. It utilizes a few external packages to
 run, so make sure to read this documentation entirely.

Requirements:
 - A webserver
 - PHP 4.3.11 or greater
 - A POSIX-compliant operating system. (May work on Windows, but I'm not keen
    on punishing myself like that)
 - GNU Wget 1.9 or compatible

Why?
 Short answer: Someone wanted it. Their requirements were a script to be able
  to take a URL and download the file on their server, for downloading
  interesting files when accessing from locations that may not allow arbitrary
  downloads. It was an interesting challenge, but one that was none too
  challenging. Read the To Do section for more challenging options for this
  software's future.
 By the way, this software works perfectly with Firefox's DownloadWith
  browser extension.

Installation instructions:
 Simply untar the files into a directory within your webroot, then open
  downloady.php in your favorite plaintext editor. Modify the variables at the
  top of the class to fit your particular installation, and make sure that the
  directory listed as the destination exists and is writable by your webserver
  user!

Usage:
 The interface is (hopefully) self-explanatory. The only thing that isn't given
  is a way to easily get URLs into the downloader. This is due to the fact that
  this script is supposed to be used to interface with browser extensions such
  as DownloadWith for Firefox. To add URLs for download, pass them via the
  'url' HTTP GET variable, as follows:
  http://mydomain.com/path/to/dstatus.php?url=http://domain.com/file/i/want.zip
  The file download will begin immediately. Visit dstatus.php to view a list of
  current downloads, with the option to view properties, stop, resume, clear
  and (in the case of fully retrieved files) download.
 A big note here is that 'clear' doesn't actually delete the files, rather just
  makes downloady forget about them by deleting status information. The files
  must be deleted manually via shell or FTP. This is by design, to prevent an
  accidental 'delete' click from wiping out hours of download. I may add a
  toggle for 'dangerous delete' in the future.

Change Log:
 v1.1 - AJAX support added by Wesha.
 v1.01 - Modified documentation slightly to include other points I had
  forgotten, and added a quick page to demonstrate how to add URLs to the
  download manager.
 v1.00 - Initial release.

To Do:
 There are quite a few annoyances in the software. These don't affect usability
  but I find them annoying nonetheless. I should fix these some time.
 - Resuming a download stands a good chance of generating a new random ID,
    making the item appear twice in the list--once for the paused download and
    again for the resumed download.
 - Handling websites that don't return Content-Length headers (some PHP scripts)
    is a major kludge. Progress will show 0%, but fetched length will increase
    as appropriate.
 - No transfer estimation. This is due to there being no easily parseable time
    remaining indicator from Wget, and I'm at the mercy of what that software
    gives me. See below
 - Relies on external software. A possibility for the future is to add a PHP-
    based file transfer module that can be executed, which will return the
    desired information in a readily parsable format, including things like
    transfer time estimation.
 - Adding a previously added file is allowed, and may corrupt the download file
    if the previous download process is still active. See #1 for why this is.
 - Adding a previously completed and cleared file is allowed, but shows as
    'incomplete'. This is due to wget's noticing the file's complete existence
    and refusing to download it any further. View the properties to see wget's
    response.
 - Code cleanup. The code is kinda messy, in my opinion, but he wanted
    something to use right away. So he's got it.

Thanks:
 - Wesha the Leopard, for many caffeine donations, among other things.
 - The folks at php.net for their versatile scripting language.
 - The creators of TorrentFlux, a great PHP-based BitTorrent manager (of which
    most of the interface is based.)

License:
 This software is distributed under the 'Don't Be Stupid' license. This license
  specifically grants the ability to use, abuse, disassemble, reassemble,
  eat, chew, digest, vomit or otherwise utilize this code in any way, shape,
  or fashion, as long as credit for the original work is given, and the creator
  is notified of any modifications to said script that would be of benefit to
  the community. The license also excludes the creator from any liability
  for any result, good or bad, that occurs either directly or indirectly
  through the use (or abuse, ingestion and subsequent reverse paristalsis,
  etc...) of this code.
 In other words, Don't Be Stupid.
