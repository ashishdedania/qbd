

<p>{{ session('status') }}</p>

<form method="POST" action="{{ url("import") }}" enctype="multipart/form-data">
{{ csrf_field() }}

<div class="form-group{{ $errors->has('file') ? ' has-error' : '' }}">
    <label for="file" class="control-label">CSV file to import</label>
    
    <input id="file" type="file" class="form-control" name="file" required>

    @if ($errors->has('file'))
        <span class="help-block">
        <strong>{{ $errors->first('file') }}</strong>
        </span>
    @endif
    
</div>
 
<p><button type="submit" class="btn btn-success" name="submit"><i class="fa fa-check"></i> Submit</button></p>

</form>





<br />
        <input type="button" onclick="startTask();"  value="Start Long Task" />
        <input type="button" onclick="stopTask();"  value="Stop Task" />
        <br />
        <br />

        <p>Results</p>
        <br />
        <div id="results" style="border:1px solid #000; padding:10px; width:300px; height:250px; overflow:auto; background:#eee;"></div>
        <br />

        <progress id='progressor' value="0" max='100' style=""></progress>
        <span id="percentage" style="text-align:right; display:block; margin-top:5px;">0</span>


<br/>

<form name="frmUpdload" id="frmUpdload" method="POST" enctype="multipart/form-data">
	<input type="file" name="csvFile" class="csvFile" />
	<input type="submit" name="btnSubmit" value="Submit" />
</form>


















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
			formData.append('fileupload',$( '.csvFile' )[0].files[0], file_name);
			
			$.ajax({
				url: {{ url("upload") }},
				 data: formData,
				 processData: false,
				 contentType: false,
				 type: 'POST',
				 success: function(data){
					alert(data);
				 }
			});
		}
		$('#frmUpdload')[0].reset();
		return false;
	});
});



/*let evtSource = new EventSource("/getEventStream", {withCredentials: true});

evtSource.onmessage = function (e) {
 let data = JSON.parse(e.data);
 console.log(data);
};*/


function startTask() {
        es = new EventSource("/getEventStream", {withCredentials: true});

        //a message is received
        es.addEventListener('message', function(e) {
              var result = JSON.parse( e.data );

              

              if(e.lastEventId == 'CLOSE') {
                  addLog('Received CLOSE closing');
                  es.close();
                  var pBar = document.getElementById('progressor');
                  pBar.value = pBar.max; //max out the progress bar
              }
              else {
                  var pBar = document.getElementById('progressor');
                  pBar.value = result.progress;
                  var perc = document.getElementById('percentage');
                  perc.innerHTML   = result.progress  + "%";
                  perc.style.width = (Math.floor(pBar.clientWidth * (result.progress/100)) + 15) + 'px';
              }
        });

        es.addEventListener('error', function(e) {
            addLog('Error occurred');
            es.close();
          });
      }
      function stopTask() {
          es.close();
          addLog('Interrupted');
       }

        function addLog(message) {
          var r = document.getElementById('results');
          r.innerHTML += message + '<br>';
          r.scrollTop = r.scrollHeight;
      }


</script>