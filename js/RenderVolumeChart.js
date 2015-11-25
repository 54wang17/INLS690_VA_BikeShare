function renderVolumeChart(all_stations){
    
    document.getElementById("volumeDistanceChart").innerHTML = "";
    console.log('renderVolumeChart called');
    // console.log(all_stations);
     // This variable is used to define size of the visualization canvas and the
    	// margin (or "padding") around the scattter plot.  We use the margin to draw
    	// things like axis labels.
      var height = 500;
      var width = 500;
      var margin = 50;
     	var ymax = d3.max(all_stations, function(d){return parseInt(d.incoming_flow);});
     	var xmax = d3.max(all_stations, function(d){return parseInt(d.outgoing_flow);});
     	// console.log("incoming="+ymax);

    	// Create the SVG canvas that will be used to render the visualization.
    	var svg = d3.select("#volumeDistanceChart")
        	.append("svg")
        	.attr("width", width)
        	.attr("height", height);

    	// Define x axis and y axis.
    	var x = d3.scale.linear()
        	.domain([0,xmax])
        	.range([margin,width-margin]);

    	var y = d3.scale.linear()
        	.domain([ymax,0])
        	.range([margin,height-margin]);

    	// Add axes.  First the X axis and label.
    	svg.append("g")
        	.attr("class", "axis")
        	.attr("transform", "translate(0,"+(width-margin)+")")
        	.call(d3.svg.axis().orient("bottom").scale(x));

    	svg.append("text")
        	.attr("class", "axis-label")
        	.attr("y", height-15)
        	.attr("x",0 + (width / 2))
        	.style("text-anchor", "middle")
        	.text("Volume Outgoing (trips)");

    	// Now the Y axis and label.
    	svg.append("g")
        	.attr("class", "axis")
        	.attr("transform", "translate("+margin+",0)")
        	.call(d3.svg.axis().orient("left").scale(y));

    	svg.append("text")
        	.attr("transform", "rotate(90)")
        	.attr("class", "axis-label")
        	.attr("y", 0)
        	.attr("x", width / 2)
        	.style("text-anchor", "middle")
        	.text("Volume Incoming (trips)");

    	// Now a clipping plain for the main axes
    	// Add the clip path.
    	svg.append("clipPath")
          	.attr("id", "clip")
        	.append("rect")
              	.attr("x", margin)
              	.attr("y", margin)
              	.attr("width", width-2*margin)
              	.attr("height", height-2*margin);
   			   
    
      
   	  var incomingOutgoing = all_stations.map(function (d) {
   		 return [d.station_id, parseInt(d.incoming_flow), parseInt(d.outgoing_flow)];});
   	 
   	 
   	 var selfRemoved = 0;
   	 for(var i = 0; i < incomingOutgoing.length; i++){
   		 if(incomingOutgoing[i][1]==incomingOutgoing[i][2] && selfRemoved==0){
   			 incomingOutgoing.splice(i,1);
   			 selfRemoved = 1;
   		  }
   	 }
   	 
   	 var circles = svg.selectAll("circle").data(incomingOutgoing);
   	 
   	 circles.enter().append("circle")
            	.attr("r", 5)
   			 .attr("cx", function(d){return x(d[1])})
   			 .attr("cy", function(d){return y(d[2])})
   			 .on('mouseover', function (d){
   				 d3.select(this).style("fill","yellow");
   				 d3.selectAll(".station").filter(function(j){return d.station_id == j.station_id;}).style("fill", "blue");
   			   })
   	 .on('mouseout', function(){
    
   				 d3.select(this).style("fill", "black")
   				 d3.selectAll(".station").style("fill", "green");
   				 d3.select("#clicked_station").style("fill", "red");
   			   });
   	 
   	 var justFlows = incomingOutgoing.map(function(d){return [d[1],d[2]]});   	 
   	 var linear_regression = ss.linearRegression(justFlows);
    	var linear_model = ss.linearRegressionLine(linear_regression);
   	 
   		 var x1 = 0;
   		 var y1 = linear_model(x1);
   		 var x2 = xmax;
   		 var y2 = linear_model(x2);
   		 
   		 if (y2>ymax){
   			 x2 = (ymax - linear_regression.b)/linear_regression.m;
   			 y2 = ymax;
   		 }

   		 var lineData = [x1,y1,x2,y2];
   		 // console.log(lineData);
   		 
   		 //Bind line to array describing line
   		 var line = svg.selectAll(".trendline").data(lineData);
   		 
   		 //Add line
   			 line.enter()
   				 .append("line")
   				 .attr("class", "trendline")
   				 .attr("x1", function(d) { return x(lineData[0]); })
   				 .attr("y1", function(d) { return y(lineData[1]); })
   				 .attr("x2", function(d) { return x(lineData[2]); })
   				 .attr("y2", function(d) { return y(lineData[3]); })
   				 .attr("stroke-dasharray", ("5,2"))
   				 .attr("stroke", "black")
   				 .attr("stroke-width", 1);
   		 
   		 var rsquared = d3.round(ss.rSquared(justFlows, linear_model),2);
   		 svg.append("text")
   			 .attr("x", 100)        	 
   			 .attr("y", 100)
   			 .attr("text-anchor", "middle")  
   			 .style("font-size", "11px")
   			 .text("R squared = "+rsquared);
 
}
