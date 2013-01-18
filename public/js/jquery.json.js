/**
 * labs_json Script by Giraldo Rosales.
 * Version 1.0
 * Visit www.liquidgear.net for documentation and updates.
 *
 *
 * Copyright (c) 2009 Nitrogen Design, Inc. All rights reserved.
 * 
 * Permission is hereby granted, free of charge, to any person
 * obtaining a copy of this software and associated documentation
 * files (the "Software"), to deal in the Software without
 * restriction, including without limitation the rights to use,
 * copy, modify, merge, publish, distribute, sublicense, and/or sell
 * copies of the Software, and to permit persons to whom the
 * Software is furnished to do so, subject to the following
 * conditions:
 * 
 * The above copyright notice and this permission notice shall be
 * included in all copies or substantial portions of the Software.
 * 
 * THE SOFTWARE IS PROVIDED "AS IS", WITHOUT WARRANTY OF ANY KIND,
 * EXPRESS OR IMPLIED, INCLUDING BUT NOT LIMITED TO THE WARRANTIES
 * OF MERCHANTABILITY, FITNESS FOR A PARTICULAR PURPOSE AND
 * NONINFRINGEMENT. IN NO EVENT SHALL THE AUTHORS OR COPYRIGHT
 * HOLDERS BE LIABLE FOR ANY CLAIM, DAMAGES OR OTHER LIABILITY,
 * WHETHER IN AN ACTION OF CONTRACT, TORT OR OTHERWISE, ARISING
 * FROM, OUT OF OR IN CONNECTION WITH THE SOFTWARE OR THE USE OR
 * OTHER DEALINGS IN THE SOFTWARE.
 **/
 
/**
 * HOW TO USE
 * ==========
 * Encode:
 * var obj = {};
 * obj.name	= "Test JSON";
 * obj.type	= "test";
 * $.json.encode(obj); //output: {"name":"Test JSON", "type":"test"}
 * 
 * Decode:
 * $.json.decode({"name":"Test JSON", "type":"test"}); //output: object
 * 
 */

jQuery.json = {
    encode:function(value, replacer, space) {
		var i;
		gap = '';
		var indent = '';
		
		if (typeof space === 'number') {
			for (i = 0; i < space; i += 1) {
				indent += ' ';
			}
			
		} else if (typeof space === 'string') {
			indent = space;
		}
		
		rep = replacer;
		if (replacer && typeof replacer !== 'function' &&
				(typeof replacer !== 'object' ||
				 typeof replacer.length !== 'number')) {
			throw new Error('JSON.encode');
		}
		
		return this.str('', {'': value});
    },
    
    decode:function(text, reviver) {
		var j;
		var cx = /[\u0000\u00ad\u0600-\u0604\u070f\u17b4\u17b5\u200c-\u200f\u2028-\u202f\u2060-\u206f\ufeff\ufff0-\uffff]/g;
		
		function walk(holder, key) {
			var k, v, value = holder[key];
			
			if (value && typeof value === 'object') {
				for (k in value) {
					if (Object.hasOwnProperty.call(value, k)) {
						v = walk(value, k);
						if (v !== undefined) {
							value[k] = v;
						} else {
							delete value[k];
						}
					}
				}
			}
			return reviver.call(holder, key, value);
		}
		
		cx.lastIndex = 0;
		
		if (cx.test(text)) {
			text = text.replace(cx, function (a) {
				return '\\u' + ('0000' + a.charCodeAt(0).toString(16)).slice(-4);
			});
		}
		
		if (/^[\],:{}\s]*$/.test(text.replace(/\\(?:["\\\/bfnrt]|u[0-9a-fA-F]{4})/g, '@').replace(/"[^"\\\n\r]*"|true|false|null|-?\d+(?:\.\d*)?(?:[eE][+\-]?\d+)?/g, ']').replace(/(?:^|:|,)(?:\s*\[)+/g, ''))) {
			j = eval('(' + text + ')');
			return typeof reviver === 'function' ? walk({'': j}, '') : j;
		}
		
		throw new SyntaxError('JSON.parse');
	},
	
    f:function(n) {
        return n < 10 ? '0' + n : n;
    },
	
	DateToJSON:function(key) {
		return this.getUTCFullYear() + '-' + this.f(this.getUTCMonth() + 1) + '-' + this.f(this.getUTCDate())      + 'T' + this.f(this.getUTCHours())     + ':' + this.f(this.getUTCMinutes())   + ':' + this.f(this.getUTCSeconds())   + 'Z';
	},
	
	StringToJSON:function(key) {
		return this.valueOf();
    },
    
    quote:function(string) {
        var meta = {'\b': '\\b','\t': '\\t','\n': '\\n','\f': '\\f','\r': '\\r','"' : '\\"','\\': '\\\\'};
        var escapable = /[\\\"\x00-\x1f\x7f-\x9f\u00ad\u0600-\u0604\u070f\u17b4\u17b5\u200c-\u200f\u2028-\u202f\u2060-\u206f\ufeff\ufff0-\uffff]/g;
        
        escapable.lastIndex = 0;
        return escapable.test(string) ?
            '"' + string.replace(escapable, function (a) {
                var c = meta[a];
                return typeof c === 'string' ? c :
                    '\\u' + ('0000' + a.charCodeAt(0).toString(16)).slice(-4);
            }) + '"' :
            '"' + string + '"';
    },
    
    str:function(key, holder) {
        var indent='', gap = '', i, k, v, length, mind = gap, partial, value = holder[key];
        
        if (value && typeof value === 'object') {
            switch((typeof value)) {
            	case 'date':
            		this.DateToJSON(key);
            		break;
            	default:
            		this.StringToJSON(key);
            		break;
            }
        }
        
        if (typeof rep === 'function') {
            value = rep.call(holder, key, value);
        }
        switch (typeof value) {
        	case 'string':
        	    return this.quote(value);
			case 'number':
	            return isFinite(value) ? String(value) : 'null';
			case 'boolean':
			case 'null':
	            return String(value);
	        case 'object':
				if (!value) {
					return 'null';
				}
				gap += indent;
				partial = [];
				
				if (Object.prototype.toString.apply(value) === '[object Array]') {
					length = value.length;
					
					for (i = 0; i < length; i += 1) {
						partial[i] = this.str(i, value) || 'null';
					}
	
					v = partial.length === 0 ? '[]' : gap ? '[\n' + gap + partial.join(',\n' + gap) + '\n' + mind + ']' : '[' + partial.join(',') + ']';
					gap = mind;
					return v;
				}
					
				if (rep && typeof rep === 'object') {
					length = rep.length;
					for (i = 0; i < length; i += 1) {
						k = rep[i];
						if (typeof k === 'string') {
							v = this.str(k, value);
							if (v) {
								partial.push(this.quote(k) + (gap ? ': ' : ':') + v);
							}
						}
					}
				} else {
					for (k in value) {
						if (Object.hasOwnProperty.call(value, k)) {
							v = this.str(k, value);
							if (v) {
								partial.push(this.quote(k) + (gap ? ': ' : ':') + v);
							}
						}
					}
				}

				v = partial.length === 0 ? '{}' :
					gap ? '{\n' + gap + partial.join(',\n' + gap) + '\n' +
							mind + '}' : '{' + partial.join(',') + '}';
				gap = mind;
				return v;
		}
	}
};
