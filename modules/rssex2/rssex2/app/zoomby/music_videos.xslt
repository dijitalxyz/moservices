<?xml version="1.0"?>
<xsl:stylesheet version="1.0"
  xmlns:xsl="http://www.w3.org/1999/XSL/Transform"
  xmlns:str="http://exslt.org/str"
  exclude-result-prefixes="str"
>

<xsl:output method="xml" encoding="utf-8" indent="yes"/>

<xsl:template match="/">
    <xsl:apply-templates select="//div[contains(@class, 'elements_col')]"/>
</xsl:template>

<xsl:template match="div">
  <xsl:variable name="img" select="div[1]/a[1]/img/@style" />
  <xsl:variable name="artist" select="div[3]/a[1]" />
  <xsl:variable name="title" select="div[4]/a[1]" />
  <xsl:variable name="link" select="concat('http://zoomby.ru',div[3]/a[1]/@href)" />

	 <item>
	  <title><xsl:value-of select="$artist" /><xsl:if test="$title">-<xsl:value-of select="$title" /></xsl:if></title>
	  <link><xsl:value-of select="$link" /></link>
	  <description></description>
	  <image url="{substring-before(substring-after($img,'url('), ');')}" width="" height=""/>
	 </item>

</xsl:template>

<xsl:template match="* | @* | node()"/>

</xsl:stylesheet>