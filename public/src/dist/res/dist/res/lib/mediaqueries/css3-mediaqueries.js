typeof Object.create!="function"&&(Object.create=function(e){function t(){}return t.prototype=e,new t});var ua={toString:function(){return navigator.userAgent},test:function(e){return this.toString().toLowerCase().indexOf(e.toLowerCase())>-1}};ua.version=(ua.toString().toLowerCase().match(/[\s\S]+(?:rv|it|ra|ie)[\/: ]([\d.]+)/)||[])[1],ua.webkit=ua.test("webkit"),ua.gecko=ua.test("gecko")&&!ua.webkit,ua.opera=ua.test("opera"),ua.ie=ua.test("msie")&&!ua.opera,ua.ie6=ua.ie&&document.compatMode&&typeof document.documentElement.style.maxHeight=="undefined",ua.ie7=ua.ie&&document.documentElement&&typeof document.documentElement.style.maxHeight!="undefined"&&typeof XDomainRequest=="undefined",ua.ie8=ua.ie&&typeof XDomainRequest!="undefined";var domReady=function(){var e=[],t=function(){if(!arguments.callee.done){arguments.callee.done=!0;for(var t=0;t<e.length;t++)e[t]()}};return document.addEventListener&&document.addEventListener("DOMContentLoaded",t,!1),ua.ie&&(function(){try{document.documentElement.doScroll("left"),document.body.length}catch(e){setTimeout(arguments.callee,50);return}t()}(),document.onreadystatechange=function(){document.readyState==="complete"&&(document.onreadystatechange=null,t())}),ua.webkit&&document.readyState&&function(){document.readyState!=="loading"?t():setTimeout(arguments.callee,10)}(),window.onload=t,function(n){return typeof n=="function"&&(t.done?n():e[e.length]=n),n}}(),cssHelper=function(){var e={BLOCKS:/[^\s{][^{]*\{(?:[^{}]*\{[^{}]*\}[^{}]*|[^{}]*)*\}/g,BLOCKS_INSIDE:/[^\s{][^{]*\{[^{}]*\}/g,DECLARATIONS:/[a-zA-Z\-]+[^;]*:[^;]+;/g,RELATIVE_URLS:/url\(['"]?([^\/\)'"][^:\)'"]+)['"]?\)/g,REDUNDANT_COMPONENTS:/(?:\/\*([^*\\\\]|\*(?!\/))+\*\/|@import[^;]+;|@-moz-document\s*url-prefix\(\)\s*{(([^{}])+{([^{}])+}([^{}])+)+})/g,REDUNDANT_WHITESPACE:/\s*(,|:|;|\{|\})\s*/g,MORE_WHITESPACE:/\s{2,}/g,FINAL_SEMICOLONS:/;\}/g,NOT_WHITESPACE:/\S+/g},t,n=!1,r=[],i=function(e){typeof e=="function"&&(r[r.length]=e)},s=function(){for(var e=0;e<r.length;e++)r[e](t)},o={},u=function(e,t){if(o[e]){var n=o[e].listeners;if(n)for(var r=0;r<n.length;r++)n[r](t)}},a=function(e,t,n){ua.ie&&!window.XMLHttpRequest&&(window.XMLHttpRequest=function(){return new ActiveXObject("Microsoft.XMLHTTP")});if(!XMLHttpRequest)return"";var r=new XMLHttpRequest;try{r.open("get",e,!0),r.setRequestHeader("X_REQUESTED_WITH","XMLHttpRequest")}catch(i){n();return}var s=!1;setTimeout(function(){s=!0},5e3),document.documentElement.style.cursor="progress",r.onreadystatechange=function(){r.readyState===4&&!s&&(!r.status&&location.protocol==="file:"||r.status>=200&&r.status<300||r.status===304||navigator.userAgent.indexOf("Safari")>-1&&typeof r.status=="undefined"?t(r.responseText):n(),document.documentElement.style.cursor="",r=null)},r.send("")},f=function(t){return t=t.replace(e.REDUNDANT_COMPONENTS,""),t=t.replace(e.REDUNDANT_WHITESPACE,"$1"),t=t.replace(e.MORE_WHITESPACE," "),t=t.replace(e.FINAL_SEMICOLONS,"}"),t},l={mediaQueryList:function(t){var n={},r=t.indexOf("{"),i=t.substring(0,r);t=t.substring(r+1,t.length-1);var s=[],o=[],u=i.toLowerCase().substring(7).split(",");for(var a=0;a<u.length;a++)s[s.length]=l.mediaQuery(u[a],n);var f=t.match(e.BLOCKS_INSIDE);if(f!==null)for(a=0;a<f.length;a++)o[o.length]=l.rule(f[a],n);return n.getMediaQueries=function(){return s},n.getRules=function(){return o},n.getListText=function(){return i},n.getCssText=function(){return t},n},mediaQuery:function(t,n){t=t||"";var r=!1,i,s=[],o=!0,u=t.match(e.NOT_WHITESPACE);for(var a=0;a<u.length;a++){var f=u[a];if(!!i||f!=="not"&&f!=="only"){if(!i)i=f;else if(f.charAt(0)==="("){var l=f.substring(1,f.length-1).split(":");s[s.length]={mediaFeature:l[0],value:l[1]||null}}}else f==="not"&&(r=!0)}return{getList:function(){return n||null},getValid:function(){return o},getNot:function(){return r},getMediaType:function(){return i},getExpressions:function(){return s}}},rule:function(e,t){var n={},r=e.indexOf("{"),i=e.substring(0,r),s=i.split(","),o=[],u=e.substring(r+1,e.length-1).split(";");for(var a=0;a<u.length;a++)o[o.length]=l.declaration(u[a],n);return n.getMediaQueryList=function(){return t||null},n.getSelectors=function(){return s},n.getSelectorText=function(){return i},n.getDeclarations=function(){return o},n.getPropertyValue=function(e){for(var t=0;t<o.length;t++)if(o[t].getProperty()===e)return o[t].getValue();return null},n},declaration:function(e,t){var n=e.indexOf(":"),r=e.substring(0,n),i=e.substring(n+1);return{getRule:function(){return t||null},getProperty:function(){return r},getValue:function(){return i}}}},c=function(n){if(typeof n.cssHelperText!="string")return;var r={mediaQueryLists:[],rules:[],selectors:{},declarations:[],properties:{}},i=r.mediaQueryLists,s=r.rules,o=n.cssHelperText.match(e.BLOCKS);if(o!==null)for(var u=0;u<o.length;u++)o[u].substring(0,7)==="@media "?(i[i.length]=l.mediaQueryList(o[u]),s=r.rules=s.concat(i[i.length-1].getRules())):s[s.length]=l.rule(o[u]);var a=r.selectors,f=function(e){var t=e.getSelectors();for(var n=0;n<t.length;n++){var r=t[n];a[r]||(a[r]=[]),a[r][a[r].length]=e}};for(u=0;u<s.length;u++)f(s[u]);var c=r.declarations;for(u=0;u<s.length;u++)c=r.declarations=c.concat(s[u].getDeclarations());var h=r.properties;for(u=0;u<c.length;u++){var p=c[u].getProperty();h[p]||(h[p]=[]),h[p][h[p].length]=c[u]}return n.cssHelperParsed=r,t[t.length]=n,r},h=function(e,t){return e.cssHelperText=f(t||e.innerHTML),c(e)},p=function(){n=!0,t=[];var r=[],i=function(){for(var e=0;e<r.length;e++)c(r[e]);var t=document.getElementsByTagName("style");for(e=0;e<t.length;e++)h(t[e]);n=!1,s()},o=document.getElementsByTagName("link");for(var u=0;u<o.length;u++){var l=o[u];l.getAttribute("rel").indexOf("style")>-1&&l.href&&l.href.length!==0&&!l.disabled&&(r[r.length]=l)}if(r.length>0){var p=0,d=function(){p++,p===r.length&&i()},v=function(t){var n=t.href;a(n,function(r){r=f(r).replace(e.RELATIVE_URLS,"url("+n.substring(0,n.lastIndexOf("/"))+"/$1)"),t.cssHelperText=r,d()},d)};for(u=0;u<r.length;u++)v(r[u])}else i()},d={mediaQueryLists:"array",rules:"array",selectors:"object",declarations:"array",properties:"object"},v={mediaQueryLists:null,rules:null,selectors:null,declarations:null,properties:null},m=function(e,t){if(v[e]!==null){if(d[e]==="array")return v[e]=v[e].concat(t);var n=v[e];for(var r in t)t.hasOwnProperty(r)&&(n[r]?n[r]=n[r].concat(t[r]):n[r]=t[r]);return n}},g=function(e){v[e]=d[e]==="array"?[]:{};for(var n=0;n<t.length;n++)m(e,t[n].cssHelperParsed[e]);return v[e]};domReady(function(){var e=document.body.getElementsByTagName("*");for(var t=0;t<e.length;t++)e[t].checkedByCssHelper=!0;document.implementation.hasFeature("MutationEvents","2.0")||window.MutationEvent?document.body.addEventListener("DOMNodeInserted",function(e){var t=e.target;t.nodeType===1&&(u("DOMElementInserted",t),t.checkedByCssHelper=!0)},!1):setInterval(function(){var e=document.body.getElementsByTagName("*");for(var t=0;t<e.length;t++)e[t].checkedByCssHelper||(u("DOMElementInserted",e[t]),e[t].checkedByCssHelper=!0)},1e3)});var y=function(e){if(typeof window.innerWidth!="undefined")return window["inner"+e];if(typeof document.documentElement!="undefined"&&typeof document.documentElement.clientWidth!="undefined"&&document.documentElement.clientWidth!=0)return document.documentElement["client"+e]};return{addStyle:function(e,t){var n;return null!==document.getElementById("css-mediaqueries-js")?n=document.getElementById("css-mediaqueries-js"):(n=document.createElement("style"),n.setAttribute("type","text/css"),n.setAttribute("id","css-mediaqueries-js"),document.getElementsByTagName("head")[0].appendChild(n)),n.styleSheet?n.styleSheet.cssText+=e:n.appendChild(document.createTextNode(e)),n.addedWithCssHelper=!0,typeof t=="undefined"||t===!0?cssHelper.parsed(function(t){var r=h(n,e);for(var i in r)r.hasOwnProperty(i)&&m(i,r[i]);u("newStyleParsed",n)}):n.parsingDisallowed=!0,n},removeStyle:function(e){if(e.parentNode)return e.parentNode.removeChild(e)},parsed:function(e){n?i(e):typeof t!="undefined"?typeof e=="function"&&e(t):(i(e),p())},mediaQueryLists:function(e){cssHelper.parsed(function(t){e(v.mediaQueryLists||g("mediaQueryLists"))})},rules:function(e){cssHelper.parsed(function(t){e(v.rules||g("rules"))})},selectors:function(e){cssHelper.parsed(function(t){e(v.selectors||g("selectors"))})},declarations:function(e){cssHelper.parsed(function(t){e(v.declarations||g("declarations"))})},properties:function(e){cssHelper.parsed(function(t){e(v.properties||g("properties"))})},broadcast:u,addListener:function(e,t){typeof t=="function"&&(o[e]||(o[e]={listeners:[]}),o[e].listeners[o[e].listeners.length]=t)},removeListener:function(e,t){if(typeof t=="function"&&o[e]){var n=o[e].listeners;for(var r=0;r<n.length;r++)n[r]===t&&(n.splice(r,1),r-=1)}},getViewportWidth:function(){return y("Width")},getViewportHeight:function(){return y("Height")}}}();domReady(function(){var t,n={LENGTH_UNIT:/[0-9]+(em|ex|px|in|cm|mm|pt|pc)$/,RESOLUTION_UNIT:/[0-9]+(dpi|dpcm)$/,ASPECT_RATIO:/^[0-9]+\/[0-9]+$/,ABSOLUTE_VALUE:/^[0-9]*(\.[0-9]+)*$/},r=[],i=function(){var e="css3-mediaqueries-test",t=document.createElement("div");t.id=e;var n=cssHelper.addStyle("@media all and (width) { #"+e+" { width: 1px !important; } }",!1);document.body.appendChild(t);var r=t.offsetWidth===1;return n.parentNode.removeChild(n),t.parentNode.removeChild(t),i=function(){return r},r},s=function(){t=document.createElement("div"),t.style.cssText="position:absolute;top:-9999em;left:-9999em;margin:0;border:none;padding:0;width:1em;font-size:1em;",document.body.appendChild(t),t.offsetWidth!==16&&(t.style.fontSize=16/t.offsetWidth+"em"),t.style.width=""},o=function(e){t.style.width=e;var n=t.offsetWidth;return t.style.width="",n},u=function(e,t){var r=e.length,i=e.substring(0,4)==="min-",s=!i&&e.substring(0,4)==="max-";if(t!==null){var u,a;if(n.LENGTH_UNIT.exec(t))u="length",a=o(t);else if(n.RESOLUTION_UNIT.exec(t)){u="resolution",a=parseInt(t,10);var f=t.substring((a+"").length)}else n.ASPECT_RATIO.exec(t)?(u="aspect-ratio",a=t.split("/")):n.ABSOLUTE_VALUE?(u="absolute",a=t):u="unknown"}var l,c;if("device-width"===e.substring(r-12,r))return l=screen.width,t!==null?u==="length"?i&&l>=a||s&&l<a||!i&&!s&&l===a:!1:l>0;if("device-height"===e.substring(r-13,r))return c=screen.height,t!==null?u==="length"?i&&c>=a||s&&c<a||!i&&!s&&c===a:!1:c>0;if("width"===e.substring(r-5,r))return l=document.documentElement.clientWidth||document.body.clientWidth,t!==null?u==="length"?i&&l>=a||s&&l<a||!i&&!s&&l===a:!1:l>0;if("height"===e.substring(r-6,r))return c=document.documentElement.clientHeight||document.body.clientHeight,t!==null?u==="length"?i&&c>=a||s&&c<a||!i&&!s&&c===a:!1:c>0;if("orientation"===e.substring(r-11,r))return l=document.documentElement.clientWidth||document.body.clientWidth,c=document.documentElement.clientHeight||document.body.clientHeight,u==="absolute"?a==="portrait"?l<=c:l>c:!1;if("aspect-ratio"===e.substring(r-12,r)){l=document.documentElement.clientWidth||document.body.clientWidth,c=document.documentElement.clientHeight||document.body.clientHeight;var h=l/c,p=a[1]/a[0];return u==="aspect-ratio"?i&&h>=p||s&&h<p||!i&&!s&&h===p:!1}if("device-aspect-ratio"===e.substring(r-19,r))return u==="aspect-ratio"&&screen.width*a[1]===screen.height*a[0];if("color-index"===e.substring(r-11,r)){var d=Math.pow(2,screen.colorDepth);return t!==null?u==="absolute"?i&&d>=a||s&&d<a||!i&&!s&&d===a:!1:d>0}if("color"===e.substring(r-5,r)){var v=screen.colorDepth;return t!==null?u==="absolute"?i&&v>=a||s&&v<a||!i&&!s&&v===a:!1:v>0}if("resolution"===e.substring(r-10,r)){var m;return f==="dpcm"?m=o("1cm"):m=o("1in"),t!==null?u==="resolution"?i&&m>=a||s&&m<a||!i&&!s&&m===a:!1:m>0}return!1},a=function(e){var t=e.getValid(),n=e.getExpressions(),r=n.length;if(r>0){for(var i=0;i<r&&t;i++)t=u(n[i].mediaFeature,n[i].value);var s=e.getNot();return t&&!s||s&&!t}},f=function(e){var t=e.getMediaQueries(),n={};for(var i=0;i<t.length;i++)a(t[i])&&(n[t[i].getMediaType()]=!0);var s=[],o=0;for(var u in n)n.hasOwnProperty(u)&&(o>0&&(s[o++]=","),s[o++]=u);s.length>0&&(r[r.length]=cssHelper.addStyle("@media "+s.join("")+"{"+e.getCssText()+"}",!1))},l=function(e){for(var t=0;t<e.length;t++)f(e[t]);ua.ie?(document.documentElement.style.display="block",setTimeout(function(){document.documentElement.style.display=""},0),setTimeout(function(){cssHelper.broadcast("cssMediaQueriesTested")},100)):cssHelper.broadcast("cssMediaQueriesTested")},c=function(){for(var e=0;e<r.length;e++)cssHelper.removeStyle(r[e]);r=[],cssHelper.mediaQueryLists(l)},h=0,p=function(){var e=cssHelper.getViewportWidth(),t=cssHelper.getViewportHeight();if(ua.ie){var n=document.createElement("div");n.style.width="100px",n.style.height="100px",n.style.position="absolute",n.style.top="-9999em",n.style.overflow="scroll",document.body.appendChild(n),h=n.offsetWidth-n.clientWidth,document.body.removeChild(n)}var r,s=function(){var n=cssHelper.getViewportWidth(),s=cssHelper.getViewportHeight();if(Math.abs(n-e)>h||Math.abs(s-t)>h)e=n,t=s,clearTimeout(r),r=setTimeout(function(){i()?cssHelper.broadcast("cssMediaQueriesTested"):c()},500)};window.onresize=function(){var e=window.onresize||function(){};return function(){e(),s()}}()},d=document.documentElement;return d.style.marginLeft="-32767px",setTimeout(function(){d.style.marginTop=""},2e4),function(){i()?d.style.marginLeft="":(cssHelper.addListener("newStyleParsed",function(e){l(e.cssHelperParsed.mediaQueryLists)}),cssHelper.addListener("cssMediaQueriesTested",function(){ua.ie&&(d.style.width="1px"),setTimeout(function(){d.style.width="",d.style.marginLeft=""},0),cssHelper.removeListener("cssMediaQueriesTested",arguments.callee)}),s(),c()),p()}}());try{document.execCommand("BackgroundImageCache",!1,!0)}catch(e){};