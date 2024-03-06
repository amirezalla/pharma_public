@extends(BaseHelper::getAdminMasterLayoutTemplate())
@section('content')
<div class="container">
    <div class="questionnaire_section">
        <p>Title</p>
        <input type="text" class="form-control" name="title" id="questionnaire_title">
        <br>
        <p>Description</p>
        <textarea name="desc" class="form-control" id="questionnaire_desc" cols="30" rows="10"></textarea>
        <br>
        <button class="btn btn-primary col-12" id="create_questionnaire">
            Create
        </button>
    </div>

    <form class="questions_section" action="https://marigopharma.marigo.collaudo.biz/admin/ecommerce/questionnaire/createQuestions" method=post >
        @csrf

    </form>

</div>
@push('footer')
<script>
$( document ).ready(function() {

$("#create_questionnaire").click(function(){
    var title=$("#questionnaire_title").val();
    var desc=$("#questionnaire_desc").val();
    axios.post('/admin/ecommerce/questionnaire/createQuestionnaire', {
        title: title,
        desc: desc
        })
        .then(function(response) {
            $('.questions_section').append('<input type="hidden" name="questionnaire_id" class="mb-3" value='+response.data+'>');
            $('.questionnaire_section').addClass('d-none');
            $('.questions_section').append('<h3 class="mb-3">'+title+'</h3>');
            $('.questions_section').append('<p>'+desc+'</p><br>');
            $('.questions_section').append('<div id="text-area-container" style="background-color:#f5f5f5;padding:20px"><div class="question_div"><label> Question :</label><textarea class="form-control" name="questions[]"></textarea></div></div><button class="btn btn-primary col-12 mb-5" id="add-text-area">+</button><button class="btn btn-success col-12 submitQuestions">Sumbit Questions</button>');

        })
        .catch(function(error) {

        });

});







});



$(document).on("click", "#add-text-area", function(e){

    e.preventDefault();
var newElement = $('<div class="question_div"><label> Question :</label><textarea class="form-control" name="questions[]"></textarea></div>');
// Find the element before which you want to insert
$('#text-area-container').append(newElement);



});

$(document).on("click", ".submitQuestions", function(){

    $('questions_section').submit();



});


</script>
@endpush
@stop
