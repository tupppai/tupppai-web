/*!
 * jQuery JavaScript Library v1.9.0
 * http://jquery.com/
 *
 * Includes Sizzle.js
 * http://sizzlejs.com/
 *
 * Copyright 2005, 2012 jQuery Foundation, Inc. and other contributors
 * Released under the MIT license
 * http://jquery.org/license
 *
 * Date: 2013-1-14
 */
(function (window, undefined) {
    "use strict";
    var
    // A central reference to the root jQuery(document)
        rootjQuery,

    // The deferred used on DOM ready
        readyList,

    // Use the correct document accordingly with window argument (sandbox)
        document = window.document,
        location = window.location,

    // Map over jQuery in case of overwrite
        _jQuery = window.jQuery,

    // Map over the $ in case of overwrite
        _$ = window.$,

    // [[Class]] -> type pairs
        class2type = {},

    // List of deleted data cache ids, so we can reuse them
        core_deletedIds = [],

        core_version = "1.9.0",

    // Save a reference to some core methods
        core_concat = core_deletedIds.concat,
        core_push = core_deletedIds.push,
        core_slice = core_deletedIds.slice,
        core_indexOf = core_deletedIds.indexOf,
        core_toString = class2type.toString,
        core_hasOwn = class2type.hasOwnProperty,
        core_trim = core_version.trim,

    // Define a local copy of jQuery
        jQuery = function (selector, context) {
            // The jQuery object is actually just the init constructor 'enhanced'
            return new jQuery.fn.init(selector, context, rootjQuery);
        },

    // Used for matching numbers
        core_pnum = /[+-]?(?:\d*\.|)\d+(?:[eE][+-]?\d+|)/.source,

    // Used for splitting on whitespace
        core_rnotwhite = /\S+/g,

    // Make sure we trim BOM and NBSP (here's looking at you, Safari 5.0 and IE)
        rtrim = /^[\s\uFEFF\xA0]+|[\s\uFEFF\xA0]+$/g,

    // A simple way to check for HTML strings
    // Prioritize #id over <tag> to avoid XSS via location.hash (#9521)
    // Strict HTML recognition (#11290: must start with <)
        rquickExpr = /^(?:(<[\w\W]+>)[^>]*|#([\w-]*))$/,

    // Match a standalone tag
        rsingleTag = /^<(\w+)\s*\/?>(?:<\/\1>|)$/,

    // JSON RegExp
        rvalidchars = /^[\],:{}\s]*$/,
        rvalidbraces = /(?:^|:|,)(?:\s*\[)+/g,
        rvalidescape = /\\(?:["\\\/bfnrt]|u[\da-fA-F]{4})/g,
        rvalidtokens = /"[^"\\\r\n]*"|true|false|null|-?(?:\d+\.|)\d+(?:[eE][+-]?\d+|)/g,

    // Matches dashed string for camelizing
        rmsPrefix = /^-ms-/,
        rdashAlpha = /-([\da-z])/gi,

    // Used by jQuery.camelCase as callback to replace()
        fcamelCase = function (all, letter) {
            return letter.toUpperCase();
        },

    // The ready event handler and self cleanup method
        DOMContentLoaded = function () {
            if (document.addEventListener) {
                document.removeEventListener("DOMContentLoaded", DOMContentLoaded, false);
                jQuery.ready();
            } else if (document.readyState === "complete") {
                // we're here because readyState === "complete" in oldIE
                // which is good enough for us to call the dom ready!
                document.detachEvent("onreadystatechange", DOMContentLoaded);
                jQuery.ready();
            }
        };

    jQuery.fn = jQuery.prototype = {
        // The current version of jQuery being used
        jquery: core_version,

        constructor: jQuery,
        init: function (selector, context, rootjQuery) {
            var match, elem;

            // HANDLE: $(""), $(null), $(undefined), $(false)
            if (!selector) {
                return this;
            }

            // Handle HTML strings
            if (typeof selector === "string") {
                if (selector.charAt(0) === "<" && selector.charAt(selector.length - 1) === ">" && selector.length >= 3) {
                    // Assume that strings that start and end with <> are HTML and skip the regex check
                    match = [ null, selector, null ];

                } else {
                    match = rquickExpr.exec(selector);
                }

                // Match html or make sure no context is specified for #id
                if (match && (match[1] || !context)) {

                    // HANDLE: $(html) -> $(array)
                    if (match[1]) {
                        context = context instanceof jQuery ? context[0] : context;

                        // scripts is true for back-compat
                        jQuery.merge(this, jQuery.parseHTML(
                            match[1],
                            context && context.nodeType ? context.ownerDocument || context : document,
                            true
                        ));

                        // HANDLE: $(html, props)
                        if (rsingleTag.test(match[1]) && jQuery.isPlainObject(context)) {
                            for (match in context) {
                                // Properties of context are called as methods if possible
                                if (jQuery.isFunction(this[ match ])) {
                                    this[ match ](context[ match ]);

                                    // ...and otherwise set as attributes
                                } else {
                                    this.attr(match, context[ match ]);
                                }
                            }
                        }

                        return this;

                        // HANDLE: $(#id)
                    } else {
                        elem = document.getElementById(match[2]);

                        // Check parentNode to catch when Blackberry 4.6 returns
                        // nodes that are no longer in the document #6963
                        if (elem && elem.parentNode) {
                            // Handle the case where IE and Opera return items
                            // by name instead of ID
                            if (elem.id !== match[2]) {
                                return rootjQuery.find(selector);
                            }

                            // Otherwise, we inject the element directly into the jQuery object
                            this.length = 1;
                            this[0] = elem;
                        }

                        this.context = document;
                        this.selector = selector;
                        return this;
                    }

                    // HANDLE: $(expr, $(...))
                } else if (!context || context.jquery) {
                    return ( context || rootjQuery ).find(selector);

                    // HANDLE: $(expr, context)
                    // (which is just equivalent to: $(context).find(expr)
                } else {
                    return this.constructor(context).find(selector);
                }

                // HANDLE: $(DOMElement)
            } else if (selector.nodeType) {
                this.context = this[0] = selector;
                this.length = 1;
                return this;

                // HANDLE: $(function)
                // Shortcut for document ready
            } else if (jQuery.isFunction(selector)) {
                return rootjQuery.ready(selector);
            }

            if (selector.selector !== undefined) {
                this.selector = selector.selector;
                this.context = selector.context;
            }

            return jQuery.makeArray(selector, this);
        },

        // Start with an empty selector
        selector: "",

        // The default length of a jQuery object is 0
        length: 0,

        // The number of elements contained in the matched element set
        size: function () {
            return this.length;
        },

        toArray: function () {
            return core_slice.call(this);
        },

        // Get the Nth element in the matched element set OR
        // Get the whole matched element set as a clean array
        get: function (num) {
            return num == null ?

                // Return a 'clean' array
                this.toArray() :

                // Return just the object
                ( num < 0 ? this[ this.length + num ] : this[ num ] );
        },

        // Take an array of elements and push it onto the stack
        // (returning the new matched element set)
        pushStack: function (elems) {

            // Build a new jQuery matched element set
            var ret = jQuery.merge(this.constructor(), elems);

            // Add the old object onto the stack (as a reference)
            ret.prevObject = this;
            ret.context = this.context;

            // Return the newly-formed element set
            return ret;
        },

        // Execute a callback for every element in the matched set.
        // (You can seed the arguments with an array of args, but this is
        // only used internally.)
        each: function (callback, args) {
            return jQuery.each(this, callback, args);
        },

        ready: function (fn) {
            // Add the callback
            jQuery.ready.promise().done(fn);

            return this;
        },

        slice: function () {
            return this.pushStack(core_slice.apply(this, arguments));
        },

        first: function () {
            return this.eq(0);
        },

        last: function () {
            return this.eq(-1);
        },

        eq: function (i) {
            var len = this.length,
                j = +i + ( i < 0 ? len : 0 );
            return this.pushStack(j >= 0 && j < len ? [ this[j] ] : []);
        },

        map: function (callback) {
            return this.pushStack(jQuery.map(this, function (elem, i) {
                return callback.call(elem, i, elem);
            }));
        },

        end: function () {
            return this.prevObject || this.constructor(null);
        },

        // For internal use only.
        // Behaves like an Array's method, not like a jQuery method.
        push: core_push,
        sort: [].sort,
        splice: [].splice
    };

// Give the init function the jQuery prototype for later instantiation
    jQuery.fn.init.prototype = jQuery.fn;

    jQuery.extend = jQuery.fn.extend = function () {
        var options, name, src, copy, copyIsArray, clone,
            target = arguments[0] || {},
            i = 1,
            length = arguments.length,
            deep = false;

        // Handle a deep copy situation
        if (typeof target === "boolean") {
            deep = target;
            target = arguments[1] || {};
            // skip the boolean and the target
            i = 2;
        }

        // Handle case when target is a string or something (possible in deep copy)
        if (typeof target !== "object" && !jQuery.isFunction(target)) {
            target = {};
        }

        // extend jQuery itself if only one argument is passed
        if (length === i) {
            target = this;
            --i;
        }

        for (; i < length; i++) {
            // Only deal with non-null/undefined values
            if ((options = arguments[ i ]) != null) {
                // Extend the base object
                for (name in options) {
                    src = target[ name ];
                    copy = options[ name ];

                    // Prevent never-ending loop
                    if (target === copy) {
                        continue;
                    }

                    // Recurse if we're merging plain objects or arrays
                    if (deep && copy && ( jQuery.isPlainObject(copy) || (copyIsArray = jQuery.isArray(copy)) )) {
                        if (copyIsArray) {
                            copyIsArray = false;
                            clone = src && jQuery.isArray(src) ? src : [];

                        } else {
                            clone = src && jQuery.isPlainObject(src) ? src : {};
                        }

                        // Never move original objects, clone them
                        target[ name ] = jQuery.extend(deep, clone, copy);

                        // Don't bring in undefined values
                    } else if (copy !== undefined) {
                        target[ name ] = copy;
                    }
                }
            }
        }

        // Return the modified object
        return target;
    };

    jQuery.extend({
        noConflict: function (deep) {
            if (window.$ === jQuery) {
                window.$ = _$;
            }

            if (deep && window.jQuery === jQuery) {
                window.jQuery = _jQuery;
            }

            return jQuery;
        },

        // Is the DOM ready to be used? Set to true once it occurs.
        isReady: false,

        // A counter to track how many items to wait for before
        // the ready event fires. See #6781
        readyWait: 1,

        // Hold (or release) the ready event
        holdReady: function (hold) {
            if (hold) {
                jQuery.readyWait++;
            } else {
                jQuery.ready(true);
            }
        },

        // Handle when the DOM is ready
        ready: function (wait) {

            // Abort if there are pending holds or we're already ready
            if (wait === true ? --jQuery.readyWait : jQuery.isReady) {
                return;
            }

            // Make sure body exists, at least, in case IE gets a little overzealous (ticket #5443).
            if (!document.body) {
                return setTimeout(jQuery.ready);
            }

            // Remember that the DOM is ready
            jQuery.isReady = true;

            // If a normal DOM Ready event fired, decrement, and wait if need be
            if (wait !== true && --jQuery.readyWait > 0) {
                return;
            }

            // If there are functions bound, to execute
            readyList.resolveWith(document, [ jQuery ]);

            // Trigger any bound ready events
            if (jQuery.fn.trigger) {
                jQuery(document).trigger("ready").off("ready");
            }
        },

        // See test/unit/core.js for details concerning isFunction.
        // Since version 1.3, DOM methods and functions like alert
        // aren't supported. They return false on IE (#2968).
        isFunction: function (obj) {
            return jQuery.type(obj) === "function";
        },

        isArray: Array.isArray || function (obj) {
            return jQuery.type(obj) === "array";
        },

        isWindow: function (obj) {
            return obj != null && obj == obj.window;
        },

        isNumeric: function (obj) {
            return !isNaN(parseFloat(obj)) && isFinite(obj);
        },

        type: function (obj) {
            if (obj == null) {
                return String(obj);
            }
            return typeof obj === "object" || typeof obj === "function" ?
                class2type[ core_toString.call(obj) ] || "object" :
                typeof obj;
        },

        isPlainObject: function (obj) {
            // Must be an Object.
            // Because of IE, we also have to check the presence of the constructor property.
            // Make sure that DOM nodes and window objects don't pass through, as well
            if (!obj || jQuery.type(obj) !== "object" || obj.nodeType || jQuery.isWindow(obj)) {
                return false;
            }

            try {
                // Not own constructor property must be Object
                if (obj.constructor && !core_hasOwn.call(obj, "constructor") && !core_hasOwn.call(obj.constructor.prototype, "isPrototypeOf")) {
                    return false;
                }
            } catch (e) {
                // IE8,9 Will throw exceptions on certain host objects #9897
                return false;
            }

            // Own properties are enumerated firstly, so to speed up,
            // if last one is own, then all properties are own.

            var key;
            for (key in obj) {
            }

            return key === undefined || core_hasOwn.call(obj, key);
        },

        isEmptyObject: function (obj) {
            var name;
            for (name in obj) {
                return false;
            }
            return true;
        },

        error: function (msg) {
            throw new Error(msg);
        },

        // data: string of html
        // context (optional): If specified, the fragment will be created in this context, defaults to document
        // keepScripts (optional): If true, will include scripts passed in the html string
        parseHTML: function (data, context, keepScripts) {
            if (!data || typeof data !== "string") {
                return null;
            }
            if (typeof context === "boolean") {
                keepScripts = context;
                context = false;
            }
            context = context || document;

            var parsed = rsingleTag.exec(data),
                scripts = !keepScripts && [];

            // Single tag
            if (parsed) {
                return [ context.createElement(parsed[1]) ];
            }

            parsed = jQuery.buildFragment([ data ], context, scripts);
            if (scripts) {
                jQuery(scripts).remove();
            }
            return jQuery.merge([], parsed.childNodes);
        },

        parseJSON: function (data) {
            // Attempt to parse using the native JSON parser first
            if (window.JSON && window.JSON.parse) {
                return window.JSON.parse(data);
            }

            if (data === null) {
                return data;
            }

            if (typeof data === "string") {

                // Make sure leading/trailing whitespace is removed (IE can't handle it)
                data = jQuery.trim(data);

                if (data) {
                    // Make sure the incoming data is actual JSON
                    // Logic borrowed from http://json.org/json2.js
                    if (rvalidchars.test(data.replace(rvalidescape, "@")
                        .replace(rvalidtokens, "]")
                        .replace(rvalidbraces, ""))) {

                        return ( new Function("return " + data) )();
                    }
                }
            }

            jQuery.error("Invalid JSON: " + data);
        },

        // Cross-browser xml parsing
        parseXML: function (data) {
            var xml, tmp;
            if (!data || typeof data !== "string") {
                return null;
            }
            try {
                if (window.DOMParser) { // Standard
                    tmp = new DOMParser();
                    xml = tmp.parseFromString(data, "text/xml");
                } else { // IE
                    xml = new ActiveXObject("Microsoft.XMLDOM");
                    xml.async = "false";
                    xml.loadXML(data);
                }
            } catch (e) {
                xml = undefined;
            }
            if (!xml || !xml.documentElement || xml.getElementsByTagName("parsererror").length) {
                jQuery.error("Invalid XML: " + data);
            }
            return xml;
        },

        noop: function () {
        },

        // Evaluates a script in a global context
        // Workarounds based on findings by Jim Driscoll
        // http://weblogs.java.net/blog/driscoll/archive/2009/09/08/eval-javascript-global-context
        globalEval: function (data) {
            if (data && jQuery.trim(data)) {
                // We use execScript on Internet Explorer
                // We use an anonymous function so that context is window
                // rather than jQuery in Firefox
                ( window.execScript || function (data) {
                    window[ "eval" ].call(window, data);
                } )(data);
            }
        },

        // Convert dashed to camelCase; used by the css and data modules
        // Microsoft forgot to hump their vendor prefix (#9572)
        camelCase: function (string) {
            return string.replace(rmsPrefix, "ms-").replace(rdashAlpha, fcamelCase);
        },

        nodeName: function (elem, name) {
            return elem.nodeName && elem.nodeName.toLowerCase() === name.toLowerCase();
        },

        // args is for internal usage only
        each: function (obj, callback, args) {
            var value,
                i = 0,
                length = obj.length,
                isArray = isArraylike(obj);

            if (args) {
                if (isArray) {
                    for (; i < length; i++) {
                        value = callback.apply(obj[ i ], args);

                        if (value === false) {
                            break;
                        }
                    }
                } else {
                    for (i in obj) {
                        value = callback.apply(obj[ i ], args);

                        if (value === false) {
                            break;
                        }
                    }
                }

                // A special, fast, case for the most common use of each
            } else {
                if (isArray) {
                    for (; i < length; i++) {
                        value = callback.call(obj[ i ], i, obj[ i ]);

                        if (value === false) {
                            break;
                        }
                    }
                } else {
                    for (i in obj) {
                        value = callback.call(obj[ i ], i, obj[ i ]);

                        if (value === false) {
                            break;
                        }
                    }
                }
            }

            return obj;
        },

        // Use native String.trim function wherever possible
        trim: core_trim && !core_trim.call("\uFEFF\xA0") ?
            function (text) {
                return text == null ?
                    "" :
                    core_trim.call(text);
            } :

            // Otherwise use our own trimming functionality
            function (text) {
                return text == null ?
                    "" :
                    ( text + "" ).replace(rtrim, "");
            },

        // results is for internal usage only
        makeArray: function (arr, results) {
            var ret = results || [];

            if (arr != null) {
                if (isArraylike(Object(arr))) {
                    jQuery.merge(ret,
                        typeof arr === "string" ?
                            [ arr ] : arr
                    );
                } else {
                    core_push.call(ret, arr);
                }
            }

            return ret;
        },

        inArray: function (elem, arr, i) {
            var len;

            if (arr) {
                if (core_indexOf) {
                    return core_indexOf.call(arr, elem, i);
                }

                len = arr.length;
                i = i ? i < 0 ? Math.max(0, len + i) : i : 0;

                for (; i < len; i++) {
                    // Skip accessing in sparse arrays
                    if (i in arr && arr[ i ] === elem) {
                        return i;
                    }
                }
            }

            return -1;
        },

        merge: function (first, second) {
            var l = second.length,
                i = first.length,
                j = 0;

            if (typeof l === "number") {
                for (; j < l; j++) {
                    first[ i++ ] = second[ j ];
                }
            } else {
                while (second[j] !== undefined) {
                    first[ i++ ] = second[ j++ ];
                }
            }

            first.length = i;

            return first;
        },

        grep: function (elems, callback, inv) {
            var retVal,
                ret = [],
                i = 0,
                length = elems.length;
            inv = !!inv;

            // Go through the array, only saving the items
            // that pass the validator function
            for (; i < length; i++) {
                retVal = !!callback(elems[ i ], i);
                if (inv !== retVal) {
                    ret.push(elems[ i ]);
                }
            }

            return ret;
        },

        // arg is for internal usage only
        map: function (elems, callback, arg) {
            var value,
                i = 0,
                length = elems.length,
                isArray = isArraylike(elems),
                ret = [];

            // Go through the array, translating each of the items to their
            if (isArray) {
                for (; i < length; i++) {
                    value = callback(elems[ i ], i, arg);

                    if (value != null) {
                        ret[ ret.length ] = value;
                    }
                }

                // Go through every key on the object,
            } else {
                for (i in elems) {
                    value = callback(elems[ i ], i, arg);

                    if (value != null) {
                        ret[ ret.length ] = value;
                    }
                }
            }

            // Flatten any nested arrays
            return core_concat.apply([], ret);
        },

        // A global GUID counter for objects
        guid: 1,

        // Bind a function to a context, optionally partially applying any
        // arguments.
        proxy: function (fn, context) {
            var tmp, args, proxy;

            if (typeof context === "string") {
                tmp = fn[ context ];
                context = fn;
                fn = tmp;
            }

            // Quick check to determine if target is callable, in the spec
            // this throws a TypeError, but we will just return undefined.
            if (!jQuery.isFunction(fn)) {
                return undefined;
            }

            // Simulated bind
            args = core_slice.call(arguments, 2);
            proxy = function () {
                return fn.apply(context || this, args.concat(core_slice.call(arguments)));
            };

            // Set the guid of unique handler to the same of original handler, so it can be removed
            proxy.guid = fn.guid = fn.guid || jQuery.guid++;

            return proxy;
        },

        // Multifunctional method to get and set values of a collection
        // The value/s can optionally be executed if it's a function
        access: function (elems, fn, key, value, chainable, emptyGet, raw) {
            var i = 0,
                length = elems.length,
                bulk = key == null;

            // Sets many values
            if (jQuery.type(key) === "object") {
                chainable = true;
                for (i in key) {
                    jQuery.access(elems, fn, i, key[i], true, emptyGet, raw);
                }

                // Sets one value
            } else if (value !== undefined) {
                chainable = true;

                if (!jQuery.isFunction(value)) {
                    raw = true;
                }

                if (bulk) {
                    // Bulk operations run against the entire set
                    if (raw) {
                        fn.call(elems, value);
                        fn = null;

                        // ...except when executing function values
                    } else {
                        bulk = fn;
                        fn = function (elem, key, value) {
                            return bulk.call(jQuery(elem), value);
                        };
                    }
                }

                if (fn) {
                    for (; i < length; i++) {
                        fn(elems[i], key, raw ? value : value.call(elems[i], i, fn(elems[i], key)));
                    }
                }
            }

            return chainable ?
                elems :

                // Gets
                bulk ?
                    fn.call(elems) :
                    length ? fn(elems[0], key) : emptyGet;
        },

        now: function () {
            return ( new Date() ).getTime();
        }
    });

    jQuery.ready.promise = function (obj) {
        if (!readyList) {

            readyList = jQuery.Deferred();

            // Catch cases where $(document).ready() is called after the browser event has already occurred.
            // we once tried to use readyState "interactive" here, but it caused issues like the one
            // discovered by ChrisS here: http://bugs.jquery.com/ticket/12282#comment:15
            if (document.readyState === "complete") {
                // Handle it asynchronously to allow scripts the opportunity to delay ready
                setTimeout(jQuery.ready);

                // Standards-based browsers support DOMContentLoaded
            } else if (document.addEventListener) {
                // Use the handy event callback
                document.addEventListener("DOMContentLoaded", DOMContentLoaded, false);

                // A fallback to window.onload, that will always work
                window.addEventListener("load", jQuery.ready, false);

                // If IE event model is used
            } else {
                // Ensure firing before onload, maybe late but safe also for iframes
                document.attachEvent("onreadystatechange", DOMContentLoaded);

                // A fallback to window.onload, that will always work
                window.attachEvent("onload", jQuery.ready);

                // If IE and not a frame
                // continually check to see if the document is ready
                var top = false;

                try {
                    top = window.frameElement == null && document.documentElement;
                } catch (e) {
                }

                if (top && top.doScroll) {
                    (function doScrollCheck() {
                        if (!jQuery.isReady) {

                            try {
                                // Use the trick by Diego Perini
                                // http://javascript.nwbox.com/IEContentLoaded/
                                top.doScroll("left");
                            } catch (e) {
                                return setTimeout(doScrollCheck, 50);
                            }

                            // and execute any waiting functions
                            jQuery.ready();
                        }
                    })();
                }
            }
        }
        return readyList.promise(obj);
    };

// Populate the class2type map
    jQuery.each("Boolean Number String Function Array Date RegExp Object Error".split(" "), function (i, name) {
        class2type[ "[object " + name + "]" ] = name.toLowerCase();
    });

    function isArraylike(obj) {
        var length = obj.length,
            type = jQuery.type(obj);

        if (jQuery.isWindow(obj)) {
            return false;
        }

        if (obj.nodeType === 1 && length) {
            return true;
        }

        return type === "array" || type !== "function" &&
            ( length === 0 ||
                typeof length === "number" && length > 0 && ( length - 1 ) in obj );
    }

// All jQuery objects should point back to these
    rootjQuery = jQuery(document);
// String to Object options format cache
    var optionsCache = {};

// Convert String-formatted options into Object-formatted ones and store in cache
    function createOptions(options) {
        var object = optionsCache[ options ] = {};
        jQuery.each(options.match(core_rnotwhite) || [], function (_, flag) {
            object[ flag ] = true;
        });
        return object;
    }

    /*
     * Create a callback list using the following parameters:
     *
     *	options: an optional list of space-separated options that will change how
     *			the callback list behaves or a more traditional option object
     *
     * By default a callback list will act like an event callback list and can be
     * "fired" multiple times.
     *
     * Possible options:
     *
     *	once:			will ensure the callback list can only be fired once (like a Deferred)
     *
     *	memory:			will keep track of previous values and will call any callback added
     *					after the list has been fired right away with the latest "memorized"
     *					values (like a Deferred)
     *
     *	unique:			will ensure a callback can only be added once (no duplicate in the list)
     *
     *	stopOnFalse:	interrupt callings when a callback returns false
     *
     */
    jQuery.Callbacks = function (options) {

        // Convert options from String-formatted to Object-formatted if needed
        // (we check in cache first)
        options = typeof options === "string" ?
            ( optionsCache[ options ] || createOptions(options) ) :
            jQuery.extend({}, options);

        var // Last fire value (for non-forgettable lists)
            memory,
        // Flag to know if list was already fired
            fired,
        // Flag to know if list is currently firing
            firing,
        // First callback to fire (used internally by add and fireWith)
            firingStart,
        // End of the loop when firing
            firingLength,
        // Index of currently firing callback (modified by remove if needed)
            firingIndex,
        // Actual callback list
            list = [],
        // Stack of fire calls for repeatable lists
            stack = !options.once && [],
        // Fire callbacks
            fire = function (data) {
                memory = options.memory && data;
                fired = true;
                firingIndex = firingStart || 0;
                firingStart = 0;
                firingLength = list.length;
                firing = true;
                for (; list && firingIndex < firingLength; firingIndex++) {
                    if (list[ firingIndex ].apply(data[ 0 ], data[ 1 ]) === false && options.stopOnFalse) {
                        memory = false; // To prevent further calls using add
                        break;
                    }
                }
                firing = false;
                if (list) {
                    if (stack) {
                        if (stack.length) {
                            fire(stack.shift());
                        }
                    } else if (memory) {
                        list = [];
                    } else {
                        self.disable();
                    }
                }
            },
        // Actual Callbacks object
            self = {
                // Add a callback or a collection of callbacks to the list
                add: function () {
                    if (list) {
                        // First, we save the current length
                        var start = list.length;
                        (function add(args) {
                            jQuery.each(args, function (_, arg) {
                                var type = jQuery.type(arg);
                                if (type === "function") {
                                    if (!options.unique || !self.has(arg)) {
                                        list.push(arg);
                                    }
                                } else if (arg && arg.length && type !== "string") {
                                    // Inspect recursively
                                    add(arg);
                                }
                            });
                        })(arguments);
                        // Do we need to add the callbacks to the
                        // current firing batch?
                        if (firing) {
                            firingLength = list.length;
                            // With memory, if we're not firing then
                            // we should call right away
                        } else if (memory) {
                            firingStart = start;
                            fire(memory);
                        }
                    }
                    return this;
                },
                // Remove a callback from the list
                remove: function () {
                    if (list) {
                        jQuery.each(arguments, function (_, arg) {
                            var index;
                            while (( index = jQuery.inArray(arg, list, index) ) > -1) {
                                list.splice(index, 1);
                                // Handle firing indexes
                                if (firing) {
                                    if (index <= firingLength) {
                                        firingLength--;
                                    }
                                    if (index <= firingIndex) {
                                        firingIndex--;
                                    }
                                }
                            }
                        });
                    }
                    return this;
                },
                // Control if a given callback is in the list
                has: function (fn) {
                    return jQuery.inArray(fn, list) > -1;
                },
                // Remove all callbacks from the list
                empty: function () {
                    list = [];
                    return this;
                },
                // Have the list do nothing anymore
                disable: function () {
                    list = stack = memory = undefined;
                    return this;
                },
                // Is it disabled?
                disabled: function () {
                    return !list;
                },
                // Lock the list in its current state
                lock: function () {
                    stack = undefined;
                    if (!memory) {
                        self.disable();
                    }
                    return this;
                },
                // Is it locked?
                locked: function () {
                    return !stack;
                },
                // Call all callbacks with the given context and arguments
                fireWith: function (context, args) {
                    args = args || [];
                    args = [ context, args.slice ? args.slice() : args ];
                    if (list && ( !fired || stack )) {
                        if (firing) {
                            stack.push(args);
                        } else {
                            fire(args);
                        }
                    }
                    return this;
                },
                // Call all the callbacks with the given arguments
                fire: function () {
                    self.fireWith(this, arguments);
                    return this;
                },
                // To know if the callbacks have already been called at least once
                fired: function () {
                    return !!fired;
                }
            };

        return self;
    };
    jQuery.extend({

        Deferred: function (func) {
            var tuples = [
                    // action, add listener, listener list, final state
                    [ "resolve", "done", jQuery.Callbacks("once memory"), "resolved" ],
                    [ "reject", "fail", jQuery.Callbacks("once memory"), "rejected" ],
                    [ "notify", "progress", jQuery.Callbacks("memory") ]
                ],
                state = "pending",
                promise = {
                    state: function () {
                        return state;
                    },
                    always: function () {
                        deferred.done(arguments).fail(arguments);
                        return this;
                    },
                    then: function (/* fnDone, fnFail, fnProgress */) {
                        var fns = arguments;
                        return jQuery.Deferred(function (newDefer) {
                            jQuery.each(tuples, function (i, tuple) {
                                var action = tuple[ 0 ],
                                    fn = jQuery.isFunction(fns[ i ]) && fns[ i ];
                                // deferred[ done | fail | progress ] for forwarding actions to newDefer
                                deferred[ tuple[1] ](function () {
                                    var returned = fn && fn.apply(this, arguments);
                                    if (returned && jQuery.isFunction(returned.promise)) {
                                        returned.promise()
                                            .done(newDefer.resolve)
                                            .fail(newDefer.reject)
                                            .progress(newDefer.notify);
                                    } else {
                                        newDefer[ action + "With" ](this === promise ? newDefer.promise() : this, fn ? [ returned ] : arguments);
                                    }
                                });
                            });
                            fns = null;
                        }).promise();
                    },
                    // Get a promise for this deferred
                    // If obj is provided, the promise aspect is added to the object
                    promise: function (obj) {
                        return obj != null ? jQuery.extend(obj, promise) : promise;
                    }
                },
                deferred = {};

            // Keep pipe for back-compat
            promise.pipe = promise.then;

            // Add list-specific methods
            jQuery.each(tuples, function (i, tuple) {
                var list = tuple[ 2 ],
                    stateString = tuple[ 3 ];

                // promise[ done | fail | progress ] = list.add
                promise[ tuple[1] ] = list.add;

                // Handle state
                if (stateString) {
                    list.add(function () {
                        // state = [ resolved | rejected ]
                        state = stateString;

                        // [ reject_list | resolve_list ].disable; progress_list.lock
                    }, tuples[ i ^ 1 ][ 2 ].disable, tuples[ 2 ][ 2 ].lock);
                }

                // deferred[ resolve | reject | notify ]
                deferred[ tuple[0] ] = function () {
                    deferred[ tuple[0] + "With" ](this === deferred ? promise : this, arguments);
                    return this;
                };
                deferred[ tuple[0] + "With" ] = list.fireWith;
            });

            // Make the deferred a promise
            promise.promise(deferred);

            // Call given func if any
            if (func) {
                func.call(deferred, deferred);
            }

            // All done!
            return deferred;
        },

        // Deferred helper
        when: function (subordinate /* , ..., subordinateN */) {
            var i = 0,
                resolveValues = core_slice.call(arguments),
                length = resolveValues.length,

            // the count of uncompleted subordinates
                remaining = length !== 1 || ( subordinate && jQuery.isFunction(subordinate.promise) ) ? length : 0,

            // the master Deferred. If resolveValues consist of only a single Deferred, just use that.
                deferred = remaining === 1 ? subordinate : jQuery.Deferred(),

            // Update function for both resolve and progress values
                updateFunc = function (i, contexts, values) {
                    return function (value) {
                        contexts[ i ] = this;
                        values[ i ] = arguments.length > 1 ? core_slice.call(arguments) : value;
                        if (values === progressValues) {
                            deferred.notifyWith(contexts, values);
                        } else if (!( --remaining )) {
                            deferred.resolveWith(contexts, values);
                        }
                    };
                },

                progressValues, progressContexts, resolveContexts;

            // add listeners to Deferred subordinates; treat others as resolved
            if (length > 1) {
                progressValues = new Array(length);
                progressContexts = new Array(length);
                resolveContexts = new Array(length);
                for (; i < length; i++) {
                    if (resolveValues[ i ] && jQuery.isFunction(resolveValues[ i ].promise)) {
                        resolveValues[ i ].promise()
                            .done(updateFunc(i, resolveContexts, resolveValues))
                            .fail(deferred.reject)
                            .progress(updateFunc(i, progressContexts, progressValues));
                    } else {
                        --remaining;
                    }
                }
            }

            // if we're not waiting on anything, resolve the master
            if (!remaining) {
                deferred.resolveWith(resolveContexts, resolveValues);
            }

            return deferred.promise();
        }
    });
    jQuery.support = (function () {

        var support, all, a, select, opt, input, fragment, eventName, isSupported, i,
            div = document.createElement("div");

        // Setup
        div.setAttribute("className", "t");
        div.innerHTML = "  <link/><table></table><a href='/a'>a</a><input type='checkbox'/>";

        // Support tests won't run in some limited or non-browser environments
        all = div.getElementsByTagName("*");
        a = div.getElementsByTagName("a")[ 0 ];
        if (!all || !a || !all.length) {
            return {};
        }

        // First batch of tests
        select = document.createElement("select");
        opt = select.appendChild(document.createElement("option"));
        input = div.getElementsByTagName("input")[ 0 ];

        a.style.cssText = "top:1px;float:left;opacity:.5";
        support = {
            // Test setAttribute on camelCase class. If it works, we need attrFixes when doing get/setAttribute (ie6/7)
            getSetAttribute: div.className !== "t",

            // IE strips leading whitespace when .innerHTML is used
            leadingWhitespace: div.firstChild.nodeType === 3,

            // Make sure that tbody elements aren't automatically inserted
            // IE will insert them into empty tables
            tbody: !div.getElementsByTagName("tbody").length,

            // Make sure that link elements get serialized correctly by innerHTML
            // This requires a wrapper element in IE
            htmlSerialize: !!div.getElementsByTagName("link").length,

            // Get the style information from getAttribute
            // (IE uses .cssText instead)
            style: /top/.test(a.getAttribute("style")),

            // Make sure that URLs aren't manipulated
            // (IE normalizes it by default)
            hrefNormalized: a.getAttribute("href") === "/a",

            // Make sure that element opacity exists
            // (IE uses filter instead)
            // Use a regex to work around a WebKit issue. See #5145
            opacity: /^0.5/.test(a.style.opacity),

            // Verify style float existence
            // (IE uses styleFloat instead of cssFloat)
            cssFloat: !!a.style.cssFloat,

            // Check the default checkbox/radio value ("" on WebKit; "on" elsewhere)
            checkOn: !!input.value,

            // Make sure that a selected-by-default option has a working selected property.
            // (WebKit defaults to false instead of true, IE too, if it's in an optgroup)
            optSelected: opt.selected,

            // Tests for enctype support on a form (#6743)
            enctype: !!document.createElement("form").enctype,

            // Makes sure cloning an html5 element does not cause problems
            // Where outerHTML is undefined, this still works
            html5Clone: document.createElement("nav").cloneNode(true).outerHTML !== "<:nav></:nav>",

            // jQuery.support.boxModel DEPRECATED in 1.8 since we don't support Quirks Mode
            boxModel: document.compatMode === "CSS1Compat",

            // Will be defined later
            deleteExpando: true,
            noCloneEvent: true,
            inlineBlockNeedsLayout: false,
            shrinkWrapBlocks: false,
            reliableMarginRight: true,
            boxSizingReliable: true,
            pixelPosition: false
        };

        // Make sure checked status is properly cloned
        input.checked = true;
        support.noCloneChecked = input.cloneNode(true).checked;

        // Make sure that the options inside disabled selects aren't marked as disabled
        // (WebKit marks them as disabled)
        select.disabled = true;
        support.optDisabled = !opt.disabled;

        // Support: IE<9
        try {
            delete div.test;
        } catch (e) {
            support.deleteExpando = false;
        }

        // Check if we can trust getAttribute("value")
        input = document.createElement("input");
        input.setAttribute("value", "");
        support.input = input.getAttribute("value") === "";

        // Check if an input maintains its value after becoming a radio
        input.value = "t";
        input.setAttribute("type", "radio");
        support.radioValue = input.value === "t";

        // #11217 - WebKit loses check when the name is after the checked attribute
        input.setAttribute("checked", "t");
        input.setAttribute("name", "t");

        fragment = document.createDocumentFragment();
        fragment.appendChild(input);

        // Check if a disconnected checkbox will retain its checked
        // value of true after appended to the DOM (IE6/7)
        support.appendChecked = input.checked;

        // WebKit doesn't clone checked state correctly in fragments
        support.checkClone = fragment.cloneNode(true).cloneNode(true).lastChild.checked;

        // Support: IE<9
        // Opera does not clone events (and typeof div.attachEvent === undefined).
        // IE9-10 clones events bound via attachEvent, but they don't trigger with .click()
        if (div.attachEvent) {
            div.attachEvent("onclick", function () {
                support.noCloneEvent = false;
            });

            div.cloneNode(true).click();
        }

        // Support: IE<9 (lack submit/change bubble), Firefox 17+ (lack focusin event)
        // Beware of CSP restrictions (https://developer.mozilla.org/en/Security/CSP), test/csp.php
        for (i in { submit: true, change: true, focusin: true }) {
            div.setAttribute(eventName = "on" + i, "t");

            support[ i + "Bubbles" ] = eventName in window || div.attributes[ eventName ].expando === false;
        }

        div.style.backgroundClip = "content-box";
        div.cloneNode(true).style.backgroundClip = "";
        support.clearCloneStyle = div.style.backgroundClip === "content-box";

        // Run tests that need a body at doc ready
        jQuery(function () {
            var container, marginDiv, tds,
                divReset = "padding:0;margin:0;border:0;display:block;box-sizing:content-box;-moz-box-sizing:content-box;-webkit-box-sizing:content-box;",
                body = document.getElementsByTagName("body")[0];

            if (!body) {
                // Return for frameset docs that don't have a body
                return;
            }

            container = document.createElement("div");
            container.style.cssText = "border:0;width:0;height:0;position:absolute;top:0;left:-9999px;margin-top:1px";

            body.appendChild(container).appendChild(div);

            // Support: IE8
            // Check if table cells still have offsetWidth/Height when they are set
            // to display:none and there are still other visible table cells in a
            // table row; if so, offsetWidth/Height are not reliable for use when
            // determining if an element has been hidden directly using
            // display:none (it is still safe to use offsets if a parent element is
            // hidden; don safety goggles and see bug #4512 for more information).
            div.innerHTML = "<table><tr><td></td><td>t</td></tr></table>";
            tds = div.getElementsByTagName("td");
            tds[ 0 ].style.cssText = "padding:0;margin:0;border:0;display:none";
            isSupported = ( tds[ 0 ].offsetHeight === 0 );

            tds[ 0 ].style.display = "";
            tds[ 1 ].style.display = "none";

            // Support: IE8
            // Check if empty table cells still have offsetWidth/Height
            support.reliableHiddenOffsets = isSupported && ( tds[ 0 ].offsetHeight === 0 );

            // Check box-sizing and margin behavior
            div.innerHTML = "";
            div.style.cssText = "box-sizing:border-box;-moz-box-sizing:border-box;-webkit-box-sizing:border-box;padding:1px;border:1px;display:block;width:4px;margin-top:1%;position:absolute;top:1%;";
            support.boxSizing = ( div.offsetWidth === 4 );
            support.doesNotIncludeMarginInBodyOffset = ( body.offsetTop !== 1 );

            // Use window.getComputedStyle because jsdom on node.js will break without it.
            if (window.getComputedStyle) {
                support.pixelPosition = ( window.getComputedStyle(div, null) || {} ).top !== "1%";
                support.boxSizingReliable = ( window.getComputedStyle(div, null) || { width: "4px" } ).width === "4px";

                // Check if div with explicit width and no margin-right incorrectly
                // gets computed margin-right based on width of container. (#3333)
                // Fails in WebKit before Feb 2011 nightlies
                // WebKit Bug 13343 - getComputedStyle returns wrong value for margin-right
                marginDiv = div.appendChild(document.createElement("div"));
                marginDiv.style.cssText = div.style.cssText = divReset;
                marginDiv.style.marginRight = marginDiv.style.width = "0";
                div.style.width = "1px";

                support.reliableMarginRight = !parseFloat(( window.getComputedStyle(marginDiv, null) || {} ).marginRight);
            }

            if (typeof div.style.zoom !== "undefined") {
                // Support: IE<8
                // Check if natively block-level elements act like inline-block
                // elements when setting their display to 'inline' and giving
                // them layout
                div.innerHTML = "";
                div.style.cssText = divReset + "width:1px;padding:1px;display:inline;zoom:1";
                support.inlineBlockNeedsLayout = ( div.offsetWidth === 3 );

                // Support: IE6
                // Check if elements with layout shrink-wrap their children
                div.style.display = "block";
                div.innerHTML = "<div></div>";
                div.firstChild.style.width = "5px";
                support.shrinkWrapBlocks = ( div.offsetWidth !== 3 );

                // Prevent IE 6 from affecting layout for positioned elements #11048
                // Prevent IE from shrinking the body in IE 7 mode #12869
                body.style.zoom = 1;
            }

            body.removeChild(container);

            // Null elements to avoid leaks in IE
            container = div = tds = marginDiv = null;
        });

        // Null elements to avoid leaks in IE
        all = select = fragment = opt = a = input = null;

        return support;
    })();

    var rbrace = /(?:\{[\s\S]*\}|\[[\s\S]*\])$/,
        rmultiDash = /([A-Z])/g;

    function internalData(elem, name, data, pvt /* Internal Use Only */) {
        if (!jQuery.acceptData(elem)) {
            return;
        }

        var thisCache, ret,
            internalKey = jQuery.expando,
            getByName = typeof name === "string",

        // We have to handle DOM nodes and JS objects differently because IE6-7
        // can't GC object references properly across the DOM-JS boundary
            isNode = elem.nodeType,

        // Only DOM nodes need the global jQuery cache; JS object data is
        // attached directly to the object so GC can occur automatically
            cache = isNode ? jQuery.cache : elem,

        // Only defining an ID for JS objects if its cache already exists allows
        // the code to shortcut on the same path as a DOM node with no cache
            id = isNode ? elem[ internalKey ] : elem[ internalKey ] && internalKey;

        // Avoid doing any more work than we need to when trying to get data on an
        // object that has no data at all
        if ((!id || !cache[id] || (!pvt && !cache[id].data)) && getByName && data === undefined) {
            return;
        }

        if (!id) {
            // Only DOM nodes need a new unique ID for each element since their data
            // ends up in the global cache
            if (isNode) {
                elem[ internalKey ] = id = core_deletedIds.pop() || jQuery.guid++;
            } else {
                id = internalKey;
            }
        }

        if (!cache[ id ]) {
            cache[ id ] = {};

            // Avoids exposing jQuery metadata on plain JS objects when the object
            // is serialized using JSON.stringify
            if (!isNode) {
                cache[ id ].toJSON = jQuery.noop;
            }
        }

        // An object can be passed to jQuery.data instead of a key/value pair; this gets
        // shallow copied over onto the existing cache
        if (typeof name === "object" || typeof name === "function") {
            if (pvt) {
                cache[ id ] = jQuery.extend(cache[ id ], name);
            } else {
                cache[ id ].data = jQuery.extend(cache[ id ].data, name);
            }
        }

        thisCache = cache[ id ];

        // jQuery data() is stored in a separate object inside the object's internal data
        // cache in order to avoid key collisions between internal data and user-defined
        // data.
        if (!pvt) {
            if (!thisCache.data) {
                thisCache.data = {};
            }

            thisCache = thisCache.data;
        }

        if (data !== undefined) {
            thisCache[ jQuery.camelCase(name) ] = data;
        }

        // Check for both converted-to-camel and non-converted data property names
        // If a data property was specified
        if (getByName) {

            // First Try to find as-is property data
            ret = thisCache[ name ];

            // Test for null|undefined property data
            if (ret == null) {

                // Try to find the camelCased property
                ret = thisCache[ jQuery.camelCase(name) ];
            }
        } else {
            ret = thisCache;
        }

        return ret;
    }

    function internalRemoveData(elem, name, pvt /* For internal use only */) {
        if (!jQuery.acceptData(elem)) {
            return;
        }

        var thisCache, i, l,

            isNode = elem.nodeType,

        // See jQuery.data for more information
            cache = isNode ? jQuery.cache : elem,
            id = isNode ? elem[ jQuery.expando ] : jQuery.expando;

        // If there is already no cache entry for this object, there is no
        // purpose in continuing
        if (!cache[ id ]) {
            return;
        }

        if (name) {

            thisCache = pvt ? cache[ id ] : cache[ id ].data;

            if (thisCache) {

                // Support array or space separated string names for data keys
                if (!jQuery.isArray(name)) {

                    // try the string as a key before any manipulation
                    if (name in thisCache) {
                        name = [ name ];
                    } else {

                        // split the camel cased version by spaces unless a key with the spaces exists
                        name = jQuery.camelCase(name);
                        if (name in thisCache) {
                            name = [ name ];
                        } else {
                            name = name.split(" ");
                        }
                    }
                } else {
                    // If "name" is an array of keys...
                    // When data is initially created, via ("key", "val") signature,
                    // keys will be converted to camelCase.
                    // Since there is no way to tell _how_ a key was added, remove
                    // both plain key and camelCase key. #12786
                    // This will only penalize the array argument path.
                    name = name.concat(jQuery.map(name, jQuery.camelCase));
                }

                for (i = 0, l = name.length; i < l; i++) {
                    delete thisCache[ name[i] ];
                }

                // If there is no data left in the cache, we want to continue
                // and let the cache object itself get destroyed
                if (!( pvt ? isEmptyDataObject : jQuery.isEmptyObject )(thisCache)) {
                    return;
                }
            }
        }

        // See jQuery.data for more information
        if (!pvt) {
            delete cache[ id ].data;

            // Don't destroy the parent cache unless the internal data object
            // had been the only thing left in it
            if (!isEmptyDataObject(cache[ id ])) {
                return;
            }
        }

        // Destroy the cache
        if (isNode) {
            jQuery.cleanData([ elem ], true);

            // Use delete when supported for expandos or `cache` is not a window per isWindow (#10080)
        } else if (jQuery.support.deleteExpando || cache != cache.window) {
            delete cache[ id ];

            // When all else fails, null
        } else {
            cache[ id ] = null;
        }
    }

    jQuery.extend({
        cache: {},

        // Unique for each copy of jQuery on the page
        // Non-digits removed to match rinlinejQuery
        expando: "jQuery" + ( core_version + Math.random() ).replace(/\D/g, ""),

        // The following elements throw uncatchable exceptions if you
        // attempt to add expando properties to them.
        noData: {
            "embed": true,
            // Ban all objects except for Flash (which handle expandos)
            "object": "clsid:D27CDB6E-AE6D-11cf-96B8-444553540000",
            "applet": true
        },

        hasData: function (elem) {
            elem = elem.nodeType ? jQuery.cache[ elem[jQuery.expando] ] : elem[ jQuery.expando ];
            return !!elem && !isEmptyDataObject(elem);
        },

        data: function (elem, name, data) {
            return internalData(elem, name, data, false);
        },

        removeData: function (elem, name) {
            return internalRemoveData(elem, name, false);
        },

        // For internal use only.
        _data: function (elem, name, data) {
            return internalData(elem, name, data, true);
        },

        _removeData: function (elem, name) {
            return internalRemoveData(elem, name, true);
        },

        // A method for determining if a DOM node can handle the data expando
        acceptData: function (elem) {
            var noData = elem.nodeName && jQuery.noData[ elem.nodeName.toLowerCase() ];

            // nodes accept data unless otherwise specified; rejection can be conditional
            return !noData || noData !== true && elem.getAttribute("classid") === noData;
        }
    });

    jQuery.fn.extend({
        data: function (key, value) {
            var attrs, name,
                elem = this[0],
                i = 0,
                data = null;

            // Gets all values
            if (key === undefined) {
                if (this.length) {
                    data = jQuery.data(elem);

                    if (elem.nodeType === 1 && !jQuery._data(elem, "parsedAttrs")) {
                        attrs = elem.attributes;
                        for (; i < attrs.length; i++) {
                            name = attrs[i].name;

                            if (!name.indexOf("data-")) {
                                name = jQuery.camelCase(name.substring(5));

                                dataAttr(elem, name, data[ name ]);
                            }
                        }
                        jQuery._data(elem, "parsedAttrs", true);
                    }
                }

                return data;
            }

            // Sets multiple values
            if (typeof key === "object") {
                return this.each(function () {
                    jQuery.data(this, key);
                });
            }

            return jQuery.access(this, function (value) {

                if (value === undefined) {
                    // Try to fetch any internally stored data first
                    return elem ? dataAttr(elem, key, jQuery.data(elem, key)) : null;
                }

                this.each(function () {
                    jQuery.data(this, key, value);
                });
            }, null, value, arguments.length > 1, null, true);
        },

        removeData: function (key) {
            return this.each(function () {
                jQuery.removeData(this, key);
            });
        }
    });

    function dataAttr(elem, key, data) {
        // If nothing was found internally, try to fetch any
        // data from the HTML5 data-* attribute
        if (data === undefined && elem.nodeType === 1) {

            var name = "data-" + key.replace(rmultiDash, "-$1").toLowerCase();

            data = elem.getAttribute(name);

            if (typeof data === "string") {
                try {
                    data = data === "true" ? true :
                        data === "false" ? false :
                            data === "null" ? null :
                                // Only convert to a number if it doesn't change the string
                                +data + "" === data ? +data :
                                    rbrace.test(data) ? jQuery.parseJSON(data) :
                                        data;
                } catch (e) {
                }

                // Make sure we set the data so it isn't changed later
                jQuery.data(elem, key, data);

            } else {
                data = undefined;
            }
        }

        return data;
    }

// checks a cache object for emptiness
    function isEmptyDataObject(obj) {
        var name;
        for (name in obj) {

            // if the public data object is empty, the private is still empty
            if (name === "data" && jQuery.isEmptyObject(obj[name])) {
                continue;
            }
            if (name !== "toJSON") {
                return false;
            }
        }

        return true;
    }

    jQuery.extend({
        queue: function (elem, type, data) {
            var queue;

            if (elem) {
                type = ( type || "fx" ) + "queue";
                queue = jQuery._data(elem, type);

                // Speed up dequeue by getting out quickly if this is just a lookup
                if (data) {
                    if (!queue || jQuery.isArray(data)) {
                        queue = jQuery._data(elem, type, jQuery.makeArray(data));
                    } else {
                        queue.push(data);
                    }
                }
                return queue || [];
            }
        },

        dequeue: function (elem, type) {
            type = type || "fx";

            var queue = jQuery.queue(elem, type),
                startLength = queue.length,
                fn = queue.shift(),
                hooks = jQuery._queueHooks(elem, type),
                next = function () {
                    jQuery.dequeue(elem, type);
                };

            // If the fx queue is dequeued, always remove the progress sentinel
            if (fn === "inprogress") {
                fn = queue.shift();
                startLength--;
            }

            hooks.cur = fn;
            if (fn) {

                // Add a progress sentinel to prevent the fx queue from being
                // automatically dequeued
                if (type === "fx") {
                    queue.unshift("inprogress");
                }

                // clear up the last queue stop function
                delete hooks.stop;
                fn.call(elem, next, hooks);
            }

            if (!startLength && hooks) {
                hooks.empty.fire();
            }
        },

        // not intended for public consumption - generates a queueHooks object, or returns the current one
        _queueHooks: function (elem, type) {
            var key = type + "queueHooks";
            return jQuery._data(elem, key) || jQuery._data(elem, key, {
                empty: jQuery.Callbacks("once memory").add(function () {
                    jQuery._removeData(elem, type + "queue");
                    jQuery._removeData(elem, key);
                })
            });
        }
    });

    jQuery.fn.extend({
        queue: function (type, data) {
            var setter = 2;

            if (typeof type !== "string") {
                data = type;
                type = "fx";
                setter--;
            }

            if (arguments.length < setter) {
                return jQuery.queue(this[0], type);
            }

            return data === undefined ?
                this :
                this.each(function () {
                    var queue = jQuery.queue(this, type, data);

                    // ensure a hooks for this queue
                    jQuery._queueHooks(this, type);

                    if (type === "fx" && queue[0] !== "inprogress") {
                        jQuery.dequeue(this, type);
                    }
                });
        },
        dequeue: function (type) {
            return this.each(function () {
                jQuery.dequeue(this, type);
            });
        },
        // Based off of the plugin by Clint Helfers, with permission.
        // http://blindsignals.com/index.php/2009/07/jquery-delay/
        delay: function (time, type) {
            time = jQuery.fx ? jQuery.fx.speeds[ time ] || time : time;
            type = type || "fx";

            return this.queue(type, function (next, hooks) {
                var timeout = setTimeout(next, time);
                hooks.stop = function () {
                    clearTimeout(timeout);
                };
            });
        },
        clearQueue: function (type) {
            return this.queue(type || "fx", []);
        },
        // Get a promise resolved when queues of a certain type
        // are emptied (fx is the type by default)
        promise: function (type, obj) {
            var tmp,
                count = 1,
                defer = jQuery.Deferred(),
                elements = this,
                i = this.length,
                resolve = function () {
                    if (!( --count )) {
                        defer.resolveWith(elements, [ elements ]);
                    }
                };

            if (typeof type !== "string") {
                obj = type;
                type = undefined;
            }
            type = type || "fx";

            while (i--) {
                tmp = jQuery._data(elements[ i ], type + "queueHooks");
                if (tmp && tmp.empty) {
                    count++;
                    tmp.empty.add(resolve);
                }
            }
            resolve();
            return defer.promise(obj);
        }
    });
    var nodeHook, boolHook,
        rclass = /[\t\r\n]/g,
        rreturn = /\r/g,
        rfocusable = /^(?:input|select|textarea|button|object)$/i,
        rclickable = /^(?:a|area)$/i,
        rboolean = /^(?:checked|selected|autofocus|autoplay|async|controls|defer|disabled|hidden|loop|multiple|open|readonly|required|scoped)$/i,
        ruseDefault = /^(?:checked|selected)$/i,
        getSetAttribute = jQuery.support.getSetAttribute,
        getSetInput = jQuery.support.input;

    jQuery.fn.extend({
        attr: function (name, value) {
            return jQuery.access(this, jQuery.attr, name, value, arguments.length > 1);
        },

        removeAttr: function (name) {
            return this.each(function () {
                jQuery.removeAttr(this, name);
            });
        },

        prop: function (name, value) {
            return jQuery.access(this, jQuery.prop, name, value, arguments.length > 1);
        },

        removeProp: function (name) {
            name = jQuery.propFix[ name ] || name;
            return this.each(function () {
                // try/catch handles cases where IE balks (such as removing a property on window)
                try {
                    this[ name ] = undefined;
                    delete this[ name ];
                } catch (e) {
                }
            });
        },

        addClass: function (value) {
            var classes, elem, cur, clazz, j,
                i = 0,
                len = this.length,
                proceed = typeof value === "string" && value;

            if (jQuery.isFunction(value)) {
                return this.each(function (j) {
                    jQuery(this).addClass(value.call(this, j, this.className));
                });
            }

            if (proceed) {
                // The disjunction here is for better compressibility (see removeClass)
                classes = ( value || "" ).match(core_rnotwhite) || [];

                for (; i < len; i++) {
                    elem = this[ i ];
                    cur = elem.nodeType === 1 && ( elem.className ?
                        ( " " + elem.className + " " ).replace(rclass, " ") :
                        " "
                        );

                    if (cur) {
                        j = 0;
                        while ((clazz = classes[j++])) {
                            if (cur.indexOf(" " + clazz + " ") < 0) {
                                cur += clazz + " ";
                            }
                        }
                        elem.className = jQuery.trim(cur);

                    }
                }
            }

            return this;
        },

        removeClass: function (value) {
            var classes, elem, cur, clazz, j,
                i = 0,
                len = this.length,
                proceed = arguments.length === 0 || typeof value === "string" && value;

            if (jQuery.isFunction(value)) {
                return this.each(function (j) {
                    jQuery(this).removeClass(value.call(this, j, this.className));
                });
            }
            if (proceed) {
                classes = ( value || "" ).match(core_rnotwhite) || [];

                for (; i < len; i++) {
                    elem = this[ i ];
                    // This expression is here for better compressibility (see addClass)
                    cur = elem.nodeType === 1 && ( elem.className ?
                        ( " " + elem.className + " " ).replace(rclass, " ") :
                        ""
                        );

                    if (cur) {
                        j = 0;
                        while ((clazz = classes[j++])) {
                            // Remove *all* instances
                            while (cur.indexOf(" " + clazz + " ") >= 0) {
                                cur = cur.replace(" " + clazz + " ", " ");
                            }
                        }
                        elem.className = value ? jQuery.trim(cur) : "";
                    }
                }
            }

            return this;
        },

        toggleClass: function (value, stateVal) {
            var type = typeof value,
                isBool = typeof stateVal === "boolean";

            if (jQuery.isFunction(value)) {
                return this.each(function (i) {
                    jQuery(this).toggleClass(value.call(this, i, this.className, stateVal), stateVal);
                });
            }

            return this.each(function () {
                if (type === "string") {
                    // toggle individual class names
                    var className,
                        i = 0,
                        self = jQuery(this),
                        state = stateVal,
                        classNames = value.match(core_rnotwhite) || [];

                    while ((className = classNames[ i++ ])) {
                        // check each className given, space separated list
                        state = isBool ? state : !self.hasClass(className);
                        self[ state ? "addClass" : "removeClass" ](className);
                    }

                    // Toggle whole class name
                } else if (type === "undefined" || type === "boolean") {
                    if (this.className) {
                        // store className if set
                        jQuery._data(this, "__className__", this.className);
                    }

                    // If the element has a class name or if we're passed "false",
                    // then remove the whole classname (if there was one, the above saved it).
                    // Otherwise bring back whatever was previously saved (if anything),
                    // falling back to the empty string if nothing was stored.
                    this.className = this.className || value === false ? "" : jQuery._data(this, "__className__") || "";
                }
            });
        },

        hasClass: function (selector) {
            var className = " " + selector + " ",
                i = 0,
                l = this.length;
            for (; i < l; i++) {
                if (this[i].nodeType === 1 && (" " + this[i].className + " ").replace(rclass, " ").indexOf(className) >= 0) {
                    return true;
                }
            }

            return false;
        },

        val: function (value) {
            var hooks, ret, isFunction,
                elem = this[0];

            if (!arguments.length) {
                if (elem) {
                    hooks = jQuery.valHooks[ elem.type ] || jQuery.valHooks[ elem.nodeName.toLowerCase() ];

                    if (hooks && "get" in hooks && (ret = hooks.get(elem, "value")) !== undefined) {
                        return ret;
                    }

                    ret = elem.value;

                    return typeof ret === "string" ?
                        // handle most common string cases
                        ret.replace(rreturn, "") :
                        // handle cases where value is null/undef or number
                        ret == null ? "" : ret;
                }

                return;
            }

            isFunction = jQuery.isFunction(value);

            return this.each(function (i) {
                var val,
                    self = jQuery(this);

                if (this.nodeType !== 1) {
                    return;
                }

                if (isFunction) {
                    val = value.call(this, i, self.val());
                } else {
                    val = value;
                }

                // Treat null/undefined as ""; convert numbers to string
                if (val == null) {
                    val = "";
                } else if (typeof val === "number") {
                    val += "";
                } else if (jQuery.isArray(val)) {
                    val = jQuery.map(val, function (value) {
                        return value == null ? "" : value + "";
                    });
                }

                hooks = jQuery.valHooks[ this.type ] || jQuery.valHooks[ this.nodeName.toLowerCase() ];

                // If set returns undefined, fall back to normal setting
                if (!hooks || !("set" in hooks) || hooks.set(this, val, "value") === undefined) {
                    this.value = val;
                }
            });
        }
    });

    jQuery.extend({
        valHooks: {
            option: {
                get: function (elem) {
                    // attributes.value is undefined in Blackberry 4.7 but
                    // uses .value. See #6932
                    var val = elem.attributes.value;
                    return !val || val.specified ? elem.value : elem.text;
                }
            },
            select: {
                get: function (elem) {
                    var value, option,
                        options = elem.options,
                        index = elem.selectedIndex,
                        one = elem.type === "select-one" || index < 0,
                        values = one ? null : [],
                        max = one ? index + 1 : options.length,
                        i = index < 0 ?
                            max :
                            one ? index : 0;

                    // Loop through all the selected options
                    for (; i < max; i++) {
                        option = options[ i ];

                        // oldIE doesn't update selected after form reset (#2551)
                        if (( option.selected || i === index ) &&
                            // Don't return options that are disabled or in a disabled optgroup
                            ( jQuery.support.optDisabled ? !option.disabled : option.getAttribute("disabled") === null ) &&
                            ( !option.parentNode.disabled || !jQuery.nodeName(option.parentNode, "optgroup") )) {

                            // Get the specific value for the option
                            value = jQuery(option).val();

                            // We don't need an array for one selects
                            if (one) {
                                return value;
                            }

                            // Multi-Selects return an array
                            values.push(value);
                        }
                    }

                    return values;
                },

                set: function (elem, value) {
                    var values = jQuery.makeArray(value);

                    jQuery(elem).find("option").each(function () {
                        this.selected = jQuery.inArray(jQuery(this).val(), values) >= 0;
                    });

                    if (!values.length) {
                        elem.selectedIndex = -1;
                    }
                    return values;
                }
            }
        },

        attr: function (elem, name, value) {
            var ret, hooks, notxml,
                nType = elem.nodeType;

            // don't get/set attributes on text, comment and attribute nodes
            if (!elem || nType === 3 || nType === 8 || nType === 2) {
                return;
            }

            // Fallback to prop when attributes are not supported
            if (typeof elem.getAttribute === "undefined") {
                return jQuery.prop(elem, name, value);
            }

            notxml = nType !== 1 || !jQuery.isXMLDoc(elem);

            // All attributes are lowercase
            // Grab necessary hook if one is defined
            if (notxml) {
                name = name.toLowerCase();
                hooks = jQuery.attrHooks[ name ] || ( rboolean.test(name) ? boolHook : nodeHook );
            }

            if (value !== undefined) {

                if (value === null) {
                    jQuery.removeAttr(elem, name);

                } else if (hooks && notxml && "set" in hooks && (ret = hooks.set(elem, value, name)) !== undefined) {
                    return ret;

                } else {
                    elem.setAttribute(name, value + "");
                    return value;
                }

            } else if (hooks && notxml && "get" in hooks && (ret = hooks.get(elem, name)) !== null) {
                return ret;

            } else {

                // In IE9+, Flash objects don't have .getAttribute (#12945)
                // Support: IE9+
                if (typeof elem.getAttribute !== "undefined") {
                    ret = elem.getAttribute(name);
                }

                // Non-existent attributes return null, we normalize to undefined
                return ret == null ?
                    undefined :
                    ret;
            }
        },

        removeAttr: function (elem, value) {
            var name, propName,
                i = 0,
                attrNames = value && value.match(core_rnotwhite);

            if (attrNames && elem.nodeType === 1) {
                while ((name = attrNames[i++])) {
                    propName = jQuery.propFix[ name ] || name;

                    // Boolean attributes get special treatment (#10870)
                    if (rboolean.test(name)) {
                        // Set corresponding property to false for boolean attributes
                        // Also clear defaultChecked/defaultSelected (if appropriate) for IE<8
                        if (!getSetAttribute && ruseDefault.test(name)) {
                            elem[ jQuery.camelCase("default-" + name) ] =
                                elem[ propName ] = false;
                        } else {
                            elem[ propName ] = false;
                        }

                        // See #9699 for explanation of this approach (setting first, then removal)
                    } else {
                        jQuery.attr(elem, name, "");
                    }

                    elem.removeAttribute(getSetAttribute ? name : propName);
                }
            }
        },

        attrHooks: {
            type: {
                set: function (elem, value) {
                    if (!jQuery.support.radioValue && value === "radio" && jQuery.nodeName(elem, "input")) {
                        // Setting the type on a radio button after the value resets the value in IE6-9
                        // Reset value to default in case type is set after value during creation
                        var val = elem.value;
                        elem.setAttribute("type", value);
                        if (val) {
                            elem.value = val;
                        }
                        return value;
                    }
                }
            }
        },

        propFix: {
            tabindex: "tabIndex",
            readonly: "readOnly",
            "for": "htmlFor",
            "class": "className",
            maxlength: "maxLength",
            cellspacing: "cellSpacing",
            cellpadding: "cellPadding",
            rowspan: "rowSpan",
            colspan: "colSpan",
            usemap: "useMap",
            frameborder: "frameBorder",
            contenteditable: "contentEditable"
        },

        prop: function (elem, name, value) {
            var ret, hooks, notxml,
                nType = elem.nodeType;

            // don't get/set properties on text, comment and attribute nodes
            if (!elem || nType === 3 || nType === 8 || nType === 2) {
                return;
            }

            notxml = nType !== 1 || !jQuery.isXMLDoc(elem);

            if (notxml) {
                // Fix name and attach hooks
                name = jQuery.propFix[ name ] || name;
                hooks = jQuery.propHooks[ name ];
            }

            if (value !== undefined) {
                if (hooks && "set" in hooks && (ret = hooks.set(elem, value, name)) !== undefined) {
                    return ret;

                } else {
                    return ( elem[ name ] = value );
                }

            } else {
                if (hooks && "get" in hooks && (ret = hooks.get(elem, name)) !== null) {
                    return ret;

                } else {
                    return elem[ name ];
                }
            }
        },

        propHooks: {
            tabIndex: {
                get: function (elem) {
                    // elem.tabIndex doesn't always return the correct value when it hasn't been explicitly set
                    // http://fluidproject.org/blog/2008/01/09/getting-setting-and-removing-tabindex-values-with-javascript/
                    var attributeNode = elem.getAttributeNode("tabindex");

                    return attributeNode && attributeNode.specified ?
                        parseInt(attributeNode.value, 10) :
                        rfocusable.test(elem.nodeName) || rclickable.test(elem.nodeName) && elem.href ?
                            0 :
                            undefined;
                }
            }
        }
    });

// Hook for boolean attributes
    boolHook = {
        get: function (elem, name) {
            var
            // Use .prop to determine if this attribute is understood as boolean
                prop = jQuery.prop(elem, name),

            // Fetch it accordingly
                attr = typeof prop === "boolean" && elem.getAttribute(name),
                detail = typeof prop === "boolean" ?

                    getSetInput && getSetAttribute ?
                        attr != null :
                        // oldIE fabricates an empty string for missing boolean attributes
                        // and conflates checked/selected into attroperties
                        ruseDefault.test(name) ?
                            elem[ jQuery.camelCase("default-" + name) ] :
                            !!attr :

                    // fetch an attribute node for properties not recognized as boolean
                    elem.getAttributeNode(name);

            return detail && detail.value !== false ?
                name.toLowerCase() :
                undefined;
        },
        set: function (elem, value, name) {
            if (value === false) {
                // Remove boolean attributes when set to false
                jQuery.removeAttr(elem, name);
            } else if (getSetInput && getSetAttribute || !ruseDefault.test(name)) {
                // IE<8 needs the *property* name
                elem.setAttribute(!getSetAttribute && jQuery.propFix[ name ] || name, name);

                // Use defaultChecked and defaultSelected for oldIE
            } else {
                elem[ jQuery.camelCase("default-" + name) ] = elem[ name ] = true;
            }

            return name;
        }
    };

// fix oldIE value attroperty
    if (!getSetInput || !getSetAttribute) {
        jQuery.attrHooks.value = {
            get: function (elem, name) {
                var ret = elem.getAttributeNode(name);
                return jQuery.nodeName(elem, "input") ?

                    // Ignore the value *property* by using defaultValue
                    elem.defaultValue :

                    ret && ret.specified ? ret.value : undefined;
            },
            set: function (elem, value, name) {
                if (jQuery.nodeName(elem, "input")) {
                    // Does not return so that setAttribute is also used
                    elem.defaultValue = value;
                } else {
                    // Use nodeHook if defined (#1954); otherwise setAttribute is fine
                    return nodeHook && nodeHook.set(elem, value, name);
                }
            }
        };
    }

// IE6/7 do not support getting/setting some attributes with get/setAttribute
    if (!getSetAttribute) {

        // Use this for any attribute in IE6/7
        // This fixes almost every IE6/7 issue
        nodeHook = jQuery.valHooks.button = {
            get: function (elem, name) {
                var ret = elem.getAttributeNode(name);
                return ret && ( name === "id" || name === "name" || name === "coords" ? ret.value !== "" : ret.specified ) ?
                    ret.value :
                    undefined;
            },
            set: function (elem, value, name) {
                // Set the existing or create a new attribute node
                var ret = elem.getAttributeNode(name);
                if (!ret) {
                    elem.setAttributeNode(
                        (ret = elem.ownerDocument.createAttribute(name))
                    );
                }

                ret.value = value += "";

                // Break association with cloned elements by also using setAttribute (#9646)
                return name === "value" || value === elem.getAttribute(name) ?
                    value :
                    undefined;
            }
        };

        // Set contenteditable to false on removals(#10429)
        // Setting to empty string throws an error as an invalid value
        jQuery.attrHooks.contenteditable = {
            get: nodeHook.get,
            set: function (elem, value, name) {
                nodeHook.set(elem, value === "" ? false : value, name);
            }
        };

        // Set width and height to auto instead of 0 on empty string( Bug #8150 )
        // This is for removals
        jQuery.each([ "width", "height" ], function (i, name) {
            jQuery.attrHooks[ name ] = jQuery.extend(jQuery.attrHooks[ name ], {
                set: function (elem, value) {
                    if (value === "") {
                        elem.setAttribute(name, "auto");
                        return value;
                    }
                }
            });
        });
    }


// Some attributes require a special call on IE
// http://msdn.microsoft.com/en-us/library/ms536429%28VS.85%29.aspx
    if (!jQuery.support.hrefNormalized) {
        jQuery.each([ "href", "src", "width", "height" ], function (i, name) {
            jQuery.attrHooks[ name ] = jQuery.extend(jQuery.attrHooks[ name ], {
                get: function (elem) {
                    var ret = elem.getAttribute(name, 2);
                    return ret == null ? undefined : ret;
                }
            });
        });

        // href/src property should get the full normalized URL (#10299/#12915)
        jQuery.each([ "href", "src" ], function (i, name) {
            jQuery.propHooks[ name ] = {
                get: function (elem) {
                    return elem.getAttribute(name, 4);
                }
            };
        });
    }

    if (!jQuery.support.style) {
        jQuery.attrHooks.style = {
            get: function (elem) {
                // Return undefined in the case of empty string
                // Note: IE uppercases css property names, but if we were to .toLowerCase()
                // .cssText, that would destroy case senstitivity in URL's, like in "background"
                return elem.style.cssText || undefined;
            },
            set: function (elem, value) {
                return ( elem.style.cssText = value + "" );
            }
        };
    }

// Safari mis-reports the default selected property of an option
// Accessing the parent's selectedIndex property fixes it
    if (!jQuery.support.optSelected) {
        jQuery.propHooks.selected = jQuery.extend(jQuery.propHooks.selected, {
            get: function (elem) {
                var parent = elem.parentNode;

                if (parent) {
                    parent.selectedIndex;

                    // Make sure that it also works with optgroups, see #5701
                    if (parent.parentNode) {
                        parent.parentNode.selectedIndex;
                    }
                }
                return null;
            }
        });
    }

// IE6/7 call enctype encoding
    if (!jQuery.support.enctype) {
        jQuery.propFix.enctype = "encoding";
    }

// Radios and checkboxes getter/setter
    if (!jQuery.support.checkOn) {
        jQuery.each([ "radio", "checkbox" ], function () {
            jQuery.valHooks[ this ] = {
                get: function (elem) {
                    // Handle the case where in Webkit "" is returned instead of "on" if a value isn't specified
                    return elem.getAttribute("value") === null ? "on" : elem.value;
                }
            };
        });
    }
    jQuery.each([ "radio", "checkbox" ], function () {
        jQuery.valHooks[ this ] = jQuery.extend(jQuery.valHooks[ this ], {
            set: function (elem, value) {
                if (jQuery.isArray(value)) {
                    return ( elem.checked = jQuery.inArray(jQuery(elem).val(), value) >= 0 );
                }
            }
        });
    });
    var rformElems = /^(?:input|select|textarea)$/i,
        rkeyEvent = /^key/,
        rmouseEvent = /^(?:mouse|contextmenu)|click/,
        rfocusMorph = /^(?:focusinfocus|focusoutblur)$/,
        rtypenamespace = /^([^.]*)(?:\.(.+)|)$/;

    function returnTrue() {
        return true;
    }

    function returnFalse() {
        return false;
    }

    /*
     * Helper functions for managing events -- not part of the public interface.
     * Props to Dean Edwards' addEvent library for many of the ideas.
     */
    jQuery.event = {

        global: {},

        add: function (elem, types, handler, data, selector) {

            var handleObjIn, eventHandle, tmp,
                events, t, handleObj,
                special, handlers, type, namespaces, origType,
            // Don't attach events to noData or text/comment nodes (but allow plain objects)
                elemData = elem.nodeType !== 3 && elem.nodeType !== 8 && jQuery._data(elem);

            if (!elemData) {
                return;
            }

            // Caller can pass in an object of custom data in lieu of the handler
            if (handler.handler) {
                handleObjIn = handler;
                handler = handleObjIn.handler;
                selector = handleObjIn.selector;
            }

            // Make sure that the handler has a unique ID, used to find/remove it later
            if (!handler.guid) {
                handler.guid = jQuery.guid++;
            }

            // Init the element's event structure and main handler, if this is the first
            if (!(events = elemData.events)) {
                events = elemData.events = {};
            }
            if (!(eventHandle = elemData.handle)) {
                eventHandle = elemData.handle = function (e) {
                    // Discard the second event of a jQuery.event.trigger() and
                    // when an event is called after a page has unloaded
                    return typeof jQuery !== "undefined" && (!e || jQuery.event.triggered !== e.type) ?
                        jQuery.event.dispatch.apply(eventHandle.elem, arguments) :
                        undefined;
                };
                // Add elem as a property of the handle fn to prevent a memory leak with IE non-native events
                eventHandle.elem = elem;
            }

            // Handle multiple events separated by a space
            // jQuery(...).bind("mouseover mouseout", fn);
            types = ( types || "" ).match(core_rnotwhite) || [""];
            t = types.length;
            while (t--) {
                tmp = rtypenamespace.exec(types[t]) || [];
                type = origType = tmp[1];
                namespaces = ( tmp[2] || "" ).split(".").sort();

                // If event changes its type, use the special event handlers for the changed type
                special = jQuery.event.special[ type ] || {};

                // If selector defined, determine special event api type, otherwise given type
                type = ( selector ? special.delegateType : special.bindType ) || type;

                // Update special based on newly reset type
                special = jQuery.event.special[ type ] || {};

                // handleObj is passed to all event handlers
                handleObj = jQuery.extend({
                    type: type,
                    origType: origType,
                    data: data,
                    handler: handler,
                    guid: handler.guid,
                    selector: selector,
                    needsContext: selector && jQuery.expr.match.needsContext.test(selector),
                    namespace: namespaces.join(".")
                }, handleObjIn);

                // Init the event handler queue if we're the first
                if (!(handlers = events[ type ])) {
                    handlers = events[ type ] = [];
                    handlers.delegateCount = 0;

                    // Only use addEventListener/attachEvent if the special events handler returns false
                    if (!special.setup || special.setup.call(elem, data, namespaces, eventHandle) === false) {
                        // Bind the global event handler to the element
                        if (elem.addEventListener) {
                            elem.addEventListener(type, eventHandle, false);

                        } else if (elem.attachEvent) {
                            elem.attachEvent("on" + type, eventHandle);
                        }
                    }
                }

                if (special.add) {
                    special.add.call(elem, handleObj);

                    if (!handleObj.handler.guid) {
                        handleObj.handler.guid = handler.guid;
                    }
                }

                // Add to the element's handler list, delegates in front
                if (selector) {
                    handlers.splice(handlers.delegateCount++, 0, handleObj);
                } else {
                    handlers.push(handleObj);
                }

                // Keep track of which events have ever been used, for event optimization
                jQuery.event.global[ type ] = true;
            }

            // Nullify elem to prevent memory leaks in IE
            elem = null;
        },

        // Detach an event or set of events from an element
        remove: function (elem, types, handler, selector, mappedTypes) {

            var j, origCount, tmp,
                events, t, handleObj,
                special, handlers, type, namespaces, origType,
                elemData = jQuery.hasData(elem) && jQuery._data(elem);

            if (!elemData || !(events = elemData.events)) {
                return;
            }

            // Once for each type.namespace in types; type may be omitted
            types = ( types || "" ).match(core_rnotwhite) || [""];
            t = types.length;
            while (t--) {
                tmp = rtypenamespace.exec(types[t]) || [];
                type = origType = tmp[1];
                namespaces = ( tmp[2] || "" ).split(".").sort();

                // Unbind all events (on this namespace, if provided) for the element
                if (!type) {
                    for (type in events) {
                        jQuery.event.remove(elem, type + types[ t ], handler, selector, true);
                    }
                    continue;
                }

                special = jQuery.event.special[ type ] || {};
                type = ( selector ? special.delegateType : special.bindType ) || type;
                handlers = events[ type ] || [];
                tmp = tmp[2] && new RegExp("(^|\\.)" + namespaces.join("\\.(?:.*\\.|)") + "(\\.|$)");

                // Remove matching events
                origCount = j = handlers.length;
                while (j--) {
                    handleObj = handlers[ j ];

                    if (( mappedTypes || origType === handleObj.origType ) &&
                        ( !handler || handler.guid === handleObj.guid ) &&
                        ( !tmp || tmp.test(handleObj.namespace) ) &&
                        ( !selector || selector === handleObj.selector || selector === "**" && handleObj.selector )) {
                        handlers.splice(j, 1);

                        if (handleObj.selector) {
                            handlers.delegateCount--;
                        }
                        if (special.remove) {
                            special.remove.call(elem, handleObj);
                        }
                    }
                }

                // Remove generic event handler if we removed something and no more handlers exist
                // (avoids potential for endless recursion during removal of special event handlers)
                if (origCount && !handlers.length) {
                    if (!special.teardown || special.teardown.call(elem, namespaces, elemData.handle) === false) {
                        jQuery.removeEvent(elem, type, elemData.handle);
                    }

                    delete events[ type ];
                }
            }

            // Remove the expando if it's no longer used
            if (jQuery.isEmptyObject(events)) {
                delete elemData.handle;

                // removeData also checks for emptiness and clears the expando if empty
                // so use it instead of delete
                jQuery._removeData(elem, "events");
            }
        },

        trigger: function (event, data, elem, onlyHandlers) {

            var i, cur, tmp, bubbleType, ontype, handle, special,
                eventPath = [ elem || document ],
                type = event.type || event,
                namespaces = event.namespace ? event.namespace.split(".") : [];

            cur = tmp = elem = elem || document;

            // Don't do events on text and comment nodes
            if (elem.nodeType === 3 || elem.nodeType === 8) {
                return;
            }

            // focus/blur morphs to focusin/out; ensure we're not firing them right now
            if (rfocusMorph.test(type + jQuery.event.triggered)) {
                return;
            }

            if (type.indexOf(".") >= 0) {
                // Namespaced trigger; create a regexp to match event type in handle()
                namespaces = type.split(".");
                type = namespaces.shift();
                namespaces.sort();
            }
            ontype = type.indexOf(":") < 0 && "on" + type;

            // Caller can pass in a jQuery.Event object, Object, or just an event type string
            event = event[ jQuery.expando ] ?
                event :
                new jQuery.Event(type, typeof event === "object" && event);

            event.isTrigger = true;
            event.namespace = namespaces.join(".");
            event.namespace_re = event.namespace ?
                new RegExp("(^|\\.)" + namespaces.join("\\.(?:.*\\.|)") + "(\\.|$)") :
                null;

            // Clean up the event in case it is being reused
            event.result = undefined;
            if (!event.target) {
                event.target = elem;
            }

            // Clone any incoming data and prepend the event, creating the handler arg list
            data = data == null ?
                [ event ] :
                jQuery.makeArray(data, [ event ]);

            // Allow special events to draw outside the lines
            special = jQuery.event.special[ type ] || {};
            if (!onlyHandlers && special.trigger && special.trigger.apply(elem, data) === false) {
                return;
            }

            // Determine event propagation path in advance, per W3C events spec (#9951)
            // Bubble up to document, then to window; watch for a global ownerDocument var (#9724)
            if (!onlyHandlers && !special.noBubble && !jQuery.isWindow(elem)) {

                bubbleType = special.delegateType || type;
                if (!rfocusMorph.test(bubbleType + type)) {
                    cur = cur.parentNode;
                }
                for (; cur; cur = cur.parentNode) {
                    eventPath.push(cur);
                    tmp = cur;
                }

                // Only add window if we got to document (e.g., not plain obj or detached DOM)
                if (tmp === (elem.ownerDocument || document)) {
                    eventPath.push(tmp.defaultView || tmp.parentWindow || window);
                }
            }

            // Fire handlers on the event path
            i = 0;
            while ((cur = eventPath[i++]) && !event.isPropagationStopped()) {

                event.type = i > 1 ?
                    bubbleType :
                    special.bindType || type;

                // jQuery handler
                handle = ( jQuery._data(cur, "events") || {} )[ event.type ] && jQuery._data(cur, "handle");
                if (handle) {
                    handle.apply(cur, data);
                }

                // Native handler
                handle = ontype && cur[ ontype ];
                if (handle && jQuery.acceptData(cur) && handle.apply && handle.apply(cur, data) === false) {
                    event.preventDefault();
                }
            }
            event.type = type;

            // If nobody prevented the default action, do it now
            if (!onlyHandlers && !event.isDefaultPrevented()) {

                if ((!special._default || special._default.apply(elem.ownerDocument, data) === false) && !(type === "click" && jQuery.nodeName(elem, "a")) && jQuery.acceptData(elem)) {

                    // Call a native DOM method on the target with the same name name as the event.
                    // Can't use an .isFunction() check here because IE6/7 fails that test.
                    // Don't do default actions on window, that's where global variables be (#6170)
                    if (ontype && elem[ type ] && !jQuery.isWindow(elem)) {

                        // Don't re-trigger an onFOO event when we call its FOO() method
                        tmp = elem[ ontype ];

                        if (tmp) {
                            elem[ ontype ] = null;
                        }

                        // Prevent re-triggering of the same event, since we already bubbled it above
                        jQuery.event.triggered = type;
                        try {
                            elem[ type ]();
                        } catch (e) {
                            // IE<9 dies on focus/blur to hidden element (#1486,#12518)
                            // only reproducible on winXP IE8 native, not IE9 in IE8 mode
                        }
                        jQuery.event.triggered = undefined;

                        if (tmp) {
                            elem[ ontype ] = tmp;
                        }
                    }
                }
            }

            return event.result;
        },

        dispatch: function (event) {

            // Make a writable jQuery.Event from the native event object
            event = jQuery.event.fix(event);

            var i, j, ret, matched, handleObj,
                handlerQueue = [],
                args = core_slice.call(arguments),
                handlers = ( jQuery._data(this, "events") || {} )[ event.type ] || [],
                special = jQuery.event.special[ event.type ] || {};

            // Use the fix-ed jQuery.Event rather than the (read-only) native event
            args[0] = event;
            event.delegateTarget = this;

            // Call the preDispatch hook for the mapped type, and let it bail if desired
            if (special.preDispatch && special.preDispatch.call(this, event) === false) {
                return;
            }

            // Determine handlers
            handlerQueue = jQuery.event.handlers.call(this, event, handlers);

            // Run delegates first; they may want to stop propagation beneath us
            i = 0;
            while ((matched = handlerQueue[ i++ ]) && !event.isPropagationStopped()) {
                event.currentTarget = matched.elem;

                j = 0;
                while ((handleObj = matched.handlers[ j++ ]) && !event.isImmediatePropagationStopped()) {

                    // Triggered event must either 1) have no namespace, or
                    // 2) have namespace(s) a subset or equal to those in the bound event (both can have no namespace).
                    if (!event.namespace_re || event.namespace_re.test(handleObj.namespace)) {

                        event.handleObj = handleObj;
                        event.data = handleObj.data;

                        ret = ( (jQuery.event.special[ handleObj.origType ] || {}).handle || handleObj.handler )
                            .apply(matched.elem, args);

                        if (ret !== undefined) {
                            if ((event.result = ret) === false) {
                                event.preventDefault();
                                event.stopPropagation();
                            }
                        }
                    }
                }
            }

            // Call the postDispatch hook for the mapped type
            if (special.postDispatch) {
                special.postDispatch.call(this, event);
            }

            return event.result;
        },

        handlers: function (event, handlers) {
            var i, matches, sel, handleObj,
                handlerQueue = [],
                delegateCount = handlers.delegateCount,
                cur = event.target;

            // Find delegate handlers
            // Black-hole SVG <use> instance trees (#13180)
            // Avoid non-left-click bubbling in Firefox (#3861)
            if (delegateCount && cur.nodeType && (!event.button || event.type !== "click")) {

                for (; cur != this; cur = cur.parentNode || this) {

                    // Don't process clicks on disabled elements (#6911, #8165, #11382, #11764)
                    if (cur.disabled !== true || event.type !== "click") {
                        matches = [];
                        for (i = 0; i < delegateCount; i++) {
                            handleObj = handlers[ i ];

                            // Don't conflict with Object.prototype properties (#13203)
                            sel = handleObj.selector + " ";

                            if (matches[ sel ] === undefined) {
                                matches[ sel ] = handleObj.needsContext ?
                                    jQuery(sel, this).index(cur) >= 0 :
                                    jQuery.find(sel, this, null, [ cur ]).length;
                            }
                            if (matches[ sel ]) {
                                matches.push(handleObj);
                            }
                        }
                        if (matches.length) {
                            handlerQueue.push({ elem: cur, handlers: matches });
                        }
                    }
                }
            }

            // Add the remaining (directly-bound) handlers
            if (delegateCount < handlers.length) {
                handlerQueue.push({ elem: this, handlers: handlers.slice(delegateCount) });
            }

            return handlerQueue;
        },

        fix: function (event) {
            if (event[ jQuery.expando ]) {
                return event;
            }

            // Create a writable copy of the event object and normalize some properties
            var i, prop,
                originalEvent = event,
                fixHook = jQuery.event.fixHooks[ event.type ] || {},
                copy = fixHook.props ? this.props.concat(fixHook.props) : this.props;

            event = new jQuery.Event(originalEvent);

            i = copy.length;
            while (i--) {
                prop = copy[ i ];
                event[ prop ] = originalEvent[ prop ];
            }

            // Support: IE<9
            // Fix target property (#1925)
            if (!event.target) {
                event.target = originalEvent.srcElement || document;
            }

            // Support: Chrome 23+, Safari?
            // Target should not be a text node (#504, #13143)
            if (event.target.nodeType === 3) {
                event.target = event.target.parentNode;
            }

            // Support: IE<9
            // For mouse/key events, metaKey==false if it's undefined (#3368, #11328)
            event.metaKey = !!event.metaKey;

            return fixHook.filter ? fixHook.filter(event, originalEvent) : event;
        },

        // Includes some event props shared by KeyEvent and MouseEvent
        props: "altKey bubbles cancelable ctrlKey currentTarget eventPhase metaKey relatedTarget shiftKey target timeStamp view which".split(" "),

        fixHooks: {},

        keyHooks: {
            props: "char charCode key keyCode".split(" "),
            filter: function (event, original) {

                // Add which for key events
                if (event.which == null) {
                    event.which = original.charCode != null ? original.charCode : original.keyCode;
                }

                return event;
            }
        },

        mouseHooks: {
            props: "button buttons clientX clientY fromElement offsetX offsetY pageX pageY screenX screenY toElement".split(" "),
            filter: function (event, original) {
                var eventDoc, doc, body,
                    button = original.button,
                    fromElement = original.fromElement;

                // Calculate pageX/Y if missing and clientX/Y available
                if (event.pageX == null && original.clientX != null) {
                    eventDoc = event.target.ownerDocument || document;
                    doc = eventDoc.documentElement;
                    body = eventDoc.body;

                    event.pageX = original.clientX + ( doc && doc.scrollLeft || body && body.scrollLeft || 0 ) - ( doc && doc.clientLeft || body && body.clientLeft || 0 );
                    event.pageY = original.clientY + ( doc && doc.scrollTop || body && body.scrollTop || 0 ) - ( doc && doc.clientTop || body && body.clientTop || 0 );
                }

                // Add relatedTarget, if necessary
                if (!event.relatedTarget && fromElement) {
                    event.relatedTarget = fromElement === event.target ? original.toElement : fromElement;
                }

                // Add which for click: 1 === left; 2 === middle; 3 === right
                // Note: button is not normalized, so don't use it
                if (!event.which && button !== undefined) {
                    event.which = ( button & 1 ? 1 : ( button & 2 ? 3 : ( button & 4 ? 2 : 0 ) ) );
                }

                return event;
            }
        },

        special: {
            load: {
                // Prevent triggered image.load events from bubbling to window.load
                noBubble: true
            },
            click: {
                // For checkbox, fire native event so checked state will be right
                trigger: function () {
                    if (jQuery.nodeName(this, "input") && this.type === "checkbox" && this.click) {
                        this.click();
                        return false;
                    }
                }
            },
            focus: {
                // Fire native event if possible so blur/focus sequence is correct
                trigger: function () {
                    if (this !== document.activeElement && this.focus) {
                        try {
                            this.focus();
                            return false;
                        } catch (e) {
                            // Support: IE<9
                            // If we error on focus to hidden element (#1486, #12518),
                            // let .trigger() run the handlers
                        }
                    }
                },
                delegateType: "focusin"
            },
            blur: {
                trigger: function () {
                    if (this === document.activeElement && this.blur) {
                        this.blur();
                        return false;
                    }
                },
                delegateType: "focusout"
            },

            beforeunload: {
                postDispatch: function (event) {

                    // Even when returnValue equals to undefined Firefox will still show alert
                    if (event.result !== undefined) {
                        event.originalEvent.returnValue = event.result;
                    }
                }
            }
        },

        simulate: function (type, elem, event, bubble) {
            // Piggyback on a donor event to simulate a different one.
            // Fake originalEvent to avoid donor's stopPropagation, but if the
            // simulated event prevents default then we do the same on the donor.
            var e = jQuery.extend(
                new jQuery.Event(),
                event,
                { type: type,
                    isSimulated: true,
                    originalEvent: {}
                }
            );
            if (bubble) {
                jQuery.event.trigger(e, null, elem);
            } else {
                jQuery.event.dispatch.call(elem, e);
            }
            if (e.isDefaultPrevented()) {
                event.preventDefault();
            }
        }
    };

    jQuery.removeEvent = document.removeEventListener ?
        function (elem, type, handle) {
            if (elem.removeEventListener) {
                elem.removeEventListener(type, handle, false);
            }
        } :
        function (elem, type, handle) {
            var name = "on" + type;

            if (elem.detachEvent) {

                // #8545, #7054, preventing memory leaks for custom events in IE6-8
                // detachEvent needed property on element, by name of that event, to properly expose it to GC
                if (typeof elem[ name ] === "undefined") {
                    elem[ name ] = null;
                }

                elem.detachEvent(name, handle);
            }
        };

    jQuery.Event = function (src, props) {
        // Allow instantiation without the 'new' keyword
        if (!(this instanceof jQuery.Event)) {
            return new jQuery.Event(src, props);
        }

        // Event object
        if (src && src.type) {
            this.originalEvent = src;
            this.type = src.type;

            // Events bubbling up the document may have been marked as prevented
            // by a handler lower down the tree; reflect the correct value.
            this.isDefaultPrevented = ( src.defaultPrevented || src.returnValue === false ||
                src.getPreventDefault && src.getPreventDefault() ) ? returnTrue : returnFalse;

            // Event type
        } else {
            this.type = src;
        }

        // Put explicitly provided properties onto the event object
        if (props) {
            jQuery.extend(this, props);
        }

        // Create a timestamp if incoming event doesn't have one
        this.timeStamp = src && src.timeStamp || jQuery.now();

        // Mark it as fixed
        this[ jQuery.expando ] = true;
    };

// jQuery.Event is based on DOM3 Events as specified by the ECMAScript Language Binding
// http://www.w3.org/TR/2003/WD-DOM-Level-3-Events-20030331/ecma-script-binding.html
    jQuery.Event.prototype = {
        isDefaultPrevented: returnFalse,
        isPropagationStopped: returnFalse,
        isImmediatePropagationStopped: returnFalse,

        preventDefault: function () {
            var e = this.originalEvent;

            this.isDefaultPrevented = returnTrue;
            if (!e) {
                return;
            }

            // If preventDefault exists, run it on the original event
            if (e.preventDefault) {
                e.preventDefault();

                // Support: IE
                // Otherwise set the returnValue property of the original event to false
            } else {
                e.returnValue = false;
            }
        },
        stopPropagation: function () {
            var e = this.originalEvent;

            this.isPropagationStopped = returnTrue;
            if (!e) {
                return;
            }
            // If stopPropagation exists, run it on the original event
            if (e.stopPropagation) {
                e.stopPropagation();
            }

            // Support: IE
            // Set the cancelBubble property of the original event to true
            e.cancelBubble = true;
        },
        stopImmediatePropagation: function () {
            this.isImmediatePropagationStopped = returnTrue;
            this.stopPropagation();
        }
    };

// Create mouseenter/leave events using mouseover/out and event-time checks
    jQuery.each({
        mouseenter: "mouseover",
        mouseleave: "mouseout"
    }, function (orig, fix) {
        jQuery.event.special[ orig ] = {
            delegateType: fix,
            bindType: fix,

            handle: function (event) {
                var ret,
                    target = this,
                    related = event.relatedTarget,
                    handleObj = event.handleObj;

                // For mousenter/leave call the handler if related is outside the target.
                // NB: No relatedTarget if the mouse left/entered the browser window
                if (!related || (related !== target && !jQuery.contains(target, related))) {
                    event.type = handleObj.origType;
                    ret = handleObj.handler.apply(this, arguments);
                    event.type = fix;
                }
                return ret;
            }
        };
    });

// IE submit delegation
    if (!jQuery.support.submitBubbles) {

        jQuery.event.special.submit = {
            setup: function () {
                // Only need this for delegated form submit events
                if (jQuery.nodeName(this, "form")) {
                    return false;
                }

                // Lazy-add a submit handler when a descendant form may potentially be submitted
                jQuery.event.add(this, "click._submit keypress._submit", function (e) {
                    // Node name check avoids a VML-related crash in IE (#9807)
                    var elem = e.target,
                        form = jQuery.nodeName(elem, "input") || jQuery.nodeName(elem, "button") ? elem.form : undefined;
                    if (form && !jQuery._data(form, "submitBubbles")) {
                        jQuery.event.add(form, "submit._submit", function (event) {
                            event._submit_bubble = true;
                        });
                        jQuery._data(form, "submitBubbles", true);
                    }
                });
                // return undefined since we don't need an event listener
            },

            postDispatch: function (event) {
                // If form was submitted by the user, bubble the event up the tree
                if (event._submit_bubble) {
                    delete event._submit_bubble;
                    if (this.parentNode && !event.isTrigger) {
                        jQuery.event.simulate("submit", this.parentNode, event, true);
                    }
                }
            },

            teardown: function () {
                // Only need this for delegated form submit events
                if (jQuery.nodeName(this, "form")) {
                    return false;
                }

                // Remove delegated handlers; cleanData eventually reaps submit handlers attached above
                jQuery.event.remove(this, "._submit");
            }
        };
    }

// IE change delegation and checkbox/radio fix
    if (!jQuery.support.changeBubbles) {

        jQuery.event.special.change = {

            setup: function () {

                if (rformElems.test(this.nodeName)) {
                    // IE doesn't fire change on a check/radio until blur; trigger it on click
                    // after a propertychange. Eat the blur-change in special.change.handle.
                    // This still fires onchange a second time for check/radio after blur.
                    if (this.type === "checkbox" || this.type === "radio") {
                        jQuery.event.add(this, "propertychange._change", function (event) {
                            if (event.originalEvent.propertyName === "checked") {
                                this._just_changed = true;
                            }
                        });
                        jQuery.event.add(this, "click._change", function (event) {
                            if (this._just_changed && !event.isTrigger) {
                                this._just_changed = false;
                            }
                            // Allow triggered, simulated change events (#11500)
                            jQuery.event.simulate("change", this, event, true);
                        });
                    }
                    return false;
                }
                // Delegated event; lazy-add a change handler on descendant inputs
                jQuery.event.add(this, "beforeactivate._change", function (e) {
                    var elem = e.target;

                    if (rformElems.test(elem.nodeName) && !jQuery._data(elem, "changeBubbles")) {
                        jQuery.event.add(elem, "change._change", function (event) {
                            if (this.parentNode && !event.isSimulated && !event.isTrigger) {
                                jQuery.event.simulate("change", this.parentNode, event, true);
                            }
                        });
                        jQuery._data(elem, "changeBubbles", true);
                    }
                });
            },

            handle: function (event) {
                var elem = event.target;

                // Swallow native change events from checkbox/radio, we already triggered them above
                if (this !== elem || event.isSimulated || event.isTrigger || (elem.type !== "radio" && elem.type !== "checkbox")) {
                    return event.handleObj.handler.apply(this, arguments);
                }
            },

            teardown: function () {
                jQuery.event.remove(this, "._change");

                return !rformElems.test(this.nodeName);
            }
        };
    }

// Create "bubbling" focus and blur events
    if (!jQuery.support.focusinBubbles) {
        jQuery.each({ focus: "focusin", blur: "focusout" }, function (orig, fix) {

            // Attach a single capturing handler while someone wants focusin/focusout
            var attaches = 0,
                handler = function (event) {
                    jQuery.event.simulate(fix, event.target, jQuery.event.fix(event), true);
                };

            jQuery.event.special[ fix ] = {
                setup: function () {
                    if (attaches++ === 0) {
                        document.addEventListener(orig, handler, true);
                    }
                },
                teardown: function () {
                    if (--attaches === 0) {
                        document.removeEventListener(orig, handler, true);
                    }
                }
            };
        });
    }

    jQuery.fn.extend({

        on: function (types, selector, data, fn, /*INTERNAL*/ one) {
            var origFn, type;

            // Types can be a map of types/handlers
            if (typeof types === "object") {
                // ( types-Object, selector, data )
                if (typeof selector !== "string") {
                    // ( types-Object, data )
                    data = data || selector;
                    selector = undefined;
                }
                for (type in types) {
                    this.on(type, selector, data, types[ type ], one);
                }
                return this;
            }

            if (data == null && fn == null) {
                // ( types, fn )
                fn = selector;
                data = selector = undefined;
            } else if (fn == null) {
                if (typeof selector === "string") {
                    // ( types, selector, fn )
                    fn = data;
                    data = undefined;
                } else {
                    // ( types, data, fn )
                    fn = data;
                    data = selector;
                    selector = undefined;
                }
            }
            if (fn === false) {
                fn = returnFalse;
            } else if (!fn) {
                return this;
            }

            if (one === 1) {
                origFn = fn;
                fn = function (event) {
                    // Can use an empty set, since event contains the info
                    jQuery().off(event);
                    return origFn.apply(this, arguments);
                };
                // Use same guid so caller can remove using origFn
                fn.guid = origFn.guid || ( origFn.guid = jQuery.guid++ );
            }
            return this.each(function () {
                jQuery.event.add(this, types, fn, data, selector);
            });
        },
        one: function (types, selector, data, fn) {
            return this.on(types, selector, data, fn, 1);
        },
        off: function (types, selector, fn) {
            var handleObj, type;
            if (types && types.preventDefault && types.handleObj) {
                // ( event )  dispatched jQuery.Event
                handleObj = types.handleObj;
                jQuery(types.delegateTarget).off(
                    handleObj.namespace ? handleObj.origType + "." + handleObj.namespace : handleObj.origType,
                    handleObj.selector,
                    handleObj.handler
                );
                return this;
            }
            if (typeof types === "object") {
                // ( types-object [, selector] )
                for (type in types) {
                    this.off(type, selector, types[ type ]);
                }
                return this;
            }
            if (selector === false || typeof selector === "function") {
                // ( types [, fn] )
                fn = selector;
                selector = undefined;
            }
            if (fn === false) {
                fn = returnFalse;
            }
            return this.each(function () {
                jQuery.event.remove(this, types, fn, selector);
            });
        },

        bind: function (types, data, fn) {
            return this.on(types, null, data, fn);
        },
        unbind: function (types, fn) {
            return this.off(types, null, fn);
        },

        delegate: function (selector, types, data, fn) {
            return this.on(types, selector, data, fn);
        },
        undelegate: function (selector, types, fn) {
            // ( namespace ) or ( selector, types [, fn] )
            return arguments.length === 1 ? this.off(selector, "**") : this.off(types, selector || "**", fn);
        },

        trigger: function (type, data) {
            return this.each(function () {
                jQuery.event.trigger(type, data, this);
            });
        },
        triggerHandler: function (type, data) {
            var elem = this[0];
            if (elem) {
                return jQuery.event.trigger(type, data, elem, true);
            }
        },

        hover: function (fnOver, fnOut) {
            return this.mouseenter(fnOver).mouseleave(fnOut || fnOver);
        }
    });

    jQuery.each(("blur focus focusin focusout load resize scroll unload click dblclick " +
        "mousedown mouseup mousemove mouseover mouseout mouseenter mouseleave " +
        "change select submit keydown keypress keyup error contextmenu").split(" "), function (i, name) {

        // Handle event binding
        jQuery.fn[ name ] = function (data, fn) {
            return arguments.length > 0 ?
                this.on(name, null, data, fn) :
                this.trigger(name);
        };

        if (rkeyEvent.test(name)) {
            jQuery.event.fixHooks[ name ] = jQuery.event.keyHooks;
        }

        if (rmouseEvent.test(name)) {
            jQuery.event.fixHooks[ name ] = jQuery.event.mouseHooks;
        }
    });
    /*!
     * Sizzle CSS Selector Engine
     * Copyright 2012 jQuery Foundation and other contributors
     * Released under the MIT license
     * http://sizzlejs.com/
     */
    (function (window, undefined) {

        var i,
            cachedruns,
            Expr,
            getText,
            isXML,
            compile,
            hasDuplicate,
            outermostContext,

        // Local document vars
            setDocument,
            document,
            docElem,
            documentIsXML,
            rbuggyQSA,
            rbuggyMatches,
            matches,
            contains,
            sortOrder,

        // Instance-specific data
            expando = "sizzle" + -(new Date()),
            preferredDoc = window.document,
            support = {},
            dirruns = 0,
            done = 0,
            classCache = createCache(),
            tokenCache = createCache(),
            compilerCache = createCache(),

        // General-purpose constants
            strundefined = typeof undefined,
            MAX_NEGATIVE = 1 << 31,

        // Array methods
            arr = [],
            pop = arr.pop,
            push = arr.push,
            slice = arr.slice,
        // Use a stripped-down indexOf if we can't use a native one
            indexOf = arr.indexOf || function (elem) {
                var i = 0,
                    len = this.length;
                for (; i < len; i++) {
                    if (this[i] === elem) {
                        return i;
                    }
                }
                return -1;
            },


        // Regular expressions

        // Whitespace characters http://www.w3.org/TR/css3-selectors/#whitespace
            whitespace = "[\\x20\\t\\r\\n\\f]",
        // http://www.w3.org/TR/css3-syntax/#characters
            characterEncoding = "(?:\\\\.|[\\w-]|[^\\x00-\\xa0])+",

        // Loosely modeled on CSS identifier characters
        // An unquoted value should be a CSS identifier http://www.w3.org/TR/css3-selectors/#attribute-selectors
        // Proper syntax: http://www.w3.org/TR/CSS21/syndata.html#value-def-identifier
            identifier = characterEncoding.replace("w", "w#"),

        // Acceptable operators http://www.w3.org/TR/selectors/#attribute-selectors
            operators = "([*^$|!~]?=)",
            attributes = "\\[" + whitespace + "*(" + characterEncoding + ")" + whitespace +
                "*(?:" + operators + whitespace + "*(?:(['\"])((?:\\\\.|[^\\\\])*?)\\3|(" + identifier + ")|)|)" + whitespace + "*\\]",

        // Prefer arguments quoted,
        //   then not containing pseudos/brackets,
        //   then attribute selectors/non-parenthetical expressions,
        //   then anything else
        // These preferences are here to reduce the number of selectors
        //   needing tokenize in the PSEUDO preFilter
            pseudos = ":(" + characterEncoding + ")(?:\\(((['\"])((?:\\\\.|[^\\\\])*?)\\3|((?:\\\\.|[^\\\\()[\\]]|" + attributes.replace(3, 8) + ")*)|.*)\\)|)",

        // Leading and non-escaped trailing whitespace, capturing some non-whitespace characters preceding the latter
            rtrim = new RegExp("^" + whitespace + "+|((?:^|[^\\\\])(?:\\\\.)*)" + whitespace + "+$", "g"),

            rcomma = new RegExp("^" + whitespace + "*," + whitespace + "*"),
            rcombinators = new RegExp("^" + whitespace + "*([\\x20\\t\\r\\n\\f>+~])" + whitespace + "*"),
            rpseudo = new RegExp(pseudos),
            ridentifier = new RegExp("^" + identifier + "$"),

            matchExpr = {
                "ID": new RegExp("^#(" + characterEncoding + ")"),
                "CLASS": new RegExp("^\\.(" + characterEncoding + ")"),
                "NAME": new RegExp("^\\[name=['\"]?(" + characterEncoding + ")['\"]?\\]"),
                "TAG": new RegExp("^(" + characterEncoding.replace("w", "w*") + ")"),
                "ATTR": new RegExp("^" + attributes),
                "PSEUDO": new RegExp("^" + pseudos),
                "CHILD": new RegExp("^:(only|first|last|nth|nth-last)-(child|of-type)(?:\\(" + whitespace +
                    "*(even|odd|(([+-]|)(\\d*)n|)" + whitespace + "*(?:([+-]|)" + whitespace +
                    "*(\\d+)|))" + whitespace + "*\\)|)", "i"),
                // For use in libraries implementing .is()
                // We use this for POS matching in `select`
                "needsContext": new RegExp("^" + whitespace + "*[>+~]|:(even|odd|eq|gt|lt|nth|first|last)(?:\\(" +
                    whitespace + "*((?:-\\d)?\\d*)" + whitespace + "*\\)|)(?=[^-]|$)", "i")
            },

            rsibling = /[\x20\t\r\n\f]*[+~]/,

            rnative = /\{\s*\[native code\]\s*\}/,

        // Easily-parseable/retrievable ID or TAG or CLASS selectors
            rquickExpr = /^(?:#([\w-]+)|(\w+)|\.([\w-]+))$/,

            rinputs = /^(?:input|select|textarea|button)$/i,
            rheader = /^h\d$/i,

            rescape = /'|\\/g,
            rattributeQuotes = /\=[\x20\t\r\n\f]*([^'"\]]*)[\x20\t\r\n\f]*\]/g,

        // CSS escapes http://www.w3.org/TR/CSS21/syndata.html#escaped-characters
            runescape = /\\([\da-fA-F]{1,6}[\x20\t\r\n\f]?|.)/g,
            funescape = function (_, escaped) {
                var high = "0x" + escaped - 0x10000;
                // NaN means non-codepoint
                return high !== high ?
                    escaped :
                    // BMP codepoint
                    high < 0 ?
                        String.fromCharCode(high + 0x10000) :
                        // Supplemental Plane codepoint (surrogate pair)
                        String.fromCharCode(high >> 10 | 0xD800, high & 0x3FF | 0xDC00);
            };

// Use a stripped-down slice if we can't use a native one
        try {
            slice.call(docElem.childNodes, 0)[0].nodeType;
        } catch (e) {
            slice = function (i) {
                var elem,
                    results = [];
                for (; (elem = this[i]); i++) {
                    results.push(elem);
                }
                return results;
            };
        }

        /**
         * For feature detection
         * @param {Function} fn The function to test for native support
         */
        function isNative(fn) {
            return rnative.test(fn + "");
        }

        /**
         * Create key-value caches of limited size
         * @returns {Function(string, Object)} Returns the Object data after storing it on itself with
         *    property name the (space-suffixed) string and (if the cache is larger than Expr.cacheLength)
         *    deleting the oldest entry
         */
        function createCache() {
            var cache,
                keys = [];

            return (cache = function (key, value) {
                // Use (key + " ") to avoid collision with native prototype properties (see Issue #157)
                if (keys.push(key += " ") > Expr.cacheLength) {
                    // Only keep the most recent entries
                    delete cache[ keys.shift() ];
                }
                return (cache[ key ] = value);
            });
        }

        /**
         * Mark a function for special use by Sizzle
         * @param {Function} fn The function to mark
         */
        function markFunction(fn) {
            fn[ expando ] = true;
            return fn;
        }

        /**
         * Support testing using an element
         * @param {Function} fn Passed the created div and expects a boolean result
         */
        function assert(fn) {
            var div = document.createElement("div");

            try {
                return fn(div);
            } catch (e) {
                return false;
            } finally {
                // release memory in IE
                div = null;
            }
        }

        function Sizzle(selector, context, results, seed) {
            var match, elem, m, nodeType,
            // QSA vars
                i, groups, old, nid, newContext, newSelector;

            if (( context ? context.ownerDocument || context : preferredDoc ) !== document) {
                setDocument(context);
            }

            context = context || document;
            results = results || [];

            if (!selector || typeof selector !== "string") {
                return results;
            }

            if ((nodeType = context.nodeType) !== 1 && nodeType !== 9) {
                return [];
            }

            if (!documentIsXML && !seed) {

                // Shortcuts
                if ((match = rquickExpr.exec(selector))) {
                    // Speed-up: Sizzle("#ID")
                    if ((m = match[1])) {
                        if (nodeType === 9) {
                            elem = context.getElementById(m);
                            // Check parentNode to catch when Blackberry 4.6 returns
                            // nodes that are no longer in the document #6963
                            if (elem && elem.parentNode) {
                                // Handle the case where IE, Opera, and Webkit return items
                                // by name instead of ID
                                if (elem.id === m) {
                                    results.push(elem);
                                    return results;
                                }
                            } else {
                                return results;
                            }
                        } else {
                            // Context is not a document
                            if (context.ownerDocument && (elem = context.ownerDocument.getElementById(m)) &&
                                contains(context, elem) && elem.id === m) {
                                results.push(elem);
                                return results;
                            }
                        }

                        // Speed-up: Sizzle("TAG")
                    } else if (match[2]) {
                        push.apply(results, slice.call(context.getElementsByTagName(selector), 0));
                        return results;

                        // Speed-up: Sizzle(".CLASS")
                    } else if ((m = match[3]) && support.getByClassName && context.getElementsByClassName) {
                        push.apply(results, slice.call(context.getElementsByClassName(m), 0));
                        return results;
                    }
                }

                // QSA path
                if (support.qsa && !rbuggyQSA.test(selector)) {
                    old = true;
                    nid = expando;
                    newContext = context;
                    newSelector = nodeType === 9 && selector;

                    // qSA works strangely on Element-rooted queries
                    // We can work around this by specifying an extra ID on the root
                    // and working up from there (Thanks to Andrew Dupont for the technique)
                    // IE 8 doesn't work on object elements
                    if (nodeType === 1 && context.nodeName.toLowerCase() !== "object") {
                        groups = tokenize(selector);

                        if ((old = context.getAttribute("id"))) {
                            nid = old.replace(rescape, "\\$&");
                        } else {
                            context.setAttribute("id", nid);
                        }
                        nid = "[id='" + nid + "'] ";

                        i = groups.length;
                        while (i--) {
                            groups[i] = nid + toSelector(groups[i]);
                        }
                        newContext = rsibling.test(selector) && context.parentNode || context;
                        newSelector = groups.join(",");
                    }

                    if (newSelector) {
                        try {
                            push.apply(results, slice.call(newContext.querySelectorAll(
                                newSelector
                            ), 0));
                            return results;
                        } catch (qsaError) {
                        } finally {
                            if (!old) {
                                context.removeAttribute("id");
                            }
                        }
                    }
                }
            }

            // All others
            return select(selector.replace(rtrim, "$1"), context, results, seed);
        }

        /**
         * Detect xml
         * @param {Element|Object} elem An element or a document
         */
        isXML = Sizzle.isXML = function (elem) {
            // documentElement is verified for cases where it doesn't yet exist
            // (such as loading iframes in IE - #4833)
            var documentElement = elem && (elem.ownerDocument || elem).documentElement;
            return documentElement ? documentElement.nodeName !== "HTML" : false;
        };

        /**
         * Sets document-related variables once based on the current document
         * @param {Element|Object} [doc] An element or document object to use to set the document
         * @returns {Object} Returns the current document
         */
        setDocument = Sizzle.setDocument = function (node) {
            var doc = node ? node.ownerDocument || node : preferredDoc;

            // If no document and documentElement is available, return
            if (doc === document || doc.nodeType !== 9 || !doc.documentElement) {
                return document;
            }

            // Set our document
            document = doc;
            docElem = doc.documentElement;

            // Support tests
            documentIsXML = isXML(doc);

            // Check if getElementsByTagName("*") returns only elements
            support.tagNameNoComments = assert(function (div) {
                div.appendChild(doc.createComment(""));
                return !div.getElementsByTagName("*").length;
            });

            // Check if attributes should be retrieved by attribute nodes
            support.attributes = assert(function (div) {
                div.innerHTML = "<select></select>";
                var type = typeof div.lastChild.getAttribute("multiple");
                // IE8 returns a string for some attributes even when not present
                return type !== "boolean" && type !== "string";
            });

            // Check if getElementsByClassName can be trusted
            support.getByClassName = assert(function (div) {
                // Opera can't find a second classname (in 9.6)
                div.innerHTML = "<div class='hidden e'></div><div class='hidden'></div>";
                if (!div.getElementsByClassName || !div.getElementsByClassName("e").length) {
                    return false;
                }

                // Safari 3.2 caches class attributes and doesn't catch changes
                div.lastChild.className = "e";
                return div.getElementsByClassName("e").length === 2;
            });

            // Check if getElementById returns elements by name
            // Check if getElementsByName privileges form controls or returns elements by ID
            support.getByName = assert(function (div) {
                // Inject content
                div.id = expando + 0;
                div.innerHTML = "<a name='" + expando + "'></a><div name='" + expando + "'></div>";
                docElem.insertBefore(div, docElem.firstChild);

                // Test
                var pass = doc.getElementsByName &&
                    // buggy browsers will return fewer than the correct 2
                    doc.getElementsByName(expando).length === 2 +
                        // buggy browsers will return more than the correct 0
                        doc.getElementsByName(expando + 0).length;
                support.getIdNotName = !doc.getElementById(expando);

                // Cleanup
                docElem.removeChild(div);

                return pass;
            });

            // IE6/7 return modified attributes
            Expr.attrHandle = assert(function (div) {
                div.innerHTML = "<a href='#'></a>";
                return div.firstChild && typeof div.firstChild.getAttribute !== strundefined &&
                    div.firstChild.getAttribute("href") === "#";
            }) ?
            {} :
            {
                "href": function (elem) {
                    return elem.getAttribute("href", 2);
                },
                "type": function (elem) {
                    return elem.getAttribute("type");
                }
            };

            // ID find and filter
            if (support.getIdNotName) {
                Expr.find["ID"] = function (id, context) {
                    if (typeof context.getElementById !== strundefined && !documentIsXML) {
                        var m = context.getElementById(id);
                        // Check parentNode to catch when Blackberry 4.6 returns
                        // nodes that are no longer in the document #6963
                        return m && m.parentNode ? [m] : [];
                    }
                };
                Expr.filter["ID"] = function (id) {
                    var attrId = id.replace(runescape, funescape);
                    return function (elem) {
                        return elem.getAttribute("id") === attrId;
                    };
                };
            } else {
                Expr.find["ID"] = function (id, context) {
                    if (typeof context.getElementById !== strundefined && !documentIsXML) {
                        var m = context.getElementById(id);

                        return m ?
                            m.id === id || typeof m.getAttributeNode !== strundefined && m.getAttributeNode("id").value === id ?
                                [m] :
                                undefined :
                            [];
                    }
                };
                Expr.filter["ID"] = function (id) {
                    var attrId = id.replace(runescape, funescape);
                    return function (elem) {
                        var node = typeof elem.getAttributeNode !== strundefined && elem.getAttributeNode("id");
                        return node && node.value === attrId;
                    };
                };
            }

            // Tag
            Expr.find["TAG"] = support.tagNameNoComments ?
                function (tag, context) {
                    if (typeof context.getElementsByTagName !== strundefined) {
                        return context.getElementsByTagName(tag);
                    }
                } :
                function (tag, context) {
                    var elem,
                        tmp = [],
                        i = 0,
                        results = context.getElementsByTagName(tag);

                    // Filter out possible comments
                    if (tag === "*") {
                        for (; (elem = results[i]); i++) {
                            if (elem.nodeType === 1) {
                                tmp.push(elem);
                            }
                        }

                        return tmp;
                    }
                    return results;
                };

            // Name
            Expr.find["NAME"] = support.getByName && function (tag, context) {
                if (typeof context.getElementsByName !== strundefined) {
                    return context.getElementsByName(name);
                }
            };

            // Class
            Expr.find["CLASS"] = support.getByClassName && function (className, context) {
                if (typeof context.getElementsByClassName !== strundefined && !documentIsXML) {
                    return context.getElementsByClassName(className);
                }
            };

            // QSA and matchesSelector support

            // matchesSelector(:active) reports false when true (IE9/Opera 11.5)
            rbuggyMatches = [];

            // qSa(:focus) reports false when true (Chrome 21),
            // no need to also add to buggyMatches since matches checks buggyQSA
            // A support test would require too much code (would include document ready)
            rbuggyQSA = [ ":focus" ];

            if ((support.qsa = isNative(doc.querySelectorAll))) {
                // Build QSA regex
                // Regex strategy adopted from Diego Perini
                assert(function (div) {
                    // Select is set to empty string on purpose
                    // This is to test IE's treatment of not explictly
                    // setting a boolean content attribute,
                    // since its presence should be enough
                    // http://bugs.jquery.com/ticket/12359
                    div.innerHTML = "<select><option selected=''></option></select>";

                    // IE8 - Some boolean attributes are not treated correctly
                    if (!div.querySelectorAll("[selected]").length) {
                        rbuggyQSA.push("\\[" + whitespace + "*(?:checked|disabled|ismap|multiple|readonly|selected|value)");
                    }

                    // Webkit/Opera - :checked should return selected option elements
                    // http://www.w3.org/TR/2011/REC-css3-selectors-20110929/#checked
                    // IE8 throws error here and will not see later tests
                    if (!div.querySelectorAll(":checked").length) {
                        rbuggyQSA.push(":checked");
                    }
                });

                assert(function (div) {

                    // Opera 10-12/IE8 - ^= $= *= and empty values
                    // Should not select anything
                    div.innerHTML = "<input type='hidden' i=''/>";
                    if (div.querySelectorAll("[i^='']").length) {
                        rbuggyQSA.push("[*^$]=" + whitespace + "*(?:\"\"|'')");
                    }

                    // FF 3.5 - :enabled/:disabled and hidden elements (hidden elements are still enabled)
                    // IE8 throws error here and will not see later tests
                    if (!div.querySelectorAll(":enabled").length) {
                        rbuggyQSA.push(":enabled", ":disabled");
                    }

                    // Opera 10-11 does not throw on post-comma invalid pseudos
                    div.querySelectorAll("*,:x");
                    rbuggyQSA.push(",.*:");
                });
            }

            if ((support.matchesSelector = isNative((matches = docElem.matchesSelector ||
                docElem.mozMatchesSelector ||
                docElem.webkitMatchesSelector ||
                docElem.oMatchesSelector ||
                docElem.msMatchesSelector)))) {

                assert(function (div) {
                    // Check to see if it's possible to do matchesSelector
                    // on a disconnected node (IE 9)
                    support.disconnectedMatch = matches.call(div, "div");

                    // This should fail with an exception
                    // Gecko does not error, returns false instead
                    matches.call(div, "[s!='']:x");
                    rbuggyMatches.push("!=", pseudos);
                });
            }

            rbuggyQSA = new RegExp(rbuggyQSA.join("|"));
            rbuggyMatches = new RegExp(rbuggyMatches.join("|"));

            // Element contains another
            // Purposefully does not implement inclusive descendent
            // As in, an element does not contain itself
            contains = isNative(docElem.contains) || docElem.compareDocumentPosition ?
                function (a, b) {
                    var adown = a.nodeType === 9 ? a.documentElement : a,
                        bup = b && b.parentNode;
                    return a === bup || !!( bup && bup.nodeType === 1 && (
                        adown.contains ?
                            adown.contains(bup) :
                            a.compareDocumentPosition && a.compareDocumentPosition(bup) & 16
                        ));
                } :
                function (a, b) {
                    if (b) {
                        while ((b = b.parentNode)) {
                            if (b === a) {
                                return true;
                            }
                        }
                    }
                    return false;
                };

            // Document order sorting
            sortOrder = docElem.compareDocumentPosition ?
                function (a, b) {
                    var compare;

                    if (a === b) {
                        hasDuplicate = true;
                        return 0;
                    }

                    if ((compare = b.compareDocumentPosition && a.compareDocumentPosition && a.compareDocumentPosition(b))) {
                        if (compare & 1 || a.parentNode && a.parentNode.nodeType === 11) {
                            if (a === doc || contains(preferredDoc, a)) {
                                return -1;
                            }
                            if (b === doc || contains(preferredDoc, b)) {
                                return 1;
                            }
                            return 0;
                        }
                        return compare & 4 ? -1 : 1;
                    }

                    return a.compareDocumentPosition ? -1 : 1;
                } :
                function (a, b) {
                    var cur,
                        i = 0,
                        aup = a.parentNode,
                        bup = b.parentNode,
                        ap = [ a ],
                        bp = [ b ];

                    // The nodes are identical, we can exit early
                    if (a === b) {
                        hasDuplicate = true;
                        return 0;

                        // Fallback to using sourceIndex (in IE) if it's available on both nodes
                    } else if (a.sourceIndex && b.sourceIndex) {
                        return ( ~b.sourceIndex || MAX_NEGATIVE ) - ( contains(preferredDoc, a) && ~a.sourceIndex || MAX_NEGATIVE );

                        // Parentless nodes are either documents or disconnected
                    } else if (!aup || !bup) {
                        return a === doc ? -1 :
                            b === doc ? 1 :
                                aup ? -1 :
                                    bup ? 1 :
                                        0;

                        // If the nodes are siblings, we can do a quick check
                    } else if (aup === bup) {
                        return siblingCheck(a, b);
                    }

                    // Otherwise we need full lists of their ancestors for comparison
                    cur = a;
                    while ((cur = cur.parentNode)) {
                        ap.unshift(cur);
                    }
                    cur = b;
                    while ((cur = cur.parentNode)) {
                        bp.unshift(cur);
                    }

                    // Walk down the tree looking for a discrepancy
                    while (ap[i] === bp[i]) {
                        i++;
                    }

                    return i ?
                        // Do a sibling check if the nodes have a common ancestor
                        siblingCheck(ap[i], bp[i]) :

                        // Otherwise nodes in our document sort first
                        ap[i] === preferredDoc ? -1 :
                            bp[i] === preferredDoc ? 1 :
                                0;
                };

            // Always assume the presence of duplicates if sort doesn't
            // pass them to our comparison function (as in Google Chrome).
            hasDuplicate = false;
            [0, 0].sort(sortOrder);
            support.detectDuplicates = hasDuplicate;

            return document;
        };

        Sizzle.matches = function (expr, elements) {
            return Sizzle(expr, null, null, elements);
        };

        Sizzle.matchesSelector = function (elem, expr) {
            // Set document vars if needed
            if (( elem.ownerDocument || elem ) !== document) {
                setDocument(elem);
            }

            // Make sure that attribute selectors are quoted
            expr = expr.replace(rattributeQuotes, "='$1']");

            // rbuggyQSA always contains :focus, so no need for an existence check
            if (support.matchesSelector && !documentIsXML && (!rbuggyMatches || !rbuggyMatches.test(expr)) && !rbuggyQSA.test(expr)) {
                try {
                    var ret = matches.call(elem, expr);

                    // IE 9's matchesSelector returns false on disconnected nodes
                    if (ret || support.disconnectedMatch ||
                        // As well, disconnected nodes are said to be in a document
                        // fragment in IE 9
                        elem.document && elem.document.nodeType !== 11) {
                        return ret;
                    }
                } catch (e) {
                }
            }

            return Sizzle(expr, document, null, [elem]).length > 0;
        };

        Sizzle.contains = function (context, elem) {
            // Set document vars if needed
            if (( context.ownerDocument || context ) !== document) {
                setDocument(context);
            }
            return contains(context, elem);
        };

        Sizzle.attr = function (elem, name) {
            var val;

            // Set document vars if needed
            if (( elem.ownerDocument || elem ) !== document) {
                setDocument(elem);
            }

            if (!documentIsXML) {
                name = name.toLowerCase();
            }
            if ((val = Expr.attrHandle[ name ])) {
                return val(elem);
            }
            if (documentIsXML || support.attributes) {
                return elem.getAttribute(name);
            }
            return ( (val = elem.getAttributeNode(name)) || elem.getAttribute(name) ) && elem[ name ] === true ?
                name :
                val && val.specified ? val.value : null;
        };

        Sizzle.error = function (msg) {
            throw new Error("Syntax error, unrecognized expression: " + msg);
        };

// Document sorting and removing duplicates
        Sizzle.uniqueSort = function (results) {
            var elem,
                duplicates = [],
                i = 1,
                j = 0;

            // Unless we *know* we can detect duplicates, assume their presence
            hasDuplicate = !support.detectDuplicates;
            results.sort(sortOrder);

            if (hasDuplicate) {
                for (; (elem = results[i]); i++) {
                    if (elem === results[ i - 1 ]) {
                        j = duplicates.push(i);
                    }
                }
                while (j--) {
                    results.splice(duplicates[ j ], 1);
                }
            }

            return results;
        };

        function siblingCheck(a, b) {
            var cur = a && b && a.nextSibling;

            for (; cur; cur = cur.nextSibling) {
                if (cur === b) {
                    return -1;
                }
            }

            return a ? 1 : -1;
        }

// Returns a function to use in pseudos for input types
        function createInputPseudo(type) {
            return function (elem) {
                var name = elem.nodeName.toLowerCase();
                return name === "input" && elem.type === type;
            };
        }

// Returns a function to use in pseudos for buttons
        function createButtonPseudo(type) {
            return function (elem) {
                var name = elem.nodeName.toLowerCase();
                return (name === "input" || name === "button") && elem.type === type;
            };
        }

// Returns a function to use in pseudos for positionals
        function createPositionalPseudo(fn) {
            return markFunction(function (argument) {
                argument = +argument;
                return markFunction(function (seed, matches) {
                    var j,
                        matchIndexes = fn([], seed.length, argument),
                        i = matchIndexes.length;

                    // Match elements found at the specified indexes
                    while (i--) {
                        if (seed[ (j = matchIndexes[i]) ]) {
                            seed[j] = !(matches[j] = seed[j]);
                        }
                    }
                });
            });
        }

        /**
         * Utility function for retrieving the text value of an array of DOM nodes
         * @param {Array|Element} elem
         */
        getText = Sizzle.getText = function (elem) {
            var node,
                ret = "",
                i = 0,
                nodeType = elem.nodeType;

            if (!nodeType) {
                // If no nodeType, this is expected to be an array
                for (; (node = elem[i]); i++) {
                    // Do not traverse comment nodes
                    ret += getText(node);
                }
            } else if (nodeType === 1 || nodeType === 9 || nodeType === 11) {
                // Use textContent for elements
                // innerText usage removed for consistency of new lines (see #11153)
                if (typeof elem.textContent === "string") {
                    return elem.textContent;
                } else {
                    // Traverse its children
                    for (elem = elem.firstChild; elem; elem = elem.nextSibling) {
                        ret += getText(elem);
                    }
                }
            } else if (nodeType === 3 || nodeType === 4) {
                return elem.nodeValue;
            }
            // Do not include comment or processing instruction nodes

            return ret;
        };

        Expr = Sizzle.selectors = {

            // Can be adjusted by the user
            cacheLength: 50,

            createPseudo: markFunction,

            match: matchExpr,

            find: {},

            relative: {
                ">": { dir: "parentNode", first: true },
                " ": { dir: "parentNode" },
                "+": { dir: "previousSibling", first: true },
                "~": { dir: "previousSibling" }
            },

            preFilter: {
                "ATTR": function (match) {
                    match[1] = match[1].replace(runescape, funescape);

                    // Move the given value to match[3] whether quoted or unquoted
                    match[3] = ( match[4] || match[5] || "" ).replace(runescape, funescape);

                    if (match[2] === "~=") {
                        match[3] = " " + match[3] + " ";
                    }

                    return match.slice(0, 4);
                },

                "CHILD": function (match) {
                    /* matches from matchExpr["CHILD"]
                     1 type (only|nth|...)
                     2 what (child|of-type)
                     3 argument (even|odd|\d*|\d*n([+-]\d+)?|...)
                     4 xn-component of xn+y argument ([+-]?\d*n|)
                     5 sign of xn-component
                     6 x of xn-component
                     7 sign of y-component
                     8 y of y-component
                     */
                    match[1] = match[1].toLowerCase();

                    if (match[1].slice(0, 3) === "nth") {
                        // nth-* requires argument
                        if (!match[3]) {
                            Sizzle.error(match[0]);
                        }

                        // numeric x and y parameters for Expr.filter.CHILD
                        // remember that false/true cast respectively to 0/1
                        match[4] = +( match[4] ? match[5] + (match[6] || 1) : 2 * ( match[3] === "even" || match[3] === "odd" ) );
                        match[5] = +( ( match[7] + match[8] ) || match[3] === "odd" );

                        // other types prohibit arguments
                    } else if (match[3]) {
                        Sizzle.error(match[0]);
                    }

                    return match;
                },

                "PSEUDO": function (match) {
                    var excess,
                        unquoted = !match[5] && match[2];

                    if (matchExpr["CHILD"].test(match[0])) {
                        return null;
                    }

                    // Accept quoted arguments as-is
                    if (match[4]) {
                        match[2] = match[4];

                        // Strip excess characters from unquoted arguments
                    } else if (unquoted && rpseudo.test(unquoted) &&
                        // Get excess from tokenize (recursively)
                        (excess = tokenize(unquoted, true)) &&
                        // advance to the next closing parenthesis
                        (excess = unquoted.indexOf(")", unquoted.length - excess) - unquoted.length)) {

                        // excess is a negative index
                        match[0] = match[0].slice(0, excess);
                        match[2] = unquoted.slice(0, excess);
                    }

                    // Return only captures needed by the pseudo filter method (type and argument)
                    return match.slice(0, 3);
                }
            },

            filter: {

                "TAG": function (nodeName) {
                    if (nodeName === "*") {
                        return function () {
                            return true;
                        };
                    }

                    nodeName = nodeName.replace(runescape, funescape).toLowerCase();
                    return function (elem) {
                        return elem.nodeName && elem.nodeName.toLowerCase() === nodeName;
                    };
                },

                "CLASS": function (className) {
                    var pattern = classCache[ className + " " ];

                    return pattern ||
                        (pattern = new RegExp("(^|" + whitespace + ")" + className + "(" + whitespace + "|$)")) &&
                            classCache(className, function (elem) {
                                return pattern.test(elem.className || (typeof elem.getAttribute !== strundefined && elem.getAttribute("class")) || "");
                            });
                },

                "ATTR": function (name, operator, check) {
                    return function (elem) {
                        var result = Sizzle.attr(elem, name);

                        if (result == null) {
                            return operator === "!=";
                        }
                        if (!operator) {
                            return true;
                        }

                        result += "";

                        return operator === "=" ? result === check :
                            operator === "!=" ? result !== check :
                                operator === "^=" ? check && result.indexOf(check) === 0 :
                                    operator === "*=" ? check && result.indexOf(check) > -1 :
                                        operator === "$=" ? check && result.substr(result.length - check.length) === check :
                                            operator === "~=" ? ( " " + result + " " ).indexOf(check) > -1 :
                                                operator === "|=" ? result === check || result.substr(0, check.length + 1) === check + "-" :
                                                    false;
                    };
                },

                "CHILD": function (type, what, argument, first, last) {
                    var simple = type.slice(0, 3) !== "nth",
                        forward = type.slice(-4) !== "last",
                        ofType = what === "of-type";

                    return first === 1 && last === 0 ?

                        // Shortcut for :nth-*(n)
                        function (elem) {
                            return !!elem.parentNode;
                        } :

                        function (elem, context, xml) {
                            var cache, outerCache, node, diff, nodeIndex, start,
                                dir = simple !== forward ? "nextSibling" : "previousSibling",
                                parent = elem.parentNode,
                                name = ofType && elem.nodeName.toLowerCase(),
                                useCache = !xml && !ofType;

                            if (parent) {

                                // :(first|last|only)-(child|of-type)
                                if (simple) {
                                    while (dir) {
                                        node = elem;
                                        while ((node = node[ dir ])) {
                                            if (ofType ? node.nodeName.toLowerCase() === name : node.nodeType === 1) {
                                                return false;
                                            }
                                        }
                                        // Reverse direction for :only-* (if we haven't yet done so)
                                        start = dir = type === "only" && !start && "nextSibling";
                                    }
                                    return true;
                                }

                                start = [ forward ? parent.firstChild : parent.lastChild ];

                                // non-xml :nth-child(...) stores cache data on `parent`
                                if (forward && useCache) {
                                    // Seek `elem` from a previously-cached index
                                    outerCache = parent[ expando ] || (parent[ expando ] = {});
                                    cache = outerCache[ type ] || [];
                                    nodeIndex = cache[0] === dirruns && cache[1];
                                    diff = cache[0] === dirruns && cache[2];
                                    node = nodeIndex && parent.childNodes[ nodeIndex ];

                                    while ((node = ++nodeIndex && node && node[ dir ] ||

                                        // Fallback to seeking `elem` from the start
                                        (diff = nodeIndex = 0) || start.pop())) {

                                        // When found, cache indexes on `parent` and break
                                        if (node.nodeType === 1 && ++diff && node === elem) {
                                            outerCache[ type ] = [ dirruns, nodeIndex, diff ];
                                            break;
                                        }
                                    }

                                    // Use previously-cached element index if available
                                } else if (useCache && (cache = (elem[ expando ] || (elem[ expando ] = {}))[ type ]) && cache[0] === dirruns) {
                                    diff = cache[1];

                                    // xml :nth-child(...) or :nth-last-child(...) or :nth(-last)?-of-type(...)
                                } else {
                                    // Use the same loop as above to seek `elem` from the start
                                    while ((node = ++nodeIndex && node && node[ dir ] ||
                                        (diff = nodeIndex = 0) || start.pop())) {

                                        if (( ofType ? node.nodeName.toLowerCase() === name : node.nodeType === 1 ) && ++diff) {
                                            // Cache the index of each encountered element
                                            if (useCache) {
                                                (node[ expando ] || (node[ expando ] = {}))[ type ] = [ dirruns, diff ];
                                            }

                                            if (node === elem) {
                                                break;
                                            }
                                        }
                                    }
                                }

                                // Incorporate the offset, then check against cycle size
                                diff -= last;
                                return diff === first || ( diff % first === 0 && diff / first >= 0 );
                            }
                        };
                },

                "PSEUDO": function (pseudo, argument) {
                    // pseudo-class names are case-insensitive
                    // http://www.w3.org/TR/selectors/#pseudo-classes
                    // Prioritize by case sensitivity in case custom pseudos are added with uppercase letters
                    // Remember that setFilters inherits from pseudos
                    var args,
                        fn = Expr.pseudos[ pseudo ] || Expr.setFilters[ pseudo.toLowerCase() ] ||
                            Sizzle.error("unsupported pseudo: " + pseudo);

                    // The user may use createPseudo to indicate that
                    // arguments are needed to create the filter function
                    // just as Sizzle does
                    if (fn[ expando ]) {
                        return fn(argument);
                    }

                    // But maintain support for old signatures
                    if (fn.length > 1) {
                        args = [ pseudo, pseudo, "", argument ];
                        return Expr.setFilters.hasOwnProperty(pseudo.toLowerCase()) ?
                            markFunction(function (seed, matches) {
                                var idx,
                                    matched = fn(seed, argument),
                                    i = matched.length;
                                while (i--) {
                                    idx = indexOf.call(seed, matched[i]);
                                    seed[ idx ] = !( matches[ idx ] = matched[i] );
                                }
                            }) :
                            function (elem) {
                                return fn(elem, 0, args);
                            };
                    }

                    return fn;
                }
            },

            pseudos: {
                // Potentially complex pseudos
                "not": markFunction(function (selector) {
                    // Trim the selector passed to compile
                    // to avoid treating leading and trailing
                    // spaces as combinators
                    var input = [],
                        results = [],
                        matcher = compile(selector.replace(rtrim, "$1"));

                    return matcher[ expando ] ?
                        markFunction(function (seed, matches, context, xml) {
                            var elem,
                                unmatched = matcher(seed, null, xml, []),
                                i = seed.length;

                            // Match elements unmatched by `matcher`
                            while (i--) {
                                if ((elem = unmatched[i])) {
                                    seed[i] = !(matches[i] = elem);
                                }
                            }
                        }) :
                        function (elem, context, xml) {
                            input[0] = elem;
                            matcher(input, null, xml, results);
                            return !results.pop();
                        };
                }),

                "has": markFunction(function (selector) {
                    return function (elem) {
                        return Sizzle(selector, elem).length > 0;
                    };
                }),

                "contains": markFunction(function (text) {
                    return function (elem) {
                        return ( elem.textContent || elem.innerText || getText(elem) ).indexOf(text) > -1;
                    };
                }),

                // "Whether an element is represented by a :lang() selector
                // is based solely on the element's language value
                // being equal to the identifier C,
                // or beginning with the identifier C immediately followed by "-".
                // The matching of C against the element's language value is performed case-insensitively.
                // The identifier C does not have to be a valid language name."
                // http://www.w3.org/TR/selectors/#lang-pseudo
                "lang": markFunction(function (lang) {
                    // lang value must be a valid identifider
                    if (!ridentifier.test(lang || "")) {
                        Sizzle.error("unsupported lang: " + lang);
                    }
                    lang = lang.replace(runescape, funescape).toLowerCase();
                    return function (elem) {
                        var elemLang;
                        do {
                            if ((elemLang = documentIsXML ?
                                elem.getAttribute("xml:lang") || elem.getAttribute("lang") :
                                elem.lang)) {

                                elemLang = elemLang.toLowerCase();
                                return elemLang === lang || elemLang.indexOf(lang + "-") === 0;
                            }
                        } while ((elem = elem.parentNode) && elem.nodeType === 1);
                        return false;
                    };
                }),

                // Miscellaneous
                "target": function (elem) {
                    var hash = window.location && window.location.hash;
                    return hash && hash.slice(1) === elem.id;
                },

                "root": function (elem) {
                    return elem === docElem;
                },

                "focus": function (elem) {
                    return elem === document.activeElement && (!document.hasFocus || document.hasFocus()) && !!(elem.type || elem.href || ~elem.tabIndex);
                },

                // Boolean properties
                "enabled": function (elem) {
                    return elem.disabled === false;
                },

                "disabled": function (elem) {
                    return elem.disabled === true;
                },

                "checked": function (elem) {
                    // In CSS3, :checked should return both checked and selected elements
                    // http://www.w3.org/TR/2011/REC-css3-selectors-20110929/#checked
                    var nodeName = elem.nodeName.toLowerCase();
                    return (nodeName === "input" && !!elem.checked) || (nodeName === "option" && !!elem.selected);
                },

                "selected": function (elem) {
                    // Accessing this property makes selected-by-default
                    // options in Safari work properly
                    if (elem.parentNode) {
                        elem.parentNode.selectedIndex;
                    }

                    return elem.selected === true;
                },

                // Contents
                "empty": function (elem) {
                    // http://www.w3.org/TR/selectors/#empty-pseudo
                    // :empty is only affected by element nodes and content nodes(including text(3), cdata(4)),
                    //   not comment, processing instructions, or others
                    // Thanks to Diego Perini for the nodeName shortcut
                    //   Greater than "@" means alpha characters (specifically not starting with "#" or "?")
                    for (elem = elem.firstChild; elem; elem = elem.nextSibling) {
                        if (elem.nodeName > "@" || elem.nodeType === 3 || elem.nodeType === 4) {
                            return false;
                        }
                    }
                    return true;
                },

                "parent": function (elem) {
                    return !Expr.pseudos["empty"](elem);
                },

                // Element/input types
                "header": function (elem) {
                    return rheader.test(elem.nodeName);
                },

                "input": function (elem) {
                    return rinputs.test(elem.nodeName);
                },

                "button": function (elem) {
                    var name = elem.nodeName.toLowerCase();
                    return name === "input" && elem.type === "button" || name === "button";
                },

                "text": function (elem) {
                    var attr;
                    // IE6 and 7 will map elem.type to 'text' for new HTML5 types (search, etc)
                    // use getAttribute instead to test this case
                    return elem.nodeName.toLowerCase() === "input" &&
                        elem.type === "text" &&
                        ( (attr = elem.getAttribute("type")) == null || attr.toLowerCase() === elem.type );
                },

                // Position-in-collection
                "first": createPositionalPseudo(function () {
                    return [ 0 ];
                }),

                "last": createPositionalPseudo(function (matchIndexes, length) {
                    return [ length - 1 ];
                }),

                "eq": createPositionalPseudo(function (matchIndexes, length, argument) {
                    return [ argument < 0 ? argument + length : argument ];
                }),

                "even": createPositionalPseudo(function (matchIndexes, length) {
                    var i = 0;
                    for (; i < length; i += 2) {
                        matchIndexes.push(i);
                    }
                    return matchIndexes;
                }),

                "odd": createPositionalPseudo(function (matchIndexes, length) {
                    var i = 1;
                    for (; i < length; i += 2) {
                        matchIndexes.push(i);
                    }
                    return matchIndexes;
                }),

                "lt": createPositionalPseudo(function (matchIndexes, length, argument) {
                    var i = argument < 0 ? argument + length : argument;
                    for (; --i >= 0;) {
                        matchIndexes.push(i);
                    }
                    return matchIndexes;
                }),

                "gt": createPositionalPseudo(function (matchIndexes, length, argument) {
                    var i = argument < 0 ? argument + length : argument;
                    for (; ++i < length;) {
                        matchIndexes.push(i);
                    }
                    return matchIndexes;
                })
            }
        };

// Add button/input type pseudos
        for (i in { radio: true, checkbox: true, file: true, password: true, image: true }) {
            Expr.pseudos[ i ] = createInputPseudo(i);
        }
        for (i in { submit: true, reset: true }) {
            Expr.pseudos[ i ] = createButtonPseudo(i);
        }

        function tokenize(selector, parseOnly) {
            var matched, match, tokens, type,
                soFar, groups, preFilters,
                cached = tokenCache[ selector + " " ];

            if (cached) {
                return parseOnly ? 0 : cached.slice(0);
            }

            soFar = selector;
            groups = [];
            preFilters = Expr.preFilter;

            while (soFar) {

                // Comma and first run
                if (!matched || (match = rcomma.exec(soFar))) {
                    if (match) {
                        // Don't consume trailing commas as valid
                        soFar = soFar.slice(match[0].length) || soFar;
                    }
                    groups.push(tokens = []);
                }

                matched = false;

                // Combinators
                if ((match = rcombinators.exec(soFar))) {
                    matched = match.shift();
                    tokens.push({
                        value: matched,
                        // Cast descendant combinators to space
                        type: match[0].replace(rtrim, " ")
                    });
                    soFar = soFar.slice(matched.length);
                }

                // Filters
                for (type in Expr.filter) {
                    if ((match = matchExpr[ type ].exec(soFar)) && (!preFilters[ type ] ||
                        (match = preFilters[ type ](match)))) {
                        matched = match.shift();
                        tokens.push({
                            value: matched,
                            type: type,
                            matches: match
                        });
                        soFar = soFar.slice(matched.length);
                    }
                }

                if (!matched) {
                    break;
                }
            }

            // Return the length of the invalid excess
            // if we're just parsing
            // Otherwise, throw an error or return tokens
            return parseOnly ?
                soFar.length :
                soFar ?
                    Sizzle.error(selector) :
                    // Cache the tokens
                    tokenCache(selector, groups).slice(0);
        }

        function toSelector(tokens) {
            var i = 0,
                len = tokens.length,
                selector = "";
            for (; i < len; i++) {
                selector += tokens[i].value;
            }
            return selector;
        }

        function addCombinator(matcher, combinator, base) {
            var dir = combinator.dir,
                checkNonElements = base && combinator.dir === "parentNode",
                doneName = done++;

            return combinator.first ?
                // Check against closest ancestor/preceding element
                function (elem, context, xml) {
                    while ((elem = elem[ dir ])) {
                        if (elem.nodeType === 1 || checkNonElements) {
                            return matcher(elem, context, xml);
                        }
                    }
                } :

                // Check against all ancestor/preceding elements
                function (elem, context, xml) {
                    var data, cache, outerCache,
                        dirkey = dirruns + " " + doneName;

                    // We can't set arbitrary data on XML nodes, so they don't benefit from dir caching
                    if (xml) {
                        while ((elem = elem[ dir ])) {
                            if (elem.nodeType === 1 || checkNonElements) {
                                if (matcher(elem, context, xml)) {
                                    return true;
                                }
                            }
                        }
                    } else {
                        while ((elem = elem[ dir ])) {
                            if (elem.nodeType === 1 || checkNonElements) {
                                outerCache = elem[ expando ] || (elem[ expando ] = {});
                                if ((cache = outerCache[ dir ]) && cache[0] === dirkey) {
                                    if ((data = cache[1]) === true || data === cachedruns) {
                                        return data === true;
                                    }
                                } else {
                                    cache = outerCache[ dir ] = [ dirkey ];
                                    cache[1] = matcher(elem, context, xml) || cachedruns;
                                    if (cache[1] === true) {
                                        return true;
                                    }
                                }
                            }
                        }
                    }
                };
        }

        function elementMatcher(matchers) {
            return matchers.length > 1 ?
                function (elem, context, xml) {
                    var i = matchers.length;
                    while (i--) {
                        if (!matchers[i](elem, context, xml)) {
                            return false;
                        }
                    }
                    return true;
                } :
                matchers[0];
        }

        function condense(unmatched, map, filter, context, xml) {
            var elem,
                newUnmatched = [],
                i = 0,
                len = unmatched.length,
                mapped = map != null;

            for (; i < len; i++) {
                if ((elem = unmatched[i])) {
                    if (!filter || filter(elem, context, xml)) {
                        newUnmatched.push(elem);
                        if (mapped) {
                            map.push(i);
                        }
                    }
                }
            }

            return newUnmatched;
        }

        function setMatcher(preFilter, selector, matcher, postFilter, postFinder, postSelector) {
            if (postFilter && !postFilter[ expando ]) {
                postFilter = setMatcher(postFilter);
            }
            if (postFinder && !postFinder[ expando ]) {
                postFinder = setMatcher(postFinder, postSelector);
            }
            return markFunction(function (seed, results, context, xml) {
                var temp, i, elem,
                    preMap = [],
                    postMap = [],
                    preexisting = results.length,

                // Get initial elements from seed or context
                    elems = seed || multipleContexts(selector || "*", context.nodeType ? [ context ] : context, []),

                // Prefilter to get matcher input, preserving a map for seed-results synchronization
                    matcherIn = preFilter && ( seed || !selector ) ?
                        condense(elems, preMap, preFilter, context, xml) :
                        elems,

                    matcherOut = matcher ?
                        // If we have a postFinder, or filtered seed, or non-seed postFilter or preexisting results,
                        postFinder || ( seed ? preFilter : preexisting || postFilter ) ?

                            // ...intermediate processing is necessary
                            [] :

                            // ...otherwise use results directly
                            results :
                        matcherIn;

                // Find primary matches
                if (matcher) {
                    matcher(matcherIn, matcherOut, context, xml);
                }

                // Apply postFilter
                if (postFilter) {
                    temp = condense(matcherOut, postMap);
                    postFilter(temp, [], context, xml);

                    // Un-match failing elements by moving them back to matcherIn
                    i = temp.length;
                    while (i--) {
                        if ((elem = temp[i])) {
                            matcherOut[ postMap[i] ] = !(matcherIn[ postMap[i] ] = elem);
                        }
                    }
                }

                if (seed) {
                    if (postFinder || preFilter) {
                        if (postFinder) {
                            // Get the final matcherOut by condensing this intermediate into postFinder contexts
                            temp = [];
                            i = matcherOut.length;
                            while (i--) {
                                if ((elem = matcherOut[i])) {
                                    // Restore matcherIn since elem is not yet a final match
                                    temp.push((matcherIn[i] = elem));
                                }
                            }
                            postFinder(null, (matcherOut = []), temp, xml);
                        }

                        // Move matched elements from seed to results to keep them synchronized
                        i = matcherOut.length;
                        while (i--) {
                            if ((elem = matcherOut[i]) &&
                                (temp = postFinder ? indexOf.call(seed, elem) : preMap[i]) > -1) {

                                seed[temp] = !(results[temp] = elem);
                            }
                        }
                    }

                    // Add elements to results, through postFinder if defined
                } else {
                    matcherOut = condense(
                        matcherOut === results ?
                            matcherOut.splice(preexisting, matcherOut.length) :
                            matcherOut
                    );
                    if (postFinder) {
                        postFinder(null, results, matcherOut, xml);
                    } else {
                        push.apply(results, matcherOut);
                    }
                }
            });
        }

        function matcherFromTokens(tokens) {
            var checkContext, matcher, j,
                len = tokens.length,
                leadingRelative = Expr.relative[ tokens[0].type ],
                implicitRelative = leadingRelative || Expr.relative[" "],
                i = leadingRelative ? 1 : 0,

            // The foundational matcher ensures that elements are reachable from top-level context(s)
                matchContext = addCombinator(function (elem) {
                    return elem === checkContext;
                }, implicitRelative, true),
                matchAnyContext = addCombinator(function (elem) {
                    return indexOf.call(checkContext, elem) > -1;
                }, implicitRelative, true),
                matchers = [ function (elem, context, xml) {
                    return ( !leadingRelative && ( xml || context !== outermostContext ) ) || (
                        (checkContext = context).nodeType ?
                            matchContext(elem, context, xml) :
                            matchAnyContext(elem, context, xml) );
                } ];

            for (; i < len; i++) {
                if ((matcher = Expr.relative[ tokens[i].type ])) {
                    matchers = [ addCombinator(elementMatcher(matchers), matcher) ];
                } else {
                    matcher = Expr.filter[ tokens[i].type ].apply(null, tokens[i].matches);

                    // Return special upon seeing a positional matcher
                    if (matcher[ expando ]) {
                        // Find the next relative operator (if any) for proper handling
                        j = ++i;
                        for (; j < len; j++) {
                            if (Expr.relative[ tokens[j].type ]) {
                                break;
                            }
                        }
                        return setMatcher(
                            i > 1 && elementMatcher(matchers),
                            i > 1 && toSelector(tokens.slice(0, i - 1)).replace(rtrim, "$1"),
                            matcher,
                            i < j && matcherFromTokens(tokens.slice(i, j)),
                            j < len && matcherFromTokens((tokens = tokens.slice(j))),
                            j < len && toSelector(tokens)
                        );
                    }
                    matchers.push(matcher);
                }
            }

            return elementMatcher(matchers);
        }

        function matcherFromGroupMatchers(elementMatchers, setMatchers) {
            // A counter to specify which element is currently being matched
            var matcherCachedRuns = 0,
                bySet = setMatchers.length > 0,
                byElement = elementMatchers.length > 0,
                superMatcher = function (seed, context, xml, results, expandContext) {
                    var elem, j, matcher,
                        setMatched = [],
                        matchedCount = 0,
                        i = "0",
                        unmatched = seed && [],
                        outermost = expandContext != null,
                        contextBackup = outermostContext,
                    // We must always have either seed elements or context
                        elems = seed || byElement && Expr.find["TAG"]("*", expandContext && context.parentNode || context),
                    // Nested matchers should use non-integer dirruns
                        dirrunsUnique = (dirruns += contextBackup == null ? 1 : Math.E);

                    if (outermost) {
                        outermostContext = context !== document && context;
                        cachedruns = matcherCachedRuns;
                    }

                    // Add elements passing elementMatchers directly to results
                    for (; (elem = elems[i]) != null; i++) {
                        if (byElement && elem) {
                            for (j = 0; (matcher = elementMatchers[j]); j++) {
                                if (matcher(elem, context, xml)) {
                                    results.push(elem);
                                    break;
                                }
                            }
                            if (outermost) {
                                dirruns = dirrunsUnique;
                                cachedruns = ++matcherCachedRuns;
                            }
                        }

                        // Track unmatched elements for set filters
                        if (bySet) {
                            // They will have gone through all possible matchers
                            if ((elem = !matcher && elem)) {
                                matchedCount--;
                            }

                            // Lengthen the array for every element, matched or not
                            if (seed) {
                                unmatched.push(elem);
                            }
                        }
                    }

                    // Apply set filters to unmatched elements
                    // `i` starts as a string, so matchedCount would equal "00" if there are no elements
                    matchedCount += i;
                    if (bySet && i !== matchedCount) {
                        for (j = 0; (matcher = setMatchers[j]); j++) {
                            matcher(unmatched, setMatched, context, xml);
                        }

                        if (seed) {
                            // Reintegrate element matches to eliminate the need for sorting
                            if (matchedCount > 0) {
                                while (i--) {
                                    if (!(unmatched[i] || setMatched[i])) {
                                        setMatched[i] = pop.call(results);
                                    }
                                }
                            }

                            // Discard index placeholder values to get only actual matches
                            setMatched = condense(setMatched);
                        }

                        // Add matches to results
                        push.apply(results, setMatched);

                        // Seedless set matches succeeding multiple successful matchers stipulate sorting
                        if (outermost && !seed && setMatched.length > 0 &&
                            ( matchedCount + setMatchers.length ) > 1) {

                            Sizzle.uniqueSort(results);
                        }
                    }

                    // Override manipulation of globals by nested matchers
                    if (outermost) {
                        dirruns = dirrunsUnique;
                        outermostContext = contextBackup;
                    }

                    return unmatched;
                };

            return bySet ?
                markFunction(superMatcher) :
                superMatcher;
        }

        compile = Sizzle.compile = function (selector, group /* Internal Use Only */) {
            var i,
                setMatchers = [],
                elementMatchers = [],
                cached = compilerCache[ selector + " " ];

            if (!cached) {
                // Generate a function of recursive functions that can be used to check each element
                if (!group) {
                    group = tokenize(selector);
                }
                i = group.length;
                while (i--) {
                    cached = matcherFromTokens(group[i]);
                    if (cached[ expando ]) {
                        setMatchers.push(cached);
                    } else {
                        elementMatchers.push(cached);
                    }
                }

                // Cache the compiled function
                cached = compilerCache(selector, matcherFromGroupMatchers(elementMatchers, setMatchers));
            }
            return cached;
        };

        function multipleContexts(selector, contexts, results) {
            var i = 0,
                len = contexts.length;
            for (; i < len; i++) {
                Sizzle(selector, contexts[i], results);
            }
            return results;
        }

        function select(selector, context, results, seed) {
            var i, tokens, token, type, find,
                match = tokenize(selector);

            if (!seed) {
                // Try to minimize operations if there is only one group
                if (match.length === 1) {

                    // Take a shortcut and set the context if the root selector is an ID
                    tokens = match[0] = match[0].slice(0);
                    if (tokens.length > 2 && (token = tokens[0]).type === "ID" &&
                        context.nodeType === 9 && !documentIsXML &&
                        Expr.relative[ tokens[1].type ]) {

                        context = Expr.find["ID"](token.matches[0].replace(runescape, funescape), context)[0];
                        if (!context) {
                            return results;
                        }

                        selector = selector.slice(tokens.shift().value.length);
                    }

                    // Fetch a seed set for right-to-left matching
                    for (i = matchExpr["needsContext"].test(selector) ? -1 : tokens.length - 1; i >= 0; i--) {
                        token = tokens[i];

                        // Abort if we hit a combinator
                        if (Expr.relative[ (type = token.type) ]) {
                            break;
                        }
                        if ((find = Expr.find[ type ])) {
                            // Search, expanding context for leading sibling combinators
                            if ((seed = find(
                                token.matches[0].replace(runescape, funescape),
                                rsibling.test(tokens[0].type) && context.parentNode || context
                            ))) {

                                // If seed is empty or no tokens remain, we can return early
                                tokens.splice(i, 1);
                                selector = seed.length && toSelector(tokens);
                                if (!selector) {
                                    push.apply(results, slice.call(seed, 0));
                                    return results;
                                }

                                break;
                            }
                        }
                    }
                }
            }

            // Compile and execute a filtering function
            // Provide `match` to avoid retokenization if we modified the selector above
            compile(selector, match)(
                seed,
                context,
                documentIsXML,
                results,
                rsibling.test(selector)
            );
            return results;
        }

// Deprecated
        Expr.pseudos["nth"] = Expr.pseudos["eq"];

// Easy API for creating new setFilters
        function setFilters() {
        }

        Expr.filters = setFilters.prototype = Expr.pseudos;
        Expr.setFilters = new setFilters();

// Initialize with the default document
        setDocument();

// Override sizzle attribute retrieval
        Sizzle.attr = jQuery.attr;
        jQuery.find = Sizzle;
        jQuery.expr = Sizzle.selectors;
        jQuery.expr[":"] = jQuery.expr.pseudos;
        jQuery.unique = Sizzle.uniqueSort;
        jQuery.text = Sizzle.getText;
        jQuery.isXMLDoc = Sizzle.isXML;
        jQuery.contains = Sizzle.contains;


    })(window);
    var runtil = /Until$/,
        rparentsprev = /^(?:parents|prev(?:Until|All))/,
        isSimple = /^.[^:#\[\.,]*$/,
        rneedsContext = jQuery.expr.match.needsContext,
    // methods guaranteed to produce a unique set when starting from a unique set
        guaranteedUnique = {
            children: true,
            contents: true,
            next: true,
            prev: true
        };

    jQuery.fn.extend({
        find: function (selector) {
            var i, ret, self;

            if (typeof selector !== "string") {
                self = this;
                return this.pushStack(jQuery(selector).filter(function () {
                    for (i = 0; i < self.length; i++) {
                        if (jQuery.contains(self[ i ], this)) {
                            return true;
                        }
                    }
                }));
            }

            ret = [];
            for (i = 0; i < this.length; i++) {
                jQuery.find(selector, this[ i ], ret);
            }

            // Needed because $( selector, context ) becomes $( context ).find( selector )
            ret = this.pushStack(jQuery.unique(ret));
            ret.selector = ( this.selector ? this.selector + " " : "" ) + selector;
            return ret;
        },

        has: function (target) {
            var i,
                targets = jQuery(target, this),
                len = targets.length;

            return this.filter(function () {
                for (i = 0; i < len; i++) {
                    if (jQuery.contains(this, targets[i])) {
                        return true;
                    }
                }
            });
        },

        not: function (selector) {
            return this.pushStack(winnow(this, selector, false));
        },

        filter: function (selector) {
            return this.pushStack(winnow(this, selector, true));
        },

        is: function (selector) {
            return !!selector && (
                typeof selector === "string" ?
                    // If this is a positional/relative selector, check membership in the returned set
                    // so $("p:first").is("p:last") won't return true for a doc with two "p".
                    rneedsContext.test(selector) ?
                        jQuery(selector, this.context).index(this[0]) >= 0 :
                        jQuery.filter(selector, this).length > 0 :
                    this.filter(selector).length > 0 );
        },

        closest: function (selectors, context) {
            var cur,
                i = 0,
                l = this.length,
                ret = [],
                pos = rneedsContext.test(selectors) || typeof selectors !== "string" ?
                    jQuery(selectors, context || this.context) :
                    0;

            for (; i < l; i++) {
                cur = this[i];

                while (cur && cur.ownerDocument && cur !== context && cur.nodeType !== 11) {
                    if (pos ? pos.index(cur) > -1 : jQuery.find.matchesSelector(cur, selectors)) {
                        ret.push(cur);
                        break;
                    }
                    cur = cur.parentNode;
                }
            }

            return this.pushStack(ret.length > 1 ? jQuery.unique(ret) : ret);
        },

        // Determine the position of an element within
        // the matched set of elements
        index: function (elem) {

            // No argument, return index in parent
            if (!elem) {
                return ( this[0] && this[0].parentNode ) ? this.first().prevAll().length : -1;
            }

            // index in selector
            if (typeof elem === "string") {
                return jQuery.inArray(this[0], jQuery(elem));
            }

            // Locate the position of the desired element
            return jQuery.inArray(
                // If it receives a jQuery object, the first element is used
                elem.jquery ? elem[0] : elem, this);
        },

        add: function (selector, context) {
            var set = typeof selector === "string" ?
                    jQuery(selector, context) :
                    jQuery.makeArray(selector && selector.nodeType ? [ selector ] : selector),
                all = jQuery.merge(this.get(), set);

            return this.pushStack(jQuery.unique(all));
        },

        addBack: function (selector) {
            return this.add(selector == null ?
                this.prevObject : this.prevObject.filter(selector)
            );
        }
    });

    jQuery.fn.andSelf = jQuery.fn.addBack;

    function sibling(cur, dir) {
        do {
            cur = cur[ dir ];
        } while (cur && cur.nodeType !== 1);

        return cur;
    }

    jQuery.each({
        parent: function (elem) {
            var parent = elem.parentNode;
            return parent && parent.nodeType !== 11 ? parent : null;
        },
        parents: function (elem) {
            return jQuery.dir(elem, "parentNode");
        },
        parentsUntil: function (elem, i, until) {
            return jQuery.dir(elem, "parentNode", until);
        },
        next: function (elem) {
            return sibling(elem, "nextSibling");
        },
        prev: function (elem) {
            return sibling(elem, "previousSibling");
        },
        nextAll: function (elem) {
            return jQuery.dir(elem, "nextSibling");
        },
        prevAll: function (elem) {
            return jQuery.dir(elem, "previousSibling");
        },
        nextUntil: function (elem, i, until) {
            return jQuery.dir(elem, "nextSibling", until);
        },
        prevUntil: function (elem, i, until) {
            return jQuery.dir(elem, "previousSibling", until);
        },
        siblings: function (elem) {
            return jQuery.sibling(( elem.parentNode || {} ).firstChild, elem);
        },
        children: function (elem) {
            return jQuery.sibling(elem.firstChild);
        },
        contents: function (elem) {
            return jQuery.nodeName(elem, "iframe") ?
                elem.contentDocument || elem.contentWindow.document :
                jQuery.merge([], elem.childNodes);
        }
    }, function (name, fn) {
        jQuery.fn[ name ] = function (until, selector) {
            var ret = jQuery.map(this, fn, until);

            if (!runtil.test(name)) {
                selector = until;
            }

            if (selector && typeof selector === "string") {
                ret = jQuery.filter(selector, ret);
            }

            ret = this.length > 1 && !guaranteedUnique[ name ] ? jQuery.unique(ret) : ret;

            if (this.length > 1 && rparentsprev.test(name)) {
                ret = ret.reverse();
            }

            return this.pushStack(ret);
        };
    });

    jQuery.extend({
        filter: function (expr, elems, not) {
            if (not) {
                expr = ":not(" + expr + ")";
            }

            return elems.length === 1 ?
                jQuery.find.matchesSelector(elems[0], expr) ? [ elems[0] ] : [] :
                jQuery.find.matches(expr, elems);
        },

        dir: function (elem, dir, until) {
            var matched = [],
                cur = elem[ dir ];

            while (cur && cur.nodeType !== 9 && (until === undefined || cur.nodeType !== 1 || !jQuery(cur).is(until))) {
                if (cur.nodeType === 1) {
                    matched.push(cur);
                }
                cur = cur[dir];
            }
            return matched;
        },

        sibling: function (n, elem) {
            var r = [];

            for (; n; n = n.nextSibling) {
                if (n.nodeType === 1 && n !== elem) {
                    r.push(n);
                }
            }

            return r;
        }
    });

// Implement the identical functionality for filter and not
    function winnow(elements, qualifier, keep) {

        // Can't pass null or undefined to indexOf in Firefox 4
        // Set to 0 to skip string check
        qualifier = qualifier || 0;

        if (jQuery.isFunction(qualifier)) {
            return jQuery.grep(elements, function (elem, i) {
                var retVal = !!qualifier.call(elem, i, elem);
                return retVal === keep;
            });

        } else if (qualifier.nodeType) {
            return jQuery.grep(elements, function (elem) {
                return ( elem === qualifier ) === keep;
            });

        } else if (typeof qualifier === "string") {
            var filtered = jQuery.grep(elements, function (elem) {
                return elem.nodeType === 1;
            });

            if (isSimple.test(qualifier)) {
                return jQuery.filter(qualifier, filtered, !keep);
            } else {
                qualifier = jQuery.filter(qualifier, filtered);
            }
        }

        return jQuery.grep(elements, function (elem) {
            return ( jQuery.inArray(elem, qualifier) >= 0 ) === keep;
        });
    }

    function createSafeFragment(document) {
        var list = nodeNames.split("|"),
            safeFrag = document.createDocumentFragment();

        if (safeFrag.createElement) {
            while (list.length) {
                safeFrag.createElement(
                    list.pop()
                );
            }
        }
        return safeFrag;
    }

    var nodeNames = "abbr|article|aside|audio|bdi|canvas|data|datalist|details|figcaption|figure|footer|" +
            "header|hgroup|mark|meter|nav|output|progress|section|summary|time|video",
        rinlinejQuery = / jQuery\d+="(?:null|\d+)"/g,
        rnoshimcache = new RegExp("<(?:" + nodeNames + ")[\\s/>]", "i"),
        rleadingWhitespace = /^\s+/,
        rxhtmlTag = /<(?!area|br|col|embed|hr|img|input|link|meta|param)(([\w:]+)[^>]*)\/>/gi,
        rtagName = /<([\w:]+)/,
        rtbody = /<tbody/i,
        rhtml = /<|&#?\w+;/,
        rnoInnerhtml = /<(?:script|style|link)/i,
        manipulation_rcheckableType = /^(?:checkbox|radio)$/i,
    // checked="checked" or checked
        rchecked = /checked\s*(?:[^=]|=\s*.checked.)/i,
        rscriptType = /^$|\/(?:java|ecma)script/i,
        rscriptTypeMasked = /^true\/(.*)/,
        rcleanScript = /^\s*<!(?:\[CDATA\[|--)|(?:\]\]|--)>\s*$/g,

    // We have to close these tags to support XHTML (#13200)
        wrapMap = {
            option: [ 1, "<select multiple='multiple'>", "</select>" ],
            legend: [ 1, "<fieldset>", "</fieldset>" ],
            area: [ 1, "<map>", "</map>" ],
            param: [ 1, "<object>", "</object>" ],
            thead: [ 1, "<table>", "</table>" ],
            tr: [ 2, "<table><tbody>", "</tbody></table>" ],
            col: [ 2, "<table><tbody></tbody><colgroup>", "</colgroup></table>" ],
            td: [ 3, "<table><tbody><tr>", "</tr></tbody></table>" ],

            // IE6-8 can't serialize link, script, style, or any html5 (NoScope) tags,
            // unless wrapped in a div with non-breaking characters in front of it.
            _default: jQuery.support.htmlSerialize ? [ 0, "", "" ] : [ 1, "X<div>", "</div>"  ]
        },
        safeFragment = createSafeFragment(document),
        fragmentDiv = safeFragment.appendChild(document.createElement("div"));

    wrapMap.optgroup = wrapMap.option;
    wrapMap.tbody = wrapMap.tfoot = wrapMap.colgroup = wrapMap.caption = wrapMap.thead;
    wrapMap.th = wrapMap.td;

    jQuery.fn.extend({
        text: function (value) {
            return jQuery.access(this, function (value) {
                return value === undefined ?
                    jQuery.text(this) :
                    this.empty().append(( this[0] && this[0].ownerDocument || document ).createTextNode(value));
            }, null, value, arguments.length);
        },

        wrapAll: function (html) {
            if (jQuery.isFunction(html)) {
                return this.each(function (i) {
                    jQuery(this).wrapAll(html.call(this, i));
                });
            }

            if (this[0]) {
                // The elements to wrap the target around
                var wrap = jQuery(html, this[0].ownerDocument).eq(0).clone(true);

                if (this[0].parentNode) {
                    wrap.insertBefore(this[0]);
                }

                wrap.map(function () {
                    var elem = this;

                    while (elem.firstChild && elem.firstChild.nodeType === 1) {
                        elem = elem.firstChild;
                    }

                    return elem;
                }).append(this);
            }

            return this;
        },

        wrapInner: function (html) {
            if (jQuery.isFunction(html)) {
                return this.each(function (i) {
                    jQuery(this).wrapInner(html.call(this, i));
                });
            }

            return this.each(function () {
                var self = jQuery(this),
                    contents = self.contents();

                if (contents.length) {
                    contents.wrapAll(html);

                } else {
                    self.append(html);
                }
            });
        },

        wrap: function (html) {
            var isFunction = jQuery.isFunction(html);

            return this.each(function (i) {
                jQuery(this).wrapAll(isFunction ? html.call(this, i) : html);
            });
        },

        unwrap: function () {
            return this.parent().each(function () {
                if (!jQuery.nodeName(this, "body")) {
                    jQuery(this).replaceWith(this.childNodes);
                }
            }).end();
        },

        append: function () {
            return this.domManip(arguments, true, function (elem) {
                if (this.nodeType === 1 || this.nodeType === 11 || this.nodeType === 9) {
                    this.appendChild(elem);
                }
            });
        },

        prepend: function () {
            return this.domManip(arguments, true, function (elem) {
                if (this.nodeType === 1 || this.nodeType === 11 || this.nodeType === 9) {
                    this.insertBefore(elem, this.firstChild);
                }
            });
        },

        before: function () {
            return this.domManip(arguments, false, function (elem) {
                if (this.parentNode) {
                    this.parentNode.insertBefore(elem, this);
                }
            });
        },

        after: function () {
            return this.domManip(arguments, false, function (elem) {
                if (this.parentNode) {
                    this.parentNode.insertBefore(elem, this.nextSibling);
                }
            });
        },

        // keepData is for internal use only--do not document
        remove: function (selector, keepData) {
            var elem,
                i = 0;

            for (; (elem = this[i]) != null; i++) {
                if (!selector || jQuery.filter(selector, [ elem ]).length > 0) {
                    if (!keepData && elem.nodeType === 1) {
                        jQuery.cleanData(getAll(elem));
                    }

                    if (elem.parentNode) {
                        if (keepData && jQuery.contains(elem.ownerDocument, elem)) {
                            setGlobalEval(getAll(elem, "script"));
                        }
                        elem.parentNode.removeChild(elem);
                    }
                }
            }

            return this;
        },

        empty: function () {
            var elem,
                i = 0;

            for (; (elem = this[i]) != null; i++) {
                // Remove element nodes and prevent memory leaks
                if (elem.nodeType === 1) {
                    jQuery.cleanData(getAll(elem, false));
                }

                // Remove any remaining nodes
                while (elem.firstChild) {
                    elem.removeChild(elem.firstChild);
                }

                // If this is a select, ensure that it displays empty (#12336)
                // Support: IE<9
                if (elem.options && jQuery.nodeName(elem, "select")) {
                    elem.options.length = 0;
                }
            }

            return this;
        },

        clone: function (dataAndEvents, deepDataAndEvents) {
            dataAndEvents = dataAndEvents == null ? false : dataAndEvents;
            deepDataAndEvents = deepDataAndEvents == null ? dataAndEvents : deepDataAndEvents;

            return this.map(function () {
                return jQuery.clone(this, dataAndEvents, deepDataAndEvents);
            });
        },

        html: function (value) {
            return jQuery.access(this, function (value) {
                var elem = this[0] || {},
                    i = 0,
                    l = this.length;

                if (value === undefined) {
                    return elem.nodeType === 1 ?
                        elem.innerHTML.replace(rinlinejQuery, "") :
                        undefined;
                }

                // See if we can take a shortcut and just use innerHTML
                if (typeof value === "string" && !rnoInnerhtml.test(value) &&
                    ( jQuery.support.htmlSerialize || !rnoshimcache.test(value)  ) &&
                    ( jQuery.support.leadingWhitespace || !rleadingWhitespace.test(value) ) && !wrapMap[ ( rtagName.exec(value) || ["", ""] )[1].toLowerCase() ]) {

                    value = value.replace(rxhtmlTag, "<$1></$2>");

                    try {
                        for (; i < l; i++) {
                            // Remove element nodes and prevent memory leaks
                            elem = this[i] || {};
                            if (elem.nodeType === 1) {
                                jQuery.cleanData(getAll(elem, false));
                                elem.innerHTML = value;
                            }
                        }

                        elem = 0;

                        // If using innerHTML throws an exception, use the fallback method
                    } catch (e) {
                    }
                }

                if (elem) {
                    this.empty().append(value);
                }
            }, null, value, arguments.length);
        },

        replaceWith: function (value) {
            var isFunc = jQuery.isFunction(value);

            // Make sure that the elements are removed from the DOM before they are inserted
            // this can help fix replacing a parent with child elements
            if (!isFunc && typeof value !== "string") {
                value = jQuery(value).not(this).detach();
            }

            return this.domManip([ value ], true, function (elem) {
                var next = this.nextSibling,
                    parent = this.parentNode;

                if (parent && this.nodeType === 1 || this.nodeType === 11) {

                    jQuery(this).remove();

                    if (next) {
                        next.parentNode.insertBefore(elem, next);
                    } else {
                        parent.appendChild(elem);
                    }
                }
            });
        },

        detach: function (selector) {
            return this.remove(selector, true);
        },

        domManip: function (args, table, callback) {

            // Flatten any nested arrays
            args = core_concat.apply([], args);

            var fragment, first, scripts, hasScripts, node, doc,
                i = 0,
                l = this.length,
                set = this,
                iNoClone = l - 1,
                value = args[0],
                isFunction = jQuery.isFunction(value);

            // We can't cloneNode fragments that contain checked, in WebKit
            if (isFunction || !( l <= 1 || typeof value !== "string" || jQuery.support.checkClone || !rchecked.test(value) )) {
                return this.each(function (index) {
                    var self = set.eq(index);
                    if (isFunction) {
                        args[0] = value.call(this, index, table ? self.html() : undefined);
                    }
                    self.domManip(args, table, callback);
                });
            }

            if (l) {
                fragment = jQuery.buildFragment(args, this[ 0 ].ownerDocument, false, this);
                first = fragment.firstChild;

                if (fragment.childNodes.length === 1) {
                    fragment = first;
                }

                if (first) {
                    table = table && jQuery.nodeName(first, "tr");
                    scripts = jQuery.map(getAll(fragment, "script"), disableScript);
                    hasScripts = scripts.length;

                    // Use the original fragment for the last item instead of the first because it can end up
                    // being emptied incorrectly in certain situations (#8070).
                    for (; i < l; i++) {
                        node = fragment;

                        if (i !== iNoClone) {
                            node = jQuery.clone(node, true, true);

                            // Keep references to cloned scripts for later restoration
                            if (hasScripts) {
                                jQuery.merge(scripts, getAll(node, "script"));
                            }
                        }

                        callback.call(
                            table && jQuery.nodeName(this[i], "table") ?
                                findOrAppend(this[i], "tbody") :
                                this[i],
                            node,
                            i
                        );
                    }

                    if (hasScripts) {
                        doc = scripts[ scripts.length - 1 ].ownerDocument;

                        // Reenable scripts
                        jQuery.map(scripts, restoreScript);

                        // Evaluate executable scripts on first document insertion
                        for (i = 0; i < hasScripts; i++) {
                            node = scripts[ i ];
                            if (rscriptType.test(node.type || "") && !jQuery._data(node, "globalEval") && jQuery.contains(doc, node)) {

                                if (node.src) {
                                    // Hope ajax is available...
                                    jQuery.ajax({
                                        url: node.src,
                                        type: "GET",
                                        dataType: "script",
                                        async: false,
                                        global: false,
                                        "throws": true
                                    });
                                } else {
                                    jQuery.globalEval(( node.text || node.textContent || node.innerHTML || "" ).replace(rcleanScript, ""));
                                }
                            }
                        }
                    }

                    // Fix #11809: Avoid leaking memory
                    fragment = first = null;
                }
            }

            return this;
        }
    });

    function findOrAppend(elem, tag) {
        return elem.getElementsByTagName(tag)[0] || elem.appendChild(elem.ownerDocument.createElement(tag));
    }

// Replace/restore the type attribute of script elements for safe DOM manipulation
    function disableScript(elem) {
        var attr = elem.getAttributeNode("type");
        elem.type = ( attr && attr.specified ) + "/" + elem.type;
        return elem;
    }

    function restoreScript(elem) {
        var match = rscriptTypeMasked.exec(elem.type);
        if (match) {
            elem.type = match[1];
        } else {
            elem.removeAttribute("type");
        }
        return elem;
    }

// Mark scripts as having already been evaluated
    function setGlobalEval(elems, refElements) {
        var elem,
            i = 0;
        for (; (elem = elems[i]) != null; i++) {
            jQuery._data(elem, "globalEval", !refElements || jQuery._data(refElements[i], "globalEval"));
        }
    }

    function cloneCopyEvent(src, dest) {

        if (dest.nodeType !== 1 || !jQuery.hasData(src)) {
            return;
        }

        var type, i, l,
            oldData = jQuery._data(src),
            curData = jQuery._data(dest, oldData),
            events = oldData.events;

        if (events) {
            delete curData.handle;
            curData.events = {};

            for (type in events) {
                for (i = 0, l = events[ type ].length; i < l; i++) {
                    jQuery.event.add(dest, type, events[ type ][ i ]);
                }
            }
        }

        // make the cloned public data object a copy from the original
        if (curData.data) {
            curData.data = jQuery.extend({}, curData.data);
        }
    }

    function fixCloneNodeIssues(src, dest) {
        var nodeName, data, e;

        // We do not need to do anything for non-Elements
        if (dest.nodeType !== 1) {
            return;
        }

        nodeName = dest.nodeName.toLowerCase();

        // IE6-8 copies events bound via attachEvent when using cloneNode.
        if (!jQuery.support.noCloneEvent && dest[ jQuery.expando ]) {
            data = jQuery._data(dest);

            for (e in data.events) {
                jQuery.removeEvent(dest, e, data.handle);
            }

            // Event data gets referenced instead of copied if the expando gets copied too
            dest.removeAttribute(jQuery.expando);
        }

        // IE blanks contents when cloning scripts, and tries to evaluate newly-set text
        if (nodeName === "script" && dest.text !== src.text) {
            disableScript(dest).text = src.text;
            restoreScript(dest);

            // IE6-10 improperly clones children of object elements using classid.
            // IE10 throws NoModificationAllowedError if parent is null, #12132.
        } else if (nodeName === "object") {
            if (dest.parentNode) {
                dest.outerHTML = src.outerHTML;
            }

            // This path appears unavoidable for IE9. When cloning an object
            // element in IE9, the outerHTML strategy above is not sufficient.
            // If the src has innerHTML and the destination does not,
            // copy the src.innerHTML into the dest.innerHTML. #10324
            if (jQuery.support.html5Clone && ( src.innerHTML && !jQuery.trim(dest.innerHTML) )) {
                dest.innerHTML = src.innerHTML;
            }

        } else if (nodeName === "input" && manipulation_rcheckableType.test(src.type)) {
            // IE6-8 fails to persist the checked state of a cloned checkbox
            // or radio button. Worse, IE6-7 fail to give the cloned element
            // a checked appearance if the defaultChecked value isn't also set

            dest.defaultChecked = dest.checked = src.checked;

            // IE6-7 get confused and end up setting the value of a cloned
            // checkbox/radio button to an empty string instead of "on"
            if (dest.value !== src.value) {
                dest.value = src.value;
            }

            // IE6-8 fails to return the selected option to the default selected
            // state when cloning options
        } else if (nodeName === "option") {
            dest.defaultSelected = dest.selected = src.defaultSelected;

            // IE6-8 fails to set the defaultValue to the correct value when
            // cloning other types of input fields
        } else if (nodeName === "input" || nodeName === "textarea") {
            dest.defaultValue = src.defaultValue;
        }
    }

    jQuery.each({
        appendTo: "append",
        prependTo: "prepend",
        insertBefore: "before",
        insertAfter: "after",
        replaceAll: "replaceWith"
    }, function (name, original) {
        jQuery.fn[ name ] = function (selector) {
            var elems,
                i = 0,
                ret = [],
                insert = jQuery(selector),
                last = insert.length - 1;

            for (; i <= last; i++) {
                elems = i === last ? this : this.clone(true);
                jQuery(insert[i])[ original ](elems);

                // Modern browsers can apply jQuery collections as arrays, but oldIE needs a .get()
                core_push.apply(ret, elems.get());
            }

            return this.pushStack(ret);
        };
    });

    function getAll(context, tag) {
        var elems, elem,
            i = 0,
            found = typeof context.getElementsByTagName !== "undefined" ? context.getElementsByTagName(tag || "*") :
                typeof context.querySelectorAll !== "undefined" ? context.querySelectorAll(tag || "*") :
                    undefined;

        if (!found) {
            for (found = [], elems = context.childNodes || context; (elem = elems[i]) != null; i++) {
                if (!tag || jQuery.nodeName(elem, tag)) {
                    found.push(elem);
                } else {
                    jQuery.merge(found, getAll(elem, tag));
                }
            }
        }

        return tag === undefined || tag && jQuery.nodeName(context, tag) ?
            jQuery.merge([ context ], found) :
            found;
    }

// Used in buildFragment, fixes the defaultChecked property
    function fixDefaultChecked(elem) {
        if (manipulation_rcheckableType.test(elem.type)) {
            elem.defaultChecked = elem.checked;
        }
    }

    jQuery.extend({
        clone: function (elem, dataAndEvents, deepDataAndEvents) {
            var destElements, srcElements, node, i, clone,
                inPage = jQuery.contains(elem.ownerDocument, elem);

            if (jQuery.support.html5Clone || jQuery.isXMLDoc(elem) || !rnoshimcache.test("<" + elem.nodeName + ">")) {
                clone = elem.cloneNode(true);

                // IE<=8 does not properly clone detached, unknown element nodes
            } else {
                fragmentDiv.innerHTML = elem.outerHTML;
                fragmentDiv.removeChild(clone = fragmentDiv.firstChild);
            }

            if ((!jQuery.support.noCloneEvent || !jQuery.support.noCloneChecked) &&
                (elem.nodeType === 1 || elem.nodeType === 11) && !jQuery.isXMLDoc(elem)) {

                // We eschew Sizzle here for performance reasons: http://jsperf.com/getall-vs-sizzle/2
                destElements = getAll(clone);
                srcElements = getAll(elem);

                // Fix all IE cloning issues
                for (i = 0; (node = srcElements[i]) != null; ++i) {
                    // Ensure that the destination node is not null; Fixes #9587
                    if (destElements[i]) {
                        fixCloneNodeIssues(node, destElements[i]);
                    }
                }
            }

            // Copy the events from the original to the clone
            if (dataAndEvents) {
                if (deepDataAndEvents) {
                    srcElements = srcElements || getAll(elem);
                    destElements = destElements || getAll(clone);

                    for (i = 0; (node = srcElements[i]) != null; i++) {
                        cloneCopyEvent(node, destElements[i]);
                    }
                } else {
                    cloneCopyEvent(elem, clone);
                }
            }

            // Preserve script evaluation history
            destElements = getAll(clone, "script");
            if (destElements.length > 0) {
                setGlobalEval(destElements, !inPage && getAll(elem, "script"));
            }

            destElements = srcElements = node = null;

            // Return the cloned set
            return clone;
        },

        buildFragment: function (elems, context, scripts, selection) {
            var contains, elem, tag, tmp, wrap, tbody, j,
                l = elems.length,

            // Ensure a safe fragment
                safe = createSafeFragment(context),

                nodes = [],
                i = 0;

            for (; i < l; i++) {
                elem = elems[ i ];

                if (elem || elem === 0) {

                    // Add nodes directly
                    if (jQuery.type(elem) === "object") {
                        jQuery.merge(nodes, elem.nodeType ? [ elem ] : elem);

                        // Convert non-html into a text node
                    } else if (!rhtml.test(elem)) {
                        nodes.push(context.createTextNode(elem));

                        // Convert html into DOM nodes
                    } else {
                        tmp = tmp || safe.appendChild(context.createElement("div"));

                        // Deserialize a standard representation
                        tag = ( rtagName.exec(elem) || ["", ""] )[1].toLowerCase();
                        wrap = wrapMap[ tag ] || wrapMap._default;

                        tmp.innerHTML = wrap[1] + elem.replace(rxhtmlTag, "<$1></$2>") + wrap[2];

                        // Descend through wrappers to the right content
                        j = wrap[0];
                        while (j--) {
                            tmp = tmp.lastChild;
                        }

                        // Manually add leading whitespace removed by IE
                        if (!jQuery.support.leadingWhitespace && rleadingWhitespace.test(elem)) {
                            nodes.push(context.createTextNode(rleadingWhitespace.exec(elem)[0]));
                        }

                        // Remove IE's autoinserted <tbody> from table fragments
                        if (!jQuery.support.tbody) {

                            // String was a <table>, *may* have spurious <tbody>
                            elem = tag === "table" && !rtbody.test(elem) ?
                                tmp.firstChild :

                                // String was a bare <thead> or <tfoot>
                                wrap[1] === "<table>" && !rtbody.test(elem) ?
                                    tmp :
                                    0;

                            j = elem && elem.childNodes.length;
                            while (j--) {
                                if (jQuery.nodeName((tbody = elem.childNodes[j]), "tbody") && !tbody.childNodes.length) {
                                    elem.removeChild(tbody);
                                }
                            }
                        }

                        jQuery.merge(nodes, tmp.childNodes);

                        // Fix #12392 for WebKit and IE > 9
                        tmp.textContent = "";

                        // Fix #12392 for oldIE
                        while (tmp.firstChild) {
                            tmp.removeChild(tmp.firstChild);
                        }

                        // Remember the top-level container for proper cleanup
                        tmp = safe.lastChild;
                    }
                }
            }

            // Fix #11356: Clear elements from fragment
            if (tmp) {
                safe.removeChild(tmp);
            }

            // Reset defaultChecked for any radios and checkboxes
            // about to be appended to the DOM in IE 6/7 (#8060)
            if (!jQuery.support.appendChecked) {
                jQuery.grep(getAll(nodes, "input"), fixDefaultChecked);
            }

            i = 0;
            while ((elem = nodes[ i++ ])) {

                // #4087 - If origin and destination elements are the same, and this is
                // that element, do not do anything
                if (selection && jQuery.inArray(elem, selection) !== -1) {
                    continue;
                }

                contains = jQuery.contains(elem.ownerDocument, elem);

                // Append to fragment
                tmp = getAll(safe.appendChild(elem), "script");

                // Preserve script evaluation history
                if (contains) {
                    setGlobalEval(tmp);
                }

                // Capture executables
                if (scripts) {
                    j = 0;
                    while ((elem = tmp[ j++ ])) {
                        if (rscriptType.test(elem.type || "")) {
                            scripts.push(elem);
                        }
                    }
                }
            }

            tmp = null;

            return safe;
        },

        cleanData: function (elems, /* internal */ acceptData) {
            var data, id, elem, type,
                i = 0,
                internalKey = jQuery.expando,
                cache = jQuery.cache,
                deleteExpando = jQuery.support.deleteExpando,
                special = jQuery.event.special;

            for (; (elem = elems[i]) != null; i++) {

                if (acceptData || jQuery.acceptData(elem)) {

                    id = elem[ internalKey ];
                    data = id && cache[ id ];

                    if (data) {
                        if (data.events) {
                            for (type in data.events) {
                                if (special[ type ]) {
                                    jQuery.event.remove(elem, type);

                                    // This is a shortcut to avoid jQuery.event.remove's overhead
                                } else {
                                    jQuery.removeEvent(elem, type, data.handle);
                                }
                            }
                        }

                        // Remove cache only if it was not already removed by jQuery.event.remove
                        if (cache[ id ]) {

                            delete cache[ id ];

                            // IE does not allow us to delete expando properties from nodes,
                            // nor does it have a removeAttribute function on Document nodes;
                            // we must handle all of these cases
                            if (deleteExpando) {
                                delete elem[ internalKey ];

                            } else if (typeof elem.removeAttribute !== "undefined") {
                                elem.removeAttribute(internalKey);

                            } else {
                                elem[ internalKey ] = null;
                            }

                            core_deletedIds.push(id);
                        }
                    }
                }
            }
        }
    });
    var curCSS, getStyles, iframe,
        ralpha = /alpha\([^)]*\)/i,
        ropacity = /opacity\s*=\s*([^)]*)/,
        rposition = /^(top|right|bottom|left)$/,
    // swappable if display is none or starts with table except "table", "table-cell", or "table-caption"
    // see here for display values: https://developer.mozilla.org/en-US/docs/CSS/display
        rdisplayswap = /^(none|table(?!-c[ea]).+)/,
        rmargin = /^margin/,
        rnumsplit = new RegExp("^(" + core_pnum + ")(.*)$", "i"),
        rnumnonpx = new RegExp("^(" + core_pnum + ")(?!px)[a-z%]+$", "i"),
        rrelNum = new RegExp("^([+-])=(" + core_pnum + ")", "i"),
        elemdisplay = { BODY: "block" },

        cssShow = { position: "absolute", visibility: "hidden", display: "block" },
        cssNormalTransform = {
            letterSpacing: 0,
            fontWeight: 400
        },

        cssExpand = [ "Top", "Right", "Bottom", "Left" ],
        cssPrefixes = [ "Webkit", "O", "Moz", "ms" ];

// return a css property mapped to a potentially vendor prefixed property
    function vendorPropName(style, name) {

        // shortcut for names that are not vendor prefixed
        if (name in style) {
            return name;
        }

        // check for vendor prefixed names
        var capName = name.charAt(0).toUpperCase() + name.slice(1),
            origName = name,
            i = cssPrefixes.length;

        while (i--) {
            name = cssPrefixes[ i ] + capName;
            if (name in style) {
                return name;
            }
        }

        return origName;
    }

    function isHidden(elem, el) {
        // isHidden might be called from jQuery#filter function;
        // in that case, element will be second argument
        elem = el || elem;
        return jQuery.css(elem, "display") === "none" || !jQuery.contains(elem.ownerDocument, elem);
    }

    function showHide(elements, show) {
        var elem,
            values = [],
            index = 0,
            length = elements.length;

        for (; index < length; index++) {
            elem = elements[ index ];
            if (!elem.style) {
                continue;
            }
            values[ index ] = jQuery._data(elem, "olddisplay");
            if (show) {
                // Reset the inline display of this element to learn if it is
                // being hidden by cascaded rules or not
                if (!values[ index ] && elem.style.display === "none") {
                    elem.style.display = "";
                }

                // Set elements which have been overridden with display: none
                // in a stylesheet to whatever the default browser style is
                // for such an element
                if (elem.style.display === "" && isHidden(elem)) {
                    values[ index ] = jQuery._data(elem, "olddisplay", css_defaultDisplay(elem.nodeName));
                }
            } else if (!values[ index ] && !isHidden(elem)) {
                jQuery._data(elem, "olddisplay", jQuery.css(elem, "display"));
            }
        }

        // Set the display of most of the elements in a second loop
        // to avoid the constant reflow
        for (index = 0; index < length; index++) {
            elem = elements[ index ];
            if (!elem.style) {
                continue;
            }
            if (!show || elem.style.display === "none" || elem.style.display === "") {
                elem.style.display = show ? values[ index ] || "" : "none";
            }
        }

        return elements;
    }

    jQuery.fn.extend({
        css: function (name, value) {
            return jQuery.access(this, function (elem, name, value) {
                var styles, len,
                    map = {},
                    i = 0;

                if (jQuery.isArray(name)) {
                    styles = getStyles(elem);
                    len = name.length;

                    for (; i < len; i++) {
                        map[ name[ i ] ] = jQuery.css(elem, name[ i ], false, styles);
                    }

                    return map;
                }

                return value !== undefined ?
                    jQuery.style(elem, name, value) :
                    jQuery.css(elem, name);
            }, name, value, arguments.length > 1);
        },
        show: function () {
            return showHide(this, true);
        },
        hide: function () {
            return showHide(this);
        },
        toggle: function (state) {
            var bool = typeof state === "boolean";

            return this.each(function () {
                if (bool ? state : isHidden(this)) {
                    jQuery(this).show();
                } else {
                    jQuery(this).hide();
                }
            });
        }
    });

    jQuery.extend({
        // Add in style property hooks for overriding the default
        // behavior of getting and setting a style property
        cssHooks: {
            opacity: {
                get: function (elem, computed) {
                    if (computed) {
                        // We should always get a number back from opacity
                        var ret = curCSS(elem, "opacity");
                        return ret === "" ? "1" : ret;
                    }
                }
            }
        },

        // Exclude the following css properties to add px
        cssNumber: {
            "columnCount": true,
            "fillOpacity": true,
            "fontWeight": true,
            "lineHeight": true,
            "opacity": true,
            "orphans": true,
            "widows": true,
            "zIndex": true,
            "zoom": true
        },

        // Add in properties whose names you wish to fix before
        // setting or getting the value
        cssProps: {
            // normalize float css property
            "float": jQuery.support.cssFloat ? "cssFloat" : "styleFloat"
        },

        // Get and set the style property on a DOM Node
        style: function (elem, name, value, extra) {
            // Don't set styles on text and comment nodes
            if (!elem || elem.nodeType === 3 || elem.nodeType === 8 || !elem.style) {
                return;
            }

            // Make sure that we're working with the right name
            var ret, type, hooks,
                origName = jQuery.camelCase(name),
                style = elem.style;

            name = jQuery.cssProps[ origName ] || ( jQuery.cssProps[ origName ] = vendorPropName(style, origName) );

            // gets hook for the prefixed version
            // followed by the unprefixed version
            hooks = jQuery.cssHooks[ name ] || jQuery.cssHooks[ origName ];

            // Check if we're setting a value
            if (value !== undefined) {
                type = typeof value;

                // convert relative number strings (+= or -=) to relative numbers. #7345
                if (type === "string" && (ret = rrelNum.exec(value))) {
                    value = ( ret[1] + 1 ) * ret[2] + parseFloat(jQuery.css(elem, name));
                    // Fixes bug #9237
                    type = "number";
                }

                // Make sure that NaN and null values aren't set. See: #7116
                if (value == null || type === "number" && isNaN(value)) {
                    return;
                }

                // If a number was passed in, add 'px' to the (except for certain CSS properties)
                if (type === "number" && !jQuery.cssNumber[ origName ]) {
                    value += "px";
                }

                // Fixes #8908, it can be done more correctly by specifing setters in cssHooks,
                // but it would mean to define eight (for every problematic property) identical functions
                if (!jQuery.support.clearCloneStyle && value === "" && name.indexOf("background") === 0) {
                    style[ name ] = "inherit";
                }

                // If a hook was provided, use that value, otherwise just set the specified value
                if (!hooks || !("set" in hooks) || (value = hooks.set(elem, value, extra)) !== undefined) {

                    // Wrapped to prevent IE from throwing errors when 'invalid' values are provided
                    // Fixes bug #5509
                    try {
                        style[ name ] = value;
                    } catch (e) {
                    }
                }

            } else {
                // If a hook was provided get the non-computed value from there
                if (hooks && "get" in hooks && (ret = hooks.get(elem, false, extra)) !== undefined) {
                    return ret;
                }

                // Otherwise just get the value from the style object
                return style[ name ];
            }
        },

        css: function (elem, name, extra, styles) {
            var val, num, hooks,
                origName = jQuery.camelCase(name);

            // Make sure that we're working with the right name
            name = jQuery.cssProps[ origName ] || ( jQuery.cssProps[ origName ] = vendorPropName(elem.style, origName) );

            // gets hook for the prefixed version
            // followed by the unprefixed version
            hooks = jQuery.cssHooks[ name ] || jQuery.cssHooks[ origName ];

            // If a hook was provided get the computed value from there
            if (hooks && "get" in hooks) {
                val = hooks.get(elem, true, extra);
            }

            // Otherwise, if a way to get the computed value exists, use that
            if (val === undefined) {
                val = curCSS(elem, name, styles);
            }

            //convert "normal" to computed value
            if (val === "normal" && name in cssNormalTransform) {
                val = cssNormalTransform[ name ];
            }

            // Return, converting to number if forced or a qualifier was provided and val looks numeric
            if (extra) {
                num = parseFloat(val);
                return extra === true || jQuery.isNumeric(num) ? num || 0 : val;
            }
            return val;
        },

        // A method for quickly swapping in/out CSS properties to get correct calculations
        swap: function (elem, options, callback, args) {
            var ret, name,
                old = {};

            // Remember the old values, and insert the new ones
            for (name in options) {
                old[ name ] = elem.style[ name ];
                elem.style[ name ] = options[ name ];
            }

            ret = callback.apply(elem, args || []);

            // Revert the old values
            for (name in options) {
                elem.style[ name ] = old[ name ];
            }

            return ret;
        }
    });

// NOTE: we've included the "window" in window.getComputedStyle
// because jsdom on node.js will break without it.
    if (window.getComputedStyle) {
        getStyles = function (elem) {
            return window.getComputedStyle(elem, null);
        };

        curCSS = function (elem, name, _computed) {
            var width, minWidth, maxWidth,
                computed = _computed || getStyles(elem),

            // getPropertyValue is only needed for .css('filter') in IE9, see #12537
                ret = computed ? computed.getPropertyValue(name) || computed[ name ] : undefined,
                style = elem.style;

            if (computed) {

                if (ret === "" && !jQuery.contains(elem.ownerDocument, elem)) {
                    ret = jQuery.style(elem, name);
                }

                // A tribute to the "awesome hack by Dean Edwards"
                // Chrome < 17 and Safari 5.0 uses "computed value" instead of "used value" for margin-right
                // Safari 5.1.7 (at least) returns percentage for a larger set of values, but width seems to be reliably pixels
                // this is against the CSSOM draft spec: http://dev.w3.org/csswg/cssom/#resolved-values
                if (rnumnonpx.test(ret) && rmargin.test(name)) {

                    // Remember the original values
                    width = style.width;
                    minWidth = style.minWidth;
                    maxWidth = style.maxWidth;

                    // Put in the new values to get a computed value out
                    style.minWidth = style.maxWidth = style.width = ret;
                    ret = computed.width;

                    // Revert the changed values
                    style.width = width;
                    style.minWidth = minWidth;
                    style.maxWidth = maxWidth;
                }
            }

            return ret;
        };
    } else if (document.documentElement.currentStyle) {
        getStyles = function (elem) {
            return elem.currentStyle;
        };

        curCSS = function (elem, name, _computed) {
            var left, rs, rsLeft,
                computed = _computed || getStyles(elem),
                ret = computed ? computed[ name ] : undefined,
                style = elem.style;

            // Avoid setting ret to empty string here
            // so we don't default to auto
            if (ret == null && style && style[ name ]) {
                ret = style[ name ];
            }

            // From the awesome hack by Dean Edwards
            // http://erik.eae.net/archives/2007/07/27/18.54.15/#comment-102291

            // If we're not dealing with a regular pixel number
            // but a number that has a weird ending, we need to convert it to pixels
            // but not position css attributes, as those are proportional to the parent element instead
            // and we can't measure the parent instead because it might trigger a "stacking dolls" problem
            if (rnumnonpx.test(ret) && !rposition.test(name)) {

                // Remember the original values
                left = style.left;
                rs = elem.runtimeStyle;
                rsLeft = rs && rs.left;

                // Put in the new values to get a computed value out
                if (rsLeft) {
                    rs.left = elem.currentStyle.left;
                }
                style.left = name === "fontSize" ? "1em" : ret;
                ret = style.pixelLeft + "px";

                // Revert the changed values
                style.left = left;
                if (rsLeft) {
                    rs.left = rsLeft;
                }
            }

            return ret === "" ? "auto" : ret;
        };
    }

    function setPositiveNumber(elem, value, subtract) {
        var matches = rnumsplit.exec(value);
        return matches ?
            // Guard against undefined "subtract", e.g., when used as in cssHooks
            Math.max(0, matches[ 1 ] - ( subtract || 0 )) + ( matches[ 2 ] || "px" ) :
            value;
    }

    function augmentWidthOrHeight(elem, name, extra, isBorderBox, styles) {
        var i = extra === ( isBorderBox ? "border" : "content" ) ?
                // If we already have the right measurement, avoid augmentation
                4 :
                // Otherwise initialize for horizontal or vertical properties
                name === "width" ? 1 : 0,

            val = 0;

        for (; i < 4; i += 2) {
            // both box models exclude margin, so add it if we want it
            if (extra === "margin") {
                val += jQuery.css(elem, extra + cssExpand[ i ], true, styles);
            }

            if (isBorderBox) {
                // border-box includes padding, so remove it if we want content
                if (extra === "content") {
                    val -= jQuery.css(elem, "padding" + cssExpand[ i ], true, styles);
                }

                // at this point, extra isn't border nor margin, so remove border
                if (extra !== "margin") {
                    val -= jQuery.css(elem, "border" + cssExpand[ i ] + "Width", true, styles);
                }
            } else {
                // at this point, extra isn't content, so add padding
                val += jQuery.css(elem, "padding" + cssExpand[ i ], true, styles);

                // at this point, extra isn't content nor padding, so add border
                if (extra !== "padding") {
                    val += jQuery.css(elem, "border" + cssExpand[ i ] + "Width", true, styles);
                }
            }
        }

        return val;
    }

    function getWidthOrHeight(elem, name, extra) {

        // Start with offset property, which is equivalent to the border-box value
        var valueIsBorderBox = true,
            val = name === "width" ? elem.offsetWidth : elem.offsetHeight,
            styles = getStyles(elem),
            isBorderBox = jQuery.support.boxSizing && jQuery.css(elem, "boxSizing", false, styles) === "border-box";

        // some non-html elements return undefined for offsetWidth, so check for null/undefined
        // svg - https://bugzilla.mozilla.org/show_bug.cgi?id=649285
        // MathML - https://bugzilla.mozilla.org/show_bug.cgi?id=491668
        if (val <= 0 || val == null) {
            // Fall back to computed then uncomputed css if necessary
            val = curCSS(elem, name, styles);
            if (val < 0 || val == null) {
                val = elem.style[ name ];
            }

            // Computed unit is not pixels. Stop here and return.
            if (rnumnonpx.test(val)) {
                return val;
            }

            // we need the check for style in case a browser which returns unreliable values
            // for getComputedStyle silently falls back to the reliable elem.style
            valueIsBorderBox = isBorderBox && ( jQuery.support.boxSizingReliable || val === elem.style[ name ] );

            // Normalize "", auto, and prepare for extra
            val = parseFloat(val) || 0;
        }

        // use the active box-sizing model to add/subtract irrelevant styles
        return ( val +
            augmentWidthOrHeight(
                elem,
                name,
                extra || ( isBorderBox ? "border" : "content" ),
                valueIsBorderBox,
                styles
            )
            ) + "px";
    }

// Try to determine the default display value of an element
    function css_defaultDisplay(nodeName) {
        var doc = document,
            display = elemdisplay[ nodeName ];

        if (!display) {
            display = actualDisplay(nodeName, doc);

            // If the simple way fails, read from inside an iframe
            if (display === "none" || !display) {
                // Use the already-created iframe if possible
                iframe = ( iframe ||
                    jQuery("<iframe frameborder='0' width='0' height='0'/>")
                        .css("cssText", "display:block !important")
                    ).appendTo(doc.documentElement);

                // Always write a new HTML skeleton so Webkit and Firefox don't choke on reuse
                doc = ( iframe[0].contentWindow || iframe[0].contentDocument ).document;
                doc.write("<!doctype html><html><body>");
                doc.close();

                display = actualDisplay(nodeName, doc);
                iframe.detach();
            }

            // Store the correct default display
            elemdisplay[ nodeName ] = display;
        }

        return display;
    }

// Called ONLY from within css_defaultDisplay
    function actualDisplay(name, doc) {
        var elem = jQuery(doc.createElement(name)).appendTo(doc.body),
            display = jQuery.css(elem[0], "display");
        elem.remove();
        return display;
    }

    jQuery.each([ "height", "width" ], function (i, name) {
        jQuery.cssHooks[ name ] = {
            get: function (elem, computed, extra) {
                if (computed) {
                    // certain elements can have dimension info if we invisibly show them
                    // however, it must have a current display style that would benefit from this
                    return elem.offsetWidth === 0 && rdisplayswap.test(jQuery.css(elem, "display")) ?
                        jQuery.swap(elem, cssShow, function () {
                            return getWidthOrHeight(elem, name, extra);
                        }) :
                        getWidthOrHeight(elem, name, extra);
                }
            },

            set: function (elem, value, extra) {
                var styles = extra && getStyles(elem);
                return setPositiveNumber(elem, value, extra ?
                    augmentWidthOrHeight(
                        elem,
                        name,
                        extra,
                        jQuery.support.boxSizing && jQuery.css(elem, "boxSizing", false, styles) === "border-box",
                        styles
                    ) : 0
                );
            }
        };
    });

    if (!jQuery.support.opacity) {
        jQuery.cssHooks.opacity = {
            get: function (elem, computed) {
                // IE uses filters for opacity
                return ropacity.test((computed && elem.currentStyle ? elem.currentStyle.filter : elem.style.filter) || "") ?
                    ( 0.01 * parseFloat(RegExp.$1) ) + "" :
                    computed ? "1" : "";
            },

            set: function (elem, value) {
                var style = elem.style,
                    currentStyle = elem.currentStyle,
                    opacity = jQuery.isNumeric(value) ? "alpha(opacity=" + value * 100 + ")" : "",
                    filter = currentStyle && currentStyle.filter || style.filter || "";

                // IE has trouble with opacity if it does not have layout
                // Force it by setting the zoom level
                style.zoom = 1;

                // if setting opacity to 1, and no other filters exist - attempt to remove filter attribute #6652
                // if value === "", then remove inline opacity #12685
                if (( value >= 1 || value === "" ) &&
                    jQuery.trim(filter.replace(ralpha, "")) === "" &&
                    style.removeAttribute) {

                    // Setting style.filter to null, "" & " " still leave "filter:" in the cssText
                    // if "filter:" is present at all, clearType is disabled, we want to avoid this
                    // style.removeAttribute is IE Only, but so apparently is this code path...
                    style.removeAttribute("filter");

                    // if there is no filter style applied in a css rule or unset inline opacity, we are done
                    if (value === "" || currentStyle && !currentStyle.filter) {
                        return;
                    }
                }

                // otherwise, set new filter values
                style.filter = ralpha.test(filter) ?
                    filter.replace(ralpha, opacity) :
                    filter + " " + opacity;
            }
        };
    }

// These hooks cannot be added until DOM ready because the support test
// for it is not run until after DOM ready
    jQuery(function () {
        if (!jQuery.support.reliableMarginRight) {
            jQuery.cssHooks.marginRight = {
                get: function (elem, computed) {
                    if (computed) {
                        // WebKit Bug 13343 - getComputedStyle returns wrong value for margin-right
                        // Work around by temporarily setting element display to inline-block
                        return jQuery.swap(elem, { "display": "inline-block" },
                            curCSS, [ elem, "marginRight" ]);
                    }
                }
            };
        }

        // Webkit bug: https://bugs.webkit.org/show_bug.cgi?id=29084
        // getComputedStyle returns percent when specified for top/left/bottom/right
        // rather than make the css module depend on the offset module, we just check for it here
        if (!jQuery.support.pixelPosition && jQuery.fn.position) {
            jQuery.each([ "top", "left" ], function (i, prop) {
                jQuery.cssHooks[ prop ] = {
                    get: function (elem, computed) {
                        if (computed) {
                            computed = curCSS(elem, prop);
                            // if curCSS returns percentage, fallback to offset
                            return rnumnonpx.test(computed) ?
                                jQuery(elem).position()[ prop ] + "px" :
                                computed;
                        }
                    }
                };
            });
        }

    });

    if (jQuery.expr && jQuery.expr.filters) {
        jQuery.expr.filters.hidden = function (elem) {
            return ( elem.offsetWidth === 0 && elem.offsetHeight === 0 ) || (!jQuery.support.reliableHiddenOffsets && ((elem.style && elem.style.display) || jQuery.css(elem, "display")) === "none");
        };

        jQuery.expr.filters.visible = function (elem) {
            return !jQuery.expr.filters.hidden(elem);
        };
    }

// These hooks are used by animate to expand properties
    jQuery.each({
        margin: "",
        padding: "",
        border: "Width"
    }, function (prefix, suffix) {
        jQuery.cssHooks[ prefix + suffix ] = {
            expand: function (value) {
                var i = 0,
                    expanded = {},

                // assumes a single number if not a string
                    parts = typeof value === "string" ? value.split(" ") : [ value ];

                for (; i < 4; i++) {
                    expanded[ prefix + cssExpand[ i ] + suffix ] =
                        parts[ i ] || parts[ i - 2 ] || parts[ 0 ];
                }

                return expanded;
            }
        };

        if (!rmargin.test(prefix)) {
            jQuery.cssHooks[ prefix + suffix ].set = setPositiveNumber;
        }
    });
    var r20 = /%20/g,
        rbracket = /\[\]$/,
        rCRLF = /\r?\n/g,
        rsubmitterTypes = /^(?:submit|button|image|reset)$/i,
        rsubmittable = /^(?:input|select|textarea|keygen)/i;

    jQuery.fn.extend({
        serialize: function () {
            return jQuery.param(this.serializeArray());
        },
        serializeArray: function () {
            return this.map(function () {
                // Can add propHook for "elements" to filter or add form elements
                var elements = jQuery.prop(this, "elements");
                return elements ? jQuery.makeArray(elements) : this;
            })
                .filter(function () {
                    var type = this.type;
                    // Use .is(":disabled") so that fieldset[disabled] works
                    return this.name && !jQuery(this).is(":disabled") &&
                        rsubmittable.test(this.nodeName) && !rsubmitterTypes.test(type) &&
                        ( this.checked || !manipulation_rcheckableType.test(type) );
                })
                .map(function (i, elem) {
                    var val = jQuery(this).val();

                    return val == null ?
                        null :
                        jQuery.isArray(val) ?
                            jQuery.map(val, function (val) {
                                return { name: elem.name, value: val.replace(rCRLF, "\r\n") };
                            }) :
                        { name: elem.name, value: val.replace(rCRLF, "\r\n") };
                }).get();
        }
    });

//Serialize an array of form elements or a set of
//key/values into a query string
    jQuery.param = function (a, traditional) {
        var prefix,
            s = [],
            add = function (key, value) {
                // If value is a function, invoke it and return its value
                value = jQuery.isFunction(value) ? value() : ( value == null ? "" : value );
                s[ s.length ] = encodeURIComponent(key) + "=" + encodeURIComponent(value);
            };

        // Set traditional to true for jQuery <= 1.3.2 behavior.
        if (traditional === undefined) {
            traditional = jQuery.ajaxSettings && jQuery.ajaxSettings.traditional;
        }

        // If an array was passed in, assume that it is an array of form elements.
        if (jQuery.isArray(a) || ( a.jquery && !jQuery.isPlainObject(a) )) {
            // Serialize the form elements
            jQuery.each(a, function () {
                add(this.name, this.value);
            });

        } else {
            // If traditional, encode the "old" way (the way 1.3.2 or older
            // did it), otherwise encode params recursively.
            for (prefix in a) {
                buildParams(prefix, a[ prefix ], traditional, add);
            }
        }

        // Return the resulting serialization
        return s.join("&").replace(r20, "+");
    };

    function buildParams(prefix, obj, traditional, add) {
        var name;

        if (jQuery.isArray(obj)) {
            // Serialize array item.
            jQuery.each(obj, function (i, v) {
                if (traditional || rbracket.test(prefix)) {
                    // Treat each array item as a scalar.
                    add(prefix, v);

                } else {
                    // Item is non-scalar (array or object), encode its numeric index.
                    buildParams(prefix + "[" + ( typeof v === "object" ? i : "" ) + "]", v, traditional, add);
                }
            });

        } else if (!traditional && jQuery.type(obj) === "object") {
            // Serialize object item.
            for (name in obj) {
                buildParams(prefix + "[" + name + "]", obj[ name ], traditional, add);
            }

        } else {
            // Serialize scalar item.
            add(prefix, obj);
        }
    }

    var
    // Document location
        ajaxLocParts,
        ajaxLocation,

        ajax_nonce = jQuery.now(),

        ajax_rquery = /\?/,
        rhash = /#.*$/,
        rts = /([?&])_=[^&]*/,
        rheaders = /^(.*?):[ \t]*([^\r\n]*)\r?$/mg, // IE leaves an \r character at EOL
    // #7653, #8125, #8152: local protocol detection
        rlocalProtocol = /^(?:about|app|app-storage|.+-extension|file|res|widget):$/,
        rnoContent = /^(?:GET|HEAD)$/,
        rprotocol = /^\/\//,
        rurl = /^([\w.+-]+:)(?:\/\/([^\/?#:]*)(?::(\d+)|)|)/,

    // Keep a copy of the old load method
        _load = jQuery.fn.load,

    /* Prefilters
     * 1) They are useful to introduce custom dataTypes (see ajax/jsonp.js for an example)
     * 2) These are called:
     *    - BEFORE asking for a transport
     *    - AFTER param serialization (s.data is a string if s.processData is true)
     * 3) key is the dataType
     * 4) the catchall symbol "*" can be used
     * 5) execution will start with transport dataType and THEN continue down to "*" if needed
     */
        prefilters = {},

    /* Transports bindings
     * 1) key is the dataType
     * 2) the catchall symbol "*" can be used
     * 3) selection will start with transport dataType and THEN go to "*" if needed
     */
        transports = {},

    // Avoid comment-prolog char sequence (#10098); must appease lint and evade compression
        allTypes = "*/".concat("*");

// #8138, IE may throw an exception when accessing
// a field from window.location if document.domain has been set
    try {
        ajaxLocation = location.href;
    } catch (e) {
        // Use the href attribute of an A element
        // since IE will modify it given document.location
        ajaxLocation = document.createElement("a");
        ajaxLocation.href = "";
        ajaxLocation = ajaxLocation.href;
    }

// Segment location into parts
    ajaxLocParts = rurl.exec(ajaxLocation.toLowerCase()) || [];

// Base "constructor" for jQuery.ajaxPrefilter and jQuery.ajaxTransport
    function addToPrefiltersOrTransports(structure) {

        // dataTypeExpression is optional and defaults to "*"
        return function (dataTypeExpression, func) {

            if (typeof dataTypeExpression !== "string") {
                func = dataTypeExpression;
                dataTypeExpression = "*";
            }

            var dataType,
                i = 0,
                dataTypes = dataTypeExpression.toLowerCase().match(core_rnotwhite) || [];

            if (jQuery.isFunction(func)) {
                // For each dataType in the dataTypeExpression
                while ((dataType = dataTypes[i++])) {
                    // Prepend if requested
                    if (dataType[0] === "+") {
                        dataType = dataType.slice(1) || "*";
                        (structure[ dataType ] = structure[ dataType ] || []).unshift(func);

                        // Otherwise append
                    } else {
                        (structure[ dataType ] = structure[ dataType ] || []).push(func);
                    }
                }
            }
        };
    }

// Base inspection function for prefilters and transports
    function inspectPrefiltersOrTransports(structure, options, originalOptions, jqXHR) {

        var inspected = {},
            seekingTransport = ( structure === transports );

        function inspect(dataType) {
            var selected;
            inspected[ dataType ] = true;
            jQuery.each(structure[ dataType ] || [], function (_, prefilterOrFactory) {
                var dataTypeOrTransport = prefilterOrFactory(options, originalOptions, jqXHR);
                if (typeof dataTypeOrTransport === "string" && !seekingTransport && !inspected[ dataTypeOrTransport ]) {
                    options.dataTypes.unshift(dataTypeOrTransport);
                    inspect(dataTypeOrTransport);
                    return false;
                } else if (seekingTransport) {
                    return !( selected = dataTypeOrTransport );
                }
            });
            return selected;
        }

        return inspect(options.dataTypes[ 0 ]) || !inspected[ "*" ] && inspect("*");
    }

// A special extend for ajax options
// that takes "flat" options (not to be deep extended)
// Fixes #9887
    function ajaxExtend(target, src) {
        var key, deep,
            flatOptions = jQuery.ajaxSettings.flatOptions || {};

        for (key in src) {
            if (src[ key ] !== undefined) {
                ( flatOptions[ key ] ? target : ( deep || (deep = {}) ) )[ key ] = src[ key ];
            }
        }
        if (deep) {
            jQuery.extend(true, target, deep);
        }

        return target;
    }

    jQuery.fn.load = function (url, params, callback) {
        if (typeof url !== "string" && _load) {
            return _load.apply(this, arguments);
        }

        var selector, type, response,
            self = this,
            off = url.indexOf(" ");

        if (off >= 0) {
            selector = url.slice(off, url.length);
            url = url.slice(0, off);
        }

        // If it's a function
        if (jQuery.isFunction(params)) {

            // We assume that it's the callback
            callback = params;
            params = undefined;

            // Otherwise, build a param string
        } else if (params && typeof params === "object") {
            type = "POST";
        }

        // If we have elements to modify, make the request
        if (self.length > 0) {
            jQuery.ajax({
                url: url,

                // if "type" variable is undefined, then "GET" method will be used
                type: type,
                dataType: "html",
                data: params
            }).done(function (responseText) {

                    // Save response for use in complete callback
                    response = arguments;

                    self.html(selector ?

                        // If a selector was specified, locate the right elements in a dummy div
                        // Exclude scripts to avoid IE 'Permission Denied' errors
                        jQuery("<div>").append(jQuery.parseHTML(responseText)).find(selector) :

                        // Otherwise use the full result
                        responseText);

                }).complete(callback && function (jqXHR, status) {
                    self.each(callback, response || [ jqXHR.responseText, status, jqXHR ]);
                });
        }

        return this;
    };

// Attach a bunch of functions for handling common AJAX events
    jQuery.each([ "ajaxStart", "ajaxStop", "ajaxComplete", "ajaxError", "ajaxSuccess", "ajaxSend" ], function (i, type) {
        jQuery.fn[ type ] = function (fn) {
            return this.on(type, fn);
        };
    });

    jQuery.each([ "get", "post" ], function (i, method) {
        jQuery[ method ] = function (url, data, callback, type) {
            // shift arguments if data argument was omitted
            if (jQuery.isFunction(data)) {
                type = type || callback;
                callback = data;
                data = undefined;
            }

            return jQuery.ajax({
                url: url,
                type: method,
                dataType: type,
                data: data,
                success: callback
            });
        };
    });

    jQuery.extend({

        // Counter for holding the number of active queries
        active: 0,

        // Last-Modified header cache for next request
        lastModified: {},
        etag: {},

        ajaxSettings: {
            url: ajaxLocation,
            type: "GET",
            isLocal: rlocalProtocol.test(ajaxLocParts[ 1 ]),
            global: true,
            processData: true,
            async: true,
            contentType: "application/x-www-form-urlencoded; charset=UTF-8",
            /*
             timeout: 0,
             data: null,
             dataType: null,
             username: null,
             password: null,
             cache: null,
             throws: false,
             traditional: false,
             headers: {},
             */

            accepts: {
                "*": allTypes,
                text: "text/plain",
                html: "text/html",
                xml: "application/xml, text/xml",
                json: "application/json, text/javascript"
            },

            contents: {
                xml: /xml/,
                html: /html/,
                json: /json/
            },

            responseFields: {
                xml: "responseXML",
                text: "responseText"
            },

            // Data converters
            // Keys separate source (or catchall "*") and destination types with a single space
            converters: {

                // Convert anything to text
                "* text": window.String,

                // Text to html (true = no transformation)
                "text html": true,

                // Evaluate text as a json expression
                "text json": jQuery.parseJSON,

                // Parse text as xml
                "text xml": jQuery.parseXML
            },

            // For options that shouldn't be deep extended:
            // you can add your own custom options here if
            // and when you create one that shouldn't be
            // deep extended (see ajaxExtend)
            flatOptions: {
                url: true,
                context: true
            }
        },

        // Creates a full fledged settings object into target
        // with both ajaxSettings and settings fields.
        // If target is omitted, writes into ajaxSettings.
        ajaxSetup: function (target, settings) {
            return settings ?

                // Building a settings object
                ajaxExtend(ajaxExtend(target, jQuery.ajaxSettings), settings) :

                // Extending ajaxSettings
                ajaxExtend(jQuery.ajaxSettings, target);
        },

        ajaxPrefilter: addToPrefiltersOrTransports(prefilters),
        ajaxTransport: addToPrefiltersOrTransports(transports),

        // Main method
        ajax: function (url, options) {

            // If url is an object, simulate pre-1.5 signature
            if (typeof url === "object") {
                options = url;
                url = undefined;
            }

            // Force options to be an object
            options = options || {};

            var transport,
            // URL without anti-cache param
                cacheURL,
            // Response headers
                responseHeadersString,
                responseHeaders,
            // timeout handle
                timeoutTimer,
            // Cross-domain detection vars
                parts,
            // To know if global events are to be dispatched
                fireGlobals,
            // Loop variable
                i,
            // Create the final options object
                s = jQuery.ajaxSetup({}, options),
            // Callbacks context
                callbackContext = s.context || s,
            // Context for global events is callbackContext if it is a DOM node or jQuery collection
                globalEventContext = s.context && ( callbackContext.nodeType || callbackContext.jquery ) ?
                    jQuery(callbackContext) :
                    jQuery.event,
            // Deferreds
                deferred = jQuery.Deferred(),
                completeDeferred = jQuery.Callbacks("once memory"),
            // Status-dependent callbacks
                statusCode = s.statusCode || {},
            // Headers (they are sent all at once)
                requestHeaders = {},
                requestHeadersNames = {},
            // The jqXHR state
                state = 0,
            // Default abort message
                strAbort = "canceled",
            // Fake xhr
                jqXHR = {
                    readyState: 0,

                    // Builds headers hashtable if needed
                    getResponseHeader: function (key) {
                        var match;
                        if (state === 2) {
                            if (!responseHeaders) {
                                responseHeaders = {};
                                while ((match = rheaders.exec(responseHeadersString))) {
                                    responseHeaders[ match[1].toLowerCase() ] = match[ 2 ];
                                }
                            }
                            match = responseHeaders[ key.toLowerCase() ];
                        }
                        return match == null ? null : match;
                    },

                    // Raw string
                    getAllResponseHeaders: function () {
                        return state === 2 ? responseHeadersString : null;
                    },

                    // Caches the header
                    setRequestHeader: function (name, value) {
                        var lname = name.toLowerCase();
                        if (!state) {
                            name = requestHeadersNames[ lname ] = requestHeadersNames[ lname ] || name;
                            requestHeaders[ name ] = value;
                        }
                        return this;
                    },

                    // Overrides response content-type header
                    overrideMimeType: function (type) {
                        if (!state) {
                            s.mimeType = type;
                        }
                        return this;
                    },

                    // Status-dependent callbacks
                    statusCode: function (map) {
                        var code;
                        if (map) {
                            if (state < 2) {
                                for (code in map) {
                                    // Lazy-add the new callback in a way that preserves old ones
                                    statusCode[ code ] = [ statusCode[ code ], map[ code ] ];
                                }
                            } else {
                                // Execute the appropriate callbacks
                                jqXHR.always(map[ jqXHR.status ]);
                            }
                        }
                        return this;
                    },

                    // Cancel the request
                    abort: function (statusText) {
                        var finalText = statusText || strAbort;
                        if (transport) {
                            transport.abort(finalText);
                        }
                        done(0, finalText);
                        return this;
                    }
                };

            // Attach deferreds
            deferred.promise(jqXHR).complete = completeDeferred.add;
            jqXHR.success = jqXHR.done;
            jqXHR.error = jqXHR.fail;

            // Remove hash character (#7531: and string promotion)
            // Add protocol if not provided (#5866: IE7 issue with protocol-less urls)
            // Handle falsy url in the settings object (#10093: consistency with old signature)
            // We also use the url parameter if available
            s.url = ( ( url || s.url || ajaxLocation ) + "" ).replace(rhash, "").replace(rprotocol, ajaxLocParts[ 1 ] + "//");

            // Alias method option to type as per ticket #12004
            s.type = options.method || options.type || s.method || s.type;

            // Extract dataTypes list
            s.dataTypes = jQuery.trim(s.dataType || "*").toLowerCase().match(core_rnotwhite) || [""];

            // A cross-domain request is in order when we have a protocol:host:port mismatch
            if (s.crossDomain == null) {
                parts = rurl.exec(s.url.toLowerCase());
                s.crossDomain = !!( parts &&
                    ( parts[ 1 ] !== ajaxLocParts[ 1 ] || parts[ 2 ] !== ajaxLocParts[ 2 ] ||
                        ( parts[ 3 ] || ( parts[ 1 ] === "http:" ? 80 : 443 ) ) !=
                            ( ajaxLocParts[ 3 ] || ( ajaxLocParts[ 1 ] === "http:" ? 80 : 443 ) ) )
                    );
            }

            // Convert data if not already a string
            if (s.data && s.processData && typeof s.data !== "string") {
                s.data = jQuery.param(s.data, s.traditional);
            }

            // Apply prefilters
            inspectPrefiltersOrTransports(prefilters, s, options, jqXHR);

            // If request was aborted inside a prefilter, stop there
            if (state === 2) {
                return jqXHR;
            }

            // We can fire global events as of now if asked to
            fireGlobals = s.global;

            // Watch for a new set of requests
            if (fireGlobals && jQuery.active++ === 0) {
                jQuery.event.trigger("ajaxStart");
            }

            // Uppercase the type
            s.type = s.type.toUpperCase();

            // Determine if request has content
            s.hasContent = !rnoContent.test(s.type);

            // Save the URL in case we're toying with the If-Modified-Since
            // and/or If-None-Match header later on
            cacheURL = s.url;

            // More options handling for requests with no content
            if (!s.hasContent) {

                // If data is available, append data to url
                if (s.data) {
                    cacheURL = ( s.url += ( ajax_rquery.test(cacheURL) ? "&" : "?" ) + s.data );
                    // #9682: remove data so that it's not used in an eventual retry
                    delete s.data;
                }

                // Add anti-cache in url if needed
                if (s.cache === false) {
                    s.url = rts.test(cacheURL) ?

                        // If there is already a '_' parameter, set its value
                        cacheURL.replace(rts, "$1_=" + ajax_nonce++) :

                        // Otherwise add one to the end
                        cacheURL + ( ajax_rquery.test(cacheURL) ? "&" : "?" ) + "_=" + ajax_nonce++;
                }
            }

            // Set the If-Modified-Since and/or If-None-Match header, if in ifModified mode.
            if (s.ifModified) {
                if (jQuery.lastModified[ cacheURL ]) {
                    jqXHR.setRequestHeader("If-Modified-Since", jQuery.lastModified[ cacheURL ]);
                }
                if (jQuery.etag[ cacheURL ]) {
                    jqXHR.setRequestHeader("If-None-Match", jQuery.etag[ cacheURL ]);
                }
            }

            // Set the correct header, if data is being sent
            if (s.data && s.hasContent && s.contentType !== false || options.contentType) {
                jqXHR.setRequestHeader("Content-Type", s.contentType);
            }

            // Set the Accepts header for the server, depending on the dataType
            jqXHR.setRequestHeader(
                "Accept",
                s.dataTypes[ 0 ] && s.accepts[ s.dataTypes[0] ] ?
                    s.accepts[ s.dataTypes[0] ] + ( s.dataTypes[ 0 ] !== "*" ? ", " + allTypes + "; q=0.01" : "" ) :
                    s.accepts[ "*" ]
            );

            // Check for headers option
            for (i in s.headers) {
                jqXHR.setRequestHeader(i, s.headers[ i ]);
            }

            // Allow custom headers/mimetypes and early abort
            if (s.beforeSend && ( s.beforeSend.call(callbackContext, jqXHR, s) === false || state === 2 )) {
                // Abort if not done already and return
                return jqXHR.abort();
            }

            // aborting is no longer a cancellation
            strAbort = "abort";

            // Install callbacks on deferreds
            for (i in { success: 1, error: 1, complete: 1 }) {
                jqXHR[ i ](s[ i ]);
            }

            // Get transport
            transport = inspectPrefiltersOrTransports(transports, s, options, jqXHR);

            // If no transport, we auto-abort
            if (!transport) {
                done(-1, "No Transport");
            } else {
                jqXHR.readyState = 1;

                // Send global event
                if (fireGlobals) {
                    globalEventContext.trigger("ajaxSend", [ jqXHR, s ]);
                }
                // Timeout
                if (s.async && s.timeout > 0) {
                    timeoutTimer = setTimeout(function () {
                        jqXHR.abort("timeout");
                    }, s.timeout);
                }

                try {
                    state = 1;
                    transport.send(requestHeaders, done);
                } catch (e) {
                    // Propagate exception as error if not done
                    if (state < 2) {
                        done(-1, e);
                        // Simply rethrow otherwise
                    } else {
                        throw e;
                    }
                }
            }

            // Callback for when everything is done
            function done(status, nativeStatusText, responses, headers) {
                var isSuccess, success, error, response, modified,
                    statusText = nativeStatusText;

                // Called once
                if (state === 2) {
                    return;
                }

                // State is "done" now
                state = 2;

                // Clear timeout if it exists
                if (timeoutTimer) {
                    clearTimeout(timeoutTimer);
                }

                // Dereference transport for early garbage collection
                // (no matter how long the jqXHR object will be used)
                transport = undefined;

                // Cache response headers
                responseHeadersString = headers || "";

                // Set readyState
                jqXHR.readyState = status > 0 ? 4 : 0;

                // Get response data
                if (responses) {
                    response = ajaxHandleResponses(s, jqXHR, responses);
                }

                // If successful, handle type chaining
                if (status >= 200 && status < 300 || status === 304) {

                    // Set the If-Modified-Since and/or If-None-Match header, if in ifModified mode.
                    if (s.ifModified) {
                        modified = jqXHR.getResponseHeader("Last-Modified");
                        if (modified) {
                            jQuery.lastModified[ cacheURL ] = modified;
                        }
                        modified = jqXHR.getResponseHeader("etag");
                        if (modified) {
                            jQuery.etag[ cacheURL ] = modified;
                        }
                    }

                    // If not modified
                    if (status === 304) {
                        isSuccess = true;
                        statusText = "notmodified";

                        // If we have data
                    } else {
                        isSuccess = ajaxConvert(s, response);
                        statusText = isSuccess.state;
                        success = isSuccess.data;
                        error = isSuccess.error;
                        isSuccess = !error;
                    }
                } else {
                    // We extract error from statusText
                    // then normalize statusText and status for non-aborts
                    error = statusText;
                    if (status || !statusText) {
                        statusText = "error";
                        if (status < 0) {
                            status = 0;
                        }
                    }
                }

                // Set data for the fake xhr object
                jqXHR.status = status;
                jqXHR.statusText = ( nativeStatusText || statusText ) + "";

                // Success/Error
                if (isSuccess) {
                    deferred.resolveWith(callbackContext, [ success, statusText, jqXHR ]);
                } else {
                    deferred.rejectWith(callbackContext, [ jqXHR, statusText, error ]);
                }

                // Status-dependent callbacks
                jqXHR.statusCode(statusCode);
                statusCode = undefined;

                if (fireGlobals) {
                    globalEventContext.trigger(isSuccess ? "ajaxSuccess" : "ajaxError",
                        [ jqXHR, s, isSuccess ? success : error ]);
                }

                // Complete
                completeDeferred.fireWith(callbackContext, [ jqXHR, statusText ]);

                if (fireGlobals) {
                    globalEventContext.trigger("ajaxComplete", [ jqXHR, s ]);
                    // Handle the global AJAX counter
                    if (!( --jQuery.active )) {
                        jQuery.event.trigger("ajaxStop");
                    }
                }
            }

            return jqXHR;
        },

        getScript: function (url, callback) {
            return jQuery.get(url, undefined, callback, "script");
        },

        getJSON: function (url, data, callback) {
            return jQuery.get(url, data, callback, "json");
        }
    });

    /* Handles responses to an ajax request:
     * - sets all responseXXX fields accordingly
     * - finds the right dataType (mediates between content-type and expected dataType)
     * - returns the corresponding response
     */
    function ajaxHandleResponses(s, jqXHR, responses) {

        var ct, type, finalDataType, firstDataType,
            contents = s.contents,
            dataTypes = s.dataTypes,
            responseFields = s.responseFields;

        // Fill responseXXX fields
        for (type in responseFields) {
            if (type in responses) {
                jqXHR[ responseFields[type] ] = responses[ type ];
            }
        }

        // Remove auto dataType and get content-type in the process
        while (dataTypes[ 0 ] === "*") {
            dataTypes.shift();
            if (ct === undefined) {
                ct = s.mimeType || jqXHR.getResponseHeader("Content-Type");
            }
        }

        // Check if we're dealing with a known content-type
        if (ct) {
            for (type in contents) {
                if (contents[ type ] && contents[ type ].test(ct)) {
                    dataTypes.unshift(type);
                    break;
                }
            }
        }

        // Check to see if we have a response for the expected dataType
        if (dataTypes[ 0 ] in responses) {
            finalDataType = dataTypes[ 0 ];
        } else {
            // Try convertible dataTypes
            for (type in responses) {
                if (!dataTypes[ 0 ] || s.converters[ type + " " + dataTypes[0] ]) {
                    finalDataType = type;
                    break;
                }
                if (!firstDataType) {
                    firstDataType = type;
                }
            }
            // Or just use first one
            finalDataType = finalDataType || firstDataType;
        }

        // If we found a dataType
        // We add the dataType to the list if needed
        // and return the corresponding response
        if (finalDataType) {
            if (finalDataType !== dataTypes[ 0 ]) {
                dataTypes.unshift(finalDataType);
            }
            return responses[ finalDataType ];
        }
    }

// Chain conversions given the request and the original response
    function ajaxConvert(s, response) {

        var conv, conv2, current, tmp,
            converters = {},
            i = 0,
        // Work with a copy of dataTypes in case we need to modify it for conversion
            dataTypes = s.dataTypes.slice(),
            prev = dataTypes[ 0 ];

        // Apply the dataFilter if provided
        if (s.dataFilter) {
            response = s.dataFilter(response, s.dataType);
        }

        // Create converters map with lowercased keys
        if (dataTypes[ 1 ]) {
            for (conv in s.converters) {
                converters[ conv.toLowerCase() ] = s.converters[ conv ];
            }
        }

        // Convert to each sequential dataType, tolerating list modification
        for (; (current = dataTypes[++i]);) {

            // There's only work to do if current dataType is non-auto
            if (current !== "*") {

                // Convert response if prev dataType is non-auto and differs from current
                if (prev !== "*" && prev !== current) {

                    // Seek a direct converter
                    conv = converters[ prev + " " + current ] || converters[ "* " + current ];

                    // If none found, seek a pair
                    if (!conv) {
                        for (conv2 in converters) {

                            // If conv2 outputs current
                            tmp = conv2.split(" ");
                            if (tmp[ 1 ] === current) {

                                // If prev can be converted to accepted input
                                conv = converters[ prev + " " + tmp[ 0 ] ] ||
                                    converters[ "* " + tmp[ 0 ] ];
                                if (conv) {
                                    // Condense equivalence converters
                                    if (conv === true) {
                                        conv = converters[ conv2 ];

                                        // Otherwise, insert the intermediate dataType
                                    } else if (converters[ conv2 ] !== true) {
                                        current = tmp[ 0 ];
                                        dataTypes.splice(i--, 0, current);
                                    }

                                    break;
                                }
                            }
                        }
                    }

                    // Apply converter (if not an equivalence)
                    if (conv !== true) {

                        // Unless errors are allowed to bubble, catch and return them
                        if (conv && s["throws"]) {
                            response = conv(response);
                        } else {
                            try {
                                response = conv(response);
                            } catch (e) {
                                return { state: "parsererror", error: conv ? e : "No conversion from " + prev + " to " + current };
                            }
                        }
                    }
                }

                // Update prev for next iteration
                prev = current;
            }
        }

        return { state: "success", data: response };
    }

// Install script dataType
    jQuery.ajaxSetup({
        accepts: {
            script: "text/javascript, application/javascript, application/ecmascript, application/x-ecmascript"
        },
        contents: {
            script: /(?:java|ecma)script/
        },
        converters: {
            "text script": function (text) {
                jQuery.globalEval(text);
                return text;
            }
        }
    });

// Handle cache's special case and global
    jQuery.ajaxPrefilter("script", function (s) {
        if (s.cache === undefined) {
            s.cache = false;
        }
        if (s.crossDomain) {
            s.type = "GET";
            s.global = false;
        }
    });

// Bind script tag hack transport
    jQuery.ajaxTransport("script", function (s) {

        // This transport only deals with cross domain requests
        if (s.crossDomain) {

            var script,
                head = document.head || jQuery("head")[0] || document.documentElement;

            return {

                send: function (_, callback) {

                    script = document.createElement("script");

                    script.async = true;

                    if (s.scriptCharset) {
                        script.charset = s.scriptCharset;
                    }

                    script.src = s.url;

                    // Attach handlers for all browsers
                    script.onload = script.onreadystatechange = function (_, isAbort) {

                        if (isAbort || !script.readyState || /loaded|complete/.test(script.readyState)) {

                            // Handle memory leak in IE
                            script.onload = script.onreadystatechange = null;

                            // Remove the script
                            if (script.parentNode) {
                                script.parentNode.removeChild(script);
                            }

                            // Dereference the script
                            script = null;

                            // Callback if not abort
                            if (!isAbort) {
                                callback(200, "success");
                            }
                        }
                    };

                    // Circumvent IE6 bugs with base elements (#2709 and #4378) by prepending
                    // Use native DOM manipulation to avoid our domManip AJAX trickery
                    head.insertBefore(script, head.firstChild);
                },

                abort: function () {
                    if (script) {
                        script.onload(undefined, true);
                    }
                }
            };
        }
    });
    var oldCallbacks = [],
        rjsonp = /(=)\?(?=&|$)|\?\?/;

// Default jsonp settings
    jQuery.ajaxSetup({
        jsonp: "callback",
        jsonpCallback: function () {
            var callback = oldCallbacks.pop() || ( jQuery.expando + "_" + ( ajax_nonce++ ) );
            this[ callback ] = true;
            return callback;
        }
    });

// Detect, normalize options and install callbacks for jsonp requests
    jQuery.ajaxPrefilter("json jsonp", function (s, originalSettings, jqXHR) {

        var callbackName, overwritten, responseContainer,
            jsonProp = s.jsonp !== false && ( rjsonp.test(s.url) ?
                "url" :
                typeof s.data === "string" && !( s.contentType || "" ).indexOf("application/x-www-form-urlencoded") && rjsonp.test(s.data) && "data"
                );

        // Handle iff the expected data type is "jsonp" or we have a parameter to set
        if (jsonProp || s.dataTypes[ 0 ] === "jsonp") {

            // Get callback name, remembering preexisting value associated with it
            callbackName = s.jsonpCallback = jQuery.isFunction(s.jsonpCallback) ?
                s.jsonpCallback() :
                s.jsonpCallback;

            // Insert callback into url or form data
            if (jsonProp) {
                s[ jsonProp ] = s[ jsonProp ].replace(rjsonp, "$1" + callbackName);
            } else if (s.jsonp !== false) {
                s.url += ( ajax_rquery.test(s.url) ? "&" : "?" ) + s.jsonp + "=" + callbackName;
            }

            // Use data converter to retrieve json after script execution
            s.converters["script json"] = function () {
                if (!responseContainer) {
                    jQuery.error(callbackName + " was not called");
                }
                return responseContainer[ 0 ];
            };

            // force json dataType
            s.dataTypes[ 0 ] = "json";

            // Install callback
            overwritten = window[ callbackName ];
            window[ callbackName ] = function () {
                responseContainer = arguments;
            };

            // Clean-up function (fires after converters)
            jqXHR.always(function () {
                // Restore preexisting value
                window[ callbackName ] = overwritten;

                // Save back as free
                if (s[ callbackName ]) {
                    // make sure that re-using the options doesn't screw things around
                    s.jsonpCallback = originalSettings.jsonpCallback;

                    // save the callback name for future use
                    oldCallbacks.push(callbackName);
                }

                // Call if it was a function and we have a response
                if (responseContainer && jQuery.isFunction(overwritten)) {
                    overwritten(responseContainer[ 0 ]);
                }

                responseContainer = overwritten = undefined;
            });

            // Delegate to script
            return "script";
        }
    });
    var xhrCallbacks, xhrSupported,
        xhrId = 0,
    // #5280: Internet Explorer will keep connections alive if we don't abort on unload
        xhrOnUnloadAbort = window.ActiveXObject && function () {
            // Abort all pending requests
            var key;
            for (key in xhrCallbacks) {
                xhrCallbacks[ key ](undefined, true);
            }
        };

// Functions to create xhrs
    function createStandardXHR() {
        try {
            return new window.XMLHttpRequest();
        } catch (e) {
        }
    }

    function createActiveXHR() {
        try {
            return new window.ActiveXObject("Microsoft.XMLHTTP");
        } catch (e) {
        }
    }

// Create the request object
// (This is still attached to ajaxSettings for backward compatibility)
    jQuery.ajaxSettings.xhr = window.ActiveXObject ?
        /* Microsoft failed to properly
         * implement the XMLHttpRequest in IE7 (can't request local files),
         * so we use the ActiveXObject when it is available
         * Additionally XMLHttpRequest can be disabled in IE7/IE8 so
         * we need a fallback.
         */
        function () {
            return !this.isLocal && createStandardXHR() || createActiveXHR();
        } :
        // For all other browsers, use the standard XMLHttpRequest object
        createStandardXHR;

// Determine support properties
    xhrSupported = jQuery.ajaxSettings.xhr();
    jQuery.support.cors = !!xhrSupported && ( "withCredentials" in xhrSupported );
    xhrSupported = jQuery.support.ajax = !!xhrSupported;

// Create transport if the browser can provide an xhr
    if (xhrSupported) {

        jQuery.ajaxTransport(function (s) {
            // Cross domain only allowed if supported through XMLHttpRequest
            if (!s.crossDomain || jQuery.support.cors) {

                var callback;

                return {
                    send: function (headers, complete) {

                        // Get a new xhr
                        var handle, i,
                            xhr = s.xhr();

                        // Open the socket
                        // Passing null username, generates a login popup on Opera (#2865)
                        if (s.username) {
                            xhr.open(s.type, s.url, s.async, s.username, s.password);
                        } else {
                            xhr.open(s.type, s.url, s.async);
                        }

                        // Apply custom fields if provided
                        if (s.xhrFields) {
                            for (i in s.xhrFields) {
                                xhr[ i ] = s.xhrFields[ i ];
                            }
                        }

                        // Override mime type if needed
                        if (s.mimeType && xhr.overrideMimeType) {
                            xhr.overrideMimeType(s.mimeType);
                        }

                        // X-Requested-With header
                        // For cross-domain requests, seeing as conditions for a preflight are
                        // akin to a jigsaw puzzle, we simply never set it to be sure.
                        // (it can always be set on a per-request basis or even using ajaxSetup)
                        // For same-domain requests, won't change header if already provided.
                        if (!s.crossDomain && !headers["X-Requested-With"]) {
                            headers["X-Requested-With"] = "XMLHttpRequest";
                        }

                        // Need an extra try/catch for cross domain requests in Firefox 3
                        try {
                            for (i in headers) {
                                xhr.setRequestHeader(i, headers[ i ]);
                            }
                        } catch (err) {
                        }

                        // Do send the request
                        // This may raise an exception which is actually
                        // handled in jQuery.ajax (so no try/catch here)
                        xhr.send(( s.hasContent && s.data ) || null);

                        // Listener
                        callback = function (_, isAbort) {

                            var status,
                                statusText,
                                responseHeaders,
                                responses,
                                xml;

                            // Firefox throws exceptions when accessing properties
                            // of an xhr when a network error occurred
                            // http://helpful.knobs-dials.com/index.php/Component_returned_failure_code:_0x80040111_(NS_ERROR_NOT_AVAILABLE)
                            try {

                                // Was never called and is aborted or complete
                                if (callback && ( isAbort || xhr.readyState === 4 )) {

                                    // Only called once
                                    callback = undefined;

                                    // Do not keep as active anymore
                                    if (handle) {
                                        xhr.onreadystatechange = jQuery.noop;
                                        if (xhrOnUnloadAbort) {
                                            delete xhrCallbacks[ handle ];
                                        }
                                    }

                                    // If it's an abort
                                    if (isAbort) {
                                        // Abort it manually if needed
                                        if (xhr.readyState !== 4) {
                                            xhr.abort();
                                        }
                                    } else {
                                        responses = {};
                                        status = xhr.status;
                                        xml = xhr.responseXML;
                                        responseHeaders = xhr.getAllResponseHeaders();

                                        // Construct response list
                                        if (xml && xml.documentElement /* #4958 */) {
                                            responses.xml = xml;
                                        }

                                        // When requesting binary data, IE6-9 will throw an exception
                                        // on any attempt to access responseText (#11426)
                                        if (typeof xhr.responseText === "string") {
                                            responses.text = xhr.responseText;
                                        }

                                        // Firefox throws an exception when accessing
                                        // statusText for faulty cross-domain requests
                                        try {
                                            statusText = xhr.statusText;
                                        } catch (e) {
                                            // We normalize with Webkit giving an empty statusText
                                            statusText = "";
                                        }

                                        // Filter status for non standard behaviors

                                        // If the request is local and we have data: assume a success
                                        // (success with no data won't get notified, that's the best we
                                        // can do given current implementations)
                                        if (!status && s.isLocal && !s.crossDomain) {
                                            status = responses.text ? 200 : 404;
                                            // IE - #1450: sometimes returns 1223 when it should be 204
                                        } else if (status === 1223) {
                                            status = 204;
                                        }
                                    }
                                }
                            } catch (firefoxAccessException) {
                                if (!isAbort) {
                                    complete(-1, firefoxAccessException);
                                }
                            }

                            // Call complete if needed
                            if (responses) {
                                complete(status, statusText, responses, responseHeaders);
                            }
                        };

                        if (!s.async) {
                            // if we're in sync mode we fire the callback
                            callback();
                        } else if (xhr.readyState === 4) {
                            // (IE6 & IE7) if it's in cache and has been
                            // retrieved directly we need to fire the callback
                            setTimeout(callback);
                        } else {
                            handle = ++xhrId;
                            if (xhrOnUnloadAbort) {
                                // Create the active xhrs callbacks list if needed
                                // and attach the unload handler
                                if (!xhrCallbacks) {
                                    xhrCallbacks = {};
                                    jQuery(window).unload(xhrOnUnloadAbort);
                                }
                                // Add to list of active xhrs callbacks
                                xhrCallbacks[ handle ] = callback;
                            }
                            xhr.onreadystatechange = callback;
                        }
                    },

                    abort: function () {
                        if (callback) {
                            callback(undefined, true);
                        }
                    }
                };
            }
        });
    }
    var fxNow, timerId,
        rfxtypes = /^(?:toggle|show|hide)$/,
        rfxnum = new RegExp("^(?:([+-])=|)(" + core_pnum + ")([a-z%]*)$", "i"),
        rrun = /queueHooks$/,
        animationPrefilters = [ defaultPrefilter ],
        tweeners = {
            "*": [function (prop, value) {
                var end, unit,
                    tween = this.createTween(prop, value),
                    parts = rfxnum.exec(value),
                    target = tween.cur(),
                    start = +target || 0,
                    scale = 1,
                    maxIterations = 20;

                if (parts) {
                    end = +parts[2];
                    unit = parts[3] || ( jQuery.cssNumber[ prop ] ? "" : "px" );

                    // We need to compute starting value
                    if (unit !== "px" && start) {
                        // Iteratively approximate from a nonzero starting point
                        // Prefer the current property, because this process will be trivial if it uses the same units
                        // Fallback to end or a simple constant
                        start = jQuery.css(tween.elem, prop, true) || end || 1;

                        do {
                            // If previous iteration zeroed out, double until we get *something*
                            // Use a string for doubling factor so we don't accidentally see scale as unchanged below
                            scale = scale || ".5";

                            // Adjust and apply
                            start = start / scale;
                            jQuery.style(tween.elem, prop, start + unit);

                            // Update scale, tolerating zero or NaN from tween.cur()
                            // And breaking the loop if scale is unchanged or perfect, or if we've just had enough
                        } while (scale !== (scale = tween.cur() / target) && scale !== 1 && --maxIterations);
                    }

                    tween.unit = unit;
                    tween.start = start;
                    // If a +=/-= token was provided, we're doing a relative animation
                    tween.end = parts[1] ? start + ( parts[1] + 1 ) * end : end;
                }
                return tween;
            }]
        };

// Animations created synchronously will run synchronously
    function createFxNow() {
        setTimeout(function () {
            fxNow = undefined;
        });
        return ( fxNow = jQuery.now() );
    }

    function createTweens(animation, props) {
        jQuery.each(props, function (prop, value) {
            var collection = ( tweeners[ prop ] || [] ).concat(tweeners[ "*" ]),
                index = 0,
                length = collection.length;
            for (; index < length; index++) {
                if (collection[ index ].call(animation, prop, value)) {

                    // we're done with this property
                    return;
                }
            }
        });
    }

    function Animation(elem, properties, options) {
        var result,
            stopped,
            index = 0,
            length = animationPrefilters.length,
            deferred = jQuery.Deferred().always(function () {
                // don't match elem in the :animated selector
                delete tick.elem;
            }),
            tick = function () {
                if (stopped) {
                    return false;
                }
                var currentTime = fxNow || createFxNow(),
                    remaining = Math.max(0, animation.startTime + animation.duration - currentTime),
                // archaic crash bug won't allow us to use 1 - ( 0.5 || 0 ) (#12497)
                    temp = remaining / animation.duration || 0,
                    percent = 1 - temp,
                    index = 0,
                    length = animation.tweens.length;

                for (; index < length; index++) {
                    animation.tweens[ index ].run(percent);
                }

                deferred.notifyWith(elem, [ animation, percent, remaining ]);

                if (percent < 1 && length) {
                    return remaining;
                } else {
                    deferred.resolveWith(elem, [ animation ]);
                    return false;
                }
            },
            animation = deferred.promise({
                elem: elem,
                props: jQuery.extend({}, properties),
                opts: jQuery.extend(true, { specialEasing: {} }, options),
                originalProperties: properties,
                originalOptions: options,
                startTime: fxNow || createFxNow(),
                duration: options.duration,
                tweens: [],
                createTween: function (prop, end) {
                    var tween = jQuery.Tween(elem, animation.opts, prop, end,
                        animation.opts.specialEasing[ prop ] || animation.opts.easing);
                    animation.tweens.push(tween);
                    return tween;
                },
                stop: function (gotoEnd) {
                    var index = 0,
                    // if we are going to the end, we want to run all the tweens
                    // otherwise we skip this part
                        length = gotoEnd ? animation.tweens.length : 0;
                    if (stopped) {
                        return this;
                    }
                    stopped = true;
                    for (; index < length; index++) {
                        animation.tweens[ index ].run(1);
                    }

                    // resolve when we played the last frame
                    // otherwise, reject
                    if (gotoEnd) {
                        deferred.resolveWith(elem, [ animation, gotoEnd ]);
                    } else {
                        deferred.rejectWith(elem, [ animation, gotoEnd ]);
                    }
                    return this;
                }
            }),
            props = animation.props;

        propFilter(props, animation.opts.specialEasing);

        for (; index < length; index++) {
            result = animationPrefilters[ index ].call(animation, elem, props, animation.opts);
            if (result) {
                return result;
            }
        }

        createTweens(animation, props);

        if (jQuery.isFunction(animation.opts.start)) {
            animation.opts.start.call(elem, animation);
        }

        jQuery.fx.timer(
            jQuery.extend(tick, {
                elem: elem,
                anim: animation,
                queue: animation.opts.queue
            })
        );

        // attach callbacks from options
        return animation.progress(animation.opts.progress)
            .done(animation.opts.done, animation.opts.complete)
            .fail(animation.opts.fail)
            .always(animation.opts.always);
    }

    function propFilter(props, specialEasing) {
        var index, name, easing, value, hooks;

        // camelCase, specialEasing and expand cssHook pass
        for (index in props) {
            name = jQuery.camelCase(index);
            easing = specialEasing[ name ];
            value = props[ index ];
            if (jQuery.isArray(value)) {
                easing = value[ 1 ];
                value = props[ index ] = value[ 0 ];
            }

            if (index !== name) {
                props[ name ] = value;
                delete props[ index ];
            }

            hooks = jQuery.cssHooks[ name ];
            if (hooks && "expand" in hooks) {
                value = hooks.expand(value);
                delete props[ name ];

                // not quite $.extend, this wont overwrite keys already present.
                // also - reusing 'index' from above because we have the correct "name"
                for (index in value) {
                    if (!( index in props )) {
                        props[ index ] = value[ index ];
                        specialEasing[ index ] = easing;
                    }
                }
            } else {
                specialEasing[ name ] = easing;
            }
        }
    }

    jQuery.Animation = jQuery.extend(Animation, {

        tweener: function (props, callback) {
            if (jQuery.isFunction(props)) {
                callback = props;
                props = [ "*" ];
            } else {
                props = props.split(" ");
            }

            var prop,
                index = 0,
                length = props.length;

            for (; index < length; index++) {
                prop = props[ index ];
                tweeners[ prop ] = tweeners[ prop ] || [];
                tweeners[ prop ].unshift(callback);
            }
        },

        prefilter: function (callback, prepend) {
            if (prepend) {
                animationPrefilters.unshift(callback);
            } else {
                animationPrefilters.push(callback);
            }
        }
    });

    function defaultPrefilter(elem, props, opts) {
        /*jshint validthis:true */
        var index, prop, value, length, dataShow, toggle, tween, hooks, oldfire,
            anim = this,
            style = elem.style,
            orig = {},
            handled = [],
            hidden = elem.nodeType && isHidden(elem);

        // handle queue: false promises
        if (!opts.queue) {
            hooks = jQuery._queueHooks(elem, "fx");
            if (hooks.unqueued == null) {
                hooks.unqueued = 0;
                oldfire = hooks.empty.fire;
                hooks.empty.fire = function () {
                    if (!hooks.unqueued) {
                        oldfire();
                    }
                };
            }
            hooks.unqueued++;

            anim.always(function () {
                // doing this makes sure that the complete handler will be called
                // before this completes
                anim.always(function () {
                    hooks.unqueued--;
                    if (!jQuery.queue(elem, "fx").length) {
                        hooks.empty.fire();
                    }
                });
            });
        }

        // height/width overflow pass
        if (elem.nodeType === 1 && ( "height" in props || "width" in props )) {
            // Make sure that nothing sneaks out
            // Record all 3 overflow attributes because IE does not
            // change the overflow attribute when overflowX and
            // overflowY are set to the same value
            opts.overflow = [ style.overflow, style.overflowX, style.overflowY ];

            // Set display property to inline-block for height/width
            // animations on inline elements that are having width/height animated
            if (jQuery.css(elem, "display") === "inline" &&
                jQuery.css(elem, "float") === "none") {

                // inline-level elements accept inline-block;
                // block-level elements need to be inline with layout
                if (!jQuery.support.inlineBlockNeedsLayout || css_defaultDisplay(elem.nodeName) === "inline") {
                    style.display = "inline-block";

                } else {
                    style.zoom = 1;
                }
            }
        }

        if (opts.overflow) {
            style.overflow = "hidden";
            if (!jQuery.support.shrinkWrapBlocks) {
                anim.done(function () {
                    style.overflow = opts.overflow[ 0 ];
                    style.overflowX = opts.overflow[ 1 ];
                    style.overflowY = opts.overflow[ 2 ];
                });
            }
        }


        // show/hide pass
        for (index in props) {
            value = props[ index ];
            if (rfxtypes.exec(value)) {
                delete props[ index ];
                toggle = toggle || value === "toggle";
                if (value === ( hidden ? "hide" : "show" )) {
                    continue;
                }
                handled.push(index);
            }
        }

        length = handled.length;
        if (length) {
            dataShow = jQuery._data(elem, "fxshow") || jQuery._data(elem, "fxshow", {});
            if ("hidden" in dataShow) {
                hidden = dataShow.hidden;
            }

            // store state if its toggle - enables .stop().toggle() to "reverse"
            if (toggle) {
                dataShow.hidden = !hidden;
            }
            if (hidden) {
                jQuery(elem).show();
            } else {
                anim.done(function () {
                    jQuery(elem).hide();
                });
            }
            anim.done(function () {
                var prop;
                jQuery._removeData(elem, "fxshow");
                for (prop in orig) {
                    jQuery.style(elem, prop, orig[ prop ]);
                }
            });
            for (index = 0; index < length; index++) {
                prop = handled[ index ];
                tween = anim.createTween(prop, hidden ? dataShow[ prop ] : 0);
                orig[ prop ] = dataShow[ prop ] || jQuery.style(elem, prop);

                if (!( prop in dataShow )) {
                    dataShow[ prop ] = tween.start;
                    if (hidden) {
                        tween.end = tween.start;
                        tween.start = prop === "width" || prop === "height" ? 1 : 0;
                    }
                }
            }
        }
    }

    function Tween(elem, options, prop, end, easing) {
        return new Tween.prototype.init(elem, options, prop, end, easing);
    }

    jQuery.Tween = Tween;

    Tween.prototype = {
        constructor: Tween,
        init: function (elem, options, prop, end, easing, unit) {
            this.elem = elem;
            this.prop = prop;
            this.easing = easing || "swing";
            this.options = options;
            this.start = this.now = this.cur();
            this.end = end;
            this.unit = unit || ( jQuery.cssNumber[ prop ] ? "" : "px" );
        },
        cur: function () {
            var hooks = Tween.propHooks[ this.prop ];

            return hooks && hooks.get ?
                hooks.get(this) :
                Tween.propHooks._default.get(this);
        },
        run: function (percent) {
            var eased,
                hooks = Tween.propHooks[ this.prop ];

            if (this.options.duration) {
                this.pos = eased = jQuery.easing[ this.easing ](
                    percent, this.options.duration * percent, 0, 1, this.options.duration
                );
            } else {
                this.pos = eased = percent;
            }
            this.now = ( this.end - this.start ) * eased + this.start;

            if (this.options.step) {
                this.options.step.call(this.elem, this.now, this);
            }

            if (hooks && hooks.set) {
                hooks.set(this);
            } else {
                Tween.propHooks._default.set(this);
            }
            return this;
        }
    };

    Tween.prototype.init.prototype = Tween.prototype;

    Tween.propHooks = {
        _default: {
            get: function (tween) {
                var result;

                if (tween.elem[ tween.prop ] != null &&
                    (!tween.elem.style || tween.elem.style[ tween.prop ] == null)) {
                    return tween.elem[ tween.prop ];
                }

                // passing a non empty string as a 3rd parameter to .css will automatically
                // attempt a parseFloat and fallback to a string if the parse fails
                // so, simple values such as "10px" are parsed to Float.
                // complex values such as "rotate(1rad)" are returned as is.
                result = jQuery.css(tween.elem, tween.prop, "auto");
                // Empty strings, null, undefined and "auto" are converted to 0.
                return !result || result === "auto" ? 0 : result;
            },
            set: function (tween) {
                // use step hook for back compat - use cssHook if its there - use .style if its
                // available and use plain properties where available
                if (jQuery.fx.step[ tween.prop ]) {
                    jQuery.fx.step[ tween.prop ](tween);
                } else if (tween.elem.style && ( tween.elem.style[ jQuery.cssProps[ tween.prop ] ] != null || jQuery.cssHooks[ tween.prop ] )) {
                    jQuery.style(tween.elem, tween.prop, tween.now + tween.unit);
                } else {
                    tween.elem[ tween.prop ] = tween.now;
                }
            }
        }
    };

// Remove in 2.0 - this supports IE8's panic based approach
// to setting things on disconnected nodes

    Tween.propHooks.scrollTop = Tween.propHooks.scrollLeft = {
        set: function (tween) {
            if (tween.elem.nodeType && tween.elem.parentNode) {
                tween.elem[ tween.prop ] = tween.now;
            }
        }
    };

    jQuery.each([ "toggle", "show", "hide" ], function (i, name) {
        var cssFn = jQuery.fn[ name ];
        jQuery.fn[ name ] = function (speed, easing, callback) {
            return speed == null || typeof speed === "boolean" ?
                cssFn.apply(this, arguments) :
                this.animate(genFx(name, true), speed, easing, callback);
        };
    });

    jQuery.fn.extend({
        fadeTo: function (speed, to, easing, callback) {

            // show any hidden elements after setting opacity to 0
            return this.filter(isHidden).css("opacity", 0).show()

                // animate to the value specified
                .end().animate({ opacity: to }, speed, easing, callback);
        },
        animate: function (prop, speed, easing, callback) {
            var empty = jQuery.isEmptyObject(prop),
                optall = jQuery.speed(speed, easing, callback),
                doAnimation = function () {
                    // Operate on a copy of prop so per-property easing won't be lost
                    var anim = Animation(this, jQuery.extend({}, prop), optall);
                    doAnimation.finish = function () {
                        anim.stop(true);
                    };
                    // Empty animations, or finishing resolves immediately
                    if (empty || jQuery._data(this, "finish")) {
                        anim.stop(true);
                    }
                };
            doAnimation.finish = doAnimation;

            return empty || optall.queue === false ?
                this.each(doAnimation) :
                this.queue(optall.queue, doAnimation);
        },
        stop: function (type, clearQueue, gotoEnd) {
            var stopQueue = function (hooks) {
                var stop = hooks.stop;
                delete hooks.stop;
                stop(gotoEnd);
            };

            if (typeof type !== "string") {
                gotoEnd = clearQueue;
                clearQueue = type;
                type = undefined;
            }
            if (clearQueue && type !== false) {
                this.queue(type || "fx", []);
            }

            return this.each(function () {
                var dequeue = true,
                    index = type != null && type + "queueHooks",
                    timers = jQuery.timers,
                    data = jQuery._data(this);

                if (index) {
                    if (data[ index ] && data[ index ].stop) {
                        stopQueue(data[ index ]);
                    }
                } else {
                    for (index in data) {
                        if (data[ index ] && data[ index ].stop && rrun.test(index)) {
                            stopQueue(data[ index ]);
                        }
                    }
                }

                for (index = timers.length; index--;) {
                    if (timers[ index ].elem === this && (type == null || timers[ index ].queue === type)) {
                        timers[ index ].anim.stop(gotoEnd);
                        dequeue = false;
                        timers.splice(index, 1);
                    }
                }

                // start the next in the queue if the last step wasn't forced
                // timers currently will call their complete callbacks, which will dequeue
                // but only if they were gotoEnd
                if (dequeue || !gotoEnd) {
                    jQuery.dequeue(this, type);
                }
            });
        },
        finish: function (type) {
            if (type !== false) {
                type = type || "fx";
            }
            return this.each(function () {
                var index,
                    data = jQuery._data(this),
                    queue = data[ type + "queue" ],
                    hooks = data[ type + "queueHooks" ],
                    timers = jQuery.timers,
                    length = queue ? queue.length : 0;

                // enable finishing flag on private data
                data.finish = true;

                // empty the queue first
                jQuery.queue(this, type, []);

                if (hooks && hooks.cur && hooks.cur.finish) {
                    hooks.cur.finish.call(this);
                }

                // look for any active animations, and finish them
                for (index = timers.length; index--;) {
                    if (timers[ index ].elem === this && timers[ index ].queue === type) {
                        timers[ index ].anim.stop(true);
                        timers.splice(index, 1);
                    }
                }

                // look for any animations in the old queue and finish them
                for (index = 0; index < length; index++) {
                    if (queue[ index ] && queue[ index ].finish) {
                        queue[ index ].finish.call(this);
                    }
                }

                // turn off finishing flag
                delete data.finish;
            });
        }
    });

// Generate parameters to create a standard animation
    function genFx(type, includeWidth) {
        var which,
            attrs = { height: type },
            i = 0;

        // if we include width, step value is 1 to do all cssExpand values,
        // if we don't include width, step value is 2 to skip over Left and Right
        includeWidth = includeWidth ? 1 : 0;
        for (; i < 4; i += 2 - includeWidth) {
            which = cssExpand[ i ];
            attrs[ "margin" + which ] = attrs[ "padding" + which ] = type;
        }

        if (includeWidth) {
            attrs.opacity = attrs.width = type;
        }

        return attrs;
    }

// Generate shortcuts for custom animations
    jQuery.each({
        slideDown: genFx("show"),
        slideUp: genFx("hide"),
        slideToggle: genFx("toggle"),
        fadeIn: { opacity: "show" },
        fadeOut: { opacity: "hide" },
        fadeToggle: { opacity: "toggle" }
    }, function (name, props) {
        jQuery.fn[ name ] = function (speed, easing, callback) {
            return this.animate(props, speed, easing, callback);
        };
    });

    jQuery.speed = function (speed, easing, fn) {
        var opt = speed && typeof speed === "object" ? jQuery.extend({}, speed) : {
            complete: fn || !fn && easing ||
                jQuery.isFunction(speed) && speed,
            duration: speed,
            easing: fn && easing || easing && !jQuery.isFunction(easing) && easing
        };

        opt.duration = jQuery.fx.off ? 0 : typeof opt.duration === "number" ? opt.duration :
            opt.duration in jQuery.fx.speeds ? jQuery.fx.speeds[ opt.duration ] : jQuery.fx.speeds._default;

        // normalize opt.queue - true/undefined/null -> "fx"
        if (opt.queue == null || opt.queue === true) {
            opt.queue = "fx";
        }

        // Queueing
        opt.old = opt.complete;

        opt.complete = function () {
            if (jQuery.isFunction(opt.old)) {
                opt.old.call(this);
            }

            if (opt.queue) {
                jQuery.dequeue(this, opt.queue);
            }
        };

        return opt;
    };

    jQuery.easing = {
        linear: function (p) {
            return p;
        },
        swing: function (p) {
            return 0.5 - Math.cos(p * Math.PI) / 2;
        }
    };

    jQuery.timers = [];
    jQuery.fx = Tween.prototype.init;
    jQuery.fx.tick = function () {
        var timer,
            timers = jQuery.timers,
            i = 0;

        fxNow = jQuery.now();

        for (; i < timers.length; i++) {
            timer = timers[ i ];
            // Checks the timer has not already been removed
            if (!timer() && timers[ i ] === timer) {
                timers.splice(i--, 1);
            }
        }

        if (!timers.length) {
            jQuery.fx.stop();
        }
        fxNow = undefined;
    };

    jQuery.fx.timer = function (timer) {
        if (timer() && jQuery.timers.push(timer)) {
            jQuery.fx.start();
        }
    };

    jQuery.fx.interval = 13;

    jQuery.fx.start = function () {
        if (!timerId) {
            timerId = setInterval(jQuery.fx.tick, jQuery.fx.interval);
        }
    };

    jQuery.fx.stop = function () {
        clearInterval(timerId);
        timerId = null;
    };

    jQuery.fx.speeds = {
        slow: 600,
        fast: 200,
        // Default speed
        _default: 400
    };

// Back Compat <1.8 extension point
    jQuery.fx.step = {};

    if (jQuery.expr && jQuery.expr.filters) {
        jQuery.expr.filters.animated = function (elem) {
            return jQuery.grep(jQuery.timers,function (fn) {
                return elem === fn.elem;
            }).length;
        };
    }
    jQuery.fn.offset = function (options) {
        if (arguments.length) {
            return options === undefined ?
                this :
                this.each(function (i) {
                    jQuery.offset.setOffset(this, options, i);
                });
        }

        var docElem, win,
            box = { top: 0, left: 0 },
            elem = this[ 0 ],
            doc = elem && elem.ownerDocument;

        if (!doc) {
            return;
        }

        docElem = doc.documentElement;

        // Make sure it's not a disconnected DOM node
        if (!jQuery.contains(docElem, elem)) {
            return box;
        }

        // If we don't have gBCR, just use 0,0 rather than error
        // BlackBerry 5, iOS 3 (original iPhone)
        if (typeof elem.getBoundingClientRect !== "undefined") {
            box = elem.getBoundingClientRect();
        }
        win = getWindow(doc);
        return {
            top: box.top + ( win.pageYOffset || docElem.scrollTop ) - ( docElem.clientTop || 0 ),
            left: box.left + ( win.pageXOffset || docElem.scrollLeft ) - ( docElem.clientLeft || 0 )
        };
    };

    jQuery.offset = {

        setOffset: function (elem, options, i) {
            var position = jQuery.css(elem, "position");

            // set position first, in-case top/left are set even on static elem
            if (position === "static") {
                elem.style.position = "relative";
            }

            var curElem = jQuery(elem),
                curOffset = curElem.offset(),
                curCSSTop = jQuery.css(elem, "top"),
                curCSSLeft = jQuery.css(elem, "left"),
                calculatePosition = ( position === "absolute" || position === "fixed" ) && jQuery.inArray("auto", [curCSSTop, curCSSLeft]) > -1,
                props = {}, curPosition = {}, curTop, curLeft;

            // need to be able to calculate position if either top or left is auto and position is either absolute or fixed
            if (calculatePosition) {
                curPosition = curElem.position();
                curTop = curPosition.top;
                curLeft = curPosition.left;
            } else {
                curTop = parseFloat(curCSSTop) || 0;
                curLeft = parseFloat(curCSSLeft) || 0;
            }

            if (jQuery.isFunction(options)) {
                options = options.call(elem, i, curOffset);
            }

            if (options.top != null) {
                props.top = ( options.top - curOffset.top ) + curTop;
            }
            if (options.left != null) {
                props.left = ( options.left - curOffset.left ) + curLeft;
            }

            if ("using" in options) {
                options.using.call(elem, props);
            } else {
                curElem.css(props);
            }
        }
    };


    jQuery.fn.extend({

        position: function () {
            if (!this[ 0 ]) {
                return;
            }

            var offsetParent, offset,
                parentOffset = { top: 0, left: 0 },
                elem = this[ 0 ];

            // fixed elements are offset from window (parentOffset = {top:0, left: 0}, because it is it's only offset parent
            if (jQuery.css(elem, "position") === "fixed") {
                // we assume that getBoundingClientRect is available when computed position is fixed
                offset = elem.getBoundingClientRect();
            } else {
                // Get *real* offsetParent
                offsetParent = this.offsetParent();

                // Get correct offsets
                offset = this.offset();
                if (!jQuery.nodeName(offsetParent[ 0 ], "html")) {
                    parentOffset = offsetParent.offset();
                }

                // Add offsetParent borders
                parentOffset.top += jQuery.css(offsetParent[ 0 ], "borderTopWidth", true);
                parentOffset.left += jQuery.css(offsetParent[ 0 ], "borderLeftWidth", true);
            }

            // Subtract parent offsets and element margins
            // note: when an element has margin: auto the offsetLeft and marginLeft
            // are the same in Safari causing offset.left to incorrectly be 0
            return {
                top: offset.top - parentOffset.top - jQuery.css(elem, "marginTop", true),
                left: offset.left - parentOffset.left - jQuery.css(elem, "marginLeft", true)
            };
        },

        offsetParent: function () {
            return this.map(function () {
                var offsetParent = this.offsetParent || document.documentElement;
                while (offsetParent && ( !jQuery.nodeName(offsetParent, "html") && jQuery.css(offsetParent, "position") === "static" )) {
                    offsetParent = offsetParent.offsetParent;
                }
                return offsetParent || document.documentElement;
            });
        }
    });


// Create scrollLeft and scrollTop methods
    jQuery.each({scrollLeft: "pageXOffset", scrollTop: "pageYOffset"}, function (method, prop) {
        var top = /Y/.test(prop);

        jQuery.fn[ method ] = function (val) {
            return jQuery.access(this, function (elem, method, val) {
                var win = getWindow(elem);

                if (val === undefined) {
                    return win ? (prop in win) ? win[ prop ] :
                        win.document.documentElement[ method ] :
                        elem[ method ];
                }

                if (win) {
                    win.scrollTo(
                        !top ? val : jQuery(win).scrollLeft(),
                        top ? val : jQuery(win).scrollTop()
                    );

                } else {
                    elem[ method ] = val;
                }
            }, method, val, arguments.length, null);
        };
    });

    function getWindow(elem) {
        return jQuery.isWindow(elem) ?
            elem :
            elem.nodeType === 9 ?
                elem.defaultView || elem.parentWindow :
                false;
    }

// Create innerHeight, innerWidth, height, width, outerHeight and outerWidth methods
    jQuery.each({ Height: "height", Width: "width" }, function (name, type) {
        jQuery.each({ padding: "inner" + name, content: type, "": "outer" + name }, function (defaultExtra, funcName) {
            // margin is only for outerHeight, outerWidth
            jQuery.fn[ funcName ] = function (margin, value) {
                var chainable = arguments.length && ( defaultExtra || typeof margin !== "boolean" ),
                    extra = defaultExtra || ( margin === true || value === true ? "margin" : "border" );

                return jQuery.access(this, function (elem, type, value) {
                    var doc;

                    if (jQuery.isWindow(elem)) {
                        // As of 5/8/2012 this will yield incorrect results for Mobile Safari, but there
                        // isn't a whole lot we can do. See pull request at this URL for discussion:
                        // https://github.com/jquery/jquery/pull/764
                        return elem.document.documentElement[ "client" + name ];
                    }

                    // Get document width or height
                    if (elem.nodeType === 9) {
                        doc = elem.documentElement;

                        // Either scroll[Width/Height] or offset[Width/Height] or client[Width/Height], whichever is greatest
                        // unfortunately, this causes bug #3838 in IE6/8 only, but there is currently no good, small way to fix it.
                        return Math.max(
                            elem.body[ "scroll" + name ], doc[ "scroll" + name ],
                            elem.body[ "offset" + name ], doc[ "offset" + name ],
                            doc[ "client" + name ]
                        );
                    }

                    return value === undefined ?
                        // Get width or height on the element, requesting but not forcing parseFloat
                        jQuery.css(elem, type, extra) :

                        // Set width or height on the element
                        jQuery.style(elem, type, value, extra);
                }, type, chainable ? margin : undefined, chainable, null);
            };
        });
    });
// Limit scope pollution from any deprecated API
// (function() {

// })();
// Expose jQuery to the global object
    window.jQuery = window.$ = jQuery;

// Expose jQuery as an AMD module, but only for AMD loaders that
// understand the issues with loading multiple versions of jQuery
// in a page that all might call define(). The loader will indicate
// they have special allowances for multiple jQuery versions by
// specifying define.amd.jQuery = true. Register as a named module,
// since jQuery can be concatenated with other files that may use define,
// but not use a proper concatenation script that understands anonymous
// AMD modules. A named AMD is safest and most robust way to register.
// Lowercase jquery is used because AMD module names are derived from
// file names, and jQuery is normally delivered in a lowercase file name.
// Do this after creating the global so that if an AMD module wants to call
// noConflict to hide this version of jQuery, it will work.
    if (typeof define === "function" && define.amd && define.amd.jQuery) {
        define("jquery", [], function () {
            return jQuery;
        });
    }

})(window);

//     Underscore.js 1.4.3
//     http://underscorejs.org
//     (c) 2009-2012 Jeremy Ashkenas, DocumentCloud Inc.
//     Underscore may be freely distributed under the MIT license.

(function () {

    // Baseline setup
    // --------------

    // Establish the root object, `window` in the browser, or `global` on the server.
    var root = this;

    // Save the previous value of the `_` variable.
    var previousUnderscore = root._;

    // Establish the object that gets returned to break out of a loop iteration.
    var breaker = {};

    // Save bytes in the minified (but not gzipped) version:
    var ArrayProto = Array.prototype, ObjProto = Object.prototype, FuncProto = Function.prototype;

    // Create quick reference variables for speed access to core prototypes.
    var push = ArrayProto.push,
        slice = ArrayProto.slice,
        concat = ArrayProto.concat,
        toString = ObjProto.toString,
        hasOwnProperty = ObjProto.hasOwnProperty;

    // All **ECMAScript 5** native function implementations that we hope to use
    // are declared here.
    var
        nativeForEach = ArrayProto.forEach,
        nativeMap = ArrayProto.map,
        nativeReduce = ArrayProto.reduce,
        nativeReduceRight = ArrayProto.reduceRight,
        nativeFilter = ArrayProto.filter,
        nativeEvery = ArrayProto.every,
        nativeSome = ArrayProto.some,
        nativeIndexOf = ArrayProto.indexOf,
        nativeLastIndexOf = ArrayProto.lastIndexOf,
        nativeIsArray = Array.isArray,
        nativeKeys = Object.keys,
        nativeBind = FuncProto.bind;

    // Create a safe reference to the Underscore object for use below.
    var _ = function (obj) {
        if (obj instanceof _) return obj;
        if (!(this instanceof _)) return new _(obj);
        this._wrapped = obj;
    };

    // Export the Underscore object for **Node.js**, with
    // backwards-compatibility for the old `require()` API. If we're in
    // the browser, add `_` as a global object via a string identifier,
    // for Closure Compiler "advanced" mode.
    if (typeof exports !== 'undefined') {
        if (typeof module !== 'undefined' && module.exports) {
            exports = module.exports = _;
        }
        exports._ = _;
    } else {
        root._ = _;
    }

    // Current version.
    _.VERSION = '1.4.3';

    // Collection Functions
    // --------------------

    // The cornerstone, an `each` implementation, aka `forEach`.
    // Handles objects with the built-in `forEach`, arrays, and raw objects.
    // Delegates to **ECMAScript 5**'s native `forEach` if available.
    var each = _.each = _.forEach = function (obj, iterator, context) {
        if (obj == null) return;
        if (nativeForEach && obj.forEach === nativeForEach) {
            obj.forEach(iterator, context);
        } else if (obj.length === +obj.length) {
            for (var i = 0, l = obj.length; i < l; i++) {
                if (iterator.call(context, obj[i], i, obj) === breaker) return;
            }
        } else {
            for (var key in obj) {
                if (_.has(obj, key)) {
                    if (iterator.call(context, obj[key], key, obj) === breaker) return;
                }
            }
        }
    };

    // Return the results of applying the iterator to each element.
    // Delegates to **ECMAScript 5**'s native `map` if available.
    _.map = _.collect = function (obj, iterator, context) {
        var results = [];
        if (obj == null) return results;
        if (nativeMap && obj.map === nativeMap) return obj.map(iterator, context);
        each(obj, function (value, index, list) {
            results[results.length] = iterator.call(context, value, index, list);
        });
        return results;
    };

    var reduceError = 'Reduce of empty array with no initial value';

    // **Reduce** builds up a single result from a list of values, aka `inject`,
    // or `foldl`. Delegates to **ECMAScript 5**'s native `reduce` if available.
    _.reduce = _.foldl = _.inject = function (obj, iterator, memo, context) {
        var initial = arguments.length > 2;
        if (obj == null) obj = [];
        if (nativeReduce && obj.reduce === nativeReduce) {
            if (context) iterator = _.bind(iterator, context);
            return initial ? obj.reduce(iterator, memo) : obj.reduce(iterator);
        }
        each(obj, function (value, index, list) {
            if (!initial) {
                memo = value;
                initial = true;
            } else {
                memo = iterator.call(context, memo, value, index, list);
            }
        });
        if (!initial) throw new TypeError(reduceError);
        return memo;
    };

    // The right-associative version of reduce, also known as `foldr`.
    // Delegates to **ECMAScript 5**'s native `reduceRight` if available.
    _.reduceRight = _.foldr = function (obj, iterator, memo, context) {
        var initial = arguments.length > 2;
        if (obj == null) obj = [];
        if (nativeReduceRight && obj.reduceRight === nativeReduceRight) {
            if (context) iterator = _.bind(iterator, context);
            return initial ? obj.reduceRight(iterator, memo) : obj.reduceRight(iterator);
        }
        var length = obj.length;
        if (length !== +length) {
            var keys = _.keys(obj);
            length = keys.length;
        }
        each(obj, function (value, index, list) {
            index = keys ? keys[--length] : --length;
            if (!initial) {
                memo = obj[index];
                initial = true;
            } else {
                memo = iterator.call(context, memo, obj[index], index, list);
            }
        });
        if (!initial) throw new TypeError(reduceError);
        return memo;
    };

    // Return the first value which passes a truth test. Aliased as `detect`.
    _.find = _.detect = function (obj, iterator, context) {
        var result;
        any(obj, function (value, index, list) {
            if (iterator.call(context, value, index, list)) {
                result = value;
                return true;
            }
        });
        return result;
    };

    // Return all the elements that pass a truth test.
    // Delegates to **ECMAScript 5**'s native `filter` if available.
    // Aliased as `select`.
    _.filter = _.select = function (obj, iterator, context) {
        var results = [];
        if (obj == null) return results;
        if (nativeFilter && obj.filter === nativeFilter) return obj.filter(iterator, context);
        each(obj, function (value, index, list) {
            if (iterator.call(context, value, index, list)) results[results.length] = value;
        });
        return results;
    };

    // Return all the elements for which a truth test fails.
    _.reject = function (obj, iterator, context) {
        return _.filter(obj, function (value, index, list) {
            return !iterator.call(context, value, index, list);
        }, context);
    };

    // Determine whether all of the elements match a truth test.
    // Delegates to **ECMAScript 5**'s native `every` if available.
    // Aliased as `all`.
    _.every = _.all = function (obj, iterator, context) {
        iterator || (iterator = _.identity);
        var result = true;
        if (obj == null) return result;
        if (nativeEvery && obj.every === nativeEvery) return obj.every(iterator, context);
        each(obj, function (value, index, list) {
            if (!(result = result && iterator.call(context, value, index, list))) return breaker;
        });
        return !!result;
    };

    // Determine if at least one element in the object matches a truth test.
    // Delegates to **ECMAScript 5**'s native `some` if available.
    // Aliased as `any`.
    var any = _.some = _.any = function (obj, iterator, context) {
        iterator || (iterator = _.identity);
        var result = false;
        if (obj == null) return result;
        if (nativeSome && obj.some === nativeSome) return obj.some(iterator, context);
        each(obj, function (value, index, list) {
            if (result || (result = iterator.call(context, value, index, list))) return breaker;
        });
        return !!result;
    };

    // Determine if the array or object contains a given value (using `===`).
    // Aliased as `include`.
    _.contains = _.include = function (obj, target) {
        if (obj == null) return false;
        if (nativeIndexOf && obj.indexOf === nativeIndexOf) return obj.indexOf(target) != -1;
        return any(obj, function (value) {
            return value === target;
        });
    };

    // Invoke a method (with arguments) on every item in a collection.
    _.invoke = function (obj, method) {
        var args = slice.call(arguments, 2);
        return _.map(obj, function (value) {
            return (_.isFunction(method) ? method : value[method]).apply(value, args);
        });
    };

    // Convenience version of a common use case of `map`: fetching a property.
    _.pluck = function (obj, key) {
        return _.map(obj, function (value) {
            return value[key];
        });
    };

    // Convenience version of a common use case of `filter`: selecting only objects
    // with specific `key:value` pairs.
    _.where = function (obj, attrs) {
        if (_.isEmpty(attrs)) return [];
        return _.filter(obj, function (value) {
            for (var key in attrs) {
                if (attrs[key] !== value[key]) return false;
            }
            return true;
        });
    };

    // Return the maximum element or (element-based computation).
    // Can't optimize arrays of integers longer than 65,535 elements.
    // See: https://bugs.webkit.org/show_bug.cgi?id=80797
    _.max = function (obj, iterator, context) {
        if (!iterator && _.isArray(obj) && obj[0] === +obj[0] && obj.length < 65535) {
            return Math.max.apply(Math, obj);
        }
        if (!iterator && _.isEmpty(obj)) return -Infinity;
        var result = {computed: -Infinity, value: -Infinity};
        each(obj, function (value, index, list) {
            var computed = iterator ? iterator.call(context, value, index, list) : value;
            computed >= result.computed && (result = {value: value, computed: computed});
        });
        return result.value;
    };

    // Return the minimum element (or element-based computation).
    _.min = function (obj, iterator, context) {
        if (!iterator && _.isArray(obj) && obj[0] === +obj[0] && obj.length < 65535) {
            return Math.min.apply(Math, obj);
        }
        if (!iterator && _.isEmpty(obj)) return Infinity;
        var result = {computed: Infinity, value: Infinity};
        each(obj, function (value, index, list) {
            var computed = iterator ? iterator.call(context, value, index, list) : value;
            computed < result.computed && (result = {value: value, computed: computed});
        });
        return result.value;
    };

    // Shuffle an array.
    _.shuffle = function (obj) {
        var rand;
        var index = 0;
        var shuffled = [];
        each(obj, function (value) {
            rand = _.random(index++);
            shuffled[index - 1] = shuffled[rand];
            shuffled[rand] = value;
        });
        return shuffled;
    };

    // An internal function to generate lookup iterators.
    var lookupIterator = function (value) {
        return _.isFunction(value) ? value : function (obj) {
            return obj[value];
        };
    };

    // Sort the object's values by a criterion produced by an iterator.
    _.sortBy = function (obj, value, context) {
        var iterator = lookupIterator(value);
        return _.pluck(_.map(obj,function (value, index, list) {
            return {
                value: value,
                index: index,
                criteria: iterator.call(context, value, index, list)
            };
        }).sort(function (left, right) {
                var a = left.criteria;
                var b = right.criteria;
                if (a !== b) {
                    if (a > b || a === void 0) return 1;
                    if (a < b || b === void 0) return -1;
                }
                return left.index < right.index ? -1 : 1;
            }), 'value');
    };

    // An internal function used for aggregate "group by" operations.
    var group = function (obj, value, context, behavior) {
        var result = {};
        var iterator = lookupIterator(value || _.identity);
        each(obj, function (value, index) {
            var key = iterator.call(context, value, index, obj);
            behavior(result, key, value);
        });
        return result;
    };

    // Groups the object's values by a criterion. Pass either a string attribute
    // to group by, or a function that returns the criterion.
    _.groupBy = function (obj, value, context) {
        return group(obj, value, context, function (result, key, value) {
            (_.has(result, key) ? result[key] : (result[key] = [])).push(value);
        });
    };

    // Counts instances of an object that group by a certain criterion. Pass
    // either a string attribute to count by, or a function that returns the
    // criterion.
    _.countBy = function (obj, value, context) {
        return group(obj, value, context, function (result, key) {
            if (!_.has(result, key)) result[key] = 0;
            result[key]++;
        });
    };

    // Use a comparator function to figure out the smallest index at which
    // an object should be inserted so as to maintain order. Uses binary search.
    _.sortedIndex = function (array, obj, iterator, context) {
        iterator = iterator == null ? _.identity : lookupIterator(iterator);
        var value = iterator.call(context, obj);
        var low = 0, high = array.length;
        while (low < high) {
            var mid = (low + high) >>> 1;
            iterator.call(context, array[mid]) < value ? low = mid + 1 : high = mid;
        }
        return low;
    };

    // Safely convert anything iterable into a real, live array.
    _.toArray = function (obj) {
        if (!obj) return [];
        if (_.isArray(obj)) return slice.call(obj);
        if (obj.length === +obj.length) return _.map(obj, _.identity);
        return _.values(obj);
    };

    // Return the number of elements in an object.
    _.size = function (obj) {
        if (obj == null) return 0;
        return (obj.length === +obj.length) ? obj.length : _.keys(obj).length;
    };

    // Array Functions
    // ---------------

    // Get the first element of an array. Passing **n** will return the first N
    // values in the array. Aliased as `head` and `take`. The **guard** check
    // allows it to work with `_.map`.
    _.first = _.head = _.take = function (array, n, guard) {
        if (array == null) return void 0;
        return (n != null) && !guard ? slice.call(array, 0, n) : array[0];
    };

    // Returns everything but the last entry of the array. Especially useful on
    // the arguments object. Passing **n** will return all the values in
    // the array, excluding the last N. The **guard** check allows it to work with
    // `_.map`.
    _.initial = function (array, n, guard) {
        return slice.call(array, 0, array.length - ((n == null) || guard ? 1 : n));
    };

    // Get the last element of an array. Passing **n** will return the last N
    // values in the array. The **guard** check allows it to work with `_.map`.
    _.last = function (array, n, guard) {
        if (array == null) return void 0;
        if ((n != null) && !guard) {
            return slice.call(array, Math.max(array.length - n, 0));
        } else {
            return array[array.length - 1];
        }
    };

    // Returns everything but the first entry of the array. Aliased as `tail` and `drop`.
    // Especially useful on the arguments object. Passing an **n** will return
    // the rest N values in the array. The **guard**
    // check allows it to work with `_.map`.
    _.rest = _.tail = _.drop = function (array, n, guard) {
        return slice.call(array, (n == null) || guard ? 1 : n);
    };

    // Trim out all falsy values from an array.
    _.compact = function (array) {
        return _.filter(array, _.identity);
    };

    // Internal implementation of a recursive `flatten` function.
    var flatten = function (input, shallow, output) {
        each(input, function (value) {
            if (_.isArray(value)) {
                shallow ? push.apply(output, value) : flatten(value, shallow, output);
            } else {
                output.push(value);
            }
        });
        return output;
    };

    // Return a completely flattened version of an array.
    _.flatten = function (array, shallow) {
        return flatten(array, shallow, []);
    };

    // Return a version of the array that does not contain the specified value(s).
    _.without = function (array) {
        return _.difference(array, slice.call(arguments, 1));
    };

    // Produce a duplicate-free version of the array. If the array has already
    // been sorted, you have the option of using a faster algorithm.
    // Aliased as `unique`.
    _.uniq = _.unique = function (array, isSorted, iterator, context) {
        if (_.isFunction(isSorted)) {
            context = iterator;
            iterator = isSorted;
            isSorted = false;
        }
        var initial = iterator ? _.map(array, iterator, context) : array;
        var results = [];
        var seen = [];
        each(initial, function (value, index) {
            if (isSorted ? (!index || seen[seen.length - 1] !== value) : !_.contains(seen, value)) {
                seen.push(value);
                results.push(array[index]);
            }
        });
        return results;
    };

    // Produce an array that contains the union: each distinct element from all of
    // the passed-in arrays.
    _.union = function () {
        return _.uniq(concat.apply(ArrayProto, arguments));
    };

    // Produce an array that contains every item shared between all the
    // passed-in arrays.
    _.intersection = function (array) {
        var rest = slice.call(arguments, 1);
        return _.filter(_.uniq(array), function (item) {
            return _.every(rest, function (other) {
                return _.indexOf(other, item) >= 0;
            });
        });
    };

    // Take the difference between one array and a number of other arrays.
    // Only the elements present in just the first array will remain.
    _.difference = function (array) {
        var rest = concat.apply(ArrayProto, slice.call(arguments, 1));
        return _.filter(array, function (value) {
            return !_.contains(rest, value);
        });
    };

    // Zip together multiple lists into a single array -- elements that share
    // an index go together.
    _.zip = function () {
        var args = slice.call(arguments);
        var length = _.max(_.pluck(args, 'length'));
        var results = new Array(length);
        for (var i = 0; i < length; i++) {
            results[i] = _.pluck(args, "" + i);
        }
        return results;
    };

    // Converts lists into objects. Pass either a single array of `[key, value]`
    // pairs, or two parallel arrays of the same length -- one of keys, and one of
    // the corresponding values.
    _.object = function (list, values) {
        if (list == null) return {};
        var result = {};
        for (var i = 0, l = list.length; i < l; i++) {
            if (values) {
                result[list[i]] = values[i];
            } else {
                result[list[i][0]] = list[i][1];
            }
        }
        return result;
    };

    // If the browser doesn't supply us with indexOf (I'm looking at you, **MSIE**),
    // we need this function. Return the position of the first occurrence of an
    // item in an array, or -1 if the item is not included in the array.
    // Delegates to **ECMAScript 5**'s native `indexOf` if available.
    // If the array is large and already in sort order, pass `true`
    // for **isSorted** to use binary search.
    _.indexOf = function (array, item, isSorted) {
        if (array == null) return -1;
        var i = 0, l = array.length;
        if (isSorted) {
            if (typeof isSorted == 'number') {
                i = (isSorted < 0 ? Math.max(0, l + isSorted) : isSorted);
            } else {
                i = _.sortedIndex(array, item);
                return array[i] === item ? i : -1;
            }
        }
        if (nativeIndexOf && array.indexOf === nativeIndexOf) return array.indexOf(item, isSorted);
        for (; i < l; i++) if (array[i] === item) return i;
        return -1;
    };

    // Delegates to **ECMAScript 5**'s native `lastIndexOf` if available.
    _.lastIndexOf = function (array, item, from) {
        if (array == null) return -1;
        var hasIndex = from != null;
        if (nativeLastIndexOf && array.lastIndexOf === nativeLastIndexOf) {
            return hasIndex ? array.lastIndexOf(item, from) : array.lastIndexOf(item);
        }
        var i = (hasIndex ? from : array.length);
        while (i--) if (array[i] === item) return i;
        return -1;
    };

    // Generate an integer Array containing an arithmetic progression. A port of
    // the native Python `range()` function. See
    // [the Python documentation](http://docs.python.org/library/functions.html#range).
    _.range = function (start, stop, step) {
        if (arguments.length <= 1) {
            stop = start || 0;
            start = 0;
        }
        step = arguments[2] || 1;

        var len = Math.max(Math.ceil((stop - start) / step), 0);
        var idx = 0;
        var range = new Array(len);

        while (idx < len) {
            range[idx++] = start;
            start += step;
        }

        return range;
    };

    // Function (ahem) Functions
    // ------------------

    // Reusable constructor function for prototype setting.
    var ctor = function () {
    };

    // Create a function bound to a given object (assigning `this`, and arguments,
    // optionally). Binding with arguments is also known as `curry`.
    // Delegates to **ECMAScript 5**'s native `Function.bind` if available.
    // We check for `func.bind` first, to fail fast when `func` is undefined.
    _.bind = function (func, context) {
        var args, bound;
        if (func.bind === nativeBind && nativeBind) return nativeBind.apply(func, slice.call(arguments, 1));
        if (!_.isFunction(func)) throw new TypeError;
        args = slice.call(arguments, 2);
        return bound = function () {
            if (!(this instanceof bound)) return func.apply(context, args.concat(slice.call(arguments)));
            ctor.prototype = func.prototype;
            var self = new ctor;
            ctor.prototype = null;
            var result = func.apply(self, args.concat(slice.call(arguments)));
            if (Object(result) === result) return result;
            return self;
        };
    };

    // Bind all of an object's methods to that object. Useful for ensuring that
    // all callbacks defined on an object belong to it.
    _.bindAll = function (obj) {
        var funcs = slice.call(arguments, 1);
        if (funcs.length == 0) funcs = _.functions(obj);
        each(funcs, function (f) {
            obj[f] = _.bind(obj[f], obj);
        });
        return obj;
    };

    // Memoize an expensive function by storing its results.
    _.memoize = function (func, hasher) {
        var memo = {};
        hasher || (hasher = _.identity);
        return function () {
            var key = hasher.apply(this, arguments);
            return _.has(memo, key) ? memo[key] : (memo[key] = func.apply(this, arguments));
        };
    };

    // Delays a function for the given number of milliseconds, and then calls
    // it with the arguments supplied.
    _.delay = function (func, wait) {
        var args = slice.call(arguments, 2);
        return setTimeout(function () {
            return func.apply(null, args);
        }, wait);
    };

    // Defers a function, scheduling it to run after the current call stack has
    // cleared.
    _.defer = function (func) {
        return _.delay.apply(_, [func, 1].concat(slice.call(arguments, 1)));
    };

    // Returns a function, that, when invoked, will only be triggered at most once
    // during a given window of time.
    _.throttle = function (func, wait) {
        var context, args, timeout, result;
        var previous = 0;
        var later = function () {
            previous = new Date;
            timeout = null;
            result = func.apply(context, args);
        };
        return function () {
            var now = new Date;
            var remaining = wait - (now - previous);
            context = this;
            args = arguments;
            if (remaining <= 0) {
                clearTimeout(timeout);
                timeout = null;
                previous = now;
                result = func.apply(context, args);
            } else if (!timeout) {
                timeout = setTimeout(later, remaining);
            }
            return result;
        };
    };

    // Returns a function, that, as long as it continues to be invoked, will not
    // be triggered. The function will be called after it stops being called for
    // N milliseconds. If `immediate` is passed, trigger the function on the
    // leading edge, instead of the trailing.
    _.debounce = function (func, wait, immediate) {
        var timeout, result;
        return function () {
            var context = this, args = arguments;
            var later = function () {
                timeout = null;
                if (!immediate) result = func.apply(context, args);
            };
            var callNow = immediate && !timeout;
            clearTimeout(timeout);
            timeout = setTimeout(later, wait);
            if (callNow) result = func.apply(context, args);
            return result;
        };
    };

    // Returns a function that will be executed at most one time, no matter how
    // often you call it. Useful for lazy initialization.
    _.once = function (func) {
        var ran = false, memo;
        return function () {
            if (ran) return memo;
            ran = true;
            memo = func.apply(this, arguments);
            func = null;
            return memo;
        };
    };

    // Returns the first function passed as an argument to the second,
    // allowing you to adjust arguments, run code before and after, and
    // conditionally execute the original function.
    _.wrap = function (func, wrapper) {
        return function () {
            var args = [func];
            push.apply(args, arguments);
            return wrapper.apply(this, args);
        };
    };

    // Returns a function that is the composition of a list of functions, each
    // consuming the return value of the function that follows.
    _.compose = function () {
        var funcs = arguments;
        return function () {
            var args = arguments;
            for (var i = funcs.length - 1; i >= 0; i--) {
                args = [funcs[i].apply(this, args)];
            }
            return args[0];
        };
    };

    // Returns a function that will only be executed after being called N times.
    _.after = function (times, func) {
        if (times <= 0) return func();
        return function () {
            if (--times < 1) {
                return func.apply(this, arguments);
            }
        };
    };

    // Object Functions
    // ----------------

    // Retrieve the names of an object's properties.
    // Delegates to **ECMAScript 5**'s native `Object.keys`
    _.keys = nativeKeys || function (obj) {
        if (obj !== Object(obj)) throw new TypeError('Invalid object');
        var keys = [];
        for (var key in obj) if (_.has(obj, key)) keys[keys.length] = key;
        return keys;
    };

    // Retrieve the values of an object's properties.
    _.values = function (obj) {
        var values = [];
        for (var key in obj) if (_.has(obj, key)) values.push(obj[key]);
        return values;
    };

    // Convert an object into a list of `[key, value]` pairs.
    _.pairs = function (obj) {
        var pairs = [];
        for (var key in obj) if (_.has(obj, key)) pairs.push([key, obj[key]]);
        return pairs;
    };

    // Invert the keys and values of an object. The values must be serializable.
    _.invert = function (obj) {
        var result = {};
        for (var key in obj) if (_.has(obj, key)) result[obj[key]] = key;
        return result;
    };

    // Return a sorted list of the function names available on the object.
    // Aliased as `methods`
    _.functions = _.methods = function (obj) {
        var names = [];
        for (var key in obj) {
            if (_.isFunction(obj[key])) names.push(key);
        }
        return names.sort();
    };

    // Extend a given object with all the properties in passed-in object(s).
    _.extend = function (obj) {
        each(slice.call(arguments, 1), function (source) {
            if (source) {
                for (var prop in source) {
                    obj[prop] = source[prop];
                }
            }
        });
        return obj;
    };

    // Return a copy of the object only containing the whitelisted properties.
    _.pick = function (obj) {
        var copy = {};
        var keys = concat.apply(ArrayProto, slice.call(arguments, 1));
        each(keys, function (key) {
            if (key in obj) copy[key] = obj[key];
        });
        return copy;
    };

    // Return a copy of the object without the blacklisted properties.
    _.omit = function (obj) {
        var copy = {};
        var keys = concat.apply(ArrayProto, slice.call(arguments, 1));
        for (var key in obj) {
            if (!_.contains(keys, key)) copy[key] = obj[key];
        }
        return copy;
    };

    // Fill in a given object with default properties.
    _.defaults = function (obj) {
        each(slice.call(arguments, 1), function (source) {
            if (source) {
                for (var prop in source) {
                    if (obj[prop] == null) obj[prop] = source[prop];
                }
            }
        });
        return obj;
    };

    // Create a (shallow-cloned) duplicate of an object.
    _.clone = function (obj) {
        if (!_.isObject(obj)) return obj;
        return _.isArray(obj) ? obj.slice() : _.extend({}, obj);
    };

    // Invokes interceptor with the obj, and then returns obj.
    // The primary purpose of this method is to "tap into" a method chain, in
    // order to perform operations on intermediate results within the chain.
    _.tap = function (obj, interceptor) {
        interceptor(obj);
        return obj;
    };

    // Internal recursive comparison function for `isEqual`.
    var eq = function (a, b, aStack, bStack) {
        // Identical objects are equal. `0 === -0`, but they aren't identical.
        // See the Harmony `egal` proposal: http://wiki.ecmascript.org/doku.php?id=harmony:egal.
        if (a === b) return a !== 0 || 1 / a == 1 / b;
        // A strict comparison is necessary because `null == undefined`.
        if (a == null || b == null) return a === b;
        // Unwrap any wrapped objects.
        if (a instanceof _) a = a._wrapped;
        if (b instanceof _) b = b._wrapped;
        // Compare `[[Class]]` names.
        var className = toString.call(a);
        if (className != toString.call(b)) return false;
        switch (className) {
            // Strings, numbers, dates, and booleans are compared by value.
            case '[object String]':
                // Primitives and their corresponding object wrappers are equivalent; thus, `"5"` is
                // equivalent to `new String("5")`.
                return a == String(b);
            case '[object Number]':
                // `NaN`s are equivalent, but non-reflexive. An `egal` comparison is performed for
                // other numeric values.
                return a != +a ? b != +b : (a == 0 ? 1 / a == 1 / b : a == +b);
            case '[object Date]':
            case '[object Boolean]':
                // Coerce dates and booleans to numeric primitive values. Dates are compared by their
                // millisecond representations. Note that invalid dates with millisecond representations
                // of `NaN` are not equivalent.
                return +a == +b;
            // RegExps are compared by their source patterns and flags.
            case '[object RegExp]':
                return a.source == b.source &&
                    a.global == b.global &&
                    a.multiline == b.multiline &&
                    a.ignoreCase == b.ignoreCase;
        }
        if (typeof a != 'object' || typeof b != 'object') return false;
        // Assume equality for cyclic structures. The algorithm for detecting cyclic
        // structures is adapted from ES 5.1 section 15.12.3, abstract operation `JO`.
        var length = aStack.length;
        while (length--) {
            // Linear search. Performance is inversely proportional to the number of
            // unique nested structures.
            if (aStack[length] == a) return bStack[length] == b;
        }
        // Add the first object to the stack of traversed objects.
        aStack.push(a);
        bStack.push(b);
        var size = 0, result = true;
        // Recursively compare objects and arrays.
        if (className == '[object Array]') {
            // Compare array lengths to determine if a deep comparison is necessary.
            size = a.length;
            result = size == b.length;
            if (result) {
                // Deep compare the contents, ignoring non-numeric properties.
                while (size--) {
                    if (!(result = eq(a[size], b[size], aStack, bStack))) break;
                }
            }
        } else {
            // Objects with different constructors are not equivalent, but `Object`s
            // from different frames are.
            var aCtor = a.constructor, bCtor = b.constructor;
            if (aCtor !== bCtor && !(_.isFunction(aCtor) && (aCtor instanceof aCtor) &&
                _.isFunction(bCtor) && (bCtor instanceof bCtor))) {
                return false;
            }
            // Deep compare objects.
            for (var key in a) {
                if (_.has(a, key)) {
                    // Count the expected number of properties.
                    size++;
                    // Deep compare each member.
                    if (!(result = _.has(b, key) && eq(a[key], b[key], aStack, bStack))) break;
                }
            }
            // Ensure that both objects contain the same number of properties.
            if (result) {
                for (key in b) {
                    if (_.has(b, key) && !(size--)) break;
                }
                result = !size;
            }
        }
        // Remove the first object from the stack of traversed objects.
        aStack.pop();
        bStack.pop();
        return result;
    };

    // Perform a deep comparison to check if two objects are equal.
    _.isEqual = function (a, b) {
        return eq(a, b, [], []);
    };

    // Is a given array, string, or object empty?
    // An "empty" object has no enumerable own-properties.
    _.isEmpty = function (obj) {
        if (obj == null) return true;
        if (_.isArray(obj) || _.isString(obj)) return obj.length === 0;
        for (var key in obj) if (_.has(obj, key)) return false;
        return true;
    };

    // Is a given value a DOM element?
    _.isElement = function (obj) {
        return !!(obj && obj.nodeType === 1);
    };

    // Is a given value an array?
    // Delegates to ECMA5's native Array.isArray
    _.isArray = nativeIsArray || function (obj) {
        return toString.call(obj) == '[object Array]';
    };

    // Is a given variable an object?
    _.isObject = function (obj) {
        return obj === Object(obj);
    };

    // Add some isType methods: isArguments, isFunction, isString, isNumber, isDate, isRegExp.
    each(['Arguments', 'Function', 'String', 'Number', 'Date', 'RegExp'], function (name) {
        _['is' + name] = function (obj) {
            return toString.call(obj) == '[object ' + name + ']';
        };
    });

    // Define a fallback version of the method in browsers (ahem, IE), where
    // there isn't any inspectable "Arguments" type.
    if (!_.isArguments(arguments)) {
        _.isArguments = function (obj) {
            return !!(obj && _.has(obj, 'callee'));
        };
    }

    // Optimize `isFunction` if appropriate.
    if (typeof (/./) !== 'function') {
        _.isFunction = function (obj) {
            return typeof obj === 'function';
        };
    }

    // Is a given object a finite number?
    _.isFinite = function (obj) {
        return isFinite(obj) && !isNaN(parseFloat(obj));
    };

    // Is the given value `NaN`? (NaN is the only number which does not equal itself).
    _.isNaN = function (obj) {
        return _.isNumber(obj) && obj != +obj;
    };

    // Is a given value a boolean?
    _.isBoolean = function (obj) {
        return obj === true || obj === false || toString.call(obj) == '[object Boolean]';
    };

    // Is a given value equal to null?
    _.isNull = function (obj) {
        return obj === null;
    };

    // Is a given variable undefined?
    _.isUndefined = function (obj) {
        return obj === void 0;
    };

    // Shortcut function for checking if an object has a given property directly
    // on itself (in other words, not on a prototype).
    _.has = function (obj, key) {
        return hasOwnProperty.call(obj, key);
    };

    // Utility Functions
    // -----------------

    // Run Underscore.js in *noConflict* mode, returning the `_` variable to its
    // previous owner. Returns a reference to the Underscore object.
    _.noConflict = function () {
        root._ = previousUnderscore;
        return this;
    };

    // Keep the identity function around for default iterators.
    _.identity = function (value) {
        return value;
    };

    // Run a function **n** times.
    _.times = function (n, iterator, context) {
        var accum = Array(n);
        for (var i = 0; i < n; i++) accum[i] = iterator.call(context, i);
        return accum;
    };

    // Return a random integer between min and max (inclusive).
    _.random = function (min, max) {
        if (max == null) {
            max = min;
            min = 0;
        }
        return min + (0 | Math.random() * (max - min + 1));
    };

    // List of HTML entities for escaping.
    var entityMap = {
        escape: {
            '&': '&amp;',
            '<': '&lt;',
            '>': '&gt;',
            '"': '&quot;',
            "'": '&#x27;',
            '/': '&#x2F;'
        }
    };
    entityMap.unescape = _.invert(entityMap.escape);

    // Regexes containing the keys and values listed immediately above.
    var entityRegexes = {
        escape: new RegExp('[' + _.keys(entityMap.escape).join('') + ']', 'g'),
        unescape: new RegExp('(' + _.keys(entityMap.unescape).join('|') + ')', 'g')
    };

    // Functions for escaping and unescaping strings to/from HTML interpolation.
    _.each(['escape', 'unescape'], function (method) {
        _[method] = function (string) {
            if (string == null) return '';
            return ('' + string).replace(entityRegexes[method], function (match) {
                return entityMap[method][match];
            });
        };
    });

    // If the value of the named property is a function then invoke it;
    // otherwise, return it.
    _.result = function (object, property) {
        if (object == null) return null;
        var value = object[property];
        return _.isFunction(value) ? value.call(object) : value;
    };

    // Add your own custom functions to the Underscore object.
    _.mixin = function (obj) {
        each(_.functions(obj), function (name) {
            var func = _[name] = obj[name];
            _.prototype[name] = function () {
                var args = [this._wrapped];
                push.apply(args, arguments);
                return result.call(this, func.apply(_, args));
            };
        });
    };

    // Generate a unique integer id (unique within the entire client session).
    // Useful for temporary DOM ids.
    var idCounter = 0;
    _.uniqueId = function (prefix) {
        var id = '' + ++idCounter;
        return prefix ? prefix + id : id;
    };

    // By default, Underscore uses ERB-style template delimiters, change the
    // following template settings to use alternative delimiters.
    _.templateSettings = {
        evaluate: /<%([\s\S]+?)%>/g,
        interpolate: /<%=([\s\S]+?)%>/g,
        escape: /<%-([\s\S]+?)%>/g
    };

    // When customizing `templateSettings`, if you don't want to define an
    // interpolation, evaluation or escaping regex, we need one that is
    // guaranteed not to match.
    var noMatch = /(.)^/;

    // Certain characters need to be escaped so that they can be put into a
    // string literal.
    var escapes = {
        "'": "'",
        '\\': '\\',
        '\r': 'r',
        '\n': 'n',
        '\t': 't',
        '\u2028': 'u2028',
        '\u2029': 'u2029'
    };

    var escaper = /\\|'|\r|\n|\t|\u2028|\u2029/g;

    // JavaScript micro-templating, similar to John Resig's implementation.
    // Underscore templating handles arbitrary delimiters, preserves whitespace,
    // and correctly escapes quotes within interpolated code.
    _.template = function (text, data, settings) {
        settings = _.defaults({}, settings, _.templateSettings);

        // Combine delimiters into one regular expression via alternation.
        var matcher = new RegExp([
            (settings.escape || noMatch).source,
            (settings.interpolate || noMatch).source,
            (settings.evaluate || noMatch).source
        ].join('|') + '|$', 'g');

        // Compile the template source, escaping string literals appropriately.
        var index = 0;
        var source = "__p+='";
        text.replace(matcher, function (match, escape, interpolate, evaluate, offset) {
            source += text.slice(index, offset)
                .replace(escaper, function (match) {
                    return '\\' + escapes[match];
                });

            if (escape) {
                source += "'+\n((__t=(" + escape + "))==null?'':_.escape(__t))+\n'";
            }
            if (interpolate) {
                source += "'+\n((__t=(" + interpolate + "))==null?'':__t)+\n'";
            }
            if (evaluate) {
                source += "';\n" + evaluate + "\n__p+='";
            }
            index = offset + match.length;
            return match;
        });
        source += "';\n";

        // If a variable is not specified, place data values in local scope.
        if (!settings.variable) source = 'with(obj||{}){\n' + source + '}\n';

        source = "var __t,__p='',__j=Array.prototype.join," +
            "print=function(){__p+=__j.call(arguments,'');};\n" +
            source + "return __p;\n";

        try {
            var render = new Function(settings.variable || 'obj', '_', source);
        } catch (e) {
            e.source = source;
            throw e;
        }

        if (data) return render(data, _);
        var template = function (data) {
            return render.call(this, data, _);
        };

        // Provide the compiled function source as a convenience for precompilation.
        template.source = 'function(' + (settings.variable || 'obj') + '){\n' + source + '}';

        return template;
    };

    // Add a "chain" function, which will delegate to the wrapper.
    _.chain = function (obj) {
        return _(obj).chain();
    };

    // OOP
    // ---------------
    // If Underscore is called as a function, it returns a wrapped object that
    // can be used OO-style. This wrapper holds altered versions of all the
    // underscore functions. Wrapped objects may be chained.

    // Helper function to continue chaining intermediate results.
    var result = function (obj) {
        return this._chain ? _(obj).chain() : obj;
    };

    // Add all of the Underscore functions to the wrapper object.
    _.mixin(_);

    // Add all mutator Array functions to the wrapper.
    each(['pop', 'push', 'reverse', 'shift', 'sort', 'splice', 'unshift'], function (name) {
        var method = ArrayProto[name];
        _.prototype[name] = function () {
            var obj = this._wrapped;
            method.apply(obj, arguments);
            if ((name == 'shift' || name == 'splice') && obj.length === 0) delete obj[0];
            return result.call(this, obj);
        };
    });

    // Add all accessor Array functions to the wrapper.
    each(['concat', 'join', 'slice'], function (name) {
        var method = ArrayProto[name];
        _.prototype[name] = function () {
            return result.call(this, method.apply(this._wrapped, arguments));
        };
    });

    _.extend(_.prototype, {

        // Start chaining a wrapped Underscore object.
        chain: function () {
            this._chain = true;
            return this;
        },

        // Extracts the result from a wrapped and chained object.
        value: function () {
            return this._wrapped;
        }

    });

}).call(this);

define("underscore", (function (global) {
    return function () {
        var ret, fn;
        return ret || global._;
    };
}(this)));

/*!
 * jQuery Cookie Plugin v1.4.1
 * https://github.com/carhartl/jquery-cookie
 *
 * Copyright 2006, 2014 Klaus Hartl
 * Released under the MIT license
 */
(function (factory) {
	if (typeof define === 'function' && define.amd) {
		// AMD (Register as an anonymous module)
		define('cookie',['jquery'], factory);
	} else if (typeof exports === 'object') {
		// Node/CommonJS
		module.exports = factory(require('jquery'));
	} else {
		// Browser globals
		factory(jQuery);
	}
}(function ($) {

	var pluses = /\+/g;

	function encode(s) {
		return config.raw ? s : encodeURIComponent(s);
	}

	function decode(s) {
		return config.raw ? s : decodeURIComponent(s);
	}

	function stringifyCookieValue(value) {
		return encode(config.json ? JSON.stringify(value) : String(value));
	}

	function parseCookieValue(s) {
		if (s.indexOf('"') === 0) {
			// This is a quoted cookie as according to RFC2068, unescape...
			s = s.slice(1, -1).replace(/\\"/g, '"').replace(/\\\\/g, '\\');
		}

		try {
			// Replace server-side written pluses with spaces.
			// If we can't decode the cookie, ignore it, it's unusable.
			// If we can't parse the cookie, ignore it, it's unusable.
			s = decodeURIComponent(s.replace(pluses, ' '));
			return config.json ? JSON.parse(s) : s;
		} catch(e) {}
	}

	function read(s, converter) {
		var value = config.raw ? s : parseCookieValue(s);
		return $.isFunction(converter) ? converter(value) : value;
	}

	var config = $.cookie = function (key, value, options) {

		// Write

		if (arguments.length > 1 && !$.isFunction(value)) {
			options = $.extend({}, config.defaults, options);

			if (typeof options.expires === 'number') {
				var days = options.expires, t = options.expires = new Date();
				t.setMilliseconds(t.getMilliseconds() + days * 864e+5);
			}

			return (document.cookie = [
				encode(key), '=', stringifyCookieValue(value),
				options.expires ? '; expires=' + options.expires.toUTCString() : '', // use expires attribute, max-age is not supported by IE
				options.path    ? '; path=' + options.path : '',
				options.domain  ? '; domain=' + options.domain : '',
				options.secure  ? '; secure' : ''
			].join(''));
		}

		// Read

		var result = key ? undefined : {},
			// To prevent the for loop in the first place assign an empty array
			// in case there are no cookies at all. Also prevents odd result when
			// calling $.cookie().
			cookies = document.cookie ? document.cookie.split('; ') : [],
			i = 0,
			l = cookies.length;

		for (; i < l; i++) {
			var parts = cookies[i].split('='),
				name = decode(parts.shift()),
				cookie = parts.join('=');

			if (key === name) {
				// If second argument (value) is a function it's a converter...
				result = read(cookie, value);
				break;
			}

			// Prevent storing a cookie that we couldn't decode.
			if (!key && (cookie = read(cookie)) !== undefined) {
				result[name] = cookie;
			}
		}

		return result;
	};

	config.defaults = {};

	$.removeCookie = function (key, options) {
		// Must not alter options, thus extending a fresh object...
		$.cookie(key, '', $.extend({}, options, { expires: -1 }));
		return !$.cookie(key);
	};

}));

/*
 * Swipe 2.0
 *
 * Brad Birdsall
 * Copyright 2013, MIT License
 *
*/

function Swipe(container, options) {

  "use strict";

  // utilities
  var noop = function() {}; // simple no operation function
  var offloadFn = function(fn) { setTimeout(fn || noop, 0) }; // offload a functions execution

  // check browser capabilities
  var browser = {
    addEventListener: !!window.addEventListener,
    touch: ('ontouchstart' in window) || window.DocumentTouch && document instanceof DocumentTouch,
    transitions: (function(temp) {
      var props = ['transitionProperty', 'WebkitTransition', 'MozTransition', 'OTransition', 'msTransition'];
      for ( var i in props ) if (temp.style[ props[i] ] !== undefined) return true;
      return false;
    })(document.createElement('swipe'))
  };

  // quit if no root element
  if (!container) return;
  var element = container.children[0];
  var slides, slidePos, width, length;
  options = options || {};
  var index = parseInt(options.startSlide, 10) || 0;
  var speed = options.speed || 300;
  options.continuous = options.continuous !== undefined ? options.continuous : true;

  function setup() {

    // cache slides
    slides = element.children;
    length = slides.length;

    // set continuous to false if only one slide
    if (slides.length < 2) options.continuous = false;

    //special case if two slides
    if (browser.transitions && options.continuous && slides.length < 3) {
      element.appendChild(slides[0].cloneNode(true));
      element.appendChild(element.children[1].cloneNode(true));
      slides = element.children;
    }

    // create an array to store current positions of each slide
    slidePos = new Array(slides.length);

    // determine width of each slide
    width = container.getBoundingClientRect().width || container.offsetWidth;

    element.style.width = (slides.length * width) + 'px';

    // stack elements
    var pos = slides.length;
    while(pos--) {

      var slide = slides[pos];

      slide.style.width = width + 'px';
      slide.setAttribute('data-index', pos);

      if (browser.transitions) {
        slide.style.left = (pos * -width) + 'px';
        move(pos, index > pos ? -width : (index < pos ? width : 0), 0);
      }

    }

    // reposition elements before and after index
    if (options.continuous && browser.transitions) {
      move(circle(index-1), -width, 0);
      move(circle(index+1), width, 0);
    }

    if (!browser.transitions) element.style.left = (index * -width) + 'px';

    container.style.visibility = 'visible';

  }

  function prev() {

    if (options.continuous) slide(index-1);
    else if (index) slide(index-1);

  }

  function next() {

    if (options.continuous) slide(index+1);
    else if (index < slides.length - 1) slide(index+1);

  }

  function circle(index) {

    // a simple positive modulo using slides.length
    return (slides.length + (index % slides.length)) % slides.length;

  }

  function slide(to, slideSpeed) {

    // do nothing if already on requested slide
    if (index == to) return;

    if (browser.transitions) {

      var direction = Math.abs(index-to) / (index-to); // 1: backward, -1: forward

      // get the actual position of the slide
      if (options.continuous) {
        var natural_direction = direction;
        direction = -slidePos[circle(to)] / width;

        // if going forward but to < index, use to = slides.length + to
        // if going backward but to > index, use to = -slides.length + to
        if (direction !== natural_direction) to =  -direction * slides.length + to;

      }

      var diff = Math.abs(index-to) - 1;

      // move all the slides between index and to in the right direction
      while (diff--) move( circle((to > index ? to : index) - diff - 1), width * direction, 0);

      to = circle(to);

      move(index, width * direction, slideSpeed || speed);
      move(to, 0, slideSpeed || speed);

      if (options.continuous) move(circle(to - direction), -(width * direction), 0); // we need to get the next in place

    } else {

      to = circle(to);
      animate(index * -width, to * -width, slideSpeed || speed);
      //no fallback for a circular continuous if the browser does not accept transitions
    }

    index = to;
    offloadFn(options.callback && options.callback(index, slides[index]));
  }

  function move(index, dist, speed) {

    translate(index, dist, speed);
    slidePos[index] = dist;

  }

  function translate(index, dist, speed) {

    var slide = slides[index];
    var style = slide && slide.style;

    if (!style) return;

    style.webkitTransitionDuration =
    style.MozTransitionDuration =
    style.msTransitionDuration =
    style.OTransitionDuration =
    style.transitionDuration = speed + 'ms';

    style.webkitTransform = 'translate(' + dist + 'px,0)' + 'translateZ(0)';
    style.msTransform =
    style.MozTransform =
    style.OTransform = 'translateX(' + dist + 'px)';

  }

  function animate(from, to, speed) {

    // if not an animation, just reposition
    if (!speed) {

      element.style.left = to + 'px';
      return;

    }

    var start = +new Date;

    var timer = setInterval(function() {

      var timeElap = +new Date - start;

      if (timeElap > speed) {

        element.style.left = to + 'px';

        if (delay) begin();

        options.transitionEnd && options.transitionEnd.call(event, index, slides[index]);

        clearInterval(timer);
        return;

      }

      element.style.left = (( (to - from) * (Math.floor((timeElap / speed) * 100) / 100) ) + from) + 'px';

    }, 4);

  }

  // setup auto slideshow
  var delay = options.auto || 0;
  var interval;

  function begin() {

    interval = setTimeout(next, delay);

  }

  function stop() {

    delay = 0;
    clearTimeout(interval);

  }


  // setup initial vars
  var start = {};
  var delta = {};
  var isScrolling;

  // setup event capturing
  var events = {

    handleEvent: function(event) {

      switch (event.type) {
        case 'touchstart': this.start(event); break;
        case 'touchmove': this.move(event); break;
        case 'touchend': offloadFn(this.end(event)); break;
        case 'webkitTransitionEnd':
        case 'msTransitionEnd':
        case 'oTransitionEnd':
        case 'otransitionend':
        case 'transitionend': offloadFn(this.transitionEnd(event)); break;
        case 'resize': offloadFn(setup); break;
      }

      if (options.stopPropagation) event.stopPropagation();

    },
    start: function(event) {

      var touches = event.touches[0];

      // measure start values
      start = {

        // get initial touch coords
        x: touches.pageX,
        y: touches.pageY,

        // store time to determine touch duration
        time: +new Date

      };

      // used for testing first move event
      isScrolling = undefined;

      // reset delta and end measurements
      delta = {};

      // attach touchmove and touchend listeners
      element.addEventListener('touchmove', this, false);
      element.addEventListener('touchend', this, false);

    },
    move: function(event) {

      // ensure swiping with one touch and not pinching
      if ( event.touches.length > 1 || event.scale && event.scale !== 1) return

      if (options.disableScroll) event.preventDefault();

      var touches = event.touches[0];

      // measure change in x and y
      delta = {
        x: touches.pageX - start.x,
        y: touches.pageY - start.y
      }

      // determine if scrolling test has run - one time test
      if ( typeof isScrolling == 'undefined') {
        isScrolling = !!( isScrolling || Math.abs(delta.x) < Math.abs(delta.y) );
      }

      // if user is not trying to scroll vertically
      if (!isScrolling) {

        // prevent native scrolling
        event.preventDefault();

        // stop slideshow
        stop();

        // increase resistance if first or last slide
        if (options.continuous) { // we don't add resistance at the end

          translate(circle(index-1), delta.x + slidePos[circle(index-1)], 0);
          translate(index, delta.x + slidePos[index], 0);
          translate(circle(index+1), delta.x + slidePos[circle(index+1)], 0);

        } else {

          delta.x =
            delta.x /
              ( (!index && delta.x > 0               // if first slide and sliding left
                || index == slides.length - 1        // or if last slide and sliding right
                && delta.x < 0                       // and if sliding at all
              ) ?
              ( Math.abs(delta.x) / width + 1 )      // determine resistance level
              : 1 );                                 // no resistance if false

          // translate 1:1
          translate(index-1, delta.x + slidePos[index-1], 0);
          translate(index, delta.x + slidePos[index], 0);
          translate(index+1, delta.x + slidePos[index+1], 0);
        }

      }

    },
    end: function(event) {

      // measure duration
      var duration = +new Date - start.time;

      // determine if slide attempt triggers next/prev slide
      var isValidSlide =
            Number(duration) < 250               // if slide duration is less than 250ms
            && Math.abs(delta.x) > 20            // and if slide amt is greater than 20px
            || Math.abs(delta.x) > width/2;      // or if slide amt is greater than half the width

      // determine if slide attempt is past start and end
      var isPastBounds =
            !index && delta.x > 0                            // if first slide and slide amt is greater than 0
            || index == slides.length - 1 && delta.x < 0;    // or if last slide and slide amt is less than 0

      if (options.continuous) isPastBounds = false;

      // determine direction of swipe (true:right, false:left)
      var direction = delta.x < 0;

      // if not scrolling vertically
      if (!isScrolling) {

        if (isValidSlide && !isPastBounds) {

          if (direction) {

            if (options.continuous) { // we need to get the next in this direction in place

              move(circle(index-1), -width, 0);
              move(circle(index+2), width, 0);

            } else {
              move(index-1, -width, 0);
            }

            move(index, slidePos[index]-width, speed);
            move(circle(index+1), slidePos[circle(index+1)]-width, speed);
            index = circle(index+1);

          } else {
            if (options.continuous) { // we need to get the next in this direction in place

              move(circle(index+1), width, 0);
              move(circle(index-2), -width, 0);

            } else {
              move(index+1, width, 0);
            }

            move(index, slidePos[index]+width, speed);
            move(circle(index-1), slidePos[circle(index-1)]+width, speed);
            index = circle(index-1);

          }

          options.callback && options.callback(index, slides[index]);

        } else {

          if (options.continuous) {

            move(circle(index-1), -width, speed);
            move(index, 0, speed);
            move(circle(index+1), width, speed);

          } else {

            move(index-1, -width, speed);
            move(index, 0, speed);
            move(index+1, width, speed);
          }

        }

      }

      // kill touchmove and touchend event listeners until touchstart called again
      element.removeEventListener('touchmove', events, false)
      element.removeEventListener('touchend', events, false)

    },
    transitionEnd: function(event) {

      if (parseInt(event.target.getAttribute('data-index'), 10) == index) {

        if (delay) begin();

        options.transitionEnd && options.transitionEnd.call(event, index, slides[index]);

      }

    }

  }

  // trigger setup
  setup();

  // start auto slideshow if applicable
  if (delay) begin();


  // add event listeners
  if (browser.addEventListener) {

    // set touchstart event on element
    if (browser.touch) element.addEventListener('touchstart', events, false);

    if (browser.transitions) {
      element.addEventListener('webkitTransitionEnd', events, false);
      element.addEventListener('msTransitionEnd', events, false);
      element.addEventListener('oTransitionEnd', events, false);
      element.addEventListener('otransitionend', events, false);
      element.addEventListener('transitionend', events, false);
    }

    // set resize event on window
    window.addEventListener('resize', events, false);

  } else {

    window.onresize = function () { setup() }; // to play nice with old IE

  }

  // expose the Swipe API
  return {
    setup: function() {

      setup();

    },
    slide: function(to, speed) {

      // cancel slideshow
      stop();

      slide(to, speed);

    },
    prev: function() {

      // cancel slideshow
      stop();

      prev();

    },
    next: function() {

      // cancel slideshow
      stop();

      next();

    },
    stop: function() {

      // cancel slideshow
      stop();

    },
    getPos: function() {

      // return current index position
      return index;

    },
    getNumSlides: function() {

      // return total number of slides
      return length;
    },
    kill: function() {

      // cancel slideshow
      stop();

      // reset element
      element.style.width = '';
      element.style.left = '';

      // reset slides
      var pos = slides.length;
      while(pos--) {

        var slide = slides[pos];
        slide.style.width = '';
        slide.style.left = '';

        if (browser.transitions) translate(pos, 0, 0);

      }

      // removed event listeners
      if (browser.addEventListener) {

        // remove current event listeners
        element.removeEventListener('touchstart', events, false);
        element.removeEventListener('webkitTransitionEnd', events, false);
        element.removeEventListener('msTransitionEnd', events, false);
        element.removeEventListener('oTransitionEnd', events, false);
        element.removeEventListener('otransitionend', events, false);
        element.removeEventListener('transitionend', events, false);
        window.removeEventListener('resize', events, false);

      }
      else {

        window.onresize = null;

      }

    }
  }

}


if ( window.jQuery || window.Zepto ) {
  (function($) {
    $.fn.Swipe = function(params) {
      return this.each(function() {
        $(this).data('Swipe', new Swipe($(this)[0], params));
      });
    }
  })( window.jQuery || window.Zepto )
}
;
define("swipe", (function (global) {
    return function () {
        var ret, fn;
        return ret || global.swipe;
    };
}(this)));

/**
 * JSON   
 */
$.JSON = {};
var cx = /[\u0000\u00ad\u0600-\u0604\u070f\u17b4\u17b5\u200c-\u200f\u2028-\u202f\u2060-\u206f\ufeff\ufff0-\uffff]/g;

if (typeof(JSON)=='object' && typeof JSON.stringify === "function") {
    $.JSON.stringify = JSON.stringify;
} else {
     $.JSON.stringify = function(value, replacer, space) {
        var i; gap = ""; indent = "";
        if (typeof space === "number") {
            for (i = 0; i < space; i += 1) {
                indent += " ";
            }
        } else {
            if (typeof space === "string") {
                indent = space;
            }
        }
        rep = replacer;
        if (replacer && typeof replacer !== "function" && (typeof replacer !== "object" || typeof replacer.length !== "number")) {
            throw new Error("JSON.stringify");
        }
        return str("", {"": value });
    };
}

if (typeof(JSON)=='object' && typeof JSON.parse === "function") {
    $.JSON.parse = JSON.parse;
} else {
    $.JSON.parse = function(text, reviver) {
        var j;
        function walk(holder, key) {
            var k, v, value = holder[key];
            if (value && typeof value === "object") {
                for (k in value) {
                    if (Object.prototype.hasOwnProperty.call(value, k)) {
                            v = walk(value, k);
                            if (v !== undefined) {value[k] = v; }
                        else {delete value[k]; }
                    }
                }
            }
            return reviver.call(holder, key, value);
        }
        text = String(text);
        cx.lastIndex = 0;
        if (cx.test(text)) {
            text = text.replace(cx, function(a) {
            return "\\u" + ("0000" + a.charCodeAt(0).toString(16)).slice(-4); });
        }
        if (/^[\],:{}\s]*$/.test(text.replace(/\\(?:["\\\/bfnrt]|u[0-9a-fA-F]{4})/g, "@").replace(/"[^"\\\n\r]*"|true|false|null|-?\d+(?:\.\d*)?(?:[eE][+\-]?\d+)?/g, "]").replace(/(?:^|:|,)(?:\s*\[)+/g, ""))) {
            j = eval("(" + text + ")");
            return typeof reviver === "function" ? walk({"": j }, "") : j;
        }
        throw new SyntaxError("JSON.parse");
    };
}

/**
 * Common script to handle the theme demo
 */
var Common = function() {

    var ajaxSetting = function(){
        // ajax loading before success
        //重写jquery的ajax方法
        var last_ajax = [];
        $.cur_log_depth = 0;    //refresh each page
        $.max_log_depth = 3;    //max depth a page
        $.post_error = function(data){
            //jslog('/common/ajaxlog/log?data=' + encodeURIComponent(data.join(',')));
            //$.post('/common/ajaxlog/log?of=json', {data: data}, function(data){});
        }

        $._post = $.post;
        $.post = function () {
            //验证input格式
            $._post.apply(this, arguments);
        }
        $.fn.post = function () {
            //验证input格式
            $._post.apply(this, arguments);
        }

        $.time33 = function (string) {
            var hash = 0;
            for (var i=string.length-1; i>=0; i--) {
                hash = hash*33 + string.substr(i, i+1).charCodeAt();
            }
            return hash.toString(36);
        };
        var loadingDiv = (function(){
            var loadingDiv = document.createElement('div');
            loadingDiv.id = '__loading';
            loadingDiv.className = 'body_loading';
            loadingDiv.innerHTML = "<img src='/theme/img/loading.gif' alt='加载中...' />";
            loadingDiv.style.position = "absolute";
            loadingDiv.style.left = "49.5%";
            loadingDiv.style.top = "64%";
            loadingDiv.style.zIndex = '9999999';

            return loadingDiv;
        })();

        $._ajax = $.ajax;
        $.ajax = function (opt) {
            var url_hash = "";
            //if(opt.url) url_hash = $.time33(0<=opt.url.indexOf('?')? opt.url.substr(0, opt.url.indexOf('?')) : opt.url);
            url_hash = $.time33(opt.url);

            //加载Loading图片
            if (typeof opt.loading === 'undefined' || opt.loading == true) $('body').append(loadingDiv);

            if(opt.type==undefined) opt.type="get";
            if(opt.type.toLowerCase() == "post"){
                //opt.data[$("#__csrf_token").attr("name")] = $("#__csrf_token").val();
            } else {
                opt.url = encodeURI(opt.url);
            }

            if (last_ajax[url_hash]!=null) last_ajax[url_hash].abort();

            //bugfix: 兼容laravel的分页
            if(opt.data && opt.data.start && opt.data.length)
                opt.url += ('&page='+opt.data.start/opt.data.length);
            last_ajax[url_hash] = $._ajax(opt).complete(function(data){
                if(data.readyState == 0)
                    return false;
                if(opt.type.toLowerCase() == "post"){
                    try {
                        /**
                         * 提示错误信息
                         */
                        var result = $.JSON.parse(data.responseText);
                        parse(result);
                        /*
                        if(result.ret != 1 && result.code == 1){
                            $(".login-popup").click();
                        }
                        else if(result.ret != 1){
                            error('操作失败', result.info);
                        }
                        */
                    } catch (e) {
                        if($.cur_log_depth ++ < $.max_log_depth){
                            error('操作失败', '操作失败');
                            //js_log
                        }

                    }
                }
                else {
                    var result = $.JSON.parse(data.responseText);
                    if (result.ret == 0 && result.info == 'logout') {
                        location.reload();
                    }
                }

                $('#__loading').remove();
            });
        };
    };
    
    var upload = function(upload_id, callback, start_callback, options) {
        setTimeout(function(){
            $(upload_id).uploadify({
                'formData'     : {
                    'timestamp' : new Date().getTime(),
                    'token'     : new Date().getTime()
                },
                'method'    : 'post',
                'buttonText': '<button class="btn btn-primary">选择文件</button>',
                'swf'      : '/theme/vendors/uploadify/uploadify.swf',
                'uploader' : options && options.url ? options.url : '/image/preview',
                'queueID': 'fileQueue',
                'width' : options && options.width ? options.width: '80',
                'buttonImage' : '',
                //'buttonImage' : ''options && options.button_image ? options.button_image : '/img/upphoto.png',
                'auto': true,
                'multi': false,
                'onUploadSuccess': function (file, data, response) {
                    callback && callback($.JSON.parse(data), upload_id);
                },
                'onUploadProgress' : function(file, bytesUploaded, bytesTotal, totalBytesUploaded, totalBytesTotal) {
                    var progress_bar = $(".pace-inactive");
                    if(progress_bar.length > 0){
                        if(bytesUploaded == 0){
                            progress_bar.find(".pace-progress").css("width", "100%");
                        }
                        else {
                            progress_bar.hide();
                        }
                    }
                },
                'onUploadStart': function(data){

                    var progress_bar = $(".pace-inactive");
                    progress_bar.find(".pace-progress").css("width", 0);
                    progress_bar.show();
                    start_callback && start_callback (data);
                }
            });
        },10);
    };

    return {
        upload: upload,
        //main function to the common tools
        init: function() {
            ajaxSetting();
        }
    };
}();
Common.init();


function getQueryVariable(variable){
    var query = window.location.search.substring(1);
    var vars = query.split("&");
    for (var i=0;i<vars.length;i++) {
        var pair = vars[i].split("=");
        if(pair[0] == variable){return pair[1];}
    }
    return(false);
}


function time( timeMatrixing ){
    var t =  Number( timeMatrixing )*1000;
    var now = new Date().getTime();
    var second = Math.ceil((now - t)/1000);
    var str = '';
    var s = 0;
    if( second < 60 ){ 
        s = Math.ceil(second);
        str = '刚刚';
    }
    else if( second < (60*60)){
        s = Math.ceil(second/60);
        str = s+'分钟前';
    }
    else if( second < (60*60*24) ){ 
        s = Math.ceil(second/(60*60));
        str = s+'小时前';
    }
    else if( second < (60*60*24*2) ){
        str = '一天前';
    }
    else if( second < (60*60*24*3) ){
        str = '一天前';
    }
    else if( second < (60*60*24*4) ){
        str = '两天前';
    }
    else if( second < (60*60*24*5) ){
        str = '三天前';
    }
    else if( second < (60*60*24*6) ){
        str = '四天前';
    }
    else if( second < (60*60*24*6) ){
        str = '五天前';
    }
    else if( second < (60*60*24*7) ){
        str = '六天前';
    }
    else if( second < (60*60*24*8) ){
        str = '一周前';
    }
    else{
        var ts = new Date( t );
        var minute = 0;
        var getMinute = ts.getMinutes();
        if( ts.getMinutes() < 10 ) {
            minute = '0' + getMinute;
        } else {
            minute = getMinute;
        }
        str = ts.getFullYear() + '-' + (ts.getMonth()+1) + '-' + ts.getDate() + ' ';
        str+= ts.getHours() + ':' + minute;
    }
    return str;
}

function append(el, item, options) {
    var opt = {
        time: 400
    }
    for(var i in options) {
        opt[i] = options[i];
    }
    var item = $(item).clone().hide();
    $(el).append(item);
    item.fadeIn(opt.time);
};

function error(title, desc, callback) {
    $("a#show-error-popup").fancybox({
        afterShow: function(){
            $('.confirm, .cancel').click(function(){ 
                $.fancybox.close();
                callback && callback();
            });
        },
        padding : 0
    });
    $("#error-popup .title").text(title);
    $("#error-popup .error-content").text(desc);

    $("#show-error-popup").click();
};

function toast(desc, callback) {
    $("a#show-toast-popup").fancybox({
        autoSize: true,
        closeBtn : false,
        helpers: {
            overlay : null
        }
    });
    $("#toast-popup .error-content").text(desc);

    $("#show-toast-popup").click();
    setTimeout(function() {
        $.fancybox.close();
        callback && callback();
    }, 2000);
};

function parse(resp, xhr) { 
    // todo  billqiang QQ
      // QC.Login({
      //       btnId:"qqLoginBtn",   //插入按钮的节点id
      //   });
    if(resp.ret == 0 && resp.code == 1  ) {


        WB2.anyWhere(function (W) {
            W.widget.connectButton({
                id: "wb_login_btn",
                //type: '3,2',
                callback: {
                    login: account.weibo_auth
                }
            });
            W.widget.connectButton({
                id: "wb_register_btn",
                //type: '3,2',
                callback: {
                    login: account.weibo_auth
                }
            });
        });
    }

    if(resp.ret == 0 && resp.code == 1 && this.url != 'user/status') { 
        if(WB2.oauthData.access_token) {
            //微博注册
            $(".binding-popup").click();
        }
        else {
            //原生登陆
            $(".login-popup").click();
        }
        return false;
    } 
    else if(resp.ret == 0 && this.url != 'user/status') {
        error('操作失败', resp.info);
    }
    //console.log('parsing base modelxxx');
    return resp.data;
};

var account = {
    keypress:function(e) {
        if(e.which == 13) {
           $("#login_btn").click(); 
        }
    },
    weibo_auth: function(e) {
        //默认只能绑定
        $.get('user/auth', {
            openid: WB2.oauthData.uid,
            type: 'weibo'
        }, function(data) {
            if(data.data.is_register == 0) {
                $(".binding-popup").click();
            }
            else {
                location.reload();
            }
        });

        if(e.gender == 'f') {
            $(".option-sex .option-girl input").click();
        }
        $('#register-avatar').attr('src', e.profile_image_url);
        $('#register_nickname').val(e.screen_name);
        $('#register_nickname').attr('type', 'weibo');
        $('#register_nickname').attr('openid', WB2.oauthData.uid);
        $(".login-popup").attr("href", "#binding-popup");

        if(!window.app.user.uid) {
            window.app.user.set('avatar', e.profile_image_url);
            window.app.user.set('nickname', e.screen_name);
            window.app.user.set('uid', e.uid);
            $(".login-popup").attr("href", "#binding-popup");
        }
        if($("#binding-popup").css("display") == 'none')
            $(".binding-popup").click();

    },
    login_keyup:function() {
        var username = $('#login_name').val();
        var password = $('#login_password').val();
        if(username != '' && password != '' ) {
            $('#login_btn').removeAttr('disabled').css('background','#F7DF68');
        }
        if(username == '' || password == '' ) {
            $('#login_btn').attr("disabled", true).css('background','#EBEBEB');
        }
    },

    login: function(e) {
        var self = this;
        var username = $('#login_name').val();
        var password = $('#login_password').val();

        $.post('/user/login', {
            username: username, 
            password: password
        }, function(returnData, data) {
            if( returnData.ret == 1 ) {
                history.go(1);
                location.reload();
            } else {
                console.log(returnData);
                return false;
            }
        });
    },
    register_keyup:function() {
        var nickname = $('#register_nickname').val();
        var phone =  $('#register_phone').val();
        var password = $('#register_password').val();

        if(nickname != '' && phone != '' && password != '' ) {
            $('.register-btn').removeAttr('disabled').addClass('bg-btn');
        }
        if(nickname == '' || phone == '' || password == '' ) {
            $('.register-btn').attr("disabled", true).removeClass('bg-btn');
        }
    },
    register: function (e) {
            var self = this;
            var boy = $('.boy-option').hasClass('boy-pressed');
            var sex = 1;
            var code = $('#register-popup input[name=registerCode]').val();
            var avatar = $('#register-avatar').attr('src');
            var nickname = $('#register_nickname').val();
            var phone    =  $('#register_phone').val();
            var password = $('#register_password').val();
    
            var url = "/user/register";
            var postData = {
                'nickname': nickname,
                'sex' : sex,
                'mobile': phone,
                'password': password,
                'avatar' : avatar,
                'code' : code
            };
            $.post(url, postData, function( returnData ){
                if(returnData.ret != 0)
                    location.reload();
            });

    },
    bind: function() {
        var boy = $('.boy-option').hasClass('boy-pressed');
        var sex = boy ? 0 : 1;
        var avatar = $('#register-avatar').attr('src');
        var nickname = $('#register_nickname').val();

        var phone = $('input[name=binding-phone]').val();
        var code = $('input[name=binding-code]').val();
        var password = $('input[name=binding-password]').val();

        var type    = $('#register_nickname').attr('type');
        var openid  = $('#register_nickname').attr('openid');
        if( phone == '') {
            //todo: 验证码
            alert('手机号不能为空');
            return false;
        }
        if( code == '') {
            alert('验证码不能为空');
            return false;
        }
        if( password == '' ) {
            alert('密码不能为空');
            return false;
        }
        var url = "/user/register";
        var postData = {
            'type': type,
            'openid': openid,
            'nickname': nickname,
            'avatar': avatar,
            'sex': sex,
            'mobile': phone,
            'code' : code,
            'password': password,
        };
        $.post(url, postData, function( returnData ){
            if(returnData.ret != 0)
                location.reload();
        });
    },
    optionSex: function(event) {
        $('.sex-pressed').removeClass('boy-pressed').removeClass('girl-pressed');
        $(event.currentTarget).addClass('boy-pressed');
        $(event.currentTarget).addClass('girl-pressed');
    }
};

define("common", ["jquery","swipe"], (function (global) {
    return function () {
        var ret, fn;
        return ret || global.common;
    };
}(this)));

//     Backbone.js 0.9.10

//     (c) 2010-2012 Jeremy Ashkenas, DocumentCloud Inc.
//     Backbone may be freely distributed under the MIT license.
//     For all details and documentation:
//     http://backbonejs.org

(function () {

    // Initial Setup
    // -------------

    // Save a reference to the global object (`window` in the browser, `exports`
    // on the server).
    var root = this;

    // Save the previous value of the `Backbone` variable, so that it can be
    // restored later on, if `noConflict` is used.
    var previousBackbone = root.Backbone;

    // Create a local reference to array methods.
    var array = [];
    var push = array.push;
    var slice = array.slice;
    var splice = array.splice;

    // The top-level namespace. All public Backbone classes and modules will
    // be attached to this. Exported for both CommonJS and the browser.
    var Backbone;
    if (typeof exports !== 'undefined') {
        Backbone = exports;
    } else {
        Backbone = root.Backbone = {};
    }

    // Current version of the library. Keep in sync with `package.json`.
    Backbone.VERSION = '0.9.10';

    // Require Underscore, if we're on the server, and it's not already present.
    var _ = root._;
    if (!_ && (typeof require !== 'undefined')) _ = require('underscore');

    // For Backbone's purposes, jQuery, Zepto, or Ender owns the `$` variable.
    Backbone.$ = root.jQuery || root.Zepto || root.ender;

    // Runs Backbone.js in *noConflict* mode, returning the `Backbone` variable
    // to its previous owner. Returns a reference to this Backbone object.
    Backbone.noConflict = function () {
        root.Backbone = previousBackbone;
        return this;
    };

    // Turn on `emulateHTTP` to support legacy HTTP servers. Setting this option
    // will fake `"PUT"` and `"DELETE"` requests via the `_method` parameter and
    // set a `X-Http-Method-Override` header.
    Backbone.emulateHTTP = false;

    // Turn on `emulateJSON` to support legacy servers that can't deal with direct
    // `application/json` requests ... will encode the body as
    // `application/x-www-form-urlencoded` instead and will send the model in a
    // form param named `model`.
    Backbone.emulateJSON = false;

    // Backbone.Events
    // ---------------

    // Regular expression used to split event strings.
    var eventSplitter = /\s+/;

    // Implement fancy features of the Events API such as multiple event
    // names `"change blur"` and jQuery-style event maps `{change: action}`
    // in terms of the existing API.
    var eventsApi = function (obj, action, name, rest) {
        if (!name) return true;
        if (typeof name === 'object') {
            for (var key in name) {
                obj[action].apply(obj, [key, name[key]].concat(rest));
            }
        } else if (eventSplitter.test(name)) {
            var names = name.split(eventSplitter);
            for (var i = 0, l = names.length; i < l; i++) {
                obj[action].apply(obj, [names[i]].concat(rest));
            }
        } else {
            return true;
        }
    };

    // Optimized internal dispatch function for triggering events. Tries to
    // keep the usual cases speedy (most Backbone events have 3 arguments).
    var triggerEvents = function (events, args) {
        var ev, i = -1, l = events.length;
        switch (args.length) {
            case 0:
                while (++i < l) (ev = events[i]).callback.call(ev.ctx);
                return;
            case 1:
                while (++i < l) (ev = events[i]).callback.call(ev.ctx, args[0]);
                return;
            case 2:
                while (++i < l) (ev = events[i]).callback.call(ev.ctx, args[0], args[1]);
                return;
            case 3:
                while (++i < l) (ev = events[i]).callback.call(ev.ctx, args[0], args[1], args[2]);
                return;
            default:
                while (++i < l) (ev = events[i]).callback.apply(ev.ctx, args);
        }
    };

    // A module that can be mixed in to *any object* in order to provide it with
    // custom events. You may bind with `on` or remove with `off` callback
    // functions to an event; `trigger`-ing an event fires all callbacks in
    // succession.
    //
    //     var object = {};
    //     _.extend(object, Backbone.Events);
    //     object.on('expand', function(){ alert('expanded'); });
    //     object.trigger('expand');
    //
    var Events = Backbone.Events = {

        // Bind one or more space separated events, or an events map,
        // to a `callback` function. Passing `"all"` will bind the callback to
        // all events fired.
        on: function (name, callback, context) {
            if (!(eventsApi(this, 'on', name, [callback, context]) && callback)) return this;
            this._events || (this._events = {});
            var list = this._events[name] || (this._events[name] = []);
            list.push({callback: callback, context: context, ctx: context || this});
            return this;
        },

        // Bind events to only be triggered a single time. After the first time
        // the callback is invoked, it will be removed.
        once: function (name, callback, context) {
            if (!(eventsApi(this, 'once', name, [callback, context]) && callback)) return this;
            var self = this;
            var once = _.once(function () {
                self.off(name, once);
                callback.apply(this, arguments);
            });
            once._callback = callback;
            this.on(name, once, context);
            return this;
        },

        // Remove one or many callbacks. If `context` is null, removes all
        // callbacks with that function. If `callback` is null, removes all
        // callbacks for the event. If `name` is null, removes all bound
        // callbacks for all events.
        off: function (name, callback, context) {
            var list, ev, events, names, i, l, j, k;
            if (!this._events || !eventsApi(this, 'off', name, [callback, context])) return this;
            if (!name && !callback && !context) {
                this._events = {};
                return this;
            }

            names = name ? [name] : _.keys(this._events);
            for (i = 0, l = names.length; i < l; i++) {
                name = names[i];
                if (list = this._events[name]) {
                    events = [];
                    if (callback || context) {
                        for (j = 0, k = list.length; j < k; j++) {
                            ev = list[j];
                            if ((callback && callback !== ev.callback &&
                                callback !== ev.callback._callback) ||
                                (context && context !== ev.context)) {
                                events.push(ev);
                            }
                        }
                    }
                    this._events[name] = events;
                }
            }

            return this;
        },

        // Trigger one or many events, firing all bound callbacks. Callbacks are
        // passed the same arguments as `trigger` is, apart from the event name
        // (unless you're listening on `"all"`, which will cause your callback to
        // receive the true name of the event as the first argument).
        trigger: function (name) {
            if (!this._events) return this;
            var args = slice.call(arguments, 1);
            if (!eventsApi(this, 'trigger', name, args)) return this;
            var events = this._events[name];
            var allEvents = this._events.all;
            if (events) triggerEvents(events, args);
            if (allEvents) triggerEvents(allEvents, arguments);
            return this;
        },

        // An inversion-of-control version of `on`. Tell *this* object to listen to
        // an event in another object ... keeping track of what it's listening to.
        listenTo: function (obj, name, callback) {
            var listeners = this._listeners || (this._listeners = {});
            var id = obj._listenerId || (obj._listenerId = _.uniqueId('l'));
            listeners[id] = obj;
            obj.on(name, typeof name === 'object' ? this : callback, this);
            return this;
        },

        // Tell this object to stop listening to either specific events ... or
        // to every object it's currently listening to.
        stopListening: function (obj, name, callback) {
            var listeners = this._listeners;
            if (!listeners) return;
            if (obj) {
                obj.off(name, typeof name === 'object' ? this : callback, this);
                if (!name && !callback) delete listeners[obj._listenerId];
            } else {
                if (typeof name === 'object') callback = this;
                for (var id in listeners) {
                    listeners[id].off(name, callback, this);
                }
                this._listeners = {};
            }
            return this;
        }
    };

    // Aliases for backwards compatibility.
    Events.bind = Events.on;
    Events.unbind = Events.off;

    // Allow the `Backbone` object to serve as a global event bus, for folks who
    // want global "pubsub" in a convenient place.
    _.extend(Backbone, Events);

    // Backbone.Model
    // --------------

    // Create a new model, with defined attributes. A client id (`cid`)
    // is automatically generated and assigned for you.
    var Model = Backbone.Model = function (attributes, options) {
        var defaults;
        var attrs = attributes || {};
        this.cid = _.uniqueId('c');
        this.attributes = {};
        if (options && options.collection) this.collection = options.collection;
        if (options && options.parse) attrs = this.parse(attrs, options) || {};
        if (defaults = _.result(this, 'defaults')) {
            attrs = _.defaults({}, attrs, defaults);
        }
        this.set(attrs, options);
        this.changed = {};
        this.initialize.apply(this, arguments);
    };

    // Attach all inheritable methods to the Model prototype.
    _.extend(Model.prototype, Events, {

        // A hash of attributes whose current and previous value differ.
        changed: null,

        // The default name for the JSON `id` attribute is `"id"`. MongoDB and
        // CouchDB users may want to set this to `"_id"`.
        idAttribute: 'id',

        // Initialize is an empty function by default. Override it with your own
        // initialization logic.
        initialize: function () {
        },

        // Return a copy of the model's `attributes` object.
        toJSON: function (options) {
            return _.clone(this.attributes);
        },

        // Proxy `Backbone.sync` by default.
        sync: function () {
            return Backbone.sync.apply(this, arguments);
        },

        // Get the value of an attribute.
        get: function (attr) {
            return this.attributes[attr];
        },

        // Get the HTML-escaped value of an attribute.
        escape: function (attr) {
            return _.escape(this.get(attr));
        },

        // Returns `true` if the attribute contains a value that is not null
        // or undefined.
        has: function (attr) {
            return this.get(attr) != null;
        },

        // ----------------------------------------------------------------------

        // Set a hash of model attributes on the object, firing `"change"` unless
        // you choose to silence it.
        set: function (key, val, options) {
            var attr, attrs, unset, changes, silent, changing, prev, current;
            if (key == null) return this;

            // Handle both `"key", value` and `{key: value}` -style arguments.
            if (typeof key === 'object') {
                attrs = key;
                options = val;
            } else {
                (attrs = {})[key] = val;
            }

            options || (options = {});

            // Run validation.
            if (!this._validate(attrs, options)) return false;

            // Extract attributes and options.
            unset = options.unset;
            silent = options.silent;
            changes = [];
            changing = this._changing;
            this._changing = true;

            if (!changing) {
                this._previousAttributes = _.clone(this.attributes);
                this.changed = {};
            }
            current = this.attributes, prev = this._previousAttributes;

            // Check for changes of `id`.
            if (this.idAttribute in attrs) this.id = attrs[this.idAttribute];

            // For each `set` attribute, update or delete the current value.
            for (attr in attrs) {
                val = attrs[attr];
                if (!_.isEqual(current[attr], val)) changes.push(attr);
                if (!_.isEqual(prev[attr], val)) {
                    this.changed[attr] = val;
                } else {
                    delete this.changed[attr];
                }
                unset ? delete current[attr] : current[attr] = val;
            }

            // Trigger all relevant attribute changes.
            if (!silent) {
                if (changes.length) this._pending = true;
                for (var i = 0, l = changes.length; i < l; i++) {
                    this.trigger('change:' + changes[i], this, current[changes[i]], options);
                }
            }

            if (changing) return this;
            if (!silent) {
                while (this._pending) {
                    this._pending = false;
                    this.trigger('change', this, options);
                }
            }
            this._pending = false;
            this._changing = false;
            return this;
        },

        // Remove an attribute from the model, firing `"change"` unless you choose
        // to silence it. `unset` is a noop if the attribute doesn't exist.
        unset: function (attr, options) {
            return this.set(attr, void 0, _.extend({}, options, {unset: true}));
        },

        // Clear all attributes on the model, firing `"change"` unless you choose
        // to silence it.
        clear: function (options) {
            var attrs = {};
            for (var key in this.attributes) attrs[key] = void 0;
            return this.set(attrs, _.extend({}, options, {unset: true}));
        },

        // Determine if the model has changed since the last `"change"` event.
        // If you specify an attribute name, determine if that attribute has changed.
        hasChanged: function (attr) {
            if (attr == null) return !_.isEmpty(this.changed);
            return _.has(this.changed, attr);
        },

        // Return an object containing all the attributes that have changed, or
        // false if there are no changed attributes. Useful for determining what
        // parts of a view need to be updated and/or what attributes need to be
        // persisted to the server. Unset attributes will be set to undefined.
        // You can also pass an attributes object to diff against the model,
        // determining if there *would be* a change.
        changedAttributes: function (diff) {
            if (!diff) return this.hasChanged() ? _.clone(this.changed) : false;
            var val, changed = false;
            var old = this._changing ? this._previousAttributes : this.attributes;
            for (var attr in diff) {
                if (_.isEqual(old[attr], (val = diff[attr]))) continue;
                (changed || (changed = {}))[attr] = val;
            }
            return changed;
        },

        // Get the previous value of an attribute, recorded at the time the last
        // `"change"` event was fired.
        previous: function (attr) {
            if (attr == null || !this._previousAttributes) return null;
            return this._previousAttributes[attr];
        },

        // Get all of the attributes of the model at the time of the previous
        // `"change"` event.
        previousAttributes: function () {
            return _.clone(this._previousAttributes);
        },

        // ---------------------------------------------------------------------

        // Fetch the model from the server. If the server's representation of the
        // model differs from its current attributes, they will be overriden,
        // triggering a `"change"` event.
        fetch: function (options) {
            options = options ? _.clone(options) : {};
            if (options.parse === void 0) options.parse = true;
            var success = options.success;
            options.success = function (model, resp, options) {
                if (!model.set(model.parse(resp, options), options)) return false;
                if (success) success(model, resp, options);
            };
            return this.sync('read', this, options);
        },

        // Set a hash of model attributes, and sync the model to the server.
        // If the server returns an attributes hash that differs, the model's
        // state will be `set` again.
        save: function (key, val, options) {
            var attrs, success, method, xhr, attributes = this.attributes;

            // Handle both `"key", value` and `{key: value}` -style arguments.
            if (key == null || typeof key === 'object') {
                attrs = key;
                options = val;
            } else {
                (attrs = {})[key] = val;
            }

            // If we're not waiting and attributes exist, save acts as `set(attr).save(null, opts)`.
            if (attrs && (!options || !options.wait) && !this.set(attrs, options)) return false;

            options = _.extend({validate: true}, options);

            // Do not persist invalid models.
            if (!this._validate(attrs, options)) return false;

            // Set temporary attributes if `{wait: true}`.
            if (attrs && options.wait) {
                this.attributes = _.extend({}, attributes, attrs);
            }

            // After a successful server-side save, the client is (optionally)
            // updated with the server-side state.
            if (options.parse === void 0) options.parse = true;
            success = options.success;
            options.success = function (model, resp, options) {
                // Ensure attributes are restored during synchronous saves.
                model.attributes = attributes;
                var serverAttrs = model.parse(resp, options);
                if (options.wait) serverAttrs = _.extend(attrs || {}, serverAttrs);
                if (_.isObject(serverAttrs) && !model.set(serverAttrs, options)) {
                    return false;
                }
                if (success) success(model, resp, options);
            };

            // Finish configuring and sending the Ajax request.
            method = this.isNew() ? 'create' : (options.patch ? 'patch' : 'update');
            if (method === 'patch') options.attrs = attrs;
            xhr = this.sync(method, this, options);

            // Restore attributes.
            if (attrs && options.wait) this.attributes = attributes;

            return xhr;
        },

        // Destroy this model on the server if it was already persisted.
        // Optimistically removes the model from its collection, if it has one.
        // If `wait: true` is passed, waits for the server to respond before removal.
        destroy: function (options) {
            options = options ? _.clone(options) : {};
            var model = this;
            var success = options.success;

            var destroy = function () {
                model.trigger('destroy', model, model.collection, options);
            };

            options.success = function (model, resp, options) {
                if (options.wait || model.isNew()) destroy();
                if (success) success(model, resp, options);
            };

            if (this.isNew()) {
                options.success(this, null, options);
                return false;
            }

            var xhr = this.sync('delete', this, options);
            if (!options.wait) destroy();
            return xhr;
        },

        // Default URL for the model's representation on the server -- if you're
        // using Backbone's restful methods, override this to change the endpoint
        // that will be called.
        url: function () {
            var base = _.result(this, 'urlRoot') || _.result(this.collection, 'url') || urlError();
            if (this.isNew()) return base;
            return base + (base.charAt(base.length - 1) === '/' ? '' : '/') + encodeURIComponent(this.id);
        },

        // **parse** converts a response into the hash of attributes to be `set` on
        // the model. The default implementation is just to pass the response along.
        parse: function (resp, options) {
            return resp;
        },

        // Create a new model with identical attributes to this one.
        clone: function () {
            return new this.constructor(this.attributes);
        },

        // A model is new if it has never been saved to the server, and lacks an id.
        isNew: function () {
            return this.id == null;
        },

        // Check if the model is currently in a valid state.
        isValid: function (options) {
            return !this.validate || !this.validate(this.attributes, options);
        },

        // Run validation against the next complete set of model attributes,
        // returning `true` if all is well. Otherwise, fire a general
        // `"error"` event and call the error callback, if specified.
        _validate: function (attrs, options) {
            if (!options.validate || !this.validate) return true;
            attrs = _.extend({}, this.attributes, attrs);
            var error = this.validationError = this.validate(attrs, options) || null;
            if (!error) return true;
            this.trigger('invalid', this, error, options || {});
            return false;
        }

    });

    // Backbone.Collection
    // -------------------

    // Provides a standard collection class for our sets of models, ordered
    // or unordered. If a `comparator` is specified, the Collection will maintain
    // its models in sort order, as they're added and removed.
    var Collection = Backbone.Collection = function (models, options) {
        options || (options = {});
        if (options.model) this.model = options.model;
        if (options.comparator !== void 0) this.comparator = options.comparator;
        this.models = [];
        this._reset();
        this.initialize.apply(this, arguments);
        if (models) this.reset(models, _.extend({silent: true}, options));
    };

    // Define the Collection's inheritable methods.
    _.extend(Collection.prototype, Events, {

        // The default model for a collection is just a **Backbone.Model**.
        // This should be overridden in most cases.
        model: Model,

        // Initialize is an empty function by default. Override it with your own
        // initialization logic.
        initialize: function () {
        },

        // The JSON representation of a Collection is an array of the
        // models' attributes.
        toJSON: function (options) {
            return this.map(function (model) {
                return model.toJSON(options);
            });
        },

        // Proxy `Backbone.sync` by default.
        sync: function () {
            return Backbone.sync.apply(this, arguments);
        },

        // Add a model, or list of models to the set.
        add: function (models, options) {
            models = _.isArray(models) ? models.slice() : [models];
            options || (options = {});
            var i, l, model, attrs, existing, doSort, add, at, sort, sortAttr;
            add = [];
            at = options.at;
            sort = this.comparator && (at == null) && options.sort != false;
            sortAttr = _.isString(this.comparator) ? this.comparator : null;

            // Turn bare objects into model references, and prevent invalid models
            // from being added.
            for (i = 0, l = models.length; i < l; i++) {
                if (!(model = this._prepareModel(attrs = models[i], options))) {
                    this.trigger('invalid', this, attrs, options);
                    continue;
                }

                // If a duplicate is found, prevent it from being added and
                // optionally merge it into the existing model.
                if (existing = this.get(model)) {
                    if (options.merge) {
                        existing.set(attrs === model ? model.attributes : attrs, options);
                        if (sort && !doSort && existing.hasChanged(sortAttr)) doSort = true;
                    }
                    continue;
                }

                // This is a new model, push it to the `add` list.
                add.push(model);

                // Listen to added models' events, and index models for lookup by
                // `id` and by `cid`.
                model.on('all', this._onModelEvent, this);
                this._byId[model.cid] = model;
                if (model.id != null) this._byId[model.id] = model;
            }

            // See if sorting is needed, update `length` and splice in new models.
            if (add.length) {
                if (sort) doSort = true;
                this.length += add.length;
                if (at != null) {
                    splice.apply(this.models, [at, 0].concat(add));
                } else {
                    push.apply(this.models, add);
                }
            }

            // Silently sort the collection if appropriate.
            if (doSort) this.sort({silent: true});

            if (options.silent) return this;

            // Trigger `add` events.
            for (i = 0, l = add.length; i < l; i++) {
                (model = add[i]).trigger('add', model, this, options);
            }

            // Trigger `sort` if the collection was sorted.
            if (doSort) this.trigger('sort', this, options);

            return this;
        },

        // Remove a model, or a list of models from the set.
        remove: function (models, options) {
            models = _.isArray(models) ? models.slice() : [models];
            options || (options = {});
            var i, l, index, model;
            for (i = 0, l = models.length; i < l; i++) {
                model = this.get(models[i]);
                if (!model) continue;
                delete this._byId[model.id];
                delete this._byId[model.cid];
                index = this.indexOf(model);
                this.models.splice(index, 1);
                this.length--;
                if (!options.silent) {
                    options.index = index;
                    model.trigger('remove', model, this, options);
                }
                this._removeReference(model);
            }
            return this;
        },

        // Add a model to the end of the collection.
        push: function (model, options) {
            model = this._prepareModel(model, options);
            this.add(model, _.extend({at: this.length}, options));
            return model;
        },

        // Remove a model from the end of the collection.
        pop: function (options) {
            var model = this.at(this.length - 1);
            this.remove(model, options);
            return model;
        },

        // Add a model to the beginning of the collection.
        unshift: function (model, options) {
            model = this._prepareModel(model, options);
            this.add(model, _.extend({at: 0}, options));
            return model;
        },

        // Remove a model from the beginning of the collection.
        shift: function (options) {
            var model = this.at(0);
            this.remove(model, options);
            return model;
        },

        // Slice out a sub-array of models from the collection.
        slice: function (begin, end) {
            return this.models.slice(begin, end);
        },

        // Get a model from the set by id.
        get: function (obj) {
            if (obj == null) return void 0;
            this._idAttr || (this._idAttr = this.model.prototype.idAttribute);
            return this._byId[obj.id || obj.cid || obj[this._idAttr] || obj];
        },

        // Get the model at the given index.
        at: function (index) {
            return this.models[index];
        },

        // Return models with matching attributes. Useful for simple cases of `filter`.
        where: function (attrs) {
            if (_.isEmpty(attrs)) return [];
            return this.filter(function (model) {
                for (var key in attrs) {
                    if (attrs[key] !== model.get(key)) return false;
                }
                return true;
            });
        },

        // Force the collection to re-sort itself. You don't need to call this under
        // normal circumstances, as the set will maintain sort order as each item
        // is added.
        sort: function (options) {
            if (!this.comparator) {
                throw new Error('Cannot sort a set without a comparator');
            }
            options || (options = {});

            // Run sort based on type of `comparator`.
            if (_.isString(this.comparator) || this.comparator.length === 1) {
                this.models = this.sortBy(this.comparator, this);
            } else {
                this.models.sort(_.bind(this.comparator, this));
            }

            if (!options.silent) this.trigger('sort', this, options);
            return this;
        },

        // Pluck an attribute from each model in the collection.
        pluck: function (attr) {
            return _.invoke(this.models, 'get', attr);
        },

        // Smartly update a collection with a change set of models, adding,
        // removing, and merging as necessary.
        update: function (models, options) {
            options = _.extend({add: true, merge: true, remove: true}, options);
            if (options.parse) models = this.parse(models, options);
            var model, i, l, existing;
            var add = [], remove = [], modelMap = {};

            // Allow a single model (or no argument) to be passed.
            if (!_.isArray(models)) models = models ? [models] : [];

            // Proxy to `add` for this case, no need to iterate...
            if (options.add && !options.remove) return this.add(models, options);

            // Determine which models to add and merge, and which to remove.
            for (i = 0, l = models.length; i < l; i++) {
                model = models[i];
                existing = this.get(model);
                if (options.remove && existing) modelMap[existing.cid] = true;
                if ((options.add && !existing) || (options.merge && existing)) {
                    add.push(model);
                }
            }
            if (options.remove) {
                for (i = 0, l = this.models.length; i < l; i++) {
                    model = this.models[i];
                    if (!modelMap[model.cid]) remove.push(model);
                }
            }

            // Remove models (if applicable) before we add and merge the rest.
            if (remove.length) this.remove(remove, options);
            if (add.length) this.add(add, options);
            return this;
        },

        // When you have more items than you want to add or remove individually,
        // you can reset the entire set with a new list of models, without firing
        // any `add` or `remove` events. Fires `reset` when finished.
        reset: function (models, options) {
            options || (options = {});
            if (options.parse) models = this.parse(models, options);
            for (var i = 0, l = this.models.length; i < l; i++) {
                this._removeReference(this.models[i]);
            }
            options.previousModels = this.models.slice();
            this._reset();
            if (models) this.add(models, _.extend({silent: true}, options));
            if (!options.silent) this.trigger('reset', this, options);
            return this;
        },

        // Fetch the default set of models for this collection, resetting the
        // collection when they arrive. If `update: true` is passed, the response
        // data will be passed through the `update` method instead of `reset`.
        fetch: function (options) {
            options = options ? _.clone(options) : {};
            if (options.parse === void 0) options.parse = true;
            var success = options.success;
            options.success = function (collection, resp, options) {
                var method = options.update ? 'update' : 'reset';
                collection[method](resp, options);
                if (success) success(collection, resp, options);
            };
            return this.sync('read', this, options);
        },

        // Create a new instance of a model in this collection. Add the model to the
        // collection immediately, unless `wait: true` is passed, in which case we
        // wait for the server to agree.
        create: function (model, options) {
            options = options ? _.clone(options) : {};
            if (!(model = this._prepareModel(model, options))) return false;
            if (!options.wait) this.add(model, options);
            var collection = this;
            var success = options.success;
            options.success = function (model, resp, options) {
                if (options.wait) collection.add(model, options);
                if (success) success(model, resp, options);
            };
            model.save(null, options);
            return model;
        },

        // **parse** converts a response into a list of models to be added to the
        // collection. The default implementation is just to pass it through.
        parse: function (resp, options) {
            return resp;
        },

        // Create a new collection with an identical list of models as this one.
        clone: function () {
            return new this.constructor(this.models);
        },

        // Reset all internal state. Called when the collection is reset.
        _reset: function () {
            this.length = 0;
            this.models.length = 0;
            this._byId = {};
        },

        // Prepare a model or hash of attributes to be added to this collection.
        _prepareModel: function (attrs, options) {
            if (attrs instanceof Model) {
                if (!attrs.collection) attrs.collection = this;
                return attrs;
            }
            options || (options = {});
            options.collection = this;
            var model = new this.model(attrs, options);
            if (!model._validate(attrs, options)) return false;
            return model;
        },

        // Internal method to remove a model's ties to a collection.
        _removeReference: function (model) {
            if (this === model.collection) delete model.collection;
            model.off('all', this._onModelEvent, this);
        },

        // Internal method called every time a model in the set fires an event.
        // Sets need to update their indexes when models change ids. All other
        // events simply proxy through. "add" and "remove" events that originate
        // in other collections are ignored.
        _onModelEvent: function (event, model, collection, options) {
            if ((event === 'add' || event === 'remove') && collection !== this) return;
            if (event === 'destroy') this.remove(model, options);
            if (model && event === 'change:' + model.idAttribute) {
                delete this._byId[model.previous(model.idAttribute)];
                if (model.id != null) this._byId[model.id] = model;
            }
            this.trigger.apply(this, arguments);
        },

        sortedIndex: function (model, value, context) {
            value || (value = this.comparator);
            var iterator = _.isFunction(value) ? value : function (model) {
                return model.get(value);
            };
            return _.sortedIndex(this.models, model, iterator, context);
        }

    });

    // Underscore methods that we want to implement on the Collection.
    var methods = ['forEach', 'each', 'map', 'collect', 'reduce', 'foldl',
        'inject', 'reduceRight', 'foldr', 'find', 'detect', 'filter', 'select',
        'reject', 'every', 'all', 'some', 'any', 'include', 'contains', 'invoke',
        'max', 'min', 'toArray', 'size', 'first', 'head', 'take', 'initial', 'rest',
        'tail', 'drop', 'last', 'without', 'indexOf', 'shuffle', 'lastIndexOf',
        'isEmpty', 'chain'];

    // Mix in each Underscore method as a proxy to `Collection#models`.
    _.each(methods, function (method) {
        Collection.prototype[method] = function () {
            var args = slice.call(arguments);
            args.unshift(this.models);
            return _[method].apply(_, args);
        };
    });

    // Underscore methods that take a property name as an argument.
    var attributeMethods = ['groupBy', 'countBy', 'sortBy'];

    // Use attributes instead of properties.
    _.each(attributeMethods, function (method) {
        Collection.prototype[method] = function (value, context) {
            var iterator = _.isFunction(value) ? value : function (model) {
                return model.get(value);
            };
            return _[method](this.models, iterator, context);
        };
    });

    // Backbone.Router
    // ---------------

    // Routers map faux-URLs to actions, and fire events when routes are
    // matched. Creating a new one sets its `routes` hash, if not set statically.
    var Router = Backbone.Router = function (options) {
        options || (options = {});
        if (options.routes) this.routes = options.routes;
        this._bindRoutes();
        this.initialize.apply(this, arguments);
    };

    // Cached regular expressions for matching named param parts and splatted
    // parts of route strings.
    var optionalParam = /\((.*?)\)/g;
    var namedParam = /(\(\?)?:\w+/g;
    var splatParam = /\*\w+/g;
    var escapeRegExp = /[\-{}\[\]+?.,\\\^$|#\s]/g;

    // Set up all inheritable **Backbone.Router** properties and methods.
    _.extend(Router.prototype, Events, {

        // Initialize is an empty function by default. Override it with your own
        // initialization logic.
        initialize: function () {
        },

        // Manually bind a single named route to a callback. For example:
        //
        //     this.route('search/:query/p:num', 'search', function(query, num) {
        //       ...
        //     });
        //
        route: function (route, name, callback) {
            if (!_.isRegExp(route)) route = this._routeToRegExp(route);
            if (!callback) callback = this[name];
            Backbone.history.route(route, _.bind(function (fragment) {
                var args = this._extractParameters(route, fragment);
                callback && callback.apply(this, args);
                this.trigger.apply(this, ['route:' + name].concat(args));
                this.trigger('route', name, args);
                Backbone.history.trigger('route', this, name, args);
            }, this));
            return this;
        },

        // Simple proxy to `Backbone.history` to save a fragment into the history.
        navigate: function (fragment, options) {
            Backbone.history.navigate(fragment, options);
            return this;
        },

        // Bind all defined routes to `Backbone.history`. We have to reverse the
        // order of the routes here to support behavior where the most general
        // routes can be defined at the bottom of the route map.
        _bindRoutes: function () {
            if (!this.routes) return;
            var route, routes = _.keys(this.routes);
            while ((route = routes.pop()) != null) {
                this.route(route, this.routes[route]);
            }
        },

        // Convert a route string into a regular expression, suitable for matching
        // against the current location hash.
        _routeToRegExp: function (route) {
            route = route.replace(escapeRegExp, '\\$&')
                .replace(optionalParam, '(?:$1)?')
                .replace(namedParam, function (match, optional) {
                    return optional ? match : '([^\/]+)';
                })
                .replace(splatParam, '(.*?)');
            return new RegExp('^' + route + '$');
        },

        // Given a route, and a URL fragment that it matches, return the array of
        // extracted parameters.
        _extractParameters: function (route, fragment) {
            return route.exec(fragment).slice(1);
        }

    });

    // Backbone.History
    // ----------------

    // Handles cross-browser history management, based on URL fragments. If the
    // browser does not support `onhashchange`, falls back to polling.
    var History = Backbone.History = function () {
        this.handlers = [];
        _.bindAll(this, 'checkUrl');

        // Ensure that `History` can be used outside of the browser.
        if (typeof window !== 'undefined') {
            this.location = window.location;
            this.history = window.history;
        }
    };

    // Cached regex for stripping a leading hash/slash and trailing space.
    var routeStripper = /^[#\/]|\s+$/g;

    // Cached regex for stripping leading and trailing slashes.
    var rootStripper = /^\/+|\/+$/g;

    // Cached regex for detecting MSIE.
    var isExplorer = /msie [\w.]+/;

    // Cached regex for removing a trailing slash.
    var trailingSlash = /\/$/;

    // Has the history handling already been started?
    History.started = false;

    // Set up all inheritable **Backbone.History** properties and methods.
    _.extend(History.prototype, Events, {

        // The default interval to poll for hash changes, if necessary, is
        // twenty times a second.
        interval: 50,

        // Gets the true hash value. Cannot use location.hash directly due to bug
        // in Firefox where location.hash will always be decoded.
        getHash: function (window) {
            var match = (window || this).location.href.match(/#(.*)$/);
            return match ? match[1] : '';
        },

        // Get the cross-browser normalized URL fragment, either from the URL,
        // the hash, or the override.
        getFragment: function (fragment, forcePushState) {
            if (fragment == null) {
                if (this._hasPushState || !this._wantsHashChange || forcePushState) {
                    fragment = this.location.pathname;
                    var root = this.root.replace(trailingSlash, '');
                    if (!fragment.indexOf(root)) fragment = fragment.substr(root.length);
                } else {
                    fragment = this.getHash();
                }
            }
            return fragment.replace(routeStripper, '');
        },

        // Start the hash change handling, returning `true` if the current URL matches
        // an existing route, and `false` otherwise.
        start: function (options) {
            if (History.started) throw new Error("Backbone.history has already been started");
            History.started = true;

            // Figure out the initial configuration. Do we need an iframe?
            // Is pushState desired ... is it available?
            this.options = _.extend({}, {root: '/'}, this.options, options);
            this.root = this.options.root;
            this._wantsHashChange = this.options.hashChange !== false;
            this._wantsPushState = !!this.options.pushState;
            this._hasPushState = !!(this.options.pushState && this.history && this.history.pushState);
            var fragment = this.getFragment();
            var docMode = document.documentMode;
            var oldIE = (isExplorer.exec(navigator.userAgent.toLowerCase()) && (!docMode || docMode <= 7));

            // Normalize root to always include a leading and trailing slash.
            this.root = ('/' + this.root + '/').replace(rootStripper, '/');

            if (oldIE && this._wantsHashChange) {
                this.iframe = Backbone.$('<iframe src="javascript:0" tabindex="-1" />').hide().appendTo('body')[0].contentWindow;
                this.navigate(fragment);
            }

            // Depending on whether we're using pushState or hashes, and whether
            // 'onhashchange' is supported, determine how we check the URL state.
            if (this._hasPushState) {
                Backbone.$(window).on('popstate', this.checkUrl);
            } else if (this._wantsHashChange && ('onhashchange' in window) && !oldIE) {
                Backbone.$(window).on('hashchange', this.checkUrl);
            } else if (this._wantsHashChange) {
                this._checkUrlInterval = setInterval(this.checkUrl, this.interval);
            }

            // Determine if we need to change the base url, for a pushState link
            // opened by a non-pushState browser.
            this.fragment = fragment;
            var loc = this.location;
            var atRoot = loc.pathname.replace(/[^\/]$/, '$&/') === this.root;

            // If we've started off with a route from a `pushState`-enabled browser,
            // but we're currently in a browser that doesn't support it...
            if (this._wantsHashChange && this._wantsPushState && !this._hasPushState && !atRoot) {
                this.fragment = this.getFragment(null, true);
                this.location.replace(this.root + this.location.search + '#' + this.fragment);
                // Return immediately as browser will do redirect to new url
                return true;

                // Or if we've started out with a hash-based route, but we're currently
                // in a browser where it could be `pushState`-based instead...
            } else if (this._wantsPushState && this._hasPushState && atRoot && loc.hash) {
                this.fragment = this.getHash().replace(routeStripper, '');
                this.history.replaceState({}, document.title, this.root + this.fragment + loc.search);
            }

            if (!this.options.silent) return this.loadUrl();
        },

        // Disable Backbone.history, perhaps temporarily. Not useful in a real app,
        // but possibly useful for unit testing Routers.
        stop: function () {
            Backbone.$(window).off('popstate', this.checkUrl).off('hashchange', this.checkUrl);
            clearInterval(this._checkUrlInterval);
            History.started = false;
        },

        // Add a route to be tested when the fragment changes. Routes added later
        // may override previous routes.
        route: function (route, callback) {
            this.handlers.unshift({route: route, callback: callback});
        },

        // Checks the current URL to see if it has changed, and if it has,
        // calls `loadUrl`, normalizing across the hidden iframe.
        checkUrl: function (e) {
            var current = this.getFragment();
            if (current === this.fragment && this.iframe) {
                current = this.getFragment(this.getHash(this.iframe));
            }
            if (current === this.fragment) return false;
            if (this.iframe) this.navigate(current);
            this.loadUrl() || this.loadUrl(this.getHash());
        },

        // Attempt to load the current URL fragment. If a route succeeds with a
        // match, returns `true`. If no defined routes matches the fragment,
        // returns `false`.
        loadUrl: function (fragmentOverride) {
            var fragment = this.fragment = this.getFragment(fragmentOverride);
            var matched = _.any(this.handlers, function (handler) {
                if (handler.route.test(fragment)) {
                    handler.callback(fragment);
                    return true;
                }
            });
            return matched;
        },

        // Save a fragment into the hash history, or replace the URL state if the
        // 'replace' option is passed. You are responsible for properly URL-encoding
        // the fragment in advance.
        //
        // The options object can contain `trigger: true` if you wish to have the
        // route callback be fired (not usually desirable), or `replace: true`, if
        // you wish to modify the current URL without adding an entry to the history.
        navigate: function (fragment, options) {
            if (!History.started) return false;
            if (!options || options === true) options = {trigger: options};
            fragment = this.getFragment(fragment || '');
            if (this.fragment === fragment) return;
            this.fragment = fragment;
            var url = this.root + fragment;

            // If pushState is available, we use it to set the fragment as a real URL.
            if (this._hasPushState) {
                this.history[options.replace ? 'replaceState' : 'pushState']({}, document.title, url);

                // If hash changes haven't been explicitly disabled, update the hash
                // fragment to store history.
            } else if (this._wantsHashChange) {
                this._updateHash(this.location, fragment, options.replace);
                if (this.iframe && (fragment !== this.getFragment(this.getHash(this.iframe)))) {
                    // Opening and closing the iframe tricks IE7 and earlier to push a
                    // history entry on hash-tag change.  When replace is true, we don't
                    // want this.
                    if (!options.replace) this.iframe.document.open().close();
                    this._updateHash(this.iframe.location, fragment, options.replace);
                }

                // If you've told us that you explicitly don't want fallback hashchange-
                // based history, then `navigate` becomes a page refresh.
            } else {
                return this.location.assign(url);
            }
            if (options.trigger) this.loadUrl(fragment);
        },

        // Update the hash location, either replacing the current entry, or adding
        // a new one to the browser history.
        _updateHash: function (location, fragment, replace) {
            if (replace) {
                var href = location.href.replace(/(javascript:|#).*$/, '');
                location.replace(href + '#' + fragment);
            } else {
                // Some browsers require that `hash` contains a leading #.
                location.hash = '#' + fragment;
            }
        }

    });

    // Create the default Backbone.history.
    Backbone.history = new History;

    // Backbone.View
    // -------------

    // Creating a Backbone.View creates its initial element outside of the DOM,
    // if an existing element is not provided...
    var View = Backbone.View = function (options) {
        this.cid = _.uniqueId('view');
        this._configure(options || {});
        this._ensureElement();
        this.initialize.apply(this, arguments);
        this.delegateEvents();
    };

    // Cached regex to split keys for `delegate`.
    var delegateEventSplitter = /^(\S+)\s*(.*)$/;

    // List of view options to be merged as properties.
    var viewOptions = ['model', 'collection', 'el', 'id', 'attributes', 'className', 'tagName', 'events'];

    // Set up all inheritable **Backbone.View** properties and methods.
    _.extend(View.prototype, Events, {

        // The default `tagName` of a View's element is `"div"`.
        tagName: 'div',

        // jQuery delegate for element lookup, scoped to DOM elements within the
        // current view. This should be prefered to global lookups where possible.
        $: function (selector) {
            return this.$el.find(selector);
        },

        // Initialize is an empty function by default. Override it with your own
        // initialization logic.
        initialize: function () {
        },

        // **render** is the core function that your view should override, in order
        // to populate its element (`this.el`), with the appropriate HTML. The
        // convention is for **render** to always return `this`.
        render: function () {
            return this;
        },

        // Remove this view by taking the element out of the DOM, and removing any
        // applicable Backbone.Events listeners.
        remove: function () {
            this.$el.remove();
            this.stopListening();
            return this;
        },

        // Change the view's element (`this.el` property), including event
        // re-delegation.
        setElement: function (element, delegate) {
            if (this.$el) this.undelegateEvents();
            this.$el = element instanceof Backbone.$ ? element : Backbone.$(element);
            this.el = this.$el[0];
            if (delegate !== false) this.delegateEvents();
            return this;
        },

        // Set callbacks, where `this.events` is a hash of
        //
        // *{"event selector": "callback"}*
        //
        //     {
        //       'mousedown .title':  'edit',
        //       'click .button':     'save'
        //       'click .open':       function(e) { ... }
        //     }
        //
        // pairs. Callbacks will be bound to the view, with `this` set properly.
        // Uses event delegation for efficiency.
        // Omitting the selector binds the event to `this.el`.
        // This only works for delegate-able events: not `focus`, `blur`, and
        // not `change`, `submit`, and `reset` in Internet Explorer.
        delegateEvents: function (events) {
            if (!(events || (events = _.result(this, 'events')))) return;
            this.undelegateEvents();
            for (var key in events) {
                var method = events[key];
                if (!_.isFunction(method)) method = this[events[key]];
                if (!method) throw new Error('Method "' + events[key] + '" does not exist');
                var match = key.match(delegateEventSplitter);
                var eventName = match[1], selector = match[2];
                method = _.bind(method, this);
                eventName += '.delegateEvents' + this.cid;
                if (selector === '') {
                    this.$el.on(eventName, method);
                } else {
                    this.$el.on(eventName, selector, method);
                }
            }
        },

        // Clears all callbacks previously bound to the view with `delegateEvents`.
        // You usually don't need to use this, but may wish to if you have multiple
        // Backbone views attached to the same DOM element.
        undelegateEvents: function () {
            this.$el.off('.delegateEvents' + this.cid);
        },

        // Performs the initial configuration of a View with a set of options.
        // Keys with special meaning *(model, collection, id, className)*, are
        // attached directly to the view.
        _configure: function (options) {
            if (this.options) options = _.extend({}, _.result(this, 'options'), options);
            _.extend(this, _.pick(options, viewOptions));
            this.options = options;
        },

        // Ensure that the View has a DOM element to render into.
        // If `this.el` is a string, pass it through `$()`, take the first
        // matching element, and re-assign it to `el`. Otherwise, create
        // an element from the `id`, `className` and `tagName` properties.
        _ensureElement: function () {
            if (!this.el) {
                var attrs = _.extend({}, _.result(this, 'attributes'));
                if (this.id) attrs.id = _.result(this, 'id');
                if (this.className) attrs['class'] = _.result(this, 'className');
                var $el = Backbone.$('<' + _.result(this, 'tagName') + '>').attr(attrs);
                this.setElement($el, false);
            } else {
                this.setElement(_.result(this, 'el'), false);
            }
        }

    });

    // Backbone.sync
    // -------------

    // Map from CRUD to HTTP for our default `Backbone.sync` implementation.
    var methodMap = {
        'create': 'POST',
        'update': 'PUT',
        'patch': 'PATCH',
        'delete': 'DELETE',
        'read': 'GET'
    };

    // Override this function to change the manner in which Backbone persists
    // models to the server. You will be passed the type of request, and the
    // model in question. By default, makes a RESTful Ajax request
    // to the model's `url()`. Some possible customizations could be:
    //
    // * Use `setTimeout` to batch rapid-fire updates into a single request.
    // * Send up the models as XML instead of JSON.
    // * Persist models via WebSockets instead of Ajax.
    //
    // Turn on `Backbone.emulateHTTP` in order to send `PUT` and `DELETE` requests
    // as `POST`, with a `_method` parameter containing the true HTTP method,
    // as well as all requests with the body as `application/x-www-form-urlencoded`
    // instead of `application/json` with the model in a param named `model`.
    // Useful when interfacing with server-side languages like **PHP** that make
    // it difficult to read the body of `PUT` requests.
    Backbone.sync = function (method, model, options) {
        var type = methodMap[method];

        // Default options, unless specified.
        _.defaults(options || (options = {}), {
            emulateHTTP: Backbone.emulateHTTP,
            emulateJSON: Backbone.emulateJSON
        });

        // Default JSON-request options.
        var params = {type: type, dataType: 'json'};

        // Ensure that we have a URL.
        if (!options.url) {
            params.url = _.result(model, 'url') || urlError();
        }

        // Ensure that we have the appropriate request data.
        if (options.data == null && model && (method === 'create' || method === 'update' || method === 'patch')) {
            params.contentType = 'application/json';
            params.data = JSON.stringify(options.attrs || model.toJSON(options));
        }

        // For older servers, emulate JSON by encoding the request into an HTML-form.
        if (options.emulateJSON) {
            params.contentType = 'application/x-www-form-urlencoded';
            params.data = params.data ? {model: params.data} : {};
        }

        // For older servers, emulate HTTP by mimicking the HTTP method with `_method`
        // And an `X-HTTP-Method-Override` header.
        if (options.emulateHTTP && (type === 'PUT' || type === 'DELETE' || type === 'PATCH')) {
            params.type = 'POST';
            if (options.emulateJSON) params.data._method = type;
            var beforeSend = options.beforeSend;
            options.beforeSend = function (xhr) {
                xhr.setRequestHeader('X-HTTP-Method-Override', type);
                if (beforeSend) return beforeSend.apply(this, arguments);
            };
        }

        // Don't process data on a non-GET request.
        if (params.type !== 'GET' && !options.emulateJSON) {
            params.processData = false;
        }

        var success = options.success;
        options.success = function (resp) {
            if (success) success(model, resp, options);
            model.trigger('sync', model, resp, options);
        };

        var error = options.error;
        options.error = function (xhr) {
            if (error) error(model, xhr, options);
            model.trigger('error', model, xhr, options);
        };

        // Make the request, allowing the user to override any Ajax options.
        var xhr = options.xhr = Backbone.ajax(_.extend(params, options));
        model.trigger('request', model, xhr, options);
        return xhr;
    };

    // Set the default implementation of `Backbone.ajax` to proxy through to `$`.
    Backbone.ajax = function () {
        return Backbone.$.ajax.apply(Backbone.$, arguments);
    };

    // Helpers
    // -------

    // Helper function to correctly set up the prototype chain, for subclasses.
    // Similar to `goog.inherits`, but uses a hash of prototype properties and
    // class properties to be extended.
    var extend = function (protoProps, staticProps) {
        var parent = this;
        var child;

        // The constructor function for the new subclass is either defined by you
        // (the "constructor" property in your `extend` definition), or defaulted
        // by us to simply call the parent's constructor.
        if (protoProps && _.has(protoProps, 'constructor')) {
            child = protoProps.constructor;
        } else {
            child = function () {
                return parent.apply(this, arguments);
            };
        }

        // Add static properties to the constructor function, if supplied.
        _.extend(child, parent, staticProps);

        // Set the prototype chain to inherit from `parent`, without calling
        // `parent`'s constructor function.
        var Surrogate = function () {
            this.constructor = child;
        };
        Surrogate.prototype = parent.prototype;
        child.prototype = new Surrogate;

        // Add prototype properties (instance properties) to the subclass,
        // if supplied.
        if (protoProps) _.extend(child.prototype, protoProps);

        // Set a convenience property in case the parent's prototype is needed
        // later.
        child.__super__ = parent.prototype;

        return child;
    };

    // Set up inheritance for the model, collection, router, view and history.
    Model.extend = Collection.extend = Router.extend = View.extend = History.extend = extend;

    // Throw an error when a URL is needed, and none is supplied.
    var urlError = function () {
        throw new Error('A "url" property or function must be specified');
    };

}).call(this);

define("backbone", ["jquery","cookie","underscore","common"], (function (global) {
    return function () {
        var ret, fn;
        return ret || global.Backbone;
    };
}(this)));

//表情选择插件
(function($){  
	$.fn.emojiSelector = function(options) {
		var defaults = {
			id : 'facebox', //表情框id
			path : 'face/', 
			assign : 'content' //输入框id
		};

		var option = $.extend(defaults, options);
		var assign = $('#' + option.assign);
		var id = option.id;
		var path = option.path;
	
		// 表情文件对应shortname
		var faces = [
			'angry', 'anguished', 'astonished', 'blush', 'broken_heart', 'clap', 'cold_sweat', 'confounded', 'confused', 'cry', 'disappointed', 'dizzy_face', 'expressionless', 'fearful', 'flushed', 'frowning', 'grimacing', 'grin', 'grinning', 'heart_eyes', 'heart', 'hushed', 'innocent', 'joy', 'kissing_closed_eyes', 'kissing_heart', 'kissing', 'laughing', 'mask', 'muscle', 'ok_hand', 'open_mouth', 'pensive', 'persevere', 'point_down', 'point_left', 'point_right', 'pray', 'relaxed', 'relieved', 'satisfied', 'scream', 'sleeping', 'sleepy', 'smile', 'smiley', 'smirk', 'sob', 'stuck_out_tongue_closed_eyes', 'stuck_out_tongue_winking_eye', 'sunglasses', 'sweat_smile', 'sweat', 'thumbsdown', 'thumbsup', 'unamused', 'v', 'wink', 'wink2', 'zzz'
		];

		if(assign.length <= 0){
			alert('缺少表情赋值对象。');
			return false;
		}
		
		$(this).click(function(e){
			var strFace, labFace;
			if($('#' + id).length <= 0){
				strFace = '<div id="' + id + '" style="position:absolute;display:none;z-index:1000;" class="qqFace">' +
							  '<table border="0" cellspacing="0" cellpadding="0"><tr>';
				for(var i = 1; i <= 60; i++) {
					labFace = faces[i - 1];
					labFaceStr = ':' + labFace + ':';

					strFace += '<td><img class="emoji-selector-icon" src="' + path + labFace +'.png" onclick="$(\'#'+option.assign+'\').insertAtCaret(\'' + labFaceStr + '\');" /></td>';
					
					if( i % 15 == 0 ) strFace += '</tr><tr>';
				}
				strFace += '</tr></table></div>';
			}
			$(this).parent().append(strFace);
			
			var offset = $(this).position();
			var top = offset.top + $(this).outerHeight();

			$('#'+id).css('top',top);
			$('#'+id).css('left',offset.left);
			$('#'+id).show();

			e.stopPropagation();
		});

		$(document).click(function(){
			$('#' + id).hide();
			$('#' + id).remove();
		});
	};

})(jQuery);

jQuery.fn.extend({ 
	// 在光标位置插入表情
	insertAtCaret: function(textFeildValue, src) { 
		var textObj = $(this).get(0); 

		if(document.all && textObj.createTextRange && textObj.caretPos){ 
			var caretPos=textObj.caretPos; 

			caretPos.text = caretPos.text.charAt(caretPos.text.length - 1) == '' ? 
				textFeildValue + '' : textFeildValue; 
		} else if(textObj.setSelectionRange){ 
			var rangeStart = textObj.selectionStart; 
			var rangeEnd = textObj.selectionEnd; 
			var tempStr1 = textObj.value.substring(0, rangeStart); 
			var tempStr2 = textObj.value.substring(rangeEnd); 
			
			textObj.value = tempStr1 + textFeildValue + tempStr2; 
			textObj.focus(); 
			
			var len = textFeildValue.length; 
			textObj.setSelectionRange(rangeStart + len,rangeStart + len); 
			textObj.blur(); 
		}else{ 
			textObj.value += textFeildValue; 
		} 
	} 
});
define("emojiSelector", (function (global) {
    return function () {
        var ret, fn;
        return ret || global.emojiSelector;
    };
}(this)));

// Backbone.Marionette, v1.0.0-rc3
// Copyright (c)2013 Derick Bailey, Muted Solutions, LLC.
// Distributed under MIT license
// http://github.com/marionettejs/backbone.marionette


/*!
 * Includes BabySitter
 * https://github.com/marionettejs/backbone.babysitter/
 *
 * Includes Wreqr
 * https://github.com/marionettejs/backbone.wreqr/
 */


// Backbone.BabySitter, v0.0.4
// Copyright (c)2012 Derick Bailey, Muted Solutions, LLC.
// Distributed under MIT license
// http://github.com/marionettejs/backbone.babysitter
// Backbone.ChildViewContainer
// ---------------------------
//
// Provide a container to store, retrieve and
// shut down child views.

Backbone.ChildViewContainer = (function (Backbone, _) {

    // Container Constructor
    // ---------------------

    var Container = function (initialViews) {
        this._views = {};
        this._indexByModel = {};
        this._indexByCollection = {};
        this._indexByCustom = {};
        this._updateLength();

        this._addInitialViews(initialViews);
    };

    // Container Methods
    // -----------------

    _.extend(Container.prototype, {

        // Add a view to this container. Stores the view
        // by `cid` and makes it searchable by the model
        // and/or collection of the view. Optionally specify
        // a custom key to store an retrieve the view.
        add: function (view, customIndex) {
            var viewCid = view.cid;

            // store the view
            this._views[viewCid] = view;

            // index it by model
            if (view.model) {
                this._indexByModel[view.model.cid] = viewCid;
            }

            // index it by collection
            if (view.collection) {
                this._indexByCollection[view.collection.cid] = viewCid;
            }

            // index by custom
            if (customIndex) {
                this._indexByCustom[customIndex] = viewCid;
            }

            this._updateLength();
        },

        // Find a view by the model that was attached to
        // it. Uses the model's `cid` to find it, and
        // retrieves the view by it's `cid` from the result
        findByModel: function (model) {
            var viewCid = this._indexByModel[model.cid];
            return this.findByCid(viewCid);
        },

        // Find a view by the collection that was attached to
        // it. Uses the collection's `cid` to find it, and
        // retrieves the view by it's `cid` from the result
        findByCollection: function (col) {
            var viewCid = this._indexByCollection[col.cid];
            return this.findByCid(viewCid);
        },

        // Find a view by a custom indexer.
        findByCustom: function (index) {
            var viewCid = this._indexByCustom[index];
            return this.findByCid(viewCid);
        },

        // Find by index. This is not guaranteed to be a
        // stable index.
        findByIndex: function (index) {
            return _.values(this._views)[index];
        },

        // retrieve a view by it's `cid` directly
        findByCid: function (cid) {
            return this._views[cid];
        },

        // Remove a view
        remove: function (view) {
            var viewCid = view.cid;

            // delete model index
            if (view.model) {
                delete this._indexByModel[view.model.cid];
            }

            // delete collection index
            if (view.collection) {
                delete this._indexByCollection[view.collection.cid];
            }

            // delete custom index
            var cust;

            for (var key in this._indexByCustom) {
                if (this._indexByCustom.hasOwnProperty(key)) {
                    if (this._indexByCustom[key] === viewCid) {
                        cust = key;
                        break;
                    }
                }
            }

            if (cust) {
                delete this._indexByCustom[cust];
            }

            // remove the view from the container
            delete this._views[viewCid];

            // update the length
            this._updateLength();
        },

        // Call a method on every view in the container,
        // passing parameters to the call method one at a
        // time, like `function.call`.
        call: function (method, args) {
            args = Array.prototype.slice.call(arguments, 1);
            this.apply(method, args);
        },

        // Apply a method on every view in the container,
        // passing parameters to the call method one at a
        // time, like `function.apply`.
        apply: function (method, args) {
            var view;

            // fix for IE < 9
            args = args || [];

            _.each(this._views, function (view, key) {
                if (_.isFunction(view[method])) {
                    view[method].apply(view, args);
                }
            });

        },

        // Update the `.length` attribute on this container
        _updateLength: function () {
            this.length = _.size(this._views);
        },

        // set up an initial list of views
        _addInitialViews: function (views) {
            if (!views) {
                return;
            }

            var view, i,
                length = views.length;

            for (i = 0; i < length; i++) {
                view = views[i];
                this.add(view);
            }
        }
    });

    // Borrowing this code from Backbone.Collection:
    // http://backbonejs.org/docs/backbone.html#section-106
    //
    // Mix in methods from Underscore, for iteration, and other
    // collection related features.
    var methods = ['forEach', 'each', 'map', 'find', 'detect', 'filter',
        'select', 'reject', 'every', 'all', 'some', 'any', 'include',
        'contains', 'invoke', 'toArray', 'first', 'initial', 'rest',
        'last', 'without', 'isEmpty', 'pluck'];

    _.each(methods, function (method) {
        Container.prototype[method] = function () {
            var views = _.values(this._views);
            var args = [views].concat(_.toArray(arguments));
            return _[method].apply(_, args);
        };
    });

    // return the public API
    return Container;
})(Backbone, _);

// Backbone.Wreqr, v0.2.0
// Copyright (c)2012 Derick Bailey, Muted Solutions, LLC.
// Distributed under MIT license
// http://github.com/marionettejs/backbone.wreqr
Backbone.Wreqr = (function (Backbone, Marionette, _) {
    "option strict";
    var Wreqr = {};

    // Handlers
    // --------
    // A registry of functions to call, given a name

    Wreqr.Handlers = (function (Backbone, _) {
        "option strict";

        // Constructor
        // -----------

        var Handlers = function () {
            "use strict";
            this._handlers = {};
        };

        Handlers.extend = Backbone.Model.extend;

        // Instance Members
        // ----------------

        _.extend(Handlers.prototype, {

            // Add a handler for the given name, with an
            // optional context to run the handler within
            addHandler: function (name, handler, context) {
                var config = {
                    callback: handler,
                    context: context
                };

                this._handlers[name] = config;
            },

            // Get the currently registered handler for
            // the specified name. Throws an exception if
            // no handler is found.
            getHandler: function (name) {
                var config = this._handlers[name];

                if (!config) {
                    throw new Error("Handler not found for '" + name + "'");
                }

                return function () {
                    var args = Array.prototype.slice.apply(arguments);
                    return config.callback.apply(config.context, args);
                };
            },

            // Remove a handler for the specified name
            removeHandler: function (name) {
                delete this._handlers[name];
            },

            // Remove all handlers from this registry
            removeAllHandlers: function () {
                this._handlers = {};
            }
        });

        return Handlers;
    })(Backbone, _);

    // Wreqr.Commands
    // --------------
    //
    // A simple command pattern implementation. Register a command
    // handler and execute it.
    Wreqr.Commands = (function (Wreqr) {
        "option strict";

        return Wreqr.Handlers.extend({
            execute: function () {
                var name = arguments[0];
                var args = Array.prototype.slice.call(arguments, 1);

                this.getHandler(name).apply(this, args);
            }
        });

    })(Wreqr);

    // Wreqr.RequestResponse
    // ---------------------
    //
    // A simple request/response implementation. Register a
    // request handler, and return a response from it
    Wreqr.RequestResponse = (function (Wreqr) {
        "option strict";

        return Wreqr.Handlers.extend({
            request: function () {
                var name = arguments[0];
                var args = Array.prototype.slice.call(arguments, 1);

                return this.getHandler(name).apply(this, args);
            }
        });

    })(Wreqr);

    // Event Aggregator
    // ----------------
    // A pub-sub object that can be used to decouple various parts
    // of an application through event-driven architecture.

    Wreqr.EventAggregator = (function (Backbone, _) {

        // Grab a reference to the original listenTo
        var listenTo = Backbone.Events.listenTo;

        // Create a version of listenTo that allows contexting binding
        function contextBoundListenTo(obj, evtSource, events, callback, context) {
            context = context || obj;
            return listenTo.call(obj, evtSource, events, _.bind(callback, context));
        }

        // Define the EventAggregator
        function EventAggregator() {
        }

        // Mix Backbone.Events in to it
        _.extend(EventAggregator.prototype, Backbone.Events, {
            // Override the listenTo so that we can have a version that
            // correctly binds context
            listenTo: function (evtSource, events, callback, context) {
                return contextBoundListenTo(this, evtSource, events, callback, context);
            }
        });

        // Allow it to be extended
        EventAggregator.extend = Backbone.Model.extend;

        return EventAggregator;
    })(Backbone, _);


    return Wreqr;
})(Backbone, Backbone.Marionette, _);

var Marionette = (function (Backbone, _, $) {
    "use strict";

    var Marionette = {};
    Backbone.Marionette = Marionette;

// Helpers
// -------

// For slicing `arguments` in functions
    var slice = Array.prototype.slice;

// Marionette.extend
// -----------------

// Borrow the Backbone `extend` method so we can use it as needed
    Marionette.extend = Backbone.Model.extend;

// Marionette.getOption
// --------------------

// Retrieve an object, function or other value from a target
// object or it's `options`, with `options` taking precedence.
    Marionette.getOption = function (target, optionName) {
        if (!target || !optionName) {
            return;
        }
        var value;

        if (target.options && target.options[optionName]) {
            value = target.options[optionName];
        } else {
            value = target[optionName];
        }

        return value;
    };

// Mairionette.createObject
// ------------------------

// A wrapper / shim for `Object.create`. Uses native `Object.create`
// if available, otherwise shims it in place for Marionette to use.
    Marionette.createObject = (function () {
        var createObject;

        // Define this once, and just replace the .prototype on it as needed,
        // to improve performance in older / less optimized JS engines
        function F() {
        }


        // Check for existing native / shimmed Object.create
        if (typeof Object.create === "function") {

            // found native/shim, so use it
            createObject = Object.create;

        } else {

            // An implementation of the Boodman/Crockford delegation
            // w/ Cornford optimization, as suggested by @unscriptable
            // https://gist.github.com/3959151

            // native/shim not found, so shim it ourself
            createObject = function (o) {

                // set the prototype of the function
                // so we will get `o` as the prototype
                // of the new object instance
                F.prototype = o;

                // create a new object that inherits from
                // the `o` parameter
                var child = new F();

                // clean up just in case o is really large
                F.prototype = null;

                // send it back
                return child;
            };

        }

        return createObject;
    })();

// Trigger an event and a corresponding method name. Examples:
//
// `this.triggerMethod("foo")` will trigger the "foo" event and
// call the "onFoo" method.
//
// `this.triggerMethod("foo:bar") will trigger the "foo:bar" event and
// call the "onFooBar" method.
    Marionette.triggerMethod = function () {
        var args = Array.prototype.slice.apply(arguments);
        var eventName = args[0];
        var segments = eventName.split(":");
        var segment, capLetter, methodName = "on";

        for (var i = 0; i < segments.length; i++) {
            segment = segments[i];
            capLetter = segment.charAt(0).toUpperCase();
            methodName += capLetter + segment.slice(1);
        }

        this.trigger.apply(this, args);

        if (_.isFunction(this[methodName])) {
            args.shift();
            return this[methodName].apply(this, args);
        }
    };

// DOMRefresh
// ----------
//
// Monitor a view's state, and after it has been rendered and shown
// in the DOM, trigger a "dom:refresh" event every time it is
// re-rendered.

    Marionette.MonitorDOMRefresh = (function () {
        // track when the view has been rendered
        function handleShow(view) {
            view._isShown = true;
            triggerDOMRefresh(view);
        }

        // track when the view has been shown in the DOM,
        // using a Marionette.Region (or by other means of triggering "show")
        function handleRender(view) {
            view._isRendered = true;
            triggerDOMRefresh(view);
        }

        // Trigger the "dom:refresh" event and corresponding "onDomRefresh" method
        function triggerDOMRefresh(view) {
            if (view._isShown && view._isRendered) {
                if (_.isFunction(view.triggerMethod)) {
                    view.triggerMethod("dom:refresh");
                }
            }
        }

        // Export public API
        return function (view) {
            view.listenTo(view, "show", function () {
                handleShow(view);
            });

            view.listenTo(view, "render", function () {
                handleRender(view);
            });
        };
    })();


// addEventBinder
// --------------
//
// Mixes in Backbone.Events to the target object, if it is not present
// already. Also adjusts the listenTo method to accept a 4th parameter
// for the callback context.

    (function (Backbone, Marionette, _) {

        // grab a reference to the original listenTo
        var listenTo = Backbone.Events.listenTo;

        // Fix the listenTo method on the target object, allowing the 4th
        // context parameter to be specified
        Marionette.addEventBinder = function (target) {
            // If the target is not already extending Backbone.Events,
            // then extend that on to it first
            if (!target.on && !target.off && !target.listenTo && !target.stopListening) {
                _.extend(target, Backbone.Events);
            }

            // Override the built-in listenTo method to make sure we
            // account for context
            target.listenTo = function (evtSource, events, callback, context) {
                context = context || this;
                return listenTo.call(this, evtSource, events, _.bind(callback, context));
            };
        };

    })(Backbone, Marionette, _);


// Event Aggregator
// ----------------
// A pub-sub object that can be used to decouple various parts
// of an application through event-driven architecture.
//
// Extends [Backbone.Wreqr.EventAggregator](https://github.com/marionettejs/backbone.wreqr)
// and mixes in an EventBinder from [Backbone.EventBinder](https://github.com/marionettejs/backbone.eventbinder).
    Marionette.EventAggregator = Backbone.Wreqr.EventAggregator.extend({

        constructor: function () {
            Marionette.addEventBinder(this);

            var args = Array.prototype.slice.apply(arguments);
            Backbone.Wreqr.EventAggregator.prototype.constructor.apply(this, args);
        }

    });

// Marionette.bindEntityEvents & unbindEntityEvents
// ---------------------------
//
// These methods are used to bind/unbind a backbone "entity" (collection/model)
// to methods on a target object.
//
// The first paremter, `target`, must have a `listenTo` method from the
// EventBinder object.
//
// The second parameter is the entity (Backbone.Model or Backbone.Collection)
// to bind the events from.
//
// The third parameter is a hash of { "event:name": "eventHandler" }
// configuration. Multiple handlers can be separated by a space. A
// function can be supplied instead of a string handler name.

    (function (Marionette) {
        "use strict";

        // Bind the event to handlers specified as a string of
        // handler names on the target object
        function bindFromStrings(target, entity, evt, methods) {
            var methodNames = methods.split(/\s+/);

            _.each(methodNames, function (methodName) {

                var method = target[methodName];
                if (!method) {
                    throw new Error("Method '" + methodName + "' was configured as an event handler, but does not exist.");
                }

                target.listenTo(entity, evt, method, target);
            });
        }

        // Bind the event to a supplied callback function
        function bindToFunction(target, entity, evt, method) {
            target.listenTo(entity, evt, method, target);
        }

        // Bind the event to handlers specified as a string of
        // handler names on the target object
        function unbindFromStrings(target, entity, evt, methods) {
            var methodNames = methods.split(/\s+/);

            _.each(methodNames, function (methodName) {
                var method = target[method];
                target.stopListening(entity, evt, method, target);
            });
        }

        // Bind the event to a supplied callback function
        function unbindToFunction(target, entity, evt, method) {
            target.stopListening(entity, evt, method, target);
        }


        // generic looping function
        function iterateEvents(target, entity, bindings, functionCallback, stringCallback) {
            if (!entity || !bindings) {
                return;
            }

            // allow the bindings to be a function
            if (_.isFunction(bindings)) {
                bindings = bindings.call(target);
            }

            // iterate the bindings and bind them
            _.each(bindings, function (methods, evt) {

                // allow for a function as the handler,
                // or a list of event names as a string
                if (_.isFunction(methods)) {
                    functionCallback(target, entity, evt, methods);
                } else {
                    stringCallback(target, entity, evt, methods);
                }

            });
        }

        // Export Public API
        Marionette.bindEntityEvents = function (target, entity, bindings) {
            iterateEvents(target, entity, bindings, bindToFunction, bindFromStrings);
        };

        Marionette.unbindEntityEvents = function (target, entity, bindings) {
            iterateEvents(target, entity, bindings, unbindToFunction, unbindFromStrings);
        };

    })(Marionette);


// Callbacks
// ---------

// A simple way of managing a collection of callbacks
// and executing them at a later point in time, using jQuery's
// `Deferred` object.
    Marionette.Callbacks = function () {
        this._deferred = $.Deferred();
        this._callbacks = [];
    };

    _.extend(Marionette.Callbacks.prototype, {

        // Add a callback to be executed. Callbacks added here are
        // guaranteed to execute, even if they are added after the
        // `run` method is called.
        add: function (callback, contextOverride) {
            this._callbacks.push({cb: callback, ctx: contextOverride});

            this._deferred.done(function (context, options) {
                if (contextOverride) {
                    context = contextOverride;
                }
                callback.call(context, options);
            });
        },

        // Run all registered callbacks with the context specified.
        // Additional callbacks can be added after this has been run
        // and they will still be executed.
        run: function (options, context) {
            this._deferred.resolve(context, options);
        },

        // Resets the list of callbacks to be run, allowing the same list
        // to be run multiple times - whenever the `run` method is called.
        reset: function () {
            var that = this;
            var callbacks = this._callbacks;
            this._deferred = $.Deferred();
            this._callbacks = [];
            _.each(callbacks, function (cb) {
                that.add(cb.cb, cb.ctx);
            });
        }
    });


// Marionette Controller
// ---------------------
//
// A multi-purpose object to use as a controller for
// modules and routers, and as a mediator for workflow
// and coordination of other objects, views, and more.
    Marionette.Controller = function (options) {
        this.triggerMethod = Marionette.triggerMethod;
        this.options = options || {};

        Marionette.addEventBinder(this);

        if (_.isFunction(this.initialize)) {
            this.initialize(this.options);
        }
    };

    Marionette.Controller.extend = Marionette.extend;

// Controller Methods
// --------------

// Ensure it can trigger events with Backbone.Events
    _.extend(Marionette.Controller.prototype, Backbone.Events, {
        close: function () {
            this.stopListening();
            this.triggerMethod("close");
            this.unbind();
        }
    });

// Region
// ------
//
// Manage the visual regions of your composite application. See
// http://lostechies.com/derickbailey/2011/12/12/composite-js-apps-regions-and-region-managers/

    Marionette.Region = function (options) {
        this.options = options || {};

        Marionette.addEventBinder(this);

        this.el = Marionette.getOption(this, "el");

        if (!this.el) {
            var err = new Error("An 'el' must be specified for a region.");
            err.name = "NoElError";
            throw err;
        }

        if (this.initialize) {
            var args = Array.prototype.slice.apply(arguments);
            this.initialize.apply(this, args);
        }
    };


// Region Type methods
// -------------------

    _.extend(Marionette.Region, {

        // Build an instance of a region by passing in a configuration object
        // and a default region type to use if none is specified in the config.
        //
        // The config object should either be a string as a jQuery DOM selector,
        // a Region type directly, or an object literal that specifies both
        // a selector and regionType:
        //
        // ```js
        // {
        //   selector: "#foo",
        //   regionType: MyCustomRegion
        // }
        // ```
        //
        buildRegion: function (regionConfig, defaultRegionType) {
            var regionIsString = (typeof regionConfig === "string");
            var regionSelectorIsString = (typeof regionConfig.selector === "string");
            var regionTypeIsUndefined = (typeof regionConfig.regionType === "undefined");
            var regionIsType = (typeof regionConfig === "function");

            if (!regionIsType && !regionIsString && !regionSelectorIsString) {
                throw new Error("Region must be specified as a Region type, a selector string or an object with selector property");
            }

            var selector, RegionType;

            // get the selector for the region

            if (regionIsString) {
                selector = regionConfig;
            }

            if (regionConfig.selector) {
                selector = regionConfig.selector;
            }

            // get the type for the region

            if (regionIsType) {
                RegionType = regionConfig;
            }

            if (!regionIsType && regionTypeIsUndefined) {
                RegionType = defaultRegionType;
            }

            if (regionConfig.regionType) {
                RegionType = regionConfig.regionType;
            }

            // build the region instance

            var regionManager = new RegionType({
                el: selector
            });

            return regionManager;
        }

    });

// Region Instance Methods
// -----------------------

    _.extend(Marionette.Region.prototype, Backbone.Events, {

        // Displays a backbone view instance inside of the region.
        // Handles calling the `render` method for you. Reads content
        // directly from the `el` attribute. Also calls an optional
        // `onShow` and `close` method on your view, just after showing
        // or just before closing the view, respectively.
        show: function (view) {

            this.ensureEl();
            this.close();

            view.render();
            this.open(view);

            Marionette.triggerMethod.call(view, "show");
            Marionette.triggerMethod.call(this, "show", view);

            this.currentView = view;
        },

        ensureEl: function () {
            if (!this.$el || this.$el.length === 0) {
                this.$el = this.getEl(this.el);
            }
        },

        // Override this method to change how the region finds the
        // DOM element that it manages. Return a jQuery selector object.
        getEl: function (selector) {
            return $(selector);
        },

        // Override this method to change how the new view is
        // appended to the `$el` that the region is managing
        open: function (view) {
            this.$el.empty().append(view.el);
        },

        // Close the current view, if there is one. If there is no
        // current view, it does nothing and returns immediately.
        close: function () {
            var view = this.currentView;
            if (!view || view.isClosed) {
                return;
            }

            if (view.close) {
                view.close();
            }
            Marionette.triggerMethod.call(this, "close");

            delete this.currentView;
        },

        // Attach an existing view to the region. This
        // will not call `render` or `onShow` for the new view,
        // and will not replace the current HTML for the `el`
        // of the region.
        attachView: function (view) {
            this.currentView = view;
        },

        // Reset the region by closing any existing view and
        // clearing out the cached `$el`. The next time a view
        // is shown via this region, the region will re-query the
        // DOM for the region's `el`.
        reset: function () {
            this.close();
            delete this.$el;
        }
    });

// Copy the `extend` function used by Backbone's classes
    Marionette.Region.extend = Marionette.extend;


// Template Cache
// --------------

// Manage templates stored in `<script>` blocks,
// caching them for faster access.
    Marionette.TemplateCache = function (templateId) {
        this.templateId = templateId;
    };

// TemplateCache object-level methods. Manage the template
// caches from these method calls instead of creating
// your own TemplateCache instances
    _.extend(Marionette.TemplateCache, {
        templateCaches: {},

        // Get the specified template by id. Either
        // retrieves the cached version, or loads it
        // from the DOM.
        get: function (templateId) {
            var that = this;
            var cachedTemplate = this.templateCaches[templateId];

            if (!cachedTemplate) {
                cachedTemplate = new Marionette.TemplateCache(templateId);
                this.templateCaches[templateId] = cachedTemplate;
            }

            return cachedTemplate.load();
        },

        // Clear templates from the cache. If no arguments
        // are specified, clears all templates:
        // `clear()`
        //
        // If arguments are specified, clears each of the
        // specified templates from the cache:
        // `clear("#t1", "#t2", "...")`
        clear: function () {
            var i;
            var args = Array.prototype.slice.apply(arguments);
            var length = args.length;

            if (length > 0) {
                for (i = 0; i < length; i++) {
                    delete this.templateCaches[args[i]];
                }
            } else {
                this.templateCaches = {};
            }
        }
    });

// TemplateCache instance methods, allowing each
// template cache object to manage it's own state
// and know whether or not it has been loaded
    _.extend(Marionette.TemplateCache.prototype, {

        // Internal method to load the template asynchronously.
        load: function () {
            var that = this;

            // Guard clause to prevent loading this template more than once
            if (this.compiledTemplate) {
                return this.compiledTemplate;
            }

            // Load the template and compile it
            var template = this.loadTemplate(this.templateId);
            this.compiledTemplate = this.compileTemplate(template);

            return this.compiledTemplate;
        },

        // Load a template from the DOM, by default. Override
        // this method to provide your own template retrieval,
        // such as asynchronous loading from a server.
        loadTemplate: function (templateId) {
            var template = $(templateId).html();

            if (!template || template.length === 0) {
                var msg = "Could not find template: '" + templateId + "'";
                var err = new Error(msg);
                err.name = "NoTemplateError";
                throw err;
            }

            return template;
        },

        // Pre-compile the template before caching it. Override
        // this method if you do not need to pre-compile a template
        // (JST / RequireJS for example) or if you want to change
        // the template engine used (Handebars, etc).
        compileTemplate: function (rawTemplate) {
            return _.template(rawTemplate);
        }
    });


// Renderer
// --------

// Render a template with data by passing in the template
// selector and the data to render.
    Marionette.Renderer = {

        // Render a template with data. The `template` parameter is
        // passed to the `TemplateCache` object to retrieve the
        // template function. Override this method to provide your own
        // custom rendering and template handling for all of Marionette.
        render: function (template, data) {
            var templateFunc = typeof template === 'function' ? template : Marionette.TemplateCache.get(template);
            var html = templateFunc(data);
            return html;
        }
    };


// Marionette.View
// ---------------

// The core view type that other Marionette views extend from.
    Marionette.View = Backbone.View.extend({

        constructor: function () {
            _.bindAll(this, "render");
            Marionette.addEventBinder(this);

            var args = Array.prototype.slice.apply(arguments);
            Backbone.View.prototype.constructor.apply(this, args);

            Marionette.MonitorDOMRefresh(this);
            this.listenTo(this, "show", this.onShowCalled, this);
        },

        // import the "triggerMethod" to trigger events with corresponding
        // methods if the method exists
        triggerMethod: Marionette.triggerMethod,

        // Get the template for this view
        // instance. You can set a `template` attribute in the view
        // definition or pass a `template: "whatever"` parameter in
        // to the constructor options.
        getTemplate: function () {
            return Marionette.getOption(this, "template");
        },

        // Mix in template helper methods. Looks for a
        // `templateHelpers` attribute, which can either be an
        // object literal, or a function that returns an object
        // literal. All methods and attributes from this object
        // are copies to the object passed in.
        mixinTemplateHelpers: function (target) {
            target = target || {};
            var templateHelpers = this.templateHelpers;
            if (_.isFunction(templateHelpers)) {
                templateHelpers = templateHelpers.call(this);
            }
            return _.extend(target, templateHelpers);
        },

        // Configure `triggers` to forward DOM events to view
        // events. `triggers: {"click .foo": "do:foo"}`
        configureTriggers: function () {
            if (!this.triggers) {
                return;
            }

            var that = this;
            var triggerEvents = {};

            // Allow `triggers` to be configured as a function
            var triggers = _.result(this, "triggers");

            // Configure the triggers, prevent default
            // action and stop propagation of DOM events
            _.each(triggers, function (value, key) {

                // build the event handler function for the DOM event
                triggerEvents[key] = function (e) {

                    // stop the event in it's tracks
                    if (e && e.preventDefault) {
                        e.preventDefault();
                    }
                    if (e && e.stopPropagation) {
                        e.stopPropagation();
                    }

                    // buil the args for the event
                    var args = {
                        view: this,
                        model: this.model,
                        collection: this.collection
                    };

                    // trigger the event
                    that.trigger(value, args);
                };

            });

            return triggerEvents;
        },

        // Overriding Backbone.View's delegateEvents to handle
        // the `triggers`, `modelEvents`, and `collectionEvents` configuration
        delegateEvents: function (events) {
            this._delegateDOMEvents(events);
            Marionette.bindEntityEvents(this, this.model, Marionette.getOption(this, "modelEvents"));
            Marionette.bindEntityEvents(this, this.collection, Marionette.getOption(this, "collectionEvents"));
        },

        // internal method to delegate DOM events and triggers
        _delegateDOMEvents: function (events) {
            events = events || this.events;
            if (_.isFunction(events)) {
                events = events.call(this);
            }

            var combinedEvents = {};
            var triggers = this.configureTriggers();
            _.extend(combinedEvents, events, triggers);

            Backbone.View.prototype.delegateEvents.call(this, combinedEvents);
        },

        // Overriding Backbone.View's undelegateEvents to handle unbinding
        // the `triggers`, `modelEvents`, and `collectionEvents` config
        undelegateEvents: function () {
            var args = Array.prototype.slice.call(arguments);
            Backbone.View.prototype.undelegateEvents.apply(this, args);

            Marionette.unbindEntityEvents(this, this.model, Marionette.getOption(this, "modelEvents"));
            Marionette.unbindEntityEvents(this, this.collection, Marionette.getOption(this, "collectionEvents"));
        },

        // Internal method, handles the `show` event.
        onShowCalled: function () {
        },

        // Default `close` implementation, for removing a view from the
        // DOM and unbinding it. Regions will call this method
        // for you. You can specify an `onClose` method in your view to
        // add custom code that is called after the view is closed.
        close: function () {
            if (this.isClosed) {
                return;
            }

            // allow the close to be stopped by returning `false`
            // from the `onBeforeClose` method
            var shouldClose = this.triggerMethod("before:close");
            if (shouldClose === false) {
                return;
            }

            // mark as closed before doing the actual close, to
            // prevent infinite loops within "close" event handlers
            // that are trying to close other views
            this.isClosed = true;
            this.triggerMethod("close");

            this.remove();
        },

        // This method binds the elements specified in the "ui" hash inside the view's code with
        // the associated jQuery selectors.
        bindUIElements: function () {
            if (!this.ui) {
                return;
            }

            var that = this;

            if (!this.uiBindings) {
                // We want to store the ui hash in uiBindings, since afterwards the values in the ui hash
                // will be overridden with jQuery selectors.
                this.uiBindings = _.result(this, "ui");
            }

            // refreshing the associated selectors since they should point to the newly rendered elements.
            this.ui = {};
            _.each(_.keys(this.uiBindings), function (key) {
                var selector = that.uiBindings[key];
                that.ui[key] = that.$(selector);
            });
        }
    });

// Item View
// ---------

// A single item view implementation that contains code for rendering
// with underscore.js templates, serializing the view's model or collection,
// and calling several methods on extended views, such as `onRender`.
    Marionette.ItemView = Marionette.View.extend({
        constructor: function () {
            var args = Array.prototype.slice.apply(arguments);
            Marionette.View.prototype.constructor.apply(this, args);
        },

        // Serialize the model or collection for the view. If a model is
        // found, `.toJSON()` is called. If a collection is found, `.toJSON()`
        // is also called, but is used to populate an `items` array in the
        // resulting data. If both are found, defaults to the model.
        // You can override the `serializeData` method in your own view
        // definition, to provide custom serialization for your view's data.
        serializeData: function () {
            var data = {};

            if (this.model) {
                data = this.model.toJSON();
            }
            else if (this.collection) {
                data = { items: this.collection.toJSON() };
            }

            return data;
        },

        // Render the view, defaulting to underscore.js templates.
        // You can override this in your view definition to provide
        // a very specific rendering for your view. In general, though,
        // you should override the `Marionette.Renderer` object to
        // change how Marionette renders views.
        render: function () {
            this.isClosed = false;

            this.triggerMethod("before:render", this);
            this.triggerMethod("item:before:render", this);

            var data = this.serializeData();
            data = this.mixinTemplateHelpers(data);

            var template = this.getTemplate();
            var html = Marionette.Renderer.render(template, data);
            this.$el.html(html);
            this.bindUIElements();

            this.triggerMethod("render", this);
            this.triggerMethod("item:rendered", this);

            return this;
        },

        // Override the default close event to add a few
        // more events that are triggered.
        close: function () {
            if (this.isClosed) {
                return;
            }

            this.triggerMethod('item:before:close');

            var args = Array.prototype.slice.apply(arguments);
            Marionette.View.prototype.close.apply(this, args);

            this.triggerMethod('item:closed');
        }
    });

// Collection View
// ---------------

// A view that iterates over a Backbone.Collection
// and renders an individual ItemView for each model.
    Marionette.CollectionView = Marionette.View.extend({
        // used as the prefix for item view events
        // that are forwarded through the collectionview
        itemViewEventPrefix: "itemview",

        // constructor
        constructor: function (options) {
            this._initChildViewStorage();

            var args = Array.prototype.slice.apply(arguments);
            Marionette.View.prototype.constructor.apply(this, args);

            this._initialEvents();
        },

        // Configured the initial events that the collection view
        // binds to. Override this method to prevent the initial
        // events, or to add your own initial events.
        _initialEvents: function () {
            if (this.collection) {
                this.listenTo(this.collection, "add", this.addChildView, this);
                this.listenTo(this.collection, "remove", this.removeItemView, this);
                this.listenTo(this.collection, "reset", this.render, this);
            }
        },

        // Handle a child item added to the collection
        addChildView: function (item, collection, options) {
            this.closeEmptyView();
            var ItemView = this.getItemView(item);
            var index = this.collection.indexOf(item);
            this.addItemView(item, ItemView, index);
        },

        // Override from `Marionette.View` to guarantee the `onShow` method
        // of child views is called.
        onShowCalled: function () {
            this.children.each(function (child) {
                Marionette.triggerMethod.call(child, "show");
            });
        },

        // Internal method to trigger the before render callbacks
        // and events
        triggerBeforeRender: function () {
            this.triggerMethod("before:render", this);
            this.triggerMethod("collection:before:render", this);
        },

        // Internal method to trigger the rendered callbacks and
        // events
        triggerRendered: function () {
            this.triggerMethod("render", this);
            this.triggerMethod("collection:rendered", this);
        },

        // Render the collection of items. Override this method to
        // provide your own implementation of a render function for
        // the collection view.
        render: function () {
            this.isClosed = false;

            this.triggerBeforeRender();
            this.closeEmptyView();
            this.closeChildren();

            if (this.collection && this.collection.length > 0) {
                this.showCollection();
            } else {
                this.showEmptyView();
            }

            this.triggerRendered();
            return this;
        },

        // Internal method to loop through each item in the
        // collection view and show it
        showCollection: function () {
            var that = this;
            var ItemView;
            this.collection.each(function (item, index) {
                ItemView = that.getItemView(item);
                that.addItemView(item, ItemView, index);
            });
        },

        // Internal method to show an empty view in place of
        // a collection of item views, when the collection is
        // empty
        showEmptyView: function () {
            var EmptyView = Marionette.getOption(this, "emptyView");

            if (EmptyView && !this._showingEmptyView) {
                this._showingEmptyView = true;
                var model = new Backbone.Model();
                this.addItemView(model, EmptyView, 0);
            }
        },

        // Internal method to close an existing emptyView instance
        // if one exists. Called when a collection view has been
        // rendered empty, and then an item is added to the collection.
        closeEmptyView: function () {
            if (this._showingEmptyView) {
                this.closeChildren();
                delete this._showingEmptyView;
            }
        },

        // Retrieve the itemView type, either from `this.options.itemView`
        // or from the `itemView` in the object definition. The "options"
        // takes precedence.
        getItemView: function (item) {
            var itemView = Marionette.getOption(this, "itemView");

            if (!itemView) {
                var err = new Error("An `itemView` must be specified");
                err.name = "NoItemViewError";
                throw err;
            }

            return itemView;
        },

        // Render the child item's view and add it to the
        // HTML for the collection view.
        addItemView: function (item, ItemView, index) {
            var that = this;

            // get the itemViewOptions if any were specified
            var itemViewOptions = Marionette.getOption(this, "itemViewOptions");
            if (_.isFunction(itemViewOptions)) {
                itemViewOptions = itemViewOptions.call(this, item);
            }

            // build the view
            var view = this.buildItemView(item, ItemView, itemViewOptions);

            // set up the child view event forwarding
            this.addChildViewEventForwarding(view);

            // this view is about to be added
            this.triggerMethod("before:item:added", view);

            // Store the child view itself so we can properly
            // remove and/or close it later
            this.children.add(view);

            // call the "show" method if the collection view
            // has already been shown
            if (this._isShown) {
                Marionette.triggerMethod.call(view, "show");
            }

            // Render it and show it
            var renderResult = this.renderItemView(view, index);

            // this view was added
            this.triggerMethod("after:item:added", view);
        },

        // Set up the child view event forwarding. Uses an "itemview:"
        // prefix in front of all forwarded events.
        addChildViewEventForwarding: function (view) {
            var prefix = Marionette.getOption(this, "itemViewEventPrefix");

            // Forward all child item view events through the parent,
            // prepending "itemview:" to the event name
            this.listenTo(view, "all", function () {
                var args = slice.call(arguments);
                args[0] = prefix + ":" + args[0];
                args.splice(1, 0, view);

                Marionette.triggerMethod.apply(this, args);
            }, this);
        },

        // render the item view
        renderItemView: function (view, index) {
            view.render();
            this.appendHtml(this, view, index);
        },

        // Build an `itemView` for every model in the collection.
        buildItemView: function (item, ItemViewType, itemViewOptions) {
            var options = _.extend({model: item}, itemViewOptions);
            var view = new ItemViewType(options);
            return view;
        },

        // get the child view by item it holds, and remove it
        removeItemView: function (item) {
            var view = this.children.findByModel(item);
            this.removeChildView(view);
        },

        // Remove the child view and close it
        removeChildView: function (view) {

            // shut down the child view properly,
            // including events that the collection has from it
            if (view) {
                this.stopListening(view);

                if (view.close) {
                    view.close();
                }

                this.children.remove(view);
            }

            // check if we're empty now, and if we are, show the
            // empty view
            if (!this.collection || this.collection.length === 0) {
                this.showEmptyView();
            }

            this.triggerMethod("item:removed", view);
        },

        // Append the HTML to the collection's `el`.
        // Override this method to do something other
        // then `.append`.
        appendHtml: function (collectionView, itemView, index) {
            collectionView.$el.append(itemView.el);
        },

        // Internal method to set up the `children` object for
        // storing all of the child views
        _initChildViewStorage: function () {
            this.children = new Backbone.ChildViewContainer();
        },

        // Handle cleanup and other closing needs for
        // the collection of views.
        close: function () {
            if (this.isClosed) {
                return;
            }

            this.triggerMethod("collection:before:close");
            this.closeChildren();
            this.triggerMethod("collection:closed");

            var args = Array.prototype.slice.apply(arguments);
            Marionette.View.prototype.close.apply(this, args);
        },

        // Close the child views that this collection view
        // is holding on to, if any
        closeChildren: function () {
            this.children.each(function (child) {
                this.removeChildView(child);
            }, this);

            // re-initialize to clean up after ourselves
            this._initChildViewStorage();
        }
    });


// Composite View
// --------------

// Used for rendering a branch-leaf, hierarchical structure.
// Extends directly from CollectionView and also renders an
// an item view as `modelView`, for the top leaf
    Marionette.CompositeView = Marionette.CollectionView.extend({
        constructor: function (options) {
            var args = Array.prototype.slice.apply(arguments);
            Marionette.CollectionView.apply(this, args);

            this.itemView = this.getItemView();
        },

        // Configured the initial events that the composite view
        // binds to. Override this method to prevent the initial
        // events, or to add your own initial events.
        _initialEvents: function () {
            if (this.collection) {
                this.listenTo(this.collection, "add", this.addChildView, this);
                this.listenTo(this.collection, "remove", this.removeItemView, this);
                this.listenTo(this.collection, "reset", this.renderCollection, this);
            }
        },

        // Retrieve the `itemView` to be used when rendering each of
        // the items in the collection. The default is to return
        // `this.itemView` or Marionette.CompositeView if no `itemView`
        // has been defined
        getItemView: function (item) {
            var itemView = Marionette.getOption(this, "itemView") || this.constructor;

            if (!itemView) {
                var err = new Error("An `itemView` must be specified");
                err.name = "NoItemViewError";
                throw err;
            }

            return itemView;
        },

        // Serialize the collection for the view.
        // You can override the `serializeData` method in your own view
        // definition, to provide custom serialization for your view's data.
        serializeData: function () {
            var data = {};

            if (this.model) {
                data = this.model.toJSON();
            }

            return data;
        },

        // Renders the model once, and the collection once. Calling
        // this again will tell the model's view to re-render itself
        // but the collection will not re-render.
        render: function () {
            this.isClosed = false;

            this.resetItemViewContainer();

            var html = this.renderModel();
            this.$el.html(html);

            // the ui bindings is done here and not at the end of render since they
            // will not be available until after the model is rendered, but should be
            // available before the collection is rendered.
            this.bindUIElements();

            this.triggerMethod("composite:model:rendered");

            this.renderCollection();
            this.triggerMethod("composite:rendered");
            return this;
        },

        // Render the collection for the composite view
        renderCollection: function () {
            var args = Array.prototype.slice.apply(arguments);
            Marionette.CollectionView.prototype.render.apply(this, args);

            this.triggerMethod("composite:collection:rendered");
        },

        // Render an individual model, if we have one, as
        // part of a composite view (branch / leaf). For example:
        // a treeview.
        renderModel: function () {
            var data = {};
            data = this.serializeData();
            data = this.mixinTemplateHelpers(data);

            var template = this.getTemplate();
            return Marionette.Renderer.render(template, data);
        },

        // Appends the `el` of itemView instances to the specified
        // `itemViewContainer` (a jQuery selector). Override this method to
        // provide custom logic of how the child item view instances have their
        // HTML appended to the composite view instance.
        appendHtml: function (cv, iv) {
            var $container = this.getItemViewContainer(cv);
            $container.append(iv.el);
        },

        // Internal method to ensure an `$itemViewContainer` exists, for the
        // `appendHtml` method to use.
        getItemViewContainer: function (containerView) {
            if ("$itemViewContainer" in containerView) {
                return containerView.$itemViewContainer;
            }

            var container;
            if (containerView.itemViewContainer) {

                var selector = _.result(containerView, "itemViewContainer");
                container = containerView.$(selector);
                if (container.length <= 0) {
                    var err = new Error("The specified `itemViewContainer` was not found: " + containerView.itemViewContainer);
                    err.name = "ItemViewContainerMissingError";
                    throw err;
                }

            } else {
                container = containerView.$el;
            }

            containerView.$itemViewContainer = container;
            return container;
        },

        // Internal method to reset the `$itemViewContainer` on render
        resetItemViewContainer: function () {
            if (this.$itemViewContainer) {
                delete this.$itemViewContainer;
            }
        }
    });


// Layout
// ------

// Used for managing application layouts, nested layouts and
// multiple regions within an application or sub-application.
//
// A specialized view type that renders an area of HTML and then
// attaches `Region` instances to the specified `regions`.
// Used for composite view management and sub-application areas.
    Marionette.Layout = Marionette.ItemView.extend({
        regionType: Marionette.Region,

        // Ensure the regions are avialable when the `initialize` method
        // is called.
        constructor: function () {
            this._firstRender = true;
            this.initializeRegions();

            var args = Array.prototype.slice.apply(arguments);
            Marionette.ItemView.apply(this, args);
        },

        // Layout's render will use the existing region objects the
        // first time it is called. Subsequent calls will close the
        // views that the regions are showing and then reset the `el`
        // for the regions to the newly rendered DOM elements.
        render: function () {

            if (this._firstRender) {
                // if this is the first render, don't do anything to
                // reset the regions
                this._firstRender = false;
            } else {
                // If this is not the first render call, then we need to
                // re-initializing the `el` for each region
                this.closeRegions();
                this.reInitializeRegions();
            }

            var args = Array.prototype.slice.apply(arguments);
            var result = Marionette.ItemView.prototype.render.apply(this, args);

            return result;
        },

        // Handle closing regions, and then close the view itself.
        close: function () {
            if (this.isClosed) {
                return;
            }

            this.closeRegions();
            this.destroyRegions();

            var args = Array.prototype.slice.apply(arguments);
            Marionette.ItemView.prototype.close.apply(this, args);
        },

        // Initialize the regions that have been defined in a
        // `regions` attribute on this layout. The key of the
        // hash becomes an attribute on the layout object directly.
        // For example: `regions: { menu: ".menu-container" }`
        // will product a `layout.menu` object which is a region
        // that controls the `.menu-container` DOM element.
        initializeRegions: function () {
            if (!this.regionManagers) {
                this.regionManagers = {};
            }

            var that = this;
            var regions = this.regions || {};
            _.each(regions, function (region, name) {

                var regionManager = Marionette.Region.buildRegion(region, that.regionType);
                regionManager.getEl = function (selector) {
                    return that.$(selector);
                };

                that.regionManagers[name] = regionManager;
                that[name] = regionManager;
            });

        },

        // Re-initialize all of the regions by updating the `el` that
        // they point to
        reInitializeRegions: function () {
            if (this.regionManagers && _.size(this.regionManagers) === 0) {
                this.initializeRegions();
            } else {
                _.each(this.regionManagers, function (region) {
                    region.reset();
                });
            }
        },

        // Close all of the regions that have been opened by
        // this layout. This method is called when the layout
        // itself is closed.
        closeRegions: function () {
            var that = this;
            _.each(this.regionManagers, function (manager, name) {
                manager.close();
            });
        },

        // Destroys all of the regions by removing references
        // from the Layout
        destroyRegions: function () {
            var that = this;
            _.each(this.regionManagers, function (manager, name) {
                delete that[name];
            });
            this.regionManagers = {};
        }
    });


// AppRouter
// ---------

// Reduce the boilerplate code of handling route events
// and then calling a single method on another object.
// Have your routers configured to call the method on
// your object, directly.
//
// Configure an AppRouter with `appRoutes`.
//
// App routers can only take one `controller` object.
// It is recommended that you divide your controller
// objects in to smaller peices of related functionality
// and have multiple routers / controllers, instead of
// just one giant router and controller.
//
// You can also add standard routes to an AppRouter.

    Marionette.AppRouter = Backbone.Router.extend({

        constructor: function (options) {
            var args = Array.prototype.slice.apply(arguments);
            Backbone.Router.prototype.constructor.apply(this, args);

            this.options = options;

            if (this.appRoutes) {
                var controller = Marionette.getOption(this, "controller");
                this.processAppRoutes(controller, this.appRoutes);
            }
        },

        // Internal method to process the `appRoutes` for the
        // router, and turn them in to routes that trigger the
        // specified method on the specified `controller`.
        processAppRoutes: function (controller, appRoutes) {
            var method, methodName;
            var route, routesLength, i;
            var routes = [];
            var router = this;

            for (route in appRoutes) {
                if (appRoutes.hasOwnProperty(route)) {
                    routes.unshift([route, appRoutes[route]]);
                }
            }

            routesLength = routes.length;
            for (i = 0; i < routesLength; i++) {
                route = routes[i][0];
                methodName = routes[i][1];
                method = controller[methodName];

                if (!method) {
                    var msg = "Method '" + methodName + "' was not found on the controller";
                    var err = new Error(msg);
                    err.name = "NoMethodError";
                    throw err;
                }

                method = _.bind(method, controller);
                router.route(route, methodName, method);
            }
        }
    });


// Application
// -----------

// Contain and manage the composite application as a whole.
// Stores and starts up `Region` objects, includes an
// event aggregator as `app.vent`
    Marionette.Application = function (options) {
        this.initCallbacks = new Marionette.Callbacks();
        this.vent = new Marionette.EventAggregator();
        this.commands = new Backbone.Wreqr.Commands();
        this.reqres = new Backbone.Wreqr.RequestResponse();
        this.submodules = {};

        _.extend(this, options);

        Marionette.addEventBinder(this);
        this.triggerMethod = Marionette.triggerMethod;
    };

    _.extend(Marionette.Application.prototype, Backbone.Events, {
        // Command execution, facilitated by Backbone.Wreqr.Commands
        execute: function () {
            var args = Array.prototype.slice.apply(arguments);
            this.commands.execute.apply(this.commands, args);
        },

        // Request/response, facilitated by Backbone.Wreqr.RequestResponse
        request: function () {
            var args = Array.prototype.slice.apply(arguments);
            return this.reqres.request.apply(this.reqres, args);
        },

        // Add an initializer that is either run at when the `start`
        // method is called, or run immediately if added after `start`
        // has already been called.
        addInitializer: function (initializer) {
            this.initCallbacks.add(initializer);
        },

        // kick off all of the application's processes.
        // initializes all of the regions that have been added
        // to the app, and runs all of the initializer functions
        start: function (options) {
            this.triggerMethod("initialize:before", options);
            this.initCallbacks.run(options, this);
            this.triggerMethod("initialize:after", options);

            this.triggerMethod("start", options);
        },

        // Add regions to your app.
        // Accepts a hash of named strings or Region objects
        // addRegions({something: "#someRegion"})
        // addRegions{{something: Region.extend({el: "#someRegion"}) });
        addRegions: function (regions) {
            var that = this;
            _.each(regions, function (region, name) {
                var regionManager = Marionette.Region.buildRegion(region, Marionette.Region);
                that[name] = regionManager;
            });
        },

        // Removes a region from your app.
        // Accepts the regions name
        // removeRegion('myRegion')
        removeRegion: function (region) {
            this[region].close();
            delete this[region];
        },

        // Create a module, attached to the application
        module: function (moduleNames, moduleDefinition) {
            // slice the args, and add this application object as the
            // first argument of the array
            var args = slice.call(arguments);
            args.unshift(this);

            // see the Marionette.Module object for more information
            return Marionette.Module.create.apply(Marionette.Module, args);
        }
    });

// Copy the `extend` function used by Backbone's classes
    Marionette.Application.extend = Marionette.extend;

// Module
// ------

// A simple module system, used to create privacy and encapsulation in
// Marionette applications
    Marionette.Module = function (moduleName, app) {
        this.moduleName = moduleName;

        // store sub-modules
        this.submodules = {};

        this._setupInitializersAndFinalizers();

        // store the configuration for this module
        this.app = app;
        this.startWithParent = true;

        // extend this module with an event binder
        Marionette.addEventBinder(this);
        this.triggerMethod = Marionette.triggerMethod;
    };

// Extend the Module prototype with events / listenTo, so that the module
// can be used as an event aggregator or pub/sub.
    _.extend(Marionette.Module.prototype, Backbone.Events, {

        // Initializer for a specific module. Initializers are run when the
        // module's `start` method is called.
        addInitializer: function (callback) {
            this._initializerCallbacks.add(callback);
        },

        // Finalizers are run when a module is stopped. They are used to teardown
        // and finalize any variables, references, events and other code that the
        // module had set up.
        addFinalizer: function (callback) {
            this._finalizerCallbacks.add(callback);
        },

        // Start the module, and run all of it's initializers
        start: function (options) {
            // Prevent re-starting a module that is already started
            if (this._isInitialized) {
                return;
            }

            // start the sub-modules (depth-first hierarchy)
            _.each(this.submodules, function (mod) {
                // check to see if we should start the sub-module with this parent
                var startWithParent = true;
                startWithParent = mod.startWithParent;

                // start the sub-module
                if (startWithParent) {
                    mod.start(options);
                }
            });

            // run the callbacks to "start" the current module
            this.triggerMethod("before:start", options);

            this._initializerCallbacks.run(options, this);
            this._isInitialized = true;

            this.triggerMethod("start", options);
        },

        // Stop this module by running its finalizers and then stop all of
        // the sub-modules for this module
        stop: function () {
            // if we are not initialized, don't bother finalizing
            if (!this._isInitialized) {
                return;
            }
            this._isInitialized = false;

            Marionette.triggerMethod.call(this, "before:stop");

            // stop the sub-modules; depth-first, to make sure the
            // sub-modules are stopped / finalized before parents
            _.each(this.submodules, function (mod) {
                mod.stop();
            });

            // run the finalizers
            this._finalizerCallbacks.run();

            // reset the initializers and finalizers
            this._initializerCallbacks.reset();
            this._finalizerCallbacks.reset();

            Marionette.triggerMethod.call(this, "stop");
        },

        // Configure the module with a definition function and any custom args
        // that are to be passed in to the definition function
        addDefinition: function (moduleDefinition, customArgs) {
            this._runModuleDefinition(moduleDefinition, customArgs);
        },

        // Internal method: run the module definition function with the correct
        // arguments
        _runModuleDefinition: function (definition, customArgs) {
            if (!definition) {
                return;
            }

            // build the correct list of arguments for the module definition
            var args = _.flatten([
                this,
                this.app,
                Backbone,
                Marionette,
                $, _,
                customArgs
            ]);

            definition.apply(this, args);
        },

        // Internal method: set up new copies of initializers and finalizers.
        // Calling this method will wipe out all existing initializers and
        // finalizers.
        _setupInitializersAndFinalizers: function () {
            this._initializerCallbacks = new Marionette.Callbacks();
            this._finalizerCallbacks = new Marionette.Callbacks();
        }
    });

// Type methods to create modules
    _.extend(Marionette.Module, {

        // Create a module, hanging off the app parameter as the parent object.
        create: function (app, moduleNames, moduleDefinition) {
            var that = this;
            var module = app;

            // get the custom args passed in after the module definition and
            // get rid of the module name and definition function
            var customArgs = slice.apply(arguments);
            customArgs.splice(0, 3);

            // split the module names and get the length
            moduleNames = moduleNames.split(".");
            var length = moduleNames.length;

            // store the module definition for the last module in the chain
            var moduleDefinitions = [];
            moduleDefinitions[length - 1] = moduleDefinition;

            // Loop through all the parts of the module definition
            _.each(moduleNames, function (moduleName, i) {
                var parentModule = module;
                module = that._getModule(parentModule, moduleName, app);
                that._addModuleDefinition(parentModule, module, moduleDefinitions[i], customArgs);
            });

            // Return the last module in the definition chain
            return module;
        },

        _getModule: function (parentModule, moduleName, app, def, args) {
            // Get an existing module of this name if we have one
            var module = parentModule[moduleName];

            if (!module) {
                // Create a new module if we don't have one
                module = new Marionette.Module(moduleName, app);
                parentModule[moduleName] = module;
                // store the module on the parent
                parentModule.submodules[moduleName] = module;
            }

            return module;
        },

        _addModuleDefinition: function (parentModule, module, def, args) {
            var fn;
            var startWithParent;

            if (_.isFunction(def)) {
                // if a function is supplied for the module definition
                fn = def;
                startWithParent = true;

            } else if (_.isObject(def)) {
                // if an object is supplied
                fn = def.define;
                startWithParent = def.startWithParent;

            } else {
                // if nothing is supplied
                startWithParent = true;
            }

            // add module definition if needed
            if (fn) {
                module.addDefinition(fn, args);
            }

            // `and` the two together, ensuring a single `false` will prevent it
            // from starting with the parent
            var tmp = module.startWithParent;
            module.startWithParent = module.startWithParent && startWithParent;

            // setup auto-start if needed
            if (module.startWithParent && !module.startWithParentIsConfigured) {

                // only configure this once
                module.startWithParentIsConfigured = true;

                // add the module initializer config
                parentModule.addInitializer(function (options) {
                    if (module.startWithParent) {
                        module.start(options);
                    }
                });

            }

        }
    });


    return Marionette;
})(Backbone, _, $ || window.jQuery || window.Zepto || window.ender);

define("marionette", ["jquery","underscore","backbone","emojiSelector"], (function (global) {
    return function () {
        var ret, fn;
        return ret || global.Marionette;
    };
}(this)));

define('app/models/Base',['backbone'], function(Backbone) {
    return Backbone.Model.extend({
        defaults: {
        },
        data: {
        },
        initialize: function(data){
            if(data) for(var i in this.defaults) {
                this.set(i, data[i]); 
            }
            this.construct(data);
        },
        construct: function(data) {
            
        },
        parse: parse 
    });

}); 

define('app/models/Ask',['app/models/Base'], function(Model) {
    return Model.extend({
        url: '/ask/',
        defaults: {
            id: 0,
            ask_id: 0,
            uped: false,
            type: 1,
            up_count: 0,
            comment_count: 0,
            click_count: 0,
            inform_count: 0,
            upload_id: 0,
            share_count: 0,
            weixin_share_count: 0,
            reply_count: 0,
            collected: false,
            desc: '',
            image_url: '',
            image_width: '',
            image_height: '',
            image_ratio: '',
            ask_uploads: [],
            avatar: '',
            uid: '',
            username: '',
            nickname: '',
            create_time: '',
            comments: [],
            replies: []
        }
    });

}); 

define('app/collections/Base',['backbone', 'underscore'], function(Backbone, _) {
    return Backbone.Collection.extend({
        data: {
            page: 0,
            size: 10
        },
		initialize: function() {
            this.data = {
                page: 0,
                size: 15
            }
        },
        post: function(callback) {
            var self = this;
            $.post(self.url, self.data, function(data) {
                var data = self.parse(data);

                self.trigger('change');
                callback && callback(data);
            });
        },
        parse: parse,
        plock: false,
        lock: function() { 
            if(this.plock != this._listenerId) {
                this.plock = this._listenerId;
                return false;
            }
            else {
                return true;
            }
        },
        unlock: function(data) {
            //if(data.length > 0 && this.plock == data._listenerId)
            this.plock = false;
        },
        fetch: function(options) {
			var self = this;
            if(self.lock()) return true;

            options = options ? _.clone(options) : {};

			// add search filter
			if(self.page)
            	self.data.page = self.page;
			else 
				self.data.page ++;
			options.data = self.data;
            if (options.parse === void 0) options.parse = true;
            var success = options.success;
            options.success = function (collection, resp, options) {
                var method = options.update ? 'update' : 'reset';
                collection[method](resp, options);
                if (success) success(collection, resp, options);

            };
            return this.sync('read', this, options);
        },
		loading: function(callback) {
            var self = this;
            this.fetch({
            	success: function(data) {
                    //add search callback
                    self.unlock(data);
                    self.trigger('change');
                    callback && callback(data);
                }
            });
        },
		paging: function(page, callback) {
			this.fetch({
				page: page,
				callback: callback
			});
		}
     });
}); 

define('app/collections/Asks',['app/collections/Base', 'app/models/Ask'], function(Collection, Ask) {
    return Collection.extend({
        model: Ask,
        url: '/asks'
     });
}); 

define('app/models/Banner',['app/models/Base'], function(Model) {
    return Model.extend({
        url: '/banners/',
        defaults: {
            id: "",
            uid: "",
            small_pic: "http://7u2spr.com1.z0.glb.clouddn.com/20151102-16242256371db69a84e.ico",
            large_pic: "http://7u2spr.com1.z0.glb.clouddn.com/20151102-16241856371db2a4b37.ico",
            url: "",
            pc_url: "",
            desc: "",
            create_time: "",
            update_time: ""
        }
    });

}); 

define('app/collections/Banners',['app/collections/Base', 'app/models/Banner'], function(Collection, Banner) {
    return Collection.extend({
        model: Banner,
        url: '/banners',
        initialize: function() {
            this.data = {
                type: 'normal',
                page: 0,
                size: 10
            }
        }
     });
}); 

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

(function () {
	

	/**
	 * Class for managing events.
	 * Can be extended to provide event functionality in other classes.
	 *
	 * @class EventEmitter Manages event registering and emitting.
	 */
	function EventEmitter() {}

	// Shortcuts to improve speed and size
	var proto = EventEmitter.prototype;
	var exports = this;
	var originalGlobalValue = exports.EventEmitter;

	/**
	 * Finds the index of the listener for the event in it's storage array.
	 *
	 * @param {Function[]} listeners Array of listeners to search through.
	 * @param {Function} listener Method to look for.
	 * @return {Number} Index of the specified listener, -1 if not found
	 * @api private
	 */
	function indexOfListener(listeners, listener) {
		var i = listeners.length;
		while (i--) {
			if (listeners[i].listener === listener) {
				return i;
			}
		}

		return -1;
	}

	/**
	 * Alias a method while keeping the context correct, to allow for overwriting of target method.
	 *
	 * @param {String} name The name of the target method.
	 * @return {Function} The aliased method
	 * @api private
	 */
	function alias(name) {
		return function aliasClosure() {
			return this[name].apply(this, arguments);
		};
	}

	/**
	 * Returns the listener array for the specified event.
	 * Will initialise the event object and listener arrays if required.
	 * Will return an object if you use a regex search. The object contains keys for each matched event. So /ba[rz]/ might return an object containing bar and baz. But only if you have either defined them with defineEvent or added some listeners to them.
	 * Each property in the object response is an array of listener functions.
	 *
	 * @param {String|RegExp} evt Name of the event to return the listeners from.
	 * @return {Function[]|Object} All listener functions for the event.
	 */
	proto.getListeners = function getListeners(evt) {
		var events = this._getEvents();
		var response;
		var key;

		// Return a concatenated array of all matching events if
		// the selector is a regular expression.
		if (typeof evt === 'object') {
			response = {};
			for (key in events) {
				if (events.hasOwnProperty(key) && evt.test(key)) {
					response[key] = events[key];
				}
			}
		}
		else {
			response = events[evt] || (events[evt] = []);
		}

		return response;
	};

	/**
	 * Takes a list of listener objects and flattens it into a list of listener functions.
	 *
	 * @param {Object[]} listeners Raw listener objects.
	 * @return {Function[]} Just the listener functions.
	 */
	proto.flattenListeners = function flattenListeners(listeners) {
		var flatListeners = [];
		var i;

		for (i = 0; i < listeners.length; i += 1) {
			flatListeners.push(listeners[i].listener);
		}

		return flatListeners;
	};

	/**
	 * Fetches the requested listeners via getListeners but will always return the results inside an object. This is mainly for internal use but others may find it useful.
	 *
	 * @param {String|RegExp} evt Name of the event to return the listeners from.
	 * @return {Object} All listener functions for an event in an object.
	 */
	proto.getListenersAsObject = function getListenersAsObject(evt) {
		var listeners = this.getListeners(evt);
		var response;

		if (listeners instanceof Array) {
			response = {};
			response[evt] = listeners;
		}

		return response || listeners;
	};

	/**
	 * Adds a listener function to the specified event.
	 * The listener will not be added if it is a duplicate.
	 * If the listener returns true then it will be removed after it is called.
	 * If you pass a regular expression as the event name then the listener will be added to all events that match it.
	 *
	 * @param {String|RegExp} evt Name of the event to attach the listener to.
	 * @param {Function} listener Method to be called when the event is emitted. If the function returns true then it will be removed after calling.
	 * @return {Object} Current instance of EventEmitter for chaining.
	 */
	proto.addListener = function addListener(evt, listener) {
		var listeners = this.getListenersAsObject(evt);
		var listenerIsWrapped = typeof listener === 'object';
		var key;

		for (key in listeners) {
			if (listeners.hasOwnProperty(key) && indexOfListener(listeners[key], listener) === -1) {
				listeners[key].push(listenerIsWrapped ? listener : {
					listener: listener,
					once: false
				});
			}
		}

		return this;
	};

	/**
	 * Alias of addListener
	 */
	proto.on = alias('addListener');

	/**
	 * Semi-alias of addListener. It will add a listener that will be
	 * automatically removed after it's first execution.
	 *
	 * @param {String|RegExp} evt Name of the event to attach the listener to.
	 * @param {Function} listener Method to be called when the event is emitted. If the function returns true then it will be removed after calling.
	 * @return {Object} Current instance of EventEmitter for chaining.
	 */
	proto.addOnceListener = function addOnceListener(evt, listener) {
		return this.addListener(evt, {
			listener: listener,
			once: true
		});
	};

	/**
	 * Alias of addOnceListener.
	 */
	proto.once = alias('addOnceListener');

	/**
	 * Defines an event name. This is required if you want to use a regex to add a listener to multiple events at once. If you don't do this then how do you expect it to know what event to add to? Should it just add to every possible match for a regex? No. That is scary and bad.
	 * You need to tell it what event names should be matched by a regex.
	 *
	 * @param {String} evt Name of the event to create.
	 * @return {Object} Current instance of EventEmitter for chaining.
	 */
	proto.defineEvent = function defineEvent(evt) {
		this.getListeners(evt);
		return this;
	};

	/**
	 * Uses defineEvent to define multiple events.
	 *
	 * @param {String[]} evts An array of event names to define.
	 * @return {Object} Current instance of EventEmitter for chaining.
	 */
	proto.defineEvents = function defineEvents(evts) {
		for (var i = 0; i < evts.length; i += 1) {
			this.defineEvent(evts[i]);
		}
		return this;
	};

	/**
	 * Removes a listener function from the specified event.
	 * When passed a regular expression as the event name, it will remove the listener from all events that match it.
	 *
	 * @param {String|RegExp} evt Name of the event to remove the listener from.
	 * @param {Function} listener Method to remove from the event.
	 * @return {Object} Current instance of EventEmitter for chaining.
	 */
	proto.removeListener = function removeListener(evt, listener) {
		var listeners = this.getListenersAsObject(evt);
		var index;
		var key;

		for (key in listeners) {
			if (listeners.hasOwnProperty(key)) {
				index = indexOfListener(listeners[key], listener);

				if (index !== -1) {
					listeners[key].splice(index, 1);
				}
			}
		}

		return this;
	};

	/**
	 * Alias of removeListener
	 */
	proto.off = alias('removeListener');

	/**
	 * Adds listeners in bulk using the manipulateListeners method.
	 * If you pass an object as the second argument you can add to multiple events at once. The object should contain key value pairs of events and listeners or listener arrays. You can also pass it an event name and an array of listeners to be added.
	 * You can also pass it a regular expression to add the array of listeners to all events that match it.
	 * Yeah, this function does quite a bit. That's probably a bad thing.
	 *
	 * @param {String|Object|RegExp} evt An event name if you will pass an array of listeners next. An object if you wish to add to multiple events at once.
	 * @param {Function[]} [listeners] An optional array of listener functions to add.
	 * @return {Object} Current instance of EventEmitter for chaining.
	 */
	proto.addListeners = function addListeners(evt, listeners) {
		// Pass through to manipulateListeners
		return this.manipulateListeners(false, evt, listeners);
	};

	/**
	 * Removes listeners in bulk using the manipulateListeners method.
	 * If you pass an object as the second argument you can remove from multiple events at once. The object should contain key value pairs of events and listeners or listener arrays.
	 * You can also pass it an event name and an array of listeners to be removed.
	 * You can also pass it a regular expression to remove the listeners from all events that match it.
	 *
	 * @param {String|Object|RegExp} evt An event name if you will pass an array of listeners next. An object if you wish to remove from multiple events at once.
	 * @param {Function[]} [listeners] An optional array of listener functions to remove.
	 * @return {Object} Current instance of EventEmitter for chaining.
	 */
	proto.removeListeners = function removeListeners(evt, listeners) {
		// Pass through to manipulateListeners
		return this.manipulateListeners(true, evt, listeners);
	};

	/**
	 * Edits listeners in bulk. The addListeners and removeListeners methods both use this to do their job. You should really use those instead, this is a little lower level.
	 * The first argument will determine if the listeners are removed (true) or added (false).
	 * If you pass an object as the second argument you can add/remove from multiple events at once. The object should contain key value pairs of events and listeners or listener arrays.
	 * You can also pass it an event name and an array of listeners to be added/removed.
	 * You can also pass it a regular expression to manipulate the listeners of all events that match it.
	 *
	 * @param {Boolean} remove True if you want to remove listeners, false if you want to add.
	 * @param {String|Object|RegExp} evt An event name if you will pass an array of listeners next. An object if you wish to add/remove from multiple events at once.
	 * @param {Function[]} [listeners] An optional array of listener functions to add/remove.
	 * @return {Object} Current instance of EventEmitter for chaining.
	 */
	proto.manipulateListeners = function manipulateListeners(remove, evt, listeners) {
		var i;
		var value;
		var single = remove ? this.removeListener : this.addListener;
		var multiple = remove ? this.removeListeners : this.addListeners;

		// If evt is an object then pass each of it's properties to this method
		if (typeof evt === 'object' && !(evt instanceof RegExp)) {
			for (i in evt) {
				if (evt.hasOwnProperty(i) && (value = evt[i])) {
					// Pass the single listener straight through to the singular method
					if (typeof value === 'function') {
						single.call(this, i, value);
					}
					else {
						// Otherwise pass back to the multiple function
						multiple.call(this, i, value);
					}
				}
			}
		}
		else {
			// So evt must be a string
			// And listeners must be an array of listeners
			// Loop over it and pass each one to the multiple method
			i = listeners.length;
			while (i--) {
				single.call(this, evt, listeners[i]);
			}
		}

		return this;
	};

	/**
	 * Removes all listeners from a specified event.
	 * If you do not specify an event then all listeners will be removed.
	 * That means every event will be emptied.
	 * You can also pass a regex to remove all events that match it.
	 *
	 * @param {String|RegExp} [evt] Optional name of the event to remove all listeners for. Will remove from every event if not passed.
	 * @return {Object} Current instance of EventEmitter for chaining.
	 */
	proto.removeEvent = function removeEvent(evt) {
		var type = typeof evt;
		var events = this._getEvents();
		var key;

		// Remove different things depending on the state of evt
		if (type === 'string') {
			// Remove all listeners for the specified event
			delete events[evt];
		}
		else if (type === 'object') {
			// Remove all events matching the regex.
			for (key in events) {
				if (events.hasOwnProperty(key) && evt.test(key)) {
					delete events[key];
				}
			}
		}
		else {
			// Remove all listeners in all events
			delete this._events;
		}

		return this;
	};

	/**
	 * Alias of removeEvent.
	 *
	 * Added to mirror the node API.
	 */
	proto.removeAllListeners = alias('removeEvent');

	/**
	 * Emits an event of your choice.
	 * When emitted, every listener attached to that event will be executed.
	 * If you pass the optional argument array then those arguments will be passed to every listener upon execution.
	 * Because it uses `apply`, your array of arguments will be passed as if you wrote them out separately.
	 * So they will not arrive within the array on the other side, they will be separate.
	 * You can also pass a regular expression to emit to all events that match it.
	 *
	 * @param {String|RegExp} evt Name of the event to emit and execute listeners for.
	 * @param {Array} [args] Optional array of arguments to be passed to each listener.
	 * @return {Object} Current instance of EventEmitter for chaining.
	 */
	proto.emitEvent = function emitEvent(evt, args) {
		var listeners = this.getListenersAsObject(evt);
		var listener;
		var i;
		var key;
		var response;

		for (key in listeners) {
			if (listeners.hasOwnProperty(key)) {
				i = listeners[key].length;

				while (i--) {
					// If the listener returns true then it shall be removed from the event
					// The function is executed either with a basic call or an apply if there is an args array
					listener = listeners[key][i];

					if (listener.once === true) {
						this.removeListener(evt, listener.listener);
					}

					response = listener.listener.apply(this, args || []);

					if (response === this._getOnceReturnValue()) {
						this.removeListener(evt, listener.listener);
					}
				}
			}
		}

		return this;
	};

	/**
	 * Alias of emitEvent
	 */
	proto.trigger = alias('emitEvent');

	/**
	 * Subtly different from emitEvent in that it will pass its arguments on to the listeners, as opposed to taking a single array of arguments to pass on.
	 * As with emitEvent, you can pass a regex in place of the event name to emit to all events that match it.
	 *
	 * @param {String|RegExp} evt Name of the event to emit and execute listeners for.
	 * @param {...*} Optional additional arguments to be passed to each listener.
	 * @return {Object} Current instance of EventEmitter for chaining.
	 */
	proto.emit = function emit(evt) {
		var args = Array.prototype.slice.call(arguments, 1);
		return this.emitEvent(evt, args);
	};

	/**
	 * Sets the current value to check against when executing listeners. If a
	 * listeners return value matches the one set here then it will be removed
	 * after execution. This value defaults to true.
	 *
	 * @param {*} value The new value to check for when executing listeners.
	 * @return {Object} Current instance of EventEmitter for chaining.
	 */
	proto.setOnceReturnValue = function setOnceReturnValue(value) {
		this._onceReturnValue = value;
		return this;
	};

	/**
	 * Fetches the current value to check against when executing listeners. If
	 * the listeners return value matches this one then it should be removed
	 * automatically. It will return true by default.
	 *
	 * @return {*|Boolean} The current value to check for or the default, true.
	 * @api private
	 */
	proto._getOnceReturnValue = function _getOnceReturnValue() {
		if (this.hasOwnProperty('_onceReturnValue')) {
			return this._onceReturnValue;
		}
		else {
			return true;
		}
	};

	/**
	 * Fetches the events object and creates one if required.
	 *
	 * @return {Object} The events storage object.
	 * @api private
	 */
	proto._getEvents = function _getEvents() {
		return this._events || (this._events = {});
	};

	/**
	 * Reverts the global {@link EventEmitter} to its previous value and returns a reference to this version.
	 *
	 * @return {Function} Non conflicting EventEmitter class.
	 */
	EventEmitter.noConflict = function noConflict() {
		exports.EventEmitter = originalGlobalValue;
		return EventEmitter;
	};

	// Expose the class either via AMD, CommonJS or the global object
	if (typeof define === 'function' && define.amd) {
		define('eventEmitter/EventEmitter',[],function () {
			return EventEmitter;
		});
	}
	else if (typeof module === 'object' && module.exports){
		module.exports = EventEmitter;
	}
	else {
		this.EventEmitter = EventEmitter;
	}
}.call(this));

/*!
 * eventie v1.0.4
 * event binding helper
 *   eventie.bind( elem, 'click', myFn )
 *   eventie.unbind( elem, 'click', myFn )
 */

/*jshint browser: true, undef: true, unused: true */
/*global define: false */

( function( window ) {



var docElem = document.documentElement;

var bind = function() {};

function getIEEvent( obj ) {
  var event = window.event;
  // add event.target
  event.target = event.target || event.srcElement || obj;
  return event;
}

if ( docElem.addEventListener ) {
  bind = function( obj, type, fn ) {
    obj.addEventListener( type, fn, false );
  };
} else if ( docElem.attachEvent ) {
  bind = function( obj, type, fn ) {
    obj[ type + fn ] = fn.handleEvent ?
      function() {
        var event = getIEEvent( obj );
        fn.handleEvent.call( fn, event );
      } :
      function() {
        var event = getIEEvent( obj );
        fn.call( obj, event );
      };
    obj.attachEvent( "on" + type, obj[ type + fn ] );
  };
}

var unbind = function() {};

if ( docElem.removeEventListener ) {
  unbind = function( obj, type, fn ) {
    obj.removeEventListener( type, fn, false );
  };
} else if ( docElem.detachEvent ) {
  unbind = function( obj, type, fn ) {
    obj.detachEvent( "on" + type, obj[ type + fn ] );
    try {
      delete obj[ type + fn ];
    } catch ( err ) {
      // can't delete window object properties
      obj[ type + fn ] = undefined;
    }
  };
}

var eventie = {
  bind: bind,
  unbind: unbind
};

// transport
if ( typeof define === 'function' && define.amd ) {
  // AMD
  define( 'eventie/eventie',eventie );
} else {
  // browser global
  window.eventie = eventie;
}

})( this );

/*!
 * imagesLoaded v3.1.8
 * JavaScript is all like "You images are done yet or what?"
 * MIT License
 */

( function( window, factory ) { 
  // universal module definition

  /*global define: false, module: false, require: false */

  if ( typeof define === 'function' && define.amd ) {
    // AMD
    define( 'imagesLoaded',[
      'eventEmitter/EventEmitter',
      'eventie/eventie'
    ], function( EventEmitter, eventie ) {
      return factory( window, EventEmitter, eventie );
    });
  } else if ( typeof exports === 'object' ) {
    // CommonJS
    module.exports = factory(
      window,
      require('wolfy87-eventemitter'),
      require('eventie')
    );
  } else {
    // browser global
    window.imagesLoaded = factory(
      window,
      window.EventEmitter,
      window.eventie
    );
  }

})( window,

// --------------------------  factory -------------------------- //

function factory( window, EventEmitter, eventie ) {



var $ = window.jQuery;
var console = window.console;
var hasConsole = typeof console !== 'undefined';

// -------------------------- helpers -------------------------- //

// extend objects
function extend( a, b ) {
  for ( var prop in b ) {
    a[ prop ] = b[ prop ];
  }
  return a;
}

var objToString = Object.prototype.toString;
function isArray( obj ) {
  return objToString.call( obj ) === '[object Array]';
}

// turn element or nodeList into an array
function makeArray( obj ) {
  var ary = [];
  if ( isArray( obj ) ) {
    // use object if already an array
    ary = obj;
  } else if ( typeof obj.length === 'number' ) {
    // convert nodeList to array
    for ( var i=0, len = obj.length; i < len; i++ ) {
      ary.push( obj[i] );
    }
  } else {
    // array of single index
    ary.push( obj );
  }
  return ary;
}

  // -------------------------- imagesLoaded -------------------------- //

  /**
   * @param {Array, Element, NodeList, String} elem
   * @param {Object or Function} options - if function, use as callback
   * @param {Function} onAlways - callback function
   */
  function ImagesLoaded( elem, options, onAlways ) {
    // coerce ImagesLoaded() without new, to be new ImagesLoaded()
    if ( !( this instanceof ImagesLoaded ) ) {
      return new ImagesLoaded( elem, options );
    }
    // use elem as selector string
    if ( typeof elem === 'string' ) {
      elem = $(elem);
      //author: jq
      //elem = document.querySelectorAll( elem );
    }

    this.elements = makeArray( elem );
    this.options = extend( {}, this.options );

    if ( typeof options === 'function' ) {
      onAlways = options;
    } else {
      extend( this.options, options );
    }

    if ( onAlways ) {
      this.on( 'always', onAlways );
    }

    this.getImages();

    if ( $ ) {
      // add jQuery Deferred object
      this.jqDeferred = new $.Deferred();
    }

    // HACK check async to allow time to bind listeners
    var _this = this;
    setTimeout( function() {
      _this.check();
    });
  }

  ImagesLoaded.prototype = new EventEmitter();

  ImagesLoaded.prototype.options = {};

  ImagesLoaded.prototype.getImages = function() {
    this.images = [];

    // filter & find items if we have an item selector
    for ( var i=0, len = this.elements.length; i < len; i++ ) {
      var elem = this.elements[i];
      // filter siblings
      if ( elem.nodeName === 'IMG' ) {
        this.addImage( elem );
      }
      // find children
      // no non-element nodes, #143
      var nodeType = elem.nodeType;
      if ( !nodeType || !( nodeType === 1 || nodeType === 9 || nodeType === 11 ) ) {
        continue;
      }
      //author: jq
      var childElems = $(elem).find('img');
      //var childElems = elem.querySelectorAll('img');
      // concat childElems to filterFound array
      for ( var j=0, jLen = childElems.length; j < jLen; j++ ) {
        var img = childElems[j];
        this.addImage( img );
      }
    }
  };

  /**
   * @param {Image} img
   */
  ImagesLoaded.prototype.addImage = function( img ) {
    var loadingImage = new LoadingImage( img );
    this.images.push( loadingImage );
  };

  ImagesLoaded.prototype.check = function() {
    var _this = this;
    var checkedCount = 0;
    var length = this.images.length;
    this.hasAnyBroken = false;
    // complete if no images
    if ( !length ) {
      this.complete();
      return;
    }

    function onConfirm( image, message ) {
      if ( _this.options.debug && hasConsole ) {
        console.log( 'confirm', image, message );
      }

      _this.progress( image );
      checkedCount++;
      if ( checkedCount === length ) {
        _this.complete();
      }
      return true; // bind once
    }

    for ( var i=0; i < length; i++ ) {
      var loadingImage = this.images[i];
      loadingImage.on( 'confirm', onConfirm );
      loadingImage.check();
    }
  };

  ImagesLoaded.prototype.progress = function( image ) {
    this.hasAnyBroken = this.hasAnyBroken || !image.isLoaded;
    // HACK - Chrome triggers event before object properties have changed. #83
    var _this = this;
    setTimeout( function() {
      _this.emit( 'progress', _this, image );
      if ( _this.jqDeferred && _this.jqDeferred.notify ) {
        _this.jqDeferred.notify( _this, image );
      }
    });
  };

  ImagesLoaded.prototype.complete = function() {
    var eventName = this.hasAnyBroken ? 'fail' : 'done';
    this.isComplete = true;
    var _this = this;
    // HACK - another setTimeout so that confirm happens after progress
    setTimeout( function() {
      _this.emit( eventName, _this );
      _this.emit( 'always', _this );
      if ( _this.jqDeferred ) {
        var jqMethod = _this.hasAnyBroken ? 'reject' : 'resolve';
        _this.jqDeferred[ jqMethod ]( _this );
      }
    });
  };

  // -------------------------- jquery -------------------------- //

  if ( $ ) {
    $.fn.imagesLoaded = function( options, callback ) {
      var instance = new ImagesLoaded( this, options, callback );
      return instance.jqDeferred.promise( $(this) );
    };
  }


  // --------------------------  -------------------------- //

  function LoadingImage( img ) {
    this.img = img;
  }

  LoadingImage.prototype = new EventEmitter();

  LoadingImage.prototype.check = function() {
    // first check cached any previous images that have same src
    var resource = cache[ this.img.src ] || new Resource( this.img.src );
    if ( resource.isConfirmed ) {
      this.confirm( resource.isLoaded, 'cached was confirmed' );
      return;
    }

    // If complete is true and browser supports natural sizes,
    // try to check for image status manually.
    if ( this.img.complete && this.img.naturalWidth !== undefined ) {
      // report based on naturalWidth
      this.confirm( this.img.naturalWidth !== 0, 'naturalWidth' );
      return;
    }

    // If none of the checks above matched, simulate loading on detached element.
    var _this = this;
    resource.on( 'confirm', function( resrc, message ) {
      _this.confirm( resrc.isLoaded, message );
      return true;
    });

    resource.check();
  };

  LoadingImage.prototype.confirm = function( isLoaded, message ) {
    this.isLoaded = isLoaded;
    this.emit( 'confirm', this, message );
  };

  // -------------------------- Resource -------------------------- //

  // Resource checks each src, only once
  // separate class from LoadingImage to prevent memory leaks. See #115

  var cache = {};

  function Resource( src ) {
    this.src = src;
    // add to cache
    cache[ src ] = this;
  }

  Resource.prototype = new EventEmitter();

  Resource.prototype.check = function() {
    // only trigger checking once
    if ( this.isChecked ) {
      return;
    }
    // simulate loading on detached element
    var proxyImage = new Image();
    eventie.bind( proxyImage, 'load', this );
    eventie.bind( proxyImage, 'error', this );
    proxyImage.src = this.src;
    // set flag
    this.isChecked = true;
  };

  // ----- events ----- //

  // trigger specified handler for event type
  Resource.prototype.handleEvent = function( event ) {
    var method = 'on' + event.type;
    if ( this[ method ] ) {
      this[ method ]( event );
    }
  };

  Resource.prototype.onload = function( event ) {
    this.confirm( true, 'onload' );
    this.unbindProxyEvents( event );
  };

  Resource.prototype.onerror = function( event ) {
    this.confirm( false, 'onerror' );
    this.unbindProxyEvents( event );
  };

  // ----- confirm ----- //

  Resource.prototype.confirm = function( isLoaded, message ) {
    this.isConfirmed = true;
    this.isLoaded = isLoaded;
    this.emit( 'confirm', this, message );
  };

  Resource.prototype.unbindProxyEvents = function( event ) {
    eventie.unbind( event.target, 'load', this );
    eventie.unbind( event.target, 'error', this );
  };

  // -----  ----- //

  return ImagesLoaded;

});

/*!
 * Masonry PACKAGED v3.3.2
 * Cascading grid layout library
 * http://masonry.desandro.com
 * MIT License
 * by David DeSandro
 */

/**
 * Bridget makes jQuery widgets
 * v1.1.0
 * MIT license
 */

( function( window ) {



// -------------------------- utils -------------------------- //

var slice = Array.prototype.slice;

function noop() {}

// -------------------------- definition -------------------------- //

function defineBridget( $ ) {

// bail if no jQuery
if ( !$ ) {
  return;
}

// -------------------------- addOptionMethod -------------------------- //

/**
 * adds option method -> $().plugin('option', {...})
 * @param {Function} PluginClass - constructor class
 */
function addOptionMethod( PluginClass ) {
  // don't overwrite original option method
  if ( PluginClass.prototype.option ) {
    return;
  }

  // option setter
  PluginClass.prototype.option = function( opts ) {
    // bail out if not an object
    if ( !$.isPlainObject( opts ) ){
      return;
    }
    this.options = $.extend( true, this.options, opts );
  };
}

// -------------------------- plugin bridge -------------------------- //

// helper function for logging errors
// $.error breaks jQuery chaining
var logError = typeof console === 'undefined' ? noop :
  function( message ) {
    console.error( message );
  };

/**
 * jQuery plugin bridge, access methods like $elem.plugin('method')
 * @param {String} namespace - plugin name
 * @param {Function} PluginClass - constructor class
 */
function bridge( namespace, PluginClass ) {
  // add to jQuery fn namespace
  $.fn[ namespace ] = function( options ) {
    if ( typeof options === 'string' ) {
      // call plugin method when first argument is a string
      // get arguments for method
      var args = slice.call( arguments, 1 );

      for ( var i=0, len = this.length; i < len; i++ ) {
        var elem = this[i];
        var instance = $.data( elem, namespace );
        if ( !instance ) {
          logError( "cannot call methods on " + namespace + " prior to initialization; " +
            "attempted to call '" + options + "'" );
          continue;
        }
        if ( !$.isFunction( instance[options] ) || options.charAt(0) === '_' ) {
          logError( "no such method '" + options + "' for " + namespace + " instance" );
          continue;
        }

        // trigger method with arguments
        var returnValue = instance[ options ].apply( instance, args );

        // break look and return first value if provided
        if ( returnValue !== undefined ) {
          return returnValue;
        }
      }
      // return this if no return value
      return this;
    } else {
      return this.each( function() {
        var instance = $.data( this, namespace );
        if ( instance ) {
          // apply options & init
          instance.option( options );
          instance._init();
        } else {
          // initialize new instance
          instance = new PluginClass( this, options );
          $.data( this, namespace, instance );
        }
      });
    }
  };

}

// -------------------------- bridget -------------------------- //

/**
 * converts a Prototypical class into a proper jQuery plugin
 *   the class must have a ._init method
 * @param {String} namespace - plugin name, used in $().pluginName
 * @param {Function} PluginClass - constructor class
 */
$.bridget = function( namespace, PluginClass ) {
  addOptionMethod( PluginClass );
  bridge( namespace, PluginClass );
};

return $.bridget;

}

// transport
if ( typeof define === 'function' && define.amd ) {
  // AMD
  define( 'jquery-bridget/jquery.bridget',[ 'jquery' ], defineBridget );
} else if ( typeof exports === 'object' ) {
  defineBridget( require('jquery') );
} else {
  // get jquery from browser global
  defineBridget( window.jQuery );
}

})( window );

/*!
 * eventie v1.0.6
 * event binding helper
 *   eventie.bind( elem, 'click', myFn )
 *   eventie.unbind( elem, 'click', myFn )
 * MIT license
 */

/*jshint browser: true, undef: true, unused: true */
/*global define: false, module: false */

( function( window ) {



var docElem = document.documentElement;

var bind = function() {};

function getIEEvent( obj ) {
  var event = window.event;
  // add event.target
  event.target = event.target || event.srcElement || obj;
  return event;
}

if ( docElem.addEventListener ) {
  bind = function( obj, type, fn ) {
    obj.addEventListener( type, fn, false );
  };
} else if ( docElem.attachEvent ) {
  bind = function( obj, type, fn ) {
    obj[ type + fn ] = fn.handleEvent ?
      function() {
        var event = getIEEvent( obj );
        fn.handleEvent.call( fn, event );
      } :
      function() {
        var event = getIEEvent( obj );
        fn.call( obj, event );
      };
    obj.attachEvent( "on" + type, obj[ type + fn ] );
  };
}

var unbind = function() {};

if ( docElem.removeEventListener ) {
  unbind = function( obj, type, fn ) {
    obj.removeEventListener( type, fn, false );
  };
} else if ( docElem.detachEvent ) {
  unbind = function( obj, type, fn ) {
    obj.detachEvent( "on" + type, obj[ type + fn ] );
    try {
      delete obj[ type + fn ];
    } catch ( err ) {
      // can't delete window object properties
      obj[ type + fn ] = undefined;
    }
  };
}

var eventie = {
  bind: bind,
  unbind: unbind
};

// ----- module definition ----- //

if ( typeof define === 'function' && define.amd ) {
  // AMD
  define( 'eventie/eventie',eventie );
} else if ( typeof exports === 'object' ) {
  // CommonJS
  module.exports = eventie;
} else {
  // browser global
  window.eventie = eventie;
}

})( window );

/*!
 * EventEmitter v4.2.11 - git.io/ee
 * Unlicense - http://unlicense.org/
 * Oliver Caldwell - http://oli.me.uk/
 * @preserve
 */

;(function () {
    

    /**
     * Class for managing events.
     * Can be extended to provide event functionality in other classes.
     *
     * @class EventEmitter Manages event registering and emitting.
     */
    function EventEmitter() {}

    // Shortcuts to improve speed and size
    var proto = EventEmitter.prototype;
    var exports = this;
    var originalGlobalValue = exports.EventEmitter;

    /**
     * Finds the index of the listener for the event in its storage array.
     *
     * @param {Function[]} listeners Array of listeners to search through.
     * @param {Function} listener Method to look for.
     * @return {Number} Index of the specified listener, -1 if not found
     * @api private
     */
    function indexOfListener(listeners, listener) {
        var i = listeners.length;
        while (i--) {
            if (listeners[i].listener === listener) {
                return i;
            }
        }

        return -1;
    }

    /**
     * Alias a method while keeping the context correct, to allow for overwriting of target method.
     *
     * @param {String} name The name of the target method.
     * @return {Function} The aliased method
     * @api private
     */
    function alias(name) {
        return function aliasClosure() {
            return this[name].apply(this, arguments);
        };
    }

    /**
     * Returns the listener array for the specified event.
     * Will initialise the event object and listener arrays if required.
     * Will return an object if you use a regex search. The object contains keys for each matched event. So /ba[rz]/ might return an object containing bar and baz. But only if you have either defined them with defineEvent or added some listeners to them.
     * Each property in the object response is an array of listener functions.
     *
     * @param {String|RegExp} evt Name of the event to return the listeners from.
     * @return {Function[]|Object} All listener functions for the event.
     */
    proto.getListeners = function getListeners(evt) {
        var events = this._getEvents();
        var response;
        var key;

        // Return a concatenated array of all matching events if
        // the selector is a regular expression.
        if (evt instanceof RegExp) {
            response = {};
            for (key in events) {
                if (events.hasOwnProperty(key) && evt.test(key)) {
                    response[key] = events[key];
                }
            }
        }
        else {
            response = events[evt] || (events[evt] = []);
        }

        return response;
    };

    /**
     * Takes a list of listener objects and flattens it into a list of listener functions.
     *
     * @param {Object[]} listeners Raw listener objects.
     * @return {Function[]} Just the listener functions.
     */
    proto.flattenListeners = function flattenListeners(listeners) {
        var flatListeners = [];
        var i;

        for (i = 0; i < listeners.length; i += 1) {
            flatListeners.push(listeners[i].listener);
        }

        return flatListeners;
    };

    /**
     * Fetches the requested listeners via getListeners but will always return the results inside an object. This is mainly for internal use but others may find it useful.
     *
     * @param {String|RegExp} evt Name of the event to return the listeners from.
     * @return {Object} All listener functions for an event in an object.
     */
    proto.getListenersAsObject = function getListenersAsObject(evt) {
        var listeners = this.getListeners(evt);
        var response;

        if (listeners instanceof Array) {
            response = {};
            response[evt] = listeners;
        }

        return response || listeners;
    };

    /**
     * Adds a listener function to the specified event.
     * The listener will not be added if it is a duplicate.
     * If the listener returns true then it will be removed after it is called.
     * If you pass a regular expression as the event name then the listener will be added to all events that match it.
     *
     * @param {String|RegExp} evt Name of the event to attach the listener to.
     * @param {Function} listener Method to be called when the event is emitted. If the function returns true then it will be removed after calling.
     * @return {Object} Current instance of EventEmitter for chaining.
     */
    proto.addListener = function addListener(evt, listener) {
        var listeners = this.getListenersAsObject(evt);
        var listenerIsWrapped = typeof listener === 'object';
        var key;

        for (key in listeners) {
            if (listeners.hasOwnProperty(key) && indexOfListener(listeners[key], listener) === -1) {
                listeners[key].push(listenerIsWrapped ? listener : {
                    listener: listener,
                    once: false
                });
            }
        }

        return this;
    };

    /**
     * Alias of addListener
     */
    proto.on = alias('addListener');

    /**
     * Semi-alias of addListener. It will add a listener that will be
     * automatically removed after its first execution.
     *
     * @param {String|RegExp} evt Name of the event to attach the listener to.
     * @param {Function} listener Method to be called when the event is emitted. If the function returns true then it will be removed after calling.
     * @return {Object} Current instance of EventEmitter for chaining.
     */
    proto.addOnceListener = function addOnceListener(evt, listener) {
        return this.addListener(evt, {
            listener: listener,
            once: true
        });
    };

    /**
     * Alias of addOnceListener.
     */
    proto.once = alias('addOnceListener');

    /**
     * Defines an event name. This is required if you want to use a regex to add a listener to multiple events at once. If you don't do this then how do you expect it to know what event to add to? Should it just add to every possible match for a regex? No. That is scary and bad.
     * You need to tell it what event names should be matched by a regex.
     *
     * @param {String} evt Name of the event to create.
     * @return {Object} Current instance of EventEmitter for chaining.
     */
    proto.defineEvent = function defineEvent(evt) {
        this.getListeners(evt);
        return this;
    };

    /**
     * Uses defineEvent to define multiple events.
     *
     * @param {String[]} evts An array of event names to define.
     * @return {Object} Current instance of EventEmitter for chaining.
     */
    proto.defineEvents = function defineEvents(evts) {
        for (var i = 0; i < evts.length; i += 1) {
            this.defineEvent(evts[i]);
        }
        return this;
    };

    /**
     * Removes a listener function from the specified event.
     * When passed a regular expression as the event name, it will remove the listener from all events that match it.
     *
     * @param {String|RegExp} evt Name of the event to remove the listener from.
     * @param {Function} listener Method to remove from the event.
     * @return {Object} Current instance of EventEmitter for chaining.
     */
    proto.removeListener = function removeListener(evt, listener) {
        var listeners = this.getListenersAsObject(evt);
        var index;
        var key;

        for (key in listeners) {
            if (listeners.hasOwnProperty(key)) {
                index = indexOfListener(listeners[key], listener);

                if (index !== -1) {
                    listeners[key].splice(index, 1);
                }
            }
        }

        return this;
    };

    /**
     * Alias of removeListener
     */
    proto.off = alias('removeListener');

    /**
     * Adds listeners in bulk using the manipulateListeners method.
     * If you pass an object as the second argument you can add to multiple events at once. The object should contain key value pairs of events and listeners or listener arrays. You can also pass it an event name and an array of listeners to be added.
     * You can also pass it a regular expression to add the array of listeners to all events that match it.
     * Yeah, this function does quite a bit. That's probably a bad thing.
     *
     * @param {String|Object|RegExp} evt An event name if you will pass an array of listeners next. An object if you wish to add to multiple events at once.
     * @param {Function[]} [listeners] An optional array of listener functions to add.
     * @return {Object} Current instance of EventEmitter for chaining.
     */
    proto.addListeners = function addListeners(evt, listeners) {
        // Pass through to manipulateListeners
        return this.manipulateListeners(false, evt, listeners);
    };

    /**
     * Removes listeners in bulk using the manipulateListeners method.
     * If you pass an object as the second argument you can remove from multiple events at once. The object should contain key value pairs of events and listeners or listener arrays.
     * You can also pass it an event name and an array of listeners to be removed.
     * You can also pass it a regular expression to remove the listeners from all events that match it.
     *
     * @param {String|Object|RegExp} evt An event name if you will pass an array of listeners next. An object if you wish to remove from multiple events at once.
     * @param {Function[]} [listeners] An optional array of listener functions to remove.
     * @return {Object} Current instance of EventEmitter for chaining.
     */
    proto.removeListeners = function removeListeners(evt, listeners) {
        // Pass through to manipulateListeners
        return this.manipulateListeners(true, evt, listeners);
    };

    /**
     * Edits listeners in bulk. The addListeners and removeListeners methods both use this to do their job. You should really use those instead, this is a little lower level.
     * The first argument will determine if the listeners are removed (true) or added (false).
     * If you pass an object as the second argument you can add/remove from multiple events at once. The object should contain key value pairs of events and listeners or listener arrays.
     * You can also pass it an event name and an array of listeners to be added/removed.
     * You can also pass it a regular expression to manipulate the listeners of all events that match it.
     *
     * @param {Boolean} remove True if you want to remove listeners, false if you want to add.
     * @param {String|Object|RegExp} evt An event name if you will pass an array of listeners next. An object if you wish to add/remove from multiple events at once.
     * @param {Function[]} [listeners] An optional array of listener functions to add/remove.
     * @return {Object} Current instance of EventEmitter for chaining.
     */
    proto.manipulateListeners = function manipulateListeners(remove, evt, listeners) {
        var i;
        var value;
        var single = remove ? this.removeListener : this.addListener;
        var multiple = remove ? this.removeListeners : this.addListeners;

        // If evt is an object then pass each of its properties to this method
        if (typeof evt === 'object' && !(evt instanceof RegExp)) {
            for (i in evt) {
                if (evt.hasOwnProperty(i) && (value = evt[i])) {
                    // Pass the single listener straight through to the singular method
                    if (typeof value === 'function') {
                        single.call(this, i, value);
                    }
                    else {
                        // Otherwise pass back to the multiple function
                        multiple.call(this, i, value);
                    }
                }
            }
        }
        else {
            // So evt must be a string
            // And listeners must be an array of listeners
            // Loop over it and pass each one to the multiple method
            i = listeners.length;
            while (i--) {
                single.call(this, evt, listeners[i]);
            }
        }

        return this;
    };

    /**
     * Removes all listeners from a specified event.
     * If you do not specify an event then all listeners will be removed.
     * That means every event will be emptied.
     * You can also pass a regex to remove all events that match it.
     *
     * @param {String|RegExp} [evt] Optional name of the event to remove all listeners for. Will remove from every event if not passed.
     * @return {Object} Current instance of EventEmitter for chaining.
     */
    proto.removeEvent = function removeEvent(evt) {
        var type = typeof evt;
        var events = this._getEvents();
        var key;

        // Remove different things depending on the state of evt
        if (type === 'string') {
            // Remove all listeners for the specified event
            delete events[evt];
        }
        else if (evt instanceof RegExp) {
            // Remove all events matching the regex.
            for (key in events) {
                if (events.hasOwnProperty(key) && evt.test(key)) {
                    delete events[key];
                }
            }
        }
        else {
            // Remove all listeners in all events
            delete this._events;
        }

        return this;
    };

    /**
     * Alias of removeEvent.
     *
     * Added to mirror the node API.
     */
    proto.removeAllListeners = alias('removeEvent');

    /**
     * Emits an event of your choice.
     * When emitted, every listener attached to that event will be executed.
     * If you pass the optional argument array then those arguments will be passed to every listener upon execution.
     * Because it uses `apply`, your array of arguments will be passed as if you wrote them out separately.
     * So they will not arrive within the array on the other side, they will be separate.
     * You can also pass a regular expression to emit to all events that match it.
     *
     * @param {String|RegExp} evt Name of the event to emit and execute listeners for.
     * @param {Array} [args] Optional array of arguments to be passed to each listener.
     * @return {Object} Current instance of EventEmitter for chaining.
     */
    proto.emitEvent = function emitEvent(evt, args) {
        var listeners = this.getListenersAsObject(evt);
        var listener;
        var i;
        var key;
        var response;

        for (key in listeners) {
            if (listeners.hasOwnProperty(key)) {
                i = listeners[key].length;

                while (i--) {
                    // If the listener returns true then it shall be removed from the event
                    // The function is executed either with a basic call or an apply if there is an args array
                    listener = listeners[key][i];

                    if (listener.once === true) {
                        this.removeListener(evt, listener.listener);
                    }

                    response = listener.listener.apply(this, args || []);

                    if (response === this._getOnceReturnValue()) {
                        this.removeListener(evt, listener.listener);
                    }
                }
            }
        }

        return this;
    };

    /**
     * Alias of emitEvent
     */
    proto.trigger = alias('emitEvent');

    /**
     * Subtly different from emitEvent in that it will pass its arguments on to the listeners, as opposed to taking a single array of arguments to pass on.
     * As with emitEvent, you can pass a regex in place of the event name to emit to all events that match it.
     *
     * @param {String|RegExp} evt Name of the event to emit and execute listeners for.
     * @param {...*} Optional additional arguments to be passed to each listener.
     * @return {Object} Current instance of EventEmitter for chaining.
     */
    proto.emit = function emit(evt) {
        var args = Array.prototype.slice.call(arguments, 1);
        return this.emitEvent(evt, args);
    };

    /**
     * Sets the current value to check against when executing listeners. If a
     * listeners return value matches the one set here then it will be removed
     * after execution. This value defaults to true.
     *
     * @param {*} value The new value to check for when executing listeners.
     * @return {Object} Current instance of EventEmitter for chaining.
     */
    proto.setOnceReturnValue = function setOnceReturnValue(value) {
        this._onceReturnValue = value;
        return this;
    };

    /**
     * Fetches the current value to check against when executing listeners. If
     * the listeners return value matches this one then it should be removed
     * automatically. It will return true by default.
     *
     * @return {*|Boolean} The current value to check for or the default, true.
     * @api private
     */
    proto._getOnceReturnValue = function _getOnceReturnValue() {
        if (this.hasOwnProperty('_onceReturnValue')) {
            return this._onceReturnValue;
        }
        else {
            return true;
        }
    };

    /**
     * Fetches the events object and creates one if required.
     *
     * @return {Object} The events storage object.
     * @api private
     */
    proto._getEvents = function _getEvents() {
        return this._events || (this._events = {});
    };

    /**
     * Reverts the global {@link EventEmitter} to its previous value and returns a reference to this version.
     *
     * @return {Function} Non conflicting EventEmitter class.
     */
    EventEmitter.noConflict = function noConflict() {
        exports.EventEmitter = originalGlobalValue;
        return EventEmitter;
    };

    // Expose the class either via AMD, CommonJS or the global object
    if (typeof define === 'function' && define.amd) {
        define('eventEmitter/EventEmitter',[],function () {
            return EventEmitter;
        });
    }
    else if (typeof module === 'object' && module.exports){
        module.exports = EventEmitter;
    }
    else {
        exports.EventEmitter = EventEmitter;
    }
}.call(this));

/*!
 * getStyleProperty v1.0.4
 * original by kangax
 * http://perfectionkills.com/feature-testing-css-properties/
 * MIT license
 */

/*jshint browser: true, strict: true, undef: true */
/*global define: false, exports: false, module: false */

( function( window ) {



var prefixes = 'Webkit Moz ms Ms O'.split(' ');
var docElemStyle = document.documentElement.style;

function getStyleProperty( propName ) {
  if ( !propName ) {
    return;
  }

  // test standard property first
  if ( typeof docElemStyle[ propName ] === 'string' ) {
    return propName;
  }

  // capitalize
  propName = propName.charAt(0).toUpperCase() + propName.slice(1);

  // test vendor specific properties
  var prefixed;
  for ( var i=0, len = prefixes.length; i < len; i++ ) {
    prefixed = prefixes[i] + propName;
    if ( typeof docElemStyle[ prefixed ] === 'string' ) {
      return prefixed;
    }
  }
}

// transport
if ( typeof define === 'function' && define.amd ) {
  // AMD
  define( 'get-style-property/get-style-property',[],function() {
    return getStyleProperty;
  });
} else if ( typeof exports === 'object' ) {
  // CommonJS for Component
  module.exports = getStyleProperty;
} else {
  // browser global
  window.getStyleProperty = getStyleProperty;
}

})( window );

/*!
 * getSize v1.2.2
 * measure size of elements
 * MIT license
 */

/*jshint browser: true, strict: true, undef: true, unused: true */
/*global define: false, exports: false, require: false, module: false, console: false */

( function( window, undefined ) {



// -------------------------- helpers -------------------------- //

// get a number from a string, not a percentage
function getStyleSize( value ) {
  var num = parseFloat( value );
  // not a percent like '100%', and a number
  var isValid = value.indexOf('%') === -1 && !isNaN( num );
  return isValid && num;
}

function noop() {}

var logError = typeof console === 'undefined' ? noop :
  function( message ) {
    console.error( message );
  };

// -------------------------- measurements -------------------------- //

var measurements = [
  'paddingLeft',
  'paddingRight',
  'paddingTop',
  'paddingBottom',
  'marginLeft',
  'marginRight',
  'marginTop',
  'marginBottom',
  'borderLeftWidth',
  'borderRightWidth',
  'borderTopWidth',
  'borderBottomWidth'
];

function getZeroSize() {
  var size = {
    width: 0,
    height: 0,
    innerWidth: 0,
    innerHeight: 0,
    outerWidth: 0,
    outerHeight: 0
  };
  for ( var i=0, len = measurements.length; i < len; i++ ) {
    var measurement = measurements[i];
    size[ measurement ] = 0;
  }
  return size;
}



function defineGetSize( getStyleProperty ) {

// -------------------------- setup -------------------------- //

var isSetup = false;

var getStyle, boxSizingProp, isBoxSizeOuter;

/**
 * setup vars and functions
 * do it on initial getSize(), rather than on script load
 * For Firefox bug https://bugzilla.mozilla.org/show_bug.cgi?id=548397
 */
function setup() {
  // setup once
  if ( isSetup ) {
    return;
  }
  isSetup = true;

  var getComputedStyle = window.getComputedStyle;
  getStyle = ( function() {
    var getStyleFn = getComputedStyle ?
      function( elem ) {
        return getComputedStyle( elem, null );
      } :
      function( elem ) {
        return elem.currentStyle;
      };

      return function getStyle( elem ) {
        var style = getStyleFn( elem );
        if ( !style ) {
          logError( 'Style returned ' + style +
            '. Are you running this code in a hidden iframe on Firefox? ' +
            'See http://bit.ly/getsizebug1' );
        }
        return style;
      };
  })();

  // -------------------------- box sizing -------------------------- //

  boxSizingProp = getStyleProperty('boxSizing');

  /**
   * WebKit measures the outer-width on style.width on border-box elems
   * IE & Firefox measures the inner-width
   */
  if ( boxSizingProp ) {
    var div = document.createElement('div');
    div.style.width = '200px';
    div.style.padding = '1px 2px 3px 4px';
    div.style.borderStyle = 'solid';
    div.style.borderWidth = '1px 2px 3px 4px';
    div.style[ boxSizingProp ] = 'border-box';

    var body = document.body || document.documentElement;
    body.appendChild( div );
    var style = getStyle( div );

    isBoxSizeOuter = getStyleSize( style.width ) === 200;
    body.removeChild( div );
  }

}

// -------------------------- getSize -------------------------- //

function getSize( elem ) {
  setup();

  // use querySeletor if elem is string
  if ( typeof elem === 'string' ) {
    elem = document.querySelector( elem );
  }

  // do not proceed on non-objects
  if ( !elem || typeof elem !== 'object' || !elem.nodeType ) {
    return;
  }

  var style = getStyle( elem );

  // if hidden, everything is 0
  if ( style.display === 'none' ) {
    return getZeroSize();
  }

  var size = {};
  size.width = elem.offsetWidth;
  size.height = elem.offsetHeight;

  var isBorderBox = size.isBorderBox = !!( boxSizingProp &&
    style[ boxSizingProp ] && style[ boxSizingProp ] === 'border-box' );

  // get all measurements
  for ( var i=0, len = measurements.length; i < len; i++ ) {
    var measurement = measurements[i];
    var value = style[ measurement ];
    value = mungeNonPixel( elem, value );
    var num = parseFloat( value );
    // any 'auto', 'medium' value will be 0
    size[ measurement ] = !isNaN( num ) ? num : 0;
  }

  var paddingWidth = size.paddingLeft + size.paddingRight;
  var paddingHeight = size.paddingTop + size.paddingBottom;
  var marginWidth = size.marginLeft + size.marginRight;
  var marginHeight = size.marginTop + size.marginBottom;
  var borderWidth = size.borderLeftWidth + size.borderRightWidth;
  var borderHeight = size.borderTopWidth + size.borderBottomWidth;

  var isBorderBoxSizeOuter = isBorderBox && isBoxSizeOuter;

  // overwrite width and height if we can get it from style
  var styleWidth = getStyleSize( style.width );
  if ( styleWidth !== false ) {
    size.width = styleWidth +
      // add padding and border unless it's already including it
      ( isBorderBoxSizeOuter ? 0 : paddingWidth + borderWidth );
  }

  var styleHeight = getStyleSize( style.height );
  if ( styleHeight !== false ) {
    size.height = styleHeight +
      // add padding and border unless it's already including it
      ( isBorderBoxSizeOuter ? 0 : paddingHeight + borderHeight );
  }

  size.innerWidth = size.width - ( paddingWidth + borderWidth );
  size.innerHeight = size.height - ( paddingHeight + borderHeight );

  size.outerWidth = size.width + marginWidth;
  size.outerHeight = size.height + marginHeight;

  return size;
}

// IE8 returns percent values, not pixels
// taken from jQuery's curCSS
function mungeNonPixel( elem, value ) {
  // IE8 and has percent value
  if ( window.getComputedStyle || value.indexOf('%') === -1 ) {
    return value;
  }
  var style = elem.style;
  // Remember the original values
  var left = style.left;
  var rs = elem.runtimeStyle;
  var rsLeft = rs && rs.left;

  // Put in the new values to get a computed value out
  if ( rsLeft ) {
    rs.left = elem.currentStyle.left;
  }
  style.left = value;
  value = style.pixelLeft;

  // Revert the changed values
  style.left = left;
  if ( rsLeft ) {
    rs.left = rsLeft;
  }

  return value;
}

return getSize;

}

// transport
if ( typeof define === 'function' && define.amd ) {
  // AMD for RequireJS
  define( 'get-size/get-size',[ 'get-style-property/get-style-property' ], defineGetSize );
} else if ( typeof exports === 'object' ) {
  // CommonJS for Component
  module.exports = defineGetSize( require('desandro-get-style-property') );
} else {
  // browser global
  window.getSize = defineGetSize( window.getStyleProperty );
}

})( window );

/*!
 * docReady v1.0.4
 * Cross browser DOMContentLoaded event emitter
 * MIT license
 */

/*jshint browser: true, strict: true, undef: true, unused: true*/
/*global define: false, require: false, module: false */

( function( window ) {



var document = window.document;
// collection of functions to be triggered on ready
var queue = [];

function docReady( fn ) {
  // throw out non-functions
  if ( typeof fn !== 'function' ) {
    return;
  }

  if ( docReady.isReady ) {
    // ready now, hit it
    fn();
  } else {
    // queue function when ready
    queue.push( fn );
  }
}

docReady.isReady = false;

// triggered on various doc ready events
function onReady( event ) {
  // bail if already triggered or IE8 document is not ready just yet
  var isIE8NotReady = event.type === 'readystatechange' && document.readyState !== 'complete';
  if ( docReady.isReady || isIE8NotReady ) {
    return;
  }

  trigger();
}

function trigger() {
  docReady.isReady = true;
  // process queue
  for ( var i=0, len = queue.length; i < len; i++ ) {
    var fn = queue[i];
    fn();
  }
}

function defineDocReady( eventie ) {
  // trigger ready if page is ready
  if ( document.readyState === 'complete' ) {
    trigger();
  } else {
    // listen for events
    eventie.bind( document, 'DOMContentLoaded', onReady );
    eventie.bind( document, 'readystatechange', onReady );
    eventie.bind( window, 'load', onReady );
  }

  return docReady;
}

// transport
if ( typeof define === 'function' && define.amd ) {
  // AMD
  define( 'doc-ready/doc-ready',[ 'eventie/eventie' ], defineDocReady );
} else if ( typeof exports === 'object' ) {
  module.exports = defineDocReady( require('eventie') );
} else {
  // browser global
  window.docReady = defineDocReady( window.eventie );
}

})( window );

/**
 * matchesSelector v1.0.3
 * matchesSelector( element, '.selector' )
 * MIT license
 */

/*jshint browser: true, strict: true, undef: true, unused: true */
/*global define: false, module: false */

( function( ElemProto ) {

  

  var matchesMethod = ( function() {
    // check for the standard method name first
    if ( ElemProto.matches ) {
      return 'matches';
    }
    // check un-prefixed
    if ( ElemProto.matchesSelector ) {
      return 'matchesSelector';
    }
    // check vendor prefixes
    var prefixes = [ 'webkit', 'moz', 'ms', 'o' ];

    for ( var i=0, len = prefixes.length; i < len; i++ ) {
      var prefix = prefixes[i];
      var method = prefix + 'MatchesSelector';
      if ( ElemProto[ method ] ) {
        return method;
      }
    }
  })();

  // ----- match ----- //

  function match( elem, selector ) {
    return elem[ matchesMethod ]( selector );
  }

  // ----- appendToFragment ----- //

  function checkParent( elem ) {
    // not needed if already has parent
    if ( elem.parentNode ) {
      return;
    }
    var fragment = document.createDocumentFragment();
    fragment.appendChild( elem );
  }

  // ----- query ----- //

  // fall back to using QSA
  // thx @jonathantneal https://gist.github.com/3062955
  function query( elem, selector ) {
    // append to fragment if no parent
    checkParent( elem );

    // match elem with all selected elems of parent
    var elems = elem.parentNode.querySelectorAll( selector );
    for ( var i=0, len = elems.length; i < len; i++ ) {
      // return true if match
      if ( elems[i] === elem ) {
        return true;
      }
    }
    // otherwise return false
    return false;
  }

  // ----- matchChild ----- //

  function matchChild( elem, selector ) {
    checkParent( elem );
    return match( elem, selector );
  }

  // ----- matchesSelector ----- //

  var matchesSelector;

  if ( matchesMethod ) {
    // IE9 supports matchesSelector, but doesn't work on orphaned elems
    // check for that
    var div = document.createElement('div');
    var supportsOrphans = match( div, 'div' );
    matchesSelector = supportsOrphans ? match : matchChild;
  } else {
    matchesSelector = query;
  }

  // transport
  if ( typeof define === 'function' && define.amd ) {
    // AMD
    define( 'matches-selector/matches-selector',[],function() {
      return matchesSelector;
    });
  } else if ( typeof exports === 'object' ) {
    module.exports = matchesSelector;
  }
  else {
    // browser global
    window.matchesSelector = matchesSelector;
  }

})( Element.prototype );

/**
 * Fizzy UI utils v1.0.1
 * MIT license
 */

/*jshint browser: true, undef: true, unused: true, strict: true */

( function( window, factory ) {
  /*global define: false, module: false, require: false */
  
  // universal module definition

  if ( typeof define == 'function' && define.amd ) {
    // AMD
    define( 'fizzy-ui-utils/utils',[
      'doc-ready/doc-ready',
      'matches-selector/matches-selector'
    ], function( docReady, matchesSelector ) {
      return factory( window, docReady, matchesSelector );
    });
  } else if ( typeof exports == 'object' ) {
    // CommonJS
    module.exports = factory(
      window,
      require('doc-ready'),
      require('desandro-matches-selector')
    );
  } else {
    // browser global
    window.fizzyUIUtils = factory(
      window,
      window.docReady,
      window.matchesSelector
    );
  }

}( window, function factory( window, docReady, matchesSelector ) {



var utils = {};

// ----- extend ----- //

// extends objects
utils.extend = function( a, b ) {
  for ( var prop in b ) {
    a[ prop ] = b[ prop ];
  }
  return a;
};

// ----- modulo ----- //

utils.modulo = function( num, div ) {
  return ( ( num % div ) + div ) % div;
};

// ----- isArray ----- //
  
var objToString = Object.prototype.toString;
utils.isArray = function( obj ) {
  return objToString.call( obj ) == '[object Array]';
};

// ----- makeArray ----- //

// turn element or nodeList into an array
utils.makeArray = function( obj ) {
  var ary = [];
  if ( utils.isArray( obj ) ) {
    // use object if already an array
    ary = obj;
  } else if ( obj && typeof obj.length == 'number' ) {
    // convert nodeList to array
    for ( var i=0, len = obj.length; i < len; i++ ) {
      ary.push( obj[i] );
    }
  } else {
    // array of single index
    ary.push( obj );
  }
  return ary;
};

// ----- indexOf ----- //

// index of helper cause IE8
utils.indexOf = Array.prototype.indexOf ? function( ary, obj ) {
    return ary.indexOf( obj );
  } : function( ary, obj ) {
    for ( var i=0, len = ary.length; i < len; i++ ) {
      if ( ary[i] === obj ) {
        return i;
      }
    }
    return -1;
  };

// ----- removeFrom ----- //

utils.removeFrom = function( ary, obj ) {
  var index = utils.indexOf( ary, obj );
  if ( index != -1 ) {
    ary.splice( index, 1 );
  }
};

// ----- isElement ----- //

// http://stackoverflow.com/a/384380/182183
utils.isElement = ( typeof HTMLElement == 'function' || typeof HTMLElement == 'object' ) ?
  function isElementDOM2( obj ) {
    return obj instanceof HTMLElement;
  } :
  function isElementQuirky( obj ) {
    return obj && typeof obj == 'object' &&
      obj.nodeType == 1 && typeof obj.nodeName == 'string';
  };

// ----- setText ----- //

utils.setText = ( function() {
  var setTextProperty;
  function setText( elem, text ) {
    // only check setTextProperty once
    setTextProperty = setTextProperty || ( document.documentElement.textContent !== undefined ? 'textContent' : 'innerText' );
    elem[ setTextProperty ] = text;
  }
  return setText;
})();

// ----- getParent ----- //

utils.getParent = function( elem, selector ) {
  while ( elem != document.body ) {
    elem = elem.parentNode;
    if ( matchesSelector( elem, selector ) ) {
      return elem;
    }
  }
};

// ----- getQueryElement ----- //

// use element as selector string
utils.getQueryElement = function( elem ) {
  if ( typeof elem == 'string' ) {
    return document.querySelector( elem );
  }
  return elem;
};

// ----- handleEvent ----- //

// enable .ontype to trigger from .addEventListener( elem, 'type' )
utils.handleEvent = function( event ) {
  var method = 'on' + event.type;
  if ( this[ method ] ) {
    this[ method ]( event );
  }
};

// ----- filterFindElements ----- //

utils.filterFindElements = function( elems, selector ) {
  // make array of elems
  elems = utils.makeArray( elems );
  var ffElems = [];

  for ( var i=0, len = elems.length; i < len; i++ ) {
    var elem = elems[i];
    // check that elem is an actual element
    if ( !utils.isElement( elem ) ) {
      continue;
    }
    // filter & find items if we have a selector
    if ( selector ) {
      // filter siblings
      if ( matchesSelector( elem, selector ) ) {
        ffElems.push( elem );
      }
      // find children
      var childElems = elem.querySelectorAll( selector );
      // concat childElems to filterFound array
      for ( var j=0, jLen = childElems.length; j < jLen; j++ ) {
        ffElems.push( childElems[j] );
      }
    } else {
      ffElems.push( elem );
    }
  }

  return ffElems;
};

// ----- debounceMethod ----- //

utils.debounceMethod = function( _class, methodName, threshold ) {
  // original method
  var method = _class.prototype[ methodName ];
  var timeoutName = methodName + 'Timeout';

  _class.prototype[ methodName ] = function() {
    var timeout = this[ timeoutName ];
    if ( timeout ) {
      clearTimeout( timeout );
    }
    var args = arguments;

    var _this = this;
    this[ timeoutName ] = setTimeout( function() {
      method.apply( _this, args );
      delete _this[ timeoutName ];
    }, threshold || 100 );
  };
};

// ----- htmlInit ----- //

// http://jamesroberts.name/blog/2010/02/22/string-functions-for-javascript-trim-to-camel-case-to-dashed-and-to-underscore/
utils.toDashed = function( str ) {
  return str.replace( /(.)([A-Z])/g, function( match, $1, $2 ) {
    return $1 + '-' + $2;
  }).toLowerCase();
};

var console = window.console;
/**
 * allow user to initialize classes via .js-namespace class
 * htmlInit( Widget, 'widgetName' )
 * options are parsed from data-namespace-option attribute
 */
utils.htmlInit = function( WidgetClass, namespace ) {
  docReady( function() {
    var dashedNamespace = utils.toDashed( namespace );
    var elems = document.querySelectorAll( '.js-' + dashedNamespace );
    var dataAttr = 'data-' + dashedNamespace + '-options';

    for ( var i=0, len = elems.length; i < len; i++ ) {
      var elem = elems[i];
      var attr = elem.getAttribute( dataAttr );
      var options;
      try {
        options = attr && JSON.parse( attr );
      } catch ( error ) {
        // log error, do not initialize
        if ( console ) {
          console.error( 'Error parsing ' + dataAttr + ' on ' +
            elem.nodeName.toLowerCase() + ( elem.id ? '#' + elem.id : '' ) + ': ' +
            error );
        }
        continue;
      }
      // initialize
      var instance = new WidgetClass( elem, options );
      // make available via $().data('layoutname')
      var jQuery = window.jQuery;
      if ( jQuery ) {
        jQuery.data( elem, namespace, instance );
      }
    }
  });
};

// -----  ----- //

return utils;

}));

/**
 * Outlayer Item
 */

( function( window, factory ) {
  
  // universal module definition
  if ( typeof define === 'function' && define.amd ) {
    // AMD
    define( 'outlayer/item',[
        'eventEmitter/EventEmitter',
        'get-size/get-size',
        'get-style-property/get-style-property',
        'fizzy-ui-utils/utils'
      ],
      function( EventEmitter, getSize, getStyleProperty, utils ) {
        return factory( window, EventEmitter, getSize, getStyleProperty, utils );
      }
    );
  } else if (typeof exports === 'object') {
    // CommonJS
    module.exports = factory(
      window,
      require('wolfy87-eventemitter'),
      require('get-size'),
      require('desandro-get-style-property'),
      require('fizzy-ui-utils')
    );
  } else {
    // browser global
    window.Outlayer = {};
    window.Outlayer.Item = factory(
      window,
      window.EventEmitter,
      window.getSize,
      window.getStyleProperty,
      window.fizzyUIUtils
    );
  }

}( window, function factory( window, EventEmitter, getSize, getStyleProperty, utils ) {


// ----- helpers ----- //

var getComputedStyle = window.getComputedStyle;
var getStyle = getComputedStyle ?
  function( elem ) {
    return getComputedStyle( elem, null );
  } :
  function( elem ) {
    return elem.currentStyle;
  };


function isEmptyObj( obj ) {
  for ( var prop in obj ) {
    return false;
  }
  prop = null;
  return true;
}

// -------------------------- CSS3 support -------------------------- //

var transitionProperty = getStyleProperty('transition');
var transformProperty = getStyleProperty('transform');
var supportsCSS3 = transitionProperty && transformProperty;
var is3d = !!getStyleProperty('perspective');

var transitionEndEvent = {
  WebkitTransition: 'webkitTransitionEnd',
  MozTransition: 'transitionend',
  OTransition: 'otransitionend',
  transition: 'transitionend'
}[ transitionProperty ];

// properties that could have vendor prefix
var prefixableProperties = [
  'transform',
  'transition',
  'transitionDuration',
  'transitionProperty'
];

// cache all vendor properties
var vendorProperties = ( function() {
  var cache = {};
  for ( var i=0, len = prefixableProperties.length; i < len; i++ ) {
    var prop = prefixableProperties[i];
    var supportedProp = getStyleProperty( prop );
    if ( supportedProp && supportedProp !== prop ) {
      cache[ prop ] = supportedProp;
    }
  }
  return cache;
})();

// -------------------------- Item -------------------------- //

function Item( element, layout ) {
  if ( !element ) {
    return;
  }

  this.element = element;
  // parent layout class, i.e. Masonry, Isotope, or Packery
  this.layout = layout;
  this.position = {
    x: 0,
    y: 0
  };

  this._create();
}

// inherit EventEmitter
utils.extend( Item.prototype, EventEmitter.prototype );

Item.prototype._create = function() {
  // transition objects
  this._transn = {
    ingProperties: {},
    clean: {},
    onEnd: {}
  };

  this.css({
    position: 'absolute'
  });
};

// trigger specified handler for event type
Item.prototype.handleEvent = function( event ) {
  var method = 'on' + event.type;
  if ( this[ method ] ) {
    this[ method ]( event );
  }
};

Item.prototype.getSize = function() {
  this.size = getSize( this.element );
};

/**
 * apply CSS styles to element
 * @param {Object} style
 */
Item.prototype.css = function( style ) {
  var elemStyle = this.element.style;

  for ( var prop in style ) {
    // use vendor property if available
    var supportedProp = vendorProperties[ prop ] || prop;
    elemStyle[ supportedProp ] = style[ prop ];
  }
};

 // measure position, and sets it
Item.prototype.getPosition = function() {
  var style = getStyle( this.element );
  var layoutOptions = this.layout.options;
  var isOriginLeft = layoutOptions.isOriginLeft;
  var isOriginTop = layoutOptions.isOriginTop;
  var xValue = style[ isOriginLeft ? 'left' : 'right' ];
  var yValue = style[ isOriginTop ? 'top' : 'bottom' ];
  // convert percent to pixels
  var layoutSize = this.layout.size;
  var x = xValue.indexOf('%') != -1 ?
    ( parseFloat( xValue ) / 100 ) * layoutSize.width : parseInt( xValue, 10 );
  var y = yValue.indexOf('%') != -1 ?
    ( parseFloat( yValue ) / 100 ) * layoutSize.height : parseInt( yValue, 10 );

  // clean up 'auto' or other non-integer values
  x = isNaN( x ) ? 0 : x;
  y = isNaN( y ) ? 0 : y;
  // remove padding from measurement
  x -= isOriginLeft ? layoutSize.paddingLeft : layoutSize.paddingRight;
  y -= isOriginTop ? layoutSize.paddingTop : layoutSize.paddingBottom;

  this.position.x = x;
  this.position.y = y;
};

// set settled position, apply padding
Item.prototype.layoutPosition = function() {
  var layoutSize = this.layout.size;
  var layoutOptions = this.layout.options;
  var style = {};

  // x
  var xPadding = layoutOptions.isOriginLeft ? 'paddingLeft' : 'paddingRight';
  var xProperty = layoutOptions.isOriginLeft ? 'left' : 'right';
  var xResetProperty = layoutOptions.isOriginLeft ? 'right' : 'left';

  var x = this.position.x + layoutSize[ xPadding ];
  // set in percentage or pixels
  style[ xProperty ] = this.getXValue( x );
  // reset other property
  style[ xResetProperty ] = '';

  // y
  var yPadding = layoutOptions.isOriginTop ? 'paddingTop' : 'paddingBottom';
  var yProperty = layoutOptions.isOriginTop ? 'top' : 'bottom';
  var yResetProperty = layoutOptions.isOriginTop ? 'bottom' : 'top';

  var y = this.position.y + layoutSize[ yPadding ];
  // set in percentage or pixels
  style[ yProperty ] = this.getYValue( y );
  // reset other property
  style[ yResetProperty ] = '';

  this.css( style );
  this.emitEvent( 'layout', [ this ] );
};

Item.prototype.getXValue = function( x ) {
  var layoutOptions = this.layout.options;
  return layoutOptions.percentPosition && !layoutOptions.isHorizontal ?
    ( ( x / this.layout.size.width ) * 100 ) + '%' : x + 'px';
};

Item.prototype.getYValue = function( y ) {
  var layoutOptions = this.layout.options;
  return layoutOptions.percentPosition && layoutOptions.isHorizontal ?
    ( ( y / this.layout.size.height ) * 100 ) + '%' : y + 'px';
};


Item.prototype._transitionTo = function( x, y ) {
  this.getPosition();
  // get current x & y from top/left
  var curX = this.position.x;
  var curY = this.position.y;

  var compareX = parseInt( x, 10 );
  var compareY = parseInt( y, 10 );
  var didNotMove = compareX === this.position.x && compareY === this.position.y;

  // save end position
  this.setPosition( x, y );

  // if did not move and not transitioning, just go to layout
  if ( didNotMove && !this.isTransitioning ) {
    this.layoutPosition();
    return;
  }

  var transX = x - curX;
  var transY = y - curY;
  var transitionStyle = {};
  transitionStyle.transform = this.getTranslate( transX, transY );

  this.transition({
    to: transitionStyle,
    onTransitionEnd: {
      transform: this.layoutPosition
    },
    isCleaning: true
  });
};

Item.prototype.getTranslate = function( x, y ) {
  // flip cooridinates if origin on right or bottom
  var layoutOptions = this.layout.options;
  x = layoutOptions.isOriginLeft ? x : -x;
  y = layoutOptions.isOriginTop ? y : -y;

  if ( is3d ) {
    return 'translate3d(' + x + 'px, ' + y + 'px, 0)';
  }

  return 'translate(' + x + 'px, ' + y + 'px)';
};

// non transition + transform support
Item.prototype.goTo = function( x, y ) {
  this.setPosition( x, y );
  this.layoutPosition();
};

// use transition and transforms if supported
Item.prototype.moveTo = supportsCSS3 ?
  Item.prototype._transitionTo : Item.prototype.goTo;

Item.prototype.setPosition = function( x, y ) {
  this.position.x = parseInt( x, 10 );
  this.position.y = parseInt( y, 10 );
};

// ----- transition ----- //

/**
 * @param {Object} style - CSS
 * @param {Function} onTransitionEnd
 */

// non transition, just trigger callback
Item.prototype._nonTransition = function( args ) {
  this.css( args.to );
  if ( args.isCleaning ) {
    this._removeStyles( args.to );
  }
  for ( var prop in args.onTransitionEnd ) {
    args.onTransitionEnd[ prop ].call( this );
  }
};

/**
 * proper transition
 * @param {Object} args - arguments
 *   @param {Object} to - style to transition to
 *   @param {Object} from - style to start transition from
 *   @param {Boolean} isCleaning - removes transition styles after transition
 *   @param {Function} onTransitionEnd - callback
 */
Item.prototype._transition = function( args ) {
  // redirect to nonTransition if no transition duration
  if ( !parseFloat( this.layout.options.transitionDuration ) ) {
    this._nonTransition( args );
    return;
  }

  var _transition = this._transn;
  // keep track of onTransitionEnd callback by css property
  for ( var prop in args.onTransitionEnd ) {
    _transition.onEnd[ prop ] = args.onTransitionEnd[ prop ];
  }
  // keep track of properties that are transitioning
  for ( prop in args.to ) {
    _transition.ingProperties[ prop ] = true;
    // keep track of properties to clean up when transition is done
    if ( args.isCleaning ) {
      _transition.clean[ prop ] = true;
    }
  }

  // set from styles
  if ( args.from ) {
    this.css( args.from );
    // force redraw. http://blog.alexmaccaw.com/css-transitions
    var h = this.element.offsetHeight;
    // hack for JSHint to hush about unused var
    h = null;
  }
  // enable transition
  this.enableTransition( args.to );
  // set styles that are transitioning
  this.css( args.to );

  this.isTransitioning = true;

};

// dash before all cap letters, including first for
// WebkitTransform => -webkit-transform
function toDashedAll( str ) {
  return str.replace( /([A-Z])/g, function( $1 ) {
    return '-' + $1.toLowerCase();
  });
}

var transitionProps = 'opacity,' +
  toDashedAll( vendorProperties.transform || 'transform' );

Item.prototype.enableTransition = function(/* style */) {
  // HACK changing transitionProperty during a transition
  // will cause transition to jump
  if ( this.isTransitioning ) {
    return;
  }

  // make `transition: foo, bar, baz` from style object
  // HACK un-comment this when enableTransition can work
  // while a transition is happening
  // var transitionValues = [];
  // for ( var prop in style ) {
  //   // dash-ify camelCased properties like WebkitTransition
  //   prop = vendorProperties[ prop ] || prop;
  //   transitionValues.push( toDashedAll( prop ) );
  // }
  // enable transition styles
  this.css({
    transitionProperty: transitionProps,
    transitionDuration: this.layout.options.transitionDuration
  });
  // listen for transition end event
  this.element.addEventListener( transitionEndEvent, this, false );
};

Item.prototype.transition = Item.prototype[ transitionProperty ? '_transition' : '_nonTransition' ];

// ----- events ----- //

Item.prototype.onwebkitTransitionEnd = function( event ) {
  this.ontransitionend( event );
};

Item.prototype.onotransitionend = function( event ) {
  this.ontransitionend( event );
};

// properties that I munge to make my life easier
var dashedVendorProperties = {
  '-webkit-transform': 'transform',
  '-moz-transform': 'transform',
  '-o-transform': 'transform'
};

Item.prototype.ontransitionend = function( event ) {
  // disregard bubbled events from children
  if ( event.target !== this.element ) {
    return;
  }
  var _transition = this._transn;
  // get property name of transitioned property, convert to prefix-free
  var propertyName = dashedVendorProperties[ event.propertyName ] || event.propertyName;

  // remove property that has completed transitioning
  delete _transition.ingProperties[ propertyName ];
  // check if any properties are still transitioning
  if ( isEmptyObj( _transition.ingProperties ) ) {
    // all properties have completed transitioning
    this.disableTransition();
  }
  // clean style
  if ( propertyName in _transition.clean ) {
    // clean up style
    this.element.style[ event.propertyName ] = '';
    delete _transition.clean[ propertyName ];
  }
  // trigger onTransitionEnd callback
  if ( propertyName in _transition.onEnd ) {
    var onTransitionEnd = _transition.onEnd[ propertyName ];
    onTransitionEnd.call( this );
    delete _transition.onEnd[ propertyName ];
  }

  this.emitEvent( 'transitionEnd', [ this ] );
};

Item.prototype.disableTransition = function() {
  this.removeTransitionStyles();
  this.element.removeEventListener( transitionEndEvent, this, false );
  this.isTransitioning = false;
};

/**
 * removes style property from element
 * @param {Object} style
**/
Item.prototype._removeStyles = function( style ) {
  // clean up transition styles
  var cleanStyle = {};
  for ( var prop in style ) {
    cleanStyle[ prop ] = '';
  }
  this.css( cleanStyle );
};

var cleanTransitionStyle = {
  transitionProperty: '',
  transitionDuration: ''
};

Item.prototype.removeTransitionStyles = function() {
  // remove transition
  this.css( cleanTransitionStyle );
};

// ----- show/hide/remove ----- //

// remove element from DOM
Item.prototype.removeElem = function() {
  this.element.parentNode.removeChild( this.element );
  // remove display: none
  this.css({ display: '' });
  this.emitEvent( 'remove', [ this ] );
};

Item.prototype.remove = function() {
  // just remove element if no transition support or no transition
  if ( !transitionProperty || !parseFloat( this.layout.options.transitionDuration ) ) {
    this.removeElem();
    return;
  }

  // start transition
  var _this = this;
  this.once( 'transitionEnd', function() {
    _this.removeElem();
  });
  this.hide();
};

Item.prototype.reveal = function() {
  delete this.isHidden;
  // remove display: none
  this.css({ display: '' });

  var options = this.layout.options;

  var onTransitionEnd = {};
  var transitionEndProperty = this.getHideRevealTransitionEndProperty('visibleStyle');
  onTransitionEnd[ transitionEndProperty ] = this.onRevealTransitionEnd;

  this.transition({
    from: options.hiddenStyle,
    to: options.visibleStyle,
    isCleaning: true,
    onTransitionEnd: onTransitionEnd
  });
};

Item.prototype.onRevealTransitionEnd = function() {
  // check if still visible
  // during transition, item may have been hidden
  if ( !this.isHidden ) {
    this.emitEvent('reveal');
  }
};

/**
 * get style property use for hide/reveal transition end
 * @param {String} styleProperty - hiddenStyle/visibleStyle
 * @returns {String}
 */
Item.prototype.getHideRevealTransitionEndProperty = function( styleProperty ) {
  var optionStyle = this.layout.options[ styleProperty ];
  // use opacity
  if ( optionStyle.opacity ) {
    return 'opacity';
  }
  // get first property
  for ( var prop in optionStyle ) {
    return prop;
  }
};

Item.prototype.hide = function() {
  // set flag
  this.isHidden = true;
  // remove display: none
  this.css({ display: '' });

  var options = this.layout.options;

  var onTransitionEnd = {};
  var transitionEndProperty = this.getHideRevealTransitionEndProperty('hiddenStyle');
  onTransitionEnd[ transitionEndProperty ] = this.onHideTransitionEnd;

  this.transition({
    from: options.visibleStyle,
    to: options.hiddenStyle,
    // keep hidden stuff hidden
    isCleaning: true,
    onTransitionEnd: onTransitionEnd
  });
};

Item.prototype.onHideTransitionEnd = function() {
  // check if still hidden
  // during transition, item may have been un-hidden
  if ( this.isHidden ) {
    this.css({ display: 'none' });
    this.emitEvent('hide');
  }
};

Item.prototype.destroy = function() {
  this.css({
    position: '',
    left: '',
    right: '',
    top: '',
    bottom: '',
    transition: '',
    transform: ''
  });
};

return Item;

}));

/*!
 * Outlayer v1.4.2
 * the brains and guts of a layout library
 * MIT license
 */

( function( window, factory ) {
  
  // universal module definition

  if ( typeof define == 'function' && define.amd ) {
    // AMD
    define( 'outlayer/outlayer',[
        'eventie/eventie',
        'eventEmitter/EventEmitter',
        'get-size/get-size',
        'fizzy-ui-utils/utils',
        './item'
      ],
      function( eventie, EventEmitter, getSize, utils, Item ) {
        return factory( window, eventie, EventEmitter, getSize, utils, Item);
      }
    );
  } else if ( typeof exports == 'object' ) {
    // CommonJS
    module.exports = factory(
      window,
      require('eventie'),
      require('wolfy87-eventemitter'),
      require('get-size'),
      require('fizzy-ui-utils'),
      require('./item')
    );
  } else {
    // browser global
    window.Outlayer = factory(
      window,
      window.eventie,
      window.EventEmitter,
      window.getSize,
      window.fizzyUIUtils,
      window.Outlayer.Item
    );
  }

}( window, function factory( window, eventie, EventEmitter, getSize, utils, Item ) {


// ----- vars ----- //

var console = window.console;
var jQuery = window.jQuery;
var noop = function() {};

// -------------------------- Outlayer -------------------------- //

// globally unique identifiers
var GUID = 0;
// internal store of all Outlayer intances
var instances = {};


/**
 * @param {Element, String} element
 * @param {Object} options
 * @constructor
 */
function Outlayer( element, options ) {
  var queryElement = utils.getQueryElement( element );
  if ( !queryElement ) {
    if ( console ) {
      console.error( 'Bad element for ' + this.constructor.namespace +
        ': ' + ( queryElement || element ) );
    }
    return;
  }
  this.element = queryElement;
  // add jQuery
  if ( jQuery ) {
    this.$element = jQuery( this.element );
  }

  // options
  this.options = utils.extend( {}, this.constructor.defaults );
  this.option( options );

  // add id for Outlayer.getFromElement
  var id = ++GUID;
  this.element.outlayerGUID = id; // expando
  instances[ id ] = this; // associate via id

  // kick it off
  this._create();

  if ( this.options.isInitLayout ) {
    this.layout();
  }
}

// settings are for internal use only
Outlayer.namespace = 'outlayer';
Outlayer.Item = Item;

// default options
Outlayer.defaults = {
  containerStyle: {
    position: 'relative'
  },
  isInitLayout: true,
  isOriginLeft: true,
  isOriginTop: true,
  isResizeBound: true,
  isResizingContainer: true,
  // item options
  transitionDuration: '0.4s',
  hiddenStyle: {
    opacity: 0,
    transform: 'scale(0.001)'
  },
  visibleStyle: {
    opacity: 1,
    transform: 'scale(1)'
  }
};

// inherit EventEmitter
utils.extend( Outlayer.prototype, EventEmitter.prototype );

/**
 * set options
 * @param {Object} opts
 */
Outlayer.prototype.option = function( opts ) {
  utils.extend( this.options, opts );
};

Outlayer.prototype._create = function() {
  // get items from children
  this.reloadItems();
  // elements that affect layout, but are not laid out
  this.stamps = [];
  this.stamp( this.options.stamp );
  // set container style
  utils.extend( this.element.style, this.options.containerStyle );

  // bind resize method
  if ( this.options.isResizeBound ) {
    this.bindResize();
  }
};

// goes through all children again and gets bricks in proper order
Outlayer.prototype.reloadItems = function() {
  // collection of item elements
  this.items = this._itemize( this.element.children );
};


/**
 * turn elements into Outlayer.Items to be used in layout
 * @param {Array or NodeList or HTMLElement} elems
 * @returns {Array} items - collection of new Outlayer Items
 */
Outlayer.prototype._itemize = function( elems ) {

  var itemElems = this._filterFindItemElements( elems );
  var Item = this.constructor.Item;

  // create new Outlayer Items for collection
  var items = [];
  for ( var i=0, len = itemElems.length; i < len; i++ ) {
    var elem = itemElems[i];
    var item = new Item( elem, this );
    items.push( item );
  }

  return items;
};

/**
 * get item elements to be used in layout
 * @param {Array or NodeList or HTMLElement} elems
 * @returns {Array} items - item elements
 */
Outlayer.prototype._filterFindItemElements = function( elems ) {
  return utils.filterFindElements( elems, this.options.itemSelector );
};

/**
 * getter method for getting item elements
 * @returns {Array} elems - collection of item elements
 */
Outlayer.prototype.getItemElements = function() {
  var elems = [];
  for ( var i=0, len = this.items.length; i < len; i++ ) {
    elems.push( this.items[i].element );
  }
  return elems;
};

// ----- init & layout ----- //

/**
 * lays out all items
 */
Outlayer.prototype.layout = function() {
  this._resetLayout();
  this._manageStamps();

  // don't animate first layout
  var isInstant = this.options.isLayoutInstant !== undefined ?
    this.options.isLayoutInstant : !this._isLayoutInited;
  this.layoutItems( this.items, isInstant );

  // flag for initalized
  this._isLayoutInited = true;
};

// _init is alias for layout
Outlayer.prototype._init = Outlayer.prototype.layout;

/**
 * logic before any new layout
 */
Outlayer.prototype._resetLayout = function() {
  this.getSize();
};


Outlayer.prototype.getSize = function() {
  this.size = getSize( this.element );
};

/**
 * get measurement from option, for columnWidth, rowHeight, gutter
 * if option is String -> get element from selector string, & get size of element
 * if option is Element -> get size of element
 * else use option as a number
 *
 * @param {String} measurement
 * @param {String} size - width or height
 * @private
 */
Outlayer.prototype._getMeasurement = function( measurement, size ) {
  var option = this.options[ measurement ];
  var elem;
  if ( !option ) {
    // default to 0
    this[ measurement ] = 0;
  } else {
    // use option as an element
    if ( typeof option === 'string' ) {
      elem = this.element.querySelector( option );
    } else if ( utils.isElement( option ) ) {
      elem = option;
    }
    // use size of element, if element
    this[ measurement ] = elem ? getSize( elem )[ size ] : option;
  }
};

/**
 * layout a collection of item elements
 * @api public
 */
Outlayer.prototype.layoutItems = function( items, isInstant ) {
  items = this._getItemsForLayout( items );

  this._layoutItems( items, isInstant );

  this._postLayout();
};

/**
 * get the items to be laid out
 * you may want to skip over some items
 * @param {Array} items
 * @returns {Array} items
 */
Outlayer.prototype._getItemsForLayout = function( items ) {
  var layoutItems = [];
  for ( var i=0, len = items.length; i < len; i++ ) {
    var item = items[i];
    if ( !item.isIgnored ) {
      layoutItems.push( item );
    }
  }
  return layoutItems;
};

/**
 * layout items
 * @param {Array} items
 * @param {Boolean} isInstant
 */
Outlayer.prototype._layoutItems = function( items, isInstant ) {
  this._emitCompleteOnItems( 'layout', items );

  if ( !items || !items.length ) {
    // no items, emit event with empty array
    return;
  }

  var queue = [];

  for ( var i=0, len = items.length; i < len; i++ ) {
    var item = items[i];
    // get x/y object from method
    var position = this._getItemLayoutPosition( item );
    // enqueue
    position.item = item;
    position.isInstant = isInstant || item.isLayoutInstant;
    queue.push( position );
  }

  this._processLayoutQueue( queue );
};

/**
 * get item layout position
 * @param {Outlayer.Item} item
 * @returns {Object} x and y position
 */
Outlayer.prototype._getItemLayoutPosition = function( /* item */ ) {
  return {
    x: 0,
    y: 0
  };
};

/**
 * iterate over array and position each item
 * Reason being - separating this logic prevents 'layout invalidation'
 * thx @paul_irish
 * @param {Array} queue
 */
Outlayer.prototype._processLayoutQueue = function( queue ) {
  for ( var i=0, len = queue.length; i < len; i++ ) {
    var obj = queue[i];
    this._positionItem( obj.item, obj.x, obj.y, obj.isInstant );
  }
};

/**
 * Sets position of item in DOM
 * @param {Outlayer.Item} item
 * @param {Number} x - horizontal position
 * @param {Number} y - vertical position
 * @param {Boolean} isInstant - disables transitions
 */
Outlayer.prototype._positionItem = function( item, x, y, isInstant ) {
  if ( isInstant ) {
    // if not transition, just set CSS
    item.goTo( x, y );
  } else {
    item.moveTo( x, y );
  }
};

/**
 * Any logic you want to do after each layout,
 * i.e. size the container
 */
Outlayer.prototype._postLayout = function() {
  this.resizeContainer();
};

Outlayer.prototype.resizeContainer = function() {
  if ( !this.options.isResizingContainer ) {
    return;
  }
  var size = this._getContainerSize();
  if ( size ) {
    this._setContainerMeasure( size.width, true );
    this._setContainerMeasure( size.height, false );
  }
};

/**
 * Sets width or height of container if returned
 * @returns {Object} size
 *   @param {Number} width
 *   @param {Number} height
 */
Outlayer.prototype._getContainerSize = noop;

/**
 * @param {Number} measure - size of width or height
 * @param {Boolean} isWidth
 */
Outlayer.prototype._setContainerMeasure = function( measure, isWidth ) {
  if ( measure === undefined ) {
    return;
  }

  var elemSize = this.size;
  // add padding and border width if border box
  if ( elemSize.isBorderBox ) {
    measure += isWidth ? elemSize.paddingLeft + elemSize.paddingRight +
      elemSize.borderLeftWidth + elemSize.borderRightWidth :
      elemSize.paddingBottom + elemSize.paddingTop +
      elemSize.borderTopWidth + elemSize.borderBottomWidth;
  }

  measure = Math.max( measure, 0 );
  this.element.style[ isWidth ? 'width' : 'height' ] = measure + 'px';
};

/**
 * emit eventComplete on a collection of items events
 * @param {String} eventName
 * @param {Array} items - Outlayer.Items
 */
Outlayer.prototype._emitCompleteOnItems = function( eventName, items ) {
  var _this = this;
  function onComplete() {
    _this.dispatchEvent( eventName + 'Complete', null, [ items ] );
  }

  var count = items.length;
  if ( !items || !count ) {
    onComplete();
    return;
  }

  var doneCount = 0;
  function tick() {
    doneCount++;
    if ( doneCount === count ) {
      onComplete();
    }
  }

  // bind callback
  for ( var i=0, len = items.length; i < len; i++ ) {
    var item = items[i];
    item.once( eventName, tick );
  }
};

/**
 * emits events via eventEmitter and jQuery events
 * @param {String} type - name of event
 * @param {Event} event - original event
 * @param {Array} args - extra arguments
 */
Outlayer.prototype.dispatchEvent = function( type, event, args ) {
  // add original event to arguments
  var emitArgs = event ? [ event ].concat( args ) : args;
  this.emitEvent( type, emitArgs );

  if ( jQuery ) {
    // set this.$element
    this.$element = this.$element || jQuery( this.element );
    if ( event ) {
      // create jQuery event
      var $event = jQuery.Event( event );
      $event.type = type;
      this.$element.trigger( $event, args );
    } else {
      // just trigger with type if no event available
      this.$element.trigger( type, args );
    }
  }
};

// -------------------------- ignore & stamps -------------------------- //


/**
 * keep item in collection, but do not lay it out
 * ignored items do not get skipped in layout
 * @param {Element} elem
 */
Outlayer.prototype.ignore = function( elem ) {
  var item = this.getItem( elem );
  if ( item ) {
    item.isIgnored = true;
  }
};

/**
 * return item to layout collection
 * @param {Element} elem
 */
Outlayer.prototype.unignore = function( elem ) {
  var item = this.getItem( elem );
  if ( item ) {
    delete item.isIgnored;
  }
};

/**
 * adds elements to stamps
 * @param {NodeList, Array, Element, or String} elems
 */
Outlayer.prototype.stamp = function( elems ) {
  elems = this._find( elems );
  if ( !elems ) {
    return;
  }

  this.stamps = this.stamps.concat( elems );
  // ignore
  for ( var i=0, len = elems.length; i < len; i++ ) {
    var elem = elems[i];
    this.ignore( elem );
  }
};

/**
 * removes elements to stamps
 * @param {NodeList, Array, or Element} elems
 */
Outlayer.prototype.unstamp = function( elems ) {
  elems = this._find( elems );
  if ( !elems ){
    return;
  }

  for ( var i=0, len = elems.length; i < len; i++ ) {
    var elem = elems[i];
    // filter out removed stamp elements
    utils.removeFrom( this.stamps, elem );
    this.unignore( elem );
  }

};

/**
 * finds child elements
 * @param {NodeList, Array, Element, or String} elems
 * @returns {Array} elems
 */
Outlayer.prototype._find = function( elems ) {
  if ( !elems ) {
    return;
  }
  // if string, use argument as selector string
  if ( typeof elems === 'string' ) {
    elems = this.element.querySelectorAll( elems );
  }
  elems = utils.makeArray( elems );
  return elems;
};

Outlayer.prototype._manageStamps = function() {
  if ( !this.stamps || !this.stamps.length ) {
    return;
  }

  this._getBoundingRect();

  for ( var i=0, len = this.stamps.length; i < len; i++ ) {
    var stamp = this.stamps[i];
    this._manageStamp( stamp );
  }
};

// update boundingLeft / Top
Outlayer.prototype._getBoundingRect = function() {
  // get bounding rect for container element
  var boundingRect = this.element.getBoundingClientRect();
  var size = this.size;
  this._boundingRect = {
    left: boundingRect.left + size.paddingLeft + size.borderLeftWidth,
    top: boundingRect.top + size.paddingTop + size.borderTopWidth,
    right: boundingRect.right - ( size.paddingRight + size.borderRightWidth ),
    bottom: boundingRect.bottom - ( size.paddingBottom + size.borderBottomWidth )
  };
};

/**
 * @param {Element} stamp
**/
Outlayer.prototype._manageStamp = noop;

/**
 * get x/y position of element relative to container element
 * @param {Element} elem
 * @returns {Object} offset - has left, top, right, bottom
 */
Outlayer.prototype._getElementOffset = function( elem ) {
  var boundingRect = elem.getBoundingClientRect();
  var thisRect = this._boundingRect;
  var size = getSize( elem );
  var offset = {
    left: boundingRect.left - thisRect.left - size.marginLeft,
    top: boundingRect.top - thisRect.top - size.marginTop,
    right: thisRect.right - boundingRect.right - size.marginRight,
    bottom: thisRect.bottom - boundingRect.bottom - size.marginBottom
  };
  return offset;
};

// -------------------------- resize -------------------------- //

// enable event handlers for listeners
// i.e. resize -> onresize
Outlayer.prototype.handleEvent = function( event ) {
  var method = 'on' + event.type;
  if ( this[ method ] ) {
    this[ method ]( event );
  }
};

/**
 * Bind layout to window resizing
 */
Outlayer.prototype.bindResize = function() {
  // bind just one listener
  if ( this.isResizeBound ) {
    return;
  }
  eventie.bind( window, 'resize', this );
  this.isResizeBound = true;
};

/**
 * Unbind layout to window resizing
 */
Outlayer.prototype.unbindResize = function() {
  if ( this.isResizeBound ) {
    eventie.unbind( window, 'resize', this );
  }
  this.isResizeBound = false;
};

// original debounce by John Hann
// http://unscriptable.com/index.php/2009/03/20/debouncing-javascript-methods/

// this fires every resize
Outlayer.prototype.onresize = function() {
  if ( this.resizeTimeout ) {
    clearTimeout( this.resizeTimeout );
  }

  var _this = this;
  function delayed() {
    _this.resize();
    delete _this.resizeTimeout;
  }

  this.resizeTimeout = setTimeout( delayed, 100 );
};

// debounced, layout on resize
Outlayer.prototype.resize = function() {
  // don't trigger if size did not change
  // or if resize was unbound. See #9
  if ( !this.isResizeBound || !this.needsResizeLayout() ) {
    return;
  }

  this.layout();
};

/**
 * check if layout is needed post layout
 * @returns Boolean
 */
Outlayer.prototype.needsResizeLayout = function() {
  var size = getSize( this.element );
  // check that this.size and size are there
  // IE8 triggers resize on body size change, so they might not be
  var hasSizes = this.size && size;
  return hasSizes && size.innerWidth !== this.size.innerWidth;
};

// -------------------------- methods -------------------------- //

/**
 * add items to Outlayer instance
 * @param {Array or NodeList or Element} elems
 * @returns {Array} items - Outlayer.Items
**/
Outlayer.prototype.addItems = function( elems ) {
  var items = this._itemize( elems );
  // add items to collection
  if ( items.length ) {
    this.items = this.items.concat( items );
  }
  return items;
};

/**
 * Layout newly-appended item elements
 * @param {Array or NodeList or Element} elems
 */
Outlayer.prototype.appended = function( elems ) {
  var items = this.addItems( elems );
  if ( !items.length ) {
    return;
  }
  // layout and reveal just the new items
  this.layoutItems( items, true );
  this.reveal( items );
};

/**
 * Layout prepended elements
 * @param {Array or NodeList or Element} elems
 */
Outlayer.prototype.prepended = function( elems ) {
  var items = this._itemize( elems );
  if ( !items.length ) {
    return;
  }
  // add items to beginning of collection
  var previousItems = this.items.slice(0);
  this.items = items.concat( previousItems );
  // start new layout
  this._resetLayout();
  this._manageStamps();
  // layout new stuff without transition
  this.layoutItems( items, true );
  this.reveal( items );
  // layout previous items
  this.layoutItems( previousItems );
};

/**
 * reveal a collection of items
 * @param {Array of Outlayer.Items} items
 */
Outlayer.prototype.reveal = function( items ) {
  this._emitCompleteOnItems( 'reveal', items );

  var len = items && items.length;
  for ( var i=0; len && i < len; i++ ) {
    var item = items[i];
    item.reveal();
  }
};

/**
 * hide a collection of items
 * @param {Array of Outlayer.Items} items
 */
Outlayer.prototype.hide = function( items ) {
  this._emitCompleteOnItems( 'hide', items );

  var len = items && items.length;
  for ( var i=0; len && i < len; i++ ) {
    var item = items[i];
    item.hide();
  }
};

/**
 * reveal item elements
 * @param {Array}, {Element}, {NodeList} items
 */
Outlayer.prototype.revealItemElements = function( elems ) {
  var items = this.getItems( elems );
  this.reveal( items );
};

/**
 * hide item elements
 * @param {Array}, {Element}, {NodeList} items
 */
Outlayer.prototype.hideItemElements = function( elems ) {
  var items = this.getItems( elems );
  this.hide( items );
};

/**
 * get Outlayer.Item, given an Element
 * @param {Element} elem
 * @param {Function} callback
 * @returns {Outlayer.Item} item
 */
Outlayer.prototype.getItem = function( elem ) {
  // loop through items to get the one that matches
  for ( var i=0, len = this.items.length; i < len; i++ ) {
    var item = this.items[i];
    if ( item.element === elem ) {
      // return item
      return item;
    }
  }
};

/**
 * get collection of Outlayer.Items, given Elements
 * @param {Array} elems
 * @returns {Array} items - Outlayer.Items
 */
Outlayer.prototype.getItems = function( elems ) {
  elems = utils.makeArray( elems );
  var items = [];
  for ( var i=0, len = elems.length; i < len; i++ ) {
    var elem = elems[i];
    var item = this.getItem( elem );
    if ( item ) {
      items.push( item );
    }
  }

  return items;
};

/**
 * remove element(s) from instance and DOM
 * @param {Array or NodeList or Element} elems
 */
Outlayer.prototype.remove = function( elems ) {
  var removeItems = this.getItems( elems );

  this._emitCompleteOnItems( 'remove', removeItems );

  // bail if no items to remove
  if ( !removeItems || !removeItems.length ) {
    return;
  }

  for ( var i=0, len = removeItems.length; i < len; i++ ) {
    var item = removeItems[i];
    item.remove();
    // remove item from collection
    utils.removeFrom( this.items, item );
  }
};

// ----- destroy ----- //

// remove and disable Outlayer instance
Outlayer.prototype.destroy = function() {
  // clean up dynamic styles
  var style = this.element.style;
  style.height = '';
  style.position = '';
  style.width = '';
  // destroy items
  for ( var i=0, len = this.items.length; i < len; i++ ) {
    var item = this.items[i];
    item.destroy();
  }

  this.unbindResize();

  var id = this.element.outlayerGUID;
  delete instances[ id ]; // remove reference to instance by id
  delete this.element.outlayerGUID;
  // remove data for jQuery
  if ( jQuery ) {
    jQuery.removeData( this.element, this.constructor.namespace );
  }

};

// -------------------------- data -------------------------- //

/**
 * get Outlayer instance from element
 * @param {Element} elem
 * @returns {Outlayer}
 */
Outlayer.data = function( elem ) {
  elem = utils.getQueryElement( elem );
  var id = elem && elem.outlayerGUID;
  return id && instances[ id ];
};


// -------------------------- create Outlayer class -------------------------- //

/**
 * create a layout class
 * @param {String} namespace
 */
Outlayer.create = function( namespace, options ) {
  // sub-class Outlayer
  function Layout() {
    Outlayer.apply( this, arguments );
  }
  // inherit Outlayer prototype, use Object.create if there
  if ( Object.create ) {
    Layout.prototype = Object.create( Outlayer.prototype );
  } else {
    utils.extend( Layout.prototype, Outlayer.prototype );
  }
  // set contructor, used for namespace and Item
  Layout.prototype.constructor = Layout;

  Layout.defaults = utils.extend( {}, Outlayer.defaults );
  // apply new options
  utils.extend( Layout.defaults, options );
  // keep prototype.settings for backwards compatibility (Packery v1.2.0)
  Layout.prototype.settings = {};

  Layout.namespace = namespace;

  Layout.data = Outlayer.data;

  // sub-class Item
  Layout.Item = function LayoutItem() {
    Item.apply( this, arguments );
  };

  Layout.Item.prototype = new Item();

  // -------------------------- declarative -------------------------- //

  utils.htmlInit( Layout, namespace );

  // -------------------------- jQuery bridge -------------------------- //

  // make into jQuery plugin
  if ( jQuery && jQuery.bridget ) {
    jQuery.bridget( namespace, Layout );
  }

  return Layout;
};

// ----- fin ----- //

// back in global
Outlayer.Item = Item;

return Outlayer;

}));


/*!
 * Masonry v3.3.2
 * Cascading grid layout library
 * http://masonry.desandro.com
 * MIT License
 * by David DeSandro
 */

( function( window, factory ) {
  
  // universal module definition
  if ( typeof define === 'function' && define.amd ) {
    // AMD
    define( 'masonry',[
        'outlayer/outlayer',
        'get-size/get-size',
        'fizzy-ui-utils/utils'
      ],
      factory );
  } else if ( typeof exports === 'object' ) {
    // CommonJS
    module.exports = factory(
      require('outlayer'),
      require('get-size'),
      require('fizzy-ui-utils')
    );
  } else {
    // browser global
    window.Masonry = factory(
      window.Outlayer,
      window.getSize,
      window.fizzyUIUtils
    );
  }

}( window, function factory( Outlayer, getSize, utils ) {



// -------------------------- masonryDefinition -------------------------- //

  // create an Outlayer layout class
  var Masonry = Outlayer.create('masonry');

  Masonry.prototype._resetLayout = function() {
    this.getSize();
    this._getMeasurement( 'columnWidth', 'outerWidth' );
    this._getMeasurement( 'gutter', 'outerWidth' );
    this.measureColumns();

    // reset column Y
    var i = this.cols;
    this.colYs = [];
    while (i--) {
      this.colYs.push( 0 );
    }

    this.maxY = 0;
  };

  Masonry.prototype.measureColumns = function() {
    this.getContainerWidth();
    // if columnWidth is 0, default to outerWidth of first item
    if ( !this.columnWidth ) {
      var firstItem = this.items[0];
      var firstItemElem = firstItem && firstItem.element;
      // columnWidth fall back to item of first element
      this.columnWidth = firstItemElem && getSize( firstItemElem ).outerWidth ||
        // if first elem has no width, default to size of container
        this.containerWidth;
    }

    var columnWidth = this.columnWidth += this.gutter;

    // calculate columns
    var containerWidth = this.containerWidth + this.gutter;
    var cols = containerWidth / columnWidth;
    // fix rounding errors, typically with gutters
    var excess = columnWidth - containerWidth % columnWidth;
    // if overshoot is less than a pixel, round up, otherwise floor it
    var mathMethod = excess && excess < 1 ? 'round' : 'floor';
    cols = Math[ mathMethod ]( cols );
    this.cols = Math.max( cols, 1 );
  };

  Masonry.prototype.getContainerWidth = function() {
    // container is parent if fit width
    var container = this.options.isFitWidth ? this.element.parentNode : this.element;
    // check that this.size and size are there
    // IE8 triggers resize on body size change, so they might not be
    var size = getSize( container );
    this.containerWidth = size && size.innerWidth;
  };

  Masonry.prototype._getItemLayoutPosition = function( item ) {
    item.getSize();
    // how many columns does this brick span
    var remainder = item.size.outerWidth % this.columnWidth;
    var mathMethod = remainder && remainder < 1 ? 'round' : 'ceil';
    // round if off by 1 pixel, otherwise use ceil
    var colSpan = Math[ mathMethod ]( item.size.outerWidth / this.columnWidth );
    colSpan = Math.min( colSpan, this.cols );

    var colGroup = this._getColGroup( colSpan );
    // get the minimum Y value from the columns
    var minimumY = Math.min.apply( Math, colGroup );
    var shortColIndex = utils.indexOf( colGroup, minimumY );

    // position the brick
    var position = {
      x: this.columnWidth * shortColIndex,
      y: minimumY
    };

    // apply setHeight to necessary columns
    var setHeight = minimumY + item.size.outerHeight;
    var setSpan = this.cols + 1 - colGroup.length;
    for ( var i = 0; i < setSpan; i++ ) {
      this.colYs[ shortColIndex + i ] = setHeight;
    }

    return position;
  };

  /**
   * @param {Number} colSpan - number of columns the element spans
   * @returns {Array} colGroup
   */
  Masonry.prototype._getColGroup = function( colSpan ) {
    if ( colSpan < 2 ) {
      // if brick spans only one column, use all the column Ys
      return this.colYs;
    }

    var colGroup = [];
    // how many different places could this brick fit horizontally
    var groupCount = this.cols + 1 - colSpan;
    // for each group potential horizontal position
    for ( var i = 0; i < groupCount; i++ ) {
      // make an array of colY values for that one group
      var groupColYs = this.colYs.slice( i, i + colSpan );
      // and get the max value of the array
      colGroup[i] = Math.max.apply( Math, groupColYs );
    }
    return colGroup;
  };

  Masonry.prototype._manageStamp = function( stamp ) {
    var stampSize = getSize( stamp );
    var offset = this._getElementOffset( stamp );
    // get the columns that this stamp affects
    var firstX = this.options.isOriginLeft ? offset.left : offset.right;
    var lastX = firstX + stampSize.outerWidth;
    var firstCol = Math.floor( firstX / this.columnWidth );
    firstCol = Math.max( 0, firstCol );
    var lastCol = Math.floor( lastX / this.columnWidth );
    // lastCol should not go over if multiple of columnWidth #425
    lastCol -= lastX % this.columnWidth ? 0 : 1;
    lastCol = Math.min( this.cols - 1, lastCol );
    // set colYs to bottom of the stamp
    var stampMaxY = ( this.options.isOriginTop ? offset.top : offset.bottom ) +
      stampSize.outerHeight;
    for ( var i = firstCol; i <= lastCol; i++ ) {
      this.colYs[i] = Math.max( stampMaxY, this.colYs[i] );
    }
  };

  Masonry.prototype._getContainerSize = function() {
    this.maxY = Math.max.apply( Math, this.colYs );
    var size = {
      height: this.maxY
    };

    if ( this.options.isFitWidth ) {
      size.width = this._getContainerFitWidth();
    }

    return size;
  };

  Masonry.prototype._getContainerFitWidth = function() {
    var unusedCols = 0;
    // count unused columns
    var i = this.cols;
    while ( --i ) {
      if ( this.colYs[i] !== 0 ) {
        break;
      }
      unusedCols++;
    }
    // fit container to columns that have been used
    return ( this.cols - unusedCols ) * this.columnWidth - this.gutter;
  };

  Masonry.prototype.needsResizeLayout = function() {
    var previousWidth = this.containerWidth;
    this.getContainerWidth();
    return previousWidth !== this.containerWidth;
  };

  return Masonry;

}));


define('app/views/Base',['marionette', 'imagesLoaded', 'masonry', 'app/models/Base'],
    function (Marionette, imagesLoaded, masonry, ModelBase) {
        "use strict";
        
        return Marionette.ItemView.extend({
            initialize: function(){ 
                //console.log('base view initialize'); 
                $(window).unbind('scroll'); 

                this.construct();
            },
            construct: function () {
            },
            onRender: function(){ 
                this.loadImage(); 
                // this.centerImage();
            },
            scrollTop:function(){
                $("body").scrollTop(0);
            },
            loadImage: function() {
                var imgLoad = imagesLoaded('.is-loading', function() { 
                    //console.log('all image loaded');
                });
                imgLoad.on('progress', function ( imgLoad, image ) {
                    if(image.isLoaded) {
                        setTimeout(function() {
                            if(image) {
                                image.img.parentNode.className =  '';
                                $(image.img).css('opacity', 0);
                                //$(image.img).fadeIn(300);
                                $(image.img).animate({
                                    opacity: 1
                                }, 300);
                            }
                        }, 400);
                    }
                });

                // class=center-loading 图片居中显示 图片被容器center-loading-image-container包裹
                var centerImgLoad = imagesLoaded('.center-loading', function() {
                    // console.log('image load to set center');
                });
                centerImgLoad.on('progress', function(centerImgLoad, image) {
                    if (image.isLoaded) {
                        var imageWidth  = image.img.width;
                        var imageHeight = image.img.height;
                        var imageRatio  = imageWidth/imageHeight;
                        var centerLoadContainer = $(image.img).parents('.center-loading-image-container');
                        var containerWidth      = $(centerLoadContainer)[0].offsetWidth;
                        var containerHeight     = $(centerLoadContainer)[0].offsetHeight;
                        var tempWidth  = 0;
                        var tempHeight = 0;
                        var offsetLeft = 0;
                        var offsetTop  = 0;
                        if (imageHeight >= containerHeight && imageWidth >= containerWidth) {
                            // 图片宽高都大于容器宽高

                            // 图片长比较长，按照高度缩放，截取中间部分
                            if (imageWidth / imageHeight >= containerWidth / containerHeight) {
                              

                                tempHeight = containerHeight;
                                tempWidth  = imageWidth * containerHeight / imageHeight;

                                offsetLeft = (containerWidth - tempWidth) / 2;
                                offsetTop  = 0;
                            } else if (imageWidth / imageHeight < containerWidth / containerHeight) {
                                //图片比较高，安装宽度缩放，截取中间部分
                                tempWidth  = containerWidth;
                                tempHeight = imageHeight * containerWidth / imageWidth;

                                offsetLeft = 0;
                                offsetTop  = (containerHeight - tempHeight) / 2;
                            };    
                        } else if (imageWidth < containerWidth && imageHeight < containerHeight) {
                            // 图片宽高都小于容器宽高
                            if (imageRatio > containerWidth / containerHeight) {
                                tempWidth    = imageWidth / imageHeight * containerHeight;
                                tempHeight   = containerHeight;

                                offsetTop    = 0;
                                offsetLeft   = (imageWidth - tempWidth) / 2;
                            } else {
                                tempWidth    = containerWidth;
                                tempHeight   = tempWidth / imageWidth * imageHeight;

                                offsetLeft   = 0;
                                offsetTop    = (imageHeight - tempHeight) / 2;
                            }
                        } else if (imageWidth < containerWidth && imageHeight > containerHeight) {
                            // 图片宽度小于容器 高度大于容器  
                            tempWidth  = containerWidth;
                            tempHeight = tempWidth / imageWidth * imageHeight;

                            offsetTop  = (imageHeight - tempHeight) / 2;
                            offsetLeft = 0;
                        } else if (imageWidth > containerWidth && imageHeight < containerHeight) {
                            // 图片宽度大于容器 图片高度小于容器
                            tempHeight = containerHeight;
                            tempWidth  = imageRatio * containerHeight;

                            offsetLeft = (imageWidth - tempWidth) / 2;
                            offsetTop  = 0;
                        };          

                        $(image.img).css('left', offsetLeft);
                        $(image.img).css('top', offsetTop);
                        $(image.img).width(tempWidth);
                        $(image.img).height(tempHeight);       
                    };
                });
            },
                // centerImage: function() {

                //     // class=center-loading-img 图片居中显示 图片被容器center-image包裹
                //     var imgLoadingImg = imagesLoaded('.center-loading-img', function() {
                //         console.log('image load to set center');
                //     });
                //     imgLoadingImg.on('progress', function(imgLoadingImg, image) {
                //         if (image.isLoaded) {
                //             var imageWidth  = image.img.width;
                //             var imageHeight = image.img.height;
                //             var imageRatio  = imageWidth/imageHeight;
                //             var centerLoadContainer = $(image.img).parents('.center-image');
                //             var containerWidth      = $(centerLoadContainer)[0].offsetWidth;
                //             var containerHeight     = $(centerLoadContainer)[0].offsetHeight;
                //             var tempWidth  = 0;
                //             var tempHeight = 0;
                //             var offsetLeft = 0;
                //             var offsetTop  = 0;
                //             debugger;
                            
                //             if (imageHeight >= containerHeight && imageWidth >= containerWidth) {
                //                 // 图片宽高都大于容器宽高

                //                 // 图片长比较长，按照高度缩放，截取中间部分
                //                 if (imageWidth / imageHeight >= containerWidth / containerHeight) {
                                  
                //                     tempWidth = containerWidth;
                //                     tempHeight = imageHeight * containerWidth / imageWidth;

                //                     offsetTop = (containerHeight - tempHeight) / 2;
                //                     offsetLeft = 0;
                //                 } else if (imageWidth / imageHeight < containerWidth / containerHeight) {
                //                     //图片比较高，安装宽度缩放，截取中间部分
                //                     tempHeight = containerHeight;
                //                     tempWidth  = imageWidth * containerHeight / imageHeight;

                //                     // tempWidth  = containerWidth;
                //                     // tempHeight = imageHeight * containerWidth / imageWidth;

                //                     offsetTop = 0;
                //                     offsetLeft  = (containerWidth - tempWidth) / 2;
                //                 };    
                //             } else if (imageWidth < containerWidth && imageHeight < containerHeight) {
                //                 // 图片宽高都小于容器宽高
                //                 if (imageRatio > containerWidth / containerHeight) {
                //                     tempWidth    = containerWidth;
                //                     tempHeight   = tempWidth / imageWidth * imageHeight;

                //                     offsetLeft   = 0;
                //                     offsetTop    = (containerHeight - tempHeight) / 2;
                //                 } else {
                //                     tempWidth    = imageWidth / imageHeight * containerHeight;
                //                     tempHeight   = containerHeight;

                //                     offsetTop    = 0;
                //                     offsetLeft   = (containerWidth - tempWidth) / 2;
                //                 }
                //             } else if (imageWidth < containerWidth && imageHeight > containerHeight) {
                //                 // 图片宽度小于容器 高度大于容器  
                //                 tempHeight = containerHeight;
                //                 tempWidth  = imageRatio * containerHeight;

                //                 offsetLeft = (containerWidth - tempWidth) / 2;
                //                 offsetTop  = 0;
                //             } else if (imageWidth > containerWidth && imageHeight < containerHeight) {
                //                 // 图片宽度大于容器 图片高度小于容器
                //                 tempWidth  = containerWidth;
                //                 tempHeight = tempWidth / imageWidth * imageHeight;

                //                 offsetTop  = (containerHeight - tempHeight) / 2;
                //                 offsetLeft = 0;
                //             };          

                //             $(image.img).css('left', offsetLeft);
                //             $(image.img).css('top', offsetTop);
                //             $(image.img).width(tempWidth);
                //             $(image.img).height(tempHeight);       
                //         };
                //     });
                // },
			page: function() {
			},
            scroll: function(collection) {
                var self = this;

                //页面滚动监听 进行翻页操作
                $(window).scroll(function() {
                    //页面可视区域高度
                    var windowHeight = $(window).height();
                    //总高度
                    var pageHeight   = $(document.body).height();
                    //滚动条top
                    var scrollTop    = $(window).scrollTop();
                
                    if ((pageHeight-windowHeight-scrollTop)/windowHeight < 0.15) {
                        if(collection) {
                            self = collection;
                        }

                        self.collection.loading(function(data){ });
                    }
                });
            },
			download: function(e) {
				var type = $(e.currentTarget).attr("data-type");
                var id   = $(e.currentTarget).attr("data-id");

                $.get('/record?type='+type+'&target='+id, function(data) {
                    parse(data);

                    if(data.ret == 1) {
                        var data = data.data;
                        var urls = data.url;
                        _.each(urls, function(url) {
                            location.href = '/download?url='+url;
                        });

                        toast('已下载该图片，到进行中处理');
                    }
                });
			},
            scrollTop:function(e) {
                $("body").scrollTop(0);
            },
			render: function() {
				if(!this.collection && !this.model) {
					var el = $(this.el);
					var template = this.template;
					append(el, template());
				}
				else if(this.collection) {
					var el = $(this.el);
					var template = this.template;
					this.collection.each(function(model){
						append(el, template(model.toJSON()));
					});
				}
                else if(this.model) {
					var el = $(this.el);
					var template = this.template;
                    $(this.el).html( template(this.model.toJSON() ));
                }
                
                this.onRender(); 
            },
			msnry: null,
			renderMasonry: function() {
				var self = this;

				var template = this.template;
                var el = this.el;

                if(this.collection.length != 0){ 
					var items = '';

					for(var i = 0; i < this.collection.models.length; i++) {
                        items += template((this.collection.models[i]).toJSON());
					}
					var $items = $(items);
					$items.hide();
                    $(el).append($items);

					$items.imagesLoaded().progress( function( imgLoad, image ) {
						var $item = $( image.img ).parents( '.grid-item' );


						self.msnry = new masonry('.grid', {
							itemSelector: '.grid-item',
							isAnimated: true,
							animationOptions: {
								duration: 750,
								easing: 'linear',
								queue: false
							}
						});
						$item.fadeIn(400);
					});
                }
			},
			likeToggle: function(e) {
                var value= $(e.currentTarget).hasClass('liked') ? -1: 1;
                var id 	 = $(e.currentTarget).attr('data-id');
                var type = $(e.currentTarget).attr('data-type');
                console.log(value);
                console.log(id);
                console.log(type);
                var like = new ModelBase({
                    id: id,
                    type: type,
                    status: value 
                });
                like.url =  '/like';
                
                like.save(null, {
                    success: function(){
                        $(e.currentTarget).toggleClass('liked');
                        $(e.currentTarget).siblings('.like-count').toggleClass('like-color');
                        var likeEle = $(e.currentTarget).siblings('.like-count');
                        likeEle.text( Number(likeEle.text())+value );
                    }
                });
            },
            likeToggleLarge: function(e){
                var value = $(e.currentTarget).hasClass('liked') ? -1: 1;
                var id   = $(e.currentTarget).attr('data-id');
                var type = $(e.currentTarget).attr('data-type');


                var like = new ModelBase({
                    id: id,
                    type: type,
                    status: value 
                });
                like.url =  '/like';

                like.save(null, {
                    success: function(){
                        $(e.currentTarget).toggleClass('liked');
                        $(e.currentTarget).find('.like-count').toggleClass('like-color');

                        var likeEle = $(e.currentTarget).find('.like-count');
                        likeEle.text( Number(likeEle.text())+value );
                    }
                });
            },
			collectToggle: function(e) {
				var value = $(e.currentTarget).hasClass('collected') ? -1: 1;

                var id 	= $(e.target).attr('data-id');
                var type= $(e.target).attr('data-type');
                var collection = new ModelBase({
                    id: id,
                    type: 1,
                    status: value 
                });
				collection.url =  '/collect';

                collection.save(null, function(){
                    $(e.currentTarget).toggleClass('collected');
                    $(e.currentTarget).siblings('.collection-count').toggleClass('collection-color');

                    var collectionEle = $(e.currentTarget).siblings('.collection-count');
                    collectionEle.text( Number(collectionEle.text())+value );
                });
            },
        });
    });

/**
 * Adapted from the official plugin text.js
 *
 * Uses UnderscoreJS micro-templates : http://documentcloud.github.com/underscore/#template
 * @author Julien Cabanès <julien@zeeagency.com>
 * @version 0.2
 *
 * @license RequireJS text 0.24.0 Copyright (c) 2010-2011, The Dojo Foundation All Rights Reserved.
 * Available via the MIT or new BSD license.
 * see: http://github.com/jrburke/requirejs for details
 */
/*jslint regexp: false, nomen: false, plusplus: false, strict: false */
/*global require: false, XMLHttpRequest: false, ActiveXObject: false,
 define: false, window: false, process: false, Packages: false,
 java: false */

(function () {
    var progIds = ['Msxml2.XMLHTTP', 'Microsoft.XMLHTTP', 'Msxml2.XMLHTTP.4.0'],

        xmlRegExp = /^\s*<\?xml(\s)+version=[\'\"](\d)*.(\d)*[\'\"](\s)*\?>/im,

        bodyRegExp = /<body[^>]*>\s*([\s\S]+)\s*<\/body>/im,

        buildMap = [],

        templateSettings = {
            evaluate: /<%([\s\S]+?)%>/g,
            interpolate: /<%=([\s\S]+?)%>/g
        },

        /**
         * JavaScript micro-templating, similar to John Resig's implementation.
         * Underscore templating handles arbitrary delimiters, preserves whitespace,
         * and correctly escapes quotes within interpolated code.
         */
            template = function (str, data) {
            var c = templateSettings;
            var tmpl = 'var __p=[],print=function(){__p.push.apply(__p,arguments);};' +
                'with(obj||{}){__p.push(\'' +
                str.replace(/\\/g, '\\\\')
                    .replace(/'/g, "\\'")
                    .replace(c.interpolate, function (match, code) {
                        return "'," + code.replace(/\\'/g, "'") + ",'";
                    })
                    .replace(c.evaluate || null, function (match, code) {
                        return "');" + code.replace(/\\'/g, "'")
                            .replace(/[\r\n\t]/g, ' ') + "; __p.push('";
                    })
                    .replace(/\r/g, '')
                    .replace(/\n/g, '')
                    .replace(/\t/g, '')
                + "');}return __p.join('');";
            return tmpl;

            /** /
             var func = new Function('obj', tmpl);
             return data ? func(data) : func;
             /**/
        };

    define('tpl',[],function () {
        var tpl;

        var get, fs;
        if (typeof window !== "undefined" && window.navigator && window.document) {
            get = function (url, callback) {

                var xhr = tpl.createXhr();
                xhr.open('GET', url, true);
                xhr.onreadystatechange = function (evt) {
                    //Do not explicitly handle errors, those should be
                    //visible via console output in the browser.
                    if (xhr.readyState === 4) {
                        callback(xhr.responseText);
                    }
                };
                xhr.send(null);
            };
        } else if (typeof process !== "undefined" &&
            process.versions && !!process.versions.node) {
            //Using special require.nodeRequire, something added by r.js.
            fs = require.nodeRequire('fs');

            get = function (url, callback) {

                callback(fs.readFileSync(url, 'utf8'));
            };
        }
        return tpl = {
            version: '0.24.0',
            strip: function (content) {
                //Strips <?xml ...?> declarations so that external SVG and XML
                //documents can be added to a document without worry. Also, if the string
                //is an HTML document, only the part inside the body tag is returned.
                if (content) {
                    content = content.replace(xmlRegExp, "");
                    var matches = content.match(bodyRegExp);
                    if (matches) {
                        content = matches[1];
                    }
                } else {
                    content = "";
                }

                return content;
            },

            jsEscape: function (content) {
                return content.replace(/(['\\])/g, '\\$1')
                    .replace(/[\f]/g, "\\f")
                    .replace(/[\b]/g, "\\b")
                    .replace(/[\n]/g, "")
                    .replace(/[\t]/g, "")
                    .replace(/[\r]/g, "");
            },

            createXhr: function () {
                //Would love to dump the ActiveX crap in here. Need IE 6 to die first.
                var xhr, i, progId;
                if (typeof XMLHttpRequest !== "undefined") {
                    return new XMLHttpRequest();
                } else {
                    for (i = 0; i < 3; i++) {
                        progId = progIds[i];
                        try {
                            xhr = new ActiveXObject(progId);
                        } catch (e) {
                        }

                        if (xhr) {
                            progIds = [progId];  // so faster next time
                            break;
                        }
                    }
                }

                if (!xhr) {
                    throw new Error("require.getXhr(): XMLHttpRequest not available");
                }

                return xhr;
            },

            get: get,

            load: function (name, req, onLoad, config) {

                //Name has format: some.module.filext!strip
                //The strip part is optional.
                //if strip is present, then that means only get the string contents
                //inside a body tag in an HTML string. For XML/SVG content it means
                //removing the <?xml ...?> declarations so the content can be inserted
                //into the current doc without problems.

                var strip = false, url, index = name.indexOf("."),
                    modName = name.substring(0, index),
                    ext = name.substring(index + 1, name.length);

                index = ext.indexOf("!");

                if (index !== -1) {
                    //Pull off the strip arg.
                    strip = ext.substring(index + 1, ext.length);
                    strip = strip === "strip";
                    ext = ext.substring(0, index);
                }

                //Load the tpl.
                url = 'nameToUrl' in req ? req.nameToUrl(modName, "." + ext) : req.toUrl(modName + "." + ext);

                tpl.get(url, function (content) {
                    content = template(content);

                    if (!config.isBuild) {
                        //if(typeof window !== "undefined" && window.navigator && window.document) {
                        content = new Function('obj', content);
                    }
                    content = strip ? tpl.strip(content) : content;

                    if (config.isBuild && config.inlineText) {
                        buildMap[name] = content;
                    }
                    onLoad(content);
                });

            },

            write: function (pluginName, moduleName, write) {
                if (moduleName in buildMap) {
                    var content = tpl.jsEscape(buildMap[moduleName]);
                    write("define('" + pluginName + "!" + moduleName +
                        "', function() {return function(obj) { " +
                        content.replace(/(\\')/g, "'").replace(/(\\\\)/g, "\\") +
                        "}});\n");
                }
            }
        };
        return function () {
        };
    });
//>>excludeEnd('excludeTpl')
}());


define('tpl!app/templates/index/IndexView.html', function() {return function(obj) { var __p=[],print=function(){__p.push.apply(__p,arguments);};with(obj||{}){__p.push('<div class="photo-container"><div class="index-container"><div class="width-hide"><i class="scrollTop-icon clearfix bg-sprite-new"></i>        <a href="#ask-uploading-popup" class="ask-uploading-popup ask-uploading-popup-hide hide"><i class="askForP-icon clearfix bg-sprite-new upload-ask" ></i>        </a>        <a href="#login-popup" class="login-popup  hide login-popup-hide"><i class="askForP-icon clearfix bg-sprite-new "></i>        </a>        <div class="login-popup-contain clearfix">        <div class="login-demand-p">        <i class="bg-sprite-rebirth demand-icon"></i>        <span>发布求P</span>        </div>        <div class="login-upload">        <i class="bg-sprite-rebirth upload-icon"></i>        <span>上传作品</span>        </div>        </div></div><div class="index-contant"><div class="recommend-container"><div id="indexBannerView"  class="swipe" ></div></div><div class="hot-title">热门</div><div class="hot-container"><div id="indexItemView"></div></div></div></div></div>');}return __p.join('');}});

define('app/views/index/IndexView',['app/views/Base', 'tpl!app/templates/index/IndexView.html'],
    function (View, template) {
        "use strict";
        
        return View.extend({

            template: template,
            events: {
            	"click .scrollTop-icon" : "scrollTop",
                "mouseover .hot-picture": "indexFadeIn",
                "mouseleave .hot-picture": "indexFadeOut",
            },
            onRender: function() {
            	$(".tupai-index").addClass("active").siblings().removeClass("active");
                setTimeout(function(){
                    var id = $("body").attr("data-uid");
                    if( id ) {
                        $(".login-popup").addClass("hide");
                        $(".ask-uploading-popup-hide").removeClass('hide');
                    } else {
                        $(".ask-uploading-popup-hide").addClass('hide');
                        $(".login-popup").removeClass("hide");
                    }
                },500);
            },
            indexFadeIn: function(e) {
                $(e.currentTarget).find(".index-artwork").stop(true, true).fadeIn(1500);
                $(e.currentTarget).find(".index-work").stop(true, true).fadeOut(1500);
            },
            indexFadeOut: function(e) {
                $(e.currentTarget).find(".index-artwork").stop(true, true).fadeOut(1500);
                $(e.currentTarget).find(".index-work").stop(true, true).fadeIn(1500);
            },
        });
    });


define('tpl!app/templates/index/IndexItemView.html', function() {return function(obj) { var __p=[],print=function(){__p.push.apply(__p,arguments);};with(obj||{}){__p.push('<div class="hot-section">    '); if ( type == 1 && reply_count == 0 ) { ; __p.push('     <a target="_blank" href="#comment/ask/', id ,'">    '); } else if( type == 1 && reply_count > 0 ) { ; __p.push('    <a target="_blank" href="#replydetailplay/', id ,'">    '); } else { ; __p.push('    <a target="_blank" href="#replydetailplay/', ask_id ,'/', id ,'">    '); } ; __p.push('        <span class="hot-picture center-loading-image-container">        <span class="center-loading">        <span class="is-loading">        '); _.each(ask_uploads, function(ask) {  ; __p.push('            <img src="', ask.image_url ,'" alt="" class="index-artwork blo">            '); }) ; __p.push('            <img src="', image_url ,'" alt="" class="index-work">            </span>            </span>        </span></a><div class="hot-footer"><a  target="_blank" href="#homepage/reply/', uid ,'"><span class="header-portrait"><img src="', avatar ,'" alt=""></span></a><span class="hot-name">', nickname ,'</span><span class="hot-item-actionbar"><span class="browse"><span class="browse-icon bg-sprite"></span><span class="browse-count">', click_count ,'</span></span><span class="like"><span class="like-icon bg-sprite"></span><span class="like-count">', up_count ,'</span></span><span class="comment"><span class="comment-icon bg-sprite"></span><span class="comment-count">', comment_count ,'</span></span></span></div></div>');}return __p.join('');}});

define('app/views/index/IndexItemView',['app/views/Base', 'app/collections/Asks', 'tpl!app/templates/index/IndexItemView.html'],
    function (View, Asks,  template) {
        "use strict";

        var indexItemView = '.indexItem';
        
        return View.extend({
            tagName: 'div',
            className: 'indexItem',
            template: template,
            collection: Asks,
            construct: function() { 
                var self = this;
                self.listenTo(self.collection, 'change', self.render);
                self.collection.loading(self.showEmptyView);
            },
            render: function() {
                var template = this.template;

                this.collection.each(function(model){
                    var html = template(model.toJSON());
                    append(indexItemView, html);
                });
                this.onRender();
            }   
        });
    });


define('tpl!app/templates/index/IndexBannerView.html', function() {return function(obj) { var __p=[],print=function(){__p.push.apply(__p,arguments);};with(obj||{}){__p.push('<span class="recomment-section"><a target="_blank" href="', pc_url ,'"><span class="is-loading"><img src="', large_pic ,'" alt=""></span></a></span>');}return __p.join('');}});

define('app/views/index/IndexBannerView',[
        'app/views/Base', 
        'app/collections/Banners', 
        'tpl!app/templates/index/IndexBannerView.html'
        ],
    function (View, Banners, template) {
        "use strict";

        var indexBannerView = '#indexBannerView '+"div";
        
        return View.extend({
            tagName: 'div',
            className: 'swipe-wrap',
            template: template,
            collection: Banners,
            construct: function() { 
                var self = this;
                this.listenTo(this.collection, 'change', this.render);
                self.collection.loading();
            },
            render: function() {

                var template = this.template;

                this.collection.each(function(model){
                    var html = template(model.toJSON());
                    append(indexBannerView, html);
                });
                var widthScreen = $(window).width();
                if( widthScreen < 700 ) {
                    //append 里面有settimeout
                    setTimeout(function() {
                       Swipe(document.getElementById('indexBannerView'));    
                    }, 1200);
                }

                this.onRender();
            } 
        });
    });

define('app/controllers/Index',[
        'app/models/Ask', 
        'app/collections/Asks', 
        'app/collections/Banners', 
        'app/views/index/IndexView', 
        'app/views/index/IndexItemView', 
        'app/views/index/IndexBannerView', 
       ],
    function (Ask, Asks, Banners, IndexView, IndexItemView, IndexBannerView) {
        "use strict";

        return function() {

    

            setTimeout(function(){
                $("title").html("图派-首页");
                $('.header-back').removeClass("height-reduce");
            },100);
            
            var asks = new Asks;
            asks.url = '/populars';
            asks.data.size = 16;

            $('.header').removeClass("hide");
            $('.header-back').removeClass("height-reduce");

            var view = new IndexView({});
            window.app.content.show(view);
            
            var indexItem = new Backbone.Marionette.Region({el:"#indexItemView"});
            var view = new IndexItemView({
                collection: asks
            });
            indexItem.show(view);

            var banners = new Banners;
            
            var indexBanner = new Backbone.Marionette.Region({el:"#indexBannerView"});
            var view = new IndexBannerView({
                collection: banners
            });
            indexBanner.show(view);

            var widthHide = $(window).width();
                if( widthHide < 1281) {
                    $('.width-hide').addClass("hide");
                } else {
                    $('.width-hide').removeClass("hide");
                }
        };
    });


define('tpl!app/templates/ask/AskFlowsView.html', function() {return function(obj) { var __p=[],print=function(){__p.push.apply(__p,arguments);};with(obj||{}){__p.push('<div class="ask-mouseenter grid-item"><div class="ask-main opacity-animate-put"><span class="ask-picture"><a class="person-show" target="_blank" href="#askdetail/ask/', ask_id ,'">            <img src="', image_url ,'" alt="', nickname ,'" width="', image_width ,'" height="'); image_width*image_ratio ; __p.push('">            '); if( ask_uploads.length > 1 ) {; __p.push('            <span class="bg-sprite-new"></span>            '); } ; __p.push('        </a></span><span class="person-message-1 opacity-animate"><a class="person-show" target="_blank" href="#askdetail/ask/', ask_id ,'"><ul ><a target="_blank" href="#homepage/reply/', uid ,'"><li class="avatar" data-status="0"><span class="avatar-border"><img src="', avatar ,'" alt=""></span></li></a><li class="message"> <a target="_blank" href="#homepage/reply/', uid ,'"><span class="name">', nickname ,'</span></a><a class="person-show" href="#comment/ask/', ask_id ,'"><p class="ask-desc">', desc ,'</p></a></li><a data-type="', type ,'" data-id="', id ,'" class="download"><i class="download-icon bg-sprite-new"></i></a></ul></a></span></div><span class="person-message"><ul class="clearfix"><li class="avatar"><a target="_blank" href="#homepage/reply/', uid ,'"><span class="avatar-border"><img src="', avatar ,'" alt="', nickname ,'"></span></a></li><li class="message"><a target="_blank" href="#homepage/reply/', uid ,'"><span class="name">', nickname ,'</span></a><span class="ask-desc">', desc ,'</span></li></ul></span></div>');}return __p.join('');}});

    define('app/views/ask/AskFlowsView',['imagesLoaded',
    		'app/views/Base',
    		'app/collections/Asks', 
    		'tpl!app/templates/ask/AskFlowsView.html'
	       ], function (imagesLoaded, View, Asks, template) {

        "use strict";
        
        return View.extend({
            collection: Asks,
            tagName: 'div',
            className: 'ask-container grid',
            template: template,
            events: {
                "click .download" : "download",
            },
            construct: function () {
                this.listenTo(this.collection, 'change', this.renderMasonry);
                this.scroll();
                this.collection.loading();
                
                $(document).on('mouseenter', '.person-message-1', function() {
                    $(this).animate({
                        'opacity' : 1
                    },11);
                    $(this).parents('.grid-item').find('.person-message').animate({
                        'opacity' : 0
                    },100);
                });
                $(document).on('mouseleave', '.person-message-1', function() {
                    $(this).animate({
                        'opacity' : 0
                    },200);
                    $(this).parents('.grid-item').find('.person-message').animate({
                        'opacity' : 1
                    },500);
                });
                $(document).on('mouseenter', '.ask-main', function() {
                    $(this).addClass('hover');
                    $(this).parents('.grid-item').find('.person-message').addClass('hide');
                });
                $(document).on('mouseenter', '.ask-desc', function() {
                    $(this).parents('.grid-item').find('.ask-main').addClass('hover');
                    $(this).parents('.grid-item').find('.person-message').addClass('hide');
                });
                $(document).on('mouseleave', '.ask-mouseenter', function() {
                    $(this).find('.person-message').removeClass('hide');
                    
                });
            },
            render: function() {
				this.renderMasonry();                	
		
            }
        });
    });

define('app/controllers/AskFlows',['underscore', 'app/collections/Asks', 'app/views/ask/AskFlowsView'],
    function (_, Asks, AskFlowsView) {
        "use strict";

        return function(category_id) {

            var category_id = category_id;            

            setTimeout(function(){
                $(".upload-ask").attr("data-id",category_id);
            },1000);

            setTimeout(function(){
                $("title").html("图派-原图");
                $('.header-back').removeClass("height-reduce");
            },100);

            var asks = new Asks;
            asks.data.width = 300;

            var view = new AskFlowsView({
                collection: asks
            });

            window.app.content.show(view);
        };
    });

define('app/models/Reply',['app/models/Base'], function(Model) {
    return Model.extend({
        url: '/reply/',
        defaults: {
            id: ' ',
            ask_id: ' ',
            type: 1,
            is_follow: false,
            is_download: false,
            uped: false,
            collected: false,
            avatar: "",
            sex: ' ',
            uid: ' ',
            nickname: ' ',
            upload_id: ' ',
            create_time: ' ',
            update_time: ' ',
            desc: ' ',
            up_count: ' ',
            comment_count: ' ',
            image_ratio: ' ',
            collect_count: 0,
            click_count: ' ',
            inform_count: 0,
            share_count: ' ',
            weixin_share_count: ' ',
            reply_count: 0,
            ask_uploads: [],
            image_url: "",
            image_width: 480,
            image_height: 480
        },
        construct: function() {

        }
    });

}); 

define('app/collections/Replies',['app/collections/Base', 'app/models/Reply'], function(Collection, Reply) {
    return Collection.extend({
        model: Reply,
        url: '/replies'
     });
}); 

define('app/models/Like',['app/models/Base'], function(Model) {
    return Model.extend({
        url: '/like/',
        defaults: {
            id: 0,
            type: 0,
            status: 0
        },
        construct: function() {

        },
        save: function(callback){ 
            this.fetch({
                data: this.toJSON(),
                success: function(data) {
                    callback && callback(data);
                }
            });
        }
    });

}); 


define('tpl!app/templates/reply/ReplyFlowsView.html', function() {return function(obj) { var __p=[],print=function(){__p.push.apply(__p,arguments);};with(obj||{}){__p.push('<div class="reply-item grid-item"><div class="reply-head"><span class="reply-avatar"><a target="_blank" href="#homepage/reply/', uid ,'"><img src="', avatar ,'" alt="头像"></a></span><span class="reply-name"><a href="#homepage/reply/', uid ,'">', nickname ,'</a></span><span class="reply-create-time">'); var timeMatrixing = time(create_time); ; __p.push('', timeMatrixing ,'</span></div><div class="reply-main" time="1"><a target="_blank" href="#replydetailplay/', ask_id ,'/', id ,'" class="clearfix reply-pic"><img src="', image_url ,'" data-type="2" alt="', nickname ,'" alt="作品"> '); _.each(ask_uploads, function(ask) {  ; __p.push('<img src="', ask.image_url ,'" data-type="1" alt="', nickname ,'" alt="作品">'); }) ; __p.push('</a></div><div class="reply-footer"><div class="nav"><span class="pressed reply-nav nav-pressed" ask="0">作品</span>'); if( ask_uploads.length > 1) { ; __p.push('<span class="pressed ask-nav" ask="1">原图1</span><span class="pressed ask-nav" ask="2"> 原图2</span>'); } else { ; __p.push('<span class="pressed ask-nav" ask="2"> 原图</span>'); } ; __p.push('<var class="nav-bottom"></var></div><div class="reply-section-icon"><span class="like"> <i class="like-icon bg-sprite-new like_toggle ', (uped)? 'liked': '' ,'" data-type="', type ,'" data-id="', id ,'"></i> <em class="like-count">', up_count ,'</em></span><a href="#replydetailplay/', ask_id ,'/', id ,'" class="comment"><i class="comment-icon bg-sprite-new"></i><em class="comment-count">', comment_count ,'</em></a></div></div></div>');}return __p.join('');}});

 define('app/views/reply/ReplyFlowsView',[
        'masonry', 
        'imagesLoaded',
        'app/views/Base',
        'app/models/Base',
        'app/models/Like',  
        'app/collections/Replies', 
        'tpl!app/templates/reply/ReplyFlowsView.html'
       ],
    function (masonry, imagesLoaded, View, ModelBase, Like, Replies, template) {

        "use strict";
        return View.extend({
            collection: Replies,
            tagName: 'div',
            className: 'reply-container grid',
            template: template,
            events: {
                "click .like_toggle" : 'likeToggle',
                "click .pressed" : 'pressed',
                "mouseenter .reply-main" : 'replyScroll',
                "mouseleave .reply-main" : 'replyScroll',
            },
            replyScroll : function(e) {
                var length = $(e.currentTarget).children().children("img").length;
                var targetVal       = $(e.currentTarget).width() * (length - 1);
                var navTargetVal    = Math.abs(($(e.currentTarget).siblings(".reply-footer").find(".nav").width() - $(".nav-bottom").width()) / targetVal);
                var time            = $(e.currentTarget).attr("time");
                var speed           = 2;

                if (e.type == "mouseenter") {             
                    if (time) {
                        clearInterval(time);
                    };
                    var startVal = $(e.currentTarget).scrollLeft();

                    time = setInterval(function() {
                        startVal += speed;
                        var scroll = Math.round(startVal / $(e.currentTarget).width());
                        $(e.currentTarget).siblings(".reply-footer").find(".nav").children("span").removeClass("nav-pressed");
                        $(e.currentTarget).siblings(".reply-footer").find(".nav").children("span").eq(scroll).addClass("nav-pressed");
                        
                        if (startVal >= targetVal) {
                            clearInterval(time);
                            startVal = targetVal;
                        };
                        $(e.currentTarget).scrollLeft(startVal);
                        $(e.currentTarget).siblings(".reply-footer").children().children(".nav-bottom").css({
                            left: startVal * navTargetVal + "px"
                        });
                        $(e.currentTarget).attr("time", time);
                    }, 1);
                };
                if (e.type == "mouseleave") {
                    if (time) {
                        clearInterval(time);
                    };
                    var startVal = $(e.currentTarget).scrollLeft();

                    time = setInterval(function() {
                        startVal -= speed;
                        var scroll = Math.round(startVal / $(e.currentTarget).width());
                        $(e.currentTarget).siblings(".reply-footer").find(".nav").children("span").removeClass("nav-pressed");
                        $(e.currentTarget).siblings(".reply-footer").find(".nav").children("span").eq(scroll).addClass("nav-pressed");
                        if (startVal <= 0) {
                            clearInterval(time);
                            startVal = 0;
                        };
                        $(e.currentTarget).scrollLeft(startVal);
                        $(e.currentTarget).siblings(".reply-footer").children().children(".nav-bottom").css({
                            left: startVal * navTargetVal + "px"
                        });
                        $(e.currentTarget).attr("time", time);
                    }, 1);
                };        
            },            
   
            pressed: function(e) {
                $(e.currentTarget).addClass("nav-pressed").siblings().removeClass("nav-pressed");
                var index = $(e.currentTarget).index();
                $(e.currentTarget).parents(".reply-footer").siblings(".reply-main").scrollLeft(index * 280);
                $(e.currentTarget).siblings(".nav-bottom").animate({
                    left: index * $(e.currentTarget).width() + "px"
                })
                $(e.currentTarget).addClass('nav-pressed').siblings().removeClass('nav-pressed');
            },
            construct: function () {
                this.listenTo(this.collection, 'change', this.renderMasonry);
                this.scroll();
                this.collection.loading();
            },
            render: function() {
                this.renderMasonry();
            }
        });
    });

define('app/controllers/ReplyFlows',['underscore', 'app/collections/Replies', 'app/views/reply/ReplyFlowsView'],
    function (_, Replies, ReplyFlowsView) {
        "use strict";

        return function() {
            setTimeout(function(){
                $("title").html("图派-作品");
                $('.header-back').removeClass("height-reduce");
            },100);

            var replies = new Replies;
            // replies.data.width = 300;

            var view = new ReplyFlowsView({
                collection: replies
            });

            window.app.content.show(view);
        };
    });

define('app/models/Message',['app/models/Base'], function(Model) {
    return Model.extend({
        url: '/message/',
        defaults: {
            id: "",
            sender: "",
            update_time: "",
            target_type: "",
            target_id: "",
            target_ask_id: "",
            content: "",
            desc: "",
            pic_url: "http://7u2spr.com1.z0.glb.clouddn.com/20150410-15385455277e0e92ff7.jpg",
            nickname: "",
            avatar: "http://7u2spr.com1.z0.glb.clouddn.com/20150403-153144551e41e02770e.jpg",
            sex: 1,
            reply_id: "",
            comment_id: "",
            ask_id: "",
            type: "",
            for_comment: ''
        },
        construct: function() {

        }
    });

}); 

define('app/collections/Messages',['app/collections/Base', 'app/models/Message'], function(Collection, Message) {
    return Collection.extend({
        model: Message,
        url: '/messages',
        initialize: function() {
            this.data = {
                type: 'normal',
                page: 0,
                size: 10
            }
        }
     });
}); 


define('tpl!app/templates/message/MessageView.html', function() {return function(obj) { var __p=[],print=function(){__p.push.apply(__p,arguments);};with(obj||{}){__p.push('<div class="message-container"><div class="message"><div class="message-title">消息</div>        <div class="nav comment-nav ', (type=='comment')? 'nav-pressed':'' ,'" data="comment">            <i class="comment-icon  comment-icon-pressed bg-sprite-new"></i>            <span class="comment">评论</span>        </div><div class="nav paste-nav ', (type=='reply')? 'nav-pressed':'' ,'" data="reply"><i class="paste-icon paste-icon-pressed bg-sprite-new"></i><span class="paste">帖子回复</span></div><div class="nav attenion-nav ', (type=='follow')? 'nav-pressed':'' ,'" data="follow"><i class="attention-icon attention-icon-pressed bg-sprite-new"></i><span class="attenion">关注通知</span></div><div class="nav system-nav ', (type=='system')? 'nav-pressed':'' ,'" data="system"><i class="system-icon system-icon-pressed bg-sprite-new"></i><span class="system">系统消息</span></div>    </div>    <div id="message-item-list" class="message-section">        <!--        <div class="title">            评论        </div>        -->    </div></div>');}return __p.join('');}});

define('app/views/message/MessageView',['app/views/Base', 'tpl!app/templates/message/MessageView.html'],
         
    function (View, template) {
        "use strict";
        
        return View.extend({
            tagName: 'div',
            className: '',
            template: template,
            events: {
                'click .message .nav' : 'switchNav',
            },
            construct: function() {
                $("a.menu-bar-item").removeClass('active');
                this.listenTo(this.model, "change", this.render);
            },
            switchNav: function(e) {
                var self = this;
                var type = $(e.currentTarget).attr('data');
                location.href = '/#message/' + type;
            }
        });
    });


define('tpl!app/templates/message/MessageItemView.html', function() {return function(obj) { var __p=[],print=function(){__p.push.apply(__p,arguments);};with(obj||{}){__p.push('<div class="message-detail">    <a target="_blank" href="#homepage/reply/', sender ,'">        <span class="head">            <img src="', avatar ,'" alt="">        </span>    </a>    <span class="message-person">        <a target="_blank" href="#homepage/reply/', sender ,'">            <span class="name">', nickname ,'</span>        </a>        <span class="time">'); var timeMatrixing = time(update_time); ; __p.push('', timeMatrixing ,'</span>    </span>    <p class="message-content">        ', content ,'    </p>    <span class="picture">        '); if(pic_url ) {; __p.push('            <a target="_blank" href="/#replydetailplay/', ask_id ,'/', target_id ,'">                <img src="', pic_url ,'" alt="" />            </a >        '); } ; __p.push('    </span></div>');}return __p.join('');}});

define('app/views/message/MessageItemView',[
        'app/views/Base', 
        'tpl!app/templates/message/MessageItemView.html'
        ],
    function (View, template) {
        "use strict";
        
        return View.extend({
            tagName: 'div',
            className: '',
            template: template,
            construct: function() {
                $("a.menu-bar-item").removeClass('active');

                this.listenTo(this.collection, "change", this.render);

                this.scroll();
                this.collection.loading(this.showEmptyView);
            },
            showEmptyView: function(data) {
                if(data.data.page == 1 && data.length == 0) {
                    append($("#contentView div"), ".emptyContentView");
                } 
            },
        });
    });


define('tpl!app/templates/message/CommentItemView.html', function() {return function(obj) { var __p=[],print=function(){__p.push.apply(__p,arguments);};with(obj||{}){__p.push('<div class="message-detail">    <a target="_blank" href="#homepage/reply/', sender ,'">        <span class="head">            <img src="', avatar ,'" alt="">        </span>    </a>    <span class="message-person">        <a target="_blank" href="#homepage/reply/', sender ,'">            <span class="name">', nickname ,'</span>        </a>        <span class="time">'); var timeMatrixing = time(update_time); ; __p.push('', timeMatrixing ,'</span>    </span>    <p class="message-content" >        ', content ,'    </p>    ');      var title = '回复了我的图片';     var tag = '原图';     if(for_comment != 0) {          title = '回复了我的评论';     } else if(target_type == 2) {         tag = '作品';     }     ; __p.push('    <div class="call-back-container">        <div class="call-back">            <span class="call-back-content">                <span class="call-back-comment">', title ,':</span>                <span class="comment-content">', desc ,'</span>                '); if (for_comment == 0) { ; __p.push('                <div class="photo-item-reply">                    <div class="photo-item-reply-work">                        <i class="bookmark">', tag ,'</i>                        ');  if(target_type == 1) { ; __p.push('                            <a class="person-show" target="_blank" href="#askdetail/ask/', target_ask_id ,'">                        '); } else { ; __p.push('                            <a target="_blank" href="/#replydetailplay/', target_ask_id ,'/', target_id ,'">                        '); } ; __p.push('                                <img src="', pic_url ,'" alt="', desc ,'" data-type ="', type ,'">                            </a>                    </div>                </div>                '); } ; __p.push('                <div class="clear"></div>            </span>            <span class="reply-comment">回复</span>        </div>        <div class="comment-frame hide">            <textarea maxlength="100" name="" id="commentContent"  target-id="', target_id ,'" data-sender="', sender ,'" comment-id="', comment_id ,'" type="', target_type ,'" placeholder="回复评论...."></textarea>            <span class="comment-btn right">评论</span>           </div>    </div></div>');}return __p.join('');}});

define('app/views/message/CommentItemView',[
        'app/views/Base', 
        'tpl!app/templates/message/CommentItemView.html'
        ],
    function (View, template) {
        "use strict";
        
        return View.extend({
            tagName: 'div',
            className: '',
            template: template,
            events: {
                'click .reply-comment' : 'replyComment',
                'click .comment-btn' : 'CommentBtn'
            },
            CommentBtn:function(e) {
                var el = $(e.currentTarget).siblings('#commentContent');
                
                var content = el.val();
                var sender = el.attr('data-sender');
                var type = el.attr('type');
                var comment_id = el.attr('comment-id');
                var target_id = el.attr('target-id');

                var url = "/comments/save";

                var postData = {
                    'content': content,
                    'type' : type,
                    'id': target_id,
                    'reply_to' : sender,
                    'for_comment' : comment_id
                };
                $.post(url, postData, function( returnData ){
                    var info = returnData.info;
                    if( returnData.ret == 1 ) {
                        $(e.currentTarget).parents('.comment-frame').addClass('hide');
                        $(e.currentTarget).parents('.call-back-container').find('.reply-comment').text('回复');
                        toast('回复评论成功');
                        console.log(returnData.ret);
                        // window.location.reload()
                    } 
                });
            },
            replyComment:function(e) {
                $(e.currentTarget).parents('.call-back').siblings('.comment-frame').toggleClass('hide');
                var has = $(e.currentTarget).parents('.call-back').siblings('.comment-frame').hasClass('hide');
                if(has) {
                    $(e.currentTarget).text('回复');
                } else {
                    $(e.currentTarget).text('收起');
                }
            },
            construct: function() {
                var self = this;
                $("a.menu-bar-item").removeClass('active');

                this.listenTo(this.collection, "change", this.render);

                self.scroll();
                self.collection.loading(self.showEmptyView);
            },
        });
    });

define('app/controllers/Message',[
        'app/models/Message',
        'app/collections/Messages',
        'app/views/message/MessageView', 
        'app/views/message/MessageItemView',
        'app/views/message/CommentItemView'
	   ],
    function ( Message, Messages, MessageView, messageItemView, CommentItemView) {
        "use strict";

        return function(type, uid) {

            setTimeout(function(){
                $("title").html("图派-消息");
                $('.header-back').removeClass("height-reduce");
            },100);

            var messages = new Messages;
            if(!type) type = 'comment';
            messages.data.type = type;

            var message = new Message({type: type});
            var view = new MessageView({model: message});
            window.app.content.show(view);


            if( type != 'comment') {
                var messageListRegion = new Backbone.Marionette.Region({el:"#message-item-list"});
                var view = new messageItemView({
                    collection: messages 
            });
                messageListRegion.show(view);
                
            } else {

            var commentListRegion = new Backbone.Marionette.Region({el:"#message-item-list"});
            var view = new CommentItemView({
                collection: messages 
            });
                commentListRegion.show(view);

            }

            
        };
    });


define('tpl!app/templates/trend/TrendView.html', function() {return function(obj) { var __p=[],print=function(){__p.push.apply(__p,arguments);};with(obj||{}){__p.push('<div class="trend-inner-container"><div class="dynamics-section clearfix"><div class="width-hide"><i class="scrollTop-icon clearfix bg-sprite-new"></i>        <a href="#ask-uploading-popup" class="ask-uploading-popup ask-uploading-popup-hide hide"><i class="askForP-icon clearfix bg-sprite-new upload-ask" ></i>        </a>        <a href="#login-popup" class="login-popup  hide login-popup-hide"><i class="askForP-icon clearfix bg-sprite-new "></i>        </a>        <div class="login-popup-contain clearfix">        <div class="login-demand-p">        <i class="bg-sprite-rebirth demand-icon"></i>        <span>发布求P</span>        </div>        <div class="login-upload">        <i class="bg-sprite-rebirth upload-icon"></i>        <span>上传作品</span>        </div>        </div></div><div class="dynamics-person"><a target="_blank" href="#homepage/reply/', uid ,'"><img src="', avatar ,'" alt="', avatar ,'"><span class="name">', nickname ,'</span></a></div><div class="dynamics-right-content"><div class="slice"></div><div class="dynamics-content"><div class="dynamics-header clearfix"><p> '); if( type == 1 ) {; __p.push('                上传了一张原图，并说：                '); } ; __p.push('                '); if( type == 2 ) {; __p.push('                上传了一张作品，并说：                '); } ; __p.push('            </p><div class="dynamics-time"><em class="time-icon bg-sprite-new"></em><span>', time(create_time) ,'</span></div></div><div class="reply-content">“<p>', desc ,'</p>”</div></div><!-- 原图作品 --><div class="picture-content clearfix"><!-- <div class="pic-center"> -->'); if( type == 2 ) {; __p.push('<div class="reply-picture"><a target="_blank" href="#replydetailplay/', ask_id ,'/', id ,'"><img src="', image_url ,'" alt=""><span class="reply-icon bg-sprite-new"></span></a></div><div class="old-pic"><a target="_blank" href="#askdetail/ask/', ask_id ,'">'); _.each(ask_uploads, function(ask_upload){ ; __p.push('<img src="', ask_uploads[0].image_url ,'" alt=""><span class="old-icon bg-sprite-new"></span>'); }) ; __p.push('</a></div>'); } else { ; __p.push('<div class="old-pic-two"><a target="_blank" href="#askdetail/ask/', ask_id ,'">'); _.each(ask_uploads, function(ask_upload){ ; __p.push(''); if(ask_upload.image_url) { ; __p.push('<img src="', ask_uploads[0].image_url ,'" alt="">');  } ; __p.push(''); }) ; __p.push('<span class="old-icon bg-sprite-new old-icon"></span></a></div>'); } ; __p.push(' </div><!-- 功能 --><div class="trend-actionbar clearfix"><!-- 点赞 -->'); if( type == 2 ) { ; __p.push('<div class="like-actionbar like like_toggle" data-type="', type ,'" data-id="', id ,'"><i class="trend-icon bg-sprite-new" ></i><em class="like-count ', (uped)? 'like-color': '' ,'">', up_count ,'</em></div>'); } else { ; __p.push('<div class="P-actionbar download" data-type="', type ,'" data-id="', id ,'"><i class="trend-icon bg-sprite-new"></i><em>BANG</em></div>'); } ; __p.push('<!-- 评论 --><!-- 微博分享按钮 --><wb:share-button appkey="1211791030" addition="simple" type="button" ralateUid="5738008040" default_text="分享" pic="http%3A%2F%2F7u2spr.com1.z0.glb.clouddn.com%2F20151118-180636564c4daca91ad.jpg%3FimageView2%2F2%2Fw%2F480||http%3A%2F%2F7u2spr.com1.z0.glb.clouddn.com%2F20151118-180636564c4daca91ad.jpg%3FimageView2%2F2%2Fw%2F480"></wb:share-button>'); if( type == 1 ) { ; __p.push('<a target="_blank" href="#comment/ask/', ask_id ,'" class="comment-actionbar"><i class="trend-icon bg-sprite-new"></i><em>', comment_count ,'</em></a><div class="share-actionbar">'); } else { ; __p.push('<a target="_blank" href="#replydetailplay/', ask_id ,'/', id ,'" class="comment-actionbar"><i class="trend-icon bg-sprite-new"></i><em>', comment_count ,'</em></a>'); } ; __p.push('<div class="share-actionbar"><i class="trend-icon bg-sprite-new"></i><em>', share_count ,'</em></div><div class="trend-share"><a class="trend-weibo" href=""><i class="bg-sprite-new"></i><em>新浪微博</em></a><a class="trend-qq" href=""><i class="bg-sprite-new"></i><em>QQ空间</em></a><a class="share" href=""></a></div></div></div></div></div>');}return __p.join('');}});

define('app/views/trend/TrendView',[
        'app/views/Base', 
        'tpl!app/templates/trend/TrendView.html'
       ],
    function (View,  template) {
        "use strict";
        
        return View.extend({
            tagName: 'div',
            className: '',
            template: template,
            events: {
                "click .like_toggle" : 'likeToggleLarge',
            },
            construct: function () {
                this.listenTo(this.collection, 'change', this.render);
                this.scroll();
                this.collection.loading(this.showEmptyView);
            },
            onRender: function() {
                $('.download').unbind('click').bind('click',this.download);
                this.loadImage(); 
            }

        });
    });

define('app/controllers/Trend',['underscore','app/views/trend/TrendView','app/collections/Replies'],
    function (_, trendView, Replies) {
        "use strict";

        return function() {

            setTimeout(function(){
                $("title").html("图派-动态页面");
                $('.header-back').removeClass("height-reduce");
            },100);

        	var replies = new Replies;
        	replies.url = 'timeline';
            var view = new trendView({collection: replies});
            
            window.app.content.show(view);
        
        };
    });

define('app/models/User',['app/models/Base'], function(Model) {
    return Model.extend({
        url: '/users',
        data: {
            uid: 0
        },
        defaults: {
            uid: "",
            username: "",
            nickname: "",
            phone: "",
            sex: 0,
            avatar: "http://7u2spr.com1.z0.glb.clouddn.com/20151118-205001564c73f9ca9be.png",
            uped_count: "0",
            current_score: 0,
            paid_score: 0,
            total_praise: 0,
            location: "",
            province: "",
            city: "",
            badges_count: "",
            bg_image: null,
            status: 1,
            is_bound_weixin: 0,
            is_bound_qq: 0,
            is_bound_weibo: 0,
            weixin: "",
            weibo: "",
            qq: "",
            fans_count: 0,
            fellow_count: 0,
            ask_count: 0,
            reply_count: 0,
            inprogress_count: 0,
            collection_count: 0,
            is_follow: 0,
            is_fan: 0,
            has_invited: false,
            replies: [ ]
        },
        construct: function() {

        }

    });
}); 


define('tpl!app/templates/SettingView.html', function() {return function(obj) { var __p=[],print=function(){__p.push.apply(__p,arguments);};with(obj||{}){__p.push('  <div class="setting-container">  <div class="setting-section"><div class="setting-header">    <ul>      <li class="base-nav base-data ', (type == 'base')? 'nav-bottom': '' ,'" data-type="base" >基本资料</li>      <li class="base-nav base-data ', (type == 'safety')? 'nav-bottom': '' ,'" data-type="safety">账号与安全</li>    </ul></div>      <div id="settingContent"></div>  </div></div>');}return __p.join('');}});

define('app/views/SettingView',['common', 'app/views/Base', 'tpl!app/templates/SettingView.html'],
    function (common, View, template) {
        "use strict";
        
        return View.extend({
            tagName: 'div',
            className: '',
            template: template,
            events: {
                'click .base-nav' : 'navBar',
            },
 			navBar: function(e) {
                var type = $(e.currentTarget).attr('data-type');
                    location.href = '#setting/'+ type;
                    setTimeout(function(){
                        location.reload();
                    },100);
                // $(".number").html($(".number").html().substring(0,3)+"****"+$(".number").html().substring(7,11));
            },
        });
    });


define('tpl!app/templates/BaseMaterialView.html', function() {return function(obj) { var __p=[],print=function(){__p.push.apply(__p,arguments);};with(obj||{}){__p.push('<div class="setting-head"><span class="head">头像:</span><span class="head-picture">      <img src="', avatar ,'" alt="">      <input id="upload_avatar" type="file" accept="image/gif, image/jpeg" class="" ></span></div><div class="setting-nickname"><span class="nickname">昵称:</span><span class="nickname-amend"><span class="nickname-input"><input type="text" class="" value="', nickname ,'" /></span><span class="nickname-remind">推荐使用中文昵称，5-25个字符，1个中算2个字符</span></span></div><div class="setting-sex"><span class="sex">性别:</span><label class="option-boy"><input type="radio" id="select-boy" class="bg-defaul bg-sprite-new '); if(sex == 1){ ; __p.push(' boy-pressed '); } ; __p.push('" name="sex"/><span class="boy">男</span></label><label class="option-girl"><input type="radio" id="select-girl" class="bg-defaul bg-sprite-new '); if(sex == 0){ ; __p.push('girl-pressed '); } ; __p.push('" name="sex"/><span class="girl">女</span></label></div><div id="" class="submit-btn disable">提交</div>');}return __p.join('');}});

define('app/views/BaseMaterialView',['common', 'app/views/Base', 'tpl!app/templates/BaseMaterialView.html'],
    function (common, View, template) {
        "use strict";
        
        return View.extend({
            tagName: 'div',
            className: '',
            template: template,
            events: {
                'click .submit' : 'submit',
                'click #select-girl' : 'selectGirl',
                'click #select-boy' : 'selectBoy',
                'keyup .nickname-input' : 'keyupNickename',
                'click .setting-sex input' : 'ChangeSex'
            },
           
            onRender: function() {
                Common.upload("#upload_avatar", function(data){
                    $(".head-picture img").attr('src', data.data.url);
                    $('.submit-btn').addClass('bg-submit submit');
                }, null, {
                     url: '/upload'
                });
            },
            ChangeSex: function() {
                    $('.submit-btn').addClass('bg-color submit');
            },
            keyupNickename: function() {
                var nickname = $(".nickname-input input").val();
                if (nickname == '') {

                    alert('昵称不能为空哦');
                    $('.submit-btn').removeClass('bg-color submit');

                    return false;
                }else {
                    $('.submit-btn').addClass('bg-color submit');
                }
            },
            construct: function() {
                this.listenTo(this.model, "change", this.render);
            },
            selectBoy: function(e) {
                var el = e;
                $(el.currentTarget).addClass('boy-pressed').parent().parent().find('#select-girl').removeClass('girl-pressed');
                
            },
            selectGirl: function(e) {
                var el = e;
                $(el.currentTarget).addClass('girl-pressed').parent().parent().find('#select-boy').removeClass('boy-pressed');

            },
            submit: function() {
                //todo: 这里存放id
                var avatar   = $(".head-picture img").attr('src');
                var nickname = $(".nickname-input input").val();
                var sex = ($(".setting-sex input[type='radio']:checked").attr('id') == 'select-boy')? 1: 0;

                if (nickname == '') {
                    error('操作失败', '昵称不能为空哦');
                    return false;
                }

                $.post('/user/save', {
                    avatar: avatar,
                    nickname: nickname,
                    sex: sex
                }, function(data) {
                    var img = $(".head-picture img").attr('src');
                    $(".user-avatar img").attr('src', img);
                    toast('修改成功');
                });
            }
        });
    });


define('tpl!app/templates/UserSafetyView.html', function() {return function(obj) { var __p=[],print=function(){__p.push.apply(__p,arguments);};with(obj||{}){__p.push('<div class="userSafety-container"><div class="login-message"><span class="user">登录账号:</span><span class="number">', phone ,'</span></div><div class="password"><span class="user-pwd">账号密码:</span><a href="#amend-popup" class="amend-popup"><span class="change-pwd">修改密码</span></a></div><!-- <div class="social-user"><span class="social">社交账号:</span><span class="weibo"><i class="weibo-icon bg-sprite-new"></i><b>微博绑定</b></span><span class="weixin"><i class="weixin-icon bg-sprite-new"></i><b>微信绑定</b></span><span class="QQ"><i class="QQ-icon bg-sprite-new"></i><b>QQ绑定</b></span></ul></div> --></div>');}return __p.join('');}});

define('app/views/UserSafetyView',['common', 'app/models/User', 'app/views/Base', 'tpl!app/templates/UserSafetyView.html'],
    function (common, User,  View, template) {
        "use strict";
        
        return View.extend({
            tagName: 'div',
            className: '',
            template: template,
            models: User,
            construct: function() {
                this.listenTo(this.model, "change", this.render);
            },
            onRender: function() {
                setTimeout(function() {
                    $(".number").html($(".number").html().substring(0,3)+"****"+$(".number").html().substring(7,11));
                }, 50)
            }
        });
    });

define('app/controllers/Setting',['underscore', 
        'app/models/User', 
        'app/views/SettingView',
        'app/views/BaseMaterialView',
        'app/views/UserSafetyView',
       ],
    function (_, User, SettingView, BaseMaterialView, UserSafetyView) {
        "use strict";

        return function(type) {
            setTimeout(function(){
                $("title").html("图派-设置");
                $('.header-back').removeClass("height-reduce");
            },100);

            var user = new User({type: type});
            user.url = 'user/status?settings';
            user.fetch();

            var view = new SettingView({ model: user });
            window.app.content.show(view);

             var baseMaterialRegion = new Backbone.Marionette.Region({el:"#settingContent"});
             var base_view = new BaseMaterialView({
                 model: user
             });

             var safetyMaterialRegion = new Backbone.Marionette.Region({el:"#settingContent"});
             var safety_view = new UserSafetyView({
                 model: user
             });

            switch(type) {
            case 'base':
                baseMaterialRegion.show(base_view);
                break;
            case 'safety':
                safetyMaterialRegion.show(safety_view);
                break;
            default:
                baseMaterialRegion.show(base_view);
                break;
            }
        };
    });

define('app/models/Comment',['app/models/Base'], function(Model) {
    return Model.extend({
        url: '/comment/',
        defaults: {
            uid: 1,
            avatar: "http://7u2spr.com1.z0.glb.clouddn.com/20151118-205001564c73f9ca9be.png",
            sex: 1,
            reply_to: 0,
            for_comment: 0,
            comment_id: 0,
            nickname: '',
            content: '123',
            up_count: 0,
            down_count: 0,
            inform_count:0,
            create_time: ' ',
            at_comment: [ ],
            target_id: 1,
            target_type: 1,
            uped: false
        },
        construct: function() {
        }
    });

}); 

define('app/collections/Comments',['app/collections/Base', 'app/models/Comment'], function(Collection, Comment) {
    return Collection.extend({
        model: Comment,
        url: '/comments',
        initialize: function() {
            this.data = {
                type: 1,
                target_id: 1,
                page: 0,
                size: 10
            }
        }
     });
}); 


define('tpl!app/templates/askdetail/AskDetailPlayView.html', function() {return function(obj) { var __p=[],print=function(){__p.push.apply(__p,arguments);};with(obj||{}){__p.push('<div class="reply-detail-content"><div class="detail-content clearfix"><!-- 左边 --><div class="replydetail-pic"><div class="reply-pic"><div class="dig-pic center-image"><div class="center-loading-img"><div class="img-loading"><img id="bigPic" src="" alt=""><span class="bg-sprite-new" id="bgIcon"></span></div></div></div><div class="reply-left bg-sprite-new" id="askDetailLeft"></div><div class="reply-right bg-sprite-new" id="askyDetailRight"></div></div><!-- 底部 --><div class="work-pic"><div class="detail-pic clearfix" originalNum="0"><div class="old-pic old-click center-loading-image-container pic-scroll" data-type="1" data-id="', id ,'"><span class="center-loading"><img src="', image_url ,'" alt=""><i class="bg-sprite-new old-icon"></i></span></div></div></div></div><div class="reply-detail-ifo"><div class="reply-more blo">查看更多评论</div><div class="right-content"><!-- 信息 --><div class="reply-user clearfix" ><div id="replyDetailPersonView" class="clearfix"></div><div class="share clearfix"><i class="share-icon bg-sprite-new"></i><em>222</em></div><div id="action"></div></div><!-- 评论 --><div class="reply-comment clearfix"><div id="count"></div><div class="comment-content clearfix"><div class="reply-inp clearfix"><input type="text" maxlength="101" name="" cols="30" rows="10" id="textInp" placeholder="添加评论内容"><span id="replyCommentBtn" class="" data-id="" data-type="">评论</span></div><div id="userIfo"></div></div></div></div></div></div></div>');}return __p.join('');}});


define('tpl!app/templates/askdetail/AskDetailPersonView.html', function() {return function(obj) { var __p=[],print=function(){__p.push.apply(__p,arguments);};with(obj||{}){__p.push('<div class="personal-user clearfix"><a target="_blank" href="#homepage/reply/', uid ,'"><var><img src="', avatar ,'" alt="" ></var><span>', nickname ,'</span><em class="create-time" data-id="" data-type="">'); var timeMatrixing = time(create_time); ; __p.push('', timeMatrixing ,'</em></a><p>', desc ,'</p></div>');}return __p.join('');}});

define('app/views/askdetail/AskDetailPersonView',[
        'app/views/Base', 
        'tpl!app/templates/askdetail/AskDetailPersonView.html'
       ],
    function (View,  template) {
        "use strict";
        
        return View.extend({
            tagName: 'div',
            className: '',
            template: template,

            construct: function() {
                var self = this;
                this.listenTo(this.model, 'change', this.render);
            },
        });
    });


define('tpl!app/templates/askdetail/AskDetailCommentView.html', function() {return function(obj) { var __p=[],print=function(){__p.push.apply(__p,arguments);};with(obj||{}){__p.push('<div class="user-ifo clearfix"><a target="_blank" href="#homepage/reply/', uid ,'" class="sculpture"><img src="', avatar ,'"></a><div class="reply-ifo clearfix"><div class="clearfix"><a target="_blank" class="reply-name" href="#homepage/reply/', uid ,'">', nickname ,'</a><em class="reply-time">'); var timeMatrixing = time(create_time); ; __p.push('', timeMatrixing ,'</em></div>'); var i = 0; _.each(at_comment, function(atComment,i) { i++ ; __p.push(''); if( i == 1) { ; __p.push('<i class="huicomment"></i><var><a target="_blank" href="#homepage/reply/', uid ,'">@', atComment.nickname ,':</a>&nbsp&nbsp', atComment.content ,'</var>'); } ; __p.push(''); }) ; __p.push('<p>', content ,'</p><span class="reply-play">回复</span></div><div class="inp-frame blo"><input type="text" maxlength="101" placeholder="', nickname ,' :" class="play-inp" target-id="', target_id ,'" comment-id="', comment_id ,'" for-comment="', for_comment ,'" data-type="', target_type ,'" reply-to="', reply_to ,'"><var>回复</var><i class="play-icon bg-sprite-new"></i><span class="inp-reply" data-id="', target_id ,'" data-type="', target_type ,'">回复</span><em class="reply-cancel">取消</em></div></div>');}return __p.join('');}});

define('app/views/askdetail/AskDetailCommentView',[
        'app/views/Base', 
        'tpl!app/templates/askdetail/AskDetailCommentView.html'
       ],
    function (View,  template) {
        "use strict";
        
        return View.extend({
            tagName: 'div',
            className: '',
            template: template,

            construct: function() {
                var self = this;
                this.listenTo(this.collection, 'change', this.render);
                self.collection.loading();
            },
    
        });
    });


define('tpl!app/templates/askdetail/AskDetailCountView.html', function() {return function(obj) { var __p=[],print=function(){__p.push.apply(__p,arguments);};with(obj||{}){__p.push('<div class="comment">评论 （', comment_count ,'）</div>');}return __p.join('');}});

define('app/views/askdetail/AskDetailCountView',[
        'app/views/Base', 
        'tpl!app/templates/askdetail/AskDetailCountView.html'
       ],
    function (View,  template) {
        "use strict";
        
        return View.extend({
            tagName: 'div',
            className: '',
            template: template,

            construct: function() {
                var self = this;
                this.listenTo(this.model, 'change', this.render);
            },
    
        });
    });


define('tpl!app/templates/askdetail/AskDetailActionView.html', function() {return function(obj) { var __p=[],print=function(){__p.push.apply(__p,arguments);};with(obj||{}){__p.push(''); if(type == 2) {; __p.push('<div class="seek clearfix like reply-Detail-liak like_toggle ', (uped)? 'liked': '' ,'" data-type="', type ,'" data-id="', id ,'"><i class="seek-icon bg-sprite-new"></i><em class="like-count ', (uped)? 'like-color': '' ,'">', up_count ,'</em></div>'); } ; __p.push(''); if(type == 1) {; __p.push('<div class="reply-detail-bang clearfix download" data-type="', type ,'" data-id="', id ,'"><i class="bang-icon bg-sprite-new"></i><em>BANG</em></div>'); } ; __p.push('');}return __p.join('');}});

define('app/views/askdetail/AskDetailActionView',[
        'app/views/Base', 
        'tpl!app/templates/askdetail/AskDetailActionView.html'
       ],
    function (View,  template) {
        "use strict";
        
        return View.extend({
            tagName: 'div',
            className: '',
            template: template,

            construct: function() {
                this.listenTo(this.model, 'change', this.render);
            },
    
        });
    });

define('app/views/askdetail/AskDetailPlayView',[
        'app/views/Base',
        'app/models/Base',
        'app/models/Ask', 
        'app/models/Reply',
        'app/collections/Comments',
        'tpl!app/templates/askdetail/AskDetailPlayView.html',
        'app/views/askdetail/AskDetailPersonView',
        'app/views/askdetail/AskDetailCommentView',
        'app/views/askdetail/AskDetailCountView',
        'app/views/askdetail/AskDetailActionView',
       ],
    function (View, ModelBase, Ask, Reply, Comments, template, ReplyDetailPersonView, ReplyDetailCommentView, ReplyDetailCountView, ReplyDetailActionView) {
        "use strict";
        
        return View.extend({
            tagName: 'div',
            className: '',
            template: template,
            events: {
                "click .like_toggle" : 'likeToggleLarge',
                "click .pic-scroll" : "picScroll",
                "click #askDetailRight" : "picScroll",
                "click #askDetailLeft" : "picScroll",
                "click .reply-play" : "replyBlo",
                "click .reply-more" : "moreScroll", 
                "click #replyCommentBtn" : "replyCommentBtn",
                "click .inp-reply" : "inpReply",
                "click .reply-cancel" : "replyNone",
                "click .download" : "download",
            },
            inpReply: function(e) {
                var el = $(e.currentTarget).siblings('.play-inp');
                var content = el.val();
                var reply_to = el.attr('reply-to');
                var type = el.attr('data-type');
                var comment_id = el.attr('comment-id');
                var target_id = el.attr('target-id');

                var url = "/comments/save";

                var postData = {
                    'content': content,
                    'type' : type,
                    'id': target_id,
                    'reply_to' : reply_to,
                    'for_comment' : comment_id
                };
                $.post(url, postData, function( returnData ){
                    var info = returnData.info;
                    if( returnData.ret == 1 ) {
                        toast('回复评论成功');
                        $('.center-loading-image-container[data-id=' + target_id + ']').trigger("click");
                        // window.location.reload()
                    } 
                });



            },
            replyCommentBtn: function(e) {
                var id = $(e.currentTarget).attr('data-id');
                var type = $(e.currentTarget).attr('data-type');
                var content = $("#textInp").val();
                if(!content || content == "") {
                    toast("内容不能为空");
                    return false;
                }
                $.post('/comments/save', {
                    id: id,
                    type: type,
                    content: content
                }, function(data) {
                    if(data.ret == 1){
                        $('.center-loading-image-container[data-id=' + id + ']').trigger("click");
                        //todo: upgrade append
                        $("#textInp").val(' ');
                        var t = $(document);
                        // t.scrollTop(t.height());  
                    }
                    else {
                        $(".login-popup").click();
                    }
                });
            },
            

            moreScroll: function() {
                $(".reply-detail-ifo").scrollTop(204);
                $(".reply-more").addClass("blo");
                $(".reply-detail-ifo").css({
                    overflow: "auto"
                })
            },
            sendComment:function(e) {
                var id = $(e.currentTarget).attr('data-id');
                var type = $(e.currentTarget).attr('data-type');
                var content = $(e.currentTarget).prev().val();
                if(!content || content == "") {
                    toast("内容不能为空");
                    return false;
                }
                $.post('/comments/save', {
                    id: id,
                    type: type,
                    content: content
                }, function(data) {
                    if(data.ret == 1){
                        $('.reply-trigger[data-id=' + id + ']').trigger("click");
                        //todo: upgrade append
                        $(".praise-comment textarea").val(' ');
                        var t = $(document);
                        t.scrollTop(t.height());  
                    }
                    else {
                        $(".login-popup").click();
                    }
                });
            },
            replyNone: function(e) {
                $(".inp-frame").addClass("blo");
            },
            replyBlo: function(e) {
                $(e.currentTarget).parents(".reply-ifo").siblings(".inp-frame").removeClass("blo").parents(".user-ifo").siblings(".user-ifo").find(".inp-frame").addClass("blo");
            },

            picScroll: function(e) {
                var replyImg = $(".pic-scroll img");  //获取img
                var replyLength = replyImg.length; //获取img长度
                var replyIndex = parseInt($(".detail-pic").attr("otherNum")); //获取索引值
                // var dataIdx = parseInt($(e.currentTarget).attr("data-idx"));    //  
                var replySrc = null;
                var picIndex = null;
                if(e.currentTarget.id == "replyDetailRight") {
                    replyIndex++;
                    if (replyIndex >= (replyLength - 1)) {
                        replyIndex = (replyLength - 1);
                    };
                    $(".detail-pic").attr("otherNum", replyIndex);
                };
                if(e.currentTarget.id == "replyDetailLeft") {
                    replyIndex--;
                    if (replyIndex <= 0) {
                        replyIndex = 0;
                    };
                    $(".detail-pic").attr("otherNum", replyIndex);
                };  

                 // 点击作品
                if($(e.currentTarget).hasClass("center-loading-image-container")) {     
                    replyIndex = $(e.currentTarget).index();
                    $(".detail-pic").attr("otherNum", replyIndex);
                };

                replySrc = replyImg.eq(replyIndex).attr("src"); //获取当前图片的src
                $("#bigPic").attr("src", replySrc);

                replyImg.eq(replyIndex).parents(".center-loading-image-container").addClass("change-pic").siblings(".center-loading-image-container").removeClass("change-pic");
                $(".original-pic").removeClass("original-change");

                if (replyIndex == (replyLength - 1)) {
                    $("#replyDetailRight").css({
                        display: "none"
                    })
                } else {
                    $("#replyDetailRight").css({
                        display: "block"
                    })
                };
                 if (replyIndex == 0) {
                    $("#replyDetailLeft").css({
                        display: "none"
                    })
                } else {
                     $("#replyDetailLeft").css({
                        display: "block"
                    })
                };

                var dataIdx = replyIndex + 1;
                if (parseInt($(".detail-pic").css("marginLeft")) == 0)  {
                    picIndex = 3;
                };
                if (dataIdx > picIndex && dataIdx < replyLength && dataIdx >= 3) {
                    $(".detail-pic").animate({
                        marginLeft: - 90 * (dataIdx - 3) + "px"
                    }, 400);
                    picIndex = dataIdx;
                };

                var reply_id = replyImg.eq(replyIndex).parents(".center-loading-image-container").attr('data-id');
                var type = replyImg.eq(replyIndex).parents(".center-loading-image-container").attr("data-type");

                $("#replyCommentBtn").attr("data-id",reply_id);
                $("#replyCommentBtn").attr("data-type", type);

                if(type == 2) {
                    var model = new Reply;
                    model.url = '/replies/' + reply_id;
                    model.fetch();
                    $("#bgIcon").addClass("other-icon").removeClass("old-icon");
                };
                if(type == 1) {
                    var model = new Ask;
                    model.url = '/asks/' + reply_id;
                    model.fetch();
                    $("#bgIcon").addClass("old-icon").removeClass("other-icon");
                };
                var comments = new Comments;
                comments.url = '/comments?target_type=new';
                comments.data.type = type;
                comments.data.target_id = reply_id;

                var replyDetailPersonView = new Backbone.Marionette.Region({el:"#replyDetailPersonView"});
                var view = new ReplyDetailPersonView({
                    model: model
                });
                replyDetailPersonView.show(view); 

                var userIfo = new Backbone.Marionette.Region({el:"#userIfo"});
                var view = new ReplyDetailCommentView({
                    collection: comments
                });
                userIfo.show(view); 

                var count = new Backbone.Marionette.Region({el:"#count"});
                var view = new ReplyDetailCountView({
                    model: model
                });
                count.show(view); 

                var action = new Backbone.Marionette.Region({el:"#action"});
                var view = new ReplyDetailActionView({
                    model: model
                });
                action.show(view);

                setTimeout(function(){
                    if($(".reply-comment").height() > 550) {
                        $(".reply-more").removeClass("blo");
                    } else {
                        $(".reply-more").addClass("blo");
                    };
                    $(".reply-detail-ifo").css({
                        overflow: "hidden"
                    });
                }, 700);

                var imageWidth  = $("#bigPic").width();
                var imageHeight = $("#bigPic").height();
                var imageRatio  = imageWidth/imageHeight;
                var centerLoadContainer = $("#bigPic").parents('.center-image');
                var containerWidth      = $(centerLoadContainer)[0].offsetWidth;
                var containerHeight     = $(centerLoadContainer)[0].offsetHeight;
                var tempWidth  = 0;
                var tempHeight = 0;
                var offsetLeft = 0;
                var offsetTop  = 0;
                
                if (imageHeight >= containerHeight && imageWidth >= containerWidth) {
                    // 图片宽高都大于容器宽高

                    // 图片长比较长，按照高度缩放，截取中间部分
                    if (imageWidth / imageHeight >= containerWidth / containerHeight) {
                      
                        tempWidth = containerWidth;
                        tempHeight = imageHeight * containerWidth / imageWidth;

                        offsetTop = (containerHeight - tempHeight) / 2;
                        offsetLeft = 0;
                    } else if (imageWidth / imageHeight < containerWidth / containerHeight) {
                        //图片比较高，安装宽度缩放，截取中间部分
                        tempHeight = containerHeight;
                        tempWidth  = imageWidth * containerHeight / imageHeight;

                        // tempWidth  = containerWidth;
                        // tempHeight = imageHeight * containerWidth / imageWidth;

                        offsetTop = 0;
                        offsetLeft  = (containerWidth - tempWidth) / 2;
                    };    
                } else if (imageWidth < containerWidth && imageHeight < containerHeight) {
                    // 图片宽高都小于容器宽高
                    if (imageRatio > containerWidth / containerHeight) {
                        tempWidth    = containerWidth;
                        tempHeight   = tempWidth / imageWidth * imageHeight;

                        offsetLeft   = 0;
                        offsetTop    = (containerHeight - tempHeight) / 2;
                    } else {
                        tempWidth    = imageWidth / imageHeight * containerHeight;
                        tempHeight   = containerHeight;

                        offsetTop    = 0;
                        offsetLeft   = (containerWidth - tempWidth) / 2;
                    }
                } else if (imageWidth <= containerWidth && imageHeight >= containerHeight) {
                    // 图片宽度小于容器 高度大于容器  
                    tempHeight = containerHeight;
                    tempWidth  = imageRatio * containerHeight;

                    offsetLeft = (containerWidth - tempWidth) / 2;
                    offsetTop  = 0;
                } else if (imageWidth >= containerWidth && imageHeight <= containerHeight) {
                    // 图片宽度大于容器 图片高度小于容器
                    tempWidth  = containerWidth;
                    tempHeight = tempWidth / imageWidth * imageHeight;

                    offsetTop  = (containerHeight - tempHeight) / 2;
                    offsetLeft = 0;
                };          

                $("#bigPic").css('left', offsetLeft);
                $("#bigPic").css('top', offsetTop);
                $("#bigPic").width(tempWidth);
                $("#bigPic").height(tempHeight);   
                setTimeout(function() {
                    $(".comment-content .border-bottom").removeClass("border-bot");
                    $(".border-bottom").eq($(".comment-content").find(".border-bottom").length - 1).addClass("border-bot");
                }, 200);
            },
            construct: function() {
                var self = this;
                this.listenTo(this.model, 'change', this.render);
            },
        });
    });

define('app/controllers/AskDetail',[
        'underscore',
        'app/models/Ask',
        'app/collections/Comments',
        'app/views/askdetail/AskDetailPlayView'
        ],
    function (_, Ask, Comments, ReplyDetailPlayView) {
        "use strict";

        return function(type , ask_id) {

            setTimeout(function(){
                $("title").html("图派-求P详情");
                $('.header-back').addClass("height-reduce");
            },500);
            var model = new Ask;
            model.url = '/asks/'+ ask_id;
            model.fetch();

            var view = new ReplyDetailPlayView({
                model: model
            });
            window.app.content.show(view);

            setTimeout(function(){
                $('.center-loading').trigger("click");
            },700);

        };
    });

define('app/controllers/Logout',['app/models/User'], function (User) {
        "use strict";

        return function() {
            var user = new User;
            user.url = '/user/logout';

            WB2.logout();
            user.fetch({
                success: function(){
                    location.href = '/#index';
                    location.reload();
                }
            });
        };

    });

define('app/collections/Users',['app/collections/Base', 'app/models/User'], function(Collection, User) {
    return Collection.extend({
        model: User,
        url: '/search'
     });
}); 

define('app/models/Inprogress',['app/models/Base'], function(Model) {
    return Model.extend({
        url: '/inprogress/',
        defaults: {
            id: 0,
            ask_id: 0,
            type: 1,
            uped: false,
            up_count: 0,
            comment_count: 0,
            click_count: 0,
            inform_count: 0,
            share_count: 0,
            weixin_share_count: 0,
            reply_count: 0,
            collected: false,
            desc: '',
            image_width: 0,
            image_height: 0,
            image_url: '',
            ask_uploads: [],
            avatar: '',
            uid: '',
            username: '',
            nickname: '',
            create_time: ''

        },
        construct: function() {

        }
    });

}); 

define('app/collections/Inprogresses',['app/collections/Base', 'app/models/Inprogress'], function(Collection, Inprogress) {
    return Collection.extend({
        model: Inprogress,
        url: '/inprogresses'
     });
}); 


define('tpl!app/templates/homepage/HomeHeadView.html', function() {return function(obj) { var __p=[],print=function(){__p.push.apply(__p,arguments);};with(obj||{}){__p.push('<div class="homehead-cantainer" data-id="', uid ,'" data-follow="', is_follow ,'"><div class="homedead-section"><div class="home-avater"><img src="', avatar ,'" alt="', username ,'"></div><div class="home-name">', nickname ,'</div><div class="personage-actionbar-item"><ul class="personage-actionbar-count"><li class="personage-attention" data-type="attention"><i>', fellow_count ,'</i><span class="personage-attention personage-nav" >关注</span></li><li class="personage-fans personage-nav" data-type="fans" ><i>', fans_count ,'</i><span>粉丝</span></li><li class="personage-link"><i>', uped_count ,'</i><span>获赞</span></li></ul></div>');  if(is_follow) {  ; __p.push('<div id="attention" class="home-attention hide" data-id="', uid ,'"><span>+关注</span></div><div id="cancel_attention" class="cancel-attention" data-id="', uid ,'"><span>已关注</span></div>'); } else { ; __p.push(''); if( !uid ) {   ; __p.push('<a href="#login-popup" class="login-popup">'); } ; __p.push('<div id="attention" class="home-attention " data-id="', uid ,'"><span>+关注</span></div><div id="cancel_attention" class="cancel-attention hide" data-id="', uid ,'"><span>已关注</span></div></a>'); } ; __p.push('<div class="home-nav"><li class="menu-bar-item menu-nav-reply active" data-type="reply" data-id="', uid ,'">作品</li><li class="menu-bar-item menu-nav-ask home-others hide" data-type="ask" data-id="', uid ,'">ta的求P</li><li class="menu-bar-item menu-nav-ask home-self hide" data-type="ask" data-id="', uid ,'">求P</li><li class="menu-bar-item menu-nav-conduct" data-type="conduct" data-id="', uid ,'">进行中</li><li class="menu-bar-item menu-nav-collection">收藏</li><li class="menu-bar-item menu-nav-liked home-self hide" data-id="', uid ,'">我赞过的</li><li class="menu-bar-item menu-nav-liked home-others hide" data-id="', uid ,'">ta赞过的</li></div></div></div><div class="home-reply-cantainer"><div class="home-reply clearfix"><div class="fans-nav hide home-self">我的粉丝 <var class="fans-count">', fans_count ,'</var></div><div class="fans-nav hide home-others">ta的粉丝 <var class="fans-count">', fans_count ,'</var></div><div class="attention-nav hide home-self">我的关注 <var class="fans-count">', fellow_count ,'</var></div><div class="attention-nav hide home-others">ta的关注 <var class="fans-count">', fellow_count ,'</var></div><div class="home-cantainer clearfix" id="homeCantainer"></div></div></div>');}return __p.join('');}});


define('tpl!app/templates/homepage/HomeReplyView.html', function() {return function(obj) { var __p=[],print=function(){__p.push.apply(__p,arguments);};with(obj||{}){__p.push('<div class="hot-section grid-item">    <div class="hot-picture center-loading-image-container">        <a target="_blank" href="/#replydetailplay/', ask_id ,'/', id ,'">    <img src="', image_url ,'" alt="', nickname ,'">    </a>    </div><div class="hot-footer"><span class="header-portrait">        '); var timeMatrixing = time(create_time); ; __p.push('', timeMatrixing ,'</span><!-- <span class="hot-name">', nickname ,'</span> --><div class="hot-item-actionbar"><span class="browse"><span class="browse-icon bg-sprite-new"></span><span class="browse-count like-count">', click_count ,'</span></span><span class="like"><span class="like-icon bg-sprite-new" data-type="', type ,'" data-id="', id ,'"></span><span class="like-count">', up_count ,'</span></span><span class="comment"><span class="comment-icon bg-sprite-new"></span><span class="comment-count">', comment_count ,'</span></span></div></div></div>');}return __p.join('');}});

define('app/views/homepage/HomeReplyView',[
        'masonry',
        'imagesLoaded',
        'app/views/Base', 
        'tpl!app/templates/homepage/HomeReplyView.html'
       ],
    function (masonry, imagesLoaded,  View, template) {
        "use strict";
        
        return View.extend({
            tagName: 'div',
            className: 'grid clearfix ',
            template: template,
            construct: function () {
                this.listenTo(this.collection, 'change', this.renderMasonry);
            },
            render: function() {
                this.renderMasonry();
            }
        });
    });


define('tpl!app/templates/homepage/HomeAskView.html', function() {return function(obj) { var __p=[],print=function(){__p.push.apply(__p,arguments);};with(obj||{}){__p.push('<div class="home-ask"><div class="ask-pic clearfix"><div class="ask-old-pic">'); if( reply_count > 0 ) { ; __p.push(''); var i = 0;  _.each( replies , function(reply,i) { i++ ; __p.push('            <a target="_blank" href="#replydetailplay/', ask_id ,'/', replies[0].id ,'" class="clearfix reply-pic">'); }) ; __p.push('        '); } else { ; __p.push('             <a target="_blank" href="/#comment/ask/', ask_id ,'">        '); } ; __p.push('<img src="', image_url ,'" alt=""><i class="home-icon bg-sprite-new"></i></a></div><div class="ask-work-pic"><div class="ask-main-pic clearfix">'); var i = 0;  _.each( replies , function(reply,i) { i++ ; __p.push('<a target="_blank" href="#replydetailplay/', ask_id ,'/', reply.id ,'" class="ask-box"><img src="', reply.image_url ,'" alt=""></a>'); }) ; __p.push(''); if( replies.length > 4 ) {; __p.push('            <em>查看更多</em>            '); } ; __p.push('</div></div><div class="ask-time">'); var timeMatrixing = time(create_time); ; __p.push('', timeMatrixing ,'</div></div><div class="ask-write clearfix"><input class="edit_self desc hide" type="text" value="', desc ,'"><input class="edit_others desc hide" type="text" value="', desc ,'" disabled="disabled"><i class="edit_self reset-btn reset-icon bg-sprite-new hide" data-id="', id ,'" ></i></div></div>');}return __p.join('');}});

define('app/views/homepage/HomeAskView',[
        'app/views/Base', 
        'app/collections/Asks',
        'tpl!app/templates/homepage/HomeAskView.html'
       ],
    function (View, Asks, template) {
        
        "use strict";
        return View.extend({
            tagName: 'div',
            className: '',
            template: template,
            collections: Asks,
            events: {
                "click .reset-btn" : "uploadDesc"
            },
            construct: function() {
                this.listenTo(this.collection, 'change', this.render);
            },
            showEmptyView: function(data) {
                if(data.data.page == 1 && data.length == 0) {
                    append($("#contentView"), ".emptyContentView");
                }
            },
            onRender: function() {
                var own_id = $(".homehead-cantainer").attr("data-id");
                var uid = window.app.user.get('uid');

                if( own_id == uid ) {
                    $('.edit_self').removeClass("hide");
                    $(".reset-icon").css({
                        display: "block"
                    })
                } else {
                    $('.edit_others').removeClass("hide");
                    display: "none"
                }
            },
            uploadDesc: function(e) {
                var id = $(e.currentTarget).attr("data-id");
                var desc = $(e.currentTarget).siblings(".desc").val();
      
                $.post('asks/save', {
                    id: id,
                    desc: desc
                }, function(data) {
                    toast('修改求P内容成功',function(){});

                });
            },
        });
    });


define('tpl!app/templates/homepage/HomeConductView.html', function() {return function(obj) { var __p=[],print=function(){__p.push.apply(__p,arguments);};with(obj||{}){__p.push('<div class="home-conduct"><div class="conduct-cantainer clearfix"><div class="conduct-pic"><a target="_blank" href="#replydetailplay/', ask_id ,'/', id ,'"><img src="', image_url ,'" alt=""></a></div><div class="conduct-right"><div class="conduct-header"><a target="_blank" href="#homepage/reply/', uid ,'"><em><img src="', avatar ,'" alt=""></em><span>', nickname ,'</span></a><em class="conduct-time">'); var timeMatrixing = time(create_time); ; __p.push('', timeMatrixing ,'</em></div><p>', desc ,'</p><div class="uploading-delete clearfix"><a href="#reply-uploading-popup" class="reply-uploading-popup" ask-id="', ask_id ,'"><div class="conduct-upload"><span class="bg-sprite-new"></span><em>作品</em></div></a><a class="download" data-type="', type ,'" data-id="', id ,'"><div class="conduct-onload"><span class="bg-sprite-new"></span><em>原图</em></div></a></div></div></div></div>');}return __p.join('');}});

define('app/views/homepage/HomeConductView',[
        'app/views/Base', 
        'app/collections/Inprogresses', 
        'tpl!app/templates/homepage/HomeConductView.html'
       ],
    function (View, Inprogresses, template) {
        "use strict";
        
        return View.extend({
            tagName: 'div',
            className: '',
            template: template,
            collections: Inprogresses,
            events: {
                "click .download" : "download",
                "click .reply-uploading-popup" : "askImageUrl"
            },

            construct: function() {
                this.listenTo(this.collection, 'change', this.render);

                var inProgressPopup = $(".inprogress-popup");
                    $(".inprogress-popup").fancybox({
                         afterShow: function(){
                            $('.conduct-upload').unbind('click').bind('click', askImageUrl);
                         }
                    }); 
            },
           askImageUrl:function(e) {   
                var ask_id = $(e.currentTarget).attr('ask-id');
                $('#reply-uploading-popup').attr('ask-id', ask_id);
                var askImageUrl = $(e.currentTarget).parents('.conduct-right').siblings(".conduct-pic").find('img').attr('src');

                $('#ask_image img').attr('src', askImageUrl);
            }
        });
    });


define('tpl!app/templates/homepage/HomeFansView.html', function() {return function(obj) { var __p=[],print=function(){__p.push.apply(__p,arguments);};with(obj||{}){__p.push('<div class="friendship-main clearfix">    <div class="friendship-avatar">        <a target="_blank" href="#homepage/reply/', uid ,'">            <img src="', avatar ,'" alt="">        </a>    </div>    <div class="friendship-section-right">        <div class="fons-right">            <div class="name" data-id="', uid ,'">', nickname ,'</div>            <!-- <div class="district">广东,深圳</div> -->            <ul class="clearfix">                <li class="attention-item">                    <span class="attention">作品</span>                    <i class="attention-count">', reply_count ,'</i>                </li>                <li class="fans-item attention-item">                    <span class="fans">粉丝</span>                    <i class="fans-count">', fans_count ,'</i>                </li>                <li class="like-item attention-item">                    <span class="like">获赞</span>                    <i class="like-count">', uped_count ,'</i>                </li>            </ul>        </div>        ');              var own_uid = $('.user-message').attr('data-id');              if( own_uid != uid ) {         ; __p.push('            '); if ( is_follow && is_fan ){ ; __p.push('                 <span id="one-another" class="one-another" data-id="', uid ,'">互相关注</span>            '); } else if( is_follow){ ; __p.push('                <span class="attention-btn" data-id="', uid ,'">已关注</span>            '); } else if( is_fan ){ ; __p.push('                <span id="one-another" class="one-another" data-id="', uid ,'">互相关注</span>                <span id="attention" class="attention-btn attention-btn" data-id="', uid ,'">+关注</span>            '); } else { ; __p.push('                <span class="attention-btn" data-id="', uid ,'">已关注</span>                <span id="attention" class="attention-btn attention-btn" data-id="', uid ,'">+关注</span>            '); } ; __p.push('        '); } ; __p.push('    </div></div>');}return __p.join('');}});

define('app/views/homepage/HomeFansView',[
        'app/views/Base', 
        'app/collections/Users', 
        'tpl!app/templates/homepage/HomeFansView.html'
       ],
    function (View, Users, template) {
            "use strict";
        
        return View.extend({
            tagName: 'div',
            className: '',
            data: 0,
            collections: Users,
            template: template,
            onRender: function() {
                $(".home-nav li").removeClass("active");
            },

            construct: function() {
                this.listenTo(this.collection, 'change', this.render);
            },
        });
    });

define('app/views/homepage/HomeAttentionView',[
        'app/views/Base', 
        'app/collections/Users', 
        'tpl!app/templates/homepage/HomeFansView.html'
       ],
    function (View, Users, template) {
            "use strict";
        
        return View.extend({
            tagName: 'div',
            className: '',
            data: 0,
            collections: Users,
            template: template,
            construct: function() {
                this.listenTo(this.collection, 'change', this.render);
            },
            onRender: function() {
                $(".home-nav li").removeClass("active");
            },
        });
    });


define('tpl!app/templates/homepage/HomeLikedView.html', function() {return function(obj) { var __p=[],print=function(){__p.push.apply(__p,arguments);};with(obj||{}){__p.push('<div class="liked-image"><a target="_blank" href="#replydetailplay/', ask_id ,'/', id ,'" class="clearfix"><img src="', image_url ,'" alt=""></a></div>');}return __p.join('');}});

define('app/views/homepage/HomeLikedView',[
        'app/views/Base', 
        'app/collections/Users', 
        'tpl!app/templates/homepage/HomeLikedView.html'
       ],
    function (View, Users, template) {
            "use strict";
        
        return View.extend({
            tagName: 'div',
            className: '',
            data: 0,
            collections: Users,
            template: template,
            construct: function() {
                this.listenTo(this.collection, 'change', this.render);
            }
        });
    });


define('tpl!app/templates/homepage/HomeCollectionView.html', function() {return function(obj) { var __p=[],print=function(){__p.push.apply(__p,arguments);};with(obj||{}){__p.push('<div class="home-collection"><div class="collection-cantainer clearfix"><div class="collection-pic"><a target="_blank" href="#replydetailplay/', ask_id ,'/', id ,'"><img src="', image_url ,'" alt="" data-type="', type ,'"><i class="collection-pic-icon bg-sprite-new"></i></a></div><div class="collection-right"><div class="collection-header"><a target="_blank" href="#homepage/reply/', uid ,'"><em><img src="', avatar ,'" alt=""></em><span>', nickname ,'</span></a><em class="collection-time">'); var timeMatrixing = time(create_time); ; __p.push('', timeMatrixing ,'</em></div><p>', desc ,'</p><div class="collection-like clearfix like like_toggle"  data-type="', type ,'" data-id="', id ,'"><i class="collection-icon bg-sprite-new" ></i><em class="like-count ', (uped)? 'like-color': '' ,'">', up_count ,'</em></div></div></div></div>');}return __p.join('');}});

define('app/views/homepage/HomeCollectionView',[
        'app/views/Base', 
        'app/collections/Inprogresses', 
        'tpl!app/templates/homepage/HomeCollectionView.html'
       ],
    function (View, Inprogresses, template) {
        "use strict";
        
        return View.extend({
            tagName: 'div',
            className: '',
            template: template,
            collections: Inprogresses,
            events: {
                "click .download" : "download",
                "click .reply-uploading-popup" : "askImageUrl"
            },

            construct: function() {
                this.listenTo(this.collection, 'change', this.render);

                var inProgressPopup = $(".inprogress-popup");
                    $(".inprogress-popup").fancybox({
                         afterShow: function(){
                            $('.conduct-upload').unbind('click').bind('click', askImageUrl);
                         }
                    }); 
            },
           askImageUrl:function(e) {   
                var ask_id = $(e.currentTarget).attr('ask-id');
                $('#reply-uploading-popup').attr('ask-id', ask_id);
                var askImageUrl = $(e.currentTarget).parents('.conduct-right').siblings(".conduct-pic").find('img').attr('src');

                $('#ask_image img').attr('src', askImageUrl);
            }
        });
    });

define('app/views/homepage/HomeHeadView',[
        'app/views/Base', 
        'app/collections/Users', 
        'app/collections/Replies',
        'app/collections/Asks',
        'app/collections/Inprogresses', 
        'tpl!app/templates/homepage/HomeHeadView.html',
        'app/views/homepage/HomeReplyView',
        'app/views/homepage/HomeAskView',
        'app/views/homepage/HomeConductView',
        'app/views/homepage/HomeFansView',
        'app/views/homepage/HomeAttentionView',
        'app/views/homepage/HomeLikedView',
        'app/views/homepage/HomeCollectionView',
       ],
    function (View, Users, Replies, Asks, Inprogresses, template, HomeReplyView, HomeAskView, HomeConductView, HomeFansView, HomeAttentionView,HomeLikedView,HomeCollectionView) {
        "use strict";
        
        return View.extend({
            tagName: 'div',
            className: '',
            template: template,
            events: {
                "click .menu-bar-item" : 'homeNav',
                "click .menu-nav-reply" : 'homeReply',
                "click .menu-nav-ask" : 'homeAsk',
                "click .menu-nav-liked" : 'homeLiked',
                "click .menu-nav-conduct" : 'homeConduct',
                "click .menu-nav-collection" : 'homeCollection',
                "click .personage-fans" : 'FansList',
                "click #attention" : "attention",
                "click #cancel_attention" : "cancelAttention",
                "click .personage-attention" : "attentionList",
                "click .like_toggle" : 'likeToggleLarge',
            },
            initialize: function() {

                this.listenTo(this.model, 'change', this.render);
            },
            homeLiked:function() {
                $('.attention-nav').addClass("hide");
                $('.fans-nav').addClass("hide");
                $("#homeCantainer").empty();

                var uid = $(".menu-nav-liked").attr("data-id");
                var ask = new Asks;
                var likedCantainer = new Backbone.Marionette.Region({el:"#homeCantainer"});
                var liked_view = new HomeLikedView({
                    collection: ask
                });
                liked_view.scroll();
                liked_view.collection.url = '/user/uped';
                liked_view.collection.reset();
                liked_view.collection.data.uid = uid;
                liked_view.collection.data.page = 0;
                liked_view.collection.loading(this.showEmptyView);
                likedCantainer.show(liked_view);
                
            },
            onRender: function() {
                var own_id = $(".homehead-cantainer").attr("data-id");
                var uid = window.app.user.get('uid');
                
                if( own_id == uid ) {
                    $("#attention").addClass("hide");
                    $("#cancel_attention").addClass("hide");
                    $('.home-self').removeClass("hide");
                } else {
                    $('.home-others').removeClass("hide");
                    $(".menu-nav-conduct").addClass("hide");
                }
          
            },
            homeAsk: function(e) {
                $('.fans-nav').addClass("hide");
                $('.attention-nav').addClass("hide");
                $("#homeCantainer").empty();
                
                var uid = $(".menu-nav-reply").attr("data-id");
                var ask = new Asks;
                var askCantainer = new Backbone.Marionette.Region({el:"#homeCantainer"});
                var ask_view = new HomeAskView({
                    collection: ask
                });

                ask_view.scroll();
                ask_view.collection.reset();
                ask_view.collection.data.uid = uid;
                ask_view.collection.data.page = 0;
                ask_view.collection.data.type = 'ask';
                ask_view.collection.loading();
                askCantainer.show(ask_view);   
            },
            homeReply: function(e) {
                $('.fans-nav').addClass("hide");
                $('.attention-nav').addClass("hide");
                $("#homeCantainer").empty();
                
                var uid = $(".menu-nav-reply").attr("data-id");
                var homeReplyCantainer = new Backbone.Marionette.Region({el:"#homeCantainer"});
                var reply = new Replies;
                var reply_view = new HomeReplyView({
                    collection: reply
                });

                reply_view.scroll();
                reply_view.collection.reset();
                reply_view.collection.data.uid = uid;
                reply_view.collection.data.page = 0;
                reply_view.collection.loading();
                homeReplyCantainer.show(reply_view);
            },
            attention: function(event) {
                var el = $(event.currentTarget);
                var id = el.attr("data-id");
                $.post('user/follow', {
                    uid: id,
                    status: 1
                }, function(data) {
                    if(data.ret == 1) 
                        $(el).addClass('hide').siblings().removeClass('hide');
                });
            },
            cancelAttention: function(event) {
                var el = $(event.currentTarget);
                var id = el.attr("data-id");
                $.post('user/follow', {
                    uid: id,
                    status: 0
                }, function(data) {
                    if(data.ret == 1) 
                        $(el).addClass('hide').siblings().removeClass('hide');
                });
            },

            attentionList: function() {
                var own_id = $(".homehead-cantainer").attr("data-id");
                var uid = window.app.user.get('uid');
                
                if( own_id == uid ) {
                    $("#attention").addClass("hide");
                    $("#cancel_attention").addClass("hide");
                    $('.home-self').removeClass("hide");
                } else {
                    $('.home-others').removeClass("hide");
                    $(".menu-nav-conduct").addClass("hide");
                }
                
                $('.fans-nav').addClass("hide");
                $("#homeCantainer").empty();
                $(".home-nav").children("li").removeClass("active");    

                var uid = $(".menu-nav-reply").attr("data-id");
                var user = new Users;
                var fansCantainer = new Backbone.Marionette.Region({el:"#homeCantainer"});
                var fans_view = new HomeAttentionView({
                    collection: user
                });

                fans_view.scroll();
                fans_view.collection.url = '/follows';
                fans_view.collection.reset();
                fans_view.collection.data.uid = uid;
                fans_view.collection.data.page = 0;
                fans_view.collection.loading(this.showEmptyView);
                fansCantainer.show(fans_view);

            },
            FansList: function(e) {
                var own_id = $(".homehead-cantainer").attr("data-id");
                var uid = window.app.user.get('uid');
                
                if( own_id == uid ) {
                    $("#attention").addClass("hide");
                    $("#cancel_attention").addClass("hide");
                    $('.home-self').removeClass("hide");
                } else {
                    $('.home-others').removeClass("hide");
                    $(".menu-nav-conduct").addClass("hide");
                }
                $("#homeCantainer").empty();
                $(".home-nav").children("li").removeClass("active");    
                $('.attention-nav').addClass("hide");


                var uid = $(".menu-nav-reply").attr("data-id");
                var user = new Users;
                var fansCantainer = new Backbone.Marionette.Region({el:"#homeCantainer"});
                var fans_view = new HomeFansView({
                    collection: user
                });
                fans_view.scroll();
                fans_view.collection.url = '/fans';
                fans_view.collection.reset();
                fans_view.collection.data.uid = uid;
                fans_view.collection.data.page = 0;
                fansCantainer.show(fans_view);
                fans_view.collection.loading(this.showEmptyView);
            },
            homeConduct: function(e) {
                $('.fans-nav').addClass("hide");
                $('.attention-nav').addClass("hide");
                $("#homeCantainer").empty();

                var uid = $(".menu-nav-reply").attr("data-id");
                var inprogress = new Inprogresses;
                var conductCantainer = new Backbone.Marionette.Region({el:"#homeCantainer"});
                var conduct_view = new HomeConductView({
                    collection: inprogress
                });
                conduct_view.scroll();
                conduct_view.collection.reset();
                conduct_view.collection.data.uid = uid;
                conduct_view.collection.data.page = 0;
                conduct_view.collection.loading(this.showEmptyView);
                conductCantainer.show(conduct_view);
            },            
            homeCollection: function(e) {
                $('.fans-nav').addClass("hide");
                $('.attention-nav').addClass("hide");
                $("#homeCantainer").empty();

                var uid = $(".homehead-cantainer").attr("data-id");
                var ask = new Asks;
                var collectionCantainer = new Backbone.Marionette.Region({el:"#homeCantainer"});
                var collection_view = new HomeCollectionView({
                    collection: ask 
                });

                collection_view.scroll();
                collection_view.collection.url = '/user/collections';
                collection_view.collection.reset();
                collection_view.collection.data.uid = uid;
                collection_view.collection.data.page = 0;
                // todo qiang
                collection_view.collection.loading(this.showEmptyView);
                collectionCantainer.show(collection_view);

            },
            homeNav : function(e) {
                $(e.currentTarget).addClass("active").siblings().removeClass("active");
                var type = $(e.currentTarget).attr('data-type');
                var id = $(e.currentTarget).attr('data-id');
            },
            // showEmptyView: function(data) {
            //     // todo qiang
            //     if(data.data.page == 1 && data.length == 0 ) {
            //         append($("#contentView div"), ".emptyContentView");
            //     } 
            // },
        });
    });

define('app/controllers/HomePage',['underscore',
        'app/models/User',
		'app/views/homepage/HomeHeadView',
		],
    function (_, User, HomeHeadView) {
        "use strict";

        return function(type, uid) {
            setTimeout(function(){
                $("title").html("图派-个人主页");
            },100);

            var user = new User;
            user.url = '/users/' + uid;
            user.fetch();
            
            var view = new HomeHeadView({
                model: user
            });
            window.app.content.show(view);
         

            setTimeout(function(){
                $('.header').addClass("hide");
                $('.header-back').addClass("height-reduce");
                $(".menu-nav-"+ type + " ").trigger("click");
            },400);
      

        
        };
    });

define('app/models/Search',['app/models/Base'], function(Model) {
    return Model.extend({
        defaults: {
            type: ' ',
            keyword: ' ',
        },
        construct: function() {

        }
    });

}); 

define('app/models/Thread',['app/models/Base'], function(Model) {
    return Model.extend({
        url: '/thread/',
        defaults: {
            id: ' ',
            ask_id: ' ',
            type: 1,
            is_follow: false,
            is_download: false,
            uped: false,
            collected: false,
            avatar: "http://7u2spr.com1.z0.glb.clouddn.com/20151028-16575056308e0eec2ce.jpg",
            sex: ' ',
            uid: ' ',
            nickname: ' ',
            upload_id: ' ',
            create_time: ' ',
            update_time: ' ',
            desc: ' ',
            up_count: ' ',
            comment_count: ' ',
            collect_count: 0,
            click_count: ' ',
            inform_count: 0,
            share_count: ' ',
            weixin_share_count: ' ',
            reply_count: 0,
            ask_uploads: [],
            image_url: "http://7u2spr.com1.z0.glb.clouddn.com/20151028-2003045630b9780ebca.jpg?imageView2/2/w/480",
            image_width: 480,
            image_height: 480
        },
        construct: function() {

        }
    });

}); 

define('app/collections/Threads',['app/collections/Base', 'app/models/Thread'], function(Collection, Reply) {
    return Collection.extend({
        model: Reply,
        url: '/threads'
     });
}); 

define('app/models/Topic',['app/models/Base'], function(Model) {
    return Model.extend({
        url: '/topic/',
        defaults: {
            topic_id: ' ',
            node_id: ' ',
            uid: ' ',
            ruid: ' ',
            title: ' ',
            avatar: ' ', 
            keywords: ' ',
            nickname: ' ', 
            content: ' ',
            addtime: ' ',
            updatetime: ' ',
            lastreply: ' ',
            views: ' ',
            comments: ' ',
            favorites: ' ',
            closecomment: null,
            is_top: ' ',
            is_hidden: ' ',
            ord: ' '
        },
        construct: function() {

        }
    });

}); 

define('app/collections/Topics',['app/collections/Base', 'app/models/Topic'], function(Collection, Topic) {
    return Collection.extend({
        model: Topic,
        url: '/topics'
     });
}); 


define('tpl!app/templates/search/SearchView.html', function() {return function(obj) { var __p=[],print=function(){__p.push.apply(__p,arguments);};with(obj||{}){__p.push('<div class="search-container"><div class="search-head"><div class="search-nav"><ul>                <li class="nav all ', (type == 'all')? 'nav-bottom': '' ,'" data-type="all">全部</li><li class="nav menu-bar-user ', (type == 'user')? 'nav-bottom': '' ,'" data-type="user">用户</li><li class="nav menu-bar-thread ', (type == 'thread')? 'nav-bottom': '' ,'" data-type="thread">内容</li><li class="nav menu-bar-topic ', (type == 'topic')? 'nav-bottom': '' ,'" data-type="topic">讨论</li></ul></div></div>    <div class="correlation-section">        '); if(type == 'all' || type == 'user') { ; __p.push('<span class="user correlation-user">相关用户        '); if(type != 'user') { ; __p.push('<span class="more nav" data-type="user" >更多<i class="more-icon bg-sprite-new"></i></span>'); } ; __p.push('</span><div id="userItemView"></div>        '); } if(type == 'all' || type == 'thread') { ; __p.push('<span class="thread correlation-content">相关内容        '); if(type != 'thread') { ; __p.push('<span class="more nav" data-type="thread" >更多<i class="more-icon bg-sprite-new"></i></span>'); } ; __p.push('</span><div id="threadItemView"></div>        '); } if(type == 'all' || type == 'topic') { ; __p.push('<span class="topic correlation-discuss" data-type="topic">相关讨论        '); if(type != 'topic') { ; __p.push('<span class="more nav" data-type="topic">更多<i class="more-icon bg-sprite-new"></i></span>'); } ; __p.push('</span>        <div id="topicItemView"></div>        '); } ; __p.push('</div></div>');}return __p.join('');}});

define('app/views/search/SearchView',['app/views/Base', 'tpl!app/templates/search/SearchView.html'],
         
    function (View, template) {
        "use strict";
        
        return View.extend({
            tagName: 'div',
            className: 'photo-container',
            template: template,
            events: {
                'click .nav' : 'navBar',
            },
            navBar: function(e) {
                var type = $(e.currentTarget).attr('data-type');
                var keyword = $('#keyword').val();
                
	            if(keyword != undefined && keyword != '') {
	                location.href = '#search/'+ type +'/'+ keyword;
                }
                else {
                    location.href = '#search/'+ type;
	            }
            }
        });
    });


define('tpl!app/templates/search/UserItemView.html', function() {return function(obj) { var __p=[],print=function(){__p.push.apply(__p,arguments);};with(obj||{}){__p.push('<div class="search-user"><span class="avatar"><a target="_blank" href="#homepage/reply/', uid ,'"><img src="', avatar ,'" alt=""></a></span><span class="person-message"><a target="_blank" href="#homepage/reply/', uid ,'"><span class="name">', nickname ,'</span></a><ul><li class="">', reply_count ,'作品</li><li class="">', fans_count ,'粉丝</li><li class="">', uped_count ,'获赞</li></ul></span>');     var own_uid = $('.user-message').attr('data-id');        if( own_uid != uid ) {      ; __p.push(''); if ( is_follow && is_fan ){ ; __p.push(' <div class="attention-btn" data-id="', uid ,'">互相关注</div>'); } else if( is_follow ){ ; __p.push('<div class="attention-btn" data-id="', uid ,'">已关注</div>'); } else if( is_fan ){ ; __p.push('<div class="attention-btn" data-id="', uid ,'">互相关注</div><div class="attention-btn attention-btn-pressed" data-id="', uid ,'">+关注</div>'); } else { ; __p.push('<span class="attention-btn" data-id="', uid ,'">已关注</span><span id="attention" class="attention-btn attention-btn-pressed" data-id="', uid ,'">+关注</span>'); } ; __p.push(''); } ; __p.push('</div>');}return __p.join('');}});

define('app/views/search/UserItemView',['app/views/Base', 'app/collections/Users', 'tpl!app/templates/search/UserItemView.html'],
function (View, Users, template) {
    "use strict";
    return View.extend({
        tagName: 'div',
        className: '',
        template: template,
        collection: Users,
        events: {
            "click #attention" : "attention",
        },
        construct: function() {
            this.listenTo(this.collection, 'change', this.render);
            this.collection.loading();
        },
        attention: function(event) {
            var el = $(event.currentTarget);
            var id = el.attr("data-id");
            $.post('user/follow', {
                uid: id
            }, function(data) {
                if(data.ret == 1) 
                    $(event.currentTarget).addClass('hide').siblings().removeClass('hide');
            });
        },
        // render: function() {
        //    var template = this.template;
        //    var el = $(this.el);
        //     this.collection.each(function(model){
        //         append(el, template(model.toJSON()));
        //     });
        //     this.onRender();
        // },
        // onRender: function() {
        //     $('a.menu-bar-search').click(function(){
        //         var keyword = $('#keyword').val();
        //         if(keyword != undefined && keyword != '') {
        //             location.href = '#search/all/'+keyword;
        //         }
        //         else {
        //             location.href = '#search/all';
        //         }
        //     });

        //     this.loadImage();
        // }
    });
});


define('tpl!app/templates/search/ThreadItemView.html', function() {return function(obj) { var __p=[],print=function(){__p.push.apply(__p,arguments);};with(obj||{}){__p.push('<div class="hot-section">'); if( reply_count > 0 ) { ; __p.push('    <a target="_blank" href="/#replydetailplay/', ask_id ,'/', id ,'">'); } else { ; __p.push('     <a target="_blank" href="/#comment/ask/', ask_id ,'">'); } ; __p.push('<span class="hot-picture"><span class="is-loading">    <img src="', image_url ,'" alt="', nickname ,'">    </span></span></a><div class="desc">', desc ,'</div>    <div class="hot-footer"><span class="avatar"><img src="', avatar ,'" alt="', nickname ,'"></span><span class="hot-name">', nickname ,'</span><span class="time">'); var timeMatrixing = time(create_time); ; __p.push('', timeMatrixing ,'</span><span class="hot-item-actionbar">'); if( type != 1) { ; __p.push('<span class="like"><span class="like-icon bg-sprite"></span><span class="like-count">', up_count ,'</span></span>'); } ; __p.push('<span class="comment"><span class="comment-icon bg-sprite"></span><span class="comment-count">', comment_count ,'</span></span></span></div></div>');}return __p.join('');}});

define('app/views/search/ThreadItemView',[
        'app/views/Base', 
        'app/collections/Threads', 
        'tpl!app/templates/search/ThreadItemView.html'
       ],
        function (View, Threads, template) {
            "use strict";
            return View.extend({
                tagName: 'div',
                className: '',
                template: template,
                collection: Threads,

                construct: function() {
                    var self = this;
                    this.listenTo(this.collection, 'change', this.render);
                    self.collection.loading();
                },
            });
        });


define('tpl!app/templates/search/TopicItemView.html', function() {return function(obj) { var __p=[],print=function(){__p.push.apply(__p,arguments);};with(obj||{}){__p.push('<div class="section"><div class="discuss-content"><span class="avatar"><img src="', avatar ,'" alt=""></span><span class="discuss-list"><ul><li class="name">', nickname ,'</li><li class="content"><a href="/bbs/topic/show/', topic_id ,'">', title ,'</a></li><li class="describe">', content ,'</li></ul></span><span class="time-item"><span class="time">'); var timeMatrixing = time(addtime); ; __p.push('', timeMatrixing ,'</span><span class="count">', comments ,'</span></span></div></div>');}return __p.join('');}});

define('app/views/search/TopicItemView',['app/views/Base', 'app/collections/Topics', 'tpl!app/templates/search/TopicItemView.html'], 
    function (View, Topics, template) {
        "use strict";
       
        return View.extend({
            tagName: 'div',
            className: '',
            template: template,
            collection: Topics,
            construct: function() {
                var self = this;
                this.listenTo(this.collection, 'change', this.render);
                self.collection.loading(self.showEmptyView);
            },
        });
    });

define('app/controllers/Search',[
        'underscore', 
        'app/models/Search',
        'app/collections/Threads',
        'app/collections/Users',
        'app/collections/Topics',
        'app/views/search/SearchView',
        'app/views/search/UserItemView',
        'app/views/search/ThreadItemView',
        'app/views/search/TopicItemView'
       ],
    function (_, Search, Threads, Users, Topics, SearchView, UserItemView, ThreadItemView, TopicItemView) {
        "use strict";

        return function(type, keyword) {

            setTimeout(function(){
                $("title").html("图派-搜索主页");
                $('.header-back').removeClass("height-reduce");
            },100);

            //渲染主页面
            var search = new Search({type: type});
            var view = new SearchView({model: search});
            window.app.content.show(view);
            $('#keyword').val(keyword);

            //获取数据
            var threads = new Threads;
            threads.url = '/search/threads';
            threads.data.keyword = keyword;

            var users = new Users;
            users.url = '/search/users';
            users.data.keyword = keyword;
    
            var topics = new Topics;
            topics.url = '/search/topics';
            topics.data.keyword = keyword;
            
            var userRegion = new Backbone.Marionette.Region({el:"#userItemView"});
            var user_view = new UserItemView({
                collection: users
            });
            
    
            var threadRegion = new Backbone.Marionette.Region({el:"#threadItemView"});
            var thread_view = new ThreadItemView({
                collection: threads 
            });

            var topicRegion = new Backbone.Marionette.Region({el:"#topicItemView"});
            var topic_view = new TopicItemView({
                collection: topics
            });

            switch(type) {
            case 'user':
                userRegion.show(user_view);
                break;
            case 'thread':
                threadRegion.show(thread_view);
                break;
            case 'topic':
                topicRegion.show(topic_view);
                break;
            default:
                userRegion.show(user_view);
                threadRegion.show(thread_view);
                topicRegion.show(topic_view);
                break;
            }
        }
    });

define('app/models/AskReplies',['app/models/Base'], function(Model) {
    return Model.extend({
        url: '/ask/',
        defaults: {
            ask: {
                id: 0,
                ask_id: 0,
                uped: false,
                type: 1,
                up_count: 0,
                comment_count: 0,
                click_count: 0,
                inform_count: 0,
                share_count: 0,
                weixin_share_count: 0,
                reply_count: 0,
                collected: false,
                desc: '',
                image_url: '',
                image_width: '',
                image_height: '',
                image_ratio: '',
                ask_uploads: [],
                avatar: '',
                uid: '',
                username: '',
                nickname: '',
                create_time: '',
                comments: [],
            },
            replies: []
        }
    });

}); 


define('tpl!app/templates/replydetailplay/ReplyDetailPlayView.html', function() {return function(obj) { var __p=[],print=function(){__p.push.apply(__p,arguments);};with(obj||{}){__p.push('<div class="reply-detail-content"><div class="detail-content clearfix"><!-- 左边 --><div class="replydetail-pic"><div class="reply-pic"><!-- <div class="dig-pic center-loading center-loading-image-container"><div class="is-loading"><img id="bigPic" src="" alt=""><span class="bg-sprite-new" id="bgIcon"></span></div></div> --><div class="dig-pic center-image"><div class="center-loading-img"><div class="img-loading"><img id="bigPic" src="" alt=""><span class="bg-sprite-new" id="bgIcon"></span></div></div></div><div class="reply-left bg-sprite-new" id="replyDetailLeft"></div><div class="reply-right bg-sprite-new" id="replyDetailRight"></div></div><!-- 底部 --><div class="work-pic"><div class="detail-pic clearfix" originalNum="0">'); _.each(ask.ask_uploads, function(asks) {  ; __p.push('<div class="old-pic old-click center-loading-image-container pic-scroll" data-type="1" data-id="', ask.id ,'"><span class="center-loading"><img src="', asks.image_url ,'" alt=""><i class="bg-sprite-new old-icon"></i></span></div>'); }) ; __p.push(''); _.each( replies, function(reply) {  ; __p.push('<div class="other-pic other-click center-loading-image-container pic-scroll" data-type="2" data-id="', reply.id ,'"><span class="center-loading"><img src="', reply.image_url ,'" data-id="', reply.id ,'"><i class="bg-sprite-new other-icon"></i></span></div>'); }) ; __p.push('</div></div></div><div class="reply-detail-ifo"><div class="reply-more blo">查看更多评论</div><div class="right-content"><!-- 信息 --><div class="reply-user clearfix" ><div id="replyDetailPersonView" class="clearfix"></div><div class="share clearfix"><i class="share-icon bg-sprite-new"></i><em>222</em></div><div id="action"></div></div><!-- 评论 --><div class="reply-comment clearfix"><div id="count"></div><div class="comment-content clearfix"><div class="reply-inp border-bottom clearfix"><input type="text" maxlength="101" name="" cols="30" rows="10" id="textInp" placeholder="添加评论内容"><span id="replyCommentBtn" class="" data-id="" data-type="">评论</span></div><div id="userIfo"></div></div></div></div></div></div></div>');}return __p.join('');}});


define('tpl!app/templates/replydetailplay/ReplyDetailPersonView.html', function() {return function(obj) { var __p=[],print=function(){__p.push.apply(__p,arguments);};with(obj||{}){__p.push('<div class="personal-user clearfix"><a target="_blank" href="#homepage/reply/', uid ,'"><var><img src="', avatar ,'" alt="" ></var><span>', nickname ,'</span><em class="create-time" data-id="" data-type="">'); var timeMatrixing = time(create_time); ; __p.push('', timeMatrixing ,'</em></a><p>', desc ,'</p></div>');}return __p.join('');}});

define('app/views/replydetailplay/ReplyDetailPersonView',[
        'app/views/Base', 
        'tpl!app/templates/replydetailplay/ReplyDetailPersonView.html'
       ],
    function (View,  template) {
        "use strict";
        
        return View.extend({
            tagName: 'div',
            className: '',
            template: template,

            construct: function() {
                var self = this;
                this.listenTo(this.model, 'change', this.render);
            },
        });
    });


define('tpl!app/templates/replydetailplay/ReplyDetailCommentView.html', function() {return function(obj) { var __p=[],print=function(){__p.push.apply(__p,arguments);};with(obj||{}){__p.push('<div class="user-ifo border-bottom clearfix"><a target="_blank" href="#homepage/reply/', uid ,'" class="sculpture"><img src="', avatar ,'"></a><div class="reply-ifo clearfix"><div class="clearfix"><a target="_blank" class="reply-name" href="#homepage/reply/', uid ,'">', nickname ,'</a><em class="reply-time">'); var timeMatrixing = time(create_time); ; __p.push('', timeMatrixing ,'</em></div>'); var i = 0; _.each(at_comment, function(atComment,i) { i++ ; __p.push(''); if( i == 1) { ; __p.push('<i class="huicomment"></i><var><a target="_blank" href="#homepage/reply/', uid ,'">@', atComment.nickname ,':</a>&nbsp&nbsp', atComment.content ,'</var>'); } ; __p.push(''); }) ; __p.push('<p>', content ,'</p><span class="reply-play">回复</span></div><div class="inp-frame blo"><input type="text" maxlength="101" value="', nickname ,' : " class="play-inp" target-id="', target_id ,'" comment-id="', comment_id ,'" for-comment="', for_comment ,'" data-type="', target_type ,'" reply-to="', reply_to ,'"><var>回复</var><i class="play-icon bg-sprite-new"></i><span class="inp-reply" data-id="', target_id ,'" data-type="', target_type ,'">回复</span><em class="reply-cancel">取消</em></div></div>');}return __p.join('');}});

define('app/views/replydetailplay/ReplyDetailCommentView',[
        'app/views/Base', 
        'tpl!app/templates/replydetailplay/ReplyDetailCommentView.html'
       ],
    function (View,  template) {
        "use strict";
        
        return View.extend({
            tagName: 'div',
            className: '',
            template: template,

            construct: function() {
                var self = this;
                this.listenTo(this.collection, 'change', this.render);
                self.collection.loading();
            },
    
        });
    });


define('tpl!app/templates/replydetailplay/ReplyDetailCountView.html', function() {return function(obj) { var __p=[],print=function(){__p.push.apply(__p,arguments);};with(obj||{}){__p.push('<div class="comment">评论 （', comment_count ,'）</div>');}return __p.join('');}});

define('app/views/replydetailplay/ReplyDetailCountView',[
        'app/views/Base', 
        'tpl!app/templates/replydetailplay/ReplyDetailCountView.html'
       ],
    function (View,  template) {
        "use strict";
        
        return View.extend({
            tagName: 'div',
            className: '',
            template: template,

            construct: function() {
                var self = this;
                this.listenTo(this.model, 'change', this.render);
            },
    
        });
    });


define('tpl!app/templates/replydetailplay/ReplyDetailActionView.html', function() {return function(obj) { var __p=[],print=function(){__p.push.apply(__p,arguments);};with(obj||{}){__p.push(''); if(type == 2) {; __p.push('<div class="seek clearfix like reply-Detail-liak like_toggle ', (uped)? 'liked': '' ,'" data-type="', type ,'" data-id="', id ,'"><i class="seek-icon bg-sprite-new"></i><em class="like-count ', (uped)? 'like-color': '' ,'">', up_count ,'</em></div>'); } ; __p.push(''); if(type == 1) {; __p.push('<div class="reply-detail-bang clearfix download" data-type="', type ,'" data-id="', id ,'"><i class="bang-icon bg-sprite-new"></i><em>BANG</em></div>'); } ; __p.push('');}return __p.join('');}});

define('app/views/replydetailplay/ReplyDetailActionView',[
        'app/views/Base', 
        'tpl!app/templates/replydetailplay/ReplyDetailActionView.html'
       ],
    function (View,  template) {
        "use strict";
        
        return View.extend({
            tagName: 'div',
            className: '',
            template: template,

            construct: function() {
                var self = this;
                this.listenTo(this.model, 'change', this.render);
            },
    
        });
    });

define('app/views/replydetailplay/ReplyDetailPlayView',[
        'app/views/Base',
        'app/models/Base',
        'app/models/Ask', 
        'app/models/Reply',
        'app/collections/Comments',
        'tpl!app/templates/replydetailplay/ReplyDetailPlayView.html',
        'app/views/replydetailplay/ReplyDetailPersonView',
        'app/views/replydetailplay/ReplyDetailCommentView',
        'app/views/replydetailplay/ReplyDetailCountView',
        'app/views/replydetailplay/ReplyDetailActionView',
       ],
    function (View, ModelBase, Ask, Reply, Comments, template, ReplyDetailPersonView, ReplyDetailCommentView, ReplyDetailCountView, ReplyDetailActionView) {
        "use strict";
        
        return View.extend({
            tagName: 'div',
            className: '',
            template: template,
            events: {
                "click .like_toggle" : 'likeToggleLarge',
                "click .pic-scroll" : "picScroll",
                "click #replyDetailRight" : "picScroll",
                "click #replyDetailLeft" : "picScroll",
                "click .reply-play" : "replyBlo",
                "click .reply-more" : "moreScroll", 
                "click #replyCommentBtn" : "replyCommentBtn",
                "click .inp-reply" : "inpReply",
                "click .reply-cancel" : "replyNone",
                "click .download" : "download",
            },
            inpReply: function(e) {
                var el = $(e.currentTarget).siblings('.play-inp');
                var content = el.val();
                var reply_to = el.attr('reply-to');
                var type = el.attr('data-type');
                var comment_id = el.attr('comment-id');
                var target_id = el.attr('target-id');

                var url = "/comments/save";

                var postData = {
                    'content': content,
                    'type' : type,
                    'id': target_id,
                    'reply_to' : reply_to,
                    'for_comment' : comment_id
                };
                $.post(url, postData, function( returnData ){
                    var info = returnData.info;
                    if( returnData.ret == 1 ) {
                        toast('回复评论成功');
                        $('.center-loading-image-container[data-id=' + target_id + ']').trigger("click");
                        // window.location.reload()
                    } 
                });



            },
            replyCommentBtn: function(e) {
                var id = $(e.currentTarget).attr('data-id');
                var type = $(e.currentTarget).attr('data-type');
                var content = $("#textInp").val();
                if(!content || content == "") {
                    toast("内容不能为空");
                    return false;
                }
                $.post('/comments/save', {
                    id: id,
                    type: type,
                    content: content
                }, function(data) {
                    if(data.ret == 1){
                        $('.center-loading-image-container[data-id=' + id + ']').trigger("click");
                        //todo: upgrade append
                        $("#textInp").val(' ');
                        var t = $(document);
                        // t.scrollTop(t.height());  
                    }
                    else {
                        $(".login-popup").click();
                    }
                });
            },
            

            moreScroll: function() {
                $(".reply-detail-ifo").scrollTop(204);
                $(".reply-more").addClass("blo");
                $(".reply-detail-ifo").css({
                    overflow: "auto"
                })
            },
            sendComment:function(e) {
                var id = $(e.currentTarget).attr('data-id');
                var type = $(e.currentTarget).attr('data-type');
                var content = $(e.currentTarget).prev().val();
                if(!content || content == "") {
                    toast("内容不能为空");
                    return false;
                }
                $.post('/comments/save', {
                    id: id,
                    type: type,
                    content: content
                }, function(data) {
                    if(data.ret == 1){
                        $('.reply-trigger[data-id=' + id + ']').trigger("click");
                        //todo: upgrade append
                        $(".praise-comment textarea").val(' ');
                        var t = $(document);
                        t.scrollTop(t.height());  
                    }
                    else {
                        $(".login-popup").click();
                    }
                });
            },
            replyNone: function(e) {
                $(".inp-frame").addClass("blo");
            },
            replyBlo: function(e) {
                $(e.currentTarget).parents(".reply-ifo").siblings(".inp-frame").removeClass("blo").parents(".user-ifo").siblings(".user-ifo").find(".inp-frame").addClass("blo");
            },
            picScroll: function(e) {
                var replyImg = $(".pic-scroll img");  //获取img
                var replyLength = replyImg.length; //获取img长度
                var replyIndex = parseInt($(".detail-pic").attr("otherNum")); //获取索引值
                // var dataIdx = parseInt($(e.currentTarget).attr("data-idx"));    //  
                var replySrc = null;
                var picIndex = null;
                if(e.currentTarget.id == "replyDetailRight") {
                    replyIndex++;
                    if (replyIndex >= (replyLength - 1)) {
                        replyIndex = (replyLength - 1);
                    };
                    $(".detail-pic").attr("otherNum", replyIndex);
                };
                if(e.currentTarget.id == "replyDetailLeft") {
                    replyIndex--;
                    if (replyIndex <= 0) {
                        replyIndex = 0;
                    };
                    $(".detail-pic").attr("otherNum", replyIndex);
                };  

                 // 点击作品
                if($(e.currentTarget).hasClass("center-loading-image-container")) {     
                    replyIndex = $(e.currentTarget).index();
                    $(".detail-pic").attr("otherNum", replyIndex);
                };

                replySrc = replyImg.eq(replyIndex).attr("src"); //获取当前图片的src
                $("#bigPic").attr("src", replySrc);

                replyImg.eq(replyIndex).parents(".center-loading-image-container").addClass("change-pic").siblings(".center-loading-image-container").removeClass("change-pic");
                $(".original-pic").removeClass("original-change");

                if (replyIndex == (replyLength - 1)) {
                    $("#replyDetailRight").css({
                        display: "none"
                    })
                } else {
                    $("#replyDetailRight").css({
                        display: "block"
                    })
                };
                 if (replyIndex == 0) {
                    $("#replyDetailLeft").css({
                        display: "none"
                    })
                } else {
                     $("#replyDetailLeft").css({
                        display: "block"
                    })
                };

                var dataIdx = replyIndex + 1;
                if (parseInt($(".detail-pic").css("marginLeft")) == 0)  {
                    picIndex = 3;
                };
                if (dataIdx > picIndex && dataIdx < replyLength && dataIdx >= 3) {
                    $(".detail-pic").animate({
                        marginLeft: - 90 * (dataIdx - 3) + "px"
                    }, 400);
                    picIndex = dataIdx;
                };

                var reply_id = replyImg.eq(replyIndex).parents(".center-loading-image-container").attr('data-id');
                var type = replyImg.eq(replyIndex).parents(".center-loading-image-container").attr("data-type");

                $("#replyCommentBtn").attr("data-id",reply_id);
                $("#replyCommentBtn").attr("data-type", type);

                if(type == 2) {
                    var model = new Reply;
                    model.url = '/replies/' + reply_id;
                    model.fetch();
                    $("#bgIcon").addClass("other-icon").removeClass("old-icon");
                };
                if(type == 1) {
                    var model = new Ask;
                    model.url = '/asks/' + reply_id;
                    model.fetch();
                    $("#bgIcon").addClass("old-icon").removeClass("other-icon");
                };
                var comments = new Comments;
                comments.url = '/comments?target_type=new';
                comments.data.type = type;
                comments.data.target_id = reply_id;

                var replyDetailPersonView = new Backbone.Marionette.Region({el:"#replyDetailPersonView"});
                var view = new ReplyDetailPersonView({
                    model: model
                });
                replyDetailPersonView.show(view); 

                var userIfo = new Backbone.Marionette.Region({el:"#userIfo"});
                var view = new ReplyDetailCommentView({
                    collection: comments
                });
                userIfo.show(view); 

                var count = new Backbone.Marionette.Region({el:"#count"});
                var view = new ReplyDetailCountView({
                    model: model
                });
                count.show(view); 

                var action = new Backbone.Marionette.Region({el:"#action"});
                var view = new ReplyDetailActionView({
                    model: model
                });
                action.show(view);

                setTimeout(function(){
                    if($(".reply-comment").height() > 550) {
                        $(".reply-more").removeClass("blo");
                    } else {
                        $(".reply-more").addClass("blo");
                    };
                    $(".reply-detail-ifo").css({
                        overflow: "hidden"
                    });
                }, 700);

                var imageWidth  = $("#bigPic").width();
                var imageHeight = $("#bigPic").height();
                var imageRatio  = imageWidth/imageHeight;
                var centerLoadContainer = $("#bigPic").parents('.center-image');
                var containerWidth      = $(centerLoadContainer)[0].offsetWidth;
                var containerHeight     = $(centerLoadContainer)[0].offsetHeight;
                var tempWidth  = 0;
                var tempHeight = 0;
                var offsetLeft = 0;
                var offsetTop  = 0;
                
                if (imageHeight >= containerHeight && imageWidth >= containerWidth) {
                    // 图片宽高都大于容器宽高

                    // 图片长比较长，按照高度缩放，截取中间部分
                    if (imageWidth / imageHeight >= containerWidth / containerHeight) {
                      
                        tempWidth = containerWidth;
                        tempHeight = imageHeight * containerWidth / imageWidth;

                        offsetTop = (containerHeight - tempHeight) / 2;
                        offsetLeft = 0;
                    } else if (imageWidth / imageHeight < containerWidth / containerHeight) {
                        //图片比较高，安装宽度缩放，截取中间部分
                        tempHeight = containerHeight;
                        tempWidth  = imageWidth * containerHeight / imageHeight;

                        // tempWidth  = containerWidth;
                        // tempHeight = imageHeight * containerWidth / imageWidth;

                        offsetTop = 0;
                        offsetLeft  = (containerWidth - tempWidth) / 2;
                    };    
                } else if (imageWidth < containerWidth && imageHeight < containerHeight) {
                    // 图片宽高都小于容器宽高
                    if (imageRatio > containerWidth / containerHeight) {
                        tempWidth    = containerWidth;
                        tempHeight   = tempWidth / imageWidth * imageHeight;

                        offsetLeft   = 0;
                        offsetTop    = (containerHeight - tempHeight) / 2;
                    } else {
                        tempWidth    = imageWidth / imageHeight * containerHeight;
                        tempHeight   = containerHeight;

                        offsetTop    = 0;
                        offsetLeft   = (containerWidth - tempWidth) / 2;
                    }
                } else if (imageWidth <= containerWidth && imageHeight >= containerHeight) {
                    // 图片宽度小于容器 高度大于容器  
                    tempHeight = containerHeight;
                    tempWidth  = imageRatio * containerHeight;

                    offsetLeft = (containerWidth - tempWidth) / 2;
                    offsetTop  = 0;
                } else if (imageWidth >= containerWidth && imageHeight <= containerHeight) {
                    // 图片宽度大于容器 图片高度小于容器
                    tempWidth  = containerWidth;
                    tempHeight = tempWidth / imageWidth * imageHeight;

                    offsetTop  = (containerHeight - tempHeight) / 2;
                    offsetLeft = 0;
                };          

                $("#bigPic").css('left', offsetLeft);
                $("#bigPic").css('top', offsetTop);
                $("#bigPic").width(tempWidth);
                $("#bigPic").height(tempHeight);   
                setTimeout(function() {
                    $(".comment-content .border-bottom").removeClass("border-bot");
                    $(".border-bottom").eq($(".comment-content").find(".border-bottom").length - 1).addClass("border-bot");
                }, 200)


            },
            construct: function() {
                this.listenTo(this.model, 'change', this.render);
                
            },
        });
    });

define('app/controllers/ReplyDetailPlay',[
        'underscore',
        'app/models/AskReplies',
        'app/collections/Comments',
        'app/views/replydetailplay/ReplyDetailPlayView'
        ],
    function (_, AskReplies, Comments, ReplyDetailPlayView) {
        "use strict";

        return function(ask_id, reply_id) {

            setTimeout(function(){
                $("title").html("图派-作品详情");
                $('.header-back').addClass("height-reduce");
            },500);

            var model = new AskReplies;
            model.url = 'replies/ask/' + reply_id;
            model.fetch();
            var view = new ReplyDetailPlayView({
                model: model
            });
            window.app.content.show(view);

            setTimeout(function(){
                $('.center-loading-image-container[data-id=' + reply_id + ']').trigger("click");
                    
            },700);
            

        };
    });

define('app/models/Category',['app/models/Base'], function(Model) {
    return Model.extend({
        url: '/category/',
        defaults: {
            id: "",
            display_name: "",
            pc_pic: "",
            app_pic: "",
            banner_pic: "",
            url: "",
            pid: "",
            icon: "",
            post_btn: "",
            description: "",
            category_type: ""
        }
    });

}); 

define('app/collections/Categories',['app/collections/Base', 'app/models/Category'], function(Collection, Category) {
    return Collection.extend({
        model: Category,
        url: '/categories'
     });
}); 

define('app/models/Activity',['app/models/Base'], function(Model) {
    return Model.extend({
        url: '/activity/',
        defaults: {
            id: 10,
            display_name: "",
            pc_pic: "",
            app_pic: "",
            banner_pic: "",
            url: "",
            pid: 4,
            icon: "",
            post_btn: "",
            description: "",
            download_count: 0,
            click_count: 0,
            replies_count: 0,
            ask_id: 0,
            users: [ ]
        }
    });

}); 

define('app/models/Channel',['app/models/Base'], function(Model) {
    return Model.extend({
        url: '/channel/',
        defaults: {
            id: '',
            ask_id: '',
            type: 1,
            is_follow: true,
            is_fan: false,
            is_download: false,
            uped: false,
            collected: false,
            avatar: '',
            sex: 0,
            uid: '',
            nickname: '',
            upload_id: '',
            create_time: '',
            update_time: '',
            desc: '',
            up_count: '',
            comment_count: '',
            reply_count: '',
            click_count: '',
            inform_count: '',
            collect_count: '0',
            share_count: '0',
            weixin_share_count: 0,
            ask_uploads:[],
            image_url: '',
            image_width: 0,
            image_height: 0,
            image_ratio: '1.29',
            replies:[],
            users: []
        }
    });

}); 

define('app/collections/Channels',['app/collections/Base', 'app/models/Channel'], function(Collection, channel) {
    return Collection.extend({
        model: channel,
        url: '/channels'
     });
}); 

define('app/collections/Activities',['app/collections/Base', 'app/models/Activity'], function(Collection, activity) {
    return Collection.extend({
        model: activity,
        url: '/activities'
     });
}); 


define('tpl!app/templates/channel/ChannelFoldView.html', function() {return function(obj) { var __p=[],print=function(){__p.push.apply(__p,arguments);};with(obj||{}){__p.push('<div class="fold-contain"><div class="fold-header clearfix"><div class="fold-works"><span>已有作品:</span><em>', reply_count ,'</em></div><div class="fold-time">'); var timeMatrixing = time(create_time); ; __p.push('', timeMatrixing ,'</div></div><div class="fold-pic-contain clearfix"><div class="channel-artwork"><a target="_blank" href="#homepage/reply/', uid ,'" class="artwork-header clearfix"><span><img src="', avatar ,'" alt="头像"></span><em>', nickname ,'</em></a><div class="artwork-contain  center-loading-image-container"><a target="_blank" href="#askdetail/ask/', ask_id ,'" class="center-loading"><span class="is-loading"><img src="', image_url ,'" data-type="1" alt="原图"><i class="artwork-icon bg-sprite-new"></i><span></a></div><p>', desc ,'</p><div class="fold-function clearfix"><div class="fold-comments"><i class="comments-icon bg-sprite-new"></i><span>', comment_count ,'</span></div><div class="fold-bang download" data-type="', type ,'" data-id="', id ,'"></div></div></div><!-- 作品 --><div class="channel-works"><!-- <div class="arrow-right bg-sprite-new"></div><div class="arrow-left bg-sprite-new"></div> --><!-- <a class="view-details" href="#replydetailplay/', ask_id ,'/', id ,'">查看详情</a> --><div class="long-pic clearfix">'); _.each(replies, function(reply) {  ; __p.push('<div class="channel-works-contain"><a target="_blank" href="#homepage/reply/', uid ,'" class="works-header" ><span><img "', reply.avatar ,'" alt="头像"></span><em>', reply.nickname ,'</em></a><div class="works-contain da-as" data-width="', reply.image_width ,'"><a target="_blank" href="#replydetailplay/', reply.ask_id ,'/', reply.id ,'" ><img src="', reply.image_url ,'" alt=""><span class="bg-sprite-new works-icon"></span></a></div><p class="reply-desc">', reply.desc ,'</p><div class="fold-function"><a target="_blank" class="fold-comments" href="#replydetailplay/', reply.ask_id ,'/', reply.id ,'"><i class="comments-icon bg-sprite-new"></i><span>', reply.comment_count ,'</span></a><div class="fold-praise like like_toggle ', (uped)? 'liked': '' ,'" data-type="', type ,'" data-id="', id ,'"><i class="praise-icon bg-sprite-new"></i><span class="like-count">', reply.up_count ,'</span></div></div></div>');        var i = [];        var width = $(".da-as").attr("data-width");       var i = width;      ; __p.push(''); }) ; __p.push('</div></div><div class="channel-going">'); var i = 0; _.each(users, function( user, i) { i++; ; __p.push('');  if( i == 1 && user.avatar) { ; __p.push('<em>进行中:</em>'); } ; __p.push('<a target="_blank" href="#homepage/reply/', user.uid ,'"><img src="', user.avatar ,'" alt="进行中"></a>'); }) ; __p.push('</div></div></div>');}return __p.join('');}});

 define('app/views/channel/ChannelFoldView',[ 
        'app/views/Base',
        'tpl!app/templates/channel/ChannelFoldView.html'
       ],
    function (View, template, ChannelFoldView ) {

        "use strict";
        return View.extend({
            tagName: 'div',
            className: 'channel-fold',
            template: template,
            construct: function () {
                this.listenTo(this.collection, 'change', this.render);
            }
        });
    });


define('tpl!app/templates/channel/ChannelWorksView.html', function() {return function(obj) { var __p=[],print=function(){__p.push.apply(__p,arguments);};with(obj||{}){__p.push('<div class="reply-item grid-item"><div class="reply-head"><span class="reply-avatar"><a target="_blank" href="#homepage/reply/', uid ,'"><img src="', avatar ,'" alt="头像"></a></span><span class="reply-name"><a href="#homepage/reply/',  uid ,'">', nickname ,'</a></span><span class="reply-create-time">'); var timeMatrixing = time(create_time); ; __p.push('', timeMatrixing ,'</span></div><div class="reply-main" time="1" ><a target="_blank" href="#replydetailplay/reply/', id ,'" class="clearfix reply-pic" ><img src="', image_url ,'" data-type="2" alt="作品" class="reply-works-pic" >');  var i = 0;  _.each(ask_uploads, function(ask,i) { i++; ; __p.push(''); if( i == 1) { ; __p.push('<img src="', ask.image_url ,'" data-type="1" alt="原图" class="reply-artwork-pic">'); } ; __p.push(''); }) ; __p.push('</a></div><div class="reply-footer"><div class="nav"><span class="pressed reply-nav nav-pressed" ask="0">作品</span><span class="pressed ask-nav" ask="2"> 原图</span><var class="nav-bottom"></var></div><div class="reply-section-icon"><span class="like like_toggle" data-type="', type ,'" data-id="', id ,'"> <i class="like-icon bg-sprite-new"></i> <em class="like-count ', (uped)? 'like-color': '' ,'">', up_count ,'</em></span><a class="comment"><i class="comment-icon bg-sprite-new"></i><em class="comment-count">', comment_count ,'</em></a></div></div></div>');}return __p.join('');}});

 define('app/views/channel/ChannelWorksView',[ 
        'app/views/Base',
        'masonry', 
        'imagesLoaded',
        'app/collections/Replies',
        'tpl!app/templates/channel/ChannelWorksView.html'
       ],
    function (View, masonry, imagesLoaded, Replies, template) {

        "use strict";
        return View.extend({
            collection: Replies,
            tagName: 'div',
            className: 'channel-reply-container grid',
            template: template,
            construct: function () {
                this.listenTo(this.collection, 'change', this.renderMasonry);
            },
            render: function() {
                this.renderMasonry();
            }
        });
    });


define('tpl!app/templates/channel/ActivityView.html', function() {return function(obj) { var __p=[],print=function(){__p.push.apply(__p,arguments);};with(obj||{}){__p.push('<div class="reply-item grid-item"><div class="reply-head"><span class="reply-avatar"><a target="_blank" href="#homepage/reply/', uid ,'"><img src="', avatar ,'" alt="头像"></a></span><span class="reply-name"><a href="#homepage/reply/',  uid ,'">', nickname ,'</a></span><span class="reply-create-time">'); var timeMatrixing = time(create_time); ; __p.push('', timeMatrixing ,'</span></div><div class="reply-main" time="1"><a target="_blank" href="#replydetailplay/reply/', id ,'" class="clearfix reply-pic"><img src="', image_url ,'" data-type="2" alt="作活动品" class="reply-artwork-pic-click"></a></div><div class="reply-footer"><div class="reply-section-icon"><span class="like like_toggle" data-type="', type ,'" data-id="', id ,'"> <i class="like-icon bg-sprite-new"></i> <em class="like-count ', (uped)? 'like-color': '' ,'">', up_count ,'</em></span><a class="comment"><i class="comment-icon bg-sprite-new"></i><em class="comment-count">', comment_count ,'</em></a></div></div></div>');}return __p.join('');}});

 define('app/views/channel/ActivityView',[ 
        'app/views/Base',
        'masonry', 
        'imagesLoaded',
        'app/collections/Replies',
        'tpl!app/templates/channel/ActivityView.html'
       ],
    function (View, masonry, imagesLoaded, Replies, template) {

        "use strict";
        return View.extend({
            collection: Replies,
            tagName: 'div',
            className: 'channel-reply-container grid',
            template: template,
            construct: function () {
                this.listenTo(this.collection, 'change', this.renderMasonry);
                this.scroll();
                this.collection.loading();
            },
            render: function() {
                this.renderMasonry();
            }
        });
    });


define('tpl!app/templates/channel/ActivityIntroView.html', function() {return function(obj) { var __p=[],print=function(){__p.push.apply(__p,arguments);};with(obj||{}){__p.push('<div class="fix-activity"><div class="activity-ifo"><em>', display_name ,'</em></div><div class="activity-theme clearfix"><span>活动主题：</span><em>', description ,'</em></div><div class="activity-time clearfix"><span>活动截止日期：</span><em>2014-1-1</em></div></div><div class="new-participation"><div class="participation"><em>最新参与</em></div><div class="participation-head clearfix">');   _.each(users, function(user) {  ; __p.push('<a href=""><img src="', user.avatar ,'" alt=""></a>'); }) ; __p.push('</div><div class="participation-fun clearfix"><div class="people-num"><i class="people-icon bg-sprite-new"></i><span>', download_count ,'</span></div><div class="qiu bg-sprite-new"></div><div class="see-num"><i class="see-icon bg-sprite-new"></i><span>', click_count ,'</span></div><div class="qiu bg-sprite-new"></div><div class="pic-num"><i class="pic-icon bg-sprite-new"></i><span>', replies_count ,'</span></div></div><div class="us-participation" data-id="', id ,'" data-ask-id="', ask_id ,'">我要参与</div></div>');}return __p.join('');}});

 define('app/views/channel/ActivityIntroView',[ 
        'app/views/Base',
        'tpl!app/templates/channel/ActivityIntroView.html'
       ],
    function (View, template) {

        "use strict";
        return View.extend({
            tagName: 'div',
            className: 'channel-reply-container grid',
            template: template,
            events: {
                "click .us-participation" : "participation"
            },
            participation:function(e) {
                var id = $(e.currentTarget).attr("data-id");
                var ask_id = $(e.currentTarget).attr("data-ask-id");
                 $.get('/record?target=' + ask_id +'&category_id='+ id +'&type=1', function( returnData ){
                    var info = returnData.info;
                    toast("参与成功,请在个人页面进行中上传作品");
                    if(returnData.info == undefined) {
                        var returnData = JSON.parse(returnData);
                    }
                });
            },
            construct: function() {
                this.listenTo(this.model, 'change', this.render);
            },            
        });
    });


define('tpl!app/templates/channel/ChannelDemandView.html', function() {return function(obj) { var __p=[],print=function(){__p.push.apply(__p,arguments);};with(obj||{}){__p.push('<div class="demmand-contain"><div href="#askflows" class="channel-demmand-contain center-loading-image-container"><span class="center-loading"><a target="_blank" href="#askdetail/ask/', ask_id ,'" class="is-loading"><img src="', image_url ,'" alt="" class="demmand-contain-img">'); if( ask_uploads.length > 1 ) {; __p.push('<i class="demmand-icon bg-sprite-new"></i>        '); } ; __p.push('    </a>    </span></div>    <a class="demmand-contain-position" href="#homepage/reply/', uid ,'">    <var>    <img src="', avatar ,'" alt="">    </var>    <span class="demmand-position-right">    <em>', nickname ,'</em>    <i>', desc ,'</i>    </span>    </a></div>');}return __p.join('');}});

 define('app/views/channel/ChannelDemandView',[ 
        'app/views/Base',
        'tpl!app/templates/channel/ChannelDemandView.html'
       ],
    function (View, template) {

        "use strict";
        return View.extend({
            tagName: 'div',
            className: 'father-grid',
            template: template,
            construct: function () {
                this.listenTo(this.collection, 'change', this.render);
                this.collection.loading();
            }
        });
    });


define('tpl!app/templates/channel/ChannelView.html', function() {return function(obj) { var __p=[],print=function(){__p.push.apply(__p,arguments);};with(obj||{}){__p.push('<div class="channel-contain"><div class="channel-header clearfix"><div id="channelNav" class="channel-nav clearfix" ></div><div href="/#askflows" class="header-nav present-nav activitHide"  data-type="ask"><img src="http://7u2spr.com1.z0.glb.clouddn.com/20151221-1501515677a3df9af43.png" alt=""><span>随意求P</span></div><div href="/#askflows" class="header-nav present-nav activitHide" data-type="reply"><img src="http://7u2spr.com1.z0.glb.clouddn.com/20151221-14341856779d6a0ee90.png" alt=""><span>最新作品</span></div></div><div class="channel-big-pic hide"><img src="/img/channelPic.png" alt=""></div><div class="demand-p hide"><div class="demand-header clearfix"><span>求P区</span><a class="askUrl" target="_blank" href="/#askflows/1"><em>更多</em><i class="bg-sprite-new demand-p-icon"></i></a></div><div id="channelDemand" class="channel-demmand clearfix" ></div></div><div class="channel-works clearfix"><div class="width-hide"><i class="scrollTop-icon clearfix bg-sprite-new"></i>        <a href="#ask-uploading-popup" class="ask-uploading-popup ask-uploading-popup-hide hide"><i class="askForP-icon clearfix bg-sprite-new upload-ask" ></i>        </a>        <a href="#login-popup" class="login-popup  hide login-popup-hide"><i class="askForP-icon clearfix bg-sprite-new"></i>        </a>        </div><!-- 频道 --><div class="channel-ask clearfix hide"><h3>求P区</h3></div><div class="channel-reply clearfix hide"><h3>最新作品</h3></div><div class="channel-works-header clearfix hide"><h3>作品区</h3><div class="channel-style"><span class="fold-icon bg-sprite-new"></span><span class="pic-icon bg-sprite-new"></span></div></div><!-- 活动 --><div class="channel-activity-works clearfix hide"><span id="hot-reply" class="color-change">最新作品</span><!-- <em>|</em> --><!-- <span id="new-reply">最新作品</span> --></div><div id="channelWorksPic" class="channel-reply-pic channel-reply-fold"></div></div><div class="channel-fix hide"><div id="activityIntro"></div></div></div>');}return __p.join('');}});

 define('app/views/channel/ChannelView',[ 
        'app/views/Base',
        'app/models/Activity',
        'app/collections/Asks', 
        'app/collections/Channels',
        'app/collections/Replies',
        'app/collections/Activities',
        'app/views/channel/ChannelFoldView',
        'app/views/channel/ChannelWorksView',
        'app/views/channel/ActivityView',
        'app/views/channel/ActivityIntroView',
        'app/views/channel/ChannelDemandView',
        'tpl!app/templates/channel/ChannelView.html'
       ],
    function (View, Activity, Asks,  Channels, Replies, Activities, ChannelFoldView, ChannelWorksView, ActivityView, ActivityIntroView, ChannelDemandView, template) {

        "use strict";
        return View.extend({
            template: template,
            events: {
                "click .like_toggle" : 'likeToggleLarge',
                "mouseover .reply-main": "channelFadeIn",
                "mouseleave .reply-main": "channelFadeOut",
                "click .fold-icon": "ChannelFold",
                "click .pic-icon": "ChannelPic",
                "click .download" : "download",
                "click .header-nav" : "colorChange", 
                "click .activitHide" : "channelOrActivity",
                "click .present-nav": "activityIntro",
                "click .scrollTop-icon" : "scrollTop",
                "mouseover .long-pic": "channelWidth",
                "mouseleave .long-pic": "channelWidth",
            },
            activityIntro:function(e) {
                var id = $(e.currentTarget).attr("data-id");
                var type = $(e.currentTarget).attr("data-type");

                if(type == "activity") {

                    var activity = new Activity;
                    activity.url = '/activities/' + id;
                    activity.fetch();

                    var activityIntro = new Backbone.Marionette.Region({el:"#activityIntro"});
                    var view = new ActivityIntroView({
                        model: activity
                    });
                    activityIntro.show(view);
                }
            },
            channelWidth: function(e) {
                if(e.type == "mouseover") {
                    $(e.currentTarget).siblings(".view-details").animate({
                        width: "20px"
                    }, 500);
                }
                if(e.type == "mouseleave") {
                    $(e.currentTarget).siblings(".view-details").stop(true, true).animate({
                        width: "0px"
                    }, 500);
                }
            },
            ChannelFold:function(e) {
                var category_id = $(".bgc-change").attr("data-id");
                var type = $(".bgc-change").attr("data-type");
                $("#channelWorksPic").empty();
                     setTimeout(function(){
                        var channel = new Channels;
                        var channelWorksFold = new Backbone.Marionette.Region({el:"#channelWorksPic"});
                        var view = new ChannelFoldView({
                            collection: channel
                        });

                        view.collection.reset();
                        view.collection.size = 10;
                        view.collection.data.category_id = category_id;
                        view.collection.data.page = 0;
                        view.collection.loading();
                        view.scroll(view);
                        channelWorksFold.show(view);

                        $(e.currentTarget).css({
                            backgroundPosition: "-155px -528px"
                        }).siblings(".pic-icon").css({
                            backgroundPosition: "-155px -501px"
                        })
                    },100);
            },
            channelOrActivity:function(e) {
                var self = this;
                var type    = $(e.currentTarget).attr("data-type");
                var id      = $(e.currentTarget).attr("data-id");
                $("#channelWorksPic").empty();
            
                setTimeout(function(){
                    if( type == "channel") {
                        var channel = new Channels;
                        var channelWorksFold = new Backbone.Marionette.Region({el:"#channelWorksPic"});
                        var view = new ChannelFoldView({
                            collection: channel
                        });
                        view.collection.reset();
                        view.collection.data.category_id = id;
                        view.collection.size = 10;
                        view.collection.data.page = 0;
                        view.collection.loading();
                        self.scroll(view);
                        channelWorksFold.show(view);

                        $(".fold-icon").css({
                            backgroundPosition: "-155px -528px"
                        }).siblings(".pic-icon").css({
                            backgroundPosition: "-155px -501px"
                        })

                    } else {
                        var activity = new Replies;
                        var activityWorksPic = new Backbone.Marionette.Region({el:"#channelWorksPic"});
                        var activity_view = new ActivityView({
                            collection: activity
                        });
                        activity_view.collection.reset();
                        activity_view.collection.data.category_id = id;
                        activity_view.collection.data.size = 6;
                        activity_view.collection.data.page = 0;
                        activity_view.collection.loading();

                        self.scroll(activity_view);
                        activityWorksPic.show(activity_view);
                    }
                },100);
            },
         
            onRender:function() {
                setTimeout(function(){
                    var id = $("body").attr("data-uid");
                    if( id ) {
                        $(".login-popup").addClass("hide");
                        $(".ask-uploading-popup-hide").removeClass('hide');
                    } else {
                        $(".ask-uploading-popup-hide").addClass('hide');
                        $(".login-popup").removeClass("hide");
                    }
                },500);
            },
            colorChange: function(e) {
                $("#channelWorksPic").empty();
                $('.header-back').addClass("height-reduce");
                $(".channel-header").find(".header-nav").removeClass('bgc-change');
                $(e.currentTarget).addClass("bgc-change");

                var id      =   $(e.currentTarget).attr("data-id");
                var type    =   $(e.currentTarget).attr("data-type");
                var askUrl  =   $(e.currentTarget).attr("href");
                                $(".askUrl").attr("href", askUrl);
                                $(".askForP-icon").attr("data-id",id);

                if( type == "activity" ) {
                    $(".channel-activity-works").removeClass('hide');
                    $(".channel-big-pic").removeClass('hide');
                    $(".demand-p").addClass('hide');
                    $(".channel-works-header").addClass('hide');
                    $(".channel-fix").removeClass('hide');
                    $(".askForP-icon").addClass("hide");
                    $(".channel-reply").addClass("hide");
                    $(".channel-ask").addClass("hide");
                    $(".channel-activity-works").addClass('hide');
                    $(".channel-activity-works").removeClass('hide');

                    var imgageUrl = $(e.currentTarget).attr("data-src");
                    $('.channel-big-pic img').attr("src",imgageUrl );
                } 
                if( type == "channel" )  {
                    $(".askForP-icon").removeClass("hide");
                    $(".channel-fix").addClass('hide');
                    $(".channel-big-pic").addClass('hide');
                    $(".channel-ask").addClass('hide');
                    $(".channel-activity-works").addClass('hide');
                    $(".demand-p").removeClass('hide');
                    $(".channel-works-header").removeClass('hide');
                    $(".reply-area").removeClass("hide");
                    $(".channel-reply").addClass("hide");


                }

                if( type == "ask") {
                    $(".demand-p").addClass("hide");
                    $(".channel-big-pic").addClass("hide");
                    $(".channel-activity-works").addClass('hide');
                    $(".channel-works-header").addClass("hide");
                    $(".channel-reply").addClass("hide");
                    $(".channel-ask").removeClass("hide");
                }

                if( type == "reply") {
                    $(".demand-p").addClass("hide");
                    $(".channel-big-pic").addClass("hide");
                    $(".channel-works-header").addClass("hide");
                    $(".channel-activity-works").addClass("hide");
                    $(".channel-ask").addClass("hide");
                    $(".channel-reply").removeClass("hide");
                }

                $(".pic-icon").css({
                    backgroundPosition: "-128px -501px"
                }).siblings(".fold-icon").css({
                    backgroundPosition: "-127px -528px"
                }) 

                 if( type == "channel" ) {
                    var ask = new Asks;
                    ask.data.size = 6;
                    ask.data.category_id = id;
                    ask.data.page = 0;

                    var channelDemand = new Backbone.Marionette.Region({el:"#channelDemand"});
                    var view = new ChannelDemandView({
                        collection: ask
                    });
                    channelDemand.show(view);
                } 
            },
            ChannelPic:function(e) {
                $("#channelWorksPic").empty();
                var id = $(".bgc-change").attr("data-id");
                var type = $(".bgc-change").attr("data-type");

                if(type == "channel") {
                        var reply = new Replies;
                        var channelWorksPic = new Backbone.Marionette.Region({el:"#channelWorksPic"});
                        var channel_view = new ChannelWorksView({
                            collection: reply
                        });
                        channel_view.collection.reset();
                        channel_view.collection.data.category_id = id;
                        channel_view.collection.data.size = 6;
                        channel_view.collection.data.page = 0;
                        channel_view.collection.loading();

                        channel_view.scroll(channel_view);
                        channelWorksPic.show(channel_view);
                        $(e.currentTarget).css({
                            backgroundPosition: "-128px -501px"
                        }).siblings(".fold-icon").css({
                            backgroundPosition: "-127px -528px"
                        })                              
                }
            },
            channelFadeIn: function(e) {
                var imgageHeight = $(e.currentTarget).height();
                $(e.currentTarget).css({
                    'height': imgageHeight + "px",
                    'line-height': imgageHeight + "px"
                });
                $(e.currentTarget).find(".reply-works-pic").fadeOut(1000);
                $(e.currentTarget).find(".reply-artwork-pic").fadeIn(1000);
                $(e.currentTarget).siblings(".reply-footer").find(".nav-bottom").animate({
                    marginLeft: "37px"
                }, 1000);
                $(e.currentTarget).siblings(".reply-footer").find(".ask-nav").addClass("nav-pressed");
                $(e.currentTarget).siblings(".reply-footer").find(".reply-nav").removeClass("nav-pressed");
            },
            channelFadeOut: function(e) {
                $(e.currentTarget).siblings(".reply-footer").find(".nav-bottom").stop(true, true).animate({
                    marginLeft: "0"
                }, 1000);
                $(e.currentTarget).find(".reply-artwork-pic").stop(true, true).fadeOut(1500);
                $(e.currentTarget).find(".reply-works-pic").stop(true, true).fadeIn(1500);
                $(e.currentTarget).siblings(".reply-footer").find(".ask-nav").removeClass("nav-pressed");
                $(e.currentTarget).siblings(".reply-footer").find(".reply-nav").addClass("nav-pressed");
            }
           
        });
    });


define('tpl!app/templates/channel/ChannelNavView.html', function() {return function(obj) { var __p=[],print=function(){__p.push.apply(__p,arguments);};with(obj||{}){__p.push('<div href="/#askflows/', id ,'" class="header-nav present-nav activitHide" data-src="', pc_pic ,'" data-id="', id ,'" data-type="', category_type ,'"><img src="', pc_pic ,'" alt=""><span>', display_name ,'</span></div>');}return __p.join('');}});

 define('app/views/channel/ChannelNavView',[ 
        'app/views/Base',
        'tpl!app/templates/channel/ChannelNavView.html'
       ],
    function (View, template) {

        "use strict";
        return View.extend({
            tagName: 'div',
            className: '',
            template: template,
            construct: function () {
                this.listenTo(this.collection, 'change', this.render);
                this.collection.loading();
            }
           
        });
    });
define('app/controllers/Channel',['underscore', 
        'app/collections/Categories', 
        'app/views/channel/ChannelView',
        'app/views/channel/ChannelWorksView',
        'app/views/channel/ChannelNavView',
        'app/views/channel/ChannelFoldView',
        ],
    function (_, Categories, ChannelView, ChannelWorksView, ChannelNavView, ChannelFoldView) {
        "use strict";

        return function() {
            setTimeout(function(){
                $('.header-back').addClass("height-reduce");
                $(".header-nav:first").trigger('click');
            },400);
            
     
            // main
            var view = new ChannelView();
            window.app.content.show(view);
            
            // 导航栏
            var categorie = new Categories;
            var channelNav = new Backbone.Marionette.Region({el:"#channelNav"});
            var view = new ChannelNavView({
                collection: categorie
            });
            channelNav.show(view);

        };
    });

var paths = [
    'marionette',
    'app/controllers/Index',
    'app/controllers/AskFlows',
    'app/controllers/ReplyFlows',
    'app/controllers/Message',
    'app/controllers/Trend',
    'app/controllers/Setting',
    'app/controllers/AskDetail',
    'app/controllers/Logout',
    'app/controllers/HomePage',
    'app/controllers/Search',
    'app/controllers/ReplyDetailPlay',
    'app/controllers/Channel',
    // 'app/controllers/Activity',
];

define('app/Router',
    [
        'marionette',
        'app/controllers/Index',
        'app/controllers/AskFlows',
        'app/controllers/ReplyFlows',
        'app/controllers/Message',
        'app/controllers/Trend',
        'app/controllers/Setting',
        'app/controllers/AskDetail',
        'app/controllers/Logout',
        'app/controllers/HomePage',
        'app/controllers/Search',
        'app/controllers/ReplyDetailPlay',
        'app/controllers/Channel'
        // 'app/controllers/Activity',
    ], 
    function (marionette) {
        'use strict';

        var routes = {};
        var controllers = {};
        //console.log(paths);

        for(var i = 1; i < paths.length; i ++) {
            var path = paths[i].substr('app/controllers/'.length);
            routes[path.toLowerCase()] = path;
            routes[path.toLowerCase() + '/:id'] = path;
            routes[path.toLowerCase() + '/:type/:id'] = path;
            controllers[path] = arguments[i];
        }

        //routes[''] = 'Asks';
        routes['*action'] = 'action';
        //extra action defined
        controllers['action'] = function (action) {
            //do nothing
            console.log(action);
        }
        //console.log(controllers);
        //console.log(routes);

        return marionette.AppRouter.extend({
            appRoutes: routes,
            controller: controllers
        });
    });

