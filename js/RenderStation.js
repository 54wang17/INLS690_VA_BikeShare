function renderStation(collection){
	d3.select("#chart").select("svg").remove();

	collection.forEach(function(d,i) {
		d.rank = i+1;
      	d.LatLng = new L.LatLng(d.latitude,
                  d.longitude);
    });
	var bounds = new L.LatLngBounds(collection.map(function(d){return d.LatLng}));
	map.fitBounds(bounds);

	var feature = g.selectAll(".station")
	      .data(collection,function(d){return d.station_id;});

	var circle_normalized = d3.mean(collection.filter(function(d){return (d.inflow+d.outflow)>0;}),function(d){return d.inflow+d.outflow;})/6.0;

	feature.enter().append("circle")
	      .style("stroke", "black")  
	      .style("opacity", .6) 
	      .style("fill", function(d){if ((d.inflow + d.outflow)>0){return "#3db7e4";}else{return "black";} })
	      .style("stroke-width", 2)
	      .attr("class","station")
	      .attr("cx",function(d){return map.latLngToLayerPoint(d.LatLng).x;})
	      .attr("cy",function(d){return map.latLngToLayerPoint(d.LatLng).y;})
	      .attr("r", function(d){return ((d.inflow+d.outflow))/circle_normalized;})
	      .on("click",function(d){
	      		document.getElementById("DateRange").innerHTML =
			//"<div id='stationInfoSection'>" +
		//"<p><b>Station Information</b> (Click on the circles to read more about the stations.)</p>"+
		//"<div id='stationInfo'>"+
		//	"<b>Station: "+d.name+"</b>"+
	      	//	"<p>Capacity: "+d.dpcapacity+" </p>"+
	      	//	"<p>Number of trips to this station during time period selected: "+d.inflow+" </p>"+
	      	//	"<p>Number of trips from this station during time period selected: "+d.outflow+" </p>"+
	      		// "<p>Average number of trips monthly during time period selected: "+
		//"</div>"+
		//"<div id='chart'></div>"+	
	//"</div>"
	"<p style='margin-top: 10px; padding-top: 10px;'>Station: "+d.name+"</p>"+
	"<ul style='list-style-type:none;'>"+
	      		"<li>Bike Capacity: "+d.dpcapacity+" </li>"+
	      		"<li># of incoming trips during selected date range: "+d.inflow+" </li>"+
	      		"<li># of outgoing trips during selected date range: "+d.outflow+" </li>"+
	"</ul>";
	document.getElementById("charttitle").innerHTML =
	"<p style='margin-top: 10px; padding-top: 10px;'>Number of trips from "+d.name+" to other stations</p>";
	document.getElementById("volumeDistanceCharttitle").innerHTML  =
	"<p style='margin-top: 10px; padding-top: 10px;'>Incoming vs. Outgoing trips for "+d.name+"</p>";
			 		
	     		d3.selectAll(".station").attr("id",null).style("fill", "#3db7e4").style("stroke-width", 2);
	     		d3.select(this).style("fill", "red").attr("id","clicked_station");
	     		renderCharts(d.station_id,collection);
	      }) ; 

	

	feature.attr("class","station")	      
			.style("fill", function(d){
				if ((d.inflow + d.outflow)>0){return "#3db7e4";}else{return "black";} })
			.attr("r", function(d){return ((d.inflow+d.outflow))/circle_normalized;});
	map.on("viewreset", update);
	update();

	feature.exit().remove();



	function update() {
		feature.attr("cx",function(d){return map.latLngToLayerPoint(d.LatLng).x;})
	    	.attr("cy",function(d){return map.latLngToLayerPoint(d.LatLng).y;})
	 		.attr("class","station")
	 		.style("fill", function(d){if ((d.inflow + d.outflow)>0){return "#3db7e4";}else{return "black";} })
	 		.attr("r", function(d){
	      		var change_ratio = map.getZoom()/6;
	      		var normalized_r = (d.inflow+d.outflow)/circle_normalized;		
	      		if ((d.inflow + d.outflow) == 0){
	      			normalized_r = 5;
	      		}
	      		return normalized_r*change_ratio;}); 

	 // feature.attr("transform", 
		 //  function(d) { 
		 //  	console.log("translate("+ 
		 //      map.latLngToLayerPoint(d.LatLng).x +","+ 
		 //      map.latLngToLayerPoint(d.LatLng).y +")");
		 //    return "translate("+ 
		 //      map.latLngToLayerPoint(d.LatLng).x +","+ 
		 //      map.latLngToLayerPoint(d.LatLng).y +")";
		 //    })
	}
	
}