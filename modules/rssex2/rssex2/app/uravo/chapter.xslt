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

<xsl:template match="/">
	<channel>
		<title><xsl:value-of select="//h2" /></title>
  <xsl:apply-templates select="//td[a/@title='Перейти к просмотру']"/>
	</channel>
</xsl:template>

<xsl:template match="td">
  <item>
    <title><xsl:value-of select="string(div[1]/a)"/></title>
    <image><xsl:value-of select="substring-before(substring-after(a/div/@style,'background-image:url('),')')"/></image>
    <location>http://uravo.tv<xsl:value-of select="a/@href"/></location>
    <stream_class>video</stream_class>
    <year><xsl:value-of select="string(div[3]/a[2])"/></year>
    <country><xsl:value-of select="string(div[3]/a[1])"/></country>
    <annotation><xsl:value-of select="normalize-space(div[2]/p)"/></annotation>
    <time><xsl:value-of select="normalize-space(div[4])"/></time>
  </item>
</xsl:template>

<xsl:template match="@*" />

</xsl:stylesheet>

