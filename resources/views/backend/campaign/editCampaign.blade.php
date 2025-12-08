@include('include.admin.backend_header')
<!-- Left side column. contains the logo and sidebar -->
@include('include.admin.backend_sidebar')



<!-- Content Wrapper. Contains page content -->
<div class="content-wrapper">
    <!-- Content Header (Page header) -->
    <section class="content-header">
        <h1>Edit Campaign</h1>
        <ol class="breadcrumb">
            <li><a href="{{URL::to('admin/dashboard')}}"><i class="fa fa-dashboard"></i> Home</a></li>
            <li class="active">Edit Campaign</li>
        </ol>
    </section>
    <!-- Main content -->
    <section class="content">
        <div class="row">
            <div class="col-xs-12">
                <div class="box">
                    <!-- /.box-header -->
                    <div class="box-body">
                        <div class="col-md-12">
                            <div id="optionModal" class="modal">
                                <!-- Modal content -->
                                <div class="modal-content">
                                    <div class="modal-header">
                                        <span id="optionModelClose" class="close">&times;</span>
                                        <h4>Optional</h4>
                                    </div>
                                    <form action="#" method="post">
                                    <div class="modal-body">
                                            <table id="optiontable" class="table table-bordered table-striped datatable">
                                            <thead>
                                                <tr>
                                                    <th>Options</th>
                                                    <th>Proceed To</th>
                                                    <th>Actions</th>
                                                </tr>
                                                </thead>
                                            <tbody>
                                                <!-- <tr>
                                                    <td><input name="option_value[1]" class="form-control optionValues" type="text"/></td>
                                                    <td><input name="proceed_To[1]" class="form-control proceed_To" type="text" /></td>
                                                    <td><input class="btn btn-danger btndeleterow" value="X" type="button" /></td>
                                                </tr> -->
                                            </tbody> 
                                            </table>
                                            <input class="btn btn-primary" id="insert-more-option" type="button" value="Add New Option" />
                                        </div>
                                        <div class="modal-footer">
                                            <input type="hidden" name="_token" value="{{ csrf_token() }}" />
                                            <button class="btn btn-primary" type="button" id="optionSave" type="button">Save</button>
                                        </div>
                                    </form>
                                </div>
                            </div>
                            <div id="textModal" class="modal">
                                <!-- Modal content -->
                                <div class="modal-content">
                                    <div class="modal-header">
                                        <span id="textModelClose" class="close">&times;</span>
                                        <h4>Text</h4>
                                    </div>
                                    <div class="modal-body">
                                        <!-- <div class="form-group col-xs-6"> -->
                                        <label for="name">Enter Text</label>
                                        <input class="form-control" id="optiontext" type="text" name="name">
                                    </div>
                                    <!-- </div> -->
                                    <div class="modal-footer">
                                        <input class="btn btn-primary" id="textSave" type="button" value="Save" />
                                    </div>
                                </div>
                            </div>
                            <div class="panel with-nav-tabs panel-default">
                                <div class="panel-heading">
                                    <ul class="nav nav-tabs">
                                        <li class="active"><a href="#tab1default" data-toggle="tab">Step 1</a></li>
                                        <li><a href="#tab2default" data-toggle="tab">Step 2</a></li>
                                        <!-- <li><a href="#tab3default" data-toggle="tab">Step 3</a></li> -->

                                    </ul>
                                </div>
                                <div class="panel-body">
                                    <div class="tab-content">
                                        <div class="tab-pane fade in active" id="tab1default">
                                            <form action="{{url('admin/campaign/editCampaignStep1')}}" method="post">
                                                <?php foreach ($data1  as $dataValue1) { ?>
                                                    <div class="form-group col-xs-6">
                                                        <div class="form-group ">
                                                            <label for="name">Campaign Type</label>
                                                            <select class="form-control" name="campType" id="campType">
                                                                @if($dataValue1->campaign_type_id == 1)
                                                                <option selected value="1">HR</option>
                                                                @else
                                                                <option selected value="2">BDO</option>
                                                                @endif
                                                                <option value="2">BDO</option>
                                                            </select>
                                                        </div>
                                                        <div class="form-group">
                                                            <label for="name">Campaign Name</label>
                                                            <input class="form-control frm_txt" value="{{$dataValue1->campaign_id}}" id="campaign_id[]" required type="hidden" name="campaign_id">
                                                            <input class="form-control frm_txt" value="{{$dataValue1->campaign_name}}" id="name" required type="text" name="name">
                                                        </div>
                                                        <div class="form-group">
                                                            <label for="email">Mandatory</label>
                                                            <div class="form-group">
                                                                <input type="radio" id="yes" name="mandatory" value="1" {{ $dataValue1->camp_mandatory == '1' ? 'checked' : '' }}>
                                                                <label for="yes">Yes</label>
                                                                <input style="margin-left: 10px;" {{ $dataValue1->camp_mandatory == '2' ? 'checked' : '' }} type="radio" id="no" name="mandatory" value="2">
                                                                <label for="no">No</label>
                                                            </div>
                                                        </div>
                                                        <div class="form-group">
                                                            <label for="email">Push Notification</label>
                                                            <div class="form-group">
                                                                <input type="radio" id="yes_push" name="push_notify" value="1" {{ $dataValue1->camp_push_notify == '1' ? 'checked' : '' }}>
                                                                <label for="yes_push">Yes</label>
                                                                <input style="margin-left: 10px;" type="radio" id="no_push" name="push_notify" value="2" {{ $dataValue1->camp_push_notify == '2' ? 'checked' : '' }}>
                                                                <label for="no_push">No</label>
                                                            </div>
                                                        </div>
                                                        <div class="form-group">
                                                        <label for="recurring">Is Recurring</label>
                                                        <div class="form-group">
                                                            <input type="radio" id="yes_is_recurring" name="is_recurring" value="0" {{ $dataValue1->rec_flag == '0' ? 'checked' : '' }}>
                                                            <label for="yes_is_recurring">Yes</label>
                                                            <input style="margin-left: 10px;" type="radio" id="no_is_recurring" name="is_recurring" value="1" {{ $dataValue1->rec_flag == '1' ? 'checked' : '' }}>
                                                            <label for="no_is_recurring">No</label>
                                                        </div>
                                                    </div>
                                                        <div class="form-group">
                                                            <label for="email">Date of Expiry</label>
                                                            <input value="{{ $dataValue1->date_of_expire}}" class="form-control" required type="date" id="expiry_date" name="expiry_date">
                                                            <!-- <input class="form-control" id="email" type="email" name="Email"> -->
                                                        </div>
                                                        <div class="form-group">
                                                            <input type="hidden" name="_token" value="{{ csrf_token() }}" />
                                                            <input class="btn btn-primary" type="button" value="Cancel" />
                                                            <input class="btn btn-primary" type="submit" value="Save/Next" />
                                                        </div>
                                                    </div>
                                                <?php } ?>
                                            </form>
                                        </div>
                                        <div class="tab-pane fade" id="tab2default">
                                                <table id="mytable" name="mytable" class="table datatable" width="100%">
                                                    <thead>
                                                        <tr>
                                                            <th>Type</th>
                                                            <th>Sr. No.</th>
                                                            <th>Question</th>
                                                            <th>Mandatory</th>
                                                            <th>Options</th>
                                                            <th>Proceed To</th>
                                                            <th>Action</th>
                                                        </tr>
                                                    </thead>

                                                    <tbody>
                                                        <?php
                                                                                                                
                                                        if ($result) {
                                                            $i=1;
                                                            $myJSVar = json_encode($data2);
                                                            foreach ($result as $dataValue2) {
                                                                if ($dataValue2["question_type"] == 0 || $dataValue2["question_type"] == 2) {
                                                                    ?>
                                                        <input class="form-control frm_txt" value="{{$dataValue2['campaign_id']}}" id="campaign_id[]" required type="hidden" name="campaign_id[]">
                                                            <tr>
                                                                <td>
                                                                    <select class="form-control" name="queType[]" id="queType[0]">
                                                                        <?php if ($dataValue2["question_type"] == 0) { ?>
                                                                            <option selected value="0">New</option>
                                                                        <?php } elseif ($dataValue2["question_type"] == 1) { ?>
                                                                            <option selected value="1">Duplicate</option>
                                                                        <?php } else { ?>
                                                                            <option selected value="2">Submission</option>
                                                                        <?php } ?>
                                                                        <option value="0">New</option>
                                                                        <option value="1">Duplicate</option>
                                                                        <option value="2">Submission</option>
                                                                    </select>
                                                                </td>
                                                                <td><?php echo $i; $i++;?> <input class="form-control frm_txt" value="{{$dataValue2['campaign_que_id']}}" id="campaign_que_id[]" required type="hidden" name="campaign_que_id[]"></td>
                                                                
                                                                <td>
                                                                <form action="{{url('admin/campaign/updateQue')}}" method="post">
                                                                <input class="form-control" id="quetext[0]" type="hidden" name="campaign_que_id[]" value="{{$dataValue2['campaign_que_id']}}">
                                                                <input class="form-control" id="quetext[0]" type="text" name="queText[]" value="<?php echo $dataValue2['question_text']; ?>"/>
                                                                <input type="hidden" name="_token" value="{{ csrf_token() }}" />
                                                                <button class="btn-info" type="submit" style="display: inline-block;margin-bottom: 3px;"><i class="fa fa-pencil"></i></button>
                                                                </form>
                                                                </td>
                                                                <td>
                                                                    <select class="form-control" name="mand[]" id="mand">
                                                                        <?php if ($dataValue2["question_type"] == 0) { ?>
                                                                            <option selected value="0">Yes</option>
                                                                        <?php } else { ?>
                                                                            <option selected value="1">N/A</option>
                                                                        <?php } ?>
                                                                        <option value="0">Yes</option>
                                                                        <option value="1">N/A</option>
                                                                    </select>
                                                                </td>
                                                                <td>
                                                                    <div class="op">
                                                                    <?php 
                                                                     if ($dataValue2["question_type"] == 0) { ?>
                                                                     <?php if ($dataValue2['option_type'] != 1) {?>
                                                                        <select style="display: inline-block;width: 70%;"class="form-control optionSelect" name="option_value[]" id="optionSelect[0]">
                                                                            <option value="2">Text</option>
                                                                        </select>
                                                                        <?php }elseif($dataValue2['option_type'] == 1){
                                                                                
                                                                            ?>
                                                                            <table class="table datatable">
                                                                                <form action="{{url('admin/campaign/updateOption')}}" method="post">
                                                                                <tbody>
                                                                                    <?php if($dataValue2['options']){for ($i=0; $i < count($dataValue2['options']); $i++) {
                                                                                    ?>
                                                                                    <tr>
                                                                                    
                                                                                    <td><input class="form-control" id="quetext[0]" type="hidden" name="optionId[]" value="{{$dataValue2['options'][$i]->option_id}}"><input class="form-control" id="quetext[0]" type="text" name="optionData[]" value="{{$dataValue2['options'][$i]->option_value}}"></td>
                                                                                    <td><input class="form-control" id="quetext[0]" type="text" name="proceedData[]" value="{{$dataValue2['options'][$i]->proceed_to}}"></td>
                                                                                    <td><input type="hidden" name="_token" value="{{ csrf_token() }}" /><button class="btn-info" type="submit" style="display: inline-block;margin-bottom: 3px;"><i class="fa fa-pencil"></i></button></td></tr>
                                                                                    <?php }?>
                                                                                </tbody>
                                                                                </form>
                                                                            </table>
                                                                            <?php }
                                                                        }?>                                                                       
                                                                    <?php } ?>
                                                                    </div>
                                                                </td>
                                                                <?php 
                                                                     if($dataValue2['option_type'] == 2){ ?>
                                                                <td>
                                                                <form action="{{url('admin/campaign/updateTextProceedTo')}}" method="post">
                                                                <input class="form-control" id="quetext[0]" type="hidden" name="campaign_que_id[]" value="{{$dataValue2['campaign_que_id']}}">
                                                                <input class="form-control" id="quetext[0]" type="text" name="proceedData[]" value="{{$dataValue2['proceed_to']}}">
                                                                <input type="hidden" name="_token" value="{{ csrf_token() }}" />
                                                                <button class="btn-info" type="submit" style="display: inline-block;margin-bottom: 3px;"><i class="fa fa-pencil"></i></button>
                                                                </form>
                                                                
                                                                </td> <?php }else{?><td></td><?php }?>
                                                                <td>
                                                                        <form action="{{url('admin/campaign/deleteQue')}}" method="post">
                                                                        <input class="form-control frm_txt" value="{{$dataValue2['campaign_que_id']}}" id="campaign_que_id[]" required type="hidden" name="campaign_que_id[]">
                                                                        <input type="hidden" name="_token" value="{{ csrf_token() }}" />
                                                                        <button class="btn btn-danger"><i class="fa fa-trash"></i></button>
                                                                        </form>
                                                                    
                                                                </td>
                                                            </tr>
                                                        <?php
                                                                }
                                                            }
                                                        }else{  $i=1;?>
                                                        
                                                        <tr>
                                                            <td><select class="form-control" name="queType[]" id="queType[0]">
                                                                    <option selected value="0">New</option>
                                                                    <option value="1">Duplicate</option>
                                                                    <option value="2">Submission</option>
                                                                </select>
                                                            </td>
                                                            <td><?php echo $i;?></td>
                                                            <td><input class="form-control" id="quetext[0]" type="text" name="queText[]" value=""></td>
                                                            <td><select class="form-control" name="mand[]" id="mand">
                                                                    <option selected value="0">Yes</option>
                                                                    <option value="1">N/A</option>
                                                                </select></td>
                                                            <td>
                                                            <!-- <input class="form-control" type="hidden" name="option_value[]" value=""> -->
                                                                <select class="form-control optionSelect" name="option_value[]" id="optionSelect[0]">
                                                                    <option value="">Select Option</option>
                                                                    <option value="1">Optional</option>
                                                                    <option value="2">Text</option>
                                                                </select>
                                                            </td>
                                                            <td><input class="form-control" id="proceedTo[0]" type="text" style="width:100px;" name="proceedTo[]" value=""></td>
                                                            <td><button class="btn btn-danger btndeleterow"><i class="fa fa-trash"></i></button></td>
                                                        </tr>
                                                        <?php $i++;}
                                                        ?>
                                                    </tbody>
                                                </table>
                                                <!-- <input type="hidden" name="_token" value="{{ csrf_token() }}" /> -->
                                                <input class="btn btn-primary" id="insert-more" type="button" value="Add New Question" />
                                                <input class="btn btn-primary" id="insert-submission" type="button" value="Add Submission Row" />
                                                <!-- <input class="btn btn-primary" type="submit" value="Save" /> -->
                                                <!-- <input class="btn btn-primary" id="getRows" type="button" value="Get Rows" /> -->
                                            </form>

                                            <p id="info"></p>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>
                        <div class="success"></div>
                    </div><!-- /.box-body -->
                </div><!-- /.box -->
            </div><!-- /.col -->
        </div><!-- /.row -->
    </section><!-- /.content -->
