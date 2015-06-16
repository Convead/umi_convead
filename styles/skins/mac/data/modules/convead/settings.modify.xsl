<?xml version="1.0" encoding="utf-8"?>
<!DOCTYPE xsl:stylesheet SYSTEM "ulang://common/content">

<xsl:stylesheet version="1.0" xmlns:xsl="http://www.w3.org/1999/XSL/Transform">
    <xsl:template match="group[@name = 'convead_config']" mode="settings.modify">
        <div class="panel properties-group">
            <div class="header">
                <span>
                    <xsl:value-of select="@label" />
                </span>
                <div class="l" /><div class="r" />
            </div>
            <div class="content">
                <table class="tableContent">
                    <tbody>
                        <tr>
                            <td colspan="2">
                                <a href="https://app.convead.io/" target="_blank">Перейти в аккаунт Convead</a>
                            </td>
                        </tr>
                        <xsl:apply-templates select="option" mode="settings.modify" />
                        <tr>
                            <td colspan="2">
                                <p>
                                    <b>Внимание!</b><br />
                                    Для работы модуля необходимо добавить в шаблон вашего магазина перед закрывающим тегом &lt;/head&gt; следующую строку:<br />
                                    - для PHP-шаблонизатора - &lt;?= $this->macros('convead', 'getConveadScript');?&gt;<br />
                                    - для XSLT-шаблонизатора - &lt;xsl:value-of select="document('udata://convead/getConveadScript/')/udata" disable-output-escaping="yes" /&gt;<br />
                                    - для TPL-шаблонзитора - %convead getConveadScript()% <br />
                                </p>
                            </td>
                        </tr>
                    </tbody>
                </table>

                <xsl:call-template name="std-save-button" />
            </div>
        </div>
    </xsl:template>
</xsl:stylesheet>