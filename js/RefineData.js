$(document).ready(function(){
	display_date_range();
	$.ajax("./controller.php/trip",
	       {   type: "GET",
		       dataType: "json",
		       success: function(return_obj, status, jqXHR) {
		      	// console.log(return_obj);
		      	filter_obj = return_obj.filter(function(d){
		      		return d.flow > 50;
		      	})
		      	// console.log(filter_obj);
		      	renderTrip(filter_obj);
		   }
	});
	$.ajax("./controller.php/station",
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
	var day_select = $("select.day");
	var month_select = $("select.month");
	var year_select = $("select.year");
	for (var i = 1; i <= 31; i++ ){
		if (i<10){
			var day = $("<option value='0"+i+"'> 0"+i+"</option>");
		}else{
			var day = $("<option value='"+i+"'> "+i+"</option>");
		}
		day_select.append(day);
	}
	for (var i=1; i<=12; i++){
		if (i<10){
			var month = $("<option value='0"+i+"'> 0"+i+"</option>");
		}else{
			var month = $("<option value='"+i+"'> "+i+"</option>");
		}
		month_select.append(month);
	}
	for (var i=2013; i<=2015; i++){
		var year = $("<option value="+i+">"+i+"</option>");
		year_select.append(year);
	}
}


var submit_date_query = function () {
	$("button#DateQuery").click(function(event){
		// alert("Submit!");
		event.preventDefault();

		var f_day = $('select#f_day').val();
		var f_month = $('select#f_month').val();
		var f_year = $('select#f_year').val();

		var t_day = $('select#t_day').val();
		var t_month = $('select#t_month').val();
		var t_year = $('select#t_year').val();
		
		var from_date = f_year+'-'+f_month+'-'+f_day;
		var to_date = t_year+'-'+t_month+'-'+t_day;
		if (from_date>to_date){
			$("#error").html("The end date should be greater than start date !")
			return false;			
		}else if (validate_date(f_day,f_month,f_year) && validate_date(t_day,t_month,t_year)){
			console.log(from_date+':'+to_date);	
			$.ajax({
				url: "./controller.php/trip/"+from_date+"/"+to_date, // app.php/review
				type: "GET",
				datatype: "JSON",
	            success: function(return_obj, status, jqXHR){
							renderStation(return_obj);
						},
				statusCode: {
    					404: function() {
      									alert( "Data not found" );
   										}
  						}
			});
		}else{
			return false;
		}
		
	});
}

var validate_date = function(day,month,year){
	if (year == '2015'){
		if (parseInt(month) > 6){
			$("#error").html("We only have data from July 2013 to 2015 June.");
			return false;
		}
	}else if (year == '2013'){
		if (parseInt(month) < 7){
			$("#error").html("We only have data from July 2013 to 2015 June.");
			return false;
		}
	} 
	if (month == '02'){
		if (parseInt(day) > 29){
			$("#error").html("The date " + year+'-'+month+'-'+ day + " is invalid.");
			return false;
		}
	}else if ('04060911'.indexOf(month) > -1){
		if (parseInt(day) > 30){
			$("#error").html("The date " + year+'-'+month+'-'+ day + " is invalid.");
			return false;
		}
	}
	return true;
}