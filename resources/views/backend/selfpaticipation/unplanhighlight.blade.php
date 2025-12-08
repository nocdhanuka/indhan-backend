@include('include.admin.backend_header')
<!-- Left side column. contains the logo and sidebar -->
@include('include.admin.backend_sidebar')
<!-- Content Wrapper. Contains page content -->

<div class="content-wrapper">
    <!-- Content Header (Page header) -->
    <section class="content-header">
        <h1>Self Participation High Light</h1>
        <ol class="breadcrumb">
            <li><a href="{{URL::to('admin/dashboard')}}"><i class="fa fa-dashboard"></i> Home</a></li>
            <li class="active">Self Participation High Light</li>
        </ol>
    </section>
    <!-- Main content -->
    <div class="content-footer" style="float: right;margin-right: 17px;margin-top: 18px;margin-bottom: 10px;">
        <button id="btn-export" onclick="exportHTML();">Export to
        word doc</button>
    </div>
    <section class="content">
        <div class="source-html-outer">
            <div id="source-html">
                <h1>Ticket Detail</h1>
                <div class="row">
                    <div class="col-xs-12">
                        <div class="box">
                            <div class="box-body table-responsive">
                              <div class="col-md-2">
                                  <h4><strong>Ticket</strong></h4>
                                  <div class="ticketdata">{{$top_open_ticket[0]->ticket_no}}</div>
                              </div>  
                              <div class="col-md-2">
                                  <h4><strong>Assign To DD</strong></h4>
                                  <div class="ticketdata">{{$top_open_ticket[0]->assign_to_dd}}</div>
                              </div>
                              <div class="col-md-2">
                                  <h4><strong>ECN (created by)</strong></h4>
                                  <div class="ticketdata">{{$top_open_ticket[0]->ecn}}</div>
                              </div>
                              <div class="col-md-2">
                                  <h4><strong>User (created by)</strong></h4>
                                  <div class="ticketdata">{{$top_open_ticket[0]->bdo}}</div>
                              </div>
                              <div class="col-md-2">
                                  <h4><strong>Role (created by)</strong></h4>
                                  <div class="ticketdata">{{$top_open_ticket[0]->user_type}}</div>
                              </div>
                              <div class="col-md-2">
                                  <h4><strong>Language</strong></h4>
                                  <div class="ticketdata">{{$top_open_ticket[0]->language}}</div>
                              </div>
                              
                            <div class="success"></div>

                         </div><!-- /.box-body -->
                        </div><!-- /.box -->
                    </div><!-- /.col -->
                    <div class="col-xs-12">
                        <div class="box">
                            <div class="box-body table-responsive">
                                <div class="col-md-2">
                                  <h4><strong>Data Source</strong></h4>
                                  <div class="ticketdata">{{$top_open_ticket[0]->data_source}}</div>
                                  </div>
                                  <div class="col-md-2">
                                      <h4><strong>State</strong></h4>
                                      <div class="ticketdata">{{$top_open_ticket[0]->state}}</div>
                                  </div>
                                  <div class="col-md-2">
                                      <h4><strong>District</strong></h4>
                                      <div class="ticketdata">{{$top_open_ticket[0]->district}}</div>
                                  </div>
                                  <div class="col-md-2">
                                      <h4><strong>Tehsil</strong></h4>
                                      <div class="ticketdata">{{$top_open_ticket[0]->tehsil}}</div>
                                  </div>
                                  <div class="col-md-2">
                                      <h4><strong>Village</strong></h4>
                                      <div class="ticketdata">{{$top_open_ticket[0]->village}}</div>
                                  </div>
                                  <div class="col-md-2">
                                      <h4><strong>Crop 1</strong></h4>
                                      <div class="ticketdata">{{$top_open_ticket[0]->crop_1}}</div>
                                  </div>
                            </div>
                        </div>
                    </div>
                    <div class="col-xs-12">
                        <div class="box">
                            <div class="box-body table-responsive">
                                <div class="col-md-2">
                                  <h4><strong>Product 1</strong></h4>
                                  <div class="ticketdata">{{$top_open_ticket[0]->product_1}}</div>
                              </div>
                              <div class="col-md-2">
                                  <h4><strong>Crop 2</strong></h4>
                                  <div class="ticketdata">{{$top_open_ticket[0]->crop_2}}</div>
                              </div>
                              <div class="col-md-2">
                                  <h4><strong>Product 2</strong></h4>
                                  <div class="ticketdata">{{$top_open_ticket[0]->product_2}}</div>
                              </div>
                              <div class="col-md-2">
                                  <h4><strong>Crop 3</strong></h4>
                                  <div class="ticketdata">{{$top_open_ticket[0]->crop_3}}</div>
                              </div>
                              <div class="col-md-2">
                                  <h4><strong>Product 3</strong></h4>
                                  <div class="ticketdata">{{$top_open_ticket[0]->product_3}}</div>
                              </div>
                              <div class="col-md-2">
                                  <h4><strong>Total Farmers</strong></h4>
                                  <div class="ticketdata">{{$top_open_ticket[0]->farmer_count}}</div>
                              </div>
                            </div>
                        </div>
                    </div>
                    <div class="col-xs-12">
                        <div class="box">
                            <div class="box-body table-responsive">
                                <div class="col-md-2">
                                  <h4><strong>Total Invited Farmers</strong></h4>
                                  <div class="ticketdata">{{$top_open_ticket[0]->invited_farmer_count}}</div>
                              </div>
                              <div class="col-md-2">
                                  <h4><strong>Special Participants</strong></h4>
                                  <div class="ticketdata">{{$top_open_ticket[0]->speacial_participant}}</div>
                              </div>
                              <div class="col-md-2">
                                  <h4><strong>Demo Number</strong></h4>
                                  <div class="ticketdata">{{$top_open_ticket[0]->demo_no}}</div>
                              </div>
                              <div class="col-md-2">
                                  <h4><strong>Status</strong></h4>
                                  <div class="ticketdata">{{$top_open_ticket[0]->ticket_status}}</div>
                              </div>
                              <div class="col-md-2">
                                  <h4><strong>Is Qualified</strong></h4>
                                  <div class="ticketdata">{{$top_open_ticket[0]->qualified_status}}</div>
                              </div>
                              <div class="col-md-2">
                                  <h4><strong>Date Created</strong></h4>
                                  <div class="ticketdata">{{$top_open_ticket[0]->c_datetime_format}}</div>
                              </div>
                            </div>
                        </div>
                    </div>

             </div><!-- /.row -->
             <h1>Feedback</h1>
             <div class="row">
                <div class="dtls-wrapper">
                    @if(count($top_open_ticket[0]->feedbackData)>0)
                    @foreach($top_open_ticket[0]->feedbackData as $key => $value)
                    <div class="col-md-4">
                        <div class="user-dtls" style="box-shadow: 0px 15px 28px 0px rgba(0, 0, 0, 0.09)!important;padding: 20px;background-color: #fff; text-align: center;">
                            <div class="user_img" style="margin: 0 auto;display: table;">
                                <img src="<?= $top_open_ticket[0]->feedbackData[$key]->feedbackImage;?>" width="150px">
                            </div>
                            <h3><?= $top_open_ticket[0]->feedbackData[$key]->name; ?></h3>
                            <div class="user-typ">
                                <?php 
                                if ($top_open_ticket[0]->feedbackData[$key]->user_type == 'zonal') {
                                    echo '<h4>Zonal</h4>';
                                }
                                elseif ($top_open_ticket[0]->feedbackData[$key]->user_type == 'regional') {
                                    echo '<h4>Regional</h4>';
                                }
                                elseif ($top_open_ticket[0]->feedbackData[$key]->user_type == 'area') {
                                    echo '<h4>Area</h4>';
                                }
                                elseif ($top_open_ticket[0]->feedbackData[$key]->user_type == 'sales_regional') {
                                 echo '<h4>RSM</h4>';
                             }
                             elseif ($top_open_ticket[0]->feedbackData[$key]->user_type == 'sales_area') {
                                echo '<h4>ASM</h4>';
                            }
                            elseif ($top_open_ticket[0]->feedbackData[$key]->user_type == 'bdo') {
                                echo '<h4>BDO</h4>';
                            }
                            elseif ($top_open_ticket[0]->feedbackData[$key]->user_type == 'marketing_supervisor') {
                                echo '<h4>Marketing Supervisor</h4>';
                            }
                            ?>
                        </div>
                        <h5><?= $top_open_ticket[0]->feedbackData[$key]->description; ?></h5>
                    </div>
                </div>
                @endforeach
                @endif

                @if(count($top_open_ticket[0]->feedbackData)==0)
                <div class="col-md-4">
                    No feedback found!
                </div>
                @endif
            </div>
        </div><!-- /.row -->

        <h1>High Lights</h1>
        <div class="row">
            <div class="dtls-wrapper">
                @if(count($top_open_ticket[0]->highlightdata)>0)
                @foreach($top_open_ticket[0]->highlightdata as $key => $value)
                <div class="col-md-4">
                    <div class="user-dtls" style="box-shadow: 0px 15px 28px 0px rgba(0, 0, 0, 0.09)!important;padding: 20px;background-color: #fff; text-align: center;">
                        <div class="user_img" style="margin: 0 auto;display: table;">
                            <img src="<?= $top_open_ticket[0]->highlightdata[$key]->highlightImage;?>" width="150px">
                        </div>
                        <h3><?= $top_open_ticket[0]->highlightdata[$key]->username;?></h3>
                        <div class="user-typ">
                            <?php 
                            if ($top_open_ticket[0]->highlightdata[$key]->user_type == 'zonal') {
                                echo '<h4>Zonal</h4>';
                            }
                            elseif ($top_open_ticket[0]->highlightdata[$key]->user_type == 'regional') {
                                echo '<h4>Regional</h4>';
                            }
                            elseif ($top_open_ticket[0]->highlightdata[$key]->user_type == 'area') {
                                echo '<h4>Area</h4>';
                            }
                            elseif ($top_open_ticket[0]->highlightdata[$key]->user_type == 'sales_regional') {
                             echo '<h4>RSM</h4>';
                         }
                         elseif ($top_open_ticket[0]->highlightdata[$key]->user_type == 'sales_area') {
                            echo '<h4>ASM</h4>';
                        }
                        elseif ($top_open_ticket[0]->highlightdata[$key]->user_type == 'bdo') {
                            echo '<h4>BDO</h4>';
                        }
                        elseif ($top_open_ticket[0]->highlightdata[$key]->user_type == 'marketing_supervisor') {
                            echo '<h4>Marketing Supervisor</h4>';
                        }
                        ?>
                    </div>
                    <h5><?= $top_open_ticket[0]->highlightdata[$key]->description;?></h5>
                </div>
            </div>
            @endforeach
            @endif

            @if(count($top_open_ticket[0]->feedbackData)==0)
            <div class="col-md-4">
                No high light found!
            </div>
            @endif
        </div>
    </div><!-- /.row -->