</div><!-- /.content-wrapper -->

@include('include.admin.footer')

</div>

<script type="text/javascript" src="http://ajax.googleapis.com/ajax/libs/jquery/1.9.1/jquery.min.js"></script>
<script type="text/javascript" src="http://maxcdn.bootstrapcdn.com/bootstrap/3.3.2/js/bootstrap.min.js"></script>
<script>
$(document).ready(function () {
    $('#tabMenu a[href="#{{ old('tab') }}"]').tab('show');

    var tableData = $("#mytable tbody tr");
    getRows();

    function getRows() {
        var table = document.getElementById('mytable');
        var rowCount = table.rows.length;
        var i=0;
        $('#mytable > tbody > tr').each(function(index, tr) { 
            console.log("Index :: "+index);
            tr.setAttribute("id", i);
            console.log(tr);
            i++;
        });
    }
});
    var j = 1;
    $("#insert-more").click(function() {
        var trIndex = $("#mytable > tbody > tr").length;
        var tableData = $("#mytable tbody tr:last td:nth-child(2)")[0].innerHTML;
        var srNo = parseInt(tableData)+1;
        var trindexOnmore = parseInt(trIndex)+1;
        $('#mytable > tbody').append('<tr id="optrow['+trindexOnmore+']"> <td><select class="form-control" name="queType[]" id="queType[' + j + ']"> <option selected value="0">New</option> <option value="1">Duplicate</option> <option value="2">Submission</option> </select> </td> <td >'+srNo+'<input class="form-control" id="srNo[0]" type="hidden" name="srNo['+srNo+']" value="'+srNo+'"/></td> <td><input class="form-control" id="queNumber['+ j +']" type="hidden" name="queNumber[]" value="' + trindexOnmore + '"><input class="form-control" id="quetext[' + j + ']" type="text" name="queText[]" value=""></td> <td><select class="form-control" name="mand[]" id="mand"> <option selected value="0">Yes</option> <option value="1">N/A</option> </select></td> <td> <select onchange="GetSelectedTextValue(this,'+trindexOnmore+')" class="form-control optionSelect" name="option_value[]" id="optionSelect[0]"> <option value="">Select Option</option> <option value="1">Optional</option> <option value="2">Text</option> </select> </td> <td><input class="form-control" id="proceedTo[0]" type="text" style="width:100px;" name="proceedTo[]" value=""></td><td><button class="btn btn-danger btndeleterow"><i class="fa fa-trash"></i></button></td><td><button onclick="getSave(this)" class="btn btn-success savebtn" id="savebtn"><i class="fa fa-check"></i></button></td></tr>');
        j++;
    });

    function getSave(params) {
        var campaign_id = document.querySelector('input[name=campaign_id]').value;
        console.log(campaign_id);

        var type = $("#mytable > tbody > tr:last td:nth-child(1)").find("option:selected").val();
        var question = "";
        if($("#mytable > tbody > tr:last td:nth-child(3)").find("input[type='text']").val()){
            question = $("#mytable > tbody > tr:last td:nth-child(3)").find("input[type='text']").val();
        }else{
            question = $("#mytable tbody tr:last td:nth-child(3)").find("input[type='text']").val();
        }
        var questionNum = "";
        if($("#mytable > tbody > tr:last td:nth-child(3)").find("input[type='hidden']").val()){
            questionNum = $("#mytable > tbody > tr:last td:nth-child(3)").find("input[type='hidden']").val();
        }else{
            questionNum = $("#mytable tbody tr:last td:nth-child(3)").find("input[type='hidden']").val();
        }
        var mand ="";
        if($("#mytable tbody tr:last td:nth-child(4)").find("option:selected").val()){
            mand = $("#mytable tbody tr:last td:nth-child(4)").find("option:selected").val()
        }else{
            mand = 0;
        }
        var proceed = "";
        if($("#mytable tbody tr:last td:nth-child(6)").find("input[type='text']").val()){
            proceed = $("#mytable tbody tr:last td:nth-child(6)").find("input[type='text']").val();
        }else{
            proceed = "";
        }

        var optVal = $('.optionValues1').map((_, el) => el.value).get();
        var proceedTo = $('.proceed_To1').map((_, el) => el.value).get();
        // var questionNum = $('.questionNum').map((_, el) => el.value).get();
        
        console.log(optVal);
        console.log(proceedTo);

        var otableData = $("#otable > tbody > tr").length;
        // console.log(otableData);
        
        var data = {
            'campaign_id':campaign_id,
            'que_type' : type,
            'question' : question,
            'mand' : mand,
            'proceed' : proceed,
            'optVal' : optVal,
            'proceedTo' : proceedTo,
            'questionNum' : questionNum
        }
        console.log(data);
        $.ajax({
        url: "{{url('admin/campaign/editCampaign/saveRow')}}",
        type: "POST",
        headers: {
          'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
        },
        data:{
            'campaign_id':campaign_id,
            'que_type' : type,
            'question' : question,
            'mand' : mand,
            'proceed' : proceed,
            'optVal' : optVal,
            'proceedTo' : proceedTo,
            'questionNum' : questionNum
        },
        dataType: "json",
        success: function(data) {
         console.log(data);         
        }
      });
      document.getElementById('savebtn').style.visibility = 'hidden';
      window.location.reload();
    }
    
    $("#insert-submission").click(function() {
        var tableData = $("#mytable tbody tr:last td:nth-child(2)")[0].innerHTML;
        var srNo = parseInt(tableData)+1;
        // $("#insert-more").hide();
        $('#mytable tbody').append('<tr name="trname[' + j + ']"><td><select class="form-control" name="queType[]" id="queType[' + j + ']"><option value="0">New</option><option value="1">Duplicate</option><option selected value="2">Submission</option></select></td><td>'+srNo+'<input class="form-control" id="srNo[0]" type="hidden" name="srNo['+srNo+']" value="'+srNo+'"/></td><td><input class="form-control" id="quetext[' + j + ']" type="text" name="queText[]" value=""></td><td><button onclick="getSave(this)" class="btn btn-success savebtn" id="savebtn"><i class="fa fa-check"></i></button></td></tr>');
        j++;
    });
    //optiontable
    var i = 1;
    $("#insert-more-option").click(function() {
        
        var tableData = $("#mytable tbody tr:last-child");
        var Cells = tableData;        
        // $('#optiontable tbody').append('<tr class="child"><td><label name="option_type[' + i + ']">Option ' + i + '</label></td><td><input name="option_value[]" id="option_value[' + i + ']" class="form-control optionValues" value="" type="text" /></td><td><input id="delete[' + i + ']" class="btn btn-danger optdeleterow" value="X" type="button" /></td></tr>');
        $('#optiontable tbody').html();
        $("#optiontable tbody").append('<tr><td><input name="option_value[' + i + ']" class="form-control optionValues" type="text"/></td><td><input class="form-control proceed_To" type="text" /></td><td><input id="deleteopt[' + i + ']" class="btn btn-danger optdeleterow" value="X" type="button" /></td></tr>');
        i++;
    });

    $('#optionSave').click(function() {
        
        // var tableData = $("#mytable tbody tr:child(2)")[0].innerHTML;
        var selData = $("#mytable tbody tr:last td:nth-child(5)")[0].innerHTML;
        var queNum = $("#mytable tbody tr:last td:nth-child(3)").find("input[type='hidden']").val();
        var qNum = parseInt(queNum);
        $("#mytable tbody tr:last td:nth-child(6)")[0].innerHTML = '';
        // var srNo = parseInt(tableData);
        var modal = document.getElementById("optionModal");
        modal.style.display = "none";
        var optVal = $('.optionValues').map((_, el) => el.value).get();
        var proceedTo = $('.proceed_To').map((_, el) => el.value).get();

        var optTableID = $("#optiontable tbody tr:last")[0].id;
        
        var table = document.getElementById('mytable');
        var rowCount = table.rows.length;

        var opTable = "<table id='otable' class='table table-bordered table-striped'><input name='option_type[]' id='option_type[' + i + ']' class='form-control' value='1' type='hidden' />";

        for (let i = 0; i < optVal.length; i++) {
            opTable     += "<tr name='optTable["+optTableID+"]' id='tid["+i+"]'><td><input name='que_opt_val[]' id='que_opt_val[" + i + "]' class='form-control questionNum' value='" + optTableID + "' type='hidden' style='width: 80%;display: inline-block;'/><input name='option_value[]' id='option_value[' + i + ']' class='form-control optionValues1' value='" + optVal[i] + "' type='text' style='width: 80%;display: inline-block;'/></td><td><input class='form-control proceed_To1' id='proceedTo[0]' type='text' style='width:100px;' name='proceedTo[]' value="+proceedTo[i]+"></td></tr>";
        }
        
        opTable     += "</table>";
        for (let i = 1; i < rowCount; i++) {
            
            // console.log(table.rows[i].id);
            // console.log(optTableID);

            if(table.rows[i].id == 'optrow['+optTableID+']'){
                //  $("#mytable tbody "+table.rows[i]+" td").length;
                var colCount = table.rows[i].cells.length;
                var values = table.rows[i].cells;
                
                for (let j = 0; j < colCount; j++) {
                    // console.log(values[j][0].find("div"));
                    if(j == 4){
                        values[j].innerHTML = '';
                        values[j].innerHTML = opTable;
                    }
                }
            }
            
        } 
$("#optiontable tbody").html();
    });

