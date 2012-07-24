$(function() {
	/**
	 * Changes all tags after schedule drop or undrop
	 * Used in AJAX callback function
	 *
	 * @param int id
	 * @param string classToAdd
	 * @param string classToRemove
	 * @param string newStatus
	 */
    function swapDropUndropTags(id, classToAdd, classToRemove, newStatus) {
        all = $("a[schedule_id=" + id + "]").children("strong");
        all.removeClass(classToRemove);
        all.addClass(classToAdd);
        jQuery.each(all, function() {
          $(this).children("span").
          replaceWith("<span>" + newStatus + "</span>");
        });
    }


	/**
	 * Makes AJAX request to drop or undrop a schedule or an event
	 *
	 * @param int id
	 * @param boolean isDropAction
	 * @param boolean isSchedule
	 */
    function makeAjaxDropUndropRequest(id, isDropAction, isSchedule) {
    	args = getDropUndropArguments(isDropAction);
		var path = getDropUndropPath(isDropAction, isSchedule);
        $.ajax({
            type: "POST",
            url: path,
            data: "schedule_id=" + id,
            success: function() {
                          swapDropUndropTags(id, args['toAdd'], args['toRemove'], args['status']); 
                    }
          });
    }

	$("a.button").click(function(){
		id = $(this).attr("schedule_id");
        
        if($(this).children("strong").attr('class') == "drop_btn_style1") {
			makeAjaxDropUndropRequest(id, true, true);
        } else if($(this).children("strong").attr('class') == "drop_btn_style1_o") {
            makeAjaxDropUndropRequest(id, false, true);
        }
    });
});