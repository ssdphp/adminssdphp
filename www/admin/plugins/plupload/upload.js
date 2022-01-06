var upload = function (options) {
    var _count = 0;
    var _expando = new Date() - 0;

    var opt = options || {};

    var id = options.id = options.id || _expando + _count;

    options = $.extend(true, {}, upload.defaults, opt);

    var o = upload.get(id);
    if(o){
        return o;
    }
    return upload.list[id] = new upload.create(options);
};

var urlSafeBase64Encode=function(t){return(t=function(t){var e="ABCDEFGHIJKLMNOPQRSTUVWXYZabcdefghijklmnopqrstuvwxyz0123456789+/=",n=void 0,r=void 0,o=void 0,i=void 0,u=void 0,s=void 0,a=void 0,c=void 0,f=0,l=0,p="",h=[];if(!t)return t;t=function(t){if(null===t||void 0===t)return"";var e=t+"",n="",r=void 0,o=void 0,i=0;r=o=0,i=e.length;for(var u=0;u<i;u++){var s=e.charCodeAt(u),a=null;if(s<128)o++;else if(s>127&&s<2048)a=String.fromCharCode(s>>6|192,63&s|128);else if(63488&s^!0)a=String.fromCharCode(s>>12|224,s>>6&63|128,63&s|128);else{if(64512&s^!0)throw new RangeError("Unmatched trail surrogate at "+u);var c=e.charCodeAt(++u);if(64512&c^!0)throw new RangeError("Unmatched lead surrogate at "+(u-1));s=((1023&s)<<10)+(1023&c)+65536,a=String.fromCharCode(s>>18|240,s>>12&63|128,s>>6&63|128,63&s|128)}null!==a&&(o>r&&(n+=e.slice(r,o)),n+=a,r=o=u+1)}return o>r&&(n+=e.slice(r,i)),n}(t+"");do{n=t.charCodeAt(f++),r=t.charCodeAt(f++),o=t.charCodeAt(f++),i=(c=n<<16|r<<8|o)>>18&63,u=c>>12&63,s=c>>6&63,a=63&c,h[l++]=e.charAt(i)+e.charAt(u)+e.charAt(s)+e.charAt(a)}while(f<t.length);switch(p=h.join(""),t.length%3){case 1:p=p.slice(0,-2)+"==";break;case 2:p=p.slice(0,-1)+"="}return p}(t)).replace(/\//g,"_").replace(/\+/g,"-")}

var createMkFileUrl=function(t,e,n,r){var i=t+"/mkfile/"+e;null!=n&&(i+="/key/"+(0,urlSafeBase64Encode)(n)),r.mimeType&&(i+="/mimeType/"+(0,urlSafeBase64Encode)(r.mimeType));var u=r.fname;return u&&(i+="/fname/"+(0,urlSafeBase64Encode)(u)),r.params&&o(r.params).forEach(function(t){return i+="/"+encodeURIComponent(t[0])+"/"+(0,urlSafeBase64Encode)(t[1])}),i}

upload.create = function (options) {
    var that = this;

    //$.extend(this, new Plupload());

    var defaultSetting = {
        browse_button : options.id,
        runtimes : 'html5,flash,silverlight,html4',
        url: 'https://up.qiniup.com',
        chunk_size:4*1024*1024,
        max_retries: 3,
        filters : {
            max_file_size: '4mb',
            mime_types: [
                {title : "图片上传", extensions : "png,jpg,jpeg,gif"}
            ]
        },
        // Flash settings
        flash_swf_url : '/admin/plugins/plupload/Moxie.swf',
        multi_selection:false,
        // Silverlight settings
        silverlight_xap_url : '/admin/plugins/plupload/Moxie.xap'
    };

    options = $.extend(true, defaultSetting, options);
    this.Uploader = new plupload.Uploader(options);
    this.Uploader.init();

    this.Uploader.bind("Error",function (up,err) {
        console.log("error")
        console.log(err)
        alertfly.error(err.message)
        // $("#okmodel").modal("show")
        // $("#okmodel").find(".modal-body").text(err.message)
    })

    //BeforeUpload
    if (typeof options.BeforeUpload == 'function'){
        this.Uploader.bind('BeforeUpload',options.BeforeUpload)
    }else{
        this.Uploader.bind("BeforeUpload",function (up,file){
            console.log('BeforeUpload')
            var chunk_size = up.getOption('chunk_size')
            console.log(chunk_size)
            up.setOption('http_method',"POST");
            up.setOption('send_file_name',true);
            up.setOption('max_retries',3);

            if (file.size <= chunk_size ){
                up.setOption('url',"https://up.qiniup.com");
                up.setOption('multipart',true);
                up.setOption('multipart_params',{
                    key:"zhedi/upload/"+file.id+".png",
                    token:options.token
                });
                up.setOption('send_chunk_number',false);
                up.setOption('required_features',"");
            }else{
                up.setOption('url',"https://up.qiniup.com/mkblk/"+chunk_size);
                up.setOption('headers', {
                    Authorization:"UpToken "+options.token,
                });
                up.setOption('send_chunk_number',true);
                up.setOption('required_features',"chunks");
                up.setOption('multipart',false);
                up.setOption('multipart_params',{})

            }
        })
    }

    if (typeof options.ChunkUploaded == 'function'){
        this.Uploader.bind("ChunkUploaded",options.ChunkUploaded)
    }else{
        this.Uploader.bind("ChunkUploaded",function (up,file,info) {
            console.log("ChunkUploaded")
            if(info.response){
                var ret = JSON.parse(info.response)
                var leftSize = info.total - info.offset;
                var chunk_size = up.getOption && up.getOption("chunk_size");
                if (leftSize < chunk_size) {
                    up.setOption({
                        url: "https://up.qiniup.com" + "/mkblk/" + leftSize
                    });
                }
                up.setOption({
                    headers: {
                        Authorization: "UpToken {{.uptoken}}"
                    }
                });

                if (ret && ret.ctx){
                    ctx.push(ret.ctx)
                }
            }
        })
    }
    if (typeof options.UploadProgress == 'function'){
        this.Uploader.bind("UploadProgress",options.UploadProgress);
    }else{
        this.Uploader.bind("UploadProgress",function (up,file) {
            console.log("UploadProgress")
            var percent = file.percent + "%";
            $('#progress').find(".progress-num").text("已上传: "+percent)
        })
    }
    if (typeof options.FileUploaded == 'function'){
        this.Uploader.bind("FileUploaded",options.FileUploaded);
    }else{
        this.Uploader.bind("FileUploaded", function(uploader, file, info) {
            console.log("FileUploaded")
            var c = ctx.join(",")
            $('#progress').find(".progress-num").text("已完成: 100%")
            if ( file.size < uploader.getOption('chunk_size')){
                var d = JSON.parse(info.response)

                if (d && d.key){
                    var src = options.domain+d.key
                    var img = new Image()
                    img.src=src
                    img.onload=function (){
                        $('#img').attr('src',src);
                        $('input[name='+options.name+']').val(src)
                        $('#progress').hide();
                    }

                }
                return
            }
            var key = "zhedi/upload/"+file.id+".png";

            var requestUrl = createMkFileUrl(
                "https://up.qiniup.com",
                file.size,
                key,
                {}
            );
            $.ajax({
                url:requestUrl,
                type:"POST",
                beforeSend: function (request) {
                    request.setRequestHeader("Authorization", "UpToken "+options.token);
                },
                contentType:"text/plain",
                data:c,
                success:function (ret){
                    console.log(ret)
                    var src = options.domain+ret.key
                    var img = new Image()
                    img.src=src
                    img.onload=function (){

                        $('#img').attr('src',src);
                        $('input[name='+options.name+']').val(src)
                        $('#progress').hide();
                    }

                }
            })
        });
    }


    return this.Uploader;

};


/**
 * 根据 ID 获取某对话框 API
 * @param    {String}    对话框 ID
 * @return   {Object}    对话框 API (实例)
 */
upload.get = function (id) {
    return id === undefined
        ? upload.list
        : upload.list[id];
};
upload.list = {};