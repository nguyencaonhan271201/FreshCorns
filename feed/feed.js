$(document).ready(function(){
    loadPosts_Public();
    selectizeSelect($('.postMvVl'));
});

function selectizeSelect(object,defaultOption = null){
  
  /*if (defaultOption){
    console.log(object);
    console.log(defaultOption);
    let temp = object.selectize({      
      valueField: "value",
      labelField: "title",
      searchField: "title",
    });
    temp[0].selectize.addOption(defaultOption);
    temp[0].selectize.addItem(1,false);
  }*/
  
  let temp = object.selectize({
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
          //console.log(item);
          let return_html = `<div>
          <h1>${escape(item['title'])}</h1>`;
          if (item['release_date']) return_html+=`
          <br>
          <h3>${escape(new Date(item['release_date']).getFullYear())}</h3>`;
          return_html += `</div>`;
          return (return_html);
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

  if (defaultOption){
    temp[0].selectize.addOption(defaultOption);
    temp[0].selectize.addItem(defaultOption['value'],false);
  };

}

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
        'movie_id':result['id'],
        'movie_type':result['media_type']=='movie'
      });

      if (result['release_date']!='' && result['title']!='')cleanedResults.push(result);
    }
  });
  return cleanedResults;
}

$('.postFile_preview').on('load',function(){
  let parent=$(this).parent();
  parent.prepend(`<button type="button" class='delMediaBtn'>X</button>`);
  parent.find(".delMediaBtn").click(function(){
    parent.parent().find('.postFile').val('');
    parent.find('.postFile_preview').attr('src','');
    $(this).remove();
  });
});

$("form").submit(e => {
  e.preventDefault();
  let postData = new FormData();
  postData.append('postUser',my_id);
  postData.append('postCap',this.$('.postCap').val());
  postData.append('postMvId',JSON.parse(this.$('.postMvVl').val())['movie_id']);
  postData.append('postMvType',JSON.parse(this.$('.postMvVl').val())['movie_type']==true?1:0);
  postData.append('postFile',this.$('.postFile')[0].files[0]);
  postData.append('postMode',this.$('.postMode').val());

  console.log(postData.get('postFile')); 
  
  $.ajax({
    url: 'ajax_post_add.php',
    type:'POST',
    data: postData,
    processData: false,
    contentType: false,
    success: function(data){
      //$('#php_return').html(data);
      loadPosts_Public();
    }
  });

});


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
      let mode_text = '';
        if (result['mode']==1)  mode_text='<i class="bi bi-globe"></i>';
        else if (result['mode']==2)  mode_text='<i class="bi bi-people"></i>';
        else if (result['mode']==3)  mode_text='<i class="bi bi-person"></i>';

        let html = `
        <div class="feedCard container-fluid p-0" id="${result['ID']}">
          <div class="d-flex pr-3">
            <div class="cardUserImg">
                <img src="${result['profile_image']}">
            </div>

            <div class="cardInfos container-fluid p-0" id="${result['ID']}">
                <h2>
                  <a href="">${result['display_name']}</a> 
                  is talking about 
                  <a class="movie_title" id='${
                    JSON.stringify({
                      movie_id:result['movie_id'],
                      movie_type:result['movie_type']
                    })}' href=""></a> 
                  • <a class="cardDate" href="single_post.php?id=${result['ID']}">${result['date_created']}</a>
                  • <span class="cardMode" id="${result['mode']}">${mode_text}</span>
                </h2>

                <p class="limited_text">${result['content']}</p>   
                
                <div class="cardMedia">    
                  <img src="${(result['media'])?result['media']:''}" class="media">
                </div>
            </div>
          </div>   
          
          <div class="cardChin container-fluid">
            <div class="row">
              <div class="col text-center">
                <div clas="d-flex">       
                  <i class="cardReact bi bi-heart" id="${result['ID']}"> 100</i>
                </div>
              </div>
              <div class="col text-center">              
                comment
              </div>
          `;
          if (my_id==result['user']) html+=`
              <div class="col text-center">
                <i class="cardEdit bi bi-pencil" id="${result['ID']}"></i>
              </div>
          `;
          html+=`
            </div>
          </div>
        </div>
        `;
        $("#mainFeed").prepend(html);
    });  
    
    loadMovieTitles();
    loadNoReactions();  
    $(".cardReact").click(function(){
      ReactPost($(this));
    });
    $(".cardEdit").click(function(){
      EditPost($(this));
    });  
};

