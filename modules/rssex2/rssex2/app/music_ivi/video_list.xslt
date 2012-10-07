<?xml version="1.0"?>
<xsl:stylesheet version="1.0"
  xmlns:xsl="http://www.w3.org/1999/XSL/Transform"
  xmlns:media="http://search.yahoo.com/mrss/"
  exclude-result-prefixes="media"
>

<xsl:output method="xml" encoding="utf-8" indent="yes"/>

<xsl:param name="type" select="''" />

<xsl:template match="/">
  <xsl:choose>
    <xsl:when test="$type = 'artist'">
      <xsl:apply-templates select="//div[@class='artist-all-video']//li"/>
    </xsl:when>
    <xsl:when test="$type = 'playlist'">
      <xsl:apply-templates select="//div[@class='playlist-other-video']//li"/>
    </xsl:when>
    <xsl:otherwise>
      <pages><xsl:value-of select="string(//div[@class='paginator']//li[last()-1]/a)" /></pages>
      <xsl:apply-templates select="//ul[@class='video-list']//li"/>
    </xsl:otherwise>
  </xsl:choose>
</xsl:template>

<xsl:template match="li">
  <xsl:variable name="img" select="a[1]/span[1]/img" />
  <xsl:variable name="link" select="a[1]/@href" />
  <xsl:variable name="song" select="string(a[1]/span[2]/strong)" />
  <xsl:variable name="artist" select="string(span/a)" />
  <xsl:variable name="title" select="concat($artist,' - ',$song)" />

	<item>
		<title><xsl:value-of select="$title" /></title>
		<location><xsl:if test="starts-with($link, '/')">http://music.ivi.ru</xsl:if><xsl:value-of select="$link" /></location>
    <media:thumbnail>
      <xsl:attribute name="url">
        <xsl:if test="starts-with($img/@src, '/')">http://music.ivi.ru</xsl:if>
        <xsl:value-of select="$img/@src" />
      </xsl:attribute>
    </media:thumbnail>
		<artist><xsl:value-of select="$artist" /></artist>
		<song><xsl:value-of select="$song" /></song>
	</item>

</xsl:template>

<xsl:template match="* | @* | node()"/>

</xsl:stylesheet>