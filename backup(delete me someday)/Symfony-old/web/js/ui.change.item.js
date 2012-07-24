$(function(){
    //変更をクリック => 入力フォームに切り替わり
    $(".edit").css("display", "none");
    
    $('.setting_box tr').each(function(i){
		$(this).attr('class','item_' + (i+1));
	});
	
	$(".setting_box tr").each(function (i) {
		i = i+1;
		
		$('.item_' + i + ' .rewrite span').click(function() {
			$('.item_' + i + ' .rewrite span').fadeOut("fast").css("display", "none");
			$('.item_' + i + ' .edit').fadeIn("fast").css("display", "block");
			$('.item_' + i + ' .set').fadeOut("fast").css("display", "none");
			$('.item_' + i + ' th').addClass("active");
			$('.item_' + i + ' td').addClass("active");
		});
		
		$('.item_' + i + ' .edit li').click(function() {
			$('.item_' + i + ' .rewrite span').fadeIn("fast").css("display", "block");
			$('.item_' + i + ' .edit').fadeOut("fast").css("display", "none");
			$('.item_' + i + ' .set').fadeIn("fast").css("display", "block");
			$('.item_' + i + ' th').removeClass("active");
			$('.item_' + i + ' td').removeClass("active");
		});
    });
    
    //スケジュールのカラムをクリック => 詳細に切り替わり
    $(".eve_detail_box").css("display", "none");
    
    $('.main_calender .eve_name').click(function() {
    	$(".main_cal_box").fadeOut("fast").css("display", "none");
    	$(".eve_detail_box").fadeIn("fast").css("display", "block");
    });
	//タイムラインのリストをクリック => 詳細に切り替わり
    $('.change_eve_detail').click(function() {
    	$(".main_cal_box").fadeOut("fast").css("display", "none");
    	$(".eve_detail_box").fadeIn("fast").css("display", "block");
    });
	$('.eve_detail_box .return_cal').click(function() {
    	$(".eve_detail_box").fadeOut("fast").css("display", "none");
    	$(".main_cal_box").fadeIn("fast").css("display", "block");
    });
});
