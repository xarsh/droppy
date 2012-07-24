$(function(){
    $('.main_calender td').addClass('register');
    
    $('.register').CreateBubblePopup({
        innerHtml: ''
        
    });
    
    $('.register').click(
        function(){
            $('.jquerybubblepopup').hide();
            $(this).ShowBubblePopup({
                
                imageFolder: 'jQuery/bubblePopup/bp_images',
                innerHtml: '<table><tbody><tr><th colspan=2>予定</th></tr><tr><td>日時:</td><td>４月３日</td></tr><tr><td>タイトル:</td><td><input type="text"></input></td></tr><tr><td><input type="submit" value="予定を作成"></td></tr></tbody><table>',
                innerHtmlStyle: {
                    color:'#000000', 
                    'text-align':'center'
                },
                themeName: 	'black',
//                themePath: 	'img/jquerybubblepopup-theme'
            

            })
            $(this).FreezeBubblePopup(); 
        })

        
        
});