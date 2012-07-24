$(function(){
    //ヘッダー右上 ログイン開閉
    $("#dulldown li div").css("display", "none");
    $("#dulldown li span").each(function(i){
        $(this).click(function(){
            $("#dulldown li div").eq(i).animate( { height: 'toggle' }, '1000' );
        });
    });
    
    //ヘッダー右上 歯車アイコン開閉
    $(".header_menu ul").css("display", "none");
    $(".header_menu p span img").each(function(i){
        $(this).click(function(){
            $(".header_menu ul").eq(i).animate( { height: 'toggle' }, '1000' );
        });
    });
    
    //トップ右カラム 新しく登録開閉
    $("#sidebar .btn_register ul").css("display", "none");
    $("#sidebar .btn_register p").each(function(i){
        $(this).click(function(){
            $("#sidebar .btn_register ul").eq(i).animate( { height: 'toggle' }, '1000' );
        });
    });
    
    //イベント作成 日時開閉
    $(".make_event_box .end_ymd").css("display", "none");
    $(".make_event_box p.end_set").each(function(i){
        $(this).click(function(){
            $(".make_event_box .end_ymd").eq(i).animate( { height: 'toggle' }, 'fast' );
        });
    });
    
    //イベント作成 詳細開閉
    $(".make_event_box .more_sche_box").css("display", "none");
    $(".make_event_box .more").each(function(i){
        $(this).click(function(){
            $(".make_event_box .more_sche_box").eq(i).animate( { height: 'toggle' }, 'fast' );
        });
    });
	
	//イベント作成 日程を追加開閉
    $(".make_event_box .add_sche_ymd").css("display", "none");
    $(".make_event_box .add_sche").each(function(i){
        $(this).click(function(){
            $(".make_event_box .add_sche_ymd").eq(i).animate( { height: 'toggle' }, 'fast' );
        });
    });
    
    //スケジュール作成 詳細開閉
    $(".make_cal_box .more_cal_box").css("display", "none");
    $(".make_cal_box .more").each(function(i){
        $(this).click(function(){
            $(".make_cal_box .more_cal_box").eq(i).animate( { height: 'toggle' }, 'fast' );
        });
    });
    
    //イベント作成（ポップアップ） 日時開閉
    $(".overlay_body .end_ymd").css("display", "none");
    $(".overlay_body p.end_set").each(function(i){
        $(this).click(function(){
            $(".overlay_body .end_ymd").eq(i).animate( { height: 'toggle' }, 'fast' );
        });
    });
    
    //イベント作成（ポップアップ） 詳細開閉
    $(".overlay_body .more_sche_box").css("display", "none");
    $(".overlay_body .more").each(function(i){
        $(this).click(function(){
            $(".overlay_body .more_sche_box").eq(i).animate( { height: 'toggle' }, 'fast' );
        });
    });
	
	//イベント作成（ポップアップ） 日程を追加開閉
    $(".overlay_body .add_sche_ymd").css("display", "none");
    $(".overlay_body .add_sche").each(function(i){
        $(this).click(function(){
            $(".overlay_body .add_sche_ymd").eq(i).animate( { height: 'toggle' }, 'fast' );
        });
    });
});
