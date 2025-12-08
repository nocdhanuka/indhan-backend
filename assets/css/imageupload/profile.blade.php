<?php //echo "<pre>"; \Symfony\Component\HttpFoundation\Session\Session::all(); exit;  ?>
<?php $end = end((explode('/', URL::previous()))); ?>
@include('include.headerprofile')

<!--<script src="{{ asset('assets/js/bootstrap.min.js') }}"></script>-->
<link href="{{ asset ('assets/css/imageupload/main.css') }}" rel="stylesheet" type="text/css" />
<link href="{{ asset ('assets/css/imageupload/jquery.Jcrop.min.css') }}" rel="stylesheet" type="text/css" />
<meta name="_token" content="{!! csrf_token() !!}"/>

<script type="text/javascript">
$(document).on('click', '.browse', function(){
  var file = $(this).parent().parent().parent().find('.file');
  file.trigger('click');
});
$(document).on('change', '.file', function(){
  $(this).parent().find('.form-control').val($(this).val().replace(/C:\\fakepath\\/i, ''));
});    

</script>


<section id="regform-new-design">
    <div class="container">
        <div class="row">
            <div class="col-md-8">
                <div class="regform-mainitem row">
                    
                    @if(Session::has('message'))
                    <div class="col-md-12" style="margin-top:20px;">
                    <p class="alert {{ Session::get('alert-class', 'alert-info') }}">{{ Session::get('message') }}</p>
                    </div>
                    @endif
                    
                    
                    <div class="col-md-4">                        
                        <div class="upload-section">
                        <?php
                        if($user_data[0]->img_status=='1') {
                            $user_profile_image = $user_data[0]->path;
                        }
                        else {
                            $user_profile_image = 'assets/images/default_profile.jpg';
                        }
                        ?>
                        <img src="{{ asset($user_profile_image) }}" alt="Avtr Image">
                                    
                            <p data-toggle="modal" data-target="#myModal" class="paraloginlink">
                                <i class="fa fa-camera" aria-hidden="true"></i><a class="loginlink" href="#">image upload</a>
                            </p>

                            </div>
                            <div class="note">
                            <p>Note: Choose Image Dimension: 512x512 Image display only after admin approval</p>
                            </div>
                            
                            <div class="navbar-collapse collapse" id="navbar" aria-expanded="false" >
                                <div id="myModal" class="modal fade" tabindex="-1" role="dialog" aria-labelledby="myModalLabel" aria-hidden="true">
                                    <div class="modal-dialog">
                                        <div class="modal-content">
                                            <div class="modal-body">
                                                <div class="mybkngwte2">
                                                    <div class="mybkngwte_bg">
                                                        <!-- upload form -->
                                                        <form id="upload_form" enctype="multipart/form-data" method="post"  onSubmit="return checkForm()" action={{('upload-image-file')}} accept-charset="UTF-8">
                                                            <input type="hidden" name="_token" value="{{ csrf_token() }}">
                                                            <!-- hidden crop params -->
                                                            <input type="hidden" id="x1" name="x1" />
                                                            <input type="hidden" id="y1" name="y1" />
                                                            <input type="hidden" id="x2" name="x2" />
                                                            <input type="hidden" id="y2" name="y2" />


                                                            <input type="file" name="img[]" class="file loginlink image_class" id="image_file" onChange="fileSelectHandler()"/>

                                                            <div class="input-group col-xs-8">
                                                              <input type="text" class="form-control input-lg" disabled placeholder="Upload Image">
                                                              <span class="input-group-btn">
                                                                <button class="browse btn btn-primary input-lg" type="button"> Browse</button>
                                                              </span>
                                                            </div>                                                           

                                                            <!--
                                                            <div>
                                                                <input type="file" name="image_file" class="loginlink image_class" id="image_file" onChange="fileSelectHandler()"/> 
                                                            </div>
                                                            -->

                                                            <!-- <div class="errors"></div>-->
                                                            <div class="step2">
                                                                <img id="preview" />
                                                                <div class="info">
                                                                    <label class="info1">File size</label> <input type="text" class="info1" id="filesize" name="filesize" />
                                                                    <label class="info1">Type</label> <input type="text"  class="info1" id="filetype" name="filetype" />
                                                                    <label class="info1">Image dimension</label> <input type="text"  class="info1" id="filedim" name="filedim" />
                                                                    <label class="info1">W</label> <input type="text" class="info1" id="w" name="w" />
                                                                    <label class="info1">H</label> <input type="text" class="info1" id="h" name="h" />
                                                                    <input type="submit" class="img-upload" value="Upload"/>
                                                                    <!--<input type="submit" href="{{ URL::to('user/profileview') }}" class="cncelbuton" value="Cancel"/>-->
                                                                </div>
                                                                <div class="error"></div>
                                                            </div> 
                                                        </form>
                                                    </div>
                                                </div>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        
                    </div>
                    <div class="col-md-8 regform-mainitem-content">
                        <h4><span>{{ucfirst($user_data[0]->name)}}</span> </h4>
                        <div class="regform-mainitem-deatils">
                            <label class="control-label">Age</label>
                            <span class="text-label"><?php if($user_data) echo  ($user_data[0]->dob!=''?date_diff(date_create($user_data[0]->dob), date_create('today'))->y.' years':'');?></span>
                        </div>
                        <div class="regform-mainitem-deatils">
                            <label class="control-label">Gender</label>
                            <span class="text-label"><?php if($user_data) echo  ($user_data[0]->gender!=''?ucfirst($user_data[0]->gender):'');?></span>
                        </div>
                        <div class="regform-mainitem-deatils">
                            <label class="control-label">Email</label>
                            <span class="text-label"><?php if($user_data) echo ($user_data[0]->email); ?></span>
                        </div>
                        <div class="regform-mainitem-deatils">
                            <label class="control-label">Location</label>
                            <span class="text-label"><?php if($country_data) echo $country_data; if($state_data) echo ", ".$state_data; ?></span>
                        </div>
                        <div class="regform-mainitem-deatils">
                            <label class="control-label">Qualification</label>
                            <span class="text-label">{{$highest_qualification_data}}</span>
                        </div>
