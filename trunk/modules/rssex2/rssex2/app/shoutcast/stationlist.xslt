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

<xsl:output method="xml" encoding="utf-8" indent="yes"/>

<xsl:param name="title" />

<xsl:template match="/">
  <xsl:apply-templates select="stationlist"/>
</xsl:template>

<xsl:template match="stationlist">
	<channel>
		<title><xsl:value-of select="$title"/></title>
    <xsl:apply-templates select="station"/>
	</channel>
</xsl:template>

<xsl:template match="station">
  <item>
    <xsl:apply-templates select="@*"/>
  </item>
</xsl:template>

<xsl:template match="@*">
  <xsl:element name="{name()}">
    <xsl:value-of select="."/>
  </xsl:element>
</xsl:template>

<xsl:template match="*" />

</xsl:stylesheet>