/*!
 * imagesLoaded PACKAGED v4.1.0
 * JavaScript is all like "You images are done yet or what?"
 * MIT License
 */

/*!
 * imagesLoaded v4.1.0
 * JavaScript is all like "You images are done yet or what?"
 * MIT License
 */

(function(e,t){typeof define=="function"&&define.amd?define("ev-emitter/ev-emitter",t):typeof module=="object"&&module.exports?module.exports=t():e.EvEmitter=t()})(this,function(){function e(){}var t=e.prototype;return t.on=function(e,t){if(!e||!t)return;var n=this._events=this._events||{},r=n[e]=n[e]||[];return r.indexOf(t)==-1&&r.push(t),this},t.once=function(e,t){if(!e||!t)return;this.on(e,t);var n=this._onceEvents=this._onceEvents||{},r=n[e]=n[e]||[];return r[t]=!0,this},t.off=function(e,t){var n=this._events&&this._events[e];if(!n||!n.length)return;var r=n.indexOf(t);return r!=-1&&n.splice(r,1),this},t.emitEvent=function(e,t){var n=this._events&&this._events[e];if(!n||!n.length)return;var r=0,i=n[r];t=t||[];var s=this._onceEvents&&this._onceEvents[e];while(i){var o=s&&s[i];o&&(this.off(e,i),delete s[i]),i.apply(this,t),r+=o?0:1,i=n[r]}return this},e}),function(e,t){"use strict";typeof define=="function"&&define.amd?define(["ev-emitter/ev-emitter"],function(n){return t(e,n)}):typeof module=="object"&&module.exports?module.exports=t(e,require("ev-emitter")):e.imagesLoaded=t(e,e.EvEmitter)}(window,function(t,n){function s(e,t){for(var n in t)e[n]=t[n];return e}function o(e){var t=[];if(Array.isArray(e))t=e;else if(typeof e.length=="number")for(var n=0;n<e.length;n++)t.push(e[n]);else t.push(e);return t}function u(e,t,n){if(!(this instanceof u))return new u(e,t,n);typeof e=="string"&&(e=document.querySelectorAll(e)),this.elements=o(e),this.options=s({},this.options),typeof t=="function"?n=t:s(this.options,t),n&&this.on("always",n),this.getImages(),r&&(this.jqDeferred=new r.Deferred),setTimeout(function(){this.check()}.bind(this))}function f(e){this.img=e}function l(e,t){this.url=e,this.element=t,this.img=new Image}var r=t.jQuery,i=t.console;u.prototype=Object.create(n.prototype),u.prototype.options={},u.prototype.getImages=function(){this.images=[],this.elements.forEach(this.addElementImages,this)},u.prototype.addElementImages=function(e){e.nodeName=="IMG"&&this.addImage(e),this.options.background===!0&&this.addElementBackgroundImages(e);var t=e.nodeType;if(!t||!a[t])return;var n=e.querySelectorAll("img");for(var r=0;r<n.length;r++){var i=n[r];this.addImage(i)}if(typeof this.options.background=="string"){var s=e.querySelectorAll(this.options.background);for(r=0;r<s.length;r++){var o=s[r];this.addElementBackgroundImages(o)}}};var a={1:!0,9:!0,11:!0};return u.prototype.addElementBackgroundImages=function(e){var t=getComputedStyle(e);if(!t)return;var n=/url\((['"])?(.*?)\1\)/gi,r=n.exec(t.backgroundImage);while(r!==null){var i=r&&r[2];i&&this.addBackground(i,e),r=n.exec(t.backgroundImage)}},u.prototype.addImage=function(e){var t=new f(e);this.images.push(t)},u.prototype.addBackground=function(e,t){var n=new l(e,t);this.images.push(n)},u.prototype.check=function(){function t(t,n,r){setTimeout(function(){e.progress(t,n,r)})}var e=this;this.progressedCount=0,this.hasAnyBroken=!1;if(!this.images.length){this.complete();return}this.images.forEach(function(e){e.once("progress",t),e.check()})},u.prototype.progress=function(e,t,n){this.progressedCount++,this.hasAnyBroken=this.hasAnyBroken||!e.isLoaded,this.emitEvent("progress",[this,e,t]),this.jqDeferred&&this.jqDeferred.notify&&this.jqDeferred.notify(this,e),this.progressedCount==this.images.length&&this.complete(),this.options.debug&&i&&i.log("progress: "+n,e,t)},u.prototype.complete=function(){var e=this.hasAnyBroken?"fail":"done";this.isComplete=!0,this.emitEvent(e,[this]),this.emitEvent("always",[this]);if(this.jqDeferred){var t=this.hasAnyBroken?"reject":"resolve";this.jqDeferred[t](this)}},f.prototype=Object.create(n.prototype),f.prototype.check=function(){var e=this.getIsImageComplete();if(e){this.confirm(this.img.naturalWidth!==0,"naturalWidth");return}this.proxyImage=new Image,this.proxyImage.addEventListener("load",this),this.proxyImage.addEventListener("error",this),this.img.addEventListener("load",this),this.img.addEventListener("error",this),this.proxyImage.src=this.img.src},f.prototype.getIsImageComplete=function(){return this.img.complete&&this.img.naturalWidth!==undefined},f.prototype.confirm=function(e,t){this.isLoaded=e,this.emitEvent("progress",[this,this.img,t])},f.prototype.handleEvent=function(e){var t="on"+e.type;this[t]&&this[t](e)},f.prototype.onload=function(){this.confirm(!0,"onload"),this.unbindEvents()},f.prototype.onerror=function(){this.confirm(!1,"onerror"),this.unbindEvents()},f.prototype.unbindEvents=function(){this.proxyImage.removeEventListener("load",this),this.proxyImage.removeEventListener("error",this),this.img.removeEventListener("load",this),this.img.removeEventListener("error",this)},l.prototype=Object.create(f.prototype),l.prototype.check=function(){this.img.addEventListener("load",this),this.img.addEventListener("error",this),this.img.src=this.url;var e=this.getIsImageComplete();e&&(this.confirm(this.img.naturalWidth!==0,"naturalWidth"),this.unbindEvents())},l.prototype.unbindEvents=function(){this.img.removeEventListener("load",this),this.img.removeEventListener("error",this)},l.prototype.confirm=function(e,t){this.isLoaded=e,this.emitEvent("progress",[this,this.element,t])},u.makeJQueryPlugin=function(e){e=e||t.jQuery;if(!e)return;r=e,r.fn.imagesLoaded=function(e,t){var n=new u(this,e,t);return n.jqDeferred.promise(r(this))}},u.makeJQueryPlugin(),u});