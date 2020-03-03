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

  
});

