$(document).ready(function(){
    loadPosts_Public();
});

$("select").selectize({
    valueField: "value",
    labelField: "title",
    searchField: "title",
    sortField:[
      {
        field: "release_date",
        direction: 'desc'
      },
      {
          field: '$score'
      }
    ],
    create: false,
    hideSelected:true,
    render: {
      option: function (item, escape) {
        console.log(item);
        
        return (
          `<div>
          <h1>${escape(item['title'])}</h1>
          <br>
          <h3>${escape(new Date(item['release_date']).getFullYear())}</h3>
          </div>
        `);
      },
    },
    load: function (query, callback) {
        if (!query.length) return callback();
        $("select")[0].selectize.clearOptions();

        theMovieDb.search.getMulti({"query":query},
        data=>{
          callback(cleanResult(JSON.parse(data)['results']));
        }, data => {
          callback();
        });
    },
});

function cleanResult(results){
  let cleanedResults=[];
  results.forEach(result => {
    if (result['media_type'] == 'tv' || result['media_type'] == 'movie'){
      if (!('title' in result) && ('name' in result))  {
        result['title'] = result['name'];  
        delete result['name'];
      }
      if (!('release_date' in result) && ('first_air_date' in result))  {
        result['release_date'] = result['first_air_date'];
        delete result['first_air_date'];
      }

      result['value'] = JSON.stringify({
        'id':result['id'],
        'movie_type':result['media_type']=='movie'
      });

      if (result['release_date']!='' && result['title']!='')cleanedResults.push(result);
    }
  });
  return cleanedResults;
}

$("form").submit(e => {
  e.preventDefault();
  let postData = new FormData();
  postData.append('postUser',my_id);
  postData.append('postCap',this.$('#postCap').val());
  postData.append('postMvId',JSON.parse(this.$('#postMvVl').val())['id']);
  postData.append('postMvType',JSON.parse(this.$('#postMvVl').val())['movie_type']==true?1:0);
  postData.append('postFile',this.$('#postFile')[0].files[0]);
  postData.append('postMode',1);
  
  if (checkPostData(postData)) {
    $.ajax({
      url: 'ajax_post_add.php',
      type:'POST',
      data: postData,
      processData: false,
      contentType: false,
      success: function(data){
        $('#php_return').html(data);
        loadPosts_Public();
      }
    })
  };

});

function checkPostData(postData){
  if (!postData.get('postCap') || !postData.get('postMvId')) {
    return false;
  }
  if (postData.get('postFile')!='undefined'){
    let validImageTypes = ["image/jpeg","image/jpg","image/png"];
    if ($.inArray(postData.get('postFile')['type'], validImageTypes) < 0) {            
      return false;
    }
  }
  return true;
}


function loadPosts_Public(){
    $.ajax({
      url: 'ajax_post_get_public.php',
      success: function(data){
        let results = JSON.parse(data);
        printPosts_Public(results);
      }
    })
  }

function printPosts_Public(results){
    $("#mainFeed").empty();
    results.forEach(result=>{
      //console.log(result);
        let html = `
        <div class="feedCard container-fluid p-0">
          <div class="d-flex pr-3">
            <div class="cardUserImg">
                <img src="${result['profile_image']}">
            </div>

            <div class="cardInfos container-fluid p-0" id="${result['ID']}">
                <div>
                    <h2><a href="">${result['display_name']}</a> is talking about <a class="movie_title" id='${
                        JSON.stringify({
                          movie_id:result['movie_id'],
                          movie_type:result['movie_type']
                        })}' href=""></a> • ${result['date_created']}</h2>
                </div>

                <p>${result['content'].substring(0,50)+'...'}</p>      
                    
                <img src="${(result['media'])?result['media']:''}">
            </div>
          </div>   
          
          <div class="cardChin container-fluid">
            <div class="row">
              <div class="col-3 text-center">
                <div clas="d-flex">       
                  <i class="cardReact bi bi-heart" id="${result['ID']}"> 100</i>
                </div>
              </div>
              <div class="col-3 text-center">              
                comment
              </div>
              <div class="col-3 text-center">
                edit
              </div>
              <div class="col-3 text-center">              
                ↺
              </div>
            </div>
          </div>
        </div>
        `;
        $("#mainFeed").prepend(html);
    });  
    
    loadMovieTitles();
    loadNoReactions();
    $(".cardInfos").click(function(){
      window.location = `single_post.php?id=${$(this).attr('id')}`;
    });
    
};
