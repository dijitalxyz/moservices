<?xml version="1.0"?>
<xsl:stylesheet version="1.0"
  xmlns:xsl="http://www.w3.org/1999/XSL/Transform" 
  xmlns:xspf="http://xspf.org/ns/0/"
  exclude-result-prefixes="xspf"
>

<!--
#
#   http://code.google.com/media-translate/
#   Copyright (C) 2010  Serge A. Timchenko
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

<xsl:output method="text" encoding="utf-8"/>

<xsl:template match="/">
  <xsl:apply-templates select="xspf:playlist"/>
</xsl:template>

<xsl:template match="xspf:playlist">#EXTM3U<xsl:apply-templates select="xspf:trackList/xspf:track"/>
</xsl:template>

<xsl:template match="xspf:track">
<xsl:variable name="url">
  <xsl:choose>
    <xsl:when test="boolean(string(xspf:meta[@rel='stream_url']))"><xsl:value-of select="xspf:meta[@rel='stream_url']"/></xsl:when>
    <xsl:otherwise><xsl:value-of select="xspf:location" /></xsl:otherwise>
  </xsl:choose>
</xsl:variable>
  <xsl:if test="boolean(string($url))">
#EXTINF:-1,<xsl:value-of select="xspf:title"/><xsl:text>
</xsl:text>
    <xsl:value-of select="$url" />
</xsl:if>
</xsl:template>

<xsl:template match="*" />

</xsl:stylesheet>