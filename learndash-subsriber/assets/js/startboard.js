jQuery(document).ready(function($) {

  init();


  var observer = new MutationObserver(function(mutations) {
    mutations.forEach(function(mutation) {
      if (mutation.attributeName === "class") {
        var attributeValue = $(mutation.target).prop(mutation.attributeName);

        if ($(mutation.target).hasClass("menu-item-selected")) {
          //
          // call function to show score

          show_popup();
        }
      }
    });
  });

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

    
    // set time interval to check if iframe loaded
    mytimer = setInterval( function(){
      // console.log('timer..');
      let item = $("iframe")
        .contents()
        .find("#panel-outline li a.cs-listitem")
        .last();
      if ( item.length>0 ){
        clearInterval(mytimer);
        observer.observe(item[0], {
          attributes: true
        });
        $("iframe").off("hover");
      }
    },2000 );

    get_course_list();
    // show_popup();
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

