<?xml version="1.0" encoding="utf-8"?>
<!DOCTYPE xsl:stylesheet [
<!ENTITY nl "&#xd;&#xa;">
]>
<xsl:stylesheet version="1.0" 
    xmlns:xsl="http://www.w3.org/1999/XSL/Transform"
    xmlns:xar="http://xaraya.com/2004/blocklayout"   
    xmlns:php="http://php.net/xsl" 
    exclude-result-prefixes="php xar">

<xsl:template match="xar:place-css">
  <xsl:processing-instruction name="php">
    <xsl:text>echo xarMod::apiFunc('themes','user','deliver',array('method' =&gt; 'render','base' =&gt; 'theme'));</xsl:text>
  </xsl:processing-instruction>
</xsl:template>

</xsl:stylesheet>
