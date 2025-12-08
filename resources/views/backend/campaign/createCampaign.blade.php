@include('include.admin.backend_header')
<!-- Left side column. contains the logo and sidebar -->
@include('include.admin.backend_sidebar')
<!-- Content Wrapper. Contains page content -->
<div class="content-wrapper">
    <!-- Content Header (Page header) -->
    <section class="content-header">
        <h1>Create Campaign</h1>
        @if(Session::has('message'))
            <div class="col-md-12" style="margin-top:20px;">
            <p class="alert {{ Session::get('alert-class', 'alert-info') }}">{{ Session::get('message') }}</p>
            </div>
            @endif
        <ol class="breadcrumb">
            <li><a href="{{URL::to('admin/dashboard')}}"><i class="fa fa-dashboard"></i> Home</a></li>
            <li class="active">Create Campaign</li>
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
                                    <form action="" method="post">
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
                                            </tbody> 
                                            </table>
                                            <input class="btn btn-primary" id="insert-more-option" type="button" value="Add New Option" />
                                        </div>
                                        <div class="modal-footer">
                                            <!-- <input type="hidden" name="_token" value="{{ csrf_token() }}" /> -->
                                            <input class="btn btn-primary" id="optionSave" type="button" value="Save" />
                                        </div>
                                    </form>
                                </div>
                            </div>
                            <!-- <div id="textModal" class="modal">
                                
                                <div class="modal-content">
                                    <div class="modal-header">
                                        <span id="textModelClose" class="close">&times;</span>
                                        <h4>Text</h4>
                                    </div>
                                    <div class="modal-body">
                                       
                                        <label for="name">Enter Text</label>
                                        <input class="form-control" id="optiontext" type="text" name="name">
                                    </div>

                                   
                                    <div class="modal-footer">
                                        <input class="btn btn-primary" id="textSave" type="button" value="Save" />
                                    </div>
                                </div>
                            </div> -->

                            <div class="panel with-nav-tabs panel-default">
                                <div class="panel-heading">
                                    <ul class="nav nav-tabs" id="tabMenu">
                                        <li class="@if($active_tab=='tab1default') active @endif"><a href="#tab1default" data-toggle="tab">Step 1</a></li>
                                        <li class="@if($active_tab=='tab2default') active @endif"><a href="#tab2default" data-toggle="tab">Step 2</a></li>
                                        <!-- <li><a href="#tab3default" data-toggle="tab">Step 3</a></li> -->

                                    </ul>
                                </div>
                                <div class="panel-body">
                                    <div class="tab-content">
                                        <div class="tab-pane fade @if($active_tab=='tab1default') in active @endif" id="tab1default">
                                            <form action="{{url('admin/campaign/saveCampaignStep1')}}" method="post">
                                                <div class="form-group col-xs-6">
                                                    <div class="form-group ">
                                                        <label for="name">Campaign Type</label>
                                                        <select class="form-control" name="campType" id="campType">
                                                            <option value="1">HR</option>
                                                            <option value="2">BDO</option>
                                                        </select>
                                                    </div>
                                                    <div class="form-group">
                                                        <label for="name">Campaign Name</label>
                                                        <input class="form-control frm_txt" id="name" required type="text" name="name">
                                                    </div>
                                                    <div class="form-group">
                                                        <label for="email">Mandatory</label>
                                                        <div class="form-group">
                                                            <input type="radio" id="yes" name="mandatory" value="1">
                                                            <label for="yes">Yes</label>
                                                            <input style="margin-left: 10px;" checked="checked" type="radio" id="no" name="mandatory" value="2">
                                                            <label for="no">No</label>
                                                        </div>
                                                    </div>
                                                    <div class="form-group">
                                                        <label for="email">Push Notification</label>
                                                        <div class="form-group">
                                                            <input type="radio" id="yes_push" name="push_notify" value="1">
                                                            <label for="yes_push">Yes</label>
                                                            <input style="margin-left: 10px;" checked="checked" type="radio" id="no_push" name="push_notify" value="2">
                                                            <label for="no_push">No</label>
                                                        </div>
                                                    </div>
                                                    <div class="form-group">
                                                        <label for="email">Is Recurring</label>
                                                        <div class="form-group">
                                                            <input type="radio" id="yes_is_recurring" name="is_recurring" value="0">
                                                            <label for="yes_is_recurring">Yes</label>
                                                            <input style="margin-left: 10px;" checked="checked" type="radio" id="no_is_recurring" name="is_recurring" value="1">
                                                            <label for="no_is_recurring">No</label>
                                                        </div>
                                                    </div>
                                                    <div class="form-group">
                                                        <label for="email">Date of Expiry</label>
                                                        <input class="form-control" required type="date" id="expiry_date" name="expiry_date">
                                                        <!-- <input class="form-control" id="email" type="email" name="Email"> -->
                                                    </div>
                                                    <div class="form-group">
                                                        <input type="hidden" name="_token" value="{{ csrf_token() }}" />
                                                        <input class="btn btn-primary" type="button" value="Cancel" />
                                                        <input class="btn btn-primary" type="submit" value="Save/Next" />
                                                    </div>
                                                </div>
                                            </form>
                                        </div>
                                        <div class="tab-pane fade @if($active_tab=='tab2default') in active @endif" id="tab2default">
                                            <form action="{{url('admin/campaign/saveCampaignStep2')}}" method="post">
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
                                                    <input class="form-control" id="id[0]" type="hidden" name="campaign_id" value="{{ $id or '' }}">   
                                                        <tr id="optrow[1]">
                                                            <td><select class="form-control" name="queType[]" id="queType[0]">
                                                                    <option selected value="0">New</option>
                                                                    <option value="1">Duplicate</option>
                                                                    <option value="2">Submission</option>
                                                                </select>
                                                            </td>
                                                            <td>1 <input class="form-control" id="srNo[0]" type="hidden" name="srNo[]" value="1"></td>
                                                            <td><input class="form-control" id="queNumber[0]" type="hidden" name="queNumber[]" value="1">
                                                            <input class="form-control" id="quetext[0]" type="text" name="queText[]" value=""></td>
                                                            <td><select class="form-control" name="mand[]" id="mand">
                                                                    <option selected value="0">Yes</option>
                                                                    <option value="1">N/A</option>
                                                                </select></td>
                                                            <td>
                                                                <select class="form-control optionSelect" onclick='GetSelectedTextValue(this,1)' name="option_value[]" id="optionSelect[0]">
                                                                    <option value="">Select Option</option>
                                                                    <option value="1">Optional</option>
                                                                    <option value="2">Text</option>
                                                                </select>
                                                            </td>
                                                            <td><input class="form-control proceedTo" id="proceedTo[0]" type="text" style="width:100px;" name="proceedTo[]" value=""></td>
                                                            <td><button class="btn btn-danger btndeleterow"><i class="fa fa-trash"></i></button></td>
                                                        </tr>
                                                    </tbody>
                                                </table>
                                                <input type="hidden" name="_token" value="{{ csrf_token() }}" />
                                                <input class="btn btn-primary" id="insert-more" type="button" value="Add New Question" />
                                                <input class="btn btn-primary" id="insert-submission" type="button" value="Add Submission Row" />
                                                <input class="btn btn-primary" type="submit" value="Save" />
                                                <!-- <input class="btn btn-primary" id="getRows" type="button" value="Save" /> -->
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
//redirect to specific tab
$(document).ready(function () {
    $('#tabMenu a[href="#{{ old('tab') }}"]').tab('show');

    var tableData = $("#mytable tbody tr");
    
    // var trLengthData = 
    // var trData = getTr();
    getRows();
    // console.log(trLengthData);

    function getRows() {
        var table = document.getElementById('mytable');
        var rowCount = table.rows.length;
        var i=0;
        $('#mytable > tbody > tr').each(function(index, tr) { 
            // console.log("Index :: "+index);
            //tr.setAttribute("id", i);
            // console.log(tr);
            i++;
        });
    }
});


