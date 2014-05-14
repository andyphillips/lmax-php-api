
<html>
<head>
    <title>Connection Sniffer</title>
</head>
<body>
<p>
Information in the first two sections is gleaned on the web server from the HTTP protocol. The third set of details is gleaned in the browser by JavaScript.
</p>
Captured at: Wed Mar 14 17:31:38 GMT 2012<br>
<p>
<b>Hostname</b> :
cxaweb02.gs2.tradefair

</p>
<h2>HTTP Info (Server Side)</h2>

<li><b>Server Name</b> : main
<li><b>Method Type</b> : GET
<li><b>Request URI</b> : /snoop.jsp
<li><b>Request URL</b> : http://main/snoop.jsp
<li><b>Protocol</b> : HTTP/1.0
<li><b>PathInfo</b> : null
<li><b>QueryString</b> : null
<li><b>Remote Address</b> : 91.215.166.4
<li><b>Is Secure</b> : False
<li><b>User Principal</b> : null
<P>

<h2>HTTP Request Headers (Server Side)</h2>
<li><b>User-Agent</b> : Wget/1.12 (linux-gnu)
<li><b>Accept</b> : */*
<li><b>Host</b> : testapi.lmaxtrader.com
<li><b>Connection</b> : Keep-Alive

<P>

<h2>JavaScript Sniffer (Client Side)</h2>

<script language="JavaScript" type="text/javascript">
    document.write("<li><b>navigator.appName</b> : " + navigator.appName);
    document.write("<li><b>navigator.userAgent</b> : " + navigator.userAgent);
    document.write("<li><b>navigator.appVersion</b> : " + navigator.appVersion);
    document.write("<li><b>Screen Resolution:</b> ", screen.width, " x ", screen.height);
    document.write("<li><b>Java Enabled:</b> ", navigator.javaEnabled());
</script>

<p>
<h3>Plugins</h3>
<script language="JavaScript" type="text/javascript">
  if (navigator.plugins)
  {
    var numPlugins = navigator.plugins.length;

    if (numPlugins > 0)
    {
        document.writeln(
            "<table border=1>",
            "<tr align=top>",
            "<th align=left>#</th>",
            "<th align=left>name</th>",
            "<th align=left>filename</th>",
            "<th align=left>description</th>",
            "<th align=left># of types</th>",
            "</tr>");

        for (i = 0; i < numPlugins; i++)
        {
            document.writeln(
                "<tr valign=top>",
                "<td>", i, "</td>",
                "<td>", navigator.plugins[i].name, "</td>",
                "<td>", navigator.plugins[i].filename, "</td>",
                "<td>", navigator.plugins[i].description, "</td>",
                "<td>", navigator.plugins[i].length, "</td>",
                "</tr>");
        }

        document.writeln("</table>");
    }
    else
    {
        document.writeln("No Plugins exist but IE never lists plugins because it uses ActiveX controls");
    }
  }
</script>

</body>
</html>