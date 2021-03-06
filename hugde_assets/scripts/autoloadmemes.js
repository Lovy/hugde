$(document).ready(function(){
	var pagesLoaded = 2; //1st page already loaded during intitialization
	var totalMemes;
	var loading = false;
	$.ajax({
		url:"http://hugde.com/memes/totalmemes",
		async:false,
		dataType:"json",
		success:function(data){
			totalMemes = data[0].count;
		}
		
	});
	var memesPerPage = 2;
	//console.log(totalMemes);
	var totalPages = Math.round(parseInt(totalMemes)/parseInt(memesPerPage));
	//console.log(totalPages);
	
	function getDocHeight() {
    var D = document;
    return Math.max(
        Math.max(D.body.scrollHeight, D.documentElement.scrollHeight),
        Math.max(D.body.offsetHeight, D.documentElement.offsetHeight),
        Math.max(D.body.clientHeight, D.documentElement.clientHeight)
    );
	}
	
	function showDiv(){
		
	}
	  
    $(window).scroll(function() { //detect page scroll
        
        if($(window).scrollTop() + $(window).height() >= (getDocHeight()))  //user scrolled to bottom of the page?
        {
            
            if(pagesLoaded < totalPages && loading==false) //there's more data to load
            {
                loading = true; //prevent further ajax loading
                $('.page-content').append('<div id="ajaxLoading" style="text-align:center"><img src="http://hugde.com/hugde_assets/img/ajax-loader.gif" /></div>'); //show loading image
                
                //load data from the server using a HTTP POST request
                $.post('http://hugde.com/memes/autoload',{'HPP': memesPerPage,'PN':pagesLoaded+1}, function(data){
                                    
                    $(".mix-grid").append(data); //append received data into the element
					try{
				        FB.XFBML.parse();
				        twttr.widgets.load(); 
				    }catch(ex){}
				    console.log("Autoscroll called"+pagesLoaded);
				    
                    pagesLoaded++; //loaded group increment
                    loading = false; 
                    //hide loading image
                    $('#ajaxLoading').remove(); //hide loading image once data is received
                    
                
                }).fail(function(xhr, ajaxOptions, thrownError) { //any errors?
                    
                    //alert(thrownError); //alert with HTTP error
                    //$('.animation_image').hide(); //hide loading image
                    loading = false;
                
                });
                
            }
        }
   
	});
});
