(function(){var e,t,n,r,i,s,o,u,a,f,l,c,h,p,d=[].slice;i="3.0.0",t="pending",r="resolved",n="rejected",a=function(e,t){return e!=null?e.hasOwnProperty(t):void 0},l=function(e){return a(e,"length")&&a(e,"callee")},c=function(e){return a(e,"promise")&&typeof (e!=null?e.promise:void 0)=="function"},u=function(e){return l(e)?u(Array.prototype.slice.call(e)):Array.isArray(e)?e.reduce(function(e,t){return Array.isArray(t)?e.concat(u(t)):(e.push(t),e)},[]):[e]},s=function(e,t){return e<=0?t():function(){if(--e<1)return t.apply(this,arguments)}},h=function(e,t){return function(){var n;return n=[e].concat(Array.prototype.slice.call(arguments,0)),t.apply(this,n)}},o=function(e,t,n){var r,i,s,o,a;o=u(e),a=[];for(i=0,s=o.length;i<s;i++)r=o[i],a.push(r.call.apply(r,[n].concat(d.call(t))));return a},e=function(){var i,s,a,f,l,h,p;return p=t,f=[],l=[],h=[],a={resolved:{},rejected:{},pending:{}},this.promise=function(i){var s,v;return i=i||{},i.state=function(){return p},v=function(e,n,r){return function(){return p===t&&n.push.apply(n,u(arguments)),e()&&o(arguments,a[r]),i}},i.done=v(function(){return p===r},f,r),i.fail=v(function(){return p===n},l,n),i.progress=v(function(){return p!==t},h,t),i.always=function(){var e;return(e=i.done.apply(i,arguments)).fail.apply(e,arguments)},s=function(t,n,r){var s,o;return o=new e,s=function(e,t,n){return n?i[e](function(){var e,r;return e=1<=arguments.length?d.call(arguments,0):[],r=n.apply(null,e),c(r)?r.done(o.resolve).fail(o.reject).progress(o.notify):o[t](r)}):i[e](o[t])},s("done","resolve",t),s("fail","reject",n),s("progress","notify",r),o},i.pipe=s,i.then=s,i.promise==null&&(i.promise=function(){return i}),i},this.promise(this),i=this,s=function(e,n,r){return function(){return p===t?(p=e,a[e]=arguments,o(n,a[e],r),i):this}},this.resolve=s(r,f),this.reject=s(n,l),this.notify=s(t,h),this.resolveWith=function(e,t){return s(r,f,e).apply(null,t)},this.rejectWith=function(e,t){return s(n,l,e).apply(null,t)},this.notifyWith=function(e,n){return s(t,h,e).apply(null,n)},this},p=function(){var t,n,r,i,o,a,f;n=u(arguments);if(n.length===1)return c(n[0])?n[0]:(new e).resolve(n[0]).promise();o=new e;if(!n.length)return o.resolve().promise();i=[],r=s(n.length,function(){return o.resolve.apply(o,i)}),n.forEach(function(e,t){return c(e)?e.done(function(){var e;return e=1<=arguments.length?d.call(arguments,0):[],i[t]=e.length>1?e:e[0],r()}):(i[t]=e,r())});for(a=0,f=n.length;a<f;a++)t=n[a],c(t)&&t.fail(o.reject);return o.promise()},f=function(t){return t.Deferred=function(){return new e},t.ajax=h(t.ajax,function(t,n){var r,i,s,o;return n==null&&(n={}),i=new e,r=function(e,t){return h(e,function(){var e,n;return n=arguments[0],e=2<=arguments.length?d.call(arguments,1):[],n&&n.apply(null,e),t.apply(null,e)})},n.success=r(n.success,i.resolve),n.error=r(n.error,i.reject),o=t(n),s=i.promise(),s.abort=function(){return o.abort()},s}),t.when=p},typeof exports!="undefined"?(exports.Deferred=function(){return new e},exports.when=p,exports.installInto=f):typeof define=="function"&&define.amd?define([],function(){return typeof Zepto!="undefined"?f(Zepto):(e.when=p,e.installInto=f,e)}):typeof Zepto!="undefined"?f(Zepto):(this.Deferred=function(){return new e},this.Deferred.when=p,this.Deferred.installInto=f)}).call(this);