</script>
<script>
    var j = 2;
    var q= 2;
    $("#insert-more").click(function() {
        var trIndex = $("#mytable > tbody > tr").length;
        var tableData = $("#mytable tbody tr:last td:nth-child(2)")[0].innerHTML;
        var queNum = $("#mytable tbody tr:last td:nth-child(3)").find("input[type='hidden']").val();
        
        var trindexOnmore = parseInt(trIndex)+1;
        var qNum = parseInt(queNum)+1;
        var srNo = parseInt(tableData)+1;
        
        $('#mytable > tbody').append('<tr id="optrow['+trindexOnmore+']"><td><select class="form-control" name="queType[]" id="queType[' + j + ']"> <option selected value="0">New</option> <option value="1">Duplicate</option> <option value="2">Submission</option> </select> </td><td>' + j + '<input class="form-control" id="srNo[0]" type="hidden" name="srNo['+trindexOnmore+']" value="'+trindexOnmore+'"/></td> <td><input class="form-control" id="queNumber['+ j +']" type="hidden" name="queNumber[]" value="' + trindexOnmore + '"><input class="form-control" id="quetext[' + j + ']" type="text" name="queText[]" value=""></td> <td><select class="form-control" name="mand[]" id="mand"> <option selected value="0">Yes</option> <option value="1">N/A</option> </select></td> <td><div class="newTable" id="div'+trindexOnmore+'"></div> <div class="op"><input name="option_type[]" id="option_type[' + i + ']" class="form-control" value="2" type="hidden" /> <select onclick="GetSelectedTextValue(this,'+trindexOnmore+')" class="form-control optionSelect" name="option_value[]" id="optionSelect[0]"> <option value="">Select Option</option> <option value="1">Optional</option> <option value="2">Text</option> </select></div> </td> <td><input class="form-control" id="proceedTo[' + j + ']" type="text" style="width:100px;" name="proceedTo[]" value=""></td> <td><button class="btn btn-danger btndeleterow"><i class="fa fa-trash"></i></button></td> </tr>');
        j++;
        q++;
    });

    $("#insert-submission").click(function() {
        var trIndex = $("#mytable > tbody > tr").length;
        var tableData = $("#mytable tbody tr:last td:nth-child(2)")[0].innerHTML;
        var srNo = parseInt(tableData)+1;
        var trindexOnmore = parseInt(trIndex)+1;
        // $("#insert-more").hide();
        $('#mytable > tbody').append('<tr name="trname[' + j + ']"><td><select class="form-control" name="queType[]" id="queType[' + j + ']"><option value="0">New</option><option value="1">Duplicate</option><option selected value="2">Submission</option></select></td><td>'+trindexOnmore+'<input class="form-control" id="srNo[0]" type="hidden" name="srNo['+trindexOnmore+']" value="'+trindexOnmore+'"/></td><td><input class="form-control" id="queNumber['+ j +']" type="hidden" name="queNumber[]" value="' + trindexOnmore + '"><input class="form-control" id="quetext[' + j + ']" type="text" name="queText[]" value=""></td><td><input name="option_type[]" id="option_type[' + i + ']" class="form-control" value="3" type="hidden" /></td><td><input name="option_value[]" id="option_value[' + i + ']" class="form-control" value="3" type="hidden"/></td><td><input class="form-control" id="proceedTo[' + j + ']" type="text" style="width:100px;" name="proceedTo[]" value=""></td></tr>');
        j++;
    });
    //optiontable
    var i = 2;
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
        // $("#mytable tbody tr:last td:nth-child(6)")[0].innerHTML = '';
        // var srNo = parseInt(tableData);
        var modal = document.getElementById("optionModal");
        modal.style.display = "none";
        var optVal = $('.optionValues').map((_, el) => el.value).get();
        var proceedTo = $('.proceed_To').map((_, el) => el.value).get();

        var optTableID = $("#optiontable tbody tr:first")[0].id;
        
        var table = document.getElementById('mytable');
        var rowCount = table.rows.length;

        var opTable = "<table id='otable' class='table table-bordered table-striped'><input name='option_type[]' id='option_type[' + i + ']' class='form-control' value='1' type='hidden' />";

        for (let i = 0; i < optVal.length; i++) {
            opTable     += "<tr name='optTable["+optTableID+"]' id='tid["+i+"]'><td><input name='que_opt_val[]' id='que_opt_val[" + i + "]' class='form-control' value='" + optTableID + "' type='hidden' style='width: 80%;display: inline-block;'/><input name='option_value[]' id='option_value[' + i + ']' class='form-control' value='" + optVal[i] + "' type='text' style='width: 80%;display: inline-block;'/></td><td><input class='form-control' id='proceedTo[0]' type='text' style='width:100px;' name='proceedTo[]' value="+proceedTo[i]+"></td></tr>";
        }
        
        opTable     += "</table>";
        for (let i = 1; i < rowCount; i++) {
            
            console.log(table.rows[i].id);
            console.log('optrow['+optTableID+']');

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
// $("#optiontable tbody").html();
    });
    $(".childRows").css("display: none");
