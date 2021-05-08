//Gets the nav bar.
$.get("navbar.html", function(data){
    $("#nav-placeholder").replaceWith(data);
});

$(document).ready(function(){
  var pathname = window.location.pathname;
  var isSearched = false;
  var selectedTable = '';
  //Handle selected table.
  if(pathname.includes('courses.html')){
    selectedTable = 'courses';
  }else if(pathname.includes('departments.html')){
    selectedTable = 'departments';
  }else if(pathname.includes('professors.html')){
    selectedTable = 'professors';
  }
  load_data(1,selectedTable, isSearched, '');

  function load_data(page, selectedTable, isSearched, txt){
    $.ajax({
      url:"../php/getTableData.php",
      method:"POST",
      data:{page:page, selectedTable:selectedTable, isSearched:isSearched, search:txt},
      success:function(data){
        $('#pagination_data').html(data);
      }
    })
  }
  //Handle pagination
  $(document).on('click','.page-item',function(){
    var page = $(this).attr("id");
    var txt = document.getElementById('searchedForm').value;
    load_data(page, selectedTable, isSearched, txt);
  });
  //Handle search input.
  $('#searchedForm').keyup(function(){
    var txt = $(this).val();
    //Automatic search for more than 3 chars.
    if(txt.length > 2){
      isSearched = true;
      load_data(1,selectedTable,isSearched, txt);
    }
    else if(txt == ''){
      isSearched = false;
      load_data(1,selectedTable,isSearched, '');
    }
  });

  //Handle button search click
  $('#searchBtn').click(function() {
    var txt = document.getElementById('searchedForm').value;
    isSearched = true;
    load_data(1,selectedTable, isSearched, txt);
   });

});