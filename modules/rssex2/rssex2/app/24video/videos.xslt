<?xml version="1.0"?> <xsl:stylesheet version="1.0"
  xmlns:xsl="http://www.w3.org/1999/XSL/Transform"
  exclude-result-prefixes=""
>

<xsl:output method="xml" encoding="utf-8" indent="yes"/>

<xsl:template match="/">
    <xsl:apply-templates select="//ul[@class='clips']//li"/>
</xsl:template>

<xsl:template match="li">
  <xsl:variable name="img" select="div/a[1]/img" />
  <xsl:variable name="title" select="div/a[1]/@title" />
  <xsl:variable name="link" select="concat('http://www.24video.net',div/a[1]/@href)" />

	 <item>
	  <title><xsl:value-of select="$title" /></title>
	  <link><xsl:value-of select="$link" /></link>
	  <image url="{$img/@src}" width="" height=""/>
	 </item>
  <xsl:text>&#xA;</xsl:text>

</xsl:template>

<xsl:template match="* | @* | node()"/>

</xsl:stylesheet>