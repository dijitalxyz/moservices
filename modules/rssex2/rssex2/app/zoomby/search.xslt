<?xml version="1.0"?>
<xsl:stylesheet version="1.0"
  xmlns:xsl="http://www.w3.org/1999/XSL/Transform"
  exclude-result-prefixes=""
>

<xsl:output method="xml" encoding="utf-8" indent="yes"/>

<xsl:template match="/">
    <xsl:apply-templates select="//div[@class='content_left']//div[@class='b-similar-items']//div[@class='b-listcolumns b-listcolumns_smallvars']//div[@class='elements_col']"/>
</xsl:template>

<xsl:template match="div[@class='elements_col']">
  <xsl:variable name="img" select="div[@class='element_row_1']/a/img/@style" />
  <xsl:variable name="title1" select="div[@class='element_row_3']/a" />
  <xsl:variable name="title2" select="div[@class='element_row_4']/a" />
  <xsl:variable name="link" select="concat('http://zoomby.ru',div[@class='element_row_3']/a/@href)" />

	 <item>
	  <title><xsl:value-of select="$title1"/>  / <xsl:value-of select="$title2"/></title>
	  <link><xsl:value-of select="$link" /></link>
	  <image url="{$img}" width="" height=""/>
	 </item>

</xsl:template>

<xsl:template match="* | @* | node()"/>

</xsl:stylesheet>