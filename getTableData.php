<?php
    //THIS FILE IS FOR GETTING COURSES/DEPARTMENTS/PROFESSORS TABLES DATA.
    //IT's ALSO HANDLES THE SEARCH QUERY
    //-It takes the query as a paremeter. 
    
    //Connecting to database
    include_once 'connection.php';
    //Setting page limit and number of page.
    $limit = 5; 
    $page = isset($_POST['page']) ? $_POST['page'] : 1;
    $selectedTable = $_POST['selectedTable'];
    $start = ($page - 1) * $limit;
    $isSearched = $_POST['isSearched'];
    //Handle searching
    if($isSearched == "true"){
        if($selectedTable == 'courses'){
            //Search by the text..
            $query = "SELECT Courses.course_name, Courses.course_description, Departments.department_name, Professors.professor_name 
            FROM Courses RIGHT JOIN Departments ON Courses.department_id = Departments.department_id 
            RIGHT JOIN Professors ON Courses.professor_id = Professors.professor_id 
            WHERE REPLACE(Courses.course_name, ' ','') LIKE '%".$_POST["search"]."%' OR
            REPLACE(Courses.course_description, ' ','') LIKE '%".$_POST["search"]."%' OR
            REPLACE(Departments.department_name, ' ','') LIKE '%".$_POST["search"]."%' OR
            REPLACE(Professors.professor_name, ' ','') LIKE '%".$_POST["search"]."%'LIMIT $start, $limit";
            
            // Getting number of courses.
            $sql2 = "SELECT COUNT(Courses.course_id) AS id
            FROM Courses RIGHT JOIN Departments ON Courses.department_id = Departments.department_id 
            RIGHT JOIN Professors ON Courses.professor_id = Professors.professor_id 
            WHERE REPLACE(Courses.course_name, ' ','') LIKE '%".$_POST["search"]."%' OR
            REPLACE(Courses.course_description, ' ','') LIKE '%".$_POST["search"]."%' OR
            REPLACE(Departments.department_name, ' ','') LIKE '%".$_POST["search"]."%' OR
            REPLACE(Professors.professor_name, ' ','') LIKE '%".$_POST["search"]."%'";
        }
        else if($selectedTable == 'departments'){
            //Search by the text..
            $query = "SELECT Departments.department_name FROM Departments 
            WHERE REPLACE(Departments.department_name, ' ','') LIKE '%".$_POST["search"]."%' LIMIT $start, $limit";
            
            // Getting number of departments.
            $sql2 = "SELECT COUNT(Departments.department_id) AS id
            FROM Departments 
            WHERE REPLACE(Departments.department_name, ' ','') LIKE '%".$_POST["search"]."%'";
        }
        else{
            //Search by the text..
            $query = "SELECT Professors.professor_name FROM Professors 
            WHERE REPLACE(Professors.professor_name, ' ','') LIKE '%".$_POST["search"]."%' LIMIT $start, $limit";
            
            // Getting number of Professors.
            $sql2 = "SELECT COUNT(Professors.professor_id) AS id
            FROM Professors 
            WHERE REPLACE(Professors.professor_name, ' ','') LIKE '%".$_POST["search"]."%'";
        }
    }
    else{
        // Getting number of rows depending on selected table.
        if ($selectedTable == 'courses'){
            $sql2 = "SELECT count(Courses.course_id) AS id FROM Courses";
            $query = "SELECT Courses.course_name, Courses.course_description, Departments.department_name, Professors.professor_name FROM Courses, Departments, Professors WHERE Courses.professor_id = Professors.professor_id && Courses.department_id = Departments.department_id LIMIT $start, $limit";
        }
        else if($selectedTable == 'departments'){
            $sql2 = "SELECT count(Departments.department_id) AS id FROM Departments";
            $query = "SELECT Departments.department_name FROM Departments LIMIT $start, $limit";
        }
        else{
            $sql2 = "SELECT count(Professors.professor_id) AS id FROM Professors";
            $query = "SELECT Professors.professor_name FROM Professors LIMIT $start, $limit";
        }
    }
    $res = $conn->query($sql2);
    $coursesCount = $res->fetch_all(MYSQLI_ASSOC);
    //Set the number of pages.
    $total = $coursesCount[0]['id'];
    $numberOfPages = ceil($total / $limit);
    $previous = $page - 1;
    $next = $page + 1;

    $output = '';
    
    //Pagination..
    $output .= '
      <nav>
      <ul class="pagination justify-content-center">
    ';
    //Enable/Disable previous button.
    if($page == 1 ):
      $output .= '<li id="'.$previous.'" class="page-item disabled">';
    else:
      $output .= '<li id="'.$previous.'" class="page-item">';
    endif;
    $output .= '
      <a class="page-link">Previous</a>
      </li>';
    //Make the selected page active.
    for($i=1; $i<= $numberOfPages; $i++) :
      if($i == $page):
        $output .= '
        <li id="'.$i.'" class="page-item active"><a class="page-link">'.$i.'</a></li>';
      else:
        $output .= '
        <li id="'.$i.'" class="page-item"><a class="page-link">'.$i.'</a></li>';
      endif;
    endfor;
    //Enable/Disable next button.
    if($page == $numberOfPages):
      $output .= '<li id="'.$next.'" class="page-item disabled">';
    else:
      $output .= '<li id="'.$next.'" class="page-item">';
    endif;
    $output .= '
    <a class="page-link">Next</a>
    </li>
    </ul> 
    </nav>';

    // Getting data.
    $result = mysqli_query($conn, $query);
    //Set the colums depending on the selected table.
    if($selectedTable == 'courses'){
          $output .= '
        <table class="table table-striped table-hover table-dark table-bordered border-primary align-bottom">
        <thead>
        <tr class="table-primary">
          <th style="width:20%" scope="col">Course Name</th>
          <th style="width:40%" scope="col">Course Description</th>
          <th style="width:25%" scope="col">Department Name</th>
          <th style="width:25%" scope="col">Professor Name</th>';
    }
    else if($selectedTable == 'departments'){
        $output .= '
    <table class="table table-striped table-hover table-dark table-bordered border-primary align-bottom" style="margin-left: 25%; width: 50%; margin-right: 25%;">
    <thead>
      <tr class="table-primary justify-content-center">
        <th style="width:40%" scope="col">Number</th>
        <th style="width:60%" scope="col">Department Name</th>';
    }
    else{
        $output .= '
        <table class="table table-striped table-hover table-dark table-bordered border-primary align-bottom" style="margin-left: 25%; width: 50%; margin-right: 25%;">
    <thead>
      <tr class="table-primary justify-content-center">
        <th style="width:40%" scope="col">Number</th>
        <th style="width:60%" scope="col">Professor Name</th>';
    }
    
    $output .= ' </tr></thead><tbody>';
    if ($result->num_rows > 0) {
    // output data of each row
        $i = 1 + (($page-1)*$limit);
        while($row = $result->fetch_assoc()) {
            if($selectedTable == 'courses'){
                $output .= '<tr><th scope="row">'.$row["course_name"].'</th><td>'.$row["course_description"].'</td><td>'.$row["department_name"].'</td><td>Dr. '.$row["professor_name"].'</td></tr>';
            }
            else if($selectedTable == 'departments'){
                $output .= '<tr><th scope="row">'.$i++.'</th><td>'.$row["department_name"].'</td></tr>';
            }
            else{
                $output .= '<tr><th scope="row">'.$i++.'</th><td>Dr. '.$row["professor_name"].'</td></tr>';
            }  
        }
    } 

    $output .= '</tbody></table>';

    echo $output;
    $conn->close();
?>
