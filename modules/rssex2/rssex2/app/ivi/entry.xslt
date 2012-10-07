<?xml version="1.0"?>
<xsl:stylesheet version="1.0"
  xmlns:xsl="http://www.w3.org/1999/XSL/Transform" 
  exclude-result-prefixes=""
>

<xsl:output method="xml" encoding="utf-8" indent="yes"/>

<xsl:template match="/">
  <entry>
    <xsl:variable name="table" select="//table[1]" />
    <content><xsl:value-of select="//p" /></content>
    <id><xsl:value-of select="$table/tr[1]/td[2]" /></id>
    <title><xsl:value-of select="$table/tr[2]/td[2]" /></title>
    <date><xsl:value-of select="$table/tr[3]/td[2]" /></date>
    <duration><xsl:value-of select="$table/tr[4]/td[2]" /></duration>
    <category><xsl:value-of select="$table/tr[5]/td[2]" /></category>
    <genre><xsl:value-of select="$table/tr[6]/td[2]" /></genre>
    <url><xsl:value-of select="$table/tr[7]/td[2]" /></url>
    <tags><xsl:value-of select="$table/tr[8]/td[2]" /></tags>
  </entry>
</xsl:template>

<xsl:template match="* | @* | node()"/>

</xsl:stylesheet>