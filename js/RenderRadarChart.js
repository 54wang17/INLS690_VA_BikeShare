renderRadarChart = function(station_id,all_stations){
	console.log("RadarChart_Render called;");
	
	var w = 700,
	h = 700;

	var colorscale = d3.scale.category10();

	//Legend titles
	var LegendOptions = ['Incoming trips','Outgoing trips'];
	
	$.ajax("./controller.php/trip/to/"+station_id,
	    {   type: "GET",
		    dataType: "json",
		    success: function(return_obj, status, jqXHR) {
		      	var from_stations = return_obj.map(function(d){return d.from_station_id;});
		   		all_stations.forEach(function(d,i) {
	     			var station_index = from_stations.indexOf(d.station_id);
	     			var station_name = d.name;
	     			var station_incoming_flow = 0;
	     			if (station_index >=0) {
	     				var fil=return_obj.filter(function(j){return d.station_id == j.from_station_id;})
	     				station_incoming_flow = fil[0].flow;
	     				// console.log(station_name + '- Incoming:' + station_incoming_flow);
	     			}
	     			d.incoming_flow = station_incoming_flow;
	      		});
	      		$.ajax("./controller.php/trip/from/"+station_id,
				    {   type: "GET",
					    dataType: "json",
					    success: function(return_obj, status, jqXHR) {
					      	var to_stations = return_obj.map(function(d){return d.to_station_id;});
					   		all_stations.forEach(function(d,i) {
				     			var station_index = to_stations.indexOf(d.station_id);
				     			var station_name = d.name;
				     			var station_outgoing_flow = 0;
				     			if (station_index >=0) {
				     				var fil=return_obj.filter(function(j){return d.station_id == j.to_station_id;})
				     				station_outgoing_flow = fil[0].flow;
				     				// console.log(station_name + '- Outgoing:' + station_outgoing_flow);
				     			}
				     			d.outgoing_flow = station_outgoing_flow;
				      		});
				      		console.log(all_stations);
				      		console.log("Generate Data!");
							var Incoming = [];
							var Outgoing = [];
							all_stations.forEach(function(d,i) {
								Incoming.push({axis:d.name,value:Number(d.incoming_flow),key:d.station_id});
								Outgoing.push({axis:d.name,value:Number(d.outgoing_flow),key:d.station_id});
							});
							// Outgoing.forEach(function(d,i) {
							// 	d.value = Math.log2(d.value);
							// });
							// Incoming.forEach(function(d,i) {
							// 	d.value = Math.log2(d.value);
							// });
							console.log(Outgoing.map(function(d){return d.value;}));
							console.log(Incoming.map(function(d){return d.value;}));

							d = [Incoming,Outgoing];
							// d3.select('#chart').remove();
							RadarChart.draw("#chart", d, mycfg);
					   	}
					});
				
			}
		});



//Options for the Radar chart, other than default
var mycfg = {
  w: w,
  h: h,
  maxValue: 1,
  levels: 10,
  ExtraWidthX: 200
}

//Call function to draw the Radar chart
//Will expect that data is in %'s
// RadarChart.draw("#chart", d, mycfg);

////////////////////////////////////////////
/////////// Initiate legend ////////////////
////////////////////////////////////////////

var svg = d3.select('#body')
	.selectAll('svg')
	.append('svg')
	.attr("width", w+300)
	.attr("height", h)

//Create the title for the legend
var text = svg.append("text")
	.attr("class", "title")
	.attr('transform', 'translate(90,0)') 
	.attr("x", w - 70)
	.attr("y", 10)
	.attr("font-size", "12px")
	.attr("fill", "#404040")
	.text("How many trips from this station?");
		
//Initiate Legend	
var legend = svg.append("g")
	.attr("class", "legend")
	.attr("height", 100)
	.attr("width", 200)
	.attr('transform', 'translate(90,20)') 
	;
	//Create colour squares
	legend.selectAll('rect')
	  .data(LegendOptions)
	  .enter()
	  .append("rect")
	  .attr("x", w - 65)
	  .attr("y", function(d, i){ return i * 20;})
	  .attr("width", 10)
	  .attr("height", 10)
	  .style("fill", function(d, i){ return colorscale(i);})
	  ;
	//Create text next to squares
	legend.selectAll('text')
	  .data(LegendOptions)
	  .enter()
	  .append("text")
	  .attr("x", w - 52)
	  .attr("y", function(d, i){ return i * 20 + 9;})
	  .attr("font-size", "11px")
	  .attr("fill", "#737373")
	  .text(function(d) { return d; })
	  ;	
}


