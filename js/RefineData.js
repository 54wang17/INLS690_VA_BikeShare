$(document).ready(function(){
	display_date_range();
	// $.ajax("./controller.php/trip",
	//        {   type: "GET",
	// 	       dataType: "json",
	// 	       success: function(return_obj, status, jqXHR) {
	// 	      	// console.log(return_obj);
	// 	      	filter_obj = return_obj.filter(function(d){
	// 	      		return d.flow > 50;
	// 	      	})
	// 	      	// console.log(filter_obj);
	// 	      	renderTrip(filter_obj);
	// 	   }
	// });
	$.ajax("./controller.php/station/top/50",
	       {   type: "GET",
		       dataType: "json",
		       success: function(return_obj, status, jqXHR) {
		      	// console.log(return_obj);

		      	renderStation(return_obj);
		   }
	});
	submit_date_query();


});


function display_date_range(){
	var date_select = $("select.date");
	
	for (var i=2013; i<=2015; i++){
		for (var j=1; j<=12; j++){
			if (j<10){
				$date = i+"-0"+j;
			}else{
				$date = i+"-"+j;
			}
			
			if (($date <= '2015-06') && ($date >= '2013-07')){
				var date_option = $("<option value='"+$date+"'>"+$date+"</option>");
				date_select.append(date_option);
			}
		}
	}
	console.log('date loaded');
}


var submit_date_query = function () {
	$("button#DateQuery").click(function(event){
		event.preventDefault();

		var from_date = $('select#f_date').val();
		
		var to_date = $('select#t_date').val();

		var weight = $('select#FlowDir').val();

		var rank = $('select#Rank').val();
	
		if (from_date>to_date){
			$("#error").html("The end date should be greater than start date !")
			return false;			
		}else{
			console.log(from_date+':'+to_date);	
			console.log("./controller.php/station/"+rank+"/50/"+weight+"/"+from_date+"/"+to_date);
			$.ajax({
				url: "./controller.php/station/"+rank+"/50/"+weight+"/"+from_date+"/"+to_date, 
				type: "GET",
				datatype: "JSON",
	            success: function(return_obj, status, jqXHR){
	            			console.log("Get Data");
	            			console.log(return_obj);
							renderStation(return_obj);
						},
				statusCode: {
    					404: function() {
      									alert( "Data not found" );
   										}
  						}
			});
		}
		
	});
}

// var validate_date = function(day,month,year){
// 	if (year == '2015'){
// 		if (parseInt(month) > 6){
// 			$("#error").html("We only have data from July 2013 to 2015 June.");
// 			return false;
// 		}
// 	}else if (year == '2013'){
// 		if (parseInt(month) < 7){
// 			$("#error").html("We only have data from July 2013 to 2015 June.");
// 			return false;
// 		}
// 	} 
// 	if (month == '02'){
// 		if (parseInt(day) > 29){
// 			$("#error").html("The date " + year+'-'+month+'-'+ day + " is invalid.");
// 			return false;
// 		}
// 	}else if ('04060911'.indexOf(month) > -1){
// 		if (parseInt(day) > 30){
// 			$("#error").html("The date " + year+'-'+month+'-'+ day + " is invalid.");
// 			return false;
// 		}
// 	}
// 	return true;
// }