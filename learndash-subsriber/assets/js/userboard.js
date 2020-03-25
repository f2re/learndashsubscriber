jQuery(document).ready(function($) {
  // save users points in global variable
  let users_points = [];
  let post_points  = [];
  let user_name    = 'Anonim';
  let audio_files  = [];
  let mytimer      = false;
  let all_scores   = 0;
  let audio        = false;
  init();

  var observer = new MutationObserver(function(mutations) {
    mutations.forEach(function(mutation) {
      if (mutation.attributeName === "class") {
        var attributeValue = $(mutation.target).prop(mutation.attributeName);

        if ($(mutation.target).hasClass("menu-item-selected") || $(mutation.target).hasClass("cs-selected")) {
          //
          // call function to show score
          //
          // get_table_data();

          //
          // get data from body attr
          //
          let _body        = $(mutation.target).parents("body");
          let correct      = 0,
              incorrect    = 0,
              points       = 0,
              right_points = 0;

          if (_body.attr("correct") !== undefined) {
            correct = parseInt(_body.attr("correct"));
          }
          if (_body.attr("incorrect") !== undefined) {
            incorrect = parseInt(_body.attr("incorrect"));
          }
          if (_body.attr("points") !== undefined) {
            points = parseInt(_body.attr("points"));
          }
          // now we must to calculate all points 
          post_points;
          allanswers = _body.attr("allanswers").split(',');
          console.log(allanswers);
          for (let k = 0; k < allanswers.length; k++) {
            if ( allanswers[k]==1 && typeof(post_points[k+1])!='undefined' ){
              console.log(post_points[k+1]);
              right_points += parseInt(post_points[k+1]);
            }
          }
          // save scores to global var
          // console.log("all_scores ",all_scores);
          // console.log("right_points ",right_points);
          all_scores += parseInt(right_points);
          // dirty_data(correct, incorrect, points);
          // console.log("Class attribute changed to:", attributeValue);
          // show_leaders_points(points,correct, incorrect);
          show_leaders_points(all_scores,correct, incorrect);
          // clear all points 
          clear_points();
        }
      }
    });
  });

  /**
   * clear all points in iframe
   */
  function clear_points(){
    let _body = $("iframe")
        .contents()
        .find('body');
    _body.attr("correct",0);
    _body.attr("incorrect",0);
    _body.attr("allanswers",'');
    _body.attr("points",0);
  }

  /**
   * initialisation function
   * @return {[type]} [description]
   */
  function init() {
    // wait for iframes loaded
    //iframes_loaded();

    // set time interval to check if iframe loaded
    mytimer = setInterval( function(){
      // console.log('timer..');
      let item = $("iframe")
        .contents()
        .find("#panel-outline li .cs-listitem, #outline-panel li .cs-listitem")
        .last();
      if ( item.length>0 ){
        clearInterval(mytimer);
        observer.observe(item[0], {
          attributes: true
        });
        $("iframe").off("hover");
      }
    },2000 );

    //
    // hide popup event
    //
    
    let popup = $("#LDS-popup");
    popup
      .find(".leaderboard__exit")
      .off("click")
      .on("click", function(ev) {
        popup.removeClass("active");
      });

    //
    // we must get user data after user finish course!
    //

    // get user data
    // get_table_data();
    get_course_list();
  }

  /**
   * when iframe loaded
   * @return {[type]} [description]
   */
  function iframes_loaded(force=false) {
    let iframes = $("iframe");

    iframes.hover(function() {
      let item = $(this)
        .contents()
        .find("#panel-outline li a.cs-listitem")
        .last();
      // console.log( item );
      if (item.length > 0) {
        observer.observe(item[0], {
          attributes: true
        });
        $(this).off("hover");
      }
    });
    return;
  }



  /**
   * get dirty data from body attrs in iframe
   * @return {[type]} [description]
   */
  function dirty_data(correct, incorrect, points) {
    let popup = $("#LDS-popup");
    popup.addClass("active");

    // popup.find(".content .correct").text(correct);
    // popup.find(".content .all").text(correct + incorrect);
    // popup.find(".content .points").text(points);

    return;
  }

  /**
   * draw ordering points leaderboard
   */
  function  show_leaders_points(points, correct, incorrect){
    users_points['you'] = { point: points, name:'You' };
    var sortable = [];
    for (var item in users_points) {
      let _v = {};
      _v[item] = users_points[item];
      sortable.push( _v );
    }
    // define sort function
    sortable.sort(function(a, b) {
      let _a = Object.keys(a)[0];
      let _b = Object.keys(b)[0];
      return a[_a].point - b[_b].point;
    });
    console.log(sortable);
    
    // fill div with order
    // let _div = $('<div class="leaderboard"></div>');   
    // $.each( sortable, function(i,obj){
    //   _div.prepend( $('<div >'+obj[0]+' / '+obj[1]+'</div>') );
    // } );

    // insert in popup
    let popup = $("#LDS-popup");
    popup.addClass("active");

    // iterate over each player
    let len = popup.find('.leaderboard__player').length;
    popup.find('.leaderboard__player').each(function(i,obj){
      let j = len - i-1;
      if ( typeof sortable[j] !== undefined ){
        let _k = Object.keys(sortable[j])[0];
        // set name
        $(obj).find('.player__thumbnail').addClass( _k );
        // set score
        $(obj).find('.player__names').text(sortable[j][_k].name);
        // set class
        $(obj).find('.player__points').text(sortable[j][_k].point);
      }
    });

    // calculate percentage 
    let audio_ind = 0;
    if ((correct+incorrect)!=0){
      audio_ind = parseInt(correct*10/(correct+incorrect));
      // console.log("points:",audio_ind);
    }

    let keys = Object.keys(audio_files);
    
    if ( audio_ind<keys[0] ){
      audio_ind=keys[0];
    }
    if ( audio_ind>keys[keys.length-1] ){
      audio_ind=keys[keys.length-1];
    }

    // play sound if exist file
    if ( typeof audio_files[audio_ind]!==undefined ){
      if ( audio===false ){
        audio = new Audio(audio_files[audio_ind]);
        // console.log( audio );
        if (audio.duration > 0 && !audio.paused) {
          console.log('already playing!');
        }else{
          audio.play();
        }
      }
    }

    // popup.find(".content").after(_div);
  }

  /**
   * request table data from API
   * @return {[type]} [description]
   */
  function get_course_list() {
    // require._defined.
    // window._gsDefine
    // require( ["models/presentation/interactions/Interaction"], function(p){
    //   console.log(p);
    // });

    $.ajax({
      url: "/wp-json/ldsubscriber/v1/get_course_list",
      method: "POST",
      data: { postid: $("#leadersection-subscriber").attr("postid"),
              userid: $("#leadersection-subscriber").attr("userid"),
            }
    }).done(function(resp) {
      // console.log(resp);
      let _div = $(resp.all_course_content);
      // save users points
      users_points = resp.users;
      // save username
      user_name = resp.you;
      // get audio
      audio_files = resp.audio;
      // save post scores
      post_points = resp.scores;
      // get all scores
      all_scores = parseInt(resp.course_score);
      
      // let url = _div.find('a').attr('href');
      // console.log(url);
      // let iframe = $('<iframe src="'+url+'" style="width:100%; height:100%;"></iframe>');
      let popup = $("#LDS-popup");
      // popup.addClass('active');

      // classes for modules
      let classes = ['leaderboard__city--yellow',
                     'leaderboard__city--pink',
                     'leaderboard__city--blue',
                     'leaderboard__city--green',
                    ];
      // remove unuser elements from div
      _div.find('#learndash_course_content_title, #lesson_heading').remove();
      // add class from list to cities
      


      popup.find(".leaderboard__cities").html(_div);
      // if next-link empty - show all courses
      if (resp.next_post == "") {
        popup.find('.leaderboard__next').addClass('d-none');
        popup.find('.leaderboard__replay').removeClass('d-none');

        let first_course = '';
        // add classes to links
        _div.find('#lessons_list > div').each(function(i,obj){
          let a = $(obj).find('a');
          let j = i % classes.length ;

          // grab first ourse url to redirect
          if ( first_course=='' ){
            first_course=a.attr('href');
          }

          if ( !a.hasClass('notcompleted') ) {
            a.addClass('checkmark');
          }
          a.addClass(classes[j]);
        });

        // assign click event to reset course button
        popup.find('.leaderboard__replay')
             .off('click')
             .on('click',function(ev){
              $.ajax({
                url: "/wp-json/ldsubscriber/v1/reset_course",
                method: "POST",
                data: { postid: $("#leadersection-subscriber").attr("postid"),
                        userid: $("#leadersection-subscriber").attr("userid"),
                      }
              }).done(function(resp) {
               
                if (first_course!=''){
                  window.location = first_course;
                }

              });
             });

      } else {

        // remove a links
        _div.find('#lessons_list > div').each(function(i,obj){
          let a = $(obj).find('a');
          let _h = a.parent();
          let j = i % classes.length ;
          _h.html( a.text() );
          if ( !a.hasClass('notcompleted') ) {
            _h.addClass('checkmark');
          }
          _h.addClass(classes[j]);
        });

        let complete = $('#learndash_mark_complete_button');

        if ( resp.course_completed && resp.next_post!=resp.first_uncompleted_link && resp.first_uncompleted_link!=false ){
          popup.find('.leaderboard__next').addClass('d-none');
          popup.find('.leaderboard__continue').removeClass('d-none');
          popup.find('.leaderboard__continue')
              .off('click')
              .on('click',function(ev){
                // console.log(resp.first_uncompleted_link);
                window.location.href = resp.first_uncompleted_link;
                return;
              });
        }else{
          popup.find('.leaderboard__next')
              .off('click')
              .on('click',function(ev){
                // complete.click();
                $.ajax({
                  url: "/wp-json/ldsubscriber/v1/complete_lesson",
                  method: "POST",
                  data: { postid: $("#leadersection-subscriber").attr("postid"),
                          userid: $("#leadersection-subscriber").attr("userid"),
                          scores: all_scores,
                        }
                }).done(function(_resp) {
                  window.location.href = resp.next_post;                 
                });
                // console.log(resp.next_post);
                return;
              });
        }
        // let lnk = $('<a href="' + resp.next_post + '">Next module </a>');
        // popup.find(".content-after ").html(lnk);
      }

      // check if lesson completed, then popup window!
      if ( resp.course_completed ){
        show_leaders_points(all_scores,0,0);
      }
    });
  }


});

