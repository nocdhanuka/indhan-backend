@include('include.frontpage_headers')
        
        
        
        
        
        
        
         
         <div class="section2">
            <div class="container">
              <div class="row">
                   <div class="part-main1">           
                        <div class="col-lg-12">
							<div class="upgradepart">
							<div class="error404">
							<img src="{{asset('assets/images/404page.png')}}">
							<h2>Oops ! Something went wrong Go back to <a href="{{URL::to('/')}}"> Home</a> Or try later.</h2><br>
							
							</div>


								
							




							
							
							
							
							
							



							
								
							</div>
						</div>
        
                   </div>               
               </div>  
            </div>
         </div>
         
         
         
         
         <!-- Footer--> 
  @include('include.footer')
         <!-- End Footer -->
      </div>
      <!-- Bootstrap core JavaScript
         ================================================== -->
      <!-- Placed at the end of the document so the pages load faster -->
       <script src="{{asset('assets/js/bootstrap.js')}}"></script>
      <!-- IE10 viewport hack for Surface/desktop Windows 8 bug -->
      <script src="{{asset('assets/js/ie10-viewport-bug-workaround.js')}}"></script>
      <script type="text/javascript">
         $(function() {
         	$("#slider").blissSlider({
         		auto: 1,
             			transitionTime: 500,
             			timeBetweenSlides: 4000
         	});
         });
		 
		 
		 $('#datetimepicker').datetimepicker({
    format: 'yyyy-mm-dd hh:ii'
});
		 
		 
      </script>
      
      <script type="text/javascript">
	  $(document).ready(function(){
								 
			 $(window).scroll(function(){
														  
				  var e= $(window).scrollTop();
								
					if ( e > 50){
							
						 $(".nav_main").addClass("short_menu")
					
					}else{
						  $(".nav_main").removeClass("short_menu")
										
					}
			});
			
		}); 
					
							  	
							  
						
      </script>
   </body>
</html>