function ReactPost(object){
  $.ajax({
    url:`ajax_react_post_add.php?post_id=${object.attr('id')}`,
    success:function(data){
      if (data==1) loadNoReactions();
    }
  });
}

function EditPost(object){
  let id = object.attr('id');

  let parent = $(`.feedCard#${id}`);

  let p = parent.find('p');

  let movie_title = parent.find('.movie_title');
  let backup_header_h2 = parent.find('.cardInfos h2').html();

  let mode = parent.find('.cardMode');
  let backup_mode = mode.attr('id');

  let mediaCard = parent.find('.cardMedia');
  let backup_media_src = mediaCard.find('img').attr('src');
  let media_changed = false;

  let chin = parent.find('.cardChin');

  p.replaceWith(`<textarea class="postCap" name="caption">${p.text()}</textarea>`);
  
  movie_title.replaceWith(`<select class="postMvVl"></select>`);
  selectizeSelect(parent.find("select"),{
    'title': movie_title.text(),
    'value': movie_title.attr('id')
  });

  mode.replaceWith(`
  <select class="postMode">        
    <option value="1">Public</option>
    <option value="2">Followers</option>
    <option value="3">Private</option>
  </select>
  `);
  parent.find(`.postMode option[value="${backup_mode}"]`).attr("selected",true);

  if (backup_media_src) mediaCard.prepend(`<button type="button" class='delMediaBtn'>X</button>`);

  parent.find('.delMediaBtn').click(function(){
    mediaCard.find('img').attr('src',null);
    $(this).hide();
  });

  mediaCard.prepend(`
  <div class="editMedia">
    <label>
      <i class="bi bi-image"></i>
        <input class="postFile" type="file" name="create_user_img" accept="image/png, image/jpeg">
    </label>
  </div>`);

  parent.find('.postFile').change(function(){
    previewFile(this,mediaCard.find('img'));

    if (!media_changed){          
      media_changed = true;
      parent.find('.delMediaBtn').show();

      parent.find('.editMedia').append(`<i class="bi bi-image-fill delNewMedia"></i>`);

      parent.find('.delNewMedia').click(function(){
        mediaCard.find('img').attr('src',backup_media_src);
        parent.find('.postFile').val('');
        this.remove();
        media_changed=false;
      });
    };
  });

  chin.hide();
  parent.append(`
  <div class="cardChin cardChin_edit container-fluid">
    <div class="row">
      <div class="col text-center">
        <i class="bi bi-check editSubmit"></i>
      </div>
      <div class="col text-center">              
      <i class="bi bi-trash editDelete"></i>
      </div>
      <div class="col text-center">              
        <i class="bi bi-x editCancel"></i>
      </div>
    </div>
  </div>
  `);
  parent.find('.editCancel').click(function(){
    parent.find('.cardInfos h2').html(backup_header_h2);
    parent.find('.postCap').replaceWith(p);
    mediaCard.html(`<img src="${backup_media_src}" class="media">`);
    parent.find('.cardChin_edit').remove();
    chin.show();
  });
  parent.find('.editDelete').click(function(){
    $.ajax({
      url: `ajax_post_delete.php?postId=${id}`,
      success: function(data){
        //$('#php_return').html(data);
        loadPosts_Public();
      }
    });
  });
  parent.find('.editSubmit').click(function(){
    let postData = new FormData();
    postData.append('postUser',my_id);
    postData.append('postId',id);

    postData.append('postCap',parent.find('.postCap').val());

    postData.append('postMvId',JSON.parse(parent.find('.postMvVl').val())['movie_id']);
    postData.append('postMvType',JSON.parse(parent.find('.postMvVl').val())['movie_type']==true?1:0);

    if (parent.find('.postFile').val()) postData.append('postFile',parent.find('.postFile')[0].files[0]);
    else if (mediaCard.find('img').attr('src')) postData.append('postFile',mediaCard.find('img').attr('src'));
    else postData.append('postFile','undefined');

    postData.append('postMode',parent.find('.postMode').val());

    $.ajax({
      url: 'ajax_post_edit.php',
      type:'POST',
      data: postData,
      processData: false,
      contentType: false,
      success: function(data){
        //$('#php_return').html(data);
        //loadPosts_Public();
        window.location.reload();
      }
    });

  });
}
