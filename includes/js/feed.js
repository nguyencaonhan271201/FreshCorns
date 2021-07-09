let selectizeControl;
let checkSinglePostPage = window.location.pathname.indexOf('single_post') != -1;

$(document).ready(function(){
  selectizeControl = selectizeSelect($('.postMvVl'));
});

function selectizeSelect(object,defaultOption = null){
  
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

  return temp[0].selectize;
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

$("form.postForm").submit(e => {
  e.preventDefault();
  let parent = this;

  if (parent.$('.postMvVl').val()=='') this.$('.selectize-input').effect("shake");
  if (parent.$('.emojionearea-editor').html() == '') this.$('.emojionearea.postCap').effect("shake");
  if ((parent.$('.postMvVl').val() != '') && (parent.$('.emojionearea-editor').html() != ''))
  {
    let postData = new FormData();
    postData.append('postUser',my_id);
    postData.append('postCap',this.$('.postCap').val());
    postData.append('postMvId',JSON.parse(this.$('.postMvVl').val())['movie_id']);
    postData.append('postMvType',JSON.parse(this.$('.postMvVl').val())['movie_type']==true?1:0);
    postData.append('postFile',this.$('.postFile')[0].files[0]);
    postData.append('postMode',this.$('.postMode').val());
    
    $.ajax({
      url: 'includes/php/feed/ajax_post_add.php',
      type:'POST',
      data: postData,
      processData: false,
      contentType: false,
      success: function(data){
        //$('#php_return').html(data);
        if (data) {

          if (parent.$('.postMode').val()==1) {
            loadPosts();
            $('#myTab a#public-tab').tab('show');
          }
          else {
            loadPosts();            
            $('#myTab a#following-tab').tab('show');
          }

          $("form.postForm").trigger('reset');          
          parent.$('.postFile').val('');
          parent.$('.postFile_preview').attr('src','');
          parent.$('.delMediaBtn').remove();
          selectizeControl.clearOptions();
          parent.$('.emojionearea-editor').html('');
        }
      }
    });
  }
});

$('#myTab a').on('click', function (event) {
  event.preventDefault();
  $(this).tab('show');
})

function loadPosts(){
  $.ajax({
    url: 'includes/php/feed/ajax_post_get_public.php',
    success: function(data){
      //console.log(data)
      //$('#php_return').html(data);
      let results = JSON.parse(data);
      printPosts($('#public'),results);
    }
  });
  $.ajax({
    url: 'includes/php/feed/ajax_post_get_following.php',
    success: function(data){
      //console.log(data)
      //$('#php_return').html(data);
      let results = JSON.parse(data);
      printPosts($('#following'),results);
    }
  });
}

Date.prototype.addHours= function(h){
  this.setHours(this.getHours() + h);
  return this;
}

function printPosts(div,results){
    div.empty();
    results.forEach(result=>{
      //console.log(result);
      let mode_text = '';
        if (result['mode']==1)  mode_text='<i class="bi bi-globe"></i>';
        else if (result['mode']==2)  mode_text='<i class="bi bi-people"></i>';
        else if (result['mode']==3)  mode_text='<i class="bi bi-person"></i>';
        let html = "";
        if (result['share_from'] == null) {
          html = `
          <div class="feedCard container-fluid p-0" id="${result['ID']}">
            <div class="d-flex pr-3">
              <a href="profile.php?id=${result['user']}" class="cardUserImg flex-fill">
                  <img src="${result['profile_image']}">
              </a>

              <div class="cardInfos container-fluid p-0" id="${result['ID']}">
                  <h2>
                    <a href="profile.php?id=${result['user']}">${result['display_name']}</a> 
                    talking about 
                    <a class="movie_title" id='${
                      JSON.stringify({
                        movie_id:result['movie_id'],
                        movie_type:result['movie_type']
                      })}' href="movie.php?id=${result['movie_id']}&type=${result['movie_type']}"></a> 
                    • <a class="cardDate" data-tooltip="${new Date(result['date_created']).addHours(7).toLocaleString()}" data-tooltip-location="bottom" 
                      href="single_post.php?id=${result['ID']}">${getDuration(new Date(result['date_created']))}</a>
                    • <span class="cardMode" data-tooltip-location="bottom" data-tooltip="${result['mode'] == 1? "Public" : result['mode'] == 2? "Followers" : "Private"}" 
                      id="${result['mode']}">${mode_text}</span>
                  </h2>

                  <p class="limited_text cardCap">${result['content']}</p>   
                  
                  <div class="cardMedia">    
                    <img src="${(result['media'])?result['media']:''}" class="media">
                  </div>
              </div>
            </div>   
            
            <div class="cardChin container-fluid">
              <div class="row">
                <div class="col text-center">
                  <i class="cardReact far fa-thumbs-up" id="${result['ID']}"></i>
                </div>
                <div class="col text-center">
                  <i class="cardComment bi bi-chat-text" id="${result['ID']}"> <span style="font-style: normal !important;">${result['number_of_comments']}</span></i>
                </div>
            `;
            if (my_id == result['user']) html+=`
                <div class="col text-center">
                  <i class="cardEdit bi bi-pencil" id="${result['ID']}"></i>
                </div>
            `;
            html+=`            
                <div class="col text-center">              
                  <i class="cardShare bi bi-arrow-counterclockwise" aria-hidden="true" id="${result['ID']}"></i>
                </div>
              </div>
            </div>
          </div>
        `;
        } 
        else
        {
          let share_mode_text = '';
          if (result['original']['mode']==1)  share_mode_text='<i class="bi bi-globe"></i>';
          else if (result['original']['mode']==2)  share_mode_text='<i class="bi bi-people"></i>';
          else if (result['original']['mode']==3)  share_mode_text='<i class="bi bi-person"></i>';
          html = `
          <div class="feedCard container-fluid p-0" id="${result['ID']}">
            <div class="d-flex pr-3">
              <a href="profile.php?id=${result['user']}" class="cardUserImg">
                  <img src="${result['profile_image']}">
              </a>

              <div class="cardInfos container-fluid p-0" id="${result['ID']}">
                  <h2>
                    <a href="profile.php?id=${result['user']}">${result['display_name']}</a> 
                    • <a class="cardDate" data-tooltip="${new Date(result['date_created']).addHours(7).toLocaleString()}" data-tooltip-location="bottom"
                     href="single_post.php?id=${result['ID']}">${getDuration(new Date(result['date_created']))}</a>
                    • <span class="cardMode" data-tooltip-location="bottom" data-tooltip="${result['mode'] == 1? "Public" : result['mode'] == 2? "Followers" : "Private"}"
                      id="${result['mode']}">${mode_text}</span>
                  </h2>

                  <div class="share-content" data-id="${result['original']['ID']}">
                    <div class="feedCard container-fluid p-0">
                      <div class="d-flex pr-3">
                        <a href="profile.php?id=${result['original']['user']}" class="cardUserImg">
                            <img src="${result['original']['profile_image']}">
                        </a>

                        <div class="cardInfos container-fluid p-0">
                            <h2>
                              <a href="profile.php?id=${result['original']['user']}">${result['original']['display_name']}</a> 
                              talking about 
                              <a class="movie_title" id='${
                              JSON.stringify({
                                movie_id:result['original']['movie_id'],
                                movie_type:result['original']['movie_type']
                              })}' href="movie.php?id=${result['original']['movie_id']}&type=${result['original']['movie_type']}"></a> 
                              • <a class="cardDate" data-tooltip="${new Date(result['original']['date_created']).addHours(7).toLocaleString()}" data-tooltip-location="bottom"
                                href="single_post.php?id=${result['original']['ID']}">${getDuration(new Date(result['original']['date_created']))}</a>
                              • <span class="cardMode" data-tooltip-location="bottom" data-tooltip="${result['original']['mode'] == 1? "Public" : result['original']['mode'] == 2? "Followers" : "Private"}"
                                id="${result['original']['mode']}">${share_mode_text}</span>
                            </h2>

                            <p class="limited_text cardCap">${result['original']['content']}</p>   
                            
                            <div class="cardMedia">    
                              <img src="${(result['original']['media'])?result['original']['media']:''}" class="media">
                            </div>
                        </div>
                      </div>   
                    </div>
                  </div>
              </div>
            </div>   
            
            <div class="cardChin container-fluid">
              <div class="row">
                <div class="col text-center">
                  <div clas="d-flex">       
                    <i class="cardReact far fa-thumbs-up" id="${result['ID']}"></i>
                  </div>
                </div>
                <div class="col text-center">              
                  <i class="cardComment bi bi-chat-text" id="${result['ID']}"> <span style="font-style: normal !important;">${result['number_of_comments']}</span></i>
                </div>
            `;
            html+=`
              </div>
            </div>
          </div>
        `;
        }

        div.prepend(html);
    });  
    
    loadMovieTitles();
    loadNoReactions();

    document.querySelectorAll(".feedCard").forEach(box => {
      box.addEventListener("click", function(e) {
          let target = e.target;
          let postID = e.target.closest(".feedCard");
          if (postID != null) {
              postID = postID.id
          }
          if (target.nodeName == "IMG" && target.parentNode.classList.contains("cardMedia")) {
            e.preventDefault();
            let getURL = target.src;
            loadImage(getURL);
            showImageBox();
          } else {
            if (target.classList.contains("comment-like")) {
              e.preventDefault();
            } else if (target.classList.contains("comment-edit")) {
              e.preventDefault();
            } else if (target.classList.contains("comment-delete")) {
              e.preventDefault();
            } else if (target.classList.contains("see-more-reply") || target.parentNode.classList.contains("see-more-reply")) {
              e.preventDefault();
            } else if (!checkSinglePostPage && postID != "" && !target.classList.contains("cardComment")
            && !target.classList.contains("cardEdit") && !target.classList.contains("cardShare") && !target.classList.contains("cardReact")) {
              console.log(postID);
              window.location = `single_post.php?id=${postID}`;
            } else {
              e.preventDefault();
            }
          }
      })
    });

    div.find(".cardReact").click(function(){
      ReactPost($(this));
    });
    div.find(".cardEdit").click(function(){
      EditPost($(this));
    });  
    div.find(".cardShare").click(function(){
      document.querySelector("#shareConfirmationModal").querySelector("#sharePostID").innerHTML = $(this).attr('id'); 
      $("#shareConfirmationModal").modal("show");
    });
    div.find(".cardComment").click(function(){
      let id = $(this).attr('id');
      window.location.href = `single_post.php?id=${id}&comment_scroll`;
    });  
}

function ReactPost(object){
  $.ajax({
    url:`includes/php/feed/ajax_react_post_add.php?post_id=${object.attr('id')}`,
    success:function(data){
      if (data==1) loadNoReactions();
    }
  });
}

function EditPost(object){
  let id = object.attr('id');

  let parent = $(`.feedCard#${id}`);

  let p = parent.find('p.cardCap');

  let movie_title = parent.find('.movie_title');
  let backup_header_h2 = parent.find('.cardInfos h2').html();

  let mode = parent.find('.cardMode');
  let backup_mode = mode.attr('id');

  let mediaCard = parent.find('.cardMedia');
  let backup_media_src = mediaCard.find('img').attr('src');
  let media_changed = false;

  let chin = parent.find('.cardChin');

  p.replaceWith(`<textarea class="postCap" name="caption">${p.text()}</textarea>`);
  parent.find('textarea.postCap').emojioneArea({
    inline: false,
  }); 
  
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
  <div class="editMedia d-flex justify-content-center align-items-center">
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
    parent.find('.emojionearea.postCap').replaceWith(p);
    mediaCard.html(`<img src="${backup_media_src}" class="media">`);
    parent.find('.cardChin_edit').remove();
    chin.show();
  });
  parent.find('.editDelete').click(function(){
    $.ajax({
      url: `includes/php/feed/ajax_post_delete.php?postId=${id}`,
      success: function(data){
        //$('#php_return').html(data);
        loadPosts();
      }
    });
  });
  parent.find('.editSubmit').click(function(){

    if (parent.find('.postMvVl').val()=='') parent.find('.selectize-input').effect("shake");
    if (parent.find('.emojionearea-editor').html() == '') parent.find('.emojionearea.postCap').effect("shake");
    
    else {
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
        url: 'includes/php/feed/ajax_post_edit.php',
        type:'POST',
        data: postData,
        processData: false,
        contentType: false,
        success: function(data){
          //$('#php_return').html(data);
          window.location.reload();
        }
      });
    };
  });  
}

