
(function(global, $){

    var TYPE_TEXT = 1;
    var TYPE_DEVICEVALUE = 2;
    var TYPE_CHECKBOX = 3;
    var TYPE_VIDEO = 4;
    var TYPE_DEVICEMULTIVALUE = 5;

    var DomoWidget = function(){
        
        var _widgets = {};
        var AMQPSendFunc = null;
        
        var addWidget = function(ind, obj){
            _widgets[ind] = new $DomoWidget(); 
            _widgets[ind].setObj(obj);
            return _widgets[ind];
        }
        
        var AMQPRecieve = function(rec){
            if(rec['command'] == 'DEVICE_VALUE'){
                var deviceID = rec['ID'];
                var value = rec['value'];

                for(var ind in _widgets){
                    var params = _widgets[ind].getParams();
                    if(params.widgetType == TYPE_DEVICEVALUE){
                        if(deviceID == params.wp_deviceID){
                            params.wp_value = value;
                            _widgets[ind].update(params);
                        }
                    } else if(params.widgetType == TYPE_CHECKBOX){
                        if(deviceID == params.wp_deviceID){
                            params.wp_value = value;
                            _widgets[ind].update(params);
                        }
                    } else if(params.widgetType == TYPE_DEVICEMULTIVALUE){
                        if(deviceID == params.wp_deviceID){
                            params.wp_value = value;
                            _widgets[ind].update(params);
                        }
                    }
                }

            } else if(rec['command'] == 'NFC_DATA'){
                var deviceID = rec['ID'];
                var value = rec['uuid'];
                
                for(var ind in _widgets){
                    var params = _widgets[ind].getParams();
                    if(params.widgetType == TYPE_DEVICEVALUE){
                        if(deviceID == params.wp_deviceID){
                            params.wp_value = value;
                            _widgets[ind].update(params);
                        }
                    }
                }
                
            }

        };
        
        
        return {
            
            getCount: function(){ return Object.keys(_widgets).length; },
            addWidget: function(ind, obj){ return addWidget(ind, obj); },
            getWidget: function(ind){ return _widgets[ind]; },
            
            AMQPSend: function(func){ AMQPSendFunc = func; },
            AMQPSendFunc: function(){ return AMQPSendFunc; },
            AMQPRecieve: function(rec){ AMQPRecieve(rec); }
            
        };
        
    }
    
    var $DomoWidget = function(){
        var _obj = null;
        var _params = null;
  
        var update = function(params){

            var type = null;
            if(!params){
                params = {};

                params.widgetType = $("#SceneWidget_type").val();

                var arr = $("#wp_type_"+$("#SceneWidget_type").val()+" form").serializeArray();
                for(var ind in arr){
                    params[arr[ind].name] = arr[ind].value;
                }
                
                arr = $("#wp_widget_css form").serializeArray();
                for(var ind in arr){
                    params[arr[ind].name] = arr[ind].value;
                }
                
            }
            _params = params;
            
            var x = _obj.css("left");
            var y = _obj.css("top");
            
            _obj.attr("style", params.wp_css);
            _obj.css("left", x);
            _obj.css("top", y);

            if(params.widgetType == TYPE_TEXT){

                _obj.text( params.wp_text );
                if(params.wp_fontsize != "") _obj.css("font-size", params.wp_fontsize);
                if(params.wp_color != "") _obj.css("color", params.wp_color);
                
            } else if(params.widgetType == TYPE_DEVICEVALUE){

                var imgSrc = "/images/icons/"+params.wp_imagevalue;

                var style = "";
                style += params.wp_imagewidth != "" ? "width: "+params.wp_imagewidth+"px; " : "";
                style += params.wp_imageheight != "" ? "height: "+params.wp_imageheight+"px; " : "";

                var imgContent = "<img src=\""+imgSrc+"\""+(style != "" ? " style=\""+style+"\"" : "")+"/>";

                style = "";
                style += params.wp_valuefontsize != "" ? "font-size: "+params.wp_valuefontsize+"px; " : "";
                style += params.wp_valuecolor != "" ? "color: "+params.wp_valuecolor+"; " : "";

                var valueContent = "<span"+(style != "" ? " style=\""+style+"\"" : "")+">"+params.wp_value+"</span>";

                var val = params.wp_template.replace(/\{\$image\}/g, imgContent);
                val = val.replace(/\{\$value\}/g, valueContent);
                _obj.html( val );

            } else if(params.widgetType == TYPE_CHECKBOX){
                //alert(objInfo(params));
                var imgSrc = "/images/icons/"+params.wp_image0value;
                if(params.wp_value == 1)
                    imgSrc = "/images/icons/"+params.wp_image1value;

                var style = "";
                style += params.wp_imagewidth != "" ? "width: "+params.wp_imagewidth+"px; " : "";
                style += params.wp_imageheight != "" ? "height: "+params.wp_imageheight+"px; " : "";

                var imgContent = "<img src=\""+imgSrc+"\""+(style != "" ? " style=\""+style+"\"" : "")+"/>";

                var val = imgContent;
                _obj.html( val );

            } else if(params.widgetType == TYPE_VIDEO){

                var style = "";
                style += params.wp_imagewidth != "" ? "width: "+params.wp_imagewidth+"px; " : "";
                style += params.wp_imageheight != "" ? "height: "+params.wp_imageheight+"px; " : "";

                var imgSrc = "";
                if(params.wp_stream == 1){
                    imgSrc = params.wp_url;
                } else{
                    imgSrc = "/images/icons/"+params.wp_imagevalue;
                }

                //var imgContent = "<img src=\""+params.wp_url+"\""+(style != "" ? " style=\""+style+"\"" : "")+"/>";
                var imgContent = "<img src=\""+imgSrc+"\""+(style != "" ? " style=\""+style+"\"" : "")+"/>";

                var val = imgContent;
                _obj.html( val );

            } else if(params.widgetType == TYPE_DEVICEMULTIVALUE){

                var imgSrc = "/images/icons/"+params.wp_imagevalue;

                var style = "";
                style += params.wp_imagewidth != "" ? "width: "+params.wp_imagewidth+"px; " : "";
                style += params.wp_imageheight != "" ? "height: "+params.wp_imageheight+"px; " : "";

                var imgContent = "<img src=\""+imgSrc+"\""+(style != "" ? " style=\""+style+"\"" : "")+"/>";

                style = "";
                style += params.wp_valuefontsize != "" ? "font-size: "+params.wp_valuefontsize+"px; " : "";
                style += params.wp_valuecolor != "" ? "color: "+params.wp_valuecolor+"; " : "";


                var val = params.wp_template.replace(/\{\$image\}/g, imgContent);

                var vals = params.wp_value.split("|");
                //alert(objInfo(params));
                //alert(params.wp_value);
                //alert(vals);
                for(var ind in vals){
                    var valueContent = "<span"+(style != "" ? " style=\""+style+"\"" : "")+">"+vals[ind]+"</span>";
                    //alert("val = val.replace(/\\{$val\\["+ind+"\\]\\}/g, valueContent);");
                    //eval("val = val.replace(/\{$val\["+ind+"\]\}/g, valueContent);");
                    var rexp = new RegExp("\\{\\$val\\["+ind+"\\]\\}", "g");
                    val = val.replace(rexp, valueContent);
                }
                //val = val.replace(/\{\$val\[0\]\}/g, vals[0]);
                //val = val.replace(/\{\$val\[1\]\}/g, vals[1]);
                _obj.html( val );
                //alertify.success(val);




//                var valueContent = "<span"+(style != "" ? " style=\""+style+"\"" : "")+">"+params.wp_value+"</span>";
//
//                var val = params.wp_template.replace(/\{\$image\}/g, imgContent);
//                val = val.replace(/\{\$value\}/g, valueContent);
//                _obj.html( val );

            }


            if(_obj.attr("id") == "widgetIcon" && $("#widgetID").val() != ""){
                $("#scene_container #scene_widgets .item[data-widget-id="+$("#widgetID").val()+"]").DomoWidget("update", params);
            }
            
        }
  
  
        var click = function(){
            
            var clickMethods = {};
            clickMethods[TYPE_CHECKBOX] = _click_Checkbox;
            clickMethods[TYPE_VIDEO] = _click_Video;
            
            if(typeof clickMethods[_params.widgetType] === "function"){
                clickMethods[_params.widgetType]();
            }

        };

        var _click_Checkbox = function(){
            if(_params.wp_interact == 1){   // интерактивный элемент
                var CMD_WRITE = 2;
                var func = global.DomoWidget.AMQPSendFunc();

                if(typeof func === "function"){
                    func( {command: CMD_WRITE, deviceID: _params.wp_deviceID, value: 1-_params.wp_value} );
                }

            }
        };

        var _click_Video = function(){
            if(_params.wp_stream == 0){     // трансляция по умолчанию не идёт, показать в отдельном окне
                $.fancybox({
                    content: "<img src='"+_params.wp_url+"'/>",
                    fitToView: false,
                    autoResize: true,
                    afterShow: function(){
                        
                    },
                    onUpdate: function(){
                        
                    }
                });
            }
        };
  
  
        return {
            
            setObj: function(obj){ _obj = obj; },
            getObj: function(){ return _obj; },
            init: function(params){ update(params); },
            update: function(params){ update(params); },
            getParams: function(){ return _params; },
            click: click
            
        };
        
    }
    
    
    $.fn.DomoWidget = function(a, b){

        var action = null, options = null, func = null;
        if(typeof a === "string") action = a;
        else if(typeof a === "object") options = a;
        
        if(typeof b === "object") options = b;

        if(typeof a === "function") func = a;
        else if(typeof b === "function") func = b;

        
        var widget;
        if(typeof this.data("domowidget-ind") === "undefined"){
            var count = global.DomoWidget.getCount();
            this.data("domowidget-ind", count);
            widget = global.DomoWidget.addWidget(count, this);
            widget.init(options);
            this.click(widget.click);
        } else{
            widget = global.DomoWidget.getWidget(this.data("domowidget-ind"));
        }
        
        
        if(action == "update"){
            
            widget.update(options);
            
        } else if(action == "click"){
            
            if(func == null){
                this.click();
            } else{
                this.unbind("click");
                this.click(func);
            }
            
        }
        return this;
        
    }
    
    if(typeof global.DomoWidget === "undefined"){
		global.DomoWidget = new DomoWidget();
	}    
    
})(this, jQuery);

