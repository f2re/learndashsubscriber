jQuery(document).ready(function($) {
  // save users points in global variable
  let users_points = [];
  let user_name    = 'Anonim';
  let audio_files  = [];
  
  init();

  var observer = new MutationObserver(function(mutations) {
    mutations.forEach(function(mutation) {
      if (mutation.attributeName === "class") {
        var attributeValue = $(mutation.target).prop(mutation.attributeName);

        if ($(mutation.target).hasClass("menu-item-selected")) {
          //
          // call function to show score
          //
          // get_table_data();

          //
          // get data from body attr
          //
          let _body = $(mutation.target).parents("body");
          let correct = 0,
            incorrect = 0,
            points = 0;

          if (_body.attr("correct") !== undefined) {
            correct = parseInt(_body.attr("correct"));
          }
          if (_body.attr("incorrect") !== undefined) {
            incorrect = parseInt(_body.attr("incorrect"));
          }
          if (_body.attr("points") !== undefined) {
            points = parseInt(_body.attr("points"));
          }

          // dirty_data(correct, incorrect, points);
          // console.log("Class attribute changed to:", attributeValue);
          show_leaders_points(points,correct, incorrect)
        }
      }
    });
  });

  /**
   * initialisation function
   * @return {[type]} [description]
   */
  function init() {
    // wait for iframes loaded
    iframes_loaded();

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
  function iframes_loaded() {
    let iframes = $("iframe");

    $("iframe").hover(function() {
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
    users_points['you'] = points;
    var sortable = [];
    for (var item in users_points) {
        sortable.push([item, users_points[item]]);
    }
    // define sort function
    sortable.sort(function(a, b) {
        return a[1] - b[1];
    });

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
        // set name
        $(obj).find('.player__thumbnail').addClass(sortable[j][0]);
        // set score
        $(obj).find('.player__names').text(sortable[j][0]);
        // set class
        $(obj).find('.player__points').text(sortable[j][1]);
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
      var audio = new Audio(audio_files[audio_ind]);
      audio.play();
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


      popup.find(".leaderboard__cities").html(_div);
      // if next-link empty - show all courses
      if (resp.next_post == "") {
        popup.find('.leaderboard__next').addClass('d-none');
      } else {
        popup.find('.leaderboard__next')
             .off('click')
             .on('click',function(ev){
              window.location.href=resp.next_post;
              return;
             });
        // let lnk = $('<a href="' + resp.next_post + '">Next module </a>');
        // popup.find(".content-after ").html(lnk);
      }
    });
  }

  /**
   * request table data from API
   * @return {[type]} [description]
   */
  function get_table_data() {
    // require._defined.
    // window._gsDefine
    // require( ["models/presentation/interactions/Interaction"], function(p){
    //   console.log(p);
    // });

    $.ajax({
      url: "/wp-json/ldsubscriber/v1/get_course_data",
      method: "POST",
      data: {}
    }).done(function(resp) {
      // console.log(resp);
      //
      // calc percentage of user summary course
      //
      let percentage = 0,
        courses = 0;

      //
      // iterate over courses
      //
      $.each(resp.data.data, function(i, ind) {
        // console.log(i,ind);

        percentage += ind.progress;
        courses += 1;
      });

      $("#leadersection-subscriber form").text(
        percentage / courses + " percentage courses complete "
      );
    });
  }

  function play_audio() {

  }
  
});
