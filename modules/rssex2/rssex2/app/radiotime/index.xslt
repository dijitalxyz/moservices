<?xml version="1.0"?>
<xsl:stylesheet version="1.0"
  xmlns:xsl="http://www.w3.org/1999/XSL/Transform" 
  xmlns:xspf="http://xspf.org/ns/0/"
  xmlns:str="http://exslt.org/strings"
  extension-element-prefixes="str"
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

<xsl:template match="/">
  <channel>
    <xsl:apply-templates select="//outline[@key='content']" mode="channel"/>
    <xsl:apply-templates select="//outline[@key='navigation']" mode="menu"/>
  </channel>
</xsl:template>

<xsl:template match="outline[@type='container']" mode="channel">
  <title>Radiotime - <xsl:value-of select="@text"/></title>
  <xsl:apply-templates select="outline[@type='audio']" mode="channel"/>
</xsl:template>

<xsl:template match="outline[@type='audio']" mode="channel">
  <item>
    <xsl:apply-templates select="@*" mode="channel"/>
  </item>
</xsl:template>

<xsl:template match="@*" mode="channel">
  <xsl:element name="{name()}">
    <xsl:value-of select="." />
  </xsl:element>
</xsl:template>

<xsl:template match="outline[@type='container']" mode="menu">
  <menuSize>
    <script>
      menuArray = null;
      menuLinkArray = null;
      <xsl:apply-templates select="outline[@type='link']" mode="menu"/>
      menuArray = pushBackStringArray(menuArray, "Поиск");
      menuLinkArray = pushBackStringArray(menuLinkArray, "=search=");
      menuSize = <xsl:value-of select="count(outline[@type='link'])+1"/>;
    </script>
  </menuSize>
</xsl:template>

<xsl:template match="outline[@type='link']" mode="menu">
  menuArray = pushBackStringArray(menuArray, "<xsl:value-of select="@text"/>");
  menuLinkArray = pushBackStringArray(menuLinkArray, "<xsl:value-of select="@URL"/>");
</xsl:template>

<xsl:template match="*" />

</xsl:stylesheet>