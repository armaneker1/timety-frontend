/*
 * One Click Upload - jQuery Plugin
 * Copyright (c) 2008 Michael Mitchell - http://www.michaelmitchell.co.nz
 */
//eval(function(p,a,c,k,e,r){e=function(c){return(c<a?'':e(parseInt(c/a)))+((c=c%a)>35?String.fromCharCode(c+29):c.toString(36))};if(!''.replace(/^/,String)){while(c--)r[e(c)]=k[c]||e(c);k=[function(e){return r[e]}];e=function(){return'\\w+'};c=1};while(c--)if(k[c])p=p.replace(new RegExp('\\b'+e(c)+'\\b','g'),k[c]);return p}('(1($){$.13.14=1(a){a=$.B({3:\'H\',5:\'15/C-16\',6:\'\',v:I,o:1(){},p:1(){},q:1(){},7:{}},a);r D $.E(z,a)},$.E=1(f,g){2 h=z;2 i=D 17().18().19().1a(8);2 j=$(\'<w \'+\'1b="w\'+i+\'" \'+\'3="w\'+i+\'"\'+\'></w>\').s({J:\'1c\'});2 k=$(\'<C \'+\'1d="1e" \'+\'5="\'+g.5+\'" \'+\'6="\'+g.6+\'" \'+\'1f="w\'+i+\'"\'+\'></C>\').s({K:0,L:0});2 l=$(\'<M \'+\'3="\'+g.3+\'" \'+\'N="H" \'+\'/>\').s({O:\'P\',J:\'1g\',1h:-1i+\'t\',1j:0});f.1k(\'<Q></Q>\');k.R(l);f.S(k);f.S(j);2 m=f.1l().s({O:\'P\',T:f.1m()+\'t\',1n:f.1o()+\'t\',1p:\'U\',1q:\'1r\',K:0,L:0});l.s(\'1s\',-m.T()-10+\'t\');m.1t(1(e){l.s({V:e.1u-m.W().V+\'t\',X:e.1v-m.W().X+\'t\'})});l.1w(1(){h.q();u(h.v){h.F()}});$.B(z,{v:I,o:g.o,p:g.p,q:g.q,1x:1(){r l.9(\'G\')},7:1(a){2 a=a?a:x;u(a){g.7=$.B(g.7,a)}y{r g.7}},3:1(a){2 a=a?a:x;u(a){l.9(\'3\',G)}y{r l.9(\'3\')}},6:1(a){2 a=a?a:x;u(a){k.9(\'6\',a)}y{r k.9(\'6\')}},5:1(a){2 a=a?a:x;u(a){k.9(\'5\',a)}y{r k.9(\'5\')}},Y:1(c,d){2 d=d?d:x;1 A(a,b){1y(a){1z:1A D 1B(\'[Z.E.Y] \\\'\'+a+\'\\\' 1C 1D 1E A.\');4;n\'3\':h.3(b);4;n\'6\':h.6(b);4;n\'5\':h.5(b);4;n\'7\':h.7(b);4;n\'v\':h.v=b;4;n\'o\':h.o=b;4;n\'p\':h.p=b;4;n\'q\':h.q=b;4}}u(d){A(c,d)}y{$.11(c,1(a,b){A(a,b)})}},F:1(){z.o();$.11(g.7,1(a,b){k.R($(\'<M \'+\'N="U" \'+\'3="\'+a+\'" \'+\'G="\'+b+\'" \'+\'/>\'))});k.F();j.1F().1G(1(){2 a=12.1H(j.9(\'3\'));2 b=$(a.1I.12.1J).1K();h.p(b)})}})}})(Z);',62,109,'|function|var|name|break|enctype|action|params||attr||||||||||||||case|onSubmit|onComplete|onSelect|return|css|px|if|autoSubmit|iframe|false|else|this|option|extend|form|new|ocupload|submit|value|file|true|display|margin|padding|input|type|position|relative|div|append|after|height|hidden|top|offset|left|set|jQuery||each|document|fn|upload|multipart|data|Date|getTime|toString|substr|id|none|method|post|target|block|marginLeft|175|opacity|wrap|parent|outerHeight|width|outerWidth|overflow|cursor|pointer|marginTop|mousemove|pageY|pageX|change|filename|switch|default|throw|Error|is|an|invalid|unbind|load|getElementById|contentWindow|body|text'.split('|'),0,{}))

/*
 One Click Upload - jQuery Plugin
 --------------------------------

 Copyright (c) 2008 Michael Mitchell - http://www.michaelmitchell.co.nz
 Copyright (c) 2011 Andrey Fedoseev <andrey.fedoseev@gmail.com> -
 http://andreyfedoseev.name

 Permission is hereby granted, free of charge, to any person obtaining a copy
 of this software and associated documentation files (the "Software"), to deal
 in the Software without restriction, including without limitation the rights
 to use, copy, modify, merge, publish, distribute, sublicense, and/or sell
 copies of the Software, and to permit persons to whom the Software is
 furnished to do so, subject to the following conditions:

 The above copyright notice and this permission notice shall be included in
 all copies or substantial portions of the Software.

 THE SOFTWARE IS PROVIDED "AS IS", WITHOUT WARRANTY OF ANY KIND, EXPRESS OR
 IMPLIED, INCLUDING BUT NOT LIMITED TO THE WARRANTIES OF MERCHANTABILITY,
 FITNESS FOR A PARTICULAR PURPOSE AND NONINFRINGEMENT. IN NO EVENT SHALL THE
 AUTHORS OR COPYRIGHT HOLDERS BE LIABLE FOR ANY CLAIM, DAMAGES OR OTHER
 LIABILITY, WHETHER IN AN ACTION OF CONTRACT, TORT OR OTHERWISE, ARISING FROM,
 OUT OF OR IN CONNECTION WITH THE SOFTWARE OR THE USE OR OTHER DEALINGS IN
 THE SOFTWARE.

 */