</div>
</div>
</section><!-- /.content -->
</div><!-- /.content-wrapper -->
@include('include.admin.footer')
</div>
<script>
    function exportHTML(){
     var header = "<html xmlns:o='urn:schemas-microsoft-com:office:office' "+
     "xmlns:w='urn:schemas-microsoft-com:office:word' "+
     "xmlns='http://www.w3.org/TR/REC-html40'>"+
     "<head><meta charset='utf-8'><title>Export HTML to Word Document with JavaScript</title></head><body>";
     var footer = "</body></html>";
     var sourceHTML = header+document.getElementById("source-html").innerHTML+footer;

     var source = 'data:application/vnd.ms-word;charset=utf-8,' + encodeURIComponent(sourceHTML);
     var fileDownload = document.createElement("a");
     document.body.appendChild(fileDownload);
     fileDownload.href = source;
     fileDownload.download = 'hightlight.doc';
     fileDownload.click();
     document.body.removeChild(fileDownload);
 }
</script>
<!-- ./wrapper --><!-- jQuery 2.1.4 -->
<script src="{{asset('assets/admin/plugins/jQuery/jQuery-2.1.4.min.js')}}"></script>

<!-- Bootstrap 3.3.5 -->
<script src="{{asset('assets/admin/bootstrap/js/bootstrap.min.js')}}"></script>
<!-- DataTables -->
<script src="{{asset('assets/admin/plugins/datatables/jquery.dataTables.min.js')}}"></script>
<script src="{{asset('assets/admin/plugins/datatables/dataTables.bootstrap.min.js')}}"></script>

