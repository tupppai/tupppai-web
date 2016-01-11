/*!
 * imagesLoaded PACKAGED v3.1.8
 * JavaScript is all like "You images are done yet or what?"
 * MIT License
 */

/*!
 * EventEmitter v4.2.6 - git.io/ee
 * Oliver Caldwell
 * MIT license
 * @preserve
 */

/*!
 * eventie v1.0.4
 * event binding helper
 *   eventie.bind( elem, 'click', myFn )
 *   eventie.unbind( elem, 'click', myFn )
 */

/*!
 * imagesLoaded v3.1.8
 * JavaScript is all like "You images are done yet or what?"
 * MIT License
 */

(function(){function e(){}function i(e,t){var n=e.length;while(n--)if(e[n].listener===t)return n;return-1}function s(e){return function(){return this[e].apply(this,arguments)}}var t=e.prototype,n=this,r=n.EventEmitter;t.getListeners=function(t){var n=this._getEvents(),r,i;if(typeof t=="object"){r={};for(i in n)n.hasOwnProperty(i)&&t.test(i)&&(r[i]=n[i])}else r=n[t]||(n[t]=[]);return r},t.flattenListeners=function(t){var n=[],r;for(r=0;r<t.length;r+=1)n.push(t[r].listener);return n},t.getListenersAsObject=function(t){var n=this.getListeners(t),r;return n instanceof Array&&(r={},r[t]=n),r||n},t.addListener=function(t,n){var r=this.getListenersAsObject(t),s=typeof n=="object",o;for(o in r)r.hasOwnProperty(o)&&i(r[o],n)===-1&&r[o].push(s?n:{listener:n,once:!1});return this},t.on=s("addListener"),t.addOnceListener=function(t,n){return this.addListener(t,{listener:n,once:!0})},t.once=s("addOnceListener"),t.defineEvent=function(t){return this.getListeners(t),this},t.defineEvents=function(t){for(var n=0;n<t.length;n+=1)this.defineEvent(t[n]);return this},t.removeListener=function(t,n){var r=this.getListenersAsObject(t),s,o;for(o in r)r.hasOwnProperty(o)&&(s=i(r[o],n),s!==-1&&r[o].splice(s,1));return this},t.off=s("removeListener"),t.addListeners=function(t,n){return this.manipulateListeners(!1,t,n)},t.removeListeners=function(t,n){return this.manipulateListeners(!0,t,n)},t.manipulateListeners=function(t,n,r){var i,s,o=t?this.removeListener:this.addListener,u=t?this.removeListeners:this.addListeners;if(typeof n!="object"||n instanceof RegExp){i=r.length;while(i--)o.call(this,n,r[i])}else for(i in n)n.hasOwnProperty(i)&&(s=n[i])&&(typeof s=="function"?o.call(this,i,s):u.call(this,i,s));return this},t.removeEvent=function(t){var n=typeof t,r=this._getEvents(),i;if(n==="string")delete r[t];else if(n==="object")for(i in r)r.hasOwnProperty(i)&&t.test(i)&&delete r[i];else delete this._events;return this},t.removeAllListeners=s("removeEvent"),t.emitEvent=function(t,n){var r=this.getListenersAsObject(t),i,s,o,u;for(o in r)if(r.hasOwnProperty(o)){s=r[o].length;while(s--)i=r[o][s],i.once===!0&&this.removeListener(t,i.listener),u=i.listener.apply(this,n||[]),u===this._getOnceReturnValue()&&this.removeListener(t,i.listener)}return this},t.trigger=s("emitEvent"),t.emit=function(t){var n=Array.prototype.slice.call(arguments,1);return this.emitEvent(t,n)},t.setOnceReturnValue=function(t){return this._onceReturnValue=t,this},t._getOnceReturnValue=function(){return this.hasOwnProperty("_onceReturnValue")?this._onceReturnValue:!0},t._getEvents=function(){return this._events||(this._events={})},e.noConflict=function(){return n.EventEmitter=r,e},typeof define=="function"&&define.amd?define("eventEmitter/EventEmitter",[],function(){return e}):typeof module=="object"&&module.exports?module.exports=e:this.EventEmitter=e}).call(this),function(e){function r(t){var n=e.event;return n.target=n.target||n.srcElement||t,n}var t=document.documentElement,n=function(){};t.addEventListener?n=function(e,t,n){e.addEventListener(t,n,!1)}:t.attachEvent&&(n=function(e,t,n){e[t+n]=n.handleEvent?function(){var t=r(e);n.handleEvent.call(n,t)}:function(){var t=r(e);n.call(e,t)},e.attachEvent("on"+t,e[t+n])});var i=function(){};t.removeEventListener?i=function(e,t,n){e.removeEventListener(t,n,!1)}:t.detachEvent&&(i=function(e,t,n){e.detachEvent("on"+t,e[t+n]);try{delete e[t+n]}catch(r){e[t+n]=undefined}});var s={bind:n,unbind:i};typeof define=="function"&&define.amd?define("eventie/eventie",s):e.eventie=s}(this),function(e,t){typeof define=="function"&&define.amd?define(["eventEmitter/EventEmitter","eventie/eventie"],function(n,r){return t(e,n,r)}):typeof exports=="object"?module.exports=t(e,require("wolfy87-eventemitter"),require("eventie")):e.imagesLoaded=t(e,e.EventEmitter,e.eventie)}(window,function(t,n,r){function u(e,t){for(var n in t)e[n]=t[n];return e}function f(e){return a.call(e)==="[object Array]"}function l(e){var t=[];if(f(e))t=e;else if(typeof e.length=="number")for(var n=0,r=e.length;n<r;n++)t.push(e[n]);else t.push(e);return t}function c(e,t,n){if(!(this instanceof c))return new c(e,t);typeof e=="string"&&(e=i(e)),this.elements=l(e),this.options=u({},this.options),typeof t=="function"?n=t:u(this.options,t),n&&this.on("always",n),this.getImages(),i&&(this.jqDeferred=new i.Deferred);var r=this;setTimeout(function(){r.check()})}function h(e){this.img=e}function d(e){this.src=e,p[e]=this}var i=t.jQuery,s=t.console,o=typeof s!="undefined",a=Object.prototype.toString;c.prototype=new n,c.prototype.options={},c.prototype.getImages=function(){this.images=[];for(var e=0,t=this.elements.length;e<t;e++){var n=this.elements[e];n.nodeName==="IMG"&&this.addImage(n);var r=n.nodeType;if(!r||r!==1&&r!==9&&r!==11)continue;var s=i(n).find("img");for(var o=0,u=s.length;o<u;o++){var a=s[o];this.addImage(a)}}},c.prototype.addImage=function(e){var t=new h(e);this.images.push(t)},c.prototype.check=function(){function r(r,i){return e.options.debug&&o&&s.log("confirm",r,i),e.progress(r),t++,t===n&&e.complete(),!0}var e=this,t=0,n=this.images.length;this.hasAnyBroken=!1;if(!n){this.complete();return}for(var i=0;i<n;i++){var u=this.images[i];u.on("confirm",r),u.check()}},c.prototype.progress=function(e){this.hasAnyBroken=this.hasAnyBroken||!e.isLoaded;var t=this;setTimeout(function(){t.emit("progress",t,e),t.jqDeferred&&t.jqDeferred.notify&&t.jqDeferred.notify(t,e)})},c.prototype.complete=function(){var e=this.hasAnyBroken?"fail":"done";this.isComplete=!0;var t=this;setTimeout(function(){t.emit(e,t),t.emit("always",t);if(t.jqDeferred){var n=t.hasAnyBroken?"reject":"resolve";t.jqDeferred[n](t)}})},i&&(i.fn.imagesLoaded=function(e,t){var n=new c(this,e,t);return n.jqDeferred.promise(i(this))}),h.prototype=new n,h.prototype.check=function(){var e=p[this.img.src]||new d(this.img.src);if(e.isConfirmed){this.confirm(e.isLoaded,"cached was confirmed");return}if(this.img.complete&&this.img.naturalWidth!==undefined){this.confirm(this.img.naturalWidth!==0,"naturalWidth");return}var t=this;e.on("confirm",function(e,n){return t.confirm(e.isLoaded,n),!0}),e.check()},h.prototype.confirm=function(e,t){this.isLoaded=e,this.emit("confirm",this,t)};var p={};return d.prototype=new n,d.prototype.check=function(){if(this.isChecked)return;var e=new Image;r.bind(e,"load",this),r.bind(e,"error",this),e.src=this.src,this.isChecked=!0},d.prototype.handleEvent=function(e){var t="on"+e.type;this[t]&&this[t](e)},d.prototype.onload=function(e){this.confirm(!0,"onload"),this.unbindProxyEvents(e)},d.prototype.onerror=function(e){this.confirm(!1,"onerror"),this.unbindProxyEvents(e)},d.prototype.confirm=function(e,t){this.isConfirmed=!0,this.isLoaded=e,this.emit("confirm",this,t)},d.prototype.unbindProxyEvents=function(e){r.unbind(e.target,"load",this),r.unbind(e.target,"error",this)},c});