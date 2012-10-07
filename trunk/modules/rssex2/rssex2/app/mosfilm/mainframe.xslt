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

<xsl:template match="/">
	<channel>
		<title>Мосфильм онлайн</title>
  <xsl:apply-templates select="//div[@class='cover']"/>
	</channel>
</xsl:template>

<xsl:template match="div">
  <item>
    <title><xsl:value-of select="string(.//span[@class='name']/a)"/></title>
    <genre><xsl:value-of select="string(.//span[@class='genres']/a)"/></genre>
    <image>http://cinema.mosfilm.ru/<xsl:value-of select="img[1]/@src"/></image>
    <location>http://cinema.mosfilm.ru/<xsl:value-of select=".//span[@class='name']/a/@href"/></location>
    <stream_class>video</stream_class>
  </item>
</xsl:template>

<xsl:template match="@*" />

</xsl:stylesheet>