</script>

<script>
    function GetSelectedTextValue(id,indexId) {
        var selectedText = id.options[id.selectedIndex].innerHTML;
        var selectedValue = id.value;
        
        var tableData = $("#mytable tbody tr:last td:nth-child(6)").find("input[type='text']");
        if (selectedValue != '') {
            if (selectedValue == 1) {
                modal = document.getElementById("optionModal");
                modal.style.display = "block";
                $("#optiontable tbody").html();
                $("#optiontable tbody").html('<tr id="'+indexId+'"><td><input name="option_value[' + 1 + ']" class="form-control optionValues" type="text"/></td><td><input name="proceed_To[' + 1 + ']" class="form-control proceed_To" type="text" /></td><td><input id="deleteopt[' + 1 + ']" class="btn btn-danger optdeleterow" value="X" type="button" /></td></tr>');
                // $('#optiontable tbody').html('<tr class="child"><td><label name="option_type[' + 1 + ']">Option ' + 1 + '</label></td><td><input name="option_value[' + 1 + ']" id="option_value[' + i + ']" class="form-control optionValues" value="" type="text" /></td><td><input id="delete[' + 1 + ']" class="btn btn-danger optdeleterow" value="X" type="button" /></td></tr>');
                // Get the <span> element that closes the modal            
                var span = document.getElementById("optionModelClose");
                // When the user clicks on <span> (x), close the modal
                span.onclick = function() {
                    modal.style.display = "none";
                }
            }
        }
    }
    
    $('#mytable tbody').on('click', '.btndeleterow', function(){
        $(this).closest('tr').remove();
        //$("").find("tr:gt(0)").remove();
    });

    $('#optiontable tbody').on('click', '.optdeleterow', function(){
        $(this).closest ('tr').remove ();
    });

    
</script>


