<?xml version="1.0"?>
<xsl:stylesheet version="1.0"
  xmlns:xsl="http://www.w3.org/1999/XSL/Transform" 
  xmlns:str="http://exslt.org/strings"
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

<xsl:param name="letter" select="''"/>

<xsl:template match="/">
	<channel>
		<title>
		  <xsl:choose>
		    <xsl:when test="$letter">Фильмы на букву - <xsl:value-of select="$letter"/></xsl:when>
		    <xsl:otherwise>Случайный выбор</xsl:otherwise>
		  </xsl:choose>
		</title>
  <xsl:apply-templates select="//div[@class='movie']"/>
	</channel>
</xsl:template>

<xsl:template match="div">
  <item>
    <title><xsl:value-of select="string(.//span[@class='name']/a)"/></title>
    <image>http://cinema.mosfilm.ru/<xsl:value-of select=".//div[@class='cover']/img[1]/@src"/></image>
    <location>http://cinema.mosfilm.ru/<xsl:value-of select=".//span[@class='name']/a/@href"/></location>
    <stream_class>video</stream_class>
    <year><xsl:value-of select="normalize-space(.//span[@class='year'])"/></year>
    <annotation><xsl:value-of select="normalize-space(.//a[@class='description'])"/></annotation>
    <viewes><xsl:value-of select="normalize-space(.//b[@class='viewes'])"/></viewes>
  </item>
</xsl:template>

<xsl:template match="@*" />

</xsl:stylesheet>