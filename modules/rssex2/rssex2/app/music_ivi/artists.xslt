<?xml version="1.0"?>
<xsl:stylesheet version="1.0"
 xmlns:xsl="http://www.w3.org/1999/XSL/Transform"
 exclude-result-prefixes=""
>

<xsl:output method="xml" encoding="utf-8" indent="yes"/>

<xsl:template match="/">
  <pages><xsl:value-of select="string(//div[@class='paginator']//li[last()-1]/a)" /></pages>
  <xsl:apply-templates select="//ul[@class='artist-list']/li"/>
</xsl:template>

<xsl:template match="li">
 <xsl:variable name="img" select="a[1]/span[1]/img" />
 <xsl:variable name="title" select="$img/@alt" />
 <xsl:variable name="link" select="a[1]/@href" />    
   <item>
      <title><xsl:value-of select="$title" /></title>
      <link>http://music.ivi.ru<xsl:value-of select="$link" /></link>
      <description></description>
      <source url="http://music.ivi.ru/artists">misic.ivi.ru/artists</source>
      <image url="{$img/@src}" width="" height=""/>
   </item>
</xsl:template>

<xsl:template match="* | @* | node()"/>

</xsl:stylesheet>
