/**
 * Created by zhangshiqiu on 2017/4/17.
 */


function autocomplet() {
    var min_length = 0;
    var reg = '[^A-Za-z]';
    keyword = $('#text_query').val();
    keyword = keyword.toLowerCase();
    if(keyword.length == 0){
        $('#downlist').hide();
    }
    //console.log(typeof keyword);
    var keywords = keyword.split(" ");
    var remain = "";
    if (keywords.length > min_length) {
        if(keywords.length == 1){
            console.log("length is 1");
            var query_term = keywords[0];
        }
        else{
            console.log("length is larger than 1");
            var query_term = keywords[keywords.length - 1];
            
            for(var i = 0; i < keywords.length - 1; i++)
                remain = keywords[i] + " ";
        }
        $.ajax({
            url: 'update.php/?',
            type: 'GET',
            data: {text_query:query_term},
            success:function(results){
                console.log(results);
                data = JSON.parse(results);
                list = data.suggest.suggest[query_term].suggestions;
                list = list.sort(sortWords);

                autocompletionlist = new Array();
                for (var i = 0; i < list.length; i++) {
                    var word = list[i].term;
                    if (autocompletionlist.length >= 5){
                        break;
                    }
                    if (word.search(reg) != -1 || word.length > 25){
                        continue;
                    }


                    var str = "<li onclick='set_item(\"" + remain + word +"\")'>"+ remain + word + "</li>";
                    autocompletionlist.push(str);
                }

                if (autocompletionlist.length !== 0){
                    $('#downlist').show();
                    $('#downlist').html(autocompletionlist);
                }
            }
        });
    } else {
        $('#downlist').hide();
    }
}

function sortWords(a, b) {
    return b.weight - a.weight;
}

function set_item(item) {
    $('#text_query').val(item);
    $('#downlist').hide();
}