<!--                        <div class="regform-mainitem-deatils">
                            <label class="control-label">Profession</label>
                            <span class="text-label">{{$occupation_data}}</span>
                        </div>-->

                    </div>
                </div>


                <div class="profile">

                    <h2>Profile: <a href="#" class="edit">Edit</a></h2>

                    <div class="row">
                        <div class="col-lg-6 col-md-6 col-sm-6 col-xs-12">

                            <h3>Profile Basics</h3>

                            <div class="profile-box">   

                                <ul>
                                <li>
                                    <span class="left">Profile Posted By:</span>

                                    <span class="right">Self</span>
                                </li>
                                <li>
                                    <span class="left">Residency Status:</span>

                                    <span class="right">Citizen</span>
                                </li>
                                <li>
                                    <span class="left">Ethnic Origin:</span>

                                    <span class="right">prefer not to say</span>
                                </li>
                                <li>
                                    <span class="left">My Religious History:</span>

                                    <span class="right">Muslim Since Birth</span>
                                </li>
                                <li>
                                    <span class="left">Living with in-laws:</span>

                                    <span class="right">Definitely Consider</span>
                                </li>
                                </ul>

                            </div>

                        </div>
                        <div class="col-lg-6 col-md-6 col-sm-6 col-xs-12">

                            <h3>Lifestyle/Current status</h3>

                            <div class="profile-box">   

                                <ul>
                                <li>
                                    <span class="left">Pray:</span>

                                    <span class="right">Always pray</span>
                                </li>
                                <li>
                                    <span class="left">Sect:</span>

                                    <span class="right">Sunni Muslim</span>
                                </li>
                                <li>
                                    <span class="left">Marital Status:</span>

                                    <span class="right">Never Married</span>
                                </li>
                                <li>
                                    <span class="left">Children:</span>

                                    <span class="right">None</span>
                                </li>
                                <li>
                                    <span class="left">Like to have children:</span>

                                    <span class="right">Yes</span>
                                </li>
                                <li>
                                    <span class="left">Languages:</span>

                                    <span class="right">English</span>
                                </li>
                                </ul>

                            </div>
                        
                        </div>
                    </div>
                    <div class="clearfix">&nbsp;</div>
                    <div class="row">
                        <div class="col-lg-6 col-md-6 col-sm-6 col-xs-12">

                            <h3>Appearance</h3>

                            <div class="profile-box">   

                                <ul>
                                <li>
                                    <span class="left">Hair Colour:</span>

                                    <span class="right">prefer not to say</span>
                                </li>
                                <li>
                                    <span class="left">Body Type:</span>

                                    <span class="right">prefer not to say</span>
                                </li>
                                <li>
                                    <span class="left">Observes Hijab:</span>

                                    <span class="right">Always</span>
                                </li>
                                <li>
                                    <span class="left">Height:</span>

                                    <span class="right">prefer not to say</span>
                                </li>
                                </ul>

                            </div>

                        </div>
                        <div class="col-lg-6 col-md-6 col-sm-6 col-xs-12">

                            <h3>Education/Career</h3>

                            <div class="profile-box">   

                                <ul>
                                <li>
                                    <span class="left">Profession:</span>

                                    <span class="right">Accountant</span>
                                </li>
                                <li>
                                    <span class="left">Highest Qualification:</span>

                                    <span class="right">prefer not to say</span>
                                </li>
                                </ul>

                            </div>
                        
                        </div>
                    </div>
                </div>
                <div class="profile look">

                    <h2>Looking for: <a href="#" class="edit">Edit</a></h2>

                    <div class="row">
                        <div class="col-lg-6 col-md-6 col-sm-6 col-xs-12">

                            <h3>Looking in</h3>

                            <div class="profile-box">   

                                <ul>
                                <li>
                                    <span class="left">India</span>
                                </li>
                                </ul>

                            </div>

                        </div>
                        <div class="col-lg-6 col-md-6 col-sm-6 col-xs-12">

                            <h3>Age</h3>

                            <div class="profile-box">   

                                <ul>
                                <li>
                                    <span class="left">From 24 to 36 years old</span>
                                </li>                                
                                </ul>

                            </div>
                        
                        </div>
                    </div>
                    <div class="clearfix">&nbsp;</div>
                    <div class="row">
                        <div class="col-lg-6 col-md-6 col-sm-6 col-xs-12">

                            <h3>Profile Basics</h3>

                            <div class="profile-box">   

                                <ul>
                                <li>
                                    <span class="left">Residency Status:</span>

                                    <span class="right">don't mind</span>
                                </li>
                                <li>
                                    <span class="left">Ethnic Origin:</span>

                                    <span class="right">don't mind</span>
                                </li>
                                <li>
                                    <span class="left">Religious History:</span>

                                    <span class="right">don't mind</span>
                                </li>
                                </ul>

                            </div>

                        </div>
                        <div class="col-lg-6 col-md-6 col-sm-6 col-xs-12">

                            <h3>Lifestyle/Current status</h3>

                            <div class="profile-box">   

                                <ul>
                                <li>
                                    <span class="left">Pray:</span>

                                    <span class="right"><span class="edit">Always pray, Sometimes miss Fajr and make Qadah Salah, Rarely miss a prayer and make Qadah Salah, Sometimes pray, Only pray before exams, Eid Salah only, Intend to start praying, Don't pray</span></span>
                                </li>
                                <li>
                                    <span class="left">What Sect are you looking for?:</span>

                                    <span class="right">don't mind</span>
                                </li>
                                <li>
                                    <span class="left">Marital Status:</span>

                                    <span class="right">don't mind</span>
                                </li>
                                <li>
                                    <span class="left">Has Children:</span>

                                    <span class="right">don't mind</span>
                                </li>
                                <li>
                                    <span class="left">Like to have children:</span>

                                    <span class="right">don't mind</span>
                                </li>
                                </ul>

                            </div>
                        
                        </div>
                    </div>
                    <div class="clearfix">&nbsp;</div>
                    <div class="row">
                        <div class="col-lg-6 col-md-6 col-sm-6 col-xs-12">

                            <h3>Appearance</h3>

                            <div class="profile-box">                               
    
                                <ul>
                                <li>
                                    <span class="left">Beard:</span>

                                    <span class="right">No, Yes</span>
                                </li>
                                <li>
                                    <span class="left">Body Type:</span>

                                    <span class="right">don't mind</span>
                                </li>
                                <li>
                                    <span class="left">Height:</span>

                                    <span class="right">don't mind</span>
                                </li>
                                </ul>

                            </div>

                        </div>
                        <div class="col-lg-6 col-md-6 col-sm-6 col-xs-12">

                            <h3>Education/Career</h3>

                            <div class="profile-box">   

                                <ul>
                                <li>
                                    <span class="left">Profession:</span>

                                    <span class="right">don't mind</span>
                                </li>
                                <li>
                                    <span class="left">Minimum Qualification:</span>

                                    <span class="right">don't mind</span>
                                </li>
                                </ul>

                            </div>
                        
                        </div>
                    </div>

                </div>

                <div class="profile about">

                    <h2>About Us: <a href="#" class="edit">Edit</a></h2>

                    <div class="row">


                        <div class="col-lg-12">

                            <div class="profile-box">   

                                <ul>
                                <li>
                                    <span><strong>Tell us more about yourself...</strong></span>

                                    <span>ddcfdgrtgtrg</span>
                                </li>
                                <li>
                                    <span><strong>Tell us more about what you are looking In your ideal marriage partner</strong></span>

                                    <span>uikiukuijkyu</span>
                                </li>
                                </ul>

                            </div>

                        </div>




                    </div>

                </div>
                <div class="profile gallery">

                    <h2>Photos: <a href="#" class="edit">Edit</a></h2>

                    <div class="row">
                        <?php
                        foreach($my_photo_data as $imageData) { ?>
                            <div class="col-md-3">
                                <img src="{{ asset($imageData->image_name) }}" class="accord-img" alt="Image">
                            </div>
                            <?php
                        }
                        ?>
                    </div>
                                        
                </div>


                <div class="regform-mainitem none row">
                    <div class="panel-group" id="accordion1">
                        <div class="panel panel-default">
                            <div class="panel-heading">
                                <h4 class="panel-title">
                                    <a data-toggle="collapse" data-parent="#accordion1" href="#collapse1a" class="<?php if($end=='basicdetails' || $end=='aboutmedetails') echo ""; else echo "collapsed"; ?>" aria-expanded="<?php if($end=='basicdetails' || $end=='aboutmedetails') echo "true"; else echo "false"; ?>">Basic Details<span class="q">i</span></a>
                                </h4>
                            </div>
                            <div id="collapse1a" class="panel-collapse <?php if($end=='basicdetails' || $end=='aboutmedetails') echo "collapse in"; else echo "collapse"; ?>" aria-expanded="<?php if($end=='basicdetails' || $end=='aboutmedetails') echo "true"; else echo "false"; ?>" style="<?php if($end=='basicdetails' || $end=='aboutmedetails') echo ""; else echo "height: 0px;"; ?>">
                                <div class="panel-body">									
                                    <h3>Basic Details</h3>
                                    <p>Follow these easy steps to fill in your profile. This will help you make the best use of Guurso Matrimony.</p>
                                    <hr>
                                    <h4><a href="basicdetails" class="edit_h4">Edit</a>Basic Details...</h4>
                                    <div class="form-field-cont col-lg-12 col-md-12 col-sm-12">
                                        <div class="row">
                                            <div class="form-group col-lg-6 col-md-6 col-sm-12">
                                                <label class="control-label">Describe Yourself</label>
                                                <span class="text-label">
                                                    <?php
                                                    if($basic_detail_data)
                                                    echo ($basic_detail_data[0]->adj_1!=''?($basic_detail_data[0]->adj_2!=''?($basic_detail_data[0]->adj_3!=''?ucfirst($basic_detail_data[0]->adj_1).', '.ucfirst($basic_detail_data[0]->adj_2).', '.ucfirst($basic_detail_data[0]->adj_3):''):''):'');
                                                    ?>
                                                </span>
                                            </div>
                                            <div class="form-group col-lg-6 col-md-6 col-sm-12">
                                                <label class="control-label">Availability status</label>
                                                <span class="text-label">
                                                    <?php
                                                    if($basic_detail_data) {
                                                        switch($basic_detail_data[0]->availstatus) {
                                                            CASE ''             : echo 'rather not say'; break;
                                                            CASE 'available'    : echo 'I am currently available for communication'; break;
                                                            CASE 'notavail'     : echo 'I am currently not available for communication'; break;
                                                            CASE 'meeting'      : echo 'I am currently meeting another member'; break;
                                                            default             : echo 'rather not say'; 
                                                        }
                                                    }
                                                    ?>
                                                </span>
                                            </div>
                                            <div class="form-group col-lg-6 col-md-6 col-sm-12">
                                                <label class="control-label">Country</label>
                                                <span class="text-label">{{$country_data}}</span>
                                            </div>
                                            <div class="form-group col-lg-6 col-md-6 col-sm-12">
                                                <label class="control-label">State</label>
                                                <span class="text-label">{{$state_data}}</span>
                                            </div>
                                            <div class="form-group col-lg-6 col-md-6 col-sm-12">
                                                <label class="control-label">City</label>
                                                <span class="text-label">{{$city_data}}</span>
                                            </div>
                                            <div class="form-group col-lg-6 col-md-6 col-sm-12">
                                                <label class="control-label">Languages Spoken</label>
                                                <span class="text-label red">
                                                    <?php
                                                    $langArray = array();
                                                    //if($language_data) {
                                                        foreach($language_data as $languageData) {
                                                            $langArray[] = $languageData[0]->language;
                                                        }
                                                        echo implode(', ', $langArray);
                                                    //}
                                                    ?>
                                                </span>
                                            </div>
                                            <div class="form-group col-lg-6 col-md-6 col-sm-12">
                                                <label class="control-label">Profile Created By</label>
                                                <span class="text-label">{{$profile_created_by_data}}</span>
                                            </div>                                            
                                            <div class="form-group col-lg-6 col-md-6 col-sm-12">
                                                <label class="control-label">Residency Status</label>
                                                <span class="text-label">{{$residency_data}}</span>
                                            </div>
                                            <div class="form-group col-lg-6 col-md-6 col-sm-12">
                                                <label class="control-label">What is your Ethnic Origin?</label>
                                                <span class="text-label">{{$ethnic_origin_data}}</span>
                                            </div>
                                            <div class="form-group col-lg-6 col-md-6 col-sm-12">
                                                <label class="control-label">Religious History</label>
                                                <span class="text-label">
                                                    <?php
                                                    if($basic_detail_data) {
                                                        switch($basic_detail_data[0]->religion_history) {
                                                            CASE ''             : echo 'rather not say'; break;
                                                            CASE 'MSB '         : echo 'Muslim Since Birth'; break;
                                                            CASE 'RM'           : echo 'Revert Muslim'; break;
                                                            default             : echo 'rather not say'; 
                                                        }
                                                    }
                                                    ?>
                                                </span>
                                            </div>
                                            <div class="form-group col-lg-6 col-md-6 col-sm-12">
                                                <label class="control-label">Living with in-laws</label>
                                                <span class="text-label">{{$in_laws_data}}</span>
                                            </div>
                                            <div class="form-group col-lg-6 col-md-6 col-sm-12">
                                                <label class="control-label">What Sect are you</label>
                                                <span class="text-label">{{$sect_muslim_data}}</span>
                                            </div>
                                            
                                            
                                            <?php if($user_data[0]->gender=='female') { ?>
                                            <div class="form-group col-lg-6 col-md-6 col-sm-12">
                                                <label class="control-label">Do you pray</label>
                                                <span class="text-label">{{$pray_type_data}}&nbsp;</span>
                                            </div>
                                            <?php }
                                            if($user_data[0]->gender=='male') { ?>
                                            <div class="form-group col-lg-6 col-md-6 col-sm-12">
                                                <label class="control-label">Beard</label>
                                                <span class="text-label"><?php if($basic_detail_data) echo ($basic_detail_data[0]->beard=='1'?'Yes':($basic_detail_data[0]->beard=='2'?'No':'')); ?>&nbsp;</span>
                                            </div>
                                            <?php } ?>
                                            
                                            
                                            <div class="form-group col-lg-6 col-md-6 col-sm-12">
                                                <label class="control-label">Marital Status</label>
                                                <span class="text-label">
                                                    <?php
                                                    if($basic_detail_data) {
                                                        switch($basic_detail_data[0]->marital_status) {
                                                            CASE 'widowed'      : echo 'Widowed'; break;
                                                            CASE 'unmarried'    : echo 'Never Married'; break;
                                                            CASE 'divorced'     : echo 'Divorced'; break;
                                                            CASE 'annulled'     : echo 'Annulled'; break;
                                                            CASE 'other'        : echo 'Other'; break;
                                                            default             : echo ''; 
                                                        }
                                                    }
                                                    ?>
                                                </span>
                                            </div>
                                            <div class="form-group col-lg-6 col-md-6 col-sm-12">
                                                <label class="control-label">Do you have Children</label>
                                                <span class="text-label">
                                                    <?php                                    
                                                    if($basic_detail_data) {
                                                        switch($basic_detail_data[0]->have_children) {
                                                            CASE '1'            : echo 'None'; break;
                                                            CASE '2'            : echo 'One'; break;
                                                            CASE '3'            : echo 'Two'; break;
                                                            CASE '4'            : echo 'Three or more'; break;
                                                            default             : echo ''; 
                                                        }
                                                    }
                                                    ?>
                                                </span>
                                            </div>
                                            <div class="form-group col-lg-6 col-md-6 col-sm-12">
                                                <label class="control-label">Like to have Children</label>
                                                <span class="text-label">
                                                    <?php     
                                                    if($basic_detail_data) {                               
                                                        switch($basic_detail_data[0]->like_to_have_children) {
                                                            CASE '1'            : echo 'Yes'; break;
                                                            CASE '2'            : echo 'No'; break;
                                                            default             : echo ''; 
                                                        }
                                                    }
                                                    ?>
                                                </span>
                                            </div>
                                            <div class="form-group col-lg-6 col-md-6 col-sm-12">
                                                <label class="control-label">Body Type</label>
                                                <span class="text-label">{{$body_type_data}}</span>
                                            </div>
                                            <div class="form-group col-lg-6 col-md-6 col-sm-12">
                                                <label class="control-label">Hair Colour</label>
                                                <span class="text-label">{{$hair_colour_data}}</span>
                                            </div>
                                            <div class="form-group col-lg-6 col-md-6 col-sm-12">
                                                <label class="control-label">Do you observe Hijab</label>
                                                <span class="text-label">{{$observe_hijab_data}}</span>
                                            </div>
                                            <div class="form-group col-lg-6 col-md-6 col-sm-12">
                                                <label class="control-label">Height</label>
                                                <span class="text-label">{{$height_data}}</span>
                                            </div>
                                            <div class="form-group col-lg-6 col-md-6 col-sm-12">
                                                <label class="control-label">Current Profession</label>
                                                <span class="text-label">{{$occupation_data}}</span>
                                            </div>
                                            <div class="form-group col-lg-6 col-md-6 col-sm-12">
                                                <label class="control-label">Highest Qualification</label>
                                                <span class="text-label">{{$highest_qualification_data}}</span>
                                            </div>
                                        </div>
                                        
                                        <h4><a href="aboutmedetails" class="edit_h4">Edit</a>Tell us more about yourself...</h4>
                                        <?php if($about_us_data) { if($about_us_data[0]->about_me) { echo $about_us_data[0]->about_me; } else { echo "-----"; } } else { echo "-----"; } ?>
                                        <h4>Tell us more about what you are looking In your ideal marriage partner</h4>
                                        <?php if($about_us_data) { if($about_us_data[0]->about_partner) { echo $about_us_data[0]->about_partner; } else { echo "-----"; } } else { echo "-----"; } ?>
                                    </div>
                                    <div class="clearfix">&nbsp;</div>
                                </div>
                            </div>
                        </div>
                        
                        
                        <div class="panel panel-default">
                            <div class="panel-heading">
                                <h4 class="panel-title">
                                    <a data-toggle="collapse" data-parent="#accordion1" href="#collapse2a" class="<?php if($end=='lookingfordetails') echo ""; else echo "collapsed"; ?>" aria-expanded="<?php if($end=='lookingfordetails') echo "true"; else echo "false"; ?>">Looking For<span class="q">i</span></a>
                                </h4>
                            </div>
                            <div id="collapse2a" class="panel-collapse <?php if($end=='lookingfordetails') echo "collapse in"; else echo "collapse"; ?>" aria-expanded="<?php if($end=='lookingfordetails') echo "true"; else echo "false"; ?>" style="<?php if($end=='lookingfordetails') echo ""; else echo "height: 0px;"; ?>">
                                <div class="panel-body">									
                                    <h3>Looking for</h3>
                                    <p>Fill in basic criteria for your ideal partner.This will help us highlight your potential guurso</p>
                                    <hr>
                                    <h4><a href="lookingfordetails" class="edit_h4">Edit</a>Where i’m looking...</h4>
                                    <div class="form-field-cont col-lg-12 col-md-12 col-sm-12">
                                        <div class="row">
                                            <div class="form-group col-lg-6 col-md-6 col-sm-12">
                                                <label class="control-label">Africa</label>
                                                <span class="text-label"><?php if($africaCountry) $africaCountry = array_filter(array_unique($africaCountry)); echo implode(', ',($africaCountry)); ?></span>
                                            </div>
                                            <div class="form-group col-lg-6 col-md-6 col-sm-12">
                                                <label class="control-label">North America</label>
                                                <span class="text-label"><?php if($northAmericaCountry) $northAmericaCountry = array_filter(array_unique($northAmericaCountry));  echo implode(', ', ($northAmericaCountry)); ?></span>
                                            </div>
                                            <div class="form-group col-lg-6 col-md-6 col-sm-12">
                                                <label class="control-label">Antarctica</label>
                                                <span class="text-label"><?php if($antarcticaCountry) $antarcticaCountry = array_filter(array_unique($antarcticaCountry));  echo implode(', ', ($antarcticaCountry)); ?></span>
                                            </div>
                                            <div class="form-group col-lg-6 col-md-6 col-sm-12">
                                                <label class="control-label">Australasia</label>
                                                <span class="text-label"><?php if($australasiaCountry) $australasiaCountry = array_filter(array_unique($australasiaCountry));  echo implode(', ', ($australasiaCountry)); ?></span>
                                            </div>
                                            <div class="form-group col-lg-6 col-md-6 col-sm-12">
                                                <label class="control-label">Asia</label>
                                                <span class="text-label"><?php if($asiaCountry) $asiaCountry = array_filter(array_unique($asiaCountry));  echo implode(', ', ($asiaCountry)); ?></span>
                                            </div>
                                            <div class="form-group col-lg-6 col-md-6 col-sm-12">
                                                <label class="control-label">South America</label>
                                                <span class="text-label red"><?php if($southAmericaCountry) $southAmericaCountry = array_filter(array_unique($southAmericaCountry));  echo implode(', ', ($southAmericaCountry)); ?></span>
                                            </div>
                                            <div class="form-group col-lg-6 col-md-6 col-sm-12">
                                                <label class="control-label">Europe</label>
                                                <span class="text-label"><?php if($europeCountry) $europeCountry = array_filter(array_unique($europeCountry));   echo implode(', ', ($europeCountry)); ?></span>
                                            </div>
                                        </div>
                                    </div>
                                    <div class="clearfix">&nbsp;</div>
                                    <h4>Personal Details...</h4>
                                    <div class="form-field-cont col-lg-12 col-md-12 col-sm-12">
                                        <div class="row">
                                            <div class="form-group col-lg-6 col-md-6 col-sm-12">
                                                <label class="control-label">Residency Status</label>
                                                <span class="text-label"><?php if($residency) $residency = array_filter(array_unique($residency));   echo implode(', ', ($residency)); ?></span>
                                            </div>

                                            <div class="form-group col-lg-6 col-md-6 col-sm-12">
                                                <label class="control-label">Ethnic Origin</label>
                                                <span class="text-label"><?php if($ethnic) $ethnic = array_filter(array_unique($ethnic));   echo implode(', ', ($ethnic)); ?></span>
                                            </div>

                                            <div class="form-group col-lg-6 col-md-6 col-sm-12">
                                                <label class="control-label">Religious History</label>
                                                <span class="text-label"><?php if($looking_for_data) echo ($looking_for_data[0]->religious_history=='RM'?'Revert Muslim':($looking_for_data[0]->religious_history=='MSB'?'Muslim Since Birth':'')); ?></span>
                                            </div>
                                            <div class="form-group col-lg-6 col-md-6 col-sm-12">
                                                <label class="control-label">Pray</label>
                                                <span class="text-label"><?php if($pray) $pray = array_filter(array_unique($pray));   echo implode(', ', ($pray)); ?></span>
                                            </div>


                                            <div class="form-group col-lg-6 col-md-6 col-sm-12">
                                                <label class="control-label">What Sect are you looking for?</label>
                                                <span class="text-label"><?php if($sectMuslim) $sectMuslim = array_filter(array_unique($sectMuslim));   echo implode(', ', ($sectMuslim)); ?></span>
                                            </div>

                                            <div class="form-group col-lg-6 col-md-6 col-sm-12">
                                                <label class="control-label">Marital Status</label>
                                                <span class="text-label"><?php if($looking_for_data)  echo ucfirst($looking_for_data[0]->marital_status); ?></span>
                                            </div>

                                            <div class="form-group col-lg-6 col-md-6 col-sm-12">
                                                <label class="control-label">Has Children</label>
                                                <span class="text-label"><?php if($looking_for_data)  echo ($looking_for_data[0]->has_children=='1'?'One':($looking_for_data[0]->has_children=='2'?'Two':($looking_for_data[0]->has_children=='3'?'Three or more':''))); ?></span>
                                            </div>

                                            <div class="form-group col-lg-6 col-md-6 col-sm-12">
                                                <label class="control-label">Like to have children</label>
                                                <span class="text-label red"><?php if($looking_for_data)  echo ($looking_for_data[0]->like_children=='1'?'Yes':($looking_for_data[0]->like_children=='2'?'No':'')); ?></span>
                                            </div>

                                            <div class="form-group col-lg-6 col-md-6 col-sm-12">
                                                <label class="control-label">Beard</label>
                                                <span class="text-label"><?php  if($looking_for_data) echo ($looking_for_data[0]->beard=='1'?'Yes':($looking_for_data[0]->beard=='2'?'No':'')); ?></span>
                                            </div>

                                            <div class="form-group col-lg-6 col-md-6 col-sm-12">
                                                <label class="control-label">Age</label>
                                                <span class="text-label"><?php if($looking_for_data)  if ($looking_for_data[0]->ageFrom!='' && $looking_for_data[0]->ageTo!='') echo ($looking_for_data[0]->ageFrom.' Years to '.$looking_for_data[0]->ageTo.' Years'); ?></span>
                                            </div>

                                            <div class="form-group col-lg-6 col-md-6 col-sm-12">
                                                <label class="control-label">Height Range</label>
                                                <span class="text-label"><?php if($heightFrom!='' && $heightTo!='') echo $heightFrom.' to '.$heightTo; ?></span>
                                            </div>

                                            <div class="form-group col-lg-6 col-md-6 col-sm-12">
                                                <label class="control-label">Body Type</label>
                                                <span class="text-label"><?php if($bodyType) $bodyType = array_filter(array_unique($bodyType));   echo implode(', ', ($bodyType)); ?></span>
                                            </div>

                                            <div class="form-group col-lg-6 col-md-6 col-sm-12">
                                                <label class="control-label">Profession</label>
                                                <span class="text-label"><?php if($profession) $profession = array_filter(array_unique($profession));   echo implode(', ', ($profession)); ?></span>
                                            </div>

                                            <div class="form-group col-lg-6 col-md-6 col-sm-12">
                                                <label class="control-label">Minimum Qualification</label>
                                                <span class="text-label"><?php if($min_qualification)   echo $min_qualification; ?></span>
                                            </div>

                                            <div class="form-group col-lg-6 col-md-6 col-sm-12">
                                                <label class="control-label">Attends the following</label>
                                                <span class="text-label"><?php if($institute) $institute = array_filter(array_unique($institute));   echo implode(', ', ($institute)); ?></span>
                                            </div>
                                        </div>
                                    </div>
                                    <div class="clearfix">&nbsp;</div>
                                </div>
                            </div>
                        </div>
                        
                        