function showImageBox() {
  $(".image-box").css("display", "flex");
  setTimeout(function() {
      $(".image-box").css("opacity", 1);
  }, 10);
}

function hideImageBox() {
  $(".image-box").css("opacity", 0);
  setTimeout(function() {
      $(".image-box").css("display", "none");
  }, 300);
}

function loadImage(src) {
  $(".image-box img").attr("src", src);
}

function getDuration(epochTime) {
  let timeDifference = Date.now() - epochTime - 3600000 * 7;
  let aWeek = 86400000 * 7;
  let getValue;
  let getUnit;
  if (timeDifference < 3600000)
  {
      getValue = Math.floor(timeDifference / 60000) <= 0? 1 : Math.floor(timeDifference / 60000);
      getUnit = getValue == 1? "minute" : "minutes";
  }
  else if (timeDifference < 86400000)
  {
      getValue = Math.floor(timeDifference / 3600000);
      getUnit = getValue == 1? "hour" : "hours";
  }
  else if (timeDifference < aWeek)
  {
      getValue = Math.floor(timeDifference / 86400000);
      getUnit = getValue == 1? "day" : "days";
  }
  else
  {
      getValue = Math.floor(timeDifference / aWeek);
      getUnit = getValue == 1? "week" : "weeks";
  }
  return `${getValue} ${getUnit}`;
}