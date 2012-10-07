<?xml version="1.0"?>
<xsl:stylesheet version="1.0"
  xmlns:xsl="http://www.w3.org/1999/XSL/Transform" 
  xmlns:str="http://exslt.org/strings"
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

<xsl:output method="xml" encoding="utf-8" indent="yes"/>

<xsl:param name="title" select="'/top_video/@lnk'"/>


<xsl:template match="/">
	<channel>
		<title><xsl:value-of select="$title"/></title>
  <xsl:apply-templates select="//video"/>
	</channel>
</xsl:template>

<xsl:template match="video">
  <item>
    <xsl:apply-templates select="@*"/>
  </item>
</xsl:template>

<xsl:template match="@name">
  <title>
    <xsl:value-of select="."/>
  </title>
</xsl:template>

<xsl:template match="@img">
  <image>
    <xsl:value-of select="."/>
  </image>
  <cover>
    <xsl:value-of select="str:replace(string(.),'list','bg_img')"/>
  </cover>
</xsl:template>

<xsl:template match="@www">
  <location>
    <xsl:value-of select="concat('http://www.tvigle.ru',str:replace(string(.), 'http://www.tvigle.ru', ''))"/>
  </location>
  <stream_class>video</stream_class>
</xsl:template>

<xsl:template match="@*" />

</xsl:stylesheet>