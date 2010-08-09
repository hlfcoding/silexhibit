jQuery(document).ready(function($) {$.preloadCssImages($);});
jQuery.preloadCssImages = function($) {
    var allImgs = []; // new array for all the image urls 
    var k = 0; // iterator for adding images
    var sheets = document.styleSheets; // array of stylesheets
    for(var i = 0; i < sheets.length; i++) // loop through each stylesheet
    {
        var cssPile = ''; // create large string of all css rules in sheet
        var csshref = (sheets[i].href) ? sheets[i].href : 'window.location.href';
        var baseURLarr = csshref.split('/'); // split href at / to make array
        baseURLarr.pop(); // remove file path from baseURL array
        var baseURL = baseURLarr.join('/'); // create base url for the images in this sheet (css file's dir)
        if (baseURL != "") baseURL += '/'; // tack on a / if needed
        if (document.styleSheets[i].cssRules) // w3
        {
            var thisSheetRules = document.styleSheets[i].cssRules; // w3
            for(var j = 0; j < thisSheetRules.length; j++)
            {
                cssPile += thisSheetRules[j].cssText;
            }
        }
        else 
        {
            cssPile += document.styleSheets[i].cssText;
        }
        // parse cssPile for image urls and load them into the DOM
        var imgUrls = cssPile.match(/[^\(]+\.(gif|jpg|jpeg|png)/g); // reg ex to get a string of between a "(" and a ".filename"
        if (imgUrls != null && imgUrls.length>0 && imgUrls != '') // loop array
        {
            var arr = $.makeArray(imgUrls); // create array from regex obj       
            $(arr).each(function()
                {
                    allImgs[k] = new Image(); // snew img obj
                    allImgs[k].src = (this[0] == '/' || this.match('http: //')) ? this : baseURL + this;     // sset src either absolute or rel to css dir
                    k++;
                }
            );
        }
    } // loop
    return allImgs;
};
