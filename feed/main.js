let my_id = '';
let my_name = '';
let my_image = '';

$(document).ready(function(){
  updateSessionInfo();
});

function updateSessionInfo(callback){
  $.ajax({
    url: 'ajax_session_get.php',
    success: function(data){      
      let result = JSON.parse(data);            
      my_id = result['user_id'];
      my_name = result['user_name'];
      my_image = result['user_image'];
    }
  })
}

$(document).ready(function(){
  loadMovieTitles();
  loadNoReactions();
})

function loadMovieTitles(){
  $("a.movie_title").each(function(i,obj){
    let result = JSON.parse($(this).attr('id'));
    //console.log(result);
    if (result['movie_type']) theMovieDb.movies.getById({"id":result['movie_id']}, data => {
      $(this).html(JSON.parse(data)['title']);
    }, data =>{
      
    });
    else theMovieDb.tv.getById({"id":result['movie_id']},  data => {
        $(this).html(JSON.parse(data)['name']);
    }, data =>{
      
    });
    
  });
}

function loadNoReactions(){
  $(".cardReact").each(function(i,obj){
    let temp = $(this);
    $.ajax({
      url:`ajax_react_get.php?post_id=${temp.attr('id')}`,
      success:function(data){
        temp.html(data);
      }
    });
    $.ajax({
      url:`ajax_reacted_check.php?post_id=${temp.attr('id')}`,
      success:function(data){
        if(data) temp.toggleClass("bi-heart");
        if(data) temp.toggleClass("bi-heart-fill");
      }
    });
  });
}