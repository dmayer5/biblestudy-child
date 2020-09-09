             
//jQuery('.lesson-single-question').each(function(){
    //jQuery(this).attr('index-id',  jQuery(this).index());
//})
var questions = document.querySelectorAll('.lesson-single-question');
for( i=0; i<questions.length; i++ ){
    questions[i].setAttribute("index-id", i+1);
}
jQuery('.lesson-single-question').each(function(){
 jQuery(this).addClass('runingggg');
    var formData = {
                                'hhtmlleer_id'              :  jQuery(this).attr('index-id'),
                                'posst_id'             : selectify_vars.postID,
                            };
                    jQuery.ajax({
                        type        : 'POST', // define the type of HTTP verb we want to use (POST for our form)
                        url         : '/selectiion_helper.php', // the url where we want to POST
                        data        : formData, // our data object
                        dataType    : 'html', // what type of data do we expect back from the server
                    })
                        // using the done promise callback
                        .done(function(data) {

                            // log data to the console so we can see
                            console.log(data); 
                            if(data != ""){
                            	id = parseInt(data);
                                console.log(id);
                                console.log(data.replace(id, ''));
                                jQuery('.lesson-single-question[index-id='+id+']').html(data.replace(id, ''));
                            }jQuery('.runingggg').removeClass('runingggg');
                            
                           

                            // here we will handle errors and validation messages
                        });
})
jQuery(document).on('touchend, mouseup', '.lesson-single-question', function(){
                    console.log('yessssssssssss');
                    if(document.getSelection().toString() != ""  ){
                       // console.log(document.getSelection());
                       text = escape(document.getSelection().toString().replace(/\*/g,'').replace(/\&/g,'%26'));
                       console.log(text);
                        length = document.getSelection().toString().length;
                        if(length < 4) return false;
                    main_html = jQuery(this).html();
                    var re = new RegExp((text),"g");
                    corped = escape(main_html).replace(re, '<span class="smallcaps">'+text+'</span><div class="selection_attr this_last"><a data-color="#E5F962"><span style="background-color:#E5F962"></span></a><a data-color="#72F721"><span style="background-color:#72F721"></span></a><a data-color="#6689FF"><span style="background-color:#6689FF"></span></a><a data-color="#D94BFF"><span style="background-color:#D94BFF"></span></a><a data-color="#FF81B1"><span style="background-color:#FF81B1"></span></a><a class="cros" data-color="remove">✕</a></div>');
                    console.log(corped);

                    jQuery(this).html(unescape(corped).replace(/%20/g, " ").replace(/%3A/g, ":").replaceAll('%u201D', "\”").replaceAll('%u201C', "\“").replace(/%2C/g, ",").replaceAll('%u2019', "’").replace(/%25/g, "%").replace(/%21/g, "!").replace(/%22/g, "\"").replace(/%23/g, "#").replace(/%24/g, "$").replace(/%26/g, "&").replace(/%27/g, "'").replace(/%28/g, "(").replace(/%29/g, ")").replace(/%2B/g, "+").replace(/%2D/g, "-").replace(/%2F/g, "/").replace(/%3F/g, "?"));
                    //do an updaaateeac
                    //setTimeout(function(){ 
                        //jQuery('.this_last').addClass('hide').removeClass('this_last');
                   // }, 3000);
                    upddaate_sselection(jQuery(this), selectify_vars.postID);
                    }
});
function replaceAll(string, search, replace) {
    return string.split(search).join(replace);
}
jQuery(document).on('mouseenter',  '.smallcaps,.selection_attr', function(){
    console.log('readddddddding');
    jQuery(this).next('.selection_attr').removeClass('hide');

});
jQuery('body').click(function(evt){    
    if(evt.target.class == "selection_attr")
       return;
    //For descendants of menu_content being clicked, remove this check if you do not want to put constraint on descendants.
    jQuery('.selection_attr').addClass('hide');         

   //Do processing of click event here for every element except with id menu_content

});
                jQuery(document).on('click',  '.selection_attr a', function(){
                    var color = jQuery(this).attr('data-color');
                    if(color =='remove'){
                        jQuery(this).parent().prev('span').contents().unwrap();
                        jQuery(this).parent().remove();
                        upddaate_sselection(jQuery(this).parent().parent('.lesson-single-question'), selectify_vars.postID);
                    }
                    jQuery(this).parent().prev('span').css('backgroundColor', color);
                    upddaate_sselection(jQuery(this).parent().parent('.lesson-single-question'), selectify_vars.postID);
                    
                })
                function getSelectionHtml() {
    var html = "";
    if (typeof window.getSelection != "undefined") {
        var sel = window.getSelection();
        if (sel.rangeCount) {
            var container = document.createElement("div");
            for (var i = 0, len = sel.rangeCount; i < len; ++i) {
                container.appendChild(sel.getRangeAt(i).cloneContents());
            }
            html = container.innerHTML;
        }
    } else if (typeof document.selection != "undefined") {
        if (document.selection.type == "Text") {
            html = document.selection.createRange().htmlText;
        }
    }
    return html;
}
function upddaate_sselection(element,posstid ){
    var formData = {
                                'index-it'              : element.attr('index-id'),
                                'contennt'             : element.html(),
                                'postid' : selectify_vars.postID
                            };
                    jQuery.ajax({
                        type        : 'POST', // define the type of HTTP verb we want to use (POST for our form)
                        url         : '/selectiion_helper.php', // the url where we want to POST
                        data        : formData, // our data object
                        dataType    : 'html', // what type of data do we expect back from the server
                    })
                        // using the done promise callback
                        .done(function(data) {

                            // log data to the console so we can see
                            console.log(data); 

                            // here we will handle errors and validation messages
                        });

}