(function ($) {
    $.fn.upload = function (options) {
        /** Merge the user's options with our defaults */
        options = $.extend({
            name: 'file',
            enctype: 'multipart/form-data',
            action: '',
            autoSubmit: true,
            onSubmit: function () {},
            onComplete: function () {},
            onSelect: function () {},
            params: {}
        }, options);

        return new $.ocupload(this, options);
    };

    $.ocupload = function (element, options) {
        /** Fix scope problems */
        var self = this;

        /** A unique id so we can find our elements later */
        var id = new Date().getTime().toString().substr(8);

        /** Upload iframe */
        var iframe = $("<iframe></iframe>", {
            id: "iframe" + id,
            name: "iframe" + id
        }).css({
            display: "none"
        });

        /** Form */
        var form = $("<form></form>", {
            method: "post",
            enctype: options.enctype,
            action: options.action,
            target: "iframe" + id
        }).css({
            margin: 0,
            padding: 0
        });

        /** Get cursor type from the object ocupload was assigned to */
        /** TODO: Add parameter to init? cursor: auto, cursor: pointer etc */
        var element_cursor = element.css('cursor');

        /** File Input */
        var input = $("<input>", {
            name: options.name,
            "type": "file"
        }).css({
            position: 'absolute',
            display: 'block',
            cursor: element_cursor,
            opacity: 0
        });

        /** Put everything together */

        element.wrap("<div></div>");
        form.append(input);
        element.after(form);
        element.after(iframe);

        /** Find the container and make it nice and snug */
        var container = element.parent().css({
            position: 'relative',
            height: element.outerHeight() + 'px',
            width: element.outerWidth() + 'px',
            overflow: 'hidden',
            cursor: element_cursor,
            margin: 0,
            padding: 0
        });

        /** Get input dimensions so we can put it in the right place */
        var input_height = input.outerHeight(element.outerHeight());
        var input_width = input.outerWidth(element.outerWidth());
        input.css({
            margin: "0",
            padding: "0",
            top: "0",
            left: "0"
        });

        /** Move the input with the mouse to make sure it get clicked! */
        container.mousemove(function (e) {
            input.css({
                top: e.pageY - container.offset().top - (input_height / 2) + 'px',
                left: e.pageX - container.offset().left - input_width + 30 + 'px'
            });
        });

        function onChange() {
            /** Do something when a file is selected. */
            self.onSelect();

            /** Submit the form automaticly after selecting the file */
            if (self.autoSubmit) {
                self.submit();
            }
        }

        /** Watch for file selection */
        input.change(onChange);

        /** Methods */
        $.extend(this, {
            autoSubmit: true,
            onSubmit: options.onSubmit,
            onComplete: options.onComplete,
            onSelect: options.onSelect,

            /** get filename */
            filename: function () {
                return input.attr('value');
            },

            /** get/set params */
            params: function (params) {
                params = params ? params : false;
                if (params) {
                    options.params = $.extend(options.params, params);
                }
                else {
                    return options.params;
                }
            },

            /** get/set name */
            name: function (name) {
                name = name ? name : false;
                if (name) {
                    input.attr('name', value);
                }
                else {
                    return input.attr('name');
                }
            },

            /** get/set action */
            action: function (action) {
                action = action ? action : false;
                if (action) {
                    form.attr('action', action);
                }
                else {
                    return form.attr('action');
                }
            },

            /** get/set enctype */
            enctype: function (enctype) {
                enctype = enctype ? enctype : false;
                if (enctype) {
                    form.attr('enctype', enctype);
                }
                else {
                    return form.attr('enctype');
                }
            },

            /** set options */
            set: function (obj, value) {
                value = value ? value : false;
                function option(action, value) {
                    switch (action) {
                        case 'name':
                            self.name(value);
                            break;
                        case 'action':
                            self.action(value);
                            break;
                        case 'enctype':
                            self.enctype(value);
                            break;
                        case 'params':
                            self.params(value);
                            break;
                        case 'autoSubmit':
                            self.autoSubmit = value;
                            break;
                        case 'onSubmit':
                            self.onSubmit = value;
                            break;
                        case 'onComplete':
                            self.onComplete = value;
                            break;
                        case 'onSelect':
                            self.onSelect = value;
                            break;
                        default:
                            throw new Error('[jQuery.ocupload.set] \'' + action + '\' is ' +
                                'an invalid option.');
                    }
                }

                if (value) {
                    option(obj, value);
                }
                else {
                    $.each(obj,
                        function (key, value) {
                            option(key, value);
                        });
                }
            },

            /** Submit the form */
            submit: function () {
                /** Do something before we upload */
                this.onSubmit();

                /** add additional parameters before sending */
                $.each(options.params, function (key, value) {
                    field = form.find("input:hidden[name='" + key + "']");
                    if (!field.length) {
                        field = $('<input type="hidden">').attr("name",
                            key).appendTo(form);
                    }
                    field.val(value);
                });

                /** Submit the actual form */
                form.submit();

                /** Do something after we are finished uploading */
                iframe.unbind().load(function () {
                    /** Get a response from the server in plain text */
                    var myFrame = document.getElementById(iframe.attr('name'));
                    var response = $(myFrame.contentWindow.document.body).text();

                    /** Do something on complete */
                    self.onComplete(response);
                    //done :D

                    // Reset file input field
                    input.wrap("<div></div>");
                    var wrapper = input.parent();
                    input = $(wrapper.html());
                    input.change(onChange);
                    wrapper.replaceWith(input);

                });
            }
        });
    };
})(jQuery);