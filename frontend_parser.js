let text = document.getElementById("newtext").value;
let lang_id = Number(document.getElementById('langselect').value);
let text_words = new Array();
let word_eng_words = new Array();
let space_index = new Array();
let tokens = text.split(' ');


let text_word = "";
let word_eng_word = "";
let is_a_word = true;
let shit = 0;
let unshit = 0;


for(let token of tokens) {
    shit = 0;
    unshit = 0;
   
    while(unshit != -1 && shit != -1) {
        shit = token.search(/\P{L}/u);
        unshit = token.search(/\p{L}/u);
        if(shit == 0) {
            is_a_word = false;
            if(unshit == -1) {
                text_word = token;
                
            }
            else {
                text_word = token.slice(0,unshit);
                token = token.slice(unshit);
            }
        }
        else if(shit == -1) {
            is_a_word = true;
            text_word = token;
            
        }
        else {
            is_a_word = true;
            text_word = token.slice(0, shit);
            token = token.slice(shit);
        }

        text_words.push(text_word);        
        if(is_a_word) {
            if(lang_id == 7) word_eng_word = text_word.toLocaleLowerCase('tr');
            else word_eng_word = text_word.toLowerCase();
            word_eng_words.push(word_eng_word);
        }
        
    }


    space_index.push(text_words.length - 1);
}
