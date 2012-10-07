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
		<title>Каталог</title>
		<xsl:apply-templates select="//ul[@id='menu']"/>
	</channel>
</xsl:template>

<xsl:template match="ul">
	<item>
	  <title>Проекты</title>
	  <indent>0</indent>
	</item>
	<item>
	  <title>Бэби бум</title>
	  <image>http://uravo.tv/media/img/content_projects/baby.jpg</image>
	  <path>/family/baby_boom</path>
	  <indent>20</indent>
	</item>
	<item>
	  <title>Немое кино</title>
	  <image>http://uravo.tv/media/img/content_projects/no_sound.jpg</image>
	  <path>/family/silent_movies/</path>
	  <indent>20</indent>
	</item>
	<item>
	  <title>Наша география</title>
	  <image>http://uravo.tv/media/img/content_projects/geographic.jpg</image>
	  <path>/family/ng/</path>
	  <indent>20</indent>
	</item>
	<item>
	  <title>Советское кино</title>
	  <image>http://uravo.tv/media/img/content_projects/russian_cinema.jpg</image>
	  <path>/country/ussr/</path>
	  <indent>20</indent>
	</item>
	<item>
	  <title>Домашнее задание</title>
	  <image>http://uravo.tv/media/img/content_projects/school.jpg</image>
	  <path>/family/homework/</path>
	  <indent>20</indent>
	</item>
	<item>
	  <title>Фильмы</title>
	  <image>http://uravo.tv<xsl:value-of select="li[1]/a/img/@src"/></image>
	  <indent>0</indent>
	</item>
  <xsl:apply-templates select="li[1]/ul/li"/>
	<item>
	  <title>Мультфильмы</title>
	  <image>http://uravo.tv<xsl:value-of select="li[2]/a/img/@src"/></image>
	  <path><xsl:value-of select="li[2]/a/@href"/></path>
	  <indent>0</indent>
	</item>
	<item>
	  <title>Новое</title>
	  <image>http://uravo.tv<xsl:value-of select="li[3]/a/img/@src"/></image>
	  <path><xsl:value-of select="li[3]/a/@href"/></path>
	  <indent>0</indent>
	</item>
	<item>
	  <title>Юмор</title>
	  <image>http://uravo.tv<xsl:value-of select="li[4]/a/img/@src"/></image>
	  <path><xsl:value-of select="li[4]/a/@href"/></path>
	  <indent>0</indent>
	</item>
	<item>
	  <title>Передачи</title>
	  <image>http://uravo.tv<xsl:value-of select="li[5]/a/img/@src"/></image>
	  <indent>0</indent>
	</item>
  <xsl:apply-templates select="li[5]/ul/li"/>
</xsl:template>

<xsl:template match="li">
	<item>
	  <title><xsl:value-of select="string(a)"/></title>
	  <path><xsl:value-of select="a/@href"/></path>
	  <indent>20</indent>
	</item>
</xsl:template>

<xsl:template match="@*" />

</xsl:stylesheet>