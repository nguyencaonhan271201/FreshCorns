function previewFile(input,img){
    console.log('yes');
    var file = input.files[0];
    if(file){
        var reader = new FileReader();

        reader.onload = function(){
            img.attr("src", reader.result);
        }

        reader.readAsDataURL(file);
    }
}