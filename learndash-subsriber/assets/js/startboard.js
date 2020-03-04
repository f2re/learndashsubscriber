jQuery(document).ready(function($) {

  init();

  /**
   * initialisation function
   * @return {[type]} [description]
   */
  function init() {
    
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

    get_course_list();
    show_popup();
  }


  /**
   * show popup
   */
  function  show_popup(){
    // insert in popup
    let popup = $("#LDS-popup");
    popup.addClass("active");
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
      data: { postid: $("#leadersection-subscriber-startboard").attr("postid"),
              userid: $("#leadersection-subscriber-startboard").attr("userid"),
            }
    }).done(function(resp) {
      // console.log(resp);
      let _div = $(resp.all_course_content);
      // save username
      user_name = resp.you;
      // get audio
      audio_files = resp.audio;
      
      let popup = $("#LDS-popup");
      
        popup.find('.leaderboard__next')
            .off('click')
            .on('click',function(ev){
                window.location.href = resp.next_post;
                return;
            });        
    });
  }


});