<script type="text/javascript" language="javascript" src="https://cdn.datatables.net/buttons/1.5.1/js/dataTables.buttons.min.js"></script>
<script type="text/javascript" language="javascript" src="https://cdn.datatables.net/buttons/1.5.1/js/buttons.flash.min.js"></script>
<script type="text/javascript" language="javascript" src="https://cdnjs.cloudflare.com/ajax/libs/jszip/3.1.3/jszip.min.js"></script>
<script type="text/javascript" language="javascript" src="https://cdnjs.cloudflare.com/ajax/libs/pdfmake/0.1.32/pdfmake.min.js"></script>
<script type="text/javascript" language="javascript" src="https://cdnjs.cloudflare.com/ajax/libs/pdfmake/0.1.32/vfs_fonts.js"></script>
<script type="text/javascript" language="javascript" src="https://cdn.datatables.net/buttons/1.5.1/js/buttons.html5.min.js"></script>
<script type="text/javascript" language="javascript" src="https://cdn.datatables.net/buttons/1.5.1/js/buttons.print.min.js"></script>
<script type="text/javascript" language="javascript" src="https://cdn.datatables.net/buttons/1.5.1/js/buttons.colVis.min.js"></script>
<script type="text/javascript" language="javascript" src="https://cdn.datatables.net/fixedheader/3.1.3/js/dataTables.fixedHeader.min.js"></script>
<script type="text/javascript" language="javascript" src="https://cdn.datatables.net/fixedcolumns/3.2.4/js/dataTables.fixedColumns.min.js"></script>
<link  rel="stylesheet" href="https://cdn.datatables.net/buttons/1.5.1/css/buttons.dataTables.min.css">
<link  rel="stylesheet" href="https://cdn.datatables.net/fixedcolumns/3.2.4/css/fixedColumns.dataTables.min.css">
<script type="text/javascript" language="javascript" src="https://cdnjs.cloudflare.com/ajax/libs/moment.js/2.8.4/moment.min.js"></script>
<script type="text/javascript" language="javascript" src="https://cdn.datatables.net/plug-ins/1.10.16/sorting/datetime-moment.js"></script>



<!-- SlimScroll -->
<script src="{{asset('assets/admin/plugins/slimScroll/jquery.slimscroll.min.js')}}"></script>
<!-- FastClick -->
<script src="{{asset('assets/admin/plugins/fastclick/fastclick.min.js')}}"></script>
<!-- AdminLTE for demo purposes -->
<script src="{{asset('assets/admin/dist/js/demo.js')}}"></script>
<!-- page script -->




<script src="http://ajax.aspnetcdn.com/ajax/jquery.validate/1.10.0/jquery.validate.js" type="text/javascript"></script>

<link rel="stylesheet" href="https://kendo.cdn.telerik.com/2018.1.221/styles/kendo.common-material.min.css" />
<link rel="stylesheet" href="https://kendo.cdn.telerik.com/2018.1.221/styles/kendo.material.min.css" />
<link rel="stylesheet" href="https://kendo.cdn.telerik.com/2018.1.221/styles/kendo.material.mobile.min.css" />
<script src="https://kendo.cdn.telerik.com/2018.1.221/js/kendo.all.min.js"></script> 
<script>
    $('.sidebar-menu li').click(function(){
        $(this).find('ul').toggle();
    });
</script>