<!--                        <div class="panel panel-default">
                            <div class="panel-heading">
                                <h4 class="panel-title">
                                    <a data-toggle="collapse" data-parent="#accordion1" href="#collapse3a" class="collapsed" aria-expanded="false">About Me<span class="q">i</span></a>
                                </h4>
                            </div>
                            <div id="collapse3a" class="panel-collapse collapse" aria-expanded="false" style="height: 0px;">
                                <div class="panel-body">
                                    <h4><a href="aboutmedetails" class="edit_h4">Edit</a>Tell us more about yourself...</h4>
                                    <?php //if($about_us_data) echo $about_us_data[0]->about_me; ?>
                                    <h4>Tell us more about yourself...</h4>
                                    <?php //if($about_us_data) echo $about_us_data[0]->about_partner; ?>
                                </div>
                            </div>
                        </div>-->
                        
                        
                        <div class="panel panel-default">
                            <div class="panel-heading">
                                <h4 class="panel-title">
                                    <a data-toggle="collapse" data-parent="#accordion1" href="#collapse4a" class="collapsed" aria-expanded="false">My Photos<span class="q">i</span></a>
                                </h4>
                            </div>
                            <div id="collapse4a" class="panel-collapse collapse" aria-expanded="false" style="height: 0px;">
                                <div class="panel-body">
                                    <h4><a href="myphotodetails" class="edit_h4">Edit</a>Uploaded Photos</h4>
                                    <div class="row">
                                        <?php
                                        foreach($my_photo_data as $imageData) { ?>
                                            <div class="col-md-3">
                                                <img src="{{ asset($imageData->image_name) }}" class="accord-img" alt="Image">
                                            </div>
                                            <?php
                                        }
                                        ?>
                                    </div> 
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
            <div class="col-md-4 aside">
                <div class="itens-side text-center">

                    <h4>Profile Strength</h4>

                    <div class="progress-bar position" data-percent="40" data-duration="1000" data-color="#ccc,yellow"></div>
         

                    <!--<img src="{{ asset('assets/images/preview.png') }}" class="" alt="Circle">-->
                </div>
                <div class="itens-side text-center">
                    <h4>Advertisement</h4>
                    <img src="{{ asset('assets/images/sidebar-img.png') }}" class="" alt="Circle">
                </div>
            </div>
        </div>
    </div>
</section>


<div id="dtBox"></div>
@include('include.home_footer')


<script src="{{asset('assets/js/bootstrap.js')}}"></script>
<script src="{{ asset('assets/js/imageupload/jquery.Jcrop.min.js') }}"></script>
<script src="{{ asset('assets/js/imageupload/script.js') }}"></script>


<link href="{{ asset ('assets/css/imageupload/jQuery-plugin-progressbar.css') }}" rel="stylesheet" type="text/css" />
<script src="{{ asset('assets/js/imageupload/jQuery-plugin-progressbar.js') }}"></script>

<script>
    $(".nextBtn").click(function () {
        window.location.replace("viewlookingfordetail");
    });

    $(".progress-bar").loading();
    $('input').on('click', function () {
         $(".progress-bar").loading();
    });
</script>

