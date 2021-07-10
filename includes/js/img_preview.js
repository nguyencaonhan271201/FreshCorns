function previewFile(input,img){
    var file = input.files[0];
    if(file){
        var reader = new FileReader();

        reader.onload = function(){
            img.attr("src", reader.result);
        }

        reader.readAsDataURL(file);
    }
}