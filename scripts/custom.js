function ajax(url, callback) {
	var xrequest;
	try {
			xrequest=new XMLHttpRequest();
		} catch (e)   {
		try {
			 xrequest=new ActiveXObject("Msxml2.XMLHTTP");
			 } catch (e) {
			 try {
				 xrequest=new ActiveXObject("Microsoft.XMLHTTP");
				  } catch (e) {
					 return false;
				  }
			}
		 }
		 
	xrequest.onreadystatechange=function() {
	   if(xrequest.readyState==4) {
				callback;
			}
	   }
   
	xrequest.open("GET", url ,true);
	xrequest.send(null);
	
}

function deleteCourse(courseId) {
	ajax('kurse.php?ajax=1&delete_course=' + courseId, function () { Effect.BlindDown('tr_' + courseId, { duration: 1.0 }); } );
}

