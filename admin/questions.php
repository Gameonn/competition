<?php 
require_once('../php_include/db_connection.php');
require_once("../php_include/header.php");
require_once("../php_include/sidebar.php");
require_once("GeneralFunctions.php");
?>
	
<!--sidebar end-->
<!--main content start-->
<section id="main-content">
<section class="wrapper">

<div class="row">
            <div class="col-sm-12">
                <section class="panel">
                    <header class="panel-heading">
                        All Questions
                        <!--<span class="tools pull-right">
                            <a href="javascript:;" class="fa fa-chevron-down"></a>
                           <a href="javascript:;" class="fa fa-times"></a>
                         </span>-->
                    </header>
                    <div class="panel-body">

                            <section id="no-more-tables">
                            <table class="table table-bordered table-striped table-condensed cf">
                                <thead class="cf">
                                <tr>
                                    <th>ID</th>
									<th>Category Image </th>
                                    <th>Category</th>
                                    <th>Title</th>
									<th>Edit</th>
                                </tr>
                                </thead>
                                <tbody>
								<?php 
									$result=GeneralFunctions::getAllQuestions();
									$r=1;
									foreach ($result as $key => $value) {
                               ?>
                                <tr>
                                    <td data-title="ID"><?php echo $r; ?></td>
                                    <td data-title="Category Image" style="text-align:center;"><img src="<?php echo BASE_PATH; ?>/uploads/<?php if($row['category_image']) echo $row['category_image']; else echo 'default_category.jpg'; ?>" style="width:100px;"></td>
                                    <td data-title="Category"><?php echo $value['category_name']; ?></td>
                                    <td data-title="Title"><?php echo $value['title']; ?></td>
									  <td data-title="Edit"> <a href="#myModal" data-toggle="modal" data-target="#myModal" class="edit_question fa fa-edit btn btn-primary btn-sm" vid="<?php echo $value['id'];?>" ></a></td>
                                </tr>
                               <?php $r=$r+1;
									} ?>
                                </tbody>
                            </table>
                        </section>
                    </div>
                </section>

                        <!--<div class="col-md-12 text-center clearfix">
                            <ul class="pagination">
                                <li><a href="#">«</a></li>
                                <li><a href="#">1</a></li>
                                <li><a href="#">2</a></li>
                                <li><a href="#">3</a></li>
                                <li><a href="#">4</a></li>
                                <li><a href="#">5</a></li>
                                <li><a href="#">»</a></li>
                            </ul>
                        </div>-->

                        
                    </div>
                </section>
            </div>
        </div>

</section>
</section>
<!--main content end-->

</section>
<!-- Placed js at the end of the document so the pages load faster -->
<!--Core js-->


<script src="js/jquery.js"></script>
<script src="js/jquery-ui/jquery-ui-1.10.1.custom.min.js"></script>
<script src="bs3/js/bootstrap.min.js"></script>
<script src="js/jquery.dcjqaccordion.2.7.js"></script>
<script src="js/jquery.scrollTo.min.js"></script>
<script src="js/jQuery-slimScroll-1.3.0/jquery.slimscroll.js"></script>
<script src="js/jquery.nicescroll.js"></script>
<!--[if lte IE 8]><script language="javascript" type="text/javascript" src="js/flot-chart/excanvas.min.js"></script><![endif]-->
<script src="js/skycons/skycons.js"></script>
<script src="js/jquery.scrollTo/jquery.scrollTo.js"></script>
<script src="//cdnjs.cloudflare.com/ajax/libs/jquery-easing/1.3/jquery.easing.min.js"></script>
<script src="js/calendar/clndr.js"></script>
<script src="http://cdnjs.cloudflare.com/ajax/libs/underscore.js/1.5.2/underscore-min.js"></script>
<script src="js/calendar/moment-2.2.1.js"></script>
<script src="js/evnt.calendar.init.js"></script>
<script src="js/jvector-map/jquery-jvectormap-1.2.2.min.js"></script>
<script type="text/javascript" src="js/bootstrap-fileupload/bootstrap-fileupload.js"></script>

<!--common script init for all pages-->
<script src="js/scripts.js"></script>
<!--script for this page-->

<script>
$('.edit_question').on('click', function (e) { 

e.preventDefault();

var vid=$(this).attr('vid');

//alert(vid);

$.post("edit_question_function.php",
{
vid: vid,
},

function(data){
$('#inside1').empty();

$('#inside1').append(data).fadeIn(1000);
});
}); 
</script>
<!-- Modal -->
<div class="modal fade" id="myModal" tabindex="-1" role="dialog" aria-labelledby="myModalLabel" aria-hidden="true">
    <div class="modal-dialog">
        <div class="modal-content">
            <div class="modal-header">
                <button type="button" class="close" data-dismiss="modal" aria-hidden="true">&times;</button>
                <h4 class="modal-title">Edit Question</h4>
            </div>

            <div class="modal-body row">
            <div id="inside1">
               
            </div> 
            </div>

        </div>
    </div>
</div>
<!-- modal -->
</body>
</html>
	