</script>

<script>
    function GetSelectedTextValue(ddlFruits,indexId) {
        var selectedText = ddlFruits.options[ddlFruits.selectedIndex].innerHTML;
        var selectedValue = ddlFruits.value;
        console.log(ddlFruits);
        // $('.opt').val(selectedValue);
        // alert("Selected Text: " + selectedText + " Value: " + selectedValue);
        var modal = "";
        if (selectedValue != '') {
            if (selectedValue == 1) {
                modal = document.getElementById("optionModal");
                modal.style.display = "block";
                var optionData = [];
                var additionalData = [];
                
                $("#optiontable tbody").append('<tr id="'+indexId+'"><td><input name="option_value[' + 1 + ']" class="form-control optionValues" type="text"/></td><td><input name="proceed_To[' + 1 + ']" class="form-control proceed_To" type="text" /></td><td><input id="deleteopt[' + 1 + ']" class="btn btn-danger optdeleterow" value="X" type="button" /></td></tr>');
                // Get the <span> element that closes the modal            
                var span = document.getElementById("optionModelClose");
                // When the user clicks on <span> (x), close the modal
                span.onclick = function() {
                    modal.style.display = "none";
                }
            } 
        }
    }
    $('.btndeleterow').click(function() {
        $(this).closest('tr').remove();
    });
    $('#optiontable tbody').on('click', '.optdeleterow', function(){
        $(this).closest ('tr').remove ();
    });

   
</script>
