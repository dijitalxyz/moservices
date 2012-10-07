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
      <xsl:apply-templates select="//ul[@class='listalka']//li/a"/>
      <xsl:apply-templates select="//div[@id='playlist']//div[@class='track']"/>
	  </trackList>
	</playlist>
</xsl:template>

<xsl:template match="a">
  <xsl:variable name="q">
    <xsl:variable name="s" select="substring-after(@href, '&amp;q=')" />
    <xsl:choose>
      <xsl:when test="contains($s, '&amp;')">
        <xsl:value-of select="substring-before($s, '&amp;')"/>
      </xsl:when>
      <xsl:otherwise>
        <xsl:value-of select="$s"/>
      </xsl:otherwise>
    </xsl:choose>
  </xsl:variable>
  <xsl:variable name="url"><xsl:value-of select="$translate"/>app/gettune/search,q:<xsl:value-of select="$q"/>
    <xsl:if test="contains(@href,'&amp;p=')">;opt:a=music&amp;p=<xsl:value-of select="substring-after(@href, '&amp;p=')"/></xsl:if>
  </xsl:variable>
<track xmlns="http://xspf.org/ns/0/">
  <title>Page <xsl:value-of select="."/></title>
  <location><xsl:value-of select="$url"/></location>
  <meta rel="stream_url"><xsl:value-of select="$url"/></meta>
  <meta rel="stream_class">playlist</meta>
  <meta rel="stream_type">application/xspf+xml</meta>
  <meta rel="stream_protocol">http</meta>
</track>
</xsl:template>

<xsl:template match="div[@class='track']">
<track xmlns="http://xspf.org/ns/0/">
  <title><xsl:value-of select="*[@class='title']"/></title>
  <location><xsl:value-of select="a[contains(@class,'download')]/@href"/></location>
  <image><xsl:value-of select="$translate"/>app,Title:<xsl:value-of select="str:encode-uri(translate(*[@class='title'],'—','-'),true())"/>,lastfm/trackimage.png</image>
  <meta rel="stream_url"><xsl:value-of select="a[contains(@class,'download')]/@href"/></meta>
  <meta rel="stream_class">audio</meta>
  <meta rel="stream_type">audio/mpeg</meta>
  <meta rel="stream_protocol">http</meta>
</track>
</xsl:template>

<xsl:template match="node() | @*" />

</xsl:stylesheet>

