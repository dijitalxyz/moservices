<?xml version="1.0"?>
<xsl:stylesheet version="1.0"
  xmlns:xsl="http://www.w3.org/1999/XSL/Transform" 
  xmlns:str="http://exslt.org/strings"
  xmlns:xspf="http://xspf.org/ns/0/"
  exclude-result-prefixes="xspf"
>

<!--
#
#   http://code.google.com/media-translate/
#   Copyright (C) 2011  Serge A. Timchenko
#
#   This program is free software: you can redistribute it and/or modify
#   it under the terms of the GNU General Public License as published by
#   the Free Software Foundation, either version 3 of the License, or
#   (at your option) any later version.
#
#   This program is distributed in the hope that it will be useful,
#   but WITHOUT ANY WARRANTY; without even the implied warranty of
#   MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.  See the
#   GNU General Public License for more details.
#
#   You should have received a copy of the GNU General Public License
#   along with this program. If not, see <http://www.gnu.org/licenses/>.
#
-->

<xsl:output method="xml" encoding="utf-8" indent="yes"/>

<xsl:param name="title" select="'get-tune.net'" />
<xsl:param name="translate" select="'http://127.0.0.1/cgi-bin/translate?'" />

<xsl:template match="/">
  <playlist version="1" xmlns="http://xspf.org/ns/0/">
    <title><xsl:value-of select="$title"/></title>
    <image>http://get-tune.net/i/minilogo.png</image>
    <trackList>
      <xsl:apply-templates select="//div[@id='xbody']//a[contains(@href,'/style/') or contains(@href,'/?a=nav&amp;')]"/>
      <xsl:apply-templates select="//div[@id='xbody']//*[@class='new_track-tr']"/>
	  </trackList>
	</playlist>
</xsl:template>

<xsl:template match="a">
<track xmlns="http://xspf.org/ns/0/">
  <title>[Page] <xsl:value-of select="."/></title>
  <location>http://get-tune.net<xsl:value-of select="@href"/></location>
  <xsl:choose>
    <xsl:when test="contains(@href,'/style/')">
      <meta rel="stream_url"><xsl:value-of select="$translate"/>app/gettune/genre,<xsl:value-of select="substring-after(@href, '/style/')"/></meta>
    </xsl:when>
    <xsl:otherwise>
      <meta rel="stream_url"><xsl:value-of select="$translate"/>app/gettune/nav,letter:<xsl:value-of select="substring-before(substring-after(@href, 'letter='),'&amp;')"/>;opt:<xsl:value-of select="substring-after(substring-after(@href, 'letter='),'&amp;')"/></meta>
    </xsl:otherwise>
  </xsl:choose>
  <meta rel="stream_class">playlist</meta>
  <meta rel="stream_type">application/xspf+xml</meta>
  <meta rel="stream_protocol">http</meta>
</track>
</xsl:template>

<xsl:template match="*[@class='new_track-tr']">
<track xmlns="http://xspf.org/ns/0/">
  <title><xsl:value-of select="a"/></title>
  <location>http://get-tune.net<xsl:value-of select="a/@href"/></location>
  <image><xsl:value-of select="$translate"/>app,Title:<xsl:value-of select="a"/>,lastfm/trackimage.png</image>
  <meta rel="stream_url"><xsl:value-of select="$translate"/>app/gettune/search,q:<xsl:value-of select="substring-after(a/@href, '&amp;q=')"/></meta>
  <meta rel="stream_class">playlist</meta>
  <meta rel="stream_type">application/xspf+xml</meta>
  <meta rel="stream_protocol">http</meta>
</track>
</xsl:template>

<xsl:template match="node() | @*" />

</xsl:stylesheet>

