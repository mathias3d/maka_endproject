$(document).ready(function() 
{

/* #################################################
   # REMOVE PHP/JS MESSAGE IF JAVASCRIPT IS ENABLED
   ################################################# */
setTimeout( function() 
{
	$(".message").remove();
}, 5000);


/* #################################################
   # PRINT MESSAGE
   ################################################# */
function printMessage(msg) 
{
	$("#messages").html('<div class="message"><div class="messageBox"><p>'+msg+'</p></div><img class="pull-left img-res" src="img/design/chat-bubble.png"><img class="img-res bee" src="img/design/hiveLogo.png"></div>');
	setTimeout( function() 
	{
    	$("#messages").html("");
	}, 5000);
}


/* #################################################
   # END ACCOUNT BUTTON
   ################################################# */
$(document).on('click', '#endAccountBtn', function(event) 
{
	event.preventDefault();

	var text    = 'Är du säker på att du vill avsluta ditt konto? <br>'; 
	var buttonY = '<a href="php/endAccountAction.php"><button> Ja </button></a>';
	var buttonN = '<a id="abort"><button> Avbryt </button></a>';

	$("#popup").html("<div class='popup-body'>"+text+buttonY+buttonN+"</div>");
});


/* #################################################
   # POPUP ABORT BUTTON
   ################################################# */
$(document).on('click', '#abort', function(event) 
{
	$(".popup-body").remove();
});


/* #################################################
   # PAGINATION FUNCTIONS
   ################################################# */
function getUrlVars() 
{
    var vars = {};
    var parts = window.location.href.replace(/[?&]+([^=&]+)=([^&]*)/gi, function(m,key,value) {
        vars[key] = value;
    });
    return vars;
}

function getUrlPage() 
{
    var re = /\w*\.php/i;
    var str = window.location.href;
    return re.exec(str)[0];
}

var isLoading 			= false;
var lazyloadingEnabled 	= true;
var id 					= 1;

//PAGINATION
$(window).scroll(function() 
{
	//turn of if on settings or edit page
	if (getUrlVars()['page'] != undefined) 
	{
		lazyloadingEnabled = false;
	};


	if (!lazyloadingEnabled)
	{
		return;
	}

	var sc_top 	 = $(window).scrollTop();
	var sc_limit = $(document).height() - $(window).height() - 5;

    if( !isLoading && sc_top >= sc_limit ) 
    {
	    id++;

	    if ( getUrlPage() == "index.php" ) 
	    {
	    	var url 	 = "includes/paginationPosts.php";
		}
		else
		{
			var url   	 = "includes/profileUserPosts.php";	
		}

		var currentPost  = getUrlVars()['post'];
	    if ( currentPost != undefined ) 
	    { 
	    	var url 	 = "includes/paginationPosts.php?post="+currentPost;
	    }
	   
	   	var currPost  = getUrlVars()['show'];
	    if ( currPost != undefined)
	    {
	    	var url   = "includes/profileUserPosts.php?show="+currPost;
	    }

	    // if its a profilepage - who is it?
	    var user = getUrlVars()['userId'];

		var posting = $.post( url, { pageination: id, userId: user }  );
		isLoading 	= true;

		$("#posts").after('<div id="loading"><i class="fa fa-spinner fa-pulse"></i></div>');

		posting.done(function( data ) 
		{
			$("#loading").remove();
			isLoading = false;

			if (data == "&#32;")
			{
				lazyloadingEnabled = false;
			}
			else
			{
				$("#posts").append(data);
			}
		});

    }
});



/* #################################################
   # POST / COMMENT ACTION
   ################################################# */
$(document).on('click', '#makePost', function(event) 
{
	event.preventDefault();
	
	var form 	 = $("#makePostForm")[0];
    var formData = new FormData( form );

    var myStats = $("#nrPosts");
	var number  = /\d{1,100000000}/.exec( myStats.html() );

    $.ajax({
        url: "php/postActionAJAX.php",
        type: "POST",
        data: formData,
        async: false,
        success: function (data) 
        {
			myStats.html("Skrivit " + (parseInt(number) + 1) + " st inlägg.");
            $("#posts").prepend(data);
        },
        cache: false,
        contentType: false,
        processData: false
    });

    //reset Form
    $("#makePostForm").find("input[type=file]").val("");
    $("#fileName").html("");
    $("#makePostForm").trigger("reset");
    
});

$(document).on('click', '.makeComment', function(event) 
{
	event.preventDefault();
	
	var form 	 = $(this).parent(".makeCommentForm")[0];
    var formData = new FormData( form );

    var comments = $(this).closest(".comments-wrapper").siblings(".article-post").find("#noCom");
    var noCom	 = parseInt( comments.html() );

    // id of the post that is commented
    var post_id   =  $(this).parent(".makeCommentForm").find("input[name='post_id']").val()
    var printHere = ".comAppendWrap" + post_id;

    $.ajax({
        url: "php/postActionAJAX.php",
        type: "POST",
        data: formData,
        async: false,
        success: function (data) 
        {
        	$(document).find(printHere).prepend(data);
        	comments.html(noCom+1);
        	comments.siblings(".fa-comments-o").addClass("liked");
        },
        cache: false,
        contentType: false,
        processData: false
    });
    //reset Form
    $(this).parent(".makeCommentForm").trigger("reset");
});


// IMAGE UPLOAD
$( "#fileButton" ).click(function(event) 
{
	event.preventDefault();
	// make a click on the "real" button
	$( "#fileInput" ).trigger( "click" )
	// get the filename and print it
	.change(function() {
        var filename = $('#fileInput').val().replace(/.*(\/|\\)/, '');
        $( "#fileName" ).html( filename );
    });
});



//////////////////////////////////// WORKING HERE ////////////////////////////////////
// Youtube Link
$( "#youtubeButton" ).click(function(event) 
{
	event.preventDefault();

	var text    = 'Skriv in adressen till Youtube-videon <br>'; 
	var buttonY = '<a id="useYoutube"><button> Använd </button></a>';
	var buttonN = '<a id="abort"><button> Avbryt </button></a>';

	$("#popup").html("<div class='popup-body'>"+text+buttonY+buttonN+"</div>");


	$("input[name='youtube']").val("__linke here___"); 
});
////////////////////////////////// WORKING HERE END //////////////////////////////////


/* #################################################
   # VALIDATION - POST / COMMENT ACTION
   ################################################# */







/* #################################################
   # CREATE ACCOUNT or LOGGIN - FORM on startpage
   ################################################# */
// create account FORM
$(document).on('click', '#createAccountBtn', function(event) 
{
	event.preventDefault();
	
	var url = "includes/createAccount_form.php";
	var posting = $.post( url );

	posting.done(function( data ) 
	{
		$(".form").html(data);
	});

});

// Login account FORM
$(document).on('click', '#loginAccountBtn', function(event) 
{
	event.preventDefault();

	var url = "includes/login_form.php";
	var posting = $.post( url );

	posting.done(function( data ) 
	{
		$(".form").html(data);
	});
});

/* #################################################
   # VALIDATION - CREATE ACCOUNT / LOGGIN on startpage
   ################################################# */
// Login account FORM
$(document).on("click", "#loginSubmitBtn, #createSubmitBtn", function(event) 
{
	var input 	 = $(".loginForm").find("section").find(":input");
	var username = $(".loginForm input[name='user']");
	
	$(input, this).each(function(index, el)
	{
        if ( !$(el).val() ) 
        {
        	$(this).css("border-color","#c00");
        	event.preventDefault();
		}
	});

	if ( $(".loginForm").find("h2").html() == "Logga in" ) 
	{
		if (username.val().indexOf("@") === -1)
		{
			username.css("border-color","#c00");
	        event.preventDefault();
		}
	}
	else
	{
		if (username.val().indexOf(" ") === -1)
		{
			username.css("border-color","#c00");
	        event.preventDefault();
		}
		//this crap email is undefined. it does not exist on page load (it gets called by ajax)
		/*
		var email = $(this).find("[name='email']");
		if (email.val().indexOf("@") === -1 || email.val().indexOf(".") === -1)
		{
			email.css("border-color","#c00");
	        event.preventDefault();
		}
		*/
	}
});

function clearBorder()
{
	if( this.value.length > 0 )
	{
		this.style.borderColor = "#ccc";
	}
	else
	{
		this.style.borderColor = "#c00";
	}
}
$(document).on('input', "input[name='user'], input[name='pwd'], input[name='email']", clearBorder);



/* #################################################
   # DROPDOWN BUTTON
   ################################################# */

$(document).on('click', '.dropdown', function() 
{
	$(this).toggleClass("open");
}); 

$(document).on('click', 'html', function() 
{
	$(".dropdown").removeClass("open");
}); 


// small screen y button meny
$(".navbar-toggle").click(function()
{
	$(".navbar-collapse").toggleClass("collapse");
	$(".navbar-collapse > ul").toggleClass("fill");
	$("body").toggleClass("overflow");
	$("header").toggleClass("white");
});


/* #################################################
   # ARTICLES TOGGLE COMMENTS 
   ################################################# */
$(document).on('click', '.article-box', function() 
{
	$( this ).find( ".comments-wrapper" ).slideToggle(500);
});

$(document).on("click", "a, .article-comment form, .dropdown, .pop", function(event) 
{
 	event.stopPropagation();
});


/* #################################################
   # ARTICLES LIKES ACTION
   ################################################# */
$(document).on('click', '.likeButton', function(event)
{

	event.preventDefault();

	// Get some values from elements on the page:
	var id 	=  $( this ).attr("value");
	var url = "php/likeActionAJAX.php";

	var myStats = $("#nrLikes");
	var number  = /\d{1,100000000}/.exec(myStats.html());

	// change color of heart
	var i 	= $(this).find("i");
	var red = $(this).find("i.liked");

	// Send the data using post
	var posting = $.post( url, { like: id } );

	// ´when done....
	posting.done(function( data ) {

		if (red.length == 1) 
		{
			red.removeClass("liked");
			myStats.html("Gillar " + (parseInt(number) - 1) + " st inlägg.");
		}
		else
		{
			i.addClass("liked");
			myStats.html("Gillar " + (parseInt(number) + 1) + " st inlägg.");
		};

		// update number of likes 
		if( data < 1 ){ data = ""; };

		$("#like"+id).html(data);
	});
});


/* #################################################
   # ARTICLES DELETE ACTION
   ################################################# */
$(document).on('click', '.delete', function(event)
{
	var text    = 'Är du säker på att du vill radera ditt inlägg? <br>'; 
	var buttonY = '<a id="yesDeletePost"><button> Ja </button></a>';
	var buttonN = '<a id="abort"><button> Avbryt </button></a>';

	$("#popup").html("<div class='popup-body'>"+text+buttonY+buttonN+"</div>");

	// Get some values from elements on the page:
	var id 	 =  $( this ).attr("value");
	var url  = "php/deletePostActionAJAX.php";

	var myStats = $("#nrPosts")
	var number  = /\d{1,100000000}/.exec(myStats.html());

	var post = $( this ).closest(".article-box")

	$(document).on('click', '#yesDeletePost', function(event)
	{
		$(".popup-body").remove();

		// Send the data using post
		var posting = $.post( url, { del: id } );

		// ´when done....
		posting.done(function( data ) {

			if (data) 
			{
				data = JSON.parse(data);

				post.html("").remove();

				myStats.html("Skrivit " + (parseInt(number) - 1) + " st inlägg.");

				printMessage(data.msg);
			};
		
		});

	});
});



/* #################################################
   # ARTICLES SHARE ACTION
   ################################################# */
$(document).on('click', '.share', function(event)
{
	event.preventDefault();

	// Get some values from elements on the page:
	var id 	 =  $( this ).attr("value");
	var url  = "php/sharePostActionAJAX.php";

	var post = $( this ).closest(".article-box");
	var button = $(this).closest(".dropdown");

	// Send the data using post
	var posting = $.post( url, { share: id } );

	// ´when done....
	posting.done(function( data ) 
	{
		if (data) 
		{
			//data = JSON.parse(data);


			button.removeClass("open");

			post.before(data);


			//printMessage(data.msg);
		};
	
	});
});


/* #################################################
   # FOLLOW ACTION
   ################################################# */
$(".followButton").click(function(event) 
{ 
	event.preventDefault();

	// Get some values from elements on the page:
	var id 			=  $( this ).attr("value");
	var url 		= "php/followActionAJAX.php";

	// Send the data using post
	var posting = $.post( url, { follow: id } );

	// ´when done....
	posting.done(function( data ) {
		data = JSON.parse(data);

		// update followbutton and number of followers
		$(".followButton").find("span").html(data.follow);

		$("#followers").html("Har "+data.numfollowers+" st följare.");
	});
});



/* #################################################
   # SEARCH 
   ################################################# */
$("#searchBox").keyup(function() 
{
	var url   = "php/searchActionAJAX.php";
	var input = $("#searchBox").val();

	// Send the data using post
	var posting = $.post( url, { searchBox: input } );

	// ´when done....
	posting.done(function( data ) {

		$("#results").html(data);
	
	});
});


/* #################################################
   # IMAGE POPUP
   ################################################# */

$(document).on('click', '.pop', function(event)
{
	var img = $(this).parent().html();

	$("#pop").html(img);
	$("#pop-backdrop").css("display","inline-block");
});

$(document).on('click', '#pop-backdrop, #pop img', function(event)
{	
	$("#pop").html();
	$("#pop-backdrop").css("display","none");
});










});//document.ready













/* #################################################
   # NOT IN USE , but saved for... nostalgic reasons
   ################################################# */

/*
function live_bind(elm, fn) {
	$(document).on('click', elm, fn);
}

function ajax_load(url, cb) {
	var posting = $.post( url );

	posting.done(cb);
}

function form_callback(data) {
	$(".form").html(data);
}

live_bind('#loginAccountBtn', ajax_load('includes/createAccountPage.php', form_callback));
*/