  const MARKER = "X";
  const MARKER_NO = " ";
  
  /**
   * Export of marker data
   * 
   * Format: json
   * Example Export:
   * {
   * 	"1" : { //Month
   * 		"0" : [4,5,6], //Position in Personlist : [Days marked]
   * 		"1" : [1,2,3]
   * 	}
   * }
   */
  function exportData() {
    var output = $("#output");
    var months = $(".month");
    var month_start_id = parseInt(months.get(0).id.split("_")[1]);
    var month_last_id = month_start_id + months.size() - 1;
    
    var data = {};
    
    for(var i = month_start_id; i <= month_last_id; i++) {
    	var worker = $("#month_"+i+" tr.worker");
    	var worker_data = {};
    	
    	for(var w = 0; w < worker.size();w++) {
    		var days = new Array();
    		var current = worker.get(w);
    		var worker_days = $(current).find(".day");
    		for(var d = 0;w < worker_days.size();d++) {
    			if($(worker_days).get(d).text() == MARKER)
    				days.push(d+1);
    		}
    		
    		worker_data[w.toString()] = days;
    	}
    	
    	data[i.toString()] = worker_data;
    }
    
    console.log(data);
    
    output.text(month_start_id + " "+ month_last_id);
  }

  $(document).ready(function(){
    $(".schichtplan td.day").click(function () { 
      if($(this).text() == MARKER) {
    	  $(this).text(MARKER_NO);
      } else {
    	  $(this).text(MARKER);
      }
    }).hover(function() {
    	$(this).css("background-color","#91b8fe");
    	$(this).css("cursor","pointer");
    }, function() {
    	$(this).css("background-color","");
    	$(this).css("cursor","default");
    });
    
    $("#data_save").click(function () {exportData();});
  });
