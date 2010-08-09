<!DOCTYPE html>
<!-- Thanks for looking at my source code. It's hand-coded and auto-formatted. -->
<html lang="en-US" dir="ltr">
<head>
    <meta charset="utf-8" />
    <meta name="expires" content="<% pxw_cache_expires %>" />
    <meta name="robots" content="all"/>
    <meta name="verify-v1" content=" <% data_goog_webm_key %>" />
    <meta name="profile" content="http://gmpg.org/xfn/11" />
    <meta name="apple-mobile-web-app-capable" content="yes" />
    <meta name="apple-mobile-web-app-status-bar-style" content="black" />

    <meta name="description" content="<% data_description %>" />
    <meta name="keywords" content="<% data_keywords %>" />
    <meta name="author" content="<% data_name %>" />
    <meta name="owner" content="<% data_email %>" />
    <meta name="build" content="<% data_site_version %>" />
    <meta name="copyright" content="(cc) <% data_copyright_start %>-<% pxw_current_year %> <% data_name %>. <% data_copyright_meta %>" />

    <title><% title %> {<% obj_name %>}</title>

    <link rel="author" href="<% baseurl %>" />
    <link rel="license content-license" href="<% data_content_license_link %>" />
    <link rel="license source-license" href="<% data_source_license_link %>" />
    <link rel="shortcut icon" href="<% baseurl %><% basename %>/site/<% obj_theme %>/favicon.ico" />
    <link rel="apple-touch-icon" href="<% baseurl %>/favicon.png" />

    <!-- custom styles -->
    <link rel="stylesheet" href="<% baseurl %><% basename %>/site/<% obj_theme %>/style.css" media="all" />
<!--[if lte IE 8]>
    <link href="<% baseurl %><% basename %>/site/<% obj_theme %>/layout_mobile.css" rel="stylesheet" media="handheld" id="layout_mobile-css" />
    <link href="<% baseurl %><% basename %>/site/<% obj_theme %>/layout_print.css" rel="stylesheet" media="print" id="layout_print-css" />
    <link href="<% baseurl %><% basename %>/site/<% obj_theme %>/layout_screen.css" rel="stylesheet" media="screen" id="layout_screen-css" />
<![endif]-->
    <link rel="stylesheet" href="<% baseurl %><% basename %>/site/<% obj_theme %>/color.css" media="all" />
    <link rel="stylesheet" href="<% baseurl %><% basename %>/site/<% obj_theme %>/type.css" media="all" />
<!--[if lte IE 8]><link href="<% baseurl %><% basename %>/site/<% obj_theme %>/lte_ie8.css" rel="stylesheet" media="all" id="lte_ie8-css" /><![endif]-->
<!--[if lte IE 7]><link href="<% baseurl %><% basename %>/site/<% obj_theme %>/lte_ie7.css" rel="stylesheet" media="all" id="lte_ie7-css" /><![endif]-->
<!--[if lte IE 6]><link href="<% baseurl %><% basename %>/site/<% obj_theme %>/lte_ie6.css" rel="stylesheet" media="all" id="lte_ie6-css" /><![endif]-->

    <!-- indexhibit styles -->
    <plug:front_lib_css />
    <plug:front_dyn_css />
    
    <!-- custom scripts -->
    <script type="text/javascript" src="http://ajax.googleapis.com/ajax/libs/jquery/1/jquery.min.js"></script>
    <script type="text/javascript" src="<% baseurl %><% basename %>/site/js/modernizr.js"></script>
    <script type="text/javascript" src="<% baseurl %><% basename %>/site/js/jquery.template.js"></script>
    <script type="text/javascript" src="<% baseurl %><% basename %>/site/js/jquery.hlf.tip.js"></script>
    <script type="text/javascript" src="<% baseurl %><% basename %>/site/js/ndxz.accordion-menu.js"></script>
    <script type="text/javascript" src="<% baseurl %><% basename %>/site/js/cookie.js"></script>
    <script type="text/javascript" src="<% baseurl %><% basename %>/site/js/swfobject.js"></script>
    <script type="text/javascript" src="<% baseurl %><% basename %>/site/pengxwang/site.js"></script>
    <script type="text/javascript"> 
        var path = Site.mediaUrl = '<% baseurl %>/files/gimgs/'; 
        Site.serviceUrl = '<% baseurl %><% basename %>/extend/service';
    </script>
    <plug:posterous_feed section_id="<% section_id %>", hostname="pengxwang"/>
    <!--[if lt IE 9]><script src="http://html5shiv.googlecode.com/svn/trunk/html5.js"></script><![endif]-->
    
    <!-- indexhibit scripts -->
    <plug:front_lib_js />
    <plug:front_dyn_js />
    
    <plug:backgrounder color="<% color %>", img="<% bgimg %>", tile="<% tiling %>" />
</head>
<body id="html" class="section-<% section_id %> <% pxw_browser %> <% pxw_browser_and_version %> <% pxw_browser_platform %>">
    <div id="menu">
        <div class="container">
            <div id="hd" class="box">
                <div class="in">
                    <h1 id="logo"><a class="replaced" href="<% baseurl %>" title="Home"><% obj_name %></a></h1>
                    <!-- <%obj_itop%> -->
                </div>
            </div>
            <div id="navigation" class="box">
                <div class="mn mnV">
                    <plug:front_index />
                </div>
            </div>
            <div id="ft" class="box copy">
                <div class="in">
                    <!-- <%obj_ibot%> -->
                    <p>
                        <a href="<% data_content_license_link %>" title="<% data_copyright_tip %>">
                            &copy;</a> 
                            <% data_copyright_start %>&ndash;<% pxw_current_year %>
                        <plug:the_email 
                              address="<% data_email %>"
                            , name="<% data_name %>"
                            , title="<% data_email_tip %>"
                            />
                    </p>
                    <p id="colophon">
                        <em>Curation and plumbing via <a href="http://www.indexhibit.org/" title="<% data_ndxz_tip %>">Indexhibit</a><br/>
                        with custom JS &amp; PHP plugins<br/>
                        and a custom <a href="<% pxw_html_validation %>" title="W3C validation">XHTML</a> &amp; <a href="<% pxw_css_validation %>" title="W3C validation">CSS</a> theme<br/>
                        Hosting via <a href="<% data_dreamhost_link %>" title="<% data_dreamhost_tip %>">DreamHost</a></em>
                    </p>
                </div>
            </div>
        </div><!-- .container -->
    </div><!-- #menu -->
    <div id="content" class="fixC">
        <div class="container">
            <div id="bd" class="copy">
                <plug:front_template section_id="<% section_id %>", template_name="feed" />
                <plug:front_exhibit />
            </div>
        </div><!-- .container -->
    </div><!-- #content -->
    <script type="text/javascript">
        var gaJsHost = (("https:" == document.location.protocol) ? "https://ssl." : "http://www.");
        document.write(unescape("%3Cscript src=\'" + gaJsHost + "google-analytics.com/ga.js\' type=\'text/javascript\'%3E%3C/script%3E"));
        try {
            var pageTracker = _gat._getTracker("UA-10346713-1");
            pageTracker._trackPageview();
        } catch (err) {}    
    </script>
</body>
</html>

