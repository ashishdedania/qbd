
<progress id='progressor' value="0" max='100' style=""></progress>
<span id="percentage" style="text-align:right; display:block; margin-top:5px;">0</span>

<br/>

<form name="frmUpdload" id="frmUpdload" method="POST" enctype="multipart/form-data">
	<input type="file" name="csvFile" class="csvFile" id="csvFile" />
	<input type="submit" name="btnSubmit" value="Submit" id="btnSubmit" />
</form>


<script src="https://ajax.googleapis.com/ajax/libs/jquery/2.2.0/jquery.min.js"></script>


<script>

$(document).ready(function() {
	$("form#frmUpdload").submit(function() {
		// validation start
		var file_name=$('.csvFile').val();
		var index_dot=file_name.lastIndexOf(".")+1;
		var ext=file_name.substr(index_dot);
		if(file_name=='') {
			alert('Please upload csv');
		}
		else if(!(ext=='csv')) {
			alert('Please upload csv only');
		}	// validation end
		else {
			//formdata object to send file upload data
			var formData = new FormData();
			formData.append('csvFile',$( '.csvFile' )[0].files[0], file_name);
            formData.append('_token',"{{ csrf_token() }}");
            
			
			$.ajax({
				url: '{{ url("upload") }}',
				 data: formData,
				 processData: false,
				 contentType: false,
				 type: 'POST',
				 success: function(data){
					
                    $('#btnSubmit').prop("disabled", true);
                    $('#csvFile').prop("disabled", true);

                    es = new EventSource("/getEventStream?filename="+data, {withCredentials: true});

                    //a message is received
                    es.addEventListener('message', function(e) {
                        var result = JSON.parse( e.data );
                        
                        if(result.total == result.count) { 
                            
                            var pBar = document.getElementById('progressor');
                            pBar.value = pBar.max; //max out the progress bar
                            var perc = document.getElementById('percentage');
                            perc.innerHTML   = result.message;
                            es.close();


                            setTimeout(function () {
                                if(result.errorArray.length > 0)
                                {
                                    var concent = confirm(result.errorArray + " \n These record have issue , should we proceed with only valid records");

                                    if(concent)
                                    {
                                        alert('accept');
                                        pBar.value       = 0;
                                        perc.innerHTML   = '';
                                        startTask(data);
                                    }
                                    
                                }
                                else{
                                    var concent = confirm("Validation done , should we proceed to next step");

                                    if(concent)
                                    {
                                        alert('accept');
                                        pBar.value       = 0;
                                        perc.innerHTML   = '';
                                        startTask(data);
                                    }
                                    
                                }
                            }, 2000);

                        
                        }
                        else {
                            var pBar = document.getElementById('progressor');
                            pBar.value = result.progress;
                            var perc = document.getElementById('percentage');
                            perc.innerHTML   = result.message;
                            perc.style.width = (Math.floor(pBar.clientWidth * (result.progress/100)) + 15) + 'px';
                        }
                    });

                    es.addEventListener('error', function(e) {
                        addLog('Error occurred');
                        es.close();
                    });
                    
				 }
			});
		}
		$('#frmUpdload')[0].reset();
		return false;
	});
});



function startTask(filename) {
    es = new EventSource("/getProcessData?filename="+filename, {withCredentials: true});

    //a message is received
    es.addEventListener('message', function(e) {
                        var result = JSON.parse( e.data );
                        
                        if(result.total == result.count) { 
                            
                            var pBar = document.getElementById('progressor');
                            pBar.value = pBar.max; //max out the progress bar
                            var perc = document.getElementById('percentage');
                            perc.innerHTML   = result.message;
                            es.close();


                            setTimeout(function () {
                                alert ('data processed');
                                $('#btnSubmit').prop("disabled", false);
                                $('#csvFile').prop("disabled", false);
                            }, 2000);

                        
                        }
                        else {
                            var pBar = document.getElementById('progressor');
                            pBar.value = result.progress;
                            var perc = document.getElementById('percentage');
                            perc.innerHTML   = result.message;
                            perc.style.width = (Math.floor(pBar.clientWidth * (result.progress/100)) + 15) + 'px';
                        }
                    });


    es.addEventListener('error', function(e) {
        alert('Error occurred');
        es.close();
        });
}


</script>