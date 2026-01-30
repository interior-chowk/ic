<?xml version="1.0" encoding="UTF-8"?>
<xsl:stylesheet 
  version="1.0" 
  xmlns:xsl="http://www.w3.org/1999/XSL/Transform" 
  xmlns:s="http://www.sitemaps.org/schemas/sitemap/0.9">
  
  <xsl:output method="html" indent="yes" />

  <xsl:template match="/">
    <html>
      <head>
        <title>Sitemap</title>
        <style>
          body { font-family: sans-serif; }
          a { display: block; margin: 5px 0; }
        </style>
      </head>
      <body>
        <h2>Sitemap Links</h2>
        <xsl:for-each select="//s:url">
          <xsl:variable name="url" select="s:loc"/>
          <a href="{s:loc}" target="_blank">
            <xsl:value-of select="$url"/>
          </a>
        </xsl:for-each>
      </body>
    </html>
  </xsl:template>
</xsl:stylesheet>
