
  window.fbAsyncInit = function() {
  FB.init({
    appId      : '226343797569469',
    status     : true, // check login status
    cookie     : true, // enable cookies to allow the server to access the session
    xfbml      : true  // parse XFBML
  });

  // Here we subscribe to the auth.authResponseChange JavaScript event. This event is fired
  // for any authentication related change, such as login, logout or session refresh. This means that
  // whenever someone who was previously logged out tries to log in again, the correct case below 
  // will be handled. 
  /*
  FB.Event.subscribe('auth.authResponseChange', function(response) {
  	//console.log(response);
    // Here we specify what we do with the response anytime this event occurs. 
    if (response.status === 'connected') {
      // The response object is returned with a status field that lets the app know the current
      // login status of the person. In this case, we're handling the situation where they 
      // have logged in to the app.
      getData();
    } 
  });*/
  };

  // Load the SDK asynchronously
  (function(d){
   var js, id = 'facebook-jssdk', ref = d.getElementsByTagName('script')[0];
   if (d.getElementById(id)) {return;}
   js = d.createElement('script'); js.id = id; js.async = true;
   js.src = "//connect.facebook.net/en_US/all.js";
   ref.parentNode.insertBefore(js, ref);
  }(document));


	function fblogin(){
		FB.login(function(response) {
		    if (response.authResponse) {
		        // The person logged into your app
		        getData();
		        console.log("redirect");
		        window.location.replace("http://hugde.com/home");
		    } else {
		        // The person cancelled the login dialog
		    }
		});
	}
  // Here we run a very simple test of the Graph API after login is successful. 
  // This testAPI() function is only called in those cases. 
  function getData() {
    FB.api('/me?fields=email,name,picture', function(response) {
    	//send data through ajax
    	$.ajax({
						
						//url to send the data to
						url: "http://hugde.com/fblogin/facebook",
						async:false,
						data: {'response':response},
						type: 'post',
						dataType: 'json',
						beforeSend:function(){
						//Show Autosaving div
							$("#spinner").css('display','block');
						},
						complete:function(){
							console.log("complete");
						},
						success:function(data){
							console.log("success");
							return 1;
						}
				});
     //console.log(response);
    });   
  }
   
  function logout(){
  	FB.logout(function(response) {
        // Person is now logged out
        console.log('Logged out');
    